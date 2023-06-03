<?php
$sub_menu = "940130";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'bom_jig';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr .= '&ser_mms_idx='.$ser_mms_idx; // 추가로 확장해서 넘겨야 할 변수들

// print_r3($member);
// print_r3($_SESSION);

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
    
    ${$pre}['com_idx'] = $_SESSION['ss_com_idx'];
    ${$pre}[$pre.'_status'] = 'ok';
}
else if ($w == 'u' || $w == 'c') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
    // 설비정보 추출
	$mms = get_table('mms', 'mms_idx', ${$pre}['mms_idx']);
    // BOM
	$bom = get_table('bom', 'bom_idx', ${$pre}['bom_idx']);

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_gender');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'추가':'수정'; 
$html_title = ($w=='c')?'복제':$html_title; 
$g5['title'] = '설비별지그 '.$html_title;
//include_once('./_top_menu_data.php');
include_once ('./_head.php');
//echo $g5['container_sub_title'];

?>
<style>
.frm_date {width:75px;}
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<input type="hidden" name="com_idx" value="<?$_SESSION['ss_com_idx']?>">
<input type="hidden" name="ser_mms_idx" value="<?php echo $ser_mms_idx ?>">

<div class="local_desc01 local_desc" style="display:none;">
    <p>각종 고유번호(업체번호, IMP번호..)들은 내부적으로 다른 데이타베이스 연동을 통해서 정보를 가지고 오게 됩니다.</p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
	<colgroup>
		<col class="grid_4" style="width:15%;">
		<col style="width:35%;">
		<col class="grid_4" style="width:15%;">
		<col style="width:35%;">
	</colgroup>
	<tbody>
	<tr> 
		<th scope="row">설비선택</th>
		<td>
            <input type="hidden" name="mms_idx" value="<?=${$pre}['mms_idx']?>"><!-- 설비번호 -->
			<input type="text" name="mms_name" value="<?php echo $g5['mms'][${$pre}['mms_idx']]['mms_name'] ?>" id="mms_name" class="frm_input required" required readonly>
            <a href="./mms_select.php?file_name=<?php echo $g5['file_name']?>" class="btn btn_02" id="btn_mms">찾기</a>
		</td>
        <th scope="row">품명/품번</th>
		<td>
            <input type="hidden" name="bom_idx" value="<?=${$pre}['bom_idx']?>">
			<input type="text" name="bom_name" value="<?=$bom['bom_name']?>" class="frm_input required readonly" required readonly>
            <span class="span_bom_part_no font_size_8"><?=$bom['bom_part_no']?></span>
            <a href="./bom_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02" id="btn_bom">찾기</a>
        </td>
    </tr>
	<tr> 
		<th scope="row">지그코드</th>
		<td>
			<input type="text" name="boj_code" value="<?=${$pre}['boj_code']?>" class="frm_input required" required>
		</td>
        <th scope="row">PLC IP</th>
		<td>
			<input type="text" name="boj_plc_ip" value="<?=${$pre}['boj_plc_ip']?>" class="frm_input">
		</td>
    </tr>
	<tr> 
        <th scope="row">PLC Port</th>
		<td>
			<input type="text" name="boj_plc_port" value="<?=${$pre}['boj_plc_port']?>" class="frm_input">
		</td>
        <th scope="row">PLC no</th>
		<td>
			<input type="text" name="boj_plc_no" value="<?=${$pre}['boj_plc_no']?>" class="frm_input">
		</td>
    </tr>
	<tr>
		<th scope="row"><label for="boj_memo">메모</label></th>
		<td colspan="3"><textarea name="boj_memo" id="boj_memo"><?php echo ${$pre}['boj_memo'] ?></textarea></td>
	</tr>
	<tr>
		<th scope="row"><label for="boj_status">상태</label></th>
		<td colspan="3">
			<?php echo help("상태값은 관리자만 수정할 수 있습니다."); ?>
			<select name="boj_status" id="boj_status"
				<?php if (auth_check($auth[$sub_menu],"d",1)) { ?>onFocus='this.initialSelect=this.selectedIndex;' onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
				<?=$g5['set_boj_status_options']?>
			</select>
			<script>$('select[name="boj_status"]').val('<?=${$pre}['boj_status']?>');</script>
		</td>
	</tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="./<?=$fname?>_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function() {
    // 설비찾기 버튼 클릭
	$("#btn_mms").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winShftSelect = window.open(href, "winShftSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winShftSelect.focus();
	});

	$("#btn_bom").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winBom = window.open(href, "winBom", "left=300,top=150,width=550,height=600,scrollbars=1");
        winBom.focus();
	});



});

function form01_submit(f) {

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
