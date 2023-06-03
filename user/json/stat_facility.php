<?php
// http://hanjoo.epcs.co.kr/user/json/stat_facility.php?token=1099de5drf09&st_date=2022-11-01&en_date=2022-11-08
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

    if(is_array($g5['mms'])) {
        foreach ($g5['mms'] as $k1=>$v1 ) {
            // echo $k1.'<br>';
            // print_r2($g5['mms'][$k1]);
            $sqls[] = "SELECT COUNT(*) as count, to_char(dta_dt, 'YYYY-MM-DD') as dta_date 
                    FROM g5_1_data_measure_".$k1."
                    WHERE dta_dt >= '".$_REQUEST['st_date']."' AND dta_dt <= '".$_REQUEST['en_date']."'
                    GROUP BY to_char(dta_dt, 'YYYY-MM-DD')
            ";
        }
    }


    $sql = "SELECT SUM(count) AS count, dta_date
            FROM ( ".implode(" UNION ",$sqls)." ) AS db1
            GROUP BY dta_date
    ";
    // $sql = "SELECT SUM(count) AS count, dta_date
    //         FROM (
    //             SELECT COUNT(*) as count, to_char(dta_dt, 'YYYY-MM-DD') as dta_date 
    //             FROM g5_1_data_measure_58
    //             WHERE dta_dt >= '2022-11-01' AND dta_dt <= '2022-11-08'
    //             GROUP BY to_char(dta_dt, 'YYYY-MM-DD')
    //             UNION
    //             SELECT COUNT(*) as count, to_char(dta_dt, 'YYYY-MM-DD') as dta_date 
    //             FROM g5_1_data_measure_59
    //             WHERE dta_dt >= '2022-11-01' AND dta_dt <= '2022-11-08'
    //             GROUP BY to_char(dta_dt, 'YYYY-MM-DD')
    //             UNION
    //             SELECT COUNT(*) as count, to_char(dta_dt, 'YYYY-MM-DD') as dta_date 
    //             FROM g5_1_data_measure_60
    //             WHERE dta_dt >= '2022-11-01' AND dta_dt <= '2022-11-08'
    //             GROUP BY to_char(dta_dt, 'YYYY-MM-DD')
    //         ) AS db1
    //         GROUP BY dta_date
    // ";
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