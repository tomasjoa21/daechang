<?php
// http://hanjoo.epcs.co.kr/user/json/measure.php?token=1099de5drf09&mms_idx=58&st_date=2022-06-30&st_time=13:33:14&en_date=2022-06-30&en_time=14:33:14&dta_type=1&dta_no=2
header("Content-Type: text/plain; charset=utf-8");
include_once('./_common.php');
if(isset($_SERVER['HTTP_ORIGIN'])){
	header("Access-Control-Allow-Origin:{$_SERVER['HTTP_ORIGIN']}");
	header("Access-Control-Allow-Credentials:true");
	header("Access-Control-Max-Age:86400"); //cache for 1 day
}

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
	if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods:GET,POST,OPTIONS");
	if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	exit(0);
}

// from변수 serialize된 변수를 분리
$params = array();
parse_str($frm_data, $params);
foreach($params as $key => $value) {
	${$key} = $value;
}

$start = $st_date.' '.$st_time;
$end = $en_date.' '.$en_time;

$sql = "SELECT * FROM g5_1_cast_shot
		WHERE machine_id = '".$machine_id."' AND start_time >= '".$start."' AND end_time <= '".$end."'
";
// echo $sql.'<br>';
$rs = sql_query_pg($sql,1);
for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
	// 샷 시작 & 종료시각
	$dta1[$j]['shot_start'] = strtotime($row['start_time'])*1000;
	$dta1[$j]['shot_end'] = strtotime($row['end_time'])*1000;
	$list[$j] = $dta1[$j];
}
//print_r2($dta1);


echo json_encode( $list );
exit;
?>