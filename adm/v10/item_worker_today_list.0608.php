<?php
$sub_menu = "922120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '작업자별현황';
@include_once('./_top_menu_item_status.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

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

$rows = $g5['setting']['set_'.$g5['file_name'].'_page_rows'] ? $g5['setting']['set_'.$g5['file_name'].'_page_rows'] : $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT pri_idx, pri.bom_idx, mms_idx, mb_id, pri_value, prd_start_date, bom.*
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$from_record}, {$rows}
";
// echo $sql.BR;
$result = sql_query($sql,1);

// 전체 게시물 수
$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산


// 작업자 select list 추출
$sql1 = " SELECT mb_id {$sql_common} 
            WHERE prd_start_date = '".$stat_date."' AND pri.com_idx = '".$_SESSION['ss_com_idx']."'
            GROUP BY mb_id
";
// echo $sql1.BR;
$rs = sql_query($sql1,1);
for ($i=0; $row=sql_fetch_array($rs); $i++) {
    $row['mb'] = get_table('member','mb_id',$row['mb_id'],'mb_name');
    $row['mb_name'] = $row['mb']['mb_name'];
    // print_r2($row);
    $mb_selects[$i] = array('mb_id'=>$row['mb_id'],'mb_name'=>$row['mb_name']);
}
// print_r2($mb_selects);


$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>
<style>
.td_mng {width:90px;max-width:90px;}
.td_pri_subject a, .td_mb_name a {text-decoration: underline;}
.td_pri_price {width:80px;}
.td_pic_value a{color:#ff5e5e;}
.tr_total td {background-color: #162037;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건</span></span>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p><?=$st_date?> 각 작업자별 생산 현황입니다.</p>
    <p>10분 정도 시차 Delay(딜레이)를 두고 실시간 반영됩니다. 시스템 부하를 분산시키기 위한 불가피한 조치입니다. 현시점 실시간 반영하시려면 상단 [생산현황동기화] 버튼을 클릭하세요.</p>
    <p>항목 중에서 비가동 시간이 의미하는 바는 (<a href="<?=G5_USER_ADMIN_URL?>/system/offwork_list.php">계획정지</a> + <a href="<?=G5_USER_ADMIN_URL?>/system/manual_downtime_list.php">설비비가동</a>)입니다.</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" style="width:100%;">
<label for="sfl" class="sound_only">검색대상</label>
<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:90px;">
<select name="ser_mb_id" id="ser_mb_id">
    <option value="">작업자전체</option>
    <?php
    for ($i=0; $i<sizeof($mb_selects); $i++) {
        echo '<option value="'.$mb_selects[$i]['mb_id'].'">'.$mb_selects[$i]['mb_name'].' ('.$mb_selects[$i]['mb_id'].')</option>';
    }
    ?>
</select>
<script>$('#ser_mb_id').val('<?=$ser_mb_id?>');</script>
<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <option value="bom_part_no" <?=get_selected($sfl, 'bom_part_no')?>>품번</option>
    <option value="bom_name" <?=get_selected($sfl, 'bom_name')?>>품명</option>
    <option value="pri.bom_idx" <?=get_selected($sfl, 'pri.bom_idx')?>>BOM번호</option>
    <option value="mms_idx" <?=get_selected($sfl, 'mms_idx')?>>설비번호</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit btn_submit2" value="검색">
</form>




<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">
<?=$form_input?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="pri_list_chk" style="display:none;">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" style="min-width:200px;">품번/품명</th>
        <th scope="col">구분</th>
        <th scope="col">차종</th>
        <th scope="col">작업자</th>
        <th scope="col">설비</th>
        <th scope="col">생산시간</th>
        <th scope="col">비가동</th>
        <th scope="col">UPH</th>
        <th scope="col">목표</th>
        <th scope="col">생산수량</th>
        <th scope="col" style="width:60px;">달성율</th>
        <th scope="col" style="width:200px;">그래프</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
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
        if($row['dt']['pic_min_dt'] && $row['dt']['pic_max_dt']) {
            // print_r2($row['dt']);
            $row['pri_work_seconds'] = strtotime($row['dt']['pic_max_dt']) - strtotime($row['dt']['pic_min_dt']);
            $row['pri_work_min'] = $row['pri_work_seconds']/60;
            $row['pri_work_min_text'] = $row['pri_work_min'] ? '<br>('.number_format($row['pri_work_min'],2).'분)' : '';
            // echo $row['pri_work_seconds'].BR;
            $row['pri_work_hour'] = $row['pri_work_seconds']/3600;
            // echo $row['pri_work_hour'].BR;

            // 실제 적용시간 범위
            $row['dta_start_his'] = preg_replace("/:/","",substr($row['dt']['pic_min_dt'],11));
            $row['dta_end_his'] = preg_replace("/:/","",substr($row['dt']['pic_max_dt'],11));
            // if($row['mms_idx']==139) {
            //     echo $i.BR;
            //     echo $row['dta_start_his'].'~'.$row['dta_end_his'].' 적용시간범위<br>';
            // }

// // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
// if($row['bom_idx'] == 261 && $row['mms_idx'] == 140 && $row['mb_id'] == '01056058011') {
//     print_r2($row['dt']);
// }

            // 계획정지 (일단은 설비 상관없이 전체 적용), 위에서 만들어둔 배열 활용
            for($j=0;$j<@sizeof($offwork);$j++){
                // print_r2($offwork[$j]);
                // echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
// // text print <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< 89311-S8530, 유말, 50호기
// if($row['bom_idx'] == 261 && $row['mms_idx'] == 140 && $row['mb_id'] == '01056058011') {
//                 echo $offwork[$j]['start'].'~'.$offwork[$j]['end'].' 원본<br>';
//                 echo num2seconds($offwork[$j]['end']).'~'.num2seconds($offwork[$j]['start']).' times<br>';
                
// }

                // 같은 값도 있네요. (통과)
                if( $row['dta_start_his'] == $row['dta_end_his']) {
                     continue;
                }
                // 완전 포함인 경우는 무조건 공제시간
                else if( $row['dta_start_his'] <= $offwork[$j]['start'] && $row['dta_end_his'] >= $offwork[$j]['end'] ) {
                    $row['offworks_arr'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 재활용
                    $row['offworks_arr'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 재활용
                    $row['offwork_sec'][$i] += num2seconds($offwork[$j]['end']) - num2seconds($offwork[$j]['start']);
                }
                // 걸쳐 있는 경우
                else if( $row['dta_start_his'] <= $offwork[$j]['end'] && $row['dta_end_his'] >= $offwork[$j]['start'] ) {
                    // echo $j.BR;
                    // echo $row['dta_start_his'] .'<='. $offwork[$j]['end'] .'&&'. $row['dta_end_his'] .'>='. $offwork[$j]['start'].BR;
                    if( $row['dta_start_his'] >= $offwork[$j]['start'] ) {
                        $row['offworks_arr'][$i][$j]['start'] = $row['dta_start_his'];  // 하단 비가동에서 재활용
                        $row['offworks_arr'][$i][$j]['end'] = $offwork[$j]['end'];      // 하단 비가동에서 재활용
                        // $offwork[$j]['start'] = $row['dta_start_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork_sec'][$i] += num2seconds($offwork[$j]['end']) - num2seconds($row['dta_start_his']);
                    }
                    if( $row['dta_end_his'] <= $offwork[$j]['end'] ) {
                        $row['offworks_arr'][$i][$j]['start'] = $offwork[$j]['start'];  // 하단 비가동에서 재활용
                        $row['offworks_arr'][$i][$j]['end'] = $row['dta_end_his'];      // 하단 비가동에서 재활용
                        // $offwork[$j]['end'] = $row['dta_end_his']; // 원본을 바꾸면 안 됨 (for문에서 변경되므로)
                        $row['offwork_sec'][$i] += num2seconds($row['dta_end_his']) - num2seconds($offwork[$j]['start']);
                    }
                }
            }
            // echo '계획정지 공제시간 합(sec): '.$row['offwork_sec'][$i].'<br>';
            // echo '계획정지 arr['.$i.']: '.BR.print_r2($row['offworks_arr'][$i]); // 하단에서 중복 제거용
            $row['offwork_hour'][$i] = $row['offwork_sec'][$i] ? $row['offwork_sec'][$i]/3600 : 0;  // convert to hour unit.
            $row['pri_work_hour'] -= $row['offwork_hour'][$i];
        

            
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
                        print_r2($row['offworks_arr']);
                        print_r2($downtime[$j]);
                        // echo $downtime[$j]['start'].'~'.$downtime[$j]['end'].' 원본<br>';
                    }

                    // 비가동 시간 일단을 먼저 추출하고
                    $row['downtime_sec'][$i] += num2seconds($downtime[$j]['end'])- num2seconds($downtime[$j]['start']);
                    echo $row['downtime_sec'][$i].' --> 비가동 시간'.BR;

                    // 계획정비 배열 전체를 돌면서 중복 부분만 제거해 주면 됨
                    if(is_array($row['offworks_arr'][$i])) {
                        foreach($row['offworks_arr'][$i] as $k1=>$v1) {
                            // print_r2($v1);
                            // echo $v1['start'].'~'.$v1['end'].' 계획정지(위에서 추출한 값)<br>';

                            // 같은 값도 있네요. (통과)
                            if( $downtime[$j]['start'] == $downtime[$j]['end']) {
                                continue;
                            }
                            // 계획정비가 비가동에 완전 포함되는 경우는 중복이므로 제외해야 함
                            else if( $downtime[$j]['start'] >= $row['offworks_arr'][$i][$k1]['start'] && $downtime[$j]['end'] <= $row['offworks_arr'][$i][$k1]['end'] ) {
                                continue;
                            }
                            // 걸쳐 있는 경우
                            else if( $downtime[$j]['end'] >= $row['offworks_arr'][$i][$k1]['start'] && $downtime[$j]['start'] <= $row['offworks_arr'][$i][$k1]['end'] ) {
                                // echo $downtime[$j]['start'].'~'.$downtime[$j]['end'].' 2 적용시간범위<br>';
                                // print_r2($row['offworks_arr'][$i][$k1]);
                                // 앞쪽 구간에 걸친 경우
                                if( $downtime[$j]['end'] >= $row['offworks_arr'][$i][$k1]['start'] ) {
                                    // print_r2($row['offworks_arr'][$i][$k1]);
                                    $row['downtime_sec_duplicated'][$i] += num2seconds($row['offworks_arr'][$i][$k1]['start'])- num2seconds($downtime[$j]['start']);
                                    echo $row['offworks_arr'][$i][$k1]['start'].' - '.$downtime[$j]['start'].' --- 2'.BR;
                                }
                                // 뒤쪽 구간에 걸친 경우
                                else if( $downtime[$j]['start'] <= $row['offworks_arr'][$i][$k1]['end'] ) {
                                    // print_r2($row['offworks_arr'][$i][$k1]);
                                    // echo $downtime[$j]['end'];
                                    $row['downtime_sec_duplicated'][$i] += num2seconds($downtime[$j]['end']) - num2seconds($row['offworks_arr'][$i][$k1]['end']);
                                    echo $downtime[$j]['end'].' - '.$row['offworks_arr'][$i][$k1]['end'].' --- 3'.BR;
                                }
                            }
                            
                        }
                    }
                    echo $row['downtime_sec_duplicated'][$i].BR;

                }

            }
            // echo '비가동 공제시간 합(sec): '.$row['downtime_sec'][$i].'<br>';



            // UPH 계산
            $row['pri_uph'] = ($row['pic']['pic_sum']&&$row['pri_work_hour']) ? number_format($row['pic']['pic_sum']/$row['pri_work_hour'],1) : 0;
            // echo $row['pri_uph'].BR;
            $row['pri_uph_text'] = $row['pic']['pic_sum'] ? '<span title="'.$row['pri_work_seconds'].'(s)='.number_format($row['pri_work_hour'],2).'(h)">'.$row['pri_uph'].'</span>' : 0;
            $pri_uph_arr[] = $row['pri_uph'];
            $pri_uph_total += $row['pri_uph'];

        }
        //// 생산 시작 및 종료시간 ----------------------------------------------------------
        
        // 계획정지 + 비가동 표시
        $row['offdown_seconds'] = $row['offwork_sec'][$i] + $row['downtime_sec'][$i];
        $row['offdown_min'] = $row['offdown_seconds'] ? $row['offdown_seconds']/60 : 0;
        $row['offdown_text'] = $row['offdown_min'] ? number_format($row['offdown_min'],1).'분' : '';


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

        // 버튼들
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&amp;w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?=$bg?>" tr_id="<?=$row[$pre.'_idx']?>">
        <td class="td_chk" style="display:none;">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_part_no_name td_left"><!-- 품번/품명 -->
            <?=$row['bom_part_no']?><br><?=$row['bom_name']?>
        </td>
        <td class="td_pri_type font_size_7"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 구분 -->
        <td class="td_bct_idx font_size_7"><?=$row['bct']['bct_name']?></td><!-- 차종 -->
        <td class="td_mb_name"><a href="?ser_mb_id=<?=$row['mb_id']?>"><?=$row['mb1']['mb_name']?></a></td><!-- 작업자 -->
        <td class="td_mms_name"><a href="?sfl=mms_idx&stx=<?=$row['mms_idx']?>"><?=$g5['mms'][$row['mms_idx']]['mms_name']?></a></td><!-- 설비 -->
        <td class="td_pri_hours font_size_7"><?=$row['pri_hours']?><?=$row['pri_work_min_text']?></td><!-- 생산시간 -->
        <td class="td_pri_offdown font_size_7"><?=$row['offdown_text']?></td><!-- 비가동 -->
        <td class="td_pri_uph"><?=$row['pri_uph']?></td><!-- UPH -->
        <td class="td_pri_value"><?=$row['pri_value']?></td><!-- 목표 -->
        <td class="td_pic_value color_red"><a href="./item_today_list.php?ser_mms_idx=<?=$row['mms_idx']?>&ser_mb_id=<?=$row['mb_id']?>"><?=(int)$row['pic']['pic_sum']?></a></td><!-- 수량 -->
        <td class="td_pri_rate color_yellow font_size_7"><?=number_format($row['rate_percent'],1)?> %</td><!-- 달성율 -->
        <td class="td_graph td_left"><!-- 그래프 -->
            <?=$row['graph']?>
        </td>
    </tr>
    <?php
        // 목표 합계
        $target_goal += $row['pri_value'];
        $production_total += (int)$row['pic']['pic_sum'];
    }
    if ($i == 0) {
        echo '<tr><td colspan="20" class="empty_table">자료가 없습니다.</td></tr>';
    }
    else {
        // print_r2($pri_uph_arr);
        // echo $pri_uph_total.BR;
        // echo sizeof($pri_uph_arr).BR;
        $row['pri_uph_ave'] = $pri_uph_arr[0] ? number_format($pri_uph_total/sizeof($pri_uph_arr),1) : 0;
        $row['rate'] = (!$target_goal||!$production_total) ? 0 : $production_total / $target_goal * 100;
        if($target_goal && $production_total) {
            $row['rate_percent'] = $production_total / $target_goal * 100;
            $row['graph'] = '<img class="graph_output" src="../img/dot.gif" style="width:'.(($row['rate_percent']>100)?100:$row['rate_percent']).'%;background:#ff9f64;" height="8px">';
        }
    ?>
    <tr class="tr_total" tr_id="">
        <td class="td_chk" style="display:none;"></td>
        <td colspan="5">합계 (UPH는 평균)</td>
        <td class="td_pri_hours font_size_7"></td><!-- 생산시간 -->
        <td class="td_offdown"></td>
        <td class="td_pri_uph"><?=$row['pri_uph_ave']?></td><!-- UPH -->
        <td class="td_pri_value"><?=number_format($target_goal)?></td>
        <td class="td_pic_value color_red"><?=number_format($production_total)?></td>
        <td class="td_pri_rate color_yellow font_size_7"><?=number_format($row['rate'],1)?> %</td><!-- 달성율 -->
        <td class="td_graph td_left"><?=$row['graph']?></td>
    </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top" style="display:no ne;">
    <a href="<?=G5_USER_URL?>/cron/socket_read.php" class="btn btn_02 btn_production_sync">생산현황동기화</a>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
var posY;
$(function(e) {
    // 생산현황동기화
	$(".btn_production_sync").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winProductionSync = window.open(href, "winProductionSync", "left=300,top=150,width=550,height=600,scrollbars=1");
        winProductionSync.focus();
	});

    $("input[name$=_date]").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        maxDate: "+0d"
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
	else if(document.pressed == "선택삭제") {
		if (!confirm("선택한 항목(들)을 정말 삭제 하시겠습니까?\n복구가 어려우니 신중하게 결정 하십시오.")) {
			return false;
		}
		// else {
		// 	$('input[name="w"]').val('d');
		// }
	}
	else if(document.pressed == "선택복제") {
		if (!confirm("선택한 항목(들)을 정말 복제 하시겠습니까?")) {
			return false;
		}
	}

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
