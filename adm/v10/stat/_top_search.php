<?php
if (!defined('_GNUBOARD_')) exit;

// print_r2($_REQUEST);
// exit;

// 보고서별 기간 설정
$mode = $mode ?: 'week';
$st_ymd_offset = 0;
if($g5['file_name']=='kpi_maintain') {
    if(!$st_date) {
        $st_ymd_offset = ($mode == 'week') ? 86400*30 : 86400*365/3;
    }
}

// 당월, 당일
// $st_ymd = date("Y-m-01",G5_SERVER_TIME);
$st_ymd = date("Y-m-d",G5_SERVER_TIME-$st_ymd_offset);
$st_ym_first = date("Y-m-01",G5_SERVER_TIME);
$ym_days = date("t",G5_SERVER_TIME);
$en_ymd = date("Y-m-".$ym_days,G5_SERVER_TIME);
$today = date("Y-m-d",G5_SERVER_TIME);
$yesterday = date("Y-m-d",G5_SERVER_TIME-86400);
$tomorrow = date("Y-m-d",G5_SERVER_TIME+86400);
//echo $today.'<br>';

// st_date, en_date
switch ($g5['file_name']) {
    case ( $g5['file_name'] == 'output' ) :
        $st_date = $st_date ?: $st_ym_first;
        break;
    default :
        $st_date = $st_date ?: date($st_ymd);
        break;
}
$en_date = $en_date ?: date("Y-m-d");
$en_date2 = ($st_date==$en_date) ? '' : ' ~ '.$en_date; // wave(~) mark before en_date.

$member['com_idx'] = $_SESSION['ss_com_idx'] ?: $member['com_idx'];
$com = get_table_meta('company','com_idx',$member['com_idx']);
$com_idx = $com['com_idx'];
// print_r2($com);



$st_timestamp = strtotime($st_date.' 00:00:00');
$en_timestamp = strtotime($en_date.' 23:59:59');

// echo $mmg_idx.' ---- mmg_idx <br>';
// echo $mms_idx.' ---- mms_idx <br>';
// exit;

// breadcrumb 표현을 위해서 up_names 추출
$sql = "SELECT mmg.mmg_idx
            , CONCAT( REPEAT('   ', COUNT(parent.mmg_idx) - 1), mmg.mmg_name) AS name
            , (COUNT(parent.mmg_idx) - 1) AS depth
            , GROUP_CONCAT(cast(parent.mmg_idx as char) ORDER BY parent.mmg_left) AS up_idxs
            , GROUP_CONCAT(parent.mmg_name ORDER BY parent.mmg_left SEPARATOR '|') AS up_names
            , (CASE WHEN mmg.mmg_right - mmg.mmg_left = 1 THEN 1 ELSE 0 END ) AS leaf_node_yn
            , mmg.mmg_left
        FROM g5_1_mms_group AS mmg,
            g5_1_mms_group AS parent
        WHERE mmg.mmg_left BETWEEN parent.mmg_left AND parent.mmg_right
            AND mmg.com_idx = '".$_SESSION['ss_com_idx']."'
            AND parent.com_idx = '".$_SESSION['ss_com_idx']."'
            AND mmg.mmg_status NOT IN ('trash','delete') AND parent.mmg_status NOT IN ('trash','delete')
        GROUP BY mmg.mmg_idx
        ORDER BY mmg.mmg_left
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    $up_names[$row['mmg_idx']] = explode("|",$row['up_names']);
    $mmg_select .= '<option value="'.$row['mmg_idx'].'">'.preg_replace("/\|/"," > ",$row['up_names']).'</option>';
}
// echo $mmg_idx.'<br>';
// print_r2($up_names);

// 그룹 숨김처리
switch ($g5['file_name']) {
    case ( $g5['file_name'] == 'output' ) :
        $mmg_display = 'none';
        break;
    default :
        $mmg_display = 'block';
        break;
}



// down_idxs를 뽑아두자. 라인별 합계를 위해서 미리 추출
$sql = "SELECT parent.mmg_idx
            , GROUP_CONCAT(cast(mmg.mmg_idx as char) ORDER BY mmg.mmg_left) AS down_idxs
            , GROUP_CONCAT(cast(mmg.mmg_name as char) ORDER BY mmg.mmg_left) AS down_names
        FROM {$g5['mms_group_table']} AS mmg, 
            {$g5['mms_group_table']} AS parent
        WHERE mmg.mmg_left BETWEEN parent.mmg_left AND parent.mmg_right
            AND mmg.com_idx='".$com_idx."'
            AND parent.com_idx='".$com_idx."'
            AND mmg.mmg_status NOT IN ('trash','delete')
            AND parent.mmg_status NOT IN ('trash','delete')
        GROUP BY parent.mmg_idx
        ORDER BY parent.mmg_left
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    $mmg_down_idxs[$row['mmg_idx']] = explode(",",$row['down_idxs']);
}
// print_r2($mmg_down_idxs);
// print_r2($mmg_down_idxs[$mmg_idx]); //mmg_idxs (그룹 번호들)

// mms_idxes 를 뽑아두어야 함 (이후 계산에서 해당 mms 관련 데이터들만 뽑아와야 함)
// 선택 라인이 있는 경우
if( is_array($mmg_down_idxs[$mmg_idx]) ) {
    // mmg_idxes 먼저 설정
    $mmg_array = $mmg_down_idxs[$mmg_idx];
    // print_r2($mmg_array);
    $sql_mmgs = " AND mmg_idx IN (".implode(',',$mmg_array).") ";
    // echo $sql_mmgs.'<br>';
    // mmg & parent 구조안에 있을 때 sql
    $sql_mmg_parent = " AND mmg.mmg_idx IN (".implode(',',$mmg_array).")
                        AND parent.mmg_idx IN (".implode(',',$mmg_array).")
    ";
    // echo $sql_mmg_parent;

    // mms_idxes 설정
    // $sql = "SELECT GROUP_CONCAT(mms_idx) AS mmses
    //         FROM {$g5['mms_table']} AS mms
    //         WHERE mms_status NOT IN ('trash','delete') 
    //             AND mms.com_idx = '".$com_idx."'
    //             AND mms.mms_output_yn = 'Y'
    //             AND mmg_idx IN (".implode(',',$mmg_down_idxs[$mmg_idx]).")
    //         ORDER BY mms_idx
    // ";
    $sql = "SELECT GROUP_CONCAT(mms_idx) AS mmses
                ,GROUP_CONCAT(mms_name SEPARATOR '|') AS mms_names
            FROM {$g5['mms_table']} AS mms
            WHERE mms_status NOT IN ('trash','delete') 
                AND mms.com_idx = '".$com_idx."'
                AND mmg_idx IN (".implode(',',$mmg_down_idxs[$mmg_idx]).")
            ORDER BY mms_idx
    ";
    // echo $sql.'<br>';
    $mms1 = sql_fetch($sql,1);
    // print_r2($mms1);
    // in case of mms_idx(설비)
    if($mms_idx) {
        $mms1['mmses'] = $mms_idx;
    }
    // echo $mms1['mmses'];
    $mms_array = explode(",",$mms1['mmses']);
    $mms_name_array = explode(",",$mms1['mms_names']);
    // print_r2($mms_array);
    $sql_mmses = $mms1['mmses'] ? " AND mms_idx IN (".$mms1['mmses'].") " : "";
    // echo $sql_mmses.'<br>';

    // arm join인 경우는 mms_idx가 명확하지 않아서 재정의 필요 
    $sql_mmses1 = " AND arm.mms_idx IN (".implode(",",$mms_array).") ";
    // 게시판용 mms_idx 조건절
    $sql_mmses2 = $mms_array[0] ? " AND wr_2 IN (".implode(",",$mms_array).") " : "";
    // arm join인 경우는 mms_idx가 명확하지 않아서 재정의 필요 
    $sql_mmses3 = " AND dta.mms_idx IN (".implode(",",$mms_array).") ";
}
// 선택라인이 없으면 전체에서 추출한다.
else {

}

/*
// 교대별 기종별 목표 먼저 추출 (아래 부분 목표 추출하는 부분에서 활용합니다.)
$sql = "SELECT shf_idx, sig_shf_no, mmi_no, sig_item_target
        FROM {$g5['shift_item_goal_table']} AS sig 
            LEFT JOIN {$g5['mms_item_table']} AS mmi ON mmi.mmi_idx = sig.mmi_idx
        WHERE (1)
            {$sql_mmses}
        ORDER BY shf_idx, sig_shf_no 
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
for($j=0;$row=sql_fetch_array($rs);$j++){
    // print_r2($row1);
    $target['shift_no_mmi'][$row['shf_idx']][$row['sig_shf_no']][$row['mmi_no']] += $row['sig_item_target'];    // 교대별 기종별 목표
}
*/


// 목표추출 get target fetch
// 전체기간 설정이 있는 경우는 마지막 부분에서 돌면서 없는 날짜 목표를 채워줍니다.
$sql = "SELECT mms_idx, shf_idx, shf_period_type
        , shf_name
        , shf_start_time
        , shf_end_time
        , shf_end_nextday
        , shf_start_dt AS db_shf_start_dt
        , shf_end_dt AS db_shf_end_dt
        , GREATEST('".$st_date." 00:00:00', shf_start_dt ) AS shf_start_dt
        , LEAST('".$en_date." 23:59:59', shf_end_dt ) AS shf_end_dt
        FROM {$g5['shift_table']}
        WHERE com_idx = '".$com['com_idx']."'
            AND shf_status NOT IN ('trash','delete')
            AND shf_end_dt >= '".$st_date."'
            AND shf_start_dt <= '".$en_date."'
            {$sql_mmses}
        ORDER BY mms_idx, shf_period_type, shf_start_dt
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
$byunit = 86400;
for($i=0;$row=sql_fetch_array($rs);$i++){
    $row['mmg_idx'] = $g5['mms'][$row['mms_idx']]['mmg_idx'];
    // print_r2($row);

    // 날짜범위를 for 돌면서 배열변수 생성
    $ts1 = strtotime(substr($row['shf_start_dt'],0,10));    // 시작 timestamp
    $ts2 = strtotime(substr($row['shf_end_dt'],0,10));    // 종료 timestamp
    // 종료일시가 오전인 경우는 전날로 바꾸어서 중복이 생기지 않도록 처리한다.
    if(substr($row['shf_end_dt'],11,2) < 12) {
        $ts2 = strtotime(substr($row['shf_end_dt'],0,10))-86400;
    }
    // echo date("Y-m-d",$ts1).'~'.date("Y-m-d",$ts2).'<br>';
    for($k=$ts1;$k<=$ts2;$k+=$byunit) {
        $date1 = preg_replace("/[ :-]/","",date("Y-m-d",$k));   // 날짜중에서 일자 추출하여 배열키값으로!

        // 전체기간 설정일 때는 동일설비, 같은 날짜값이 있으면 통과, 중복 계산하지 않도록 한다.
        if( $row['shf_period_type'] && $mms_date[$row['mms_idx']][$date1] ) {
            continue;
        }

        $date2 = preg_replace("/[ :-]/","",date("Y-m",$k));     // 날짜중에서 월 추출하여 배열키값으로!
        $date3 = preg_replace("/[ :-]/","",date("Y",$k));       // 년도만
        $week1 = date("w",$k); // 0 (for Sunday) through 6 (for Saturday)
        // 주차값 (1년 중 몇 주, date('w')랑 기준이 달라서 일요일인 경우 다음차수로 넘김)
        $week2 = (!$week1) ? date("W",$k)+1 : date("W",$k);
        // echo $week1.'(0=sunsay..) : '.$week2.'주차 : ';
        // echo date("Y-m-d",$k).'(오늘날짜) : ';
        // echo date('Y-m-d', strtotime(date("Y-m-d",$k)." -".$week1."days")).'(주첫날)<br>';
        $target['week_day'][$week2] = date('Y-m-d', strtotime(date("Y-m-d",$k)." -".$week1."days"));  // 주차의 시작 일요일

        $target['date'][$date1] += $row['shf_target_sum'];  // 날짜별 목표
        $target['week'][$week2] += $row['shf_target_sum'];  // 주차별 목표
        $target['month'][$date2] += $row['shf_target_sum'];  // 월별 목표
        $target['year'][$date3] += $row['shf_target_sum'];  // 연도별 목표
        $target['mms'][$row['mms_idx']] += $row['shf_target_sum'];  // 설비별 목표
        $target['mmg'][$row['mmg_idx']] += $row['shf_target_sum'];  // 그룹별 목표
        $target['total'] += $row['shf_target_sum'];  // 전체 목표
        // 날짜별 교대별 목표
        for($j=1;$j<4;$j++) {
            // echo $row['shf_target_'.$j].'<br>';
            $target['date_shift'][$date1][$j] += $row['shf_target_'.$j];    // 날짜별 교대별 목표
            $target['shift'][$j] += $row['shf_target_'.$j];    // 교대별 목표만
            $target['mms_shift'][$row['mms_idx']][$j] += $row['shf_target_'.$j];    // 설비별 교대별 목표만
            // 날짜별 교대별 목표. $target['shift_no_mmi'][shf_idx][shf_no][mmi_no] = 200 과 같은 구조로 되어 있음
            if( is_array($target['shift_no_mmi'][$row['shf_idx']]) ) {
                if(is_array($target['shift_no_mmi'][$row['shf_idx']][$j])) {
                    // $j=교대번호
                    foreach($target['shift_no_mmi'][$row['shf_idx']][$j] as $k1=>$v1) {
                        // k1=기종번호, $v1=목표값
                        $target['mms_mmi'][$row['mms_idx']][$k1] += $v1;    // 설비별 기종목표
                    }
                }
            }
        }
        $mms_date[$row['mms_idx']][$date1] = 1; // 중복 체크를 위해서 배열 생성해 둠
        // echo '------<br>';
    }
    // echo '<br>--------------<br>';
}
// print_r2($mms_date);
// print_r2($target);



// 비가동추출 get offwork time
// 전체기간 설정이 있는 경우는 마지막 부분에서 돌면서 없는 날짜 목표를 채워줍니다.
// 설비별 가동율도 계산해야 하지만 여기에서는 제외하는 걸로 합니다. (나중에 추가하자.)
$sql = "SELECT mms_idx, off_idx, off_period_type
        , off_start_time AS db_off_start_time
        , off_end_time AS db_off_end_time
        , FROM_UNIXTIME(off_start_time,'%Y-%m-%d %H:%i:%s') AS db_off_start_ymdhis
        , FROM_UNIXTIME(off_end_time,'%Y-%m-%d %H:%i:%s') AS db_off_end_ymdhis
        , GREATEST('".$st_timestamp."', off_start_time ) AS off_start_time
        , LEAST('".$en_timestamp."', off_end_time ) AS off_end_time
        , FROM_UNIXTIME( GREATEST('".$st_timestamp."', off_start_time ) ,'%Y-%m-%d %H:%i:%s') AS off_start_ymdhis
        , FROM_UNIXTIME( LEAST('".$en_timestamp."', off_end_time ) ,'%Y-%m-%d %H:%i:%s') AS off_end_ymdhis
        FROM {$g5['offwork_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
            AND off_status IN ('ok')
            AND off_end_time >= '".$st_timestamp."'
            AND off_start_time <= '".$en_timestamp."'
            AND mms_idx IN (0)
        ORDER BY mms_idx DESC, off_period_type, off_start_time
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
$byunit = 86400;
for($i=0;$row=sql_fetch_array($rs);$i++){
    // print_r2($row);
    $offwork[$i]['mms_idx'] = $row['mms_idx'];
    $offwork[$i]['start'] = date("His",$row['db_off_start_time']);
    $offwork[$i]['end'] = date("His",$row['db_off_end_time']);
    // print_r2($offwork[$i]);
    // echo '<br>----<br>';
    // echo $i.'번째  <br>';
    // 앞에서 정의한 겹치는 시간이 있으면 빼야 함, 중복 계산하지 않도록 한다.
    if( is_array($offwork) ) {
        $offworkold = $offwork;
        for($j=0;$j<sizeof($offworkold);$j++){
            // print_r2($offworkold[$j]);
            // 완전 내부 포함인 경우는 중복 제외
            if( $offwork[$i]['start'] > $offworkold[$j]['start'] && $offwork[$i]['end'] < $offworkold[$j]['end'] ) {
                unset($offwork[$i]);
            }
            // 걸쳐 있는 경우
            else if( $offwork[$i]['start'] < $offworkold[$j]['end'] && $offwork[$i]['end'] > $offworkold[$j]['start'] ) {
                if( $offwork[$i]['start'] < $offworkold[$j]['start'] ) {
                    $offwork[$i]['end'] = $offworkold[$j]['start'];
                }
                if( $offwork[$i]['end'] > $offworkold[$j]['end'] ) {
                    $offwork[$i]['start'] = $offworkold[$j]['end'];
                }
            }
        }
    }
    // echo '<br>정리<br>';
    // print_r2($offwork[$i]);
    // echo '<br>----------------------------------------<br>';

    
}
// print_r2($mms_date);
// print_r2($offwork);

// 하루의 전체 비가동 시간 계산
$off_total = 0;
for($j=0;$j<@sizeof($offwork);$j++){
    // print_r2($offwork[$j]);
    $off_total += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
}
// echo $off_total.'<br>';

add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/function.date.js"></script>', 0);
add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js"></script>', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/jquery-nice-select-1.1.0/css/nice-select.css">', 0);
?>
<div class="stat_wrapper">
    <div class="title01">
        <?=$com['com_name']?>
        <span class="title_breadcrumb">
            <?php
            if($mmg_idx && is_array($up_names[$mmg_idx])) {
                // print_r2($up_names[$mmg_idx]);
                for($i=0;$i<sizeof($up_names[$mmg_idx]);$i++) {
                    echo ' > '.$up_names[$mmg_idx][$i];
                }
                if($mms_idx) {
                    $mms2 = get_table('mms','mms_idx',$mms_idx);
                    echo ' > '.$mms2['mms_name'];
                }
            }
            ?>
        </span><!-- > 제1공장 > 1라인 -->
        <span class="text01 title_date"><?=$st_date?><?=$en_date2?></span>
    </div>
    <!-- selections -->
    <form id="form01" name="form01" class="form01" onsubmit="return sch_submit(this);" method="get">
        <input type="hidden" name="com_idx" value="<?=$com['com_idx']?>" class="frm_input">
        <input type="hidden" name="mode" value="<?=$mode?>" class="frm_input">
        <input type="text" name="st_date" id="st_date" value="<?=$st_date?>" class="frm_input">
        <span class="text01">~</span>
        <input type="text" name="en_date" id="en_date" value="<?=$en_date?>" class="frm_input">
        <div class="text02 prev_month"><i class="fa fa-chevron-left"></i></div>
        <div class="text02 this_month" s_ymd="<?=$st_ym_first?>" e_ymd="<?=$en_ymd?>">이번달</div>
        <div class="text02 next_month"><i class="fa fa-chevron-right"></i></div>
        <div class="text02 prev_day"><i class="fa fa-chevron-left"></i></div>
        <div class="text02 this_day" s_ymd="<?=$today?>" e_ymd="<?=$today?>">오늘</div>
        <div class="text02 next_day"><i class="fa fa-chevron-right"></i></div>
        <div>
            <div style="display:inline-block;">
            <select name="mmg_idx" id="mmg_idx" style="display:<?=$mmg_display?>">
                <option value="">전체</option>
                <?=$mmg_select?>
            </select>
            </div>
            <div style="display:none;"><!-- inline-block -->
            <select name="mms_idx" id="mms_idx" mms_idx="<?=$mms_idx?>">
                <option value="">전체</option>
                <?=$mms_select?>
            </select>
            </div>
        </div>
        <input type="submit" class="btn_submit" value="확인">
    </form>
    <script>
    // 공장선택
    $('#mmg_idx').val('<?=$mmg_idx?>').attr('selected','selected');
    // 설비선택
    $('#mms_idx').val('<?=$mms_idx?>').attr('selected','selected');

    $(function(e){
        $('#mmg_idx, #mms_idx').niceSelect();
    });
    function sch_submit(f){
        
        if(f.st_date.value && f.en_date.value){
            var st_d = new Date(f.st_date.value);
            var en_d = new Date(f.en_date.value);
            if(st_d.getTime() > en_d.getTime()){
                alert('검색날짜의 최종날짜를 시작날짜보다 과거날짜를 입력 할 수는 없습니다.');
                return false;
            }
        }
        
        return true;
    }
    </script>
</div><!-- .stat_wrapper -->

<script>
$(function(e){
	// group select change
	$(document).on('change','select[name^=mmg]',function(e) {
		// console.log( $(this).attr('id') );
		var mmg_depth = $(this).attr('id').replace('mmg','');
		var mmg_idx = $(this).val();
		// console.log( 'select tag count: '+$('select[name^=mmg]').length );
		var mmg_select_count = $('select[name^=mmg]').length;

		// 일단 제거한 후 mms_idx가 있으면 보임
		$('#mms_idx').closest('div').hide();

		// 선택항목이 있는 경우만
		if(mmg_idx) {
            group_loading(<?=$com_idx?>, mmg_idx);
		}

	});
	// default group loading.
    <?php if($mmg_idx) { ?>
	group_loading(<?=$com_idx?>, <?=$mmg_idx?>);
    <?php } ?>

	// prev Month
	$(document).on('click','.prev_month',function(e) {
		// console.log( $('#st_date').val() );
		this_day = $('#st_date').val();
		$('#st_date').val( getPrevMonthFirst( this_day ) );
		$('#en_date').val( getPrevMonthLast( this_day ) );
	});
	// next Month
	$(document).on('click','.next_month',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNextMonthFirst( this_day ) );
		$('#en_date').val( getNextMonthLast( this_day ) );
	});
	// prev Day
	$(document).on('click','.prev_day',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNthPrevDay( this_day, 1 ) );
		$('#en_date').val( getNthPrevDay( this_day, 1 ) );
	});
	// prev Day
	$(document).on('click','.next_day',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNthNextDay( this_day, 1 ) );
		$('#en_date').val( getNthNextDay( this_day, 1 ) );
	});

	// this month, this day click
	$(document).on('click','div[s_ymd]',function(e) {
		$('#st_date').val( $(this).attr('s_ymd') );
		$('#en_date').val( $(this).attr('e_ymd') );
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

});

function group_loading(com_idx, mmg_idx) {

    // $('#mms_idx').val('').attr("selected","selected");
    $('#mms_idx option').each(function(i,v){
        if( $(v).val() != '' ) { 
            $(this).remove();
        }
    });

	// console.log(mmg_idx);
	//-- 디버깅 Ajax --//
	$.ajax({
		url:g5_user_admin_ajax_url+'/mms.list.php',
		data:{"aj":"grp","com_idx":com_idx,"mmg_idx":mmg_idx},
		dataType:'json', 
        timeout:15000, 
        beforeSend:function(){}, 
        success:function(res){
			// console.log(res);
			//var prop1; for(prop1 in res.rows) { console.log( prop1 +': '+ res.rows[prop1] ); }
			if(res.result == true) {
                if(res.list!=null) {
                    $.each(res.list, function(i,v){
                        $('<option value="'+ v['mms_idx'] +'">' + v['mms_name'] + '</option>').appendTo('#mms_idx');
                    });
                    // mms_idx 값이 있으면 선택상태로..
                    if($('#mms_idx').attr('mms_idx')!='') {
                        // console.log($('#mms_idx').attr('mms_idx'));
                        $('#mms_idx').val($('#mms_idx').attr('mms_idx')).attr('selected','selected');
                    }
                    $('#mms_idx').attr('mms_idx',''); // 두 번째 셀렉박스부터 초기화 상태로 보이게 설정
                    $('#mms_idx').closest('div').css("display","inline-block");
                    $('#mms_idx').niceSelect('update');
                }
			}
			else {
				console.log(res.msg);
			}
		},
		error:function(xmlRequest) {
			// console.log('<?=$row['mms_name']?>(<?=$row['mms_idx']?>): error');
			console.log('Status: ' + xmlRequest.status);
			console.log('statusText: ' + xmlRequest.statusText);
			console.log('responseText: ' + xmlRequest.responseText);
		}
	});

}
</script>