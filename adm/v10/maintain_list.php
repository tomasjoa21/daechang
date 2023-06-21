<?php
$sub_menu = "930120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'maintain';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_list/","",$g5['file_name']); // _list을 제외한 파일명
$qstr .= "&st_date=$st_date&st_time=$st_time&en_date=$en_date&en_time=$en_time&ser_mms_idx=$ser_mms_idx";

$g5['title'] = '정비조치관리';
include_once('./_top_menu_maintain.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];


$sql_common = " FROM {$g5_table_name} AS ".$pre."
                LEFT JOIN {$g5['mms_table']} AS mms ON mms.mms_idx = ".$pre.".mms_idx
"; 

$where = array();
$where[] = " ".$pre."_status NOT IN ('trash','delete') ";   // 디폴트 검색조건

// com_idx 조건
$where[] = " ".$pre.".com_idx IN (".$_SESSION['ss_com_idx'].") ";

if ($stx) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'trm_idx_tag' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ( $sfl == 'tgc.com_idx' || $sfl == 'tgc.mms_idx' ) :
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
if ($st_date) {
    if ($st_time) {
        $where[] = " mnt_date >= '".$st_date.' '.$st_time."' ";
    }
    else {
        $where[] = " mnt_date >= '".$st_date." 00:00:00' ";
    }
}
if ($en_date) {
    if ($en_time) {
        $where[] = " mnt_date <= '".$en_date.' '.$en_time."' ";
    }
    else {
        $where[] = " mnt_date <= '".$en_date." 23:59:59' ";
    }
}

// 설비번호 검색
if ($ser_mms_idx) {
    $where[] = " mnt.mms_idx = '".$ser_mms_idx."' ";
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

$sql = " SELECT SQL_CALC_FOUND_ROWS ".$pre.".*
            , mms.mms_name AS mms_name
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$from_record}, {$rows} 
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
$count = sql_fetch_array( sql_query(" SELECT FOUND_ROWS() as total ") ); 
$total_count = $count['total'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// 각 항목명 및 항목 설정값 정의, 형식: 항목명, colspan, rowspan, 정렬링크여부(타이틀클릭)
$items1 = array(
    "mnt_idx"=>array("번호",0,0,0)
    ,"mms_idx"=>array("설비명",0,0,0)
    ,"mnt_subject"=>array("제목",0,0,0)
    ,"mnt_db_code"=>array("알람코드",0,0,0)
    ,"code_name"=>array("알람내용",0,0,0)
    ,"code_category"=>array("알람분류",0,0,0)
    ,"trm_idx_maintain"=>array("조치분류",0,0,0)
    ,"mnt_db_idx"=>array("디비번호",0,0,0)
    ,"mnt_name"=>array("담당자",0,0,0)
    ,"mnt_date"=>array("정비일자",0,0,0)
    ,"mnt_minute"=>array("정비시간",0,0,0)
);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.tr_stop, .tr_stop .mnt_type_text_p {color:#bbb;}
.ui-dialog .ui-dialog-titlebar-close span {
    display: unset;
    margin: -8px 0 0 -8px;
}
.mnt_setting_target {color:darkorange;}
.td_mnt_subject,.td_mms_idx {
    text-align:left !important;
}
.td_mnt_date, .td_code_name {font-size:0.8em;}
.td_admin a, .td_mnt_date, .td_mnt_name {white-space:nowrap;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총</span><span class="ov_num"> <?php echo number_format($total_count) ?></span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
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
        echo '<option value="'.$row2['mms_idx'].'" '.get_selected($ser_mms_idx, $row2['mms_idx']).'>'.$row2['mms_name'].'</option>';
    }
    ?>
</select>
<script>$('select[name=ser_mms_idx]').val("<?=$ser_mms_idx?>").attr('selected','selected');</script>

<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;">
<input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">
~
<input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
<input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00">

<select name="sfl" id="sfl">
    <?php
    $skips = array('mnt_idx','mms_idx','mnt_db_idx','code_name','code_category','trm_idx_maintain');
    if(is_array($items1)) {
        foreach($items1 as $k1 => $v1) {
            if(in_array($k1,$skips)) {continue;}
            echo '<option value="'.$k1.'" '.get_selected($sfl, $k1).'>'.$v1[0].'</option>';
        }
    }
    ?>
    <?php if($member['mb_level']>=9) { ?>
    <option value="tgc.com_idx" <?=get_selected($sfl, "tgc.com_idx")?>>업체번호</option>
    <?php } ?>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>정비 조치에 관한 이력을 입력하고 관리하는 페이지입니다.</p>
    <p>알람 발행 시 조치사항을 예측하고 예방하기 위한 소중한 정보입니다. 정보 입력 시 관련 알람을 잘 선택해 주시고 내용을 상세히 입력해 주시기 바랍니다.</p>
    <p>엑셀을 등록하실 때는 표준 형식에 맞추어야 합니다. 엑셀 형식이 바뀌면 등록 시 에러가 발생할 수 있습니다. <a href="https://docs.google.com/spreadsheets/d/1uWi8lsZg96DqFQ7Ryy61dJiO1twYUP0Z1mHNZKxol2Y/edit?usp=sharing" target="_blank">[엑셀샘플보기]</a></p>
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
		<th scope="col">
			<label for="chkall" class="sound_only">항목 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
        <?php
        $skips = (!$member['mb_manager_yn']) ? array('com_idx','mnt_idx','mnt_db_idx','mnt_db_code') : array('mnt_idx','mnt_db_idx','mnt_db_code');
        if(is_array($items1)) {
            foreach($items1 as $k1 => $v1) {
                if(in_array($k1,$skips)) {continue;}
                $row['colspan'] = ($v1[1]>1) ? ' colspan="'.$v1[1].'"' : '';   // colspan 설정
                $row['rowspan'] = ($v1[2]>1) ? ' rowspan="'.$v1[2].'"' : '';   // rowspan 설정
                // 정렬 링크
                if($v1[3]>0)
                    echo '<th scope="col" '.$row['colspan'].' '.$row['rowspan'].'>'.subject_sort_link($k1).$v1[0].'</a></th>';
                else
                    echo '<th scope="col" '.$row['colspan'].' '.$row['rowspan'].'>'.$v1[0].'</th>';
            }
        }
        ?>
		<th scope="col" id="mb_list_mng">수정</th>
	</tr>
	</thead>
	<tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $row['mms'] = get_table_meta('mms','mms_idx',$row['mms_idx']);
        // print_r2($row['mms']);
        $row['mnt_db_tables'] = explode("_",$row['mnt_db_table']);
        if($row['mnt_db_table']=='code') {
            $row['code'] = get_table_meta('code','cod_idx',$row['mnt_db_idx']);
            // print_r2($row['code']);
            $row['code']['code'] = $row['code']['cod_code'];
            $row['code_term'] = get_table_meta('term','trm_idx',$row['code']['trm_idx_category']);
        }
        else {
            $row['code'] = get_table_meta('tag_code','tgc_idx',$row['mnt_db_idx']);
            $row['code']['code'] = $row['code']['tgc_code'];
            $row['code']['cod_name'] = $row['code']['tgc_name'];
            $row['code_term'] = get_table_meta('term','trm_idx',$row['code']['trm_idx_tag']);
        }
        // print_r2($row['code']);
        // print_r2($row['code_term']);

		// 버튼
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';
        
        $bg = 'bg'.($i%2);
        // 1번 라인 ================================================================================
        echo '<tr class="'.$bg.' tr_'.$row[$pre.'_status'].'" tr_id="'.$row[$pre.'_idx'].'">'.PHP_EOL;
        ?>
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?php echo $i ?>]" value="<?php echo $row[$pre.'_idx'] ?>" id="<?=$pre?>_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row[$pre.'name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <?php
        $skips = (!$member['mb_manager_yn']) ? array('com_idx','mnt_idx','mnt_db_idx','mnt_db_code') : array('mnt_idx','mnt_db_idx','mnt_db_code');
        if(is_array($items1)) {
//            print_r2($items1);
            foreach($items1 as $k1 => $v1) {
                if(in_array($k1,$skips)) {continue;}
                // echo $k1.' / '.$row[$k1].'<br>';

                $list[$k1] = $row[$k1];

                if(preg_match("/_price$/",$k1)) {
                    $list[$k1] = number_format($row[$k1]);
                }
                else if(preg_match("/_dt$/",$k1)) {
                    $list[$k1] = '<span class="font_size_8">'.substr($row[$k1],0,16).'</span>';
                }
                else if($k1=='mms_idx') {
                    $list[$k1] = cut_str($row['mms']['mms_name'],15,'..');
                }
                else if($k1=='code_name') {
                    $list[$k1] = cut_str($row['code']['cod_name'],15,'..');
                }
                else if($k1=='code_category') {
                    $list[$k1] = '<span class="font_size_8">'.$row['code_term']['trm_name'].'</span>';
                }
                else if($k1=='trm_idx_maintain') {
                    $list[$k1] = '<span class="font_size_8">'.$g5['maintain_name'][$row['trm_idx_maintain']].'</span>';
                }
                else if($k1=='mnt_minute') {
                    // $list[$k1] = '<span class="font_size_8">'.date('H:i', mktime(0,$row['mnt_minute'])).'</span>';
                    $list[$k1] = '<span class="font_size_8">'.sec2hms($row['mnt_minute']).'</span>';
                }
                else if($k1=='mnt_status') {
                    $list[$k1] = '<span class="font_size_8">'.$g5['set_status_value'][$row[$k1]].'</span>';
                }

                $row['colspan'] = ($v1[1]>1) ? ' colspan="'.$v1[1].'"' : '';   // colspan 설정
                $row['rowspan'] = ($v1[2]>1) ? ' rowspan="'.$v1[2].'"' : '';   // rowspan 설정
                echo '<td class="td_'.$k1.'" '.$row['colspan'].' '.$row['rowspan'].'>'.$list[$k1].'</td>';
            }
        }
        echo '<td class="td_admin">'.$s_view.' '.$s_mod.'</td>'.PHP_EOL;
        echo '</tr>'.PHP_EOL;	
	}
	if ($i == 0)
		echo '<tr><td colspan="20" class="empty_table">자료가 없습니다.</td></tr>';
	?>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="javascript:alert('작업중입니다.')" id="btn_excel_upload" class="btn btn_01" style="margin-right:50px;">엑셀등록</a>
    <?php if(!auth_check($auth[$sub_menu],"w",1)) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn">
    <?php } ?>
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01">추가하기</a>
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div id="modal01" title="엑셀 파일 업로드" style="display:none;">
    <form name="form02" id="form02" action="./<?=$fname?>_excel_upload.php" onsubmit="return form02_submit(this);" method="post" enctype="multipart/form-data">
        <table>
        <tbody>
        <tr>
            <td style="line-height:130%;padding:10px 0;">
                <ol>
                    <li>엑셀은 97-2003통합문서만 등록가능합니다. (*.xls파일로 저장)</li>
                    <li>엑셀은 하단에 탭으로 여러개 있으면 등록 안 됩니다. (한개의 독립 문서이어야 합니다.)</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <input type="file" name="file_excel" onfocus="this.blur()">
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <button type="submit" class="btn btn_01">확인</button>
            </td>
        </tr>
        </tbody>
        </table>
    </form>
</div>

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

    // 엑셀등록 버튼
    $( "#btn_excel_upload" ).on( "click", function(e) {
        e.preventDefault();
        $( "#modal01" ).dialog( "open" );
    });
    $( "#modal01" ).dialog({
        autoOpen: false
        , position: { my: "right-10 top-10", of: "#btn_excel_upload"}
    });


});

function form01_submit(f)
{
	if(document.pressed == "테스트입력") {
		window.open('<?=G5_URL?>/device/code/form.php');
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

function form02_submit(f) {
    if (!f.file_excel.value) {
        alert('엑셀 파일(.xls)을 입력하세요.');
        return false;
    }
    else if (!f.file_excel.value.match(/\.xls$|\.xlsx$/i) && f.file_excel.value) {
        alert('엑셀 파일만 업로드 가능합니다.');
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
