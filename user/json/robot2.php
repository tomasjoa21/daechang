<?php
// http://hanjoo.epcs.co.kr/user/json/robot.php?token=1099de5drf09&robot=1&type=tq2 (1번로봇)
// http://hanjoo.epcs.co.kr/user/json/robot.php?token=1099de5drf09&robot=2&type=tq2 (2번로봇)
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

$skips = array('rob_idx','time','robot_no','alarm','status');


// 토큰 비교
if(!check_token1($_REQUEST['token'])) {
    $list = array("code"=>499,"message"=>"token error");
    echo json_encode( array($list) );
    exit;
}
else if($_REQUEST['robot']){

    $fields = sql_field_names_pg('g5_1_robot');

    $sql = "SELECT * FROM g5_1_robot WHERE robot_no = '".$_REQUEST['robot']."' ORDER BY time DESC LIMIT 10 ";
    // echo $sql.'<br>';
    // exit;
    $result = sql_query_pg($sql,1);
    $list = array();
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
        $row['no'] = $i;
        $row['timestamp'] = strtotime($row['time']);
        // 좌표에 표현할 value
        $dta1[$i]['x'] = $row['timestamp']*1000;
        $dta1[$i]['y'] = intval($row['tq1']);
        $dta1[$i]['dt'] = $row['time'];
        for($j=0;$j<sizeof($fields);$j++) {
            if(in_array($fields[$j],$skips)) {continue;}
            $dta1[$i][$fields[$j]] = intval($row[$fields[$j]]);
        }
        // 좌표 list array
        $list[$i] = $dta1[$i];
    }
    //print_r2($dta1);
    
    // in case of no data.
    if(!$list[0]) {
        $dta1[0]['x'] = G5_SERVER_TIME*1000;
        $dta1[0]['y'] = 0;
        $dta1[0]['dt'] = date("Y-m-d H:i:s",G5_SERVER_TIME);
        for($j=0;$j<sizeof($fields);$j++) {
            if(in_array($fields[$j],$skips)) {continue;}
            $dta1[$i][$fields[$j]] = 0;
        }
        $list[0] = $dta1[0];
    }
}


echo json_encode( $list );
?>