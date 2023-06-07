<?php
include_once('./_common.php');
include_once('./_head.php');
/*
plt_idx => 2
bom_idxs => 135_193
bom_idx
*/
$plt = sql_fetch(" SELECT mb_id_worker AS mb_id
                , mms_idx
                , plt_reg_dt
            FROM {$g5['pallet_table']} WHERE plt_idx = '{$plt_idx}'
");
$mb_id = $plt['mb_id'];
$mms_idx = $plt['mms_idx'];
$mms_name = $g5['mms_arr'][$mms_idx];
$_mb = sql_fetch(" SELECT mb_name FROM {$g5['member_table']} WHERE mb_id = '{$mb_id}' ");
$mb_name = $_mb['mb_name'];
$_bom_idx_arr = explode('_',$bom_idxs);
$plt_check_yn = 0;
$drv_id_arr = array();
$drv_name_arr = array();
$bom_arr = array();
$total_cnt = 0;
for($b=0;$b<count($_bom_idx_arr);$b++){
    $bom = sql_fetch(" SELECT bom_idx
                ,cst_idx_customer AS cst_idx
                ,bom_name
                ,bom_part_no
                ,bom_delivery_check_yn
            FROM {$g5['bom_table']}
            WHERE bom_idx = '{$_bom_idx_arr[$b]}'
    ");
    if($b == 0){
        $drv = sql_fetch(" SELECT GROUP_CONCAT(ctm.mb_id) AS mb_ids
                                , GROUP_CONCAT(mb.mb_name) AS mb_names
                        FROM {$g5['customer_member_table']} ctm
                        LEFT JOIN {$g5['member_table']} mb ON ctm.mb_id = mb.mb_id
                        WHERE cst_idx = '{$bom['cst_idx']}' 
        ");
        $drv_id_arr = explode(',',$drv['mb_idx']);
        $drv_name_arr = explode(',',$drv['mb_names']);
    }

    $itm_sql = " SELECT COUNT(itm_idx) AS itm_cnt
                FROM {$g5['item_table']}
                WHERE plt_idx = '{$plt_idx}'
                    AND bom_idx = '{$_bom_idx_arr[$b]}'
                    AND itm_status NOT IN ('trash','defect','scrap')
    ";
    $itm = sql_fetch($itm_sql);
    $bom_arr[$bom['bom_idx']] = array(
        'bom_part_no' => $bom['bom_part_no']
        ,'bom_name' => $bom['bom_name']
        ,'itm_cnt' => $itm['itm_cnt']
    );
    $total_cnt += $itm['itm_cnt'];
    if($bom['bom_delivery_check_yn']){
        $plt_check_yn = 1;
    }
}
$drv_names = implode(',', $drv_name_arr);

$rowspan = 5;
$rowspan += ($plt_check_yn) ? 1 : 0;
$rowspan += (count($bom_arr)) ? count($bom_arr) : 0;
//여기서는 form01의 기능 필요없고 label.production_print.php파일도 사옹안함
?>
<div id="main" class="<?= $main_type_class ?>">
    <div id="form">
        <div class="lbl_box" id="lbl_box">
            <div class="lbl_cont" id="lbl_cont">
                <table class="lbl_tbl">
                    <tr>
                        <td rowspan="<?= $rowspan ?>" class="td_qr_delivery" id="td_qr_drv">
                            <img src="https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=<?= $plt_idx ?>" id="qr_drv"><br><span>출하QR</span>
                        </td>
                        <td colspan="2" class="td_dt" id="td_dt"><?=$plt['plt_reg_dt']?></td>
                        <th>No.</th>
                        <td class="td_plt_idx" id="td_plt_idx"><?=$plt_idx?></td>
                    </tr>
                    <tr>
                        <th>생산자</th>
                        <td colspan="3" class="td_total"><?=$mb_name?></td>
                    </tr>
                    <tr>
                        <th>설비</th>
                        <td><?=$mms_name?></td>
                        <th>총수량</th>
                        <td><?=$total_cnt?> EA</td>
                    </tr>
                    <tr>
                        <th>배차기사</th>
                        <td colspan="3"><?=$drv_names?></td>
                    </tr>
                    <?php if ($plt_check_yn == 1) { ?>
                        <tr>
                            <td colspan="4" class="td_check">품질검사필요상품<span>인</span></td>
                        </tr>
                    <?php } ?>
                    <?php //for ($j = 0; $j < count($bom_arr); $j++) { ?>
                    <?php foreach ($bom_arr as $bk => $bv) { ?>
                        <tr>
                            <td colspan="4" class="td_bom">
                                <div class="dv_bom"><?=$bv['bom_name']?></div>
                                <span><?=$bv['bom_part_no']?> (<?=$bv['itm_cnt']?> EA)</span>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="td_qr_mobile" id="td_qr_chk">
                            <span>모바일QR</span>
                            <img src="https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl=<?=G5_USER_ADMIN_MOBILE_URL?>/check.php?plt_idx=<?=$plt_idx?>" id="qr_chk">
                        </td>
                    </tr>
                </table>
            </div><!--//.lbl_cont-->
        </div><!--//.lbl_box-->
        <div class="btn_fixed_top">
            <button type="button" id="btn_print" class="btn btn05 btn_print">프린트재출력</button>
        </div>
    </div><!--#form01-->
</div><!--#main-->
<script>
function init(){
    var printBtn = document.getElementById("btn_print");
    function handlePrint(){
        var prtCtnt = document.getElementById('lbl_box').innerHTML;
        var orgCtnt = document.body.innerHTML;
        document.body.innerHTML = prtCtnt;
        window.print();
        document.body.innerHTML = orgCtnt;
        // window.location.reload();
        window.location.href = '<?=G5_USER_ADMIN_KIOSK_URL?>/label_production_pallet_list.php?bom_idx=<?=$bom_idx?>';
    }
    printBtn.addEventListener("click", handlePrint);
}

// 페이지 로드가 완료되면 이벤트 리스너를 등록
window.addEventListener('load', init);
</script>
<?php
include_once('./_tail.php');