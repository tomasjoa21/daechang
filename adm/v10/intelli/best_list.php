<?php
$sub_menu = "920130";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$pre = 'css';
$fname = preg_replace("/_list/","",$g5['file_name']); // 파일명생성

$g5['title'] = '최적 파라메터';
@include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM g5_1_data_measure_best ";

$where = array();
$where[] = " (1) ";   // 디폴트 검색조건

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

// mms_idx 검색
if ($ser_mms_idx) {
    $where[] = " mms_idx = '".$ser_mms_idx."' ";
}


// 태그검색
if ($ser_type_no) {
    $ser_type_no_arr = explode("_",$ser_type_no);
    $ser_dta_type = $ser_type_no_arr[0];
    $ser_dta_no = $ser_type_no_arr[1];
    $where[] = " dta_type = '".$ser_dta_type."' ";
    $where[] = " dta_no = '".$ser_dta_no."' ";
}

// 기간 검색
if ($st_date) {
    if ($st_time) {
        $where[] = " dmb_dt >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " dmb_dt >= '".$st_date.' 00:00:00'."' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " dmb_dt <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " dmb_dt <= '".$en_date.' 23:59:59'."' ";
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = "dmb_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";


$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];


// $rows = $config['cf_page_rows'];
$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// echo $sql.'<br>';
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 넘겨줄 변수가 추가로 있어서 qstr 별도 설정
$qstr = $qstr."&st_date=$st_date&en_date=$en_date";

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
    <p>최적 파라메타를 찾는 추적 작업은 하루에 1회 실행됩니다.</p>
    <p>맨 마지막(최근)에 수집된 최적 파라메타 수집 시점이 기준값이 됩니다. 그 값을 기준으로 설비의 현재 상태를 비교 관리합니다.</p>
    <p>최적 파라메타란 양품이 집단적으로 생산되는 시간 분포의 중심점을 의미합니다. 최적 파라메타 수집 조건을 변경할 수 있습니다. <a href="./config_form.php">[주조파라메타설정]</a></p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
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
<input type="submit" class="btn_submit" value="검색">
</form>


<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">
<div class="tbl_head01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr>
		<th scope="col" style="display:<?=!$member['mb_manager_yn']?'none':''?>;">
			<label for="chkall" class="sound_only">항목 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
		<th scope="col">번호</th>
		<th scope="col">설비</th>
		<th scope="col">최적파라메타추출시점</th>
		<th scope="col">그룹핑수</th>
		<th scope="col">등급범위</th>
		<th scope="col" style="display:none;">관리</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $s_mod_a = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&dmb_idx='.$row['dmb_idx'].'">';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&dmb_idx='.$row['dmb_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&dmb_idx='.$row['dmb_idx'].'" class="btn btn_03">복제</a>';

        $check_display = !$member['mb_manager_yn'] ? 'none':'';
        echo '
			<tr tr_id="'.$i.'">
                <td class="td_chk" style="display:'.$check_display.';">
                    <input type="hidden" name="dmb_idx['.$i.']" value="'.$row['dmb_idx'].'" id="dmb_idx_'.$i.'">
                    <input type="checkbox" name="chk[]" value="'.$i.'" id="chk_'.$i.'">
                </td>
				<td>'.$row['dmb_idx'].'</td>
				<td>'.$g5['mms'][$row['mms_idx']]['mms_name'].'</td>
				<td>'.$row['dmb_dt'].'</td>
				<td>'.$row['dmb_group_count'].'</td>
				<td>'.$row['dmb_min'].'~'.$row['dmb_max'].'</td>
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

<div class="btn_fixed_top">
    <?php if($member['mb_manager_yn']) { ?>
        <a href="./<?=$fname?>_parameter.php" class="btn_04 btn btn_parameter" style="margin-right:50px;">최적파라메타추적</a>
        <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn">
        <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01" style="display:none;">추가하기</a>
    <?php } ?>
</div>
</form>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
//-- $(document).ready 페이지로드 후 js실행 --//
$(document).ready(function(){

    // 최적파라메터추적 클릭
    $(".btn_parameter").click(function(e) {
        if(confirm('최적의 주조 파라메터를 추적합니다.\n현재 시점부터 거꾸로 최적 파라메터를 찾습니다.')) {
            var href = '<?=G5_USER_URL?>/cron/<?=$g5['file_name']?>_parameter.php';
            winParemeter = window.open(href, "winParemeter", "left=100,top=100,width=520,height=600,scrollbars=1");
            winParemeter.focus();
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

	$( "#fsearch" ).submit(function(e) {
		if($('input[name=st_date]').val() > $('input[name=en_date]').val()) {
			alert('시작일이 종료일보다 큰 값이면 안 됩니다.');
			e.preventDefault();
		}
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
</script>

<?php
include_once ('./_tail.php');
?>
