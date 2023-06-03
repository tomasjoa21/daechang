<?php
$sub_menu = "940160";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$pre = 'css';
$fname = preg_replace("/_list/","",$g5['file_name']); // 파일명생성


$g5['title'] = '주조공정';
include_once('./_top_menu_tsdb.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM g5_1_cast_shot ";

$where = array();
$where[] = " 1=1 ";   // 디폴트 검색조건

if ($stx) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
       default :
            $where[] = " {$sfl} = '{$stx}' ";
            break;
    }
}

// 설비검색
if ($mms_idx) {
    $where[] = " machine_id = '".$mms_idx."' ";
}

// 날자 검색
if ($st_date) {
    $where[] = " start_time >= '".$st_date." 00:00:00' ";
}
if ($en_date) {
    $where[] = " start_time <= '".$en_date." 23:59:59' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = "start_time";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";


if(sizeof($where)<=1) {
    $sql = " SELECT row_estimate AS cnt FROM hypertable_approximate_row_count('g5_1_cast_shot') ";
}
else {
    $sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
}
// echo $sql.'<br>';
$row = sql_fetch_pg($sql,1);
$total_count = $row['cnt'];


// $rows = $config['cf_page_rows'];
$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
        {$sql_common} {$sql_search} {$sql_order}
		LIMIT {$rows} OFFSET {$from_record}
";
// echo $sql.'<br>';
$result = sql_query_pg($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 넘겨줄 변수가 추가로 있어서 qstr 별도 설정
$qstr = $qstr."&st_date=$st_date&en_date=$en_date&mms_idx=$mms_idx";
?>
<style>
.tbl_body td {text-align:center;border-bottom:solid 1px #e1e1e1;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?> </span></span>
</div>

<div class="local_desc01 local_desc" style="display:none;">
    <p>총건수가 65,411,218 이상이므로 기간 검색을 반드시 설정하세요. 하루 이상 입력 금지</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="mms_idx">
	<option value="">전체설비</option>
	<option value="45">LPM05(17)</option>
	<option value="44">LPM04(18)</option>
	<option value="58">LPM03(19)</option>
	<option value="59">LPM02(20)</option>
</select>
<script>$('select[name=mms_idx]').val('<?=$mms_idx?>');</script>
기간:
<input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" class="frm_input" style="width:80px;"> ~
<input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" class="frm_input" style="width:80px;">
&nbsp;&nbsp;
<select name="sfl" id="sfl">
    <option value="shot_id" <?=get_selected($sfl, 'shot_id')?>>샷ID</option>
    <option value="work_shift" <?=get_selected($sfl, 'work_shift')?>>주야간</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>


<div class="tbl_head01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr>
		<th scope="col">Idx/샷ID</th>
		<th scope="col">작업일</th>
		<th scope="col">시작시각/종료시각</th>
		<th scope="col">설비번호(ID)</th>
		<th scope="col">제품번호/제품명</th>
		<th scope="col">금형</th>
		<th scope="col">샷번호</th>
		<th scope="col">PVCT</th>
		<th scope="col">설비CT/제품CT</th>
		<th scope="col">보온로온도</th>
		<th scope="col">상하형히트</th>
		<th scope="col">상금형1~6</th>
		<th scope="col">하금형1~3</th>
		<th scope="col" style="display:none;">관리</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
	for ($i=0; $row=sql_fetch_array_pg($result); $i++) {

		// 스타일
		// $row['tr_bgcolor'] = ($i==0) ? '#fff7ea' : '' ;
		// $row['tr_color'] = ($i==0) ? 'blue' : '' ;

        $s_mod_a = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&csh_idx='.$row['csh_idx'].'">';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&csh_idx='.$row['csh_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&csh_idx='.$row['csh_idx'].'" class="btn btn_03">복제</a>';

        echo '
			<tr tr_id="'.$i.'" style="background-color:'.$row['tr_bgcolor'].';color:'.$row['tr_color'].'">
				<td>'.$row['csh_idx'].'<br>'.$row['shot_id'].'</td>
				<td>'.$row['work_date'].'<br>'.$g5['set_work_shift'][$row['work_shift']].'</td>
				<td class="font_size_7">'.substr($row['start_time'],0,19).'<br>~'.substr($row['end_time'],0,19).'<br>('.$row['elapsed_time'].' sec)</td>
				<td>'.$row['machine_no'].'<br>'.$row['machine_id'].'</td>
				<td class="font_size_7">'.$row['item_no'].'<br>'.$row['item_name'].'</td>
				<td>'.$row['mold_no'].'</td>
				<td>'.$row['shot_no'].'</td>
				<td>'.$row['pv_cycletime'].'</td>
				<td>'.$row['machine_cycletime'].'<br>'.$row['product_cycletime'].'</td>
				<td>'.$row['hold_temp'].'</td>
				<td>'.$row['upper_heat'].'<br>'.$row['lower_heat'].'</td>
				<td>'.$row['upper_1_temp'].' / '.$row['upper_2_temp'].' / '.$row['upper_3_temp'].'<br>'.
					$row['upper_4_temp'].' / '.$row['upper_5_temp'].' / '.$row['upper_6_temp'].'
				</td>
				<td>'.$row['lower_1_temp'].'<br>'.$row['lower_2_temp'].'<br>'.$row['lower_3_temp'].'</td>
				<td style="display:none;">'.$s_copy.'</td>
			</tr>
		';
	}
	if ($i == 0)
		echo '<tr class="no-data"><td colspan="8" class="text-center">등록(검색)된 자료가 없습니다.</td></tr>';
	?>
    </tbody>
    </table>
</div>
<!-- //리스트 테이블 -->

<div class="btn_fixed_top">
    <?php if($member['mb_manager_yn']) { ?>
        <a href="./<?=$fname?>_graph.php" class="btn_04 btn">그래프</a>
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <?php } ?>
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01" style="display:none;">추가하기</a> 
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
//-- $(document).ready 페이지로드 후 js실행 --//
$(document).ready(function(){

	$("#st_date,#en_date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		showButtonPanel: true,
		yearRange: "c-99:c+99",
		//maxDate: "+0d"
	});

	$( "#fsearch" ).submit(function(e) {
		if($('input[name=st_date]').val() > $('input[name=en_date]').val()) {
			alert('시작일이 종료일보다 큰 값이면 안 됩니다.');
			e.preventDefault();
		}
	});

});
</script>

<?php
include_once ('./_tail.php');
?>
