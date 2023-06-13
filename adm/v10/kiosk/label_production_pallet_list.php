<?php
include_once('./_common.php');
include_once('./_head.php');

$shif_date = statics_date(G5_TIME_YMDHIS);
// echo '<br><br><br><br><br>';
// echo $bom_idx."<br>";
// echo $member['mb_id'];
//우선 오늘 적재한 해당 bom_idx가 포함되어 있는 plt_idx를 전부 추출하자
// $sql = " SELECT itm.plt_idx
//                 , plt_reg_dt
//                 , mb_id_worker
//         FROM {$g5['item_table']} itm
//         LEFT JOIN {$g5['pallet_table']} plt ON itm.plt_idx = plt.plt_idx
//         WHERE plt.mb_id_worker = '{$member['mb_id']}'
//             AND bom_idx = '{$bom_idx}'
//             AND itm.plt_idx != '0'
//             AND plt_reg_dt LIKE '".G5_TIME_YMD."%'
//         GROUP BY itm.plt_idx
//         ORDER BY itm.plt_idx DESC
// ";
$sql = " SELECT itm.plt_idx
                , plt_reg_dt
                , mb_id_worker
        FROM {$g5['item_table']} itm
        LEFT JOIN {$g5['pallet_table']} plt ON itm.plt_idx = plt.plt_idx
        WHERE plt.mb_id_worker = '{$member['mb_id']}'
            AND bom_idx = '{$bom_idx}'
            AND itm.plt_idx != '0'
            AND plt_reg_dt >= '".statics_date(G5_TIME_YMDHIS)." 00:00:00'
            AND plt_status IN ('ok','check','delivery')
        GROUP BY itm.plt_idx
        ORDER BY itm.plt_idx DESC
";
// echo '<br><br><br><br><br><br><br><br>';
// echo $sql;exit;
$result = sql_query($sql,1);
if(!$result->num_rows){
    goto_url('./label_production_list.php',false);
}
?>
<div id="main" class="<?=$main_type_class?>">
<form name="form01" id="form01" action="./label_production_pallet_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
<input type="hidden" name="mb_name" value="<?=$member['mb_name']?>">
<input type="hidden" name="bom_idx" value="<?=$bom_idx?>">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">선택</th>
        <th scope="col">파렛트ID</th>
        <th scope="col">품목</th>
        <th scope="col">발행일시</th>
        <th scope="col">적재수량</th>
        <th scope="col">재출력</th>
    </tr>
    </thead>
    <tbody>
    <?php for($i=0;$row=sql_fetch_array($result);$i++){ 
        $bg = 'bg'.($i%2);
        //불량처리(defect)된것도 카운터에 넣지 말자
        $sql2 = " SELECT itm.bom_idx
                        , bom.bom_part_no
                        , bom.bom_name
                        , itm.mms_idx
                        , COUNT(itm_idx) AS itm_cnt
            FROM {$g5['item_table']} itm
            LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
            WHERE plt_idx = '{$row['plt_idx']}'
                AND itm_status NOT IN ('trash','defect','scrap')
            GROUP BY itm.bom_idx
        ";
        $res = sql_query($sql2,1);

        $row['itm_total'] = 0;
        $row['bom_idxs'] = '';
    ?>
    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['plt_idx'] ?>">
        <td class="td_chk">
            <input type="checkbox" class="inp_chk" name="chk[]" value="<?=$row['plt_idx']?>" id="chk_<?php echo $i ?>">
            <label for="chk_<?php echo $i ?>" class="chk"></label>
            <!-- <input type="hidden" name="plt_idx[<?php //$row['plt_idx']?>]" value="<?php //$row['plt_idx']?>"> -->
        </td>
        <td class="td_plt_idx"><?=$row['plt_idx']?></td><!-- 파렛트ID -->
        <td class="td_bom">
            <ul>
            <?php for($j=0;$row2=sql_fetch_array($res);$j++){ 
                $row['itm_total'] += $row2['itm_cnt'];
                $row['bom_idxs'] .= ($j == 0) ? $row2['bom_idx'] : '_'.$row2['bom_idx'];
            ?>
            <li>
                <span class="ss ss_no">[ <?=$row2['bom_part_no']?> ]</span>
                <strong class="ss ss_name"><?=$row2['bom_name']?></strong>
                <span class="ss ss_cnt">( <?=$row2['itm_cnt']?>EA )</span>
            </li>
            <?php } ?>
            </ul>
        </td><!-- 품목 -->
        <td class="td_plt_reg_dt"><?=$row['plt_reg_dt']?></td>
        <td class="td_plt_in_cnt"><?=$row['itm_total']?></td>
        <td class="td_plt_reprint">
            <a href="<?=G5_USER_ADMIN_KIOSK_URL?>/label_production_form2.php?plt_idx=<?=$row['plt_idx']?>&bom_idxs=<?=$row['bom_idxs']?>&bom_idx=<?=$bom_idx?>" class="btn btn05 btn_reprint">재발행</a>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div><!--//.tbl_wrap-->
<div class="btn_fixed_top" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn04 btn_del">
</div>
</form>
</div><!--//#main-->
<script>
function form01_submit(f){
    $submit_flag = true;
    if(!is_checked("chk[]")){
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        $submit_flag = false;
        return false;
    }

    return $submit_flag;
}
</script>
<?php
include_once('./_tail.php');