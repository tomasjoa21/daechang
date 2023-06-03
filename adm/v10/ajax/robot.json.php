<?php
// http://hanjoo.epcs.co.kr/adm/v10/ajax/robot.json.php?aj=r1&act=warn&no=1
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


//-- 디폴트 상태 (실패) --//
$response = new stdClass();
$response->result=false;

// print_r2($_REQUEST);

if ($aj == "r1") {

    $sleep_time = ($act=='stop') ? ' 10':'';

	$py = exec('python /home/admin/robot/james/robot_'.$act.'.py '.$no.$sleep_time);
	// $py = system('python /home/admin/robot/james/robot_warn.py 1');
    // $py = exec('python /home/admin/robot/james/z1.py');
    // echo $py;
	// echo exec("whoami").'<br>';


	// exec("python /home/admin/robot/james/robot_warn.py 1", $output); 
	// // exec("python /home/admin/robot/james/z1.py", $output); 
	// var_dump($output);	

    $response->result = true;
    $response->msg = $py;

}
else {
	$response->err_code='E00';
	$response->msg = "잘못된 접근입니다. \n정상적인 방법으로 이용해 주세요.";
}


$response->sql = $sql;

echo json_encode($response);
exit;
?>