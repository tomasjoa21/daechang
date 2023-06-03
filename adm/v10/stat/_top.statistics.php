<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.


// 목표달성 & 불량
$sql = "SELECT SUM( dta_value ) AS output_sum
            , SUM( CASE WHEN dta_defect = 1 THEN dta_value ELSE 0 END ) AS output_defect
        FROM {$g5['data_output_sum_table']}
        WHERE dta_date >= '".$st_date."'
            AND dta_date <= '".$en_date."'
            AND com_idx='".$com_idx."'
            {$sql_mmses}
";
// echo $sql;
$output1 = sql_fetch($sql,1);
// 목표달성율, 불량율 입력
$sum_target = number_format( $output1['output_sum']/$target['total']*100,1 );
$sum_defect = number_format( $output1['output_defect']/$output1['output_sum']*100, 2);


// 예지/알람 통계 숨김 (일지테크)
if($_SESSION['ss_com_idx']=='8') {
    $sum_predict_display = "display:none;";
}



// 설비가동율 추출
$sql = "SELECT (CASE WHEN n='1' THEN CONCAT(mms_idx) ELSE 'total' END) AS item_name
            , mms_idx
            , SUM(dta_value_sum) AS dta_value_sum
            , COUNT(mms_idx) AS mms_count
        FROM
        (
            SELECT
                mms_idx
                , SUM(dta_value) AS dta_value_sum
            FROM g5_1_data_run_sum
            WHERE dta_date >= '".$st_date."' AND dta_date <= '".$en_date."'
                AND com_idx='".$com_idx."'
                {$sql_mmses}
            GROUP BY mms_idx
            ORDER BY mms_idx

        ) AS db1, g5_5_tally AS db_no
        WHERE n <= 2
        GROUP BY item_name
        ORDER BY n DESC, item_name
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    // total 부분만 가지고 와서 사용
    if($row['item_name'] == 'total') {
        $runtime_avg = $row['dta_value_sum']/$row['mms_count'];
    }
}
// echo $runtime_avg.'<br>';

// 날짜 차이 (+1을 해 줘야 함)
$sql = " SELECT TIMESTAMPDIFF(day,'".$st_date."','".$en_date."')+1 AS days ";
$days = sql_fetch($sql,1);
$days['seconds'] = $days['days']*86400;
// echo $days['days'].'<br>';
// echo $days['days'].'<br>';
$run_rate = $runtime_avg / $days['seconds'] * 100;
// echo $run_rate.'<br>';

// 비가동 시간 다시 계산합니다. (가동율이 너무 낮다는 요청으로!!)
$run_rate2 = ($days['seconds']-$off_total) / $days['seconds'] * 100;



// 알람발생
$sql = "SELECT SUM( IF(arm_cod_type='a',1,0) ) AS arm_alarm_sum
            , SUM( IF(arm_cod_type IN ('p','p2'),1,0) ) AS arm_predict_sum
        FROM g5_1_alarm
        WHERE arm_reg_dt >= '".$st_date." 00:00:00' AND arm_reg_dt <= '".$en_date." 23:59:59'
            AND com_idx='".$com_idx."'
            {$sql_mmses}
";
// echo $sql;
$alarm1 = sql_fetch($sql,1);
$sum_alarm = number_format($alarm1['arm_alarm_sum']);
$sum_predict = number_format($alarm1['arm_predict_sum']);


// 계획정비 오늘 ~ +10일
$tmp_write_table = $g5['write_prefix'].'plan'; // 게시판 테이블 전체이름
$sql = "SELECT COUNT(wr_id) AS plan_cnt
        FROM {$tmp_write_table}
        WHERE wr_is_comment = 0
            AND wr_1 = '".$com['com_idx']."'
            {$sql_mmses2}
            AND wr_3 != ''
            AND wr_3 >= '".G5_TIME_YMD."'
            AND wr_3 < DATE_ADD('".G5_TIME_YMDHIS."' , INTERVAL +10 DAY)
        ORDER BY wr_num
";
// echo $sql.'<br>';
$plan = sql_fetch($sql,1);
$sum_plan = number_format($plan['plan_cnt']);


?>
    <!-- the start of .div_stat  -->
	<div class="div_stat">
		<ul>
			<li>
				<span class="title">생산계획</span>
				<span class="content" id="sum_alarm"><?=number_format($target['total'])?></span>
				<span class="unit">개</span>
			</li>
			<li>
				<span class="title">생산실적</span>
				<span class="content" id="sum_alarm"><?=number_format($output1['output_sum'])?></span>
				<span class="unit">개</span>
			</li>
			<li>
			   <span class="title">목표달성율</span>
               <span class="content" id="sum_target"><?=$sum_target?></span>
				<span class="unit">%</span>
			</li>
			<li style="display:none;">
			   <span class="title">불량율</span>
				<span class="content" id="sum_defect"><?=$sum_defect?></span>
				<span class="unit">%</span>
			</li>
			<li>
				<span class="title">설비가동율</span>
				<span class="content" id="sum_runtime"><?=number_format($run_rate2,1)?></span>
				<span class="unit">%</span>
			</li>
			<li style="<?=$sum_predict_display?>">
				<span class="title">예지/알람</span>
				<span class="content" id="sum_predict"><?=$sum_predict?>/<?=$sum_alarm?></span>
				<span class="unit">건</span>
			</li>
			<li style="display:none;">
				<span class="title">계획정비<d style="font-size:0.8em;">(D-10)</d></span>
				<span class="content" id="sum_plan"><?=$sum_plan?></span>
				<span class="unit">건</span>
			</li>
		</ul>
	</div>
    <!-- the end of .div_stat  -->
