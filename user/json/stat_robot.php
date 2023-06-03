<?php
// http://hanjoo.epcs.co.kr/user/json/stat_robot.php?token=1099de5drf09&st_date=2022-11-01&en_date=2022-11-08
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
else {

    $sql = "SELECT 
                COUNT(*) as count, 
                to_char(time, 'YYYY-MM-DD') as dta_date 
            FROM 
                g5_1_robot
            WHERE 
                time >= '".$_REQUEST['st_date']."' AND time <= '".$_REQUEST['en_date']."'
            GROUP BY 
                to_char(time, 'YYYY-MM-DD')
    ";
    // echo $sql.'<br>';
    // exit;
    $result = sql_query_pg($sql,1);
    $list = array();
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
        // print_r2($row);
        $data[$i] = array($row['dta_date'],intval($row['count']));
        // 좌표 list array
        $list[$i] = $data[$i];
    }
    //print_r2($list);
}

echo json_encode( $list );
?>