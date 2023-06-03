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

if(!$_REQUEST['token']){
//	$list = array('');
}
else if($_REQUEST['mms_idx']){

    // 토큰 비교
    if(!check_token1($_REQUEST['token'])) {
        $list = array("code"=>499,"message"=>"token error");
        echo json_encode( array($list) );
		exit;
    }

    $where = array();
    // $where[] = " (1) ";   // 디폴트 검색조건

    if($dta_type) {
        $where[] =  " dta_type = '".$dta_type."' ";
    }
    if($dta_no) {
        $where[] =  " dta_no = '".$dta_no."' ";
    }

    // 날짜 조건 (mbd_idx 있는 경우 대시보드에서 호출할 때 범위안에 값이 없으면 시간 범위를 앞쪽으로 할당해서 그래프가 최소한 보이게 설정합니다.)
    $start = $st_date.' '.$st_time;
    $end = $en_date.' '.$en_time;
    if($_REQUEST['mbd_idx']) {  // 대시보드에서 호출한 경우 재설정이 필요한 경우는 재설정

        $ar['type'] = 'data'; // 데이터 입력 시점을 기준으로 재설정한 시간
        $ar['st_date'] = $st_date;
        $ar['st_time'] = $st_time;
        $ar['en_date'] = $en_date;
        $ar['en_time'] = $en_time;
        $ar['mms_idx'] = $mms_idx;
        $ar['dta_type'] = $dta_type;
        $ar['dta_no'] = $dta_no;
        // print_r2($ar);
        $start_end_dt = get_start_end_dt($ar);  // 데이터가 없을 때를 고려한 시간 범위로 재설정
        // print_r2($start_end_dt);
        unset($ar);
    
        $start = $start_end_dt['start'];
        $end = $start_end_dt['end'];
        // echo $start.'~'.$end.'<br>';
    }
    $where[] =  " dta_dt >= '".$start."' AND dta_dt <= '".$end."' ";
    
    // 최종 WHERE 생성
    if ($where) {
        $sql_search = ' WHERE '.implode(' AND ', $where);
    }

 	// 측정 추출
    $sql = "SELECT * FROM g5_1_data_measure_".$mms_idx."
            {$sql_search}
            ORDER BY dta_dt ASC
    ";
    // echo $sql.'<br>';
    // exit;
    $result = sql_query_pg($sql,1);
	$list = array();
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
        $row['no'] = $i;
        $row['timestamp'] = strtotime($row['dta_dt']);
        // 좌표에 표현할 value
        $dta1[$i]['x'] = $row['timestamp']*1000;
        $dta1[$i]['y'] = (float)$row['dta_value'];
        $dta1[$i]['dta_1'] = (int)$row['dta_1'];
        $dta1[$i]['dta_2'] = (int)$row['dta_2'];
        $dta1[$i]['dta_3'] = (int)$row['dta_3'];
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
        $dta1[0]['dta_1'] = null;
        $dta1[0]['dta_2'] = null;
        $dta1[0]['dta_3'] = null;
        $dta1[0]['yraw'] = 0;
        $dta1[0]['yamp'] = 1;
        $dta1[0]['ymove'] = 0;
        $list[0] = $dta1[0];
    }

}

echo json_encode( $list );
?>