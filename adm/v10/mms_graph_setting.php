<?php
$sub_menu = "950110";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$mms = get_table_meta('mms', 'mms_idx', $mms_idx);
// print_r2($mms);

$sql = "SELECT mta_key, mta_value
          , SUBSTRING_INDEX(SUBSTRING_INDEX(mta_key,'-',-2),'-',1) AS dta_type
          , SUBSTRING_INDEX(mta_key,'-',-1) AS dta_no
        FROM {$g5['meta_table']}
        WHERE mta_key LIKE 'dta_type_label%' 
            AND mta_db_table = 'mms' AND mta_db_id = '".$mms_idx."'
        ORDER BY convert(dta_type, decimal), convert(dta_no, decimal)
";
$rs = sql_query($sql,1);

$html_title = ($w=='')?'추가':'수정'; 
$g5['title'] = $mms['mms_name'].' 그래프 설정';
include_once('./_head.sub.php');

?>
<style>
.td_item_range {margin-bottom:4px;}
input[type=file] {width: 165px;}
    .dta_type_no {color:#bfbfbf;font-size:0.7em;}
</style>
<div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc" style="display:no ne;">
        <p>태그에 이름표(레이블)를 달아주세요.</p>
    </div>

    <form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_check(this);" method="post" enctype="multipart/form-data">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="token" value="">
    <input type="hidden" name="mms_idx" value="<?php echo $mms_idx; ?>">
    <div class=" new_win_con">
        <div class="tbl_frm01 tbl_wrap">
            <table>
            <caption><?php echo $g5['title']; ?></caption>
            <colgroup>
                <col class="grid_1" style="width:22%;">
                <col class="grid_3">
            </colgroup>
            <tbody>
            <?php
            for($i=0;$row=sql_fetch_array($rs);$i++) {
                $row['mta_key_arr'] = explode("-",$row['mta_key']);
                // print_r2($row['mta_key_arr']);
                $row['dta_type'] = $row['mta_key_arr'][1];
                $row['dta_no'] = $row['mta_key_arr'][2];
                $item_name = $g5['set_data_type_value'][$row['dta_type']] ?: $row['dta_type'];
            ?>
                <tr>
                    <th scope="row"><?=$item_name.'-'.$row['dta_no']?></th>
                    <td>
                        <input type="hidden" name="dta_type[]" value="<?=$row['dta_type']?>" class="frm_input">
                        <input type="hidden" name="dta_no[]" value="<?=$row['dta_no']?>" class="frm_input">
                        <input type="text" name="dta_label[]" value="<?=$mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]?>" class="frm_input" style="width:80%;">
                        <span class="dta_type_no"><?=$row['dta_type'].'-'.$row['dta_no']?></span>
                    </td>
                </tr>
            <?php
            }
            if($i<=0) {
            ?>
                <tr>
                    <td colspan="2" class="empty_table">측정값이 존재하지 않습니다.</td>
                </tr>
            <?php
            }
            ?>
            </tbody>
            </table>
        </div>
    </div>
    <div class="win_btn ">
        <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
        <input type="button" class="btn_close btn" value="창닫기" onclick="javascript:window.close();">
    </div>

    </form>

    <div class="btn_fixed_top" style="display:none;">
        <a href="./mms_view.popup.php?mms_idx=<?=$mms_idx?>" id="btn_mms_view" class="btn btn_03" title="장비이력카드"><i class="fa fa-address-card-o"></i></a>
        <a href="javascript:window.close();" id="member_add" class="btn btn_02">창닫기</a>
    </div>
</div>

<script>
// 윈도우 크기 재설정
window.onload = reSize;
window.onresize = reSize;
function reSize() {
	resizeTo(520, 680);    // 여는 페이지 설정 높이 80 차이
}

$(function() {

});

function form01_check(f) {
    
    return true;
}
</script>


<?php
include_once('./_tail.sub.php');
?>
