<?php
$sub_menu = "920140";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'data_measure';
$g5_table_name = $g5[$table_name.'_table'];
$pre = 'dta';
$fname = preg_replace("/_list/","",$g5['file_name']); // _list을 제외한 파일명
$qstr .= '&ser_mms_idx='.$ser_mms_idx.'&ser_type_no='.$ser_type_no.'&st_date='.$st_date.'&en_date='.$en_date.'&st_time='.$st_time.'&en_time='.$en_time; // 추가로 확장해서 넘겨야 할 변수들


$g5['title'] = '측정데이터';
// @include_once('./_top_menu_data.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// Get default mms_idx for first mms_idx.
$sql = "SELECT mms_idx, mms_name
        FROM {$g5['mms_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
        ORDER BY mms_idx
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    $mms[$row['mms_idx']] = $row['mms_name'];
}

// Get default mms_idx for first mms_idx.
$sql = "SELECT mms_idx, mms_name
        FROM {$g5['mms_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
        ORDER BY mms_idx
        LIMIT 1
";
// echo $sql.'<br>';
$one = sql_fetch($sql,1);
$ser_mms_idx = $ser_mms_idx ?: $one['mms_idx'];

if(!$ser_mms_idx)
    alert('설비정보가 존재하지 않습니다.');



// get the total data volumn which is not exict. it is just estimation.
$sql = "SELECT * FROM pg_tables
        WHERE tableowner = '".G5_PGSQL_USER."' AND tablename ~ 'g5_1_data_measure_[0-9]+$'
        ORDER BY tablename
";
// echo $sql.'<br>';
$rs = sql_query_pg($sql,1);
for($i=0;$row=sql_fetch_array_pg($rs);$i++) {
    // print_r2($row);
    $row['ar'] = explode("_",$row['tablename']);
    // print_r2($row['ar']);

    $sql2 = " SELECT row_estimate AS cnt FROM hypertable_approximate_row_count('".$row['tablename']."') ";
    // echo $sql2.'<br>';
    $row2 = sql_fetch_pg($sql2,1);
    $table_count = $row2['cnt'];
    // echo $table_count.'<br>';

    // total sum
    $grand_total += $table_count;
    // sum for due company
    if($mms[$row['ar'][4]]) {
        $com_total += $table_count;
    }
}
// echo $grand_total.'<br>';
// echo $com_total.'<br>';

$db_table = 'g5_1_data_measure_'.$ser_mms_idx;
$sql_common = " FROM ".$db_table." ";

$where = array();
$where[] = " 1=1 ";   // pg 디폴트 검색조건

if ($stx && $sfl) {
    switch ($sfl) {
        case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'mms_idx' || $sfl == $pre.'_value' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == 'dta_more') :
            $where[] = " dta_value >= '".$stx."' ";
            break;
		case ($sfl == 'dta_less') :
            $where[] = " dta_value <= '".$stx."' ";
            break;
		case ($sfl == 'dta_range') :
            $stxs = explode("-",$stx);
            // print_r2($stxs);
            $where[] = " dta_value >= '".$stxs[0]."' AND dta_value <= '".$stxs[1]."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
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
        $where[] = " dta_dt >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " dta_dt >= '".$st_date.' 00:00:00'."' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " dta_dt <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " dta_dt <= '".$en_date.' 23:59:59'."' ";
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = $pre."_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";

if(sizeof($where)<=1) {
    $sql = " SELECT row_estimate AS cnt FROM hypertable_approximate_row_count('".$db_table."') ";
}
else {
    $sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
}
// echo $sql.'<br>';
$row = sql_fetch_pg($sql,1);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT *
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$rows} OFFSET {$from_record}
";
// echo $sql;
$result = sql_query_pg($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 각 항목명 및 항목 설정값 정의, 형식: 항목명, colspan, rowspan, 정렬링크여부(타이틀클릭)
// arr0:name, arr1:colspan, arr2:rowspan, arr3: sort
$items1 = array(
    "dta_idx"=>array("번호",0,0,1)
    ,"mms_idx"=>array("설비",0,0,0)
    ,"dta_type_no"=>array("태그명",0,0,0)
    ,"dta_value"=>array("값",0,0,0)
    ,"dta_dt"=>array("일시",0,0,1)
);

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.td_dta_type_no {text-align:left !important;}
.td_dta_type_no span{color:#555;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총</span><span class="ov_num"> <?php echo number_format($total_count) ?></span></span>
    <span class="btn_ov01"><span class="ov_txt">업체측정데이터</span><span class="ov_num"> <?php echo number_format($com_total) ?></span></span>
    <span class="btn_ov01" style="display:<?=(!$member['mb_manager_yn'])?'none':''?>"><span class="ov_txt">전체측정데이터</span><span class="ov_num"> <?php echo number_format($grand_total) ?></span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" onsubmit="return sch_submit(this);" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_mms_idx" id="ser_mms_idx">
    <option value="">설비전체</option>
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

<?php
// // get mms info with meta extened data.
$mms = get_table_meta('mms', 'mms_idx', $ser_mms_idx);
// print_r2($mms);
$sql = "SELECT mta_key, mta_value
        FROM {$g5['meta_table']}
        WHERE mta_key LIKE 'dta_type_label%' 
            AND mta_db_table = 'mms' AND mta_db_id = '".$ser_mms_idx."'
        ORDER BY mta_key
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
// chang query for query speed issue.
// $sql = "SELECT dta_type, dta_no
//         FROM g5_1_data_measure_".$ser_mms_idx."
//         GROUP BY dta_type, dta_no
//         ORDER BY dta_type, dta_no
// ";
// echo $sql.'<br>';
// $rs = sql_query_pg($sql,1);
// for($i=0;$row=sql_fetch_array_pg($rs);$i++) {
for($i=0;$row=sql_fetch_array($rs);$i++) {
    // print_r2($row);
    $opt_type_no_arr = explode("-",$row['mta_key']);
    // print_r2($opt_type_no_arr);
    $opt_dta_type = $opt_type_no_arr[1];
    $opt_dta_no = $opt_type_no_arr[2];
    // 각 태그별 명칭
    $row['dta_type_no_name'] = $mms['dta_type_label-'.$opt_dta_type.'-'.$opt_dta_no] ? 
                                    $mms['dta_type_label-'.$opt_dta_type.'-'.$opt_dta_no]
                                        : $g5['set_data_type_value'][$opt_dta_type].'-'.$opt_dta_no;
    // echo $row['dta_type_no_name'].'<br>';
    $type_no_options .= '<option value="'.$opt_dta_type.'_'.$opt_dta_no.'">'.$row['dta_type_no_name'].'</option>';
}
?>
<select name="ser_type_no" id="ser_type_no">
    <option value="">태그전체</option>
    <?=$type_no_options?>
</select>
<script>$('select[name=ser_type_no]').val("<?=$ser_type_no?>").attr('selected','selected');</script>

기간:
<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;">
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">

<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <?php
    $skips = array('dta_idx','mms_idx','dta_type_no','dta_dt');
    if(is_array($items1)) {
        foreach($items1 as $k1 => $v1) {
            if(in_array($k1,$skips)) {continue;}
            echo '<option value="'.$k1.'" '.get_selected($sfl, $k1).'>'.$v1[0].'</option>';
        }
    }
    ?>
	<option value="dta_more"<?php echo get_selected($_GET['sfl'], "dta_more"); ?>>값이상</option>
	<option value="dta_less"<?php echo get_selected($_GET['sfl'], "dta_less"); ?>>값이하</option>
	<option value="dta_range"<?php echo get_selected($_GET['sfl'], "dta_range"); ?>>범위(시작-끝)</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">

<div class="float_right" style="display:inline-block;">
    <a href="../mms_graph_setting.php?mms_idx=<?=$ser_mms_idx?>" class="btn btn_02 btn_graph_tag">태그값설정</a>
</div>
</form>
<script>
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
</script>
<div class="local_desc01 local_desc" style="display:none;">
    <p>TYPE: </p>
</div>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">

<div class="tbl_head01 tbl_wrap">
	<table class="table table-bordered table-condensed">
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr class="success">
		<th scope="col" style="display:<?=(!$member['mb_manager_yn'])?'none':''?>;">
			<label for="chkall" class="sound_only">항목 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
        <?php
        $skips = array();
        if(is_array($items1)) {
            foreach($items1 as $k1 => $v1) {
                if(in_array($k1,$skips)) {continue;}
                $row['colspan'] = ($v1[1]>1) ? ' colspan="'.$v1[1].'"' : '';   // colspan 설정
                $row['rowspan'] = ($v1[2]>1) ? ' rowspan="'.$v1[2].'"' : '';   // rowspan 설정
                // 정렬 링크
                if($v1[3])
                    echo '<th scope="col" '.$row['colspan'].' '.$row['rowspan'].'>'.subject_sort_link($k1).$v1[0].'</a></th>';
                else
                    echo '<th scope="col" '.$row['colspan'].' '.$row['rowspan'].'>'.$v1[0].'</th>';
            }
        }
        ?>
		<th scope="col" id="mb_list_mng" style="display:<?=(!$member['mb_manager_yn'])?'none':''?>;">수정</th>
	</tr>
	</thead>
	<tbody>
    <?php
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
        $row['com'] = sql_fetch(" SELECT com_name FROM {$g5['company_table']} WHERE com_idx = '".$row['com_idx']."' ");
        $row['mms'] = sql_fetch(" SELECT mms_name FROM {$g5['mms_table']} WHERE mms_idx = '".$row['mms_idx']."' ");
        $row['mmi'] = sql_fetch(" SELECT mmi_name FROM {$g5['mms_item_table']} WHERE mmi_idx = '".$row['dta_mmi_no']."' ");
        $row['mta'] = get_meta('mms',$row['mms_idx']);  // 측정값 레이블형식: [dta_type_label-1-1] => 좌측온도
        // print_r2($row['mta']);
        // print_r2($row);
        
		// 수정 및 발송 버튼
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';
        
        $bg = 'bg'.($i%2);
        // 1번 라인 ================================================================================
        echo '<tr class="'.$bg.' tr_'.$row[$pre.'status'].'" tr_id="'.$row[$pre.'_idx'].'">'.PHP_EOL;
        ?>
        <td class="td_chk" style="display:<?=(!$member['mb_manager_yn'])?'none':''?>;">
            <input type="hidden" name="<?=$pre?>_idx[<?php echo $i ?>]" value="<?php echo $row[$pre.'_idx'] ?>" id="<?=$pre?>_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row[$pre.'name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <?php
        $skips = array();
        if(is_array($items1)) {
//            print_r2($items1);
            foreach($items1 as $k1 => $v1) {
                if(in_array($k1,$skips)) {continue;}

                $list[$k1] = $row[$k1];

                if(preg_match("/_price$/",$k1)) {
                    $list[$k1] = number_format($row[$k1]);
                }
                else if(preg_match("/_dt$/",$k1)) {
                    $list[$k1] = '<span class="font_size_8">'.substr($row[$k1],0,19).'</span>';
//                    $list[$k1] = substr($row[$k1],0,10);
                }
                else if($k1=='mms_idx') {
                    // $list[$k1] = '<span class="font_size_8">'.$ser_mms_idx.'</span>';
                    $list[$k1] = '<span class="font_size_8">'.$g5['mms'][$ser_mms_idx]['mms_name'].'</span>';
                }
                else if($k1=='dta_type_no') {
                    $row['dta_type_no_name'] = $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']] ? 
                                                $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]
                                                    : $g5['set_data_type_value'][$row['dta_type']].'-'.$row['dta_no'];
                    // echo $row['dta_type_no_name'].'<br>';
                    $list[$k1] = $row['dta_type_no_name'].' <span class="font_size_8">'.$row['dta_type'].'-'.$row['dta_no'].'</span>';
                }
                else if($k1=='dta_no') {
                    $list[$k1] = '<a href="?ser_dta_no='.$row[$k1].$qstr1.'">'.$row[$k1].'</a>';
                }
                else if($k1=='dta_type') {
                    $list[$k1] = $ser_dta_type.' <span class="font_size_8">'.$g5['set_data_type_value'][$ser_dta_type].'</span>';
                }

                $row['colspan'] = ($v1[1]>1) ? ' colspan="'.$v1[1].'"' : '';   // colspan 설정
                $row['rowspan'] = ($v1[2]>1) ? ' rowspan="'.$v1[2].'"' : '';   // rowspan 설정
                echo '<td class="td_'.$k1.'" '.$row['colspan'].' '.$row['rowspan'].'>'.$list[$k1].'</td>';
            }
        }
        if($member['mb_manager_yn']) {
            echo '<td class="td_mngsmall">'.$s_mod.'</td>'.PHP_EOL;
        }
        echo '</tr>'.PHP_EOL;	
	}
	if ($i == 0)
		echo '<tr><td colspan="20" class="empty_table">자료가 없습니다.</td></tr>';
	?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <?php if(!auth_check($auth[$sub_menu],"d",1)) { ?>
    <input type="submit" name="act_button" value="테스트입력" onclick="document.pressed=this.value" class="btn_03 btn">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn">
    <?php } ?>
    <?php if(!auth_check($auth[$sub_menu],"w",1)) { ?>
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01">추가하기</a>
    <?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;ser_dta_type='.$ser_dta_type.'&amp;page='); ?>

<script>
$(function(e) {
    // 태그값설정
	$(document).on('click','.btn_graph_tag',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winGraphTag = window.open(href, "winGraphTag", "left=100,top=100,width=520,height=600,scrollbars=1");
        winGraphTag.focus();
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

function form01_submit(f)
{
	if(document.pressed == "Chart(정주기)") {
        self.location = './data_measure_chart.php';
        return false;
	}
	if(document.pressed == "Chart(실측,비주기)") {
        self.location = './data_measure_real_chart.php';
        return false;
	}

	if(document.pressed == "일괄입력") {
        if(confirm('하루치(1일) 데이타를 입력합니다. 창을 닫지 마세요. 입력을 시작합니다.')) {
            winDataInsert = window.open('<?=G5_USER_ADMIN_URL?>/convert/data_measure1.php', "winDataInsert", "left=100,top=100,width=520,height=600,scrollbars=1");
            winDataInsert.focus();
            return false;
        }
        return false;
	}

	if(document.pressed == "테스트입력") {
		window.open('<?=G5_URL?>/device/measure/form.php');
        return false;
	}

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
