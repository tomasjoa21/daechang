<?php
$sub_menu = "920130";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$pre = 'xry';
$fname = preg_replace("/_list/","",$g5['file_name']); // 파일명생성


$g5['title'] = '주조코드조회';
@include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM g5_1_xray_inspection AS xry
                LEFT JOIN g5_1_qr_cast_code AS qrc USING(qrcode)
"; 

$where = array();
$where[] = " (1) ";   // 디폴트 검색조건

// cod_group 조건
if ($stx) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'xry.trm_idx_category' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ( $sfl == 'xry.com_idx' || $sfl == 'xry.mms_idx' ) :
            $where[] = " {$sfl} = '{$stx}' ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(mb_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}

// 기간 검색
$ser_time_type = $ser_time_type ?: 'end_time';
if ($st_date) {
    if ($st_time) {
        $where[] = " ".$ser_time_type." >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " ".$ser_time_type." >= '".$st_date.' 00:00:00'."' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " ".$ser_time_type." <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " ".$ser_time_type." <= '".$en_date.' 23:59:59'."' ";
    }
}

// 설비번호 검색
if ($ser_mms_idx) {
    $where[] = " mms_idx = '".$ser_mms_idx."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = $pre."_idx";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";

$rows = $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT SQL_CALC_FOUND_ROWS *
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$from_record}, {$rows} 
";
// echo $sql;
$result = sql_query($sql,1);
$count = sql_fetch_array( sql_query(" SELECT FOUND_ROWS() as total ") ); 
$total_count = $count['total'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 넘겨줄 변수가 추가로 있어서 qstr 별도 설정
$qstr = $qstr."&ser_mms_idx=$ser_mms_idx&ser_time_type=$ser_time_type&st_date=$st_date&en_date=$en_date"."&st_time=$st_time&en_time=$en_time";

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.tbl_body td {text-align:center;border-bottom:solid 1px #e1e1e1;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?> </span></span>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>설비번호 검색 시: 56=ADR1호기, 60=ADR2호기</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" autocomplete="off">
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_mms_idx" id="ser_mms_idx">
    <option value="">설비전체</option>
    <?php
    // 해당 범위 안의 모든 설비를 select option으로 만들어서 선택할 수 있도록 한다.
    // Get all the mms_idx values to make them optionf for selection.
    $sql2 = "SELECT mms_idx, mms_name
            FROM {$g5['mms_table']}
            WHERE com_idx = '".$_SESSION['ss_com_idx']."'
            ORDER BY mms_idx       
    ";
    // echo $sql2.'<br>';
    $result2 = sql_query($sql2,1);
    for ($i=0; $row2=sql_fetch_array($result2); $i++) {
        // print_r2($row2);
        echo '<option value="'.$row2['mms_idx'].'" '.get_selected($ser_mms_idx, $row2['mms_idx']).'>'.$row2['mms_name'].'('.$row2['mms_idx'].')</option>';
    }
    ?>
</select>
<script>$('select[name=ser_mms_idx]').val("<?=$ser_mms_idx?>").attr('selected','selected');</script>

<select name="ser_time_type" id="ser_time_type">
    <option value="end_time">종료시각</option>
    <option value="event_time">주조시각</option>
</select>
<script>$('select[name=ser_time_type]').val("<?=$ser_time_type?>").attr('selected','selected');</script>
기간:
<input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" class="frm_input" style="width:80px;"> ~
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" class="frm_input" style="width:80px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
&nbsp;&nbsp;
<select name="sfl" id="sfl">
    <option value="cast_code" <?=get_selected($sfl, 'cast_code')?>>주조코드</option>
    <option value="qrcode" <?=get_selected($sfl, 'qrcode')?>>QRCode</option>
    <option value="qrc_grade" <?=get_selected($sfl, 'qrc_grade')?>>등급</option>
    <option value="result" <?=get_selected($sfl, 'result')?>>결과</option>
    <option value="production_id" <?=get_selected($sfl, 'production_id')?>>생상품ID</option>
    <option value="work_shift" <?=get_selected($sfl, 'work_shift')?>>주야간</option>
    <option value="machine_id" <?=get_selected($sfl, 'machine_id')?>>설비번호</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>


<div class="local_desc01 local_desc" style="display:none;">
    <p>설비에서 자동으로 넘어온 알람, 예지 등과 관련된 코드를 확인하고 설정하는 페이지입니다.</p>
</div>

<div class="tbl_head01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr>
		<th scope="col">Idx</th>
		<th scope="col">작업일</th>
		<th scope="col">종료시각</th>
		<th scope="col">설비번호</th>
		<th scope="col">QRCode</th>
		<th scope="col">결과</th>
		<th scope="col">주조코드</th>
		<th scope="col">주조기</th>
		<th scope="col">주조시각</th>
		<th scope="col">등급</th>
		<th scope="col">판정</th>
		<th scope="col" style="display:none;">관리</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

		// 검사포인트
		for($j=0;$j<19;$j++) {
			$row['points_br'] = ($j%9==0 && $j>0) ? '<br>':'';
			$row['points'] .= '<a href="?'.$qstr.'&sfl=position_'.$j.'&stx='.$row['position_'.$j].'">'.$row['position_'.$j].'</a> '.$row['points_br'];
		}

		// 스타일
		// $row['tr_bgcolor'] = ($i==0) ? '#fff7ea' : '' ;
		// $row['tr_color'] = ($i==0) ? 'blue' : '' ;

        $s_mod_a = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&xry_idx='.$row['xry_idx'].'">';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&xry_idx='.$row['xry_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&xry_idx='.$row['xry_idx'].'" class="btn btn_03">복제</a>';

        echo '
			<tr tr_id="'.$i.'" style="background-color:'.$row['tr_bgcolor'].';color:'.$row['tr_color'].'">
				<td>'.$row['xry_idx'].'</td>
				<td>'.$row['work_date'].'</td>
				<td>'.substr($row['end_time'],0,19).'</td>
				<td>'.$row['machine_no'].'</td>
				<td>'.$row['qrcode'].'</td>
				<td>'.$row['result'].'</td>
				<td>'.$row['cast_code'].'</td>
				<td>'.$g5['mms'][$row['mms_idx']]['mms_name'].'</td>
				<td>'.$row['event_time'].'</td>
				<td>'.$row['qrc_grade'].'</td>
				<td>'.$row['qrc_result'].'</td>
				<td style="display:none;">'.$s_mod.'</td>
			</tr>
		';
	}
	if ($i == 0)
		echo '<tr class="no-data"><td colspan="15" class="text-center">등록(검색)된 자료가 없습니다.</td></tr>';
	?>
    </tbody>
    </table>
</div>
<!-- //리스트 테이블 -->

<div class="btn_fixed_top" style="display:no ne;">
    <?php if($member['mb_manager_yn']) { ?>
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
        <a href="./<?=$fname?>_change.php" class="btn_04 btn btn_change">주조코드임의생성</a>
    <?php } ?>
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01" style="display:none;">추가하기</a> 
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div id="modal30" title="날짜입력" style="display:none;">
    <form name="form30" id="form30" action="" onsubmit="return form30_submit(this);" method="post" enctype="multipart/form-data">
        <table>
        <tbody>
        <tr>
            <td style="line-height:130%;padding:10px 0;">
                <ul>
                    <li>시작날짜를 입력하세요.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td style="padding:5px 0;">
                <input type="text" name="ymd" class="frm_input" first="2019-07-01" value="<?=G5_TIME_YMD?>" placeholder="YYYY-MM-DD">
            </td>
        </tr>
        <tr>
            <td style="padding:5px 0;">
                <button type="submit" class="btn btn_01">확인</button>
            </td>
        </tr>
        </tbody>
        </table>
    </form>
</div>


<script>
$(function(e) {
    // 주조코드임의생성
    $( ".btn_change" ).on( "click", function(e) {
        e.preventDefault();
        $( "#modal30" ).dialog( "open" );
    });
    $( "#modal30" ).dialog({
        autoOpen: false
        , width:250
        , position: { my: "right-10 top-10", of: ".btn_change"}
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

});

function form01_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

	if(document.pressed == "선택수정") {
		$('input[name="w"]').val('u');
	}
	if(document.pressed == "선택삭제") {
		if (!confirm("선택한 항목(들)을 정말 삭제 하시겠습니까?\n복구가 어려우니 신중하게 결정 하십시오.")) {
			return false;
		}
		else {
			$('input[name="w"]').val('d');
		} 
	}
    return true;
}
function form30_submit(f) {
    var href = './<?=$g5['file_name']?>_change.php?ymd='+f.ymd.value;
    winChange = window.open(href, "winChange", "left=100,top=100,width=520,height=600,scrollbars=1");
    winChange.focus();
    $( "#modal30" ).dialog( "close" );
    return false;
}
</script>

<?php
include_once ('./_tail.php');
?>
