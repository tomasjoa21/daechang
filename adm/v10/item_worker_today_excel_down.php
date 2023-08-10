<?php
$sub_menu = "922120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

function column_char($i) { return chr( 65 + $i ); }

// st_date, en_date
$st_date = $st_date ?: date("Y-m-d",G5_SERVER_TIME);
$st_time = $st_time ?: '00:00:00';
$en_time = $en_time ?: '23:59:59';
$st_datetime = $st_date.' '.$st_time;
$en_datetime = $st_date.' '.$en_time;

// 검색일자
$stat_date = $st_date ?: statics_date(G5_TIME_YMDHIS);
// echo $stat_date;

// 계획정지 offwork, 중복 제거가 있어서 불러오는 순서가 중요함
$ser_mms_idxs = $ser_mms_idx ? $ser_mms_idx.',0' : '0';
$sql = "SELECT off_idx, mms_idx, off_period_type
        , off_start_time
        , off_end_time
        FROM {$g5['offwork_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
            AND off_status IN ('ok')
            AND off_start_dt <= '".$st_datetime."'
            AND off_end_dt >= '".$en_datetime."'
            AND mms_idx IN (".$ser_mms_idxs.")
        ORDER BY mms_idx, off_period_type, off_start_time
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++){
    // print_r2($row);
    $offwork[$i]['mms_idx'] = $row['mms_idx'];
    $offwork[$i]['start'] = preg_replace("/:/","",$row['off_start_time']);
    $offwork[$i]['end'] = preg_replace("/:/","",$row['off_end_time']);
    // print_r2($offwork[$i]);
    // echo $i.'번째  <br>';
    // 중복 제거 처리 (앞에서 정의했던 것들과 겹치는 시간이 있으면 빼야 함, 중복 계산하지 않도록 한다.)
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
}
// print_r2($offwork);


// 설비별 비가동 downtime, 중복 제거가 있어서 불러오는 순서가 중요함
$day_arr = shift_period($st_date);
// print_r2($day_arr);
$sql = "SELECT dta_idx, mms_idx
        , dta_start_dt
        , dta_end_dt
        FROM {$g5['data_downtime_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
            AND dta_start_dt <= '".$day_arr['end_dt']."' AND dta_end_dt >= '".$day_arr['start_dt']."'
        ORDER BY mms_idx, dta_start_dt
";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++){
    // print_r2($row);
    $downtime[$i]['mms_idx'] = $row['mms_idx'];
    $downtime[$i]['start'] = preg_replace("/:/","",substr($row['dta_start_dt'],11));
    $downtime[$i]['end'] = preg_replace("/:/","",substr($row['dta_end_dt'],11));
    // print_r2($downtime[$i]);
}
// print_r2($downtime);


$sql_common = " FROM {$g5['production_item_table']} AS pri
                LEFT JOIN {$g5['production_table']} AS prd USING(prd_idx)
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = pri.bom_idx
";

$where = array();
//$where[] = " (1) ";   // 디폴트 검색조건
$where[] = " prd_start_date = '".$stat_date."' ";    // 오늘 것만

// 해당 업체만
$where[] = " pri.com_idx = '".$_SESSION['ss_com_idx']."' ";

if ($stx && $sfl) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'mms_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(pic_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}

// 고객사
if ($ser_cst_idx_customer) {
    $where[] = " mtr.cst_idx_customer = '".$ser_cst_idx_customer."' ";
    $cst_customer = get_table('customer','cst_idx',$ser_cst_idx_customer);
}
// 공급사
if ($ser_cst_idx_provider) {
    $where[] = " mtr.cst_idx_provider = '".$ser_cst_idx_provider."' ";
    $cst_provider = get_table('customer','cst_idx',$ser_cst_idx_provider);
}

// 작업자
if ($ser_mb_id) {
    $where[] = " pri.mb_id = '".$ser_mb_id."' ";
    $mb1 = get_table('member','mb_id',$ser_mb_id,'mb_name');
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
	$sst = "pri_idx";
    //$sst = "pri_sort, ".$pre."_reg_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";


$sql = " SELECT pri_idx, pri.bom_idx, mms_idx, mb_id, pri_value, prd_start_date, bom.*
		{$sql_common}
		{$sql_search}
        {$sql_order}
";
// echo $sql.BR;
$result = sql_query($sql,1);

// 전체 게시물 수
$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
if (!$total_count)
    alert("출력할 내역이 없습니다.");


// 각 항목 설정
$headers = array('품번','품명','구분','차종','작업자','설비','생산시간범위','생산시간(분)','비가동시간(분)','UPH','목표','생산수량','달성율');
$widths  = array(20   ,40   ,10   , 10  ,20    ,10   ,15        ,10      ,10    ,10    ,10  ,10      ,10);
$header_bgcolor = 'FFABCDEF';
$last_char = column_char(count($headers) - 1);

// 엑셀 데이타 출력
include_once(G5_LIB_PATH.'/PHPExcel.php');

// 두번째 줄부터 실제 데이터 입력
for($i=1; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);
        $row['cst_customer'] = get_table('customer','cst_idx',$row['cst_idx_customer'],'cst_name');
        $row['bct'] = get_table('bom_category','bct_idx',$row['bct_idx'],'bct_name');
        $row['mb1'] = get_table('member','mb_id',$row['mb_id'],'mb_name');
        // print_r2($row['cst_customer']);

        // 현재 생산수량 합계
        $sql1 = " SELECT SUM(pic_value) AS pic_sum FROM {$g5['production_item_count_table']} 
                    WHERE pri_idx = '".$row['pri_idx']."' AND pic_date = '".$stat_date."'
        ";
        // echo $sql1.BR;
        $row['pic'] = sql_fetch($sql1,1);

        // 생산 시작 및 종료시간 ----------------------------------------------------------
        $sql1 = "   SELECT MIN(pic_reg_dt) AS pic_min_dt, MAX(pic_reg_dt) AS pic_max_dt
                    FROM {$g5['production_item_count_table']} 
                    WHERE pri_idx = '".$row['pri_idx']."' AND pic_date = '".$stat_date."'
        ";
        // echo $sql1.BR;
        $row['dt'] = sql_fetch($sql1,1);
        // print_r2($row['dt']);
        $row['pri_hours'] = $row['dt']['pic_min_dt'] ? substr($row['dt']['pic_min_dt'],11,-3) : '';
        $row['pri_hours'] .= $row['dt']['pic_max_dt'] ? '~'.substr($row['dt']['pic_max_dt'],11,-3) : '';
        // 생산 시작 및 종료시간이 존재할 때 ----------------------------------------------------------
        if($row['dt']['pic_min_dt'] && $row['dt']['pic_max_dt']) {
            // print_r2($row['dt']);
            $row['pri_work_seconds'] = strtotime($row['dt']['pic_max_dt']) - strtotime($row['dt']['pic_min_dt']);
            $row['pri_work_min'] = $row['pri_work_seconds']/60;
            $row['pri_work_min_text'] = $row['pri_work_min'] ? '<br>('.number_format($row['pri_work_min'],2).'분)' : '';
            // echo $row['pri_work_seconds'].BR;
            $row['pri_work_hour'] = $row['pri_work_seconds']/3600;  // 1. 1차 작업시간 계산 //<-----------
            // echo $row['pri_work_hour'].BR;

            // 실제 적용시간 범위
            $row['dta_start_his'] = preg_replace("/:/","",substr($row['dt']['pic_min_dt'],11));
            $row['dta_end_his'] = preg_replace("/:/","",substr($row['dt']['pic_max_dt'],11));
            // if($row['mms_idx']==139) {
            //     echo $i.BR;
            //     echo $row['dta_start_his'].'~'.$row['dta_end_his'].' 적용시간범위<br>';
            // }

// // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//     print_r2($row['dt']);
// }

            // 계획정지 (일단은 설비 상관없이 전체 적용), 위에서 만들어둔 배열 활용
            for($j=0;$j<@sizeof($offwork);$j++){
                // print_r2($offwork[$j]);
                // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
// // // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//                 echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
//                 echo num2seconds($offwork[$j]['end']).'~'.num2seconds($offwork[$j]['start']).' times<br>';
// }
                // 같은 값도 있네요. (통과)
                if( $row['dta_start_his'] == $row['dta_end_his']) {
                     continue;
                }
                // 완전 벗어난 경우는 무조건 건너뜀
                else if( $row['dta_start_his'] >= $offwork[$j]['start'] && $row['dta_end_his'] <= $offwork[$j]['end'] ) {
                    continue;
                }
                // 완전 포함인 경우는 무조건 공제시간
                else if( $row['dta_start_his'] <= $offwork[$j]['start'] && $row['dta_end_his'] >= $offwork[$j]['end'] ) {
                    $row['offwork_arr'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 재활용
                    $row['offwork_arr'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 재활용
                    $row['offwork_sec'][$i] += num2seconds($offwork[$j]['end']) - num2seconds($offwork[$j]['start']);
                }
                // 걸쳐 있는 경우
                else if( $row['dta_start_his'] <= $offwork[$j]['end'] && $row['dta_end_his'] >= $offwork[$j]['start'] ) {
                    // echo $j.BR;
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//                     echo $row['dta_start_his'] .'<='. $offwork[$j]['end'] .'&&'. $row['dta_end_his'] .'>='. $offwork[$j]['start'].BR;
// }
                    if( $row['dta_start_his'] >= $offwork[$j]['start'] ) {
                        $row['offwork_arr'][$i][$j]['start'] = $row['dta_start_his'];  // 하단 비가동에서 재활용
                        $row['offwork_arr'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 재활용
                        // $offwork[$j]['start'] = $row['dta_start_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork_sec'][$i] += num2seconds($offwork[$j]['end']) - num2seconds($row['dta_start_his']);
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//                         echo $row['offwork_sec'][$i].BR;
// }
                    }
                    if( $row['dta_end_his'] <= $offwork[$j]['end'] ) {
                        $row['offwork_arr'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 재활용
                        $row['offwork_arr'][$i][$j]['end'] = $row['dta_end_his'];      // 하단 비가동에서 재활용
                        // $offwork[$j]['end'] = $row['dta_end_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork_sec'][$i] += num2seconds($row['dta_end_his']) - num2seconds($offwork[$j]['start']);
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//                         echo $row['offwork_sec'][$i].BR;
// }
                    }
                }
            }
// if($row['bom_idx'] == 240 && $row['mms_idx'] == 144 && $row['mb_id'] == '01021634581') {
//             echo '계획정지 공제시간 합(sec): '.$row['offwork_sec'][$i].'<br>';
// }
            // echo '계획정지 arr['.$i.']: '.BR.print_r2($row['offwork_arr'][$i]); // 최종 적용된 계획정지 배열 (하단에서 중복 제거용)
            $row['offwork_hour'][$i] = $row['offwork_sec'][$i] ? $row['offwork_sec'][$i]/3600 : 0;  // convert to hour unit.
            $row['pri_work_hour'] -= $row['offwork_hour'][$i];  // 2. 2차 작업시간 계산: 계획정지 시간 제외해 줌 //<-----------
        


            // 비가동정지 (downtime), 위에서 만들어둔 배열 활용
            for($j=0;$j<@sizeof($downtime);$j++){
                // print_r2($downtime[$j]);
                // echo $downtime[$j]['start'].'~'.$downtime[$j]['end'].' 원본<br>';

                // 해당 설비인 경우만 적용함
                if($downtime[$j]['mms_idx']==$row['mms_idx']) {
                    // echo $downtime[$j]['mms_idx'].'/'.$row['mms_idx'].BR;
                    // print_r2($downtime[$j]);

                    // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
                    if($row['bom_idx'] == 261 && $row['mms_idx'] == 140 && $row['mb_id'] == '01056058011') {
                        // 결국에는 아래 두개 배열을 비교해서 제거하는 거네요.
                        // echo $row['dta_start_his'].'~'.$row['dta_end_his'].' 적용시간범위<br>';
                        // print_r2($row['offwork_arr']);
                        // print_r2($downtime[$j]);
                        // echo $downtime[$j]['start'].'~'.$downtime[$j]['end'].' 원본<br>';

                    
                    // 같은 값도 있네요. (통과)
                    if( $row['dta_start_his'] == $row['dta_end_his']) {
                        continue;
                    }
                    // 완전 벗어난 경우는 무조건 건너뜀
                    else if( $row['dta_start_his'] >= $downtime[$j]['start'] && $row['dta_end_his'] <= $downtime[$j]['end'] ) {
                        continue;
                    }
                    // 완전 포함인 경우는 무조건 공제
                    else if( $row['dta_start_his'] <= $downtime[$j]['start'] && $row['dta_end_his'] >= $downtime[$j]['end'] ) {
                        $row['downtime_arr'][$i][$j]['start'] = $downtime[$j]['start'];  // 하단 중복처리에서 재활용
                        $row['downtime_arr'][$i][$j]['end'] = $downtime[$j]['end'];      // 하단 중복처리에서 재활용
                        $row['downtime_sec'][$i] += num2seconds($downtime[$j]['end']) - num2seconds($downtime[$j]['start']);
                        // echo $downtime[$j]['end'].' - '.$downtime[$j]['start'].' --- 1 완전포함'.BR;
                    }
                    // 걸쳐 있는 경우
                    else if( $row['dta_start_his'] <= $downtime[$j]['end'] && $row['dta_end_his'] >= $downtime[$j]['start'] ) {
                        // echo $j.BR;
                        // echo $row['dta_start_his'] .'<='. $downtime[$j]['end'] .'&&'. $row['dta_end_his'] .'>='. $downtime[$j]['start'].BR;
                        if( $row['dta_start_his'] >= $downtime[$j]['start'] ) {
                            $row['downtime_arr'][$i][$j]['start'] = $row['dta_start_his'];  // 하단 중복처리에서 재활용
                            $row['downtime_arr'][$i][$j]['end'] = $downtime[$j]['end'];      // 하단 중복처리에서 재활용
                            $row['downtime_sec'][$i] += num2seconds($downtime[$j]['end']) - num2seconds($row['dta_start_his']);
                            // echo $downtime[$j]['end'].' - '.$row['dta_start_his'].' --- 2 앞쪽'.BR;
                        }
                        if( $row['dta_end_his'] <= $downtime[$j]['end'] ) {
                            $row['downtime_arr'][$i][$j]['start'] = $downtime[$j]['start'];  // 하단 중복처리에서 재활용
                            $row['downtime_arr'][$i][$j]['end'] = $row['dta_end_his'];      // 하단 중복처리에서 재활용
                            $row['downtime_sec'][$i] += num2seconds($row['dta_end_his']) - num2seconds($downtime[$j]['start']);
                            // echo $row['dta_end_his'].' - '.$downtime[$j]['start'].' --- 3 뒤쪽'.BR;
                        }
                    }
                    // echo $row['downtime_sec'][$i].' 초'.BR;
                    // print_r2($row['downtime_arr'][$i]);  // 최종 적용한 downtime 배열


                    } // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
                }
            }
            $row['downtime_hour'][$i] = $row['downtime_sec'][$i] ? $row['downtime_sec'][$i]/3600 : 0;  // convert to hour unit.
            $row['pri_work_hour'] -= $row['downtime_hour'][$i];  // 3. 3차 작업시간 계산: 비가동 시간 제외해 줌 //<-----------


            // 비가동과 계획정비가 중복되는 시간은 다시 공제에서 제외 (두번 제외된 구간 복구)
            if($row['downtime_sec'][$i]) {
                // print_r2($row['offwork_arr'][$i]);
                // print_r2($row['downtime_arr'][$i]);  // 최종 적용한 downtime 배열
                // 계획정비 배열 전체를 돌면서 중복 부분 제거 (비가동을 돌면서 계획정비를 처리해도 마찬가지!)
                if(is_array($row['offwork_arr'][$i])) {
                    foreach($row['offwork_arr'][$i] as $k1=>$v1) {
                        // print_r2($v1);
                        // echo $v1['start'].'~'.$v1['end'].' 계획정지 구간<br>';

                        // 각 구간마다 중복되는 부분 추출
                        if(is_array($row['downtime_arr'][$i])) {
                            foreach($row['downtime_arr'][$i] as $k2=>$v2) {
                                // print_r2($v2);
                                // echo '---> '.$v2['start'].'~'.$v2['end'].' 비가동 구간<br>';

                                // 비가동이 계획정비에 완전 포함되는 경우는 중복된 시간이므로 추출
                                if( $v2['start'] >= $v1['start'] && $v2['end'] <= $v1['end'] ) {
                                    $row['duplicated_sec'][$i] += num2seconds($v2['end'])- num2seconds($v2['start']);
                                    // echo $v2['end'].' - '.$v2['start'].' --- 2 완전포함'.BR;
                                }
                                // 걸쳐 있는 경우
                                else if( $v2['end'] >= $v1['start'] && $v2['start'] <= $v1['end'] ) {
                                    // echo $v2['start'].'~'.$v2['end'].' 2 적용시간범위<br>';
                                    // print_r2($v1);
                                    // 앞쪽 구간에 걸친 경우
                                    if( $v2['end'] >= $v1['start'] ) {
                                        // print_r2($v1);
                                        $row['duplicated_sec'][$i] += num2seconds($v2['end'])- num2seconds($v1['start']);
                                        // echo $v2['end'].' - '.$v1['start'].' --- 2 앞쪽구간'.BR;
                                    }
                                    // 뒤쪽 구간에 걸친 경우
                                    else if( $v2['start'] <= $v1['end'] ) {
                                        // print_r2($v1);
                                        $row['duplicated_sec'][$i] += num2seconds($v1['end']) - num2seconds($v2['start']);
                                        // echo $v1['end'].' - '.$v2['start'].' --- 3 뒤쪽구간'.BR;
                                    }
                                }
                            }
                        }
                        // echo '============='.BR;
                        
                    }
                }
                // echo $row['duplicated_sec'][$i].' 중복더블차감된 부분이므로 다시 복구해 줘야 하는 초'.BR;
            }
            $row['duplicated_hour'][$i] = $row['duplicated_sec'][$i] ? $row['duplicated_sec'][$i]/3600 : 0;  // convert to hour unit.
            $row['pri_work_hour'] += $row['duplicated_hour'][$i];  // 4. 4차 작업시간 계산: 계획정비와 비가동에서 중복제외된 시간 복구해 줌 //<-----------


            // 비가동전체 = 계획정지 + 비가동 - 중복적용시간
            $row['offdown_seconds'] = $row['offwork_sec'][$i] + $row['downtime_sec'][$i] - $row['duplicated_sec'][$i];
            $row['offdown_min'] = $row['offdown_seconds'] ? $row['offdown_seconds']/60 : 0;
            $row['offdown_text'] = $row['offdown_min'] ? number_format($row['offdown_min'],1).'분' : '';



            // UPH 계산
            $row['pri_uph'] = ($row['pic']['pic_sum']&&$row['pri_work_hour']) ? number_format($row['pic']['pic_sum']/$row['pri_work_hour'],1) : 0;
            // echo $row['pri_uph'].BR;
            $row['pri_uph_text'] = $row['pic']['pic_sum'] ? '<span title="'.$row['pri_work_seconds'].'(s)='.number_format($row['pri_work_hour'],2).'(h)">'.$row['pri_uph'].'</span>' : 0;
            $pri_uph_arr[] = $row['pri_uph'];
            $pri_uph_total += $row['pri_uph'];

        }
        //// 생산 시작 및 종료시간이 존재할 때 ----------------------------------------------------------

        // 비율
        $row['rate'] = ($row['pri_value']) ? $row['pic']['pic_sum'] / $row['pri_value'] * 100 : 0 ;
        $row['rate_color'] = '#d1c594';
        $row['rate_color'] = ($row['rate']>=80) ? '#72ddf5' : $row['rate_color'];
        $row['rate_color'] = ($row['rate']>=100) ? '#ff9f64' : $row['rate_color'];

        // 그래프
        if($row['pri_value'] && $row['pic']['pic_sum']) {
            // $row['rate_percent'] = $row['pic']['pic_sum'] / max($item_max) * 100;
            $row['rate_percent'] = $row['pic']['pic_sum'] / $row['pri_value'] * 100;
            $row['graph'] = '<img class="graph_output" src="../img/dot.gif" style="width:'.(($row['rate_percent']>100)?100:$row['rate_percent']).'%;background:'.$row['rate_color'].';" height="8px">';
        }
        

    // $row['com'] = sql_fetch(" SELECT com_name FROM {$g5['company_table']} WHERE com_idx = '".$row['com_idx']."' ");
    // $row['mms'] = sql_fetch(" SELECT mms_name FROM {$g5['mms_table']} WHERE mms_idx = '".$row['mms_idx']."' ");
    // $row['cod'] = sql_fetch(" SELECT cod_name, trm_idx_category FROM {$g5['code_table']} WHERE cod_idx = '".$row['cod_idx']."' ");
    // // print_r2($row);

    $rows[] = array($row['bom_part_no']
                  , $row['bom_name']
                  , $g5['set_bom_type_value'][$row['bom_type']]
                  , $row['bct']['bct_name']
                  , $row['mb1']['mb_name']
                  , $g5['mms'][$row['mms_idx']]['mms_name']
                  , $row['pri_hours']
                  , $row['pri_work_min']
                  , $row['offdown_min']
                  , $row['pri_uph']
                  , $row['pri_value']
                  , (int)$row['pic']['pic_sum']
                  , number_format($row['rate_percent'],1)
              );
}
// print_r2($rows);
// exit;


$data = array_merge(array($headers), $rows);

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0)->getStyle( "A1:${last_char}1" )->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle( "A:$last_char" )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
foreach($widths as $i => $w) $excel->setActiveSheetIndex(0)->getColumnDimension( column_char($i) )->setWidth($w);
$excel->getActiveSheet()->fromArray($data,NULL,'A1');

header("Content-Type: application/octet-stream");
// header("Content-Disposition: attachment; filename=\"today-".date("ymdHi", time()).".xls\"");
header("Content-Disposition: attachment; filename=\"today-".$st_date.".xls\"");
header("Cache-Control: max-age=0");

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');

?>