<?php
$sub_menu = "930120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$pre = 'msg';
$fname = preg_replace("/_list/","",$g5['file_name']); // 파일명생성


$g5['title'] = '조치제안관리';
include_once('./_top_menu_maintain.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM g5_1_maintain_suggest AS msg
                LEFT JOIN g5_1_code AS cod ON cod.mms_idx = msg.mms_idx AND cod.cod_code = msg.msg_code
";

$where = array();
$where[] = " (1) ";   // 디폴트 검색조건

if ($stx) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
        case ($sfl == 'dta_more') :
            $where[] = " dta_value >= '".$stx."' ";
            break;
        case ($sfl == 'dta_less') :
            $where[] = " dta_value <= '".$stx."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}

// 설비검색
if ($ser_robot_no) {
    $where[] = " robot_no = '".$ser_robot_no."' ";
}

// 기간 검색
if ($st_date) {
    if ($st_time) {
        $where[] = " msg_date >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " msg_date >= '".$st_date.' 00:00:00'."' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " msg_date <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " msg_date <= '".$en_date.' 23:59:59'."' ";
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = $pre."_idx";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";


$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
// echo $sql.'<br>';
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];


$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
        {$sql_common} {$sql_search} {$sql_order}
		LIMIT {$rows} OFFSET {$from_record}
";
// echo $sql.'<br>';
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 넘겨줄 변수가 추가로 있어서 qstr 별도 설정
$qstr = $qstr."&ser_robot_no=$ser_robot_no&st_date=$st_date&en_date=$en_date"."&st_time=$st_time&en_time=$en_time";
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.cod_name, .msg_suggest {text-align:left !important;padding-left:10px !important;}
.msg_suggest {font-size:0.9em;}
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
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_mms_idx" id="ser_mms_idx">
    <option value="">전체설비</option>
    <?php
    if(is_array($g5['mms'])) {
        foreach ($g5['mms'] as $k1=>$v1 ) {
            // print_r2($g5['mms'][$k1]);
            if($g5['mms'][$k1]['com_idx']==$_SESSION['ss_com_idx']) {
                echo '<option value="'.$k1.'" '.get_selected($ser_mms_idx, $k1).'>'.$g5['mms'][$k1]['mms_name'].'</option>';
            }
        }
    }
    ?>
</select>
<script>$('select[name=ser_mms_idx]').val("<?=$ser_mms_idx?>").attr('selected','selected');</script>
기간:
<input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" class="frm_input" style="width:80px;"> ~
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" class="frm_input" style="width:80px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
&nbsp;&nbsp;
<select name="sfl" id="sfl">
    <option value="msg_code" <?=get_selected($sfl, 'msg_code')?>>알람</option>
    <option value="status" <?=get_selected($sfl, 'status')?>>상태</option>
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
		<th scope="col">No</th>
		<th scope="col">설비명</th>
		<th scope="col">코드</th>
		<th scope="col">알람내용</th>
		<th scope="col">조치제안</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {

		// 스타일
		// $row['tr_bgcolor'] = ($i==0) ? '#fff7ea' : '' ;
		// $row['tr_color'] = ($i==0) ? 'blue' : '' ;

        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&csh_idx='.$row['csh_idx'].'" class="btn btn_03">수정</a>';

        for($j=1;$j<6;$j++) {
            $row['msg_suggest'] .= $row['msg_suggest'.$j] ? '<div>'.$j.'. '.$row['msg_suggest'.$j].'</div>' : '';
        }

        echo '
			<tr tr_id="'.$i.'">
				<td class="font_size_7">'.$row[$pre.'_idx'].'</td>
				<td>'.$g5['mms'][$row['mms_idx']]['mms_name'].'</td>
				<td>'.$row[$pre.'_code'].'</td>
				<td class="cod_name">'.$row['cod_name'].'</td>
				<td class="msg_suggest">'.$row['msg_suggest'].'</td>
			</tr>
		';
	}
	if ($i == 0)
		echo '<tr class="no-data"><td colspan="20" class="text-center">등록(검색)된 자료가 없습니다.</td></tr>';
	?>
    </tbody>
    </table>
</div>
<!-- //리스트 테이블 -->

<div class="btn_fixed_top">
    <?php if($member['mb_manager_yn']) { ?>
        <a href="maintain_suggest_exec.php" class="btn_04 btn btn_exec">조치제안 머신러닝 실행</a>
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <?php } ?>
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01" style="display:none;">추가하기</a> 
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
//-- $(document).ready 페이지로드 후 js실행 --//
$(document).ready(function(){

    // 조치제안 머신러닝
    $(".btn_exec").click(function(e) {
        if(confirm('조치제안 머신러닝 실행하시겠습니까?\n데이터 처리양에 따라 시간이 꽤 걸릴 수 있습니다.')) {
            var href = '<?=G5_USER_URL?>/cron/maintain_suggest_exec.php';
            winMaintainExec = window.open(href, "winMaintainExec", "left=100,top=100,width=520,height=600,scrollbars=1");
            winMaintainExec.focus();
            return false;
        }
        return false;
    });

    // timepicker 설정
    $("input[name$=_time]").timepicker({
        'timeFormat': 'H:i:s',
        'step': 10
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

    $("#st_date,#en_date").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		showButtonPanel: true,
		yearRange: "c-99:c+99",
		//maxDate: "+0d"
	});

	// $( "#fsearch" ).submit(function(e) {
	// 	if($('input[name=st_date]').val() > $('input[name=en_date]').val()) {
	// 		alert('시작일이 종료일보다 큰 값이면 안 됩니다.');
	// 		e.preventDefault();
	// 	}
	// });

});
</script>

<?php
include_once ('./_tail.php');
?>
