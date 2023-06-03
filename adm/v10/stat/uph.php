<?php
$sub_menu = "935110";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");


// 변수 설정, 필드 구조 및 prefix 추출
$pre = 'dta';
$qstr .= '&ser_item_type='.$ser_item_type.'&st_date='.$st_date.'&en_date='.$en_date.'&st_time='.$st_time.'&en_time='.$en_time; // 추가로 확장해서 넘겨야 할 변수들

// st_date, en_date
$st_date = $st_date ?: date("Y-m-01",G5_SERVER_TIME);
$en_date = $en_date ?: date("Y-m-d");
$st_time = $st_time ?: '00:00:00';
$en_time = $en_time ?: '23:59:59';
$st_datetime = $st_date.' '.$st_time;
$en_datetime = $en_date.' '.$en_time;
$st_timestamp = strtotime($st_datetime);
$en_timestamp = strtotime($en_datetime);

$g5['title'] = 'UPH보고서';
include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// // Get all the mms_idx values to make them optionf for selection.
// $sql2 = "   SELECT mms_idx, mms_name
//             FROM {$g5['mms_table']}
//             WHERE com_idx = '".$_SESSION['ss_com_idx']."'
//             ORDER BY mms_idx       
// ";
// // echo $sql2.'<br>';
// $result2 = sql_query($sql2,1);
// for ($i=0; $row2=sql_fetch_array($result2); $i++) {
//     // print_r2($row2);
//     $mms[$row2['mms_idx']] = $row2['mms_name'];
// }
// 
// // 쪼개서 검색
// $sql = "SELECT table_name, table_rows, auto_increment
//             , SUBSTRING_INDEX (SUBSTRING_INDEX(table_name,'_',-3), '_', 1) AS mms_idx
//             , SUBSTRING_INDEX (SUBSTRING_INDEX(table_name,'_',-2), '_', 1) AS dta_type
//             , SUBSTRING_INDEX (SUBSTRING_INDEX(table_name,'_',-1), '_', 1) AS dta_no
//         FROM Information_schema.tables
//         WHERE TABLE_SCHEMA = '".G5_MYSQL_DB."'
//             AND TABLE_NAME REGEXP 'g5_1_data_output_[0-9]{1,4}$'
//         ORDER BY convert(mms_idx, decimal), convert(dta_type, decimal), convert(dta_no, decimal)
// ";
// // echo $sql.'<br>';
// $rs = sql_query($sql,1);
// for($i=0;$row=sql_fetch_array($rs);$i++) {
//     // print_r2($row);
//     // echo ($i+1).'<br>';
//     $row['ar'] = explode("_",$row['table_name']);
//     // print_r2($row['ar']);
//     // 해당 업체 것만 추출, 아니면 통과
//     if($mms[$row['ar'][4]] && in_array($row['dta_no'],array(63,64))) {
//         // echo $mms[$row['ar'][4]].' / ';
//         // echo $g5['set_data_type'][$row['ar'][5]].' / ';
//         // echo $row['ar'][4].'_'.$row['ar'][5].'_'.$row['ar'][6].' (mms_idx='.$row['ar'][4].'/.dat_type='.$row['ar'][5].'/dat_no='.$row['ar'][6].')<br>';
//         $ser_mms_idx = ($ser_mms_idx) ?: $row['ar'][4];
//     }
// }
// // echo $ser_mms_idx.'<br>';

// if(!$ser_mms_idx)
//     alert('설비정보가 존재하지 않습니다.');


// Get the mmi_nos for each mms
$sql = "SELECT mms_idx, mmi_no, mmi_name
        FROM g5_1_mms_item
        WHERE mmi_status = 'ok'
        GROUP BY mms_idx, mmi_no
        ORDER BY mms_idx, mmi_no
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++) {
    // print_r3($row);
    // print_r3('설비: '.$row['mms_idx'].' - '.$row['mmi_no'].'--------------------------');
    $mms_mmi[$row['mms_idx']][] = $row['mmi_no'];
    $mmi_name[$row['mms_idx']][$row['mmi_no']] = $row['mmi_name'];
}
// print_r3($mms_mmi);
// print_r2($mmi_name);



// 공제 get offwork time
// 전체기간 설정이 있는 경우는 마지막 부분에서 돌면서 없는 날짜 목표를 채워줍니다.
$ser_mms_idxs = $ser_mms_idx ? $ser_mms_idx.',0' : '0';
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
            AND mms_idx IN (".$ser_mms_idxs.")
        ORDER BY mms_idx DESC, off_period_type, off_start_time
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
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

// 하루의 전체 공제 시간 계산
for($j=0;$j<sizeof($offwork);$j++){
    // print_r2($offwork[$j]);
    $off_total += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
}
// echo $off_total.'<br>';


$sql_common = " FROM g5_1_xray_inspection ";

$where = array();
$where[] = " (1) ";

if ($stx && $sfl) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'result' ) :
            $where[] = " {$sfl} = '{$stx}' ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(mb_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
		case ($sfl == 'item_lhrh') :
            $sql_select = ", SUBSTRING(qrcode,8,2) AS item_lhrh";
            $sql_group = ", item_lhrh";
            $sql_order = ", item_lhrh";
            $where[] = " SUBSTRING(qrcode,8,2) = '".trim($stx)."' ";
            $sql_search2 .= " AND SUBSTRING(qrcode,8,2) = '".trim($stx)."' ";
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
            $where[] = " {$sfl} LIKE '%{$stx}%' ";
            break;
    }
}

// 설비, 56=ADR1호기(로봇1=63), 60=ADR2호기(로봇2=64)
$ser_mms_idx_array = array(63=>56,64=>60);
// print_r2($ser_mms_idx_array);
if ($ser_mms_idx) {
    $where[] = " machine_id = '".$ser_mms_idx_array[$ser_mms_idx]."' ";
}

// 사양
if ($ser_item_type) {
    $sql_select .= ", SUBSTRING(qrcode,7,1) AS item_type";
    $sql_group .= ", item_type";
    $sql_order .= ", item_type";
    $where[] = " SUBSTRING(qrcode,7,1) = '".trim($ser_item_type)."' ";
    $sql_search2 .= " AND SUBSTRING(qrcode,7,1) = '".trim($ser_item_type)."' ";
}

// 기간 검색
if ($st_date) {
    $where[] = " work_date >= '".$st_date."' ";
}
if ($en_date) {
    $where[] = " work_date <= '".$en_date."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


$sql = " SELECT work_date
            {$sql_select}
            , COUNT(xry_idx) AS output_sum
		{$sql_common}
		{$sql_search}
        GROUP BY work_date{$sql_group}
        ORDER BY work_date DESC{$sql_order}
";
// echo $sql;
$result = sql_query($sql,1);


$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';




$file_name_css_path = G5_USER_ADMIN_STAT_PATH.'/css/'.$g5['file_name'].'.css';
$file_name_css_url = G5_USER_ADMIN_STAT_URL.'/css/'.$g5['file_name'].'.css';

// include_once('./_top.stat.php');

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_STAT_URL.'/css/stat.css">', 0);
if(is_file($file_name_css_path)){
    @add_stylesheet('<link rel="stylesheet" href="'.$file_name_css_url.'">', 0);
}
add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/function.date.js"></script>', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/timepicker/jquery.timepicker.css">', 2);
add_javascript('<script type="text/javascript" src="'.G5_USER_ADMIN_JS_URL.'/timepicker/jquery.timepicker.js"></script>', 2);
?>
<style>
.td_start_end {font-size:0.8em;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_mms_idx" id="ser_mms_idx">
    <option value="">전체설비</option>
    <?php
    // 해당 범위 안의 모든 설비를 select option으로 만들어서 선택할 수 있도록 한다.
    // Get all the mms_idx values to make them optionf for selection.
    $sql2 = "SELECT mms_idx, mms_name
            FROM {$g5['mms_table']}
            WHERE com_idx = '".$_SESSION['ss_com_idx']."'
                AND mms_idx IN (63,64)
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

<select name="ser_item_type" id="ser_item_type">
    <option value="">전체사양</option>
    <option value="R" <?php echo get_selected('ser_item_type', 'R'); ?>>일반사양(R)</option>
    <option value="E" <?php echo get_selected('ser_item_type', 'E'); ?>>EV사양(E)</option>
</select>
<script>$('select[name=ser_item_type]').val("<?=$ser_item_type?>").attr('selected','selected');</script>
<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;">
<!-- <input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00"> -->
~
<input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
<!-- <input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;" placeholder="00:00:00"> -->

<select name="sfl" id="sfl">
    <option value="">전체</option>
    <option value="item_lhrh" <?php echo get_selected($sfl, 'item_lhrh'); ?>>LH,RH</option>
    <option value="result" <?php echo get_selected($sfl, 'result'); ?>>결과(OK,NG)</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>


<div class="local_desc01 local_desc" style="display:no ne;">
    <p>별도의 비가동 설정이 필요한 경우 해당 페이지 설정값을 조정해 주시기 바랍니다. <a href="../system/manual_downtime_list.php">[바로가기]</a></p>
    <p>UPH 계산은 설비의 가동시간을 기반으로 할 수도 있고 제품생산 시간을 기준으로 할 수도 있습니다.<a href="../stat/config_form.php">[바로가기]</a></p>
    <p>UPH 계산에 계획정지 시간은 포함될 수도 있고 제외될 수 있습니다. (제품생산 시간이 기준일 때는 포함, 설비가동시간 기준인 경우는 포함되지 않습니다.)</p>
</div>


<div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
    <caption>목록</caption>
    <thead>
    <tr>
        <th scope="col">날짜</th>
        <th scope="col">사양</th>
        <th scope="col" style="display:<?=($sfl=='item_lhrh'&&$stx)?'':'none'?>;">LH/RH</th>
        <th scope="col">생산수량</th>
        <th scope="col">시작 ~ 종료</th>
        <th scope="col">작업시간(분)</th>
        <th scope="col" style="display:<?=($g5['setting']['set_uph_worktime']=='output')?'':'none'?>;">계획정지(분)</th>
        <th scope="col">실작업(시)</th>
        <th scope="col">비가동(시)</th>
        <th scope="col">UPH(비가동포함)</th>
        <th scope="col">UPH(비가동제외)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // print_r2($row);
        // 시작시간(dta_start_his HHIISS), 종료시간(dta_end_his HHIISS), 시간형식은 숫자만(크기 비교를 위해서)
        $sql2 = "   SELECT work_date AS dta_date
                        , SUBSTRING(qrcode,7,1) AS item_type
                        , SUBSTRING(qrcode,8,2) AS item_lhrh
                        , min(end_time) AS dta_ymdhis_min
                        , max(end_time) AS dta_ymdhis_max
                        , REPLACE(SUBSTRING(min(end_time),11,9),':','') AS dta_start_his
                        , REPLACE(SUBSTRING(max(end_time),11,9),':','') AS dta_end_his
                    FROM g5_1_xray_inspection
                    WHERE work_date = '".$row['work_date']."'
                        {$sql_search2}
        ";
        // echo $sql2.'<br>';
        $row2 = sql_fetch($sql2,1);
        $row['period'] = $row2;
        $row['period']['dta_ymdhis_max_display'] = $row['period']['dta_ymdhis_max'];    // 목록에 종료시간 표시(24기간 경계 때문에 중간에 값이 바뀔 수 있어서 따로 정의함)
        // print_r2($row['period']);

        // 작업시간 합계... 생산품 기준이 아니고 장비 가동기준으로 바뀜에 따라 다시 계산합니다.
        if($g5['setting']['set_uph_worktime']=='output') {
            $row['worktime'] = strtotime($row['period']['dta_ymdhis_max']) - strtotime($row['period']['dta_ymdhis_min']);
            // echo $row['worktime'].' 초<br>';
        }
        else {
            // 가동설비들 추출 (설비별로 시작시간과 종료시간이 달라요.)
            $sql2 = "   SELECT GROUP_CONCAT(machine_id) AS machine_ids
                        FROM (
                            SELECT machine_id, COUNT(*) AS dta_count
                            FROM g5_1_xray_inspection
                            WHERE work_date = '".$row['work_date']."'
                            GROUP BY machine_id
                        ) AS db1
            ";
            $row2 = sql_fetch($sql2,1);
            $row['machine_array'] = explode(",",$row2['machine_ids']);
            // echo $row2['machine_ids'].'<br>';

            // 설비별로 가동시간 계산을 합해야 함
            for($j=0;$j<sizeof($row['machine_array']);$j++){
                // echo $row['machine_array'][$j].'<br>';
                // echo array_search($row['machine_array'][$j],$ser_mms_idx_array).'--- 배열의 key값<br>';

                // 가동시간 계산
                $sql2 = "   SELECT SUM(dta_value) AS dta_sum
                            FROM {$g5['data_run_table']}
                            WHERE dta_dt >= '".strtotime($row['period']['dta_ymdhis_min'])."' AND dta_dt <= '".strtotime($row['period']['dta_ymdhis_max'])."'
                                AND mms_idx = '".array_search($row['machine_array'][$j],$ser_mms_idx_array)."'
                ";
                // echo $sql2.'<br>';
                $row2 = sql_fetch($sql2,1);
                $row['runtime_sum'] += $row2['dta_sum'];
            }
            $row['worktime'] = $row['runtime_sum'];
            // echo $row['worktime'].' 초 ---- <br>';

        }




        // 종료시간이 시작보다 작은 경우는 다음날이므로 일단 24시까지 추출한 다음 한번 더 추출해야 함
        if( $row['period']['dta_start_his'] > $row['period']['dta_end_his'] ) {
            // echo 'big ++++++++++++++++++++++ <br>';
            $row['period']['dta_end_his2'] = $row['period']['dta_end_his']; // 한번더 추출을 위해서 저장해 두고
            $row['period']['dta_end_his'] = 235959; // 일단 마지막 시간으로 설정해서 1차 계산
            $row['period']['dta_ymdhis_max2'] = $row['period']['dta_ymdhis_max']; // 한번더 추출을 위해서 저장
            $row['period']['dta_ymdhis_max'] = $row['period']['dta_date'].' 23:59:59';
        }
        // print_r2($row['period']);
        // echo $row['period']['dta_start_his'].'~'.$row['period']['dta_end_his'].' 1차 기간<br>';
        // print_r2($offwork);


        // 공제시간 및 비가동시간 디폴트
        $row['offwork'][$i] = 0;
        $row['downtime'][$i] = 0;
        for($j=0;$j<sizeof($offwork);$j++){
            // print_r2($offwork[$j]);
            // echo $i.'-'.$j.'<br>';
            // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
            // 완전 포함인 경우는 무조건 공제시간에 포함됨
            if( $row['period']['dta_start_his'] <= $offwork[$j]['start'] && $row['period']['dta_end_his'] >= $offwork[$j]['end'] ) {
                $row['offworks'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 활용
                $row['offworks'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 활용
                $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
            }
            // 걸쳐 있는 경우
            else if( $row['period']['dta_start_his'] <= $offwork[$j]['end'] && $row['period']['dta_end_his'] >= $offwork[$j]['start'] ) {
                if( $row['period']['dta_start_his'] >= $offwork[$j]['start'] ) {
                    $row['offworks'][$i][$j]['start'] = $row['period']['dta_start_his'];  // 하단 비가동에서 활용
                    $row['offworks'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 활용
                    // $offwork[$j]['start'] = $row['period']['dta_start_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                    $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($row['period']['dta_start_his']);
                }
                if( $row['period']['dta_end_his'] <= $offwork[$j]['end'] ) {
                    $row['offworks'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 활용
                    $row['offworks'][$i][$j]['end'] = $row['period']['dta_end_his'];      // 하단 비가동에서 활용
                    // $offwork[$j]['end'] = $row['period']['dta_end_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                    $row['offwork'][$i] += strtotime($row['period']['dta_end_his']) - strtotime($offwork[$j]['start']);
                }
                // $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
            }
            // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 변경<br>';
            // echo $row['offwork'][$i].'<br>';
        }
        // echo $row['offwork'][$i].'<br>';
        // print_r2($row['offworks'][$i]);
        // echo '<br>--------------------------------------------------<br>';

        //  다음날인 경우는 한번 더
        if( $row['period']['dta_end_his2'] ) {
            $row['period']['dta_start_his2'] = '000000';
            // echo $row['period']['dta_start_his2'].'~'.$row['period']['dta_end_his2'].' 2차 기간<br>';
            // echo 'one more time ++++++++++++++++++++++ <br>';
            for($j=0;$j<sizeof($offwork);$j++){
                // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
                // 완전 포함인 경우는 무조건 공제시간에 포함됨
                if( $row['period']['dta_start_his2'] <= $offwork[$j]['start'] && $row['period']['dta_end_his2'] >= $offwork[$j]['end'] ) {
                    $row['offworks'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 활용
                    $row['offworks'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 활용
                    $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
                }
                // 걸쳐 있는 경우
                else if( $row['period']['dta_start_his2'] <= $offwork[$j]['end'] && $row['period']['dta_end_his2'] >= $offwork[$j]['start'] ) {
                    if( $row['period']['dta_start_his2'] >= $offwork[$j]['start'] ) {
                        $row['offworks'][$i][$j]['start'] = $row['period']['dta_start_his2'];  // 하단 비가동에서 활용
                        $row['offworks'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 활용
                        // $offwork[$j]['start'] = $row['period']['dta_start_his2'];   // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($row['period']['dta_start_his2']);
                    }
                    if( $row['period']['dta_end_his2'] <= $offwork[$j]['end'] ) {
                        $row['offworks'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 활용
                        $row['offworks'][$i][$j]['end'] = $row['period']['dta_end_his2'];      // 하단 비가동에서 활용
                        // $offwork[$j]['end'] = $row['period']['dta_end_his2'];   // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork'][$i] += strtotime($row['period']['dta_end_his2']) - strtotime($offwork[$j]['start']);
                    }
                    // $row['offwork'][$i] += strtotime($offwork[$j]['end']) - strtotime($offwork[$j]['start']);
                }
                // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 변경<br>';
                // print_r2($row['offworks'][$i]);
                // echo $row['offwork'][$i].'<br>';
            }
            // echo $row['offwork'][$i].'<br>';
            // echo '<br>--------------------------------------------------<br>';
    
        }
        // print_r2($row['offworks'][$i]); // 공제시간 전체 배열
        // echo '<br>=====================================================================<br>';
        $row['offworkmin'] = round($row['offwork'][$i]/60);         // 공제시간(분)

        $row['workmin'] = round($row['worktime']/60);           // 작업시간(분)
        $row['workreal'] = ($g5['setting']['set_uph_worktime']=='output') ?
                                $row['worktime']-$row['offwork'][$i]    // 실작업시간 = 작업시간 - 계획정지(공제)
                                :$row['worktime'];  // 가동시간만
        // $row['workreal'] = $row['worktime']-$row['offwork'][$i];
        $row['workrealmin'] = round($row['workreal']/60);       // 실작업시간(분)
        $row['workhour'] = round($row['workreal']/3600,2);      // 작업시간(시)






        // 비가동(downtime) 추출 1차 (24시 전) -----------------------
        // echo $row['period']['dta_start_his'].' / '.$row['period']['dta_end_his'].' 1차<br>';
        // echo $row['period']['dta_start_his2'].' / '.$row['period']['dta_end_his2'].' 2차<br>';
        $sql_downtime_mms_idxs = $ser_mms_idx ? " AND mms_idx = '".$ser_mms_idx."'" : "";

        $row['downtime_start'] = strtotime($row['period']['dta_ymdhis_min']);
        $row['downtime_end'] = strtotime($row['period']['dta_ymdhis_max']);
        // echo  $row['downtime_start'].' ('.$row['period']['dta_ymdhis_min'].') 1차 시작시점<br>';
        // echo  $row['downtime_end'].' ('.$row['period']['dta_ymdhis_max'].') 1차 종료시점<br>';
        $sql2 = "SELECT dta_idx, mms_idx
                , dta_start_dt AS db_dta_start_time
                , dta_end_dt AS db_dta_end_time
                , FROM_UNIXTIME(dta_start_dt,'%Y-%m-%d %H:%i:%s') AS db_off_start_ymdhis
                , FROM_UNIXTIME(dta_end_dt,'%Y-%m-%d %H:%i:%s') AS db_off_end_ymdhis
                , GREATEST('".$row['downtime_start']."', dta_start_dt ) AS dta_start_dt
                , LEAST('".$row['downtime_end']."', dta_end_dt ) AS dta_end_dt
                , FROM_UNIXTIME( GREATEST('".$row['downtime_start']."', dta_start_dt ) ,'%Y-%m-%d %H:%i:%s') AS dta_start_ymdhis
                , FROM_UNIXTIME( LEAST('".$row['downtime_end']."', dta_end_dt ) ,'%Y-%m-%d %H:%i:%s') AS dta_end_ymdhis
                , FROM_UNIXTIME( GREATEST('".$row['downtime_start']."', dta_start_dt ) ,'%H%i%s') AS dta_start_his
                , FROM_UNIXTIME( LEAST('".$row['downtime_end']."', dta_end_dt ) ,'%H%i%s') AS dta_end_his
                FROM {$g5['data_downtime_table']}
                WHERE dta_end_dt >= '".$row['downtime_start']."'
                    AND dta_start_dt <= '".$row['downtime_end']."'
                    {$sql_downtime_mms_idxs}
                ORDER BY dta_start_dt
        ";
        // echo $sql2.'<br>';
        $rs2 = sql_query($sql2,1);
        for ($j=0; $row2=sql_fetch_array($rs2); $j++) {
            // print_r2($row2);

            // 비가동 시간(초), 일단 추출해 놓고 공제시간 돌면서 해당 사항 있으면 공제
            $row['downtime1'][$i][$j] = $row2['dta_end_dt'] - $row2['dta_start_dt'];
            // echo $row['downtime1'][$i][$j].' downtime original<br>';

            // 공제시간 배열 전체를 돌면서 중복을 제거해야 함
            if(is_array($row['offworks'][$i])) {
                foreach($row['offworks'][$i] as $k1=>$v1) {
                    // print_r2($v1);
                    // echo $v1['start'].'~'.$v1['end'].' 원본<br>';

                    // 완전 포함인 경우는 무조건 중복이므로 제외해야 함
                    if( $row2['dta_start_his'] <= $row['offworks'][$i][$k1]['start'] && $row2['dta_end_his'] >= $row['offworks'][$i][$k1]['end'] ) {
                        // echo '3<br>';
                        $row['downtime_tmp'][$i][$j] = strtotime($row['offworks'][$i][$k1]['end']) - strtotime($row['offworks'][$i][$k1]['start']);
                        $row['downtime1'][$i][$j] = $row['downtime1'][$i][$j] - $row['downtime_tmp'][$i][$j];
                    }
                    // 걸쳐 있는 경우는 중복 부분만 중복으로 제거해야 함
                    else if( $row2['dta_start_his'] <= $row['offworks'][$i][$k1]['end'] && $row2['dta_end_his'] >= $row['offworks'][$i][$k1]['start'] ) {
                        // echo '4<br>';
                        if( $row2['dta_start_his'] >= $row['offworks'][$i][$k1]['start'] ) {
                            $row['offworks'][$i][$k1]['start'] = $row2['dta_start_his'];  // 공제 시작 시점 변경
                        }
                        if( $row2['dta_end_his'] <= $row['offworks'][$i][$k1]['end'] ) {
                            $row['offworks'][$i][$k1]['end'] = $row2['dta_end_his'];  // 공제 끝점 변경
                        }
                        $row['downtime_tmp'][$i][$j] = strtotime($row['offworks'][$i][$k1]['end']) - strtotime($row['offworks'][$i][$k1]['start']);
                        $row['downtime1'][$i][$j] = $row['downtime1'][$i][$j] - $row['downtime_tmp'][$i][$j];
                    }
                    // echo $row['downtime1'][$i][$j].' <== downtime changed.<br>';
                    // print_r2($row['downtime1'][$i]);
                    // echo $row['downtime1'][$i].'<br>';
                    
                }
            }
            // echo '<br>---------------------------<br>';

        }
        // echo '<br>==================================================================<br>';
        // print_r2($row['downtime1'][$i]);
        if(is_array($row['downtime1'][$i])) {
            for ($j=0; $j<sizeof($row['downtime1'][$i]); $j++) {
                // echo $row['downtime1'][$i][$j].'<br>';
                $row['downtime'][$i] += $row['downtime1'][$i][$j];
            }
        }
        // echo $row['downtime'][$i].'<br>';
            

        // 비가동(downtime) 추출 2차 (24시 이후)
        if( $row['period']['dta_end_his2'] ) {

            $row['downtime_start'] = strtotime(substr($row['period']['dta_ymdhis_max2'],0,10).' 00:00:00');
            $row['downtime_end'] = strtotime($row['period']['dta_ymdhis_max2']);
            // echo  $row['downtime_start'].' ('.substr($row['period']['dta_ymdhis_max2'],0,10).' 00:00:00'.') 2차 시작시점<br>';
            // echo  $row['downtime_end'].' ('.$row['period']['dta_ymdhis_max2'].') 2차 종료시점<br>';
            $sql2 = "SELECT dta_idx, mms_idx
                    , dta_start_dt AS db_dta_start_time
                    , dta_end_dt AS db_dta_end_time
                    , FROM_UNIXTIME(dta_start_dt,'%Y-%m-%d %H:%i:%s') AS db_off_start_ymdhis
                    , FROM_UNIXTIME(dta_end_dt,'%Y-%m-%d %H:%i:%s') AS db_off_end_ymdhis
                    , GREATEST('".$row['downtime_start']."', dta_start_dt ) AS dta_start_dt
                    , LEAST('".$row['downtime_end']."', dta_end_dt ) AS dta_end_dt
                    , FROM_UNIXTIME( GREATEST('".$row['downtime_start']."', dta_start_dt ) ,'%Y-%m-%d %H:%i:%s') AS dta_start_ymdhis
                    , FROM_UNIXTIME( LEAST('".$row['downtime_end']."', dta_end_dt ) ,'%Y-%m-%d %H:%i:%s') AS dta_end_ymdhis
                    , FROM_UNIXTIME( GREATEST('".$row['downtime_start']."', dta_start_dt ) ,'%H%i%s') AS dta_start_his
                    , FROM_UNIXTIME( LEAST('".$row['downtime_end']."', dta_end_dt ) ,'%H%i%s') AS dta_end_his
                    FROM {$g5['data_downtime_table']}
                    WHERE dta_end_dt >= '".$row['downtime_start']."'
                        AND dta_start_dt <= '".$row['downtime_end']."'
                        {$sql_downtime_mms_idxs}
                    ORDER BY dta_start_dt
            ";
            // echo $sql2.'<br>';
            $rs2 = sql_query($sql2,1);
            for ($j=0; $row2=sql_fetch_array($rs2); $j++) {
                // print_r2($row2);
    
                // 비가동 시간(초), 일단 추출해 놓고 공제시간 돌면서 해당 사항 있으면 공제
                $row['downtime2'][$i][$j] = $row2['dta_end_dt'] - $row2['dta_start_dt'];
                // echo $row['downtime2'][$i][$j].' downtime original<br>';
    
                // 공제시간 배열 전체를 돌면서 중복을 제거해야 함
                if(is_array($row['offworks'][$i])) {
                    foreach($row['offworks'][$i] as $k1=>$v1) {
                        // print_r2($v1);
                        // echo $v1['start'].'~'.$v1['end'].' 원본<br>';
    
                        // 완전 포함인 경우는 무조건 중복이므로 제외해야 함
                        if( $row2['dta_start_his'] <= $row['offworks'][$i][$k1]['start'] && $row2['dta_end_his'] >= $row['offworks'][$i][$k1]['end'] ) {
                            // echo '3<br>';
                            $row['downtime_tmp'][$i][$j] = strtotime($row['offworks'][$i][$k1]['end']) - strtotime($row['offworks'][$i][$k1]['start']);
                            $row['downtime2'][$i][$j] = $row['downtime2'][$i][$j] - $row['downtime_tmp'][$i][$j];
                        }
                        // 걸쳐 있는 경우는 중복 부분만 중복으로 제거해야 함
                        else if( $row2['dta_start_his'] <= $row['offworks'][$i][$k1]['end'] && $row2['dta_end_his'] >= $row['offworks'][$i][$k1]['start'] ) {
                            // echo '4<br>';
                            if( $row2['dta_start_his'] >= $row['offworks'][$i][$k1]['start'] ) {
                                $row['offworks'][$i][$k1]['start'] = $row2['dta_start_his'];  // 공제 시작 시점 변경
                            }
                            if( $row2['dta_end_his'] <= $row['offworks'][$i][$k1]['end'] ) {
                                $row['offworks'][$i][$k1]['end'] = $row2['dta_end_his'];  // 공제 끝점 변경
                            }
                            $row['downtime_tmp'][$i][$j] = strtotime($row['offworks'][$i][$k1]['end']) - strtotime($row['offworks'][$i][$k1]['start']);
                            $row['downtime2'][$i][$j] = $row['downtime2'][$i][$j] - $row['downtime_tmp'][$i][$j];
                        }
                        // echo $row['downtime2'][$i][$j].' <== downtime changed.<br>';
                        // print_r2($row['downtime2'][$i]);
                        // echo $row['downtime2'][$i].'<br>';
                        
                    }
                }
                // echo '<br>---------------------------<br>';
    
            }
            // echo '<br>==================================================================<br>';
            // print_r2($row['downtime2'][$i]);
            if(is_array($row['downtime2'][$i])) {
                for ($j=0; $j<sizeof($row['downtime2'][$i]); $j++) {
                    // echo $row['downtime2'][$i][$j].'<br>';
                    $row['downtime'][$i] += $row['downtime2'][$i][$j];
                }
            }
            // echo $row['downtime'][$i].'<br>';


        }



        $row['downtimemin'] = round($row['downtime'][$i]/60,1);    // 비가동시간(분)
        $row['downtimehour'] = round($row['downtime'][$i]/3600,2); // 비가동시간(시)

        // 링크
        $row['ahref'] = '<a href="?'.$qstr.'&sfl=item_lhrh&stx='.$row['item_lhrh'].'">';
        
        // 사양명
        $row['item_type'] = $ser_item_type ? $row['item_type'] : '전체';
        
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?> tr_<?=$row['dmn_status']?>">
        <td><?=$row['work_date']?></td><!-- 날짜 -->
        <td><?=$row['ahref'].$row['item_type']?></a></td><!-- 사양 -->
        <td style="display:<?=($sfl=='item_lhrh'&&$stx)?'':'none'?>;"><!-- LH/RH -->
            <?=$row['ahref'].$row['item_lhrh']?></a>
        </td>
        <td class="td_right pr_10"><?=number_format($row['output_sum'])?></td><!-- 생산수량(타) -->
        <td class="td_start_end"><?=substr($row['period']['dta_ymdhis_min'],5)?> ~ <?=substr($row['period']['dta_ymdhis_max_display'],5)?></td><!-- 시작~종료 -->
        <td><?=$row['workmin']?></td><!-- 작업시간(분) -->
        <td style="display:<?=($g5['setting']['set_uph_worktime']=='output')?'':'none'?>;"><?=$row['offworkmin']?></td><!-- 계획정지(분) -->
        <td><?=$row['workrealmin']?> (<?=$row['workhour']?>)</td><!-- 실작업시간(시) -->
        <td><?=$row['downtimemin']?> (<?=$row['downtimehour']?>)</td><!-- 비가동시간(시) -->
        <td><?=round($row['output_sum']/$row['workhour'],2)?></td><!-- SPH(비가동포함) -->
        <td><?=round($row['output_sum']/($row['workhour']-$row['downtimehour']),2)?></td><!-- SPH(비가동제외) -->
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
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

    // 마우스 hover 설정
    $(".tbl_head01 tbody tr").on({
        mouseenter: function () {
            $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','#e6e6e6 ');
        },
        mouseleave: function () {
            $('tr[tr_id='+$(this).attr('tr_id')+']').find('td').css('background','unset');
        }    
    });

});
</script>


<?php
include_once ('./_tail.php');
?>
