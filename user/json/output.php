<?php
// http://hanjoo.epcs.co.kr/user/json/output.php?token=1099de5drf09&mms_idx=58&st_date=2022-08-25&st_time=13:33:14&en_date=2022-08-25&en_time=14:33:14&type1=ng (디폴트는 등급합계)
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


// 토큰 비교
if(!check_token1($_REQUEST['token'])) {
    $list = array("code"=>499,"message"=>"token error");
    echo json_encode( array($list) );
    exit;
}

$where = array();
// $where[] = " (1) ";   // 디폴트 검색조건

// 날짜 조건 (범위안에 값이 없으면 시간 범위를 앞쪽으로 할당해서 그래프가 일단은 보이게 설정합니다.)
$start = $st_date.' '.$st_time;
$end = $en_date.' '.$en_time;

$where[] =  " end_time >= '".$start."' AND end_time <= '".$end."' ";

// 최종 WHERE 생성
if ($where) {
    $sql_search = ' WHERE '.implode(' AND ', $where);
}

// 측정 추출
$sql = "SELECT * FROM g5_1_xray_inspection
        {$sql_search}
        ORDER BY end_time ASC
";
// echo $sql.'<br>';
// exit;
$result = sql_query_pg($sql,1);
$list = array();
for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
    $row['no'] = $i;
    $row['timestamp'] = strtotime($row['end_time']);
    for ($j=1; $j<19; $j++) {
        $row['dta_value'] += $row['position_'.$j];
    }
    if($_REQUEST['type1'] == 'ng') {
        $row['dta_value'] = ($row['result']=='OK') ? 0:1;
    }
    // 좌표에 표현할 value
    $dta1[$i]['x'] = $row['timestamp']*1000;
    $dta1[$i]['y'] = intval($row['dta_value']);
    $dta1[$i]['yraw'] = ($dta1[$i]['y']) ?: 0;
    $dta1[$i]['yamp'] = 1;
    $dta1[$i]['ymove'] = 0;
    // 좌표 list array
    $list[$i] = $dta1[$i];
}
//print_r2($dta1);

// in case of no data.
if(!$list[0]) {
    $dta1[0]['x'] = G5_SERVER_TIME*1000;
    $dta1[0]['y'] = 0;
    $dta1[0]['yraw'] = 0;
    $dta1[0]['yamp'] = 1;
    $dta1[0]['ymove'] = 0;
    $list[0] = $dta1[0];
}


echo json_encode( $list );
?>