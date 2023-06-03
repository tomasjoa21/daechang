<?php
$sub_menu = "925900";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

$pre = 'dta';

$g5['title'] = '비가동 관리';
include_once('./_top_menu_setting.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM {$g5['data_downtime_table']} AS dta
                    LEFT JOIN {$g5['mms_table']} AS mms ON dta.mms_idx = mms.mms_idx
                    LEFT JOIN {$g5['mms_status_table']} AS mst ON dta.mst_idx = mst.mst_idx
";

$where = array();
//디폴트 조건
$where[] = " dta.com_idx = '{$_SESSION['ss_com_idx']}' ";

if ($stx && $sfl) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'mms_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(mb_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
		case ($sfl == 'dta_more') :
            $where[] = " dta_value >= '".$stx."' ";
            break;
		case ($sfl == 'dta_less') :
            $where[] = " dta_value <= '".$stx."' ";
            break;
		case ($sfl == 'dta_range') :
            $stxs = explode("-",$stx);
            $where[] = " dta_value >= '".$stxs[0]."' AND dta_value <= '".$stxs[1]."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}

// 설비
if ($ser_mms_idx) {
    $where[] = " dta.mms_idx = '".$ser_mms_idx."' ";
}

// 기간 검색
if ($st_date) {
    if ($st_time) {
        $where[] = " dta_start_dt >= '".strtotime($st_date.' '.$st_time)."' ";
    }
    else {
        $where[] = " dta_start_dt >= '".strtotime($st_date.' 00:00:00')."' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " dta_end_dt <= '".strtotime($en_date.' '.$en_time)."' ";
    }
    else {
        $where[] = " dta_end_dt <= '".strtotime($en_date.' 23:59:59')."' ";
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "dta.dta_reg_dt";
    $sod = "desc";
}


$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT dta.*, mms.mms_name, mst.mst_name, mst.mst_type
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// echo $sql.'<br>';
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>

</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총</span><span class="ov_num"> <?php echo number_format($total_count) ?></span></span>
</div>


<form id="fsearch" name="fsearch" class="local_sch01 local_sch" onsubmit="return sch_submit(this);" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_mms_idx" id="ser_mms_idx">
    <option value="">설비선택</option>
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
        echo '<option value="'.$row2['mms_idx'].'" '.get_selected($ser_mms_idx, $row2['mms_idx']).'>'.$row2['mms_name'].'</option>';
    }
    ?>
</select>
<script>
$('select[name=ser_mms_idx]').val("<?=$ser_mms_idx?>").attr('selected','selected');
$(document).on('change','#ser_mms_idx',function(e){
    $('#fsearch').submit();
});
</script>

<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:90px;">
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:70px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:90px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:70px;" placeholder="00:00:00">

<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <?php
    $skips = array('dta_idx','mms_idx','com_idx','dta_dt','dta_reg_dt');
    if(is_array($items1)) {
        foreach($items1 as $k1 => $v1) {
            if(in_array($k1,$skips)) {continue;}
            echo '<option value="'.$k1.'" '.get_selected($sfl, $k1).'>'.$v1[0].'</option>';
        }
    }
    ?>
	<option value="dta_more"<?php echo get_selected($_GET['sfl'], "dta_more"); ?>>값이상</option>
	<option value="dta_less"<?php echo get_selected($_GET['sfl'], "dta_less"); ?>>값이하</option>
	<option value="dta_range"<?php echo get_selected($_GET['sfl'], "dta_range"); ?>>범위</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>긴급점검, 고장 등으로 설비가 비가동될 때 그 시간을 별도로 설정하고 관리합니다.</p>
    <p>UPH 계산 시 공제 시간에 포함되는 포함됩니다.</p>
</div>

<form name="form01" id="form01" action="./manual_downtime_list_update.php" onsubmit="return form01_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="schrows" value="<?php echo $schrows ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="com_idx" value="<?php echo $_SESSION['ss_com_idx'] ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">
<input type="hidden" name="ser_mms_idx" value="<?=$ser_mms_idx?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="dta_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">설비</th>
        <th scope="col">내용</th>
        <th scope="col">비가동시간</th>
        <th scope="col">등록일시</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for($i=0;$row=sql_fetch_array($result);$i++){

        $s_mod = '<a href="./manual_downtime_form.php?'.$qstr.'&amp;w=u&amp;dta_idx='.$row['dta_idx'].'" class="btn btn_03">수정</a>';
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['dta_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?php echo $i ?>]" value="<?php echo $row[$pre.'_idx'] ?>" id="<?=$pre?>_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['dta_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mms_name"><?=$row['mms_name']?> <span class="font_size_7"><?=$row['mms_idx']?></span></td>
        <td class="td_mst_name"><?=$row['mst_name']?></td>
        <td class="td_dta_start_end_dt"><?=$row['dta_start_dt']?> ~ <?=$row['dta_end_dt']?></td>
        <td class="td_dta_reg_dt"><?=$row['dta_reg_dt']?></td>
        <td class="td_mng"><?=$s_mod?></td>
    </tr>
    <?php
    }
    if($i == 0){
        echo "<tr><td colspan='11' class=\"empty_table\">자료가 없습니다.</td></tr>";
    }
    ?>
    </tbody>
    </table>
</div>
<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w',1)) { ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <a href="./manual_downtime_form.php" id="dta_add" class="btn btn_01">추가하기</a>
    <?php } ?>
</div>
</form>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
$(function(e) {
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

    $("input[name$=_date]").datepicker({
        closeText: "닫기",
        currentText: "오늘",
        monthNames: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNamesMin:['일','월','화','수','목','금','토'],
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        //maxDate: "+0d"
    });

    // 마우스 hover 설정
    $(".tbl_head01 tbody tr").on({
        mouseenter: function () {
            //stuff to do on mouse enter
            //console.log($(this).attr('od_id')+' mouseenter');
            //$(this).find('td').css('background','red');
            $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','#e6e6e6 ');
            
        },
        mouseleave: function () {
            //stuff to do on mouse leave
            //console.log($(this).attr('od_id')+' mouseleave');
            //$(this).find('td').css('background','unset');
            $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','unset');
        }    
    });

});

function sch_submit(f){
    
    if(f.st_date.value && f.en_date.value){
        var st_d = new Date(f.st_date.value+' '+f.st_time.value);
        var en_d = new Date(f.en_date.value+' '+f.en_time.value);
        if(st_d.getTime() > en_d.getTime()){
            alert('검색날짜의 최종일시를 시작일시보다 과거일시를 입력 할 수는 없습니다.');
            return false;
        }
    }

    return true;
}

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
