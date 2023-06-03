<?php
$sub_menu = "940160";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$pre = 'sck';
$table = 'g5_1_socket';
$fname = preg_replace("/_list/","",$g5['file_name']); // 파일명생성


$g5['title'] = '소켓데이터';
@include_once('./_top_menu_data.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM {$table} ";

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

// 기간 검색
if ($st_date) {
    if ($st_time) {
        $where[] = " sck_dt >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " sck_dt >= '".$st_date." 00:00:00' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " sck_dt <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " sck_dt <= '".$en_date." 23:59:59' ";
    }
}


// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = "sck_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";


if(sizeof($where)<=1) {
    $sql = " SELECT row_estimate AS cnt FROM hypertable_approximate_row_count('".$table."') ";
}
else {
    $sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
}
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
$qstr = $qstr."&st_date=$st_date&en_date=$en_date&st_time=$st_time&en_time=$en_time&no=$no";
?>
<style>
.tbl_body td {text-align:center;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?> </span></span>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
	<p>DATA MAP(최호기) <a href="https://docs.google.com/spreadsheets/d/1baQOZuue_rMJ2xiY1DhqHxFDAfeefK_94llKKmPBEO8/edit?usp=sharing" target="_blank">direct link</a></p>
	<p> 엑셀의 Word No를 입력하시면 그 번호에 해당하는 항목이 <span style="color:darkorange;">색깔 표시</span>됩니다.</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
기간:
<input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" class="frm_input" style="width:90px;">
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:68px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" class="frm_input" style="width:90px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:68px;" placeholder="00:00:00">
&nbsp;&nbsp;
<select name="sfl" id="sfl">
    <option value="sck_ip" <?=get_selected($sfl, 'sck_ip')?>>아이피</option>
    <option value="sck_port" <?=get_selected($sfl, 'sck_port')?>>Port</option>
    <option value="sck_idx" <?=get_selected($sfl, 'sck_idx')?>>SCKidx</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="text" name="no" value="<?=$no?>" id="no" class="frm_input" autocomplete="off" style="width:36px;">
<input type="submit" class="btn_submit" value="검색">
</form>


<div class="tbl_head01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr>
		<th scope="col">Idx</th>
		<th scope="col" style="width:121px;">일시/ip/port</th>
		<th scope="col">배열값</th>
		<th scope="col" style="display:none;">관리</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {

		// 스타일
		// $row['tr_bgcolor'] = ($i==0) ? '#fff7ea' : '' ;
		// $row['tr_color'] = ($i==0) ? 'blue' : '' ;
		$row['arr'] = json_decode($row['sck_value'], true);
        // print_r($row['arr']);
        for($j=0;$j<sizeof($row['arr']);$j++) {
            // echo $row['arr'][$no].BR;
            // 최호기 팀장 엑셀 번호가 1부터 시작해서 헷갈리지 않게 수정 좀 했습니다.
            if($no && ($no-1)==$j) {
                $row['sck_value_arr'][] = '<span style="color:darkorange;">'.$row['arr'][$j].'</span>';
            }
            else {
                $row['sck_value_arr'][] = '<a href="?'.$qstr.'&no='.($j+1).'">'.$row['arr'][$j].'</a>';
                // $row['sck_value_arr'][] = $row['arr'][$j];
            }
            if(($j+1)%50==0) {
                $row['sck_value_arr'][] = BR;
            }
        }

        $s_mod_a = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&sck_idx='.$row['sck_idx'].'">';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&sck_idx='.$row['sck_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&sck_idx='.$row['sck_idx'].'" class="btn btn_03">복제</a>';

        echo '
			<tr tr_id="'.$i.'" style="background-color:'.$row['tr_bgcolor'].';color:'.$row['tr_color'].'">
				<td class="td_left font_size_8" style="vertical-align:top;">'.$row['sck_idx'].'</td>
				<td class="td_left font_size_8" style="vertical-align:top;">
					<div>'.substr($row['sck_dt'],0,19).'</div>
					<div><a href="./'.$fname.'_list.php?'.$qstr.'&sfl=sck_ip&stx='.$row['sck_ip'].'">'.$row['sck_ip'].'</a></div>
					<div><a href="./'.$fname.'_list.php?'.$qstr.'&sfl=sck_port&stx='.$row['sck_port'].'">'.$row['sck_port'].'</a></div>
				</td>
				<td class="td_left font_size_8">'.implode(", ",$row['sck_value_arr']).'</td>
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
    // st_date chage
    $(document).on('focusin', 'input[name=st_date]', function(){
        // console.log("Saving value: " + $(this).val());
        $(this).data('val', $(this).val());
    }).on('change','input[name=st_date]', function(){
        var prev = $(this).data('val');
        var current = $(this).val();
        // console.log("Prev value: " + prev);
        // console.log("New value: " + current);
        if(prev=='') {
            $('input[name=st_time]').val('00:00:00');
        }
    });
    // en_date chage
    $(document).on('focusin', 'input[name=en_date]', function(){
        $(this).data('val', $(this).val());
    }).on('change','input[name=en_date]', function(){
        var prev = $(this).data('val');
        if(prev=='') {
            $('input[name=en_time]').val('23:59:59');
        }
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
