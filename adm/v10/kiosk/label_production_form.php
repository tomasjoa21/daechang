<?php
include_once('./_common.php');
include_once('./_head.php');
// print_r2($_POST);
/*
번호 : plt_no
배차기사

$act_button = 선택라벨출력
$mb_id = 01073861823
$mb_name = 코요
$prd_idx
$prd_start_date
$cst_idxs
$bom_idx
$bom_name
$bom_part_no
$bom_ship_count
$bom_delivery_check_yn
$mms_idx
$mms_name
$plt_in_cnt
*/
$total_cnt = 0;
$prd_start_dt = '0000-00-00';
$mms_id = 0;
$mms_nm = '';
$plt_check_yn = 0;
$drv_id_arr = array();
$drv_name_arr = array();
$plt_idx = 0;
//아작스로 보낼 bom데이터배열
$boms = '';
for ($i = 0; $i < count($chk); $i++) {
    if ($i == 0) {
        $sql = " SELECT GROUP_CONCAT(ctm.mb_id) AS mb_ids, GROUP_CONCAT(mb.mb_name) AS mb_names
                    FROM {$g5['customer_member_table']} ctm
                        LEFT JOIN {$g5['member_table']} mb ON ctm.mb_id = mb.mb_id
                WHERE cst_idx = '{$cst_idxs[$chk[$i]]}'
        ";
        // echo $sql;
        $cst = sql_fetch($sql);
        $drv_idx_arr = explode(',', $cst['mb_ids']);
        $drv_name_arr = explode(',', $cst['mb_names']);
        $prd_start_dt = $prd_start_date[$chk[$i]];
        $mms_id = $mms_idx[$chk[$i]];
        $mms_nm = $mms_name[$chk[$i]];
    }
    $total_cnt += $plt_in_cnt[$chk[$i]];
    if($bom_delivery_check_yn[$i]){
        $plt_check_yn = 1;
    }

    // $boms[$bom_idx[$chk[$i]]] = $plt_in_cnt[$chk[$i]];
    $bom_str = $bom_idx[$chk[$i]].'='.$plt_in_cnt[$chk[$i]];
    $boms .= ($i == 0) ? $bom_str : ','.$bom_str;
}
$drv_names = implode(',', $drv_name_arr);

$rowspan = 5;
$rowspan += ($plt_check_yn) ? 1 : 0;
$rowspan += (count($bom_idx)) ? count($bom_idx) : 0;

//여기서는 form01의 기능 필요없고 label.production_print.php파일도 사옹안함
?>

<div id="main" class="<?= $main_type_class ?>">
    <form name="form01" id="form01" action="./label_production_print.php" onsubmit="return form01_submit(this);" method="post">
        <input type="hidden" name="total_cnt" value="<?= $total_cnt ?>">
        <input type="hidden" name="mms_idx" value="<?= $mms_id ?>">
        <input type="hidden" name="mms_nm" value="<?= $mms_nm ?>">
        <input type="hidden" name="mb_id" value="<?= $mb_id ?>">
        <input type="hidden" name="mb_name" value="<?= $mb_name ?>">
        <input type="hidden" name="drv_names" value="<?= $drv_names ?>">
        <input type="hidden" name="plt_check_yn" value="<?= $plt_check_yn ?>">
        <?php for ($i = 0; $i < count($chk); $i++) { ?>
            <input type="hidden" name="bom_idx[<?= $i ?>]" value="<?= $bom_idx[$chk[$i]] ?>">
            <input type="hidden" name="bom_name[<?= $i ?>]" value="<?= $bom_name[$chk[$i]] ?>">
            <input type="hidden" name="plt_in_cnt[<?= $i ?>]" value="<?= $plt_in_cnt[$chk[$i]] ?>">
            <input type="hidden" name="bom_part_no[<?= $i ?>]" value="<?= $bom_part_no[$chk[$i]] ?>">
        <?php } ?>
        <div class="lbl_box" id="lbl_box">
            <div class="lbl_cont" id="lbl_cont">
                <table class="lbl_tbl">
                    <tr>
                        <td rowspan="<?= $rowspan ?>" class="td_qr_delivery" id="td_qr_drv">
                            <img src="https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=<?= $plt_idx ?>" id="qr_drv"><br><span>출하QR</span>
                        </td>
                        <td colspan="2" class="td_dt" id="td_dt"><?= G5_TIME_YMDHIS ?></td>
                        <th>No.</th>
                        <td class="td_plt_idx" id="td_plt_idx"></td>
                    </tr>
                    <tr>
                        <th>생산자</th>
                        <td colspan="3" class="td_total"><?= $mb_name ?></td>
                    </tr>
                    <tr>
                        <th>설비</th>
                        <td><?= $mms_nm ?></td>
                        <th>총수량</th>
                        <td><?= $total_cnt ?> EA</td>
                    </tr>
                    <tr>
                        <th>배차기사</th>
                        <td colspan="3"><?= $drv_names ?></td>
                    </tr>
                    <?php if ($plt_check_yn == 1) { ?>
                        <tr>
                            <td colspan="4" class="td_check">품질검사필요상품<span>인</span></td>
                        </tr>
                    <?php } ?>
                    <?php for ($j = 0; $j < count($chk); $j++) { ?>
                        <tr>
                            <td colspan="4" class="td_bom<?= (($j == 0) ? ' td_first_bom' : '') ?>">
                                <div class="dv_bom"><?= $bom_name[$chk[$j]] ?></div>
                                <span><?= $bom_part_no[$chk[$j]] ?> (<?= $plt_in_cnt[$chk[$j]] ?> EA)</span>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="td_qr_mobile" id="td_qr_chk">
                            <span>모바일QR</span>
                            <img src="https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl=<?= G5_USER_ADMIN_MOBILE_URL ?>/check.php?plt_idx=<?= $plt_idx ?>" id="qr_chk">
                        </td>
                    </tr>
                </table>
            </div><!--//.lbl_cont-->
        </div><!--//.lbl_box-->
        <div class="btn_fixed_top">
            <!-- <input type="text" id="lbl_cnt" name="lbl_cnt" readonly value="" class="frm_input input_cnt" size="10" maxlength="10" placeholder="출력개수"><span class="sp_lbl_cnt">장</span> -->
            <button type="button" id="btn_print" class="btn btn05 btn_print">프린트시작</button>
        </div>
    </form><!--#form01-->
</div><!--#main-->
<script>
function init() {
    var mbId = document.querySelectorAll('input[name="mb_id"]')[0];
    // var printCnt = document.getElementById("lbl_cnt");
    var printBtn = document.getElementById("btn_print");

    // function countInput(){
    //     $('.input_cnt').removeClass('input_cnt_on');
    //     $('#lbl_cnt').addClass('input_cnt_on');
    //     $('#mdl_num').css('display','flex');
    //     $('#input_num').val($('#lbl_cnt').val());  
    //     $('#input_num').focus();
    // }

    function handlePrint() {
        //로딩시작
        $('#loading_box').addClass('focus');
        var qr_drv_url = "https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=";
        var qr_chk_url = "https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl=<?= G5_USER_ADMIN_MOBILE_URL ?>/check.php?plt_idx=";
        var td_qr_drv = $('#td_qr_drv');
        var td_qr_chk = $('#td_qr_chk');
        var td_dt = $('#td_dt');
        var td_plt_idx = $('#td_plt_idx');
        var w = '<?=$w?>';
        var ajax_url = '<?=G5_USER_ADMIN_KIOSK_AJAX_URL?>/plt_print.php';
        var boms = '<?=$boms?>';
        var mb_id = '<?=$mb_id?>';
        var mms_idx = '<?=$mms_id?>';
        
        $.ajax({
            url: ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {'w': w,'mb_id_worker': mb_id,'mms_idx': mms_idx,'plt_status':'<?=(($plt_check_yn)?'check':'ok')?>','boms_str': boms},
            async: false,
            success: function(res){
                if(res.ok){
                    //반환된 새로운정보로 라벨 내용을 재구성해라
                    var qr_drv_img = '<img src="'+qr_drv_url+res.plt_idx+'" id="qr_drv">';
                    var qr_chk_img = '<img src="'+qr_chk_url+res.plt_idx+'" id="qr_chk">';
                    //기존 출하QR코드이미지 제거
                    td_qr_drv.find('img').remove();
                    //기존 검사QR코드이미지 제거
                    td_qr_chk.find('img').remove();
                    //새로운 출하QR코드이미지 추가
                    $(qr_drv_img).prependTo('#td_qr_drv');
                    //새로운 검사QR코드이미지 추가
                    $(qr_chk_img).appendTo('#td_qr_chk');
                    //프린트 날짜 표시
                    td_dt.text(res.print_dt);
                    //plt_idx번호 표시
                    td_plt_idx.text(res.plt_idx);
                    setTimeout(function(){
                        //데이터등록에 성공하면 인쇄해라
                        var prtCtnt = document.getElementById('lbl_box').innerHTML;
                        var orgCtnt = document.body.innerHTML;
                        document.body.innerHTML = prtCtnt;
                        window.print();
                        document.body.innerHTML = orgCtnt;
                        window.location.reload();
                        //로딩끝
                        $('#loading_box').removeClass('focus');
                    },1000);
                }
                else{
                    alert(res.msg);
                    //로딩끝
                    $('#loading_box').removeClass('focus');
                }
            },
            error: function(xmlReq){
                alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
                //로딩끝
                $('#loading_box').removeClass('focus');
            }
        });
  
    }

    // printCnt.addEventListener("click", countInput);
    printBtn.addEventListener("click", handlePrint);
}

// 페이지 로드가 완료되면 이벤트 리스너를 등록
window.addEventListener('load', init);
</script>
<?php
include_once('./_tail.php');