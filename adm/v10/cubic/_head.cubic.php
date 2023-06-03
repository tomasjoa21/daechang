<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// cubic 서버 연결 함수
if(!function_exists('cubic_server_connect')){
function cubic_server_connect()
{
	global $g5,$connect_db_pdo;

	// // 기존 디비 연결 해제
	// $link = $g5['connect_db'];
	// if( function_exists('mysqli_query') )
	// 	$result = mysqli_close($link) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
	// else
	// 	$result = mysql_close($link) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");

	// $smsDbHost="61.83.89.58"; // 공인아이피
	$smsDbHost="192.1.2.5";
	$smsDbUser="MES";
	$smsDbPass="MES_HANJOO";
	$smsDbName="HANJOO";
	try {
		$connect_db_pdo = new PDO('mysql:host='.$smsDbHost.';port=9933;dbname='.$smsDbName, $smsDbUser, $smsDbPass);
		$g5['pdo_yn'] = 1;
	}
	catch( PDOException $Exception ) {
		$connect_db_pdo  = @mysql_connect($smsDbHost,$smsDbUser,$smsDbPass) or die("DB connent Error... Check your database setting");
		mysql_select_db($smsDbName,$connect_db_pdo);
		$g5['pdo_yn'] = 0;
	}

}
}

// cubic 서버 연결 종료
if(!function_exists('cubic_server_close')){
function cubic_server_close()
{
	global $g5,$connect_db_pdo,$connect_db;

	// 종료
	if($g5['pdo_yn'])
		$connect_db_pdo = null;
	else
		mysql_close($connect_db_pdo);


	// // 영카트 디비 재연결
    // $connect_db = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
    // $select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
    // $g5['connect_db'] = $connect_db;
    // sql_set_charset('utf8', $connect_db);

}
}

// DB 연결
cubic_server_connect();


// 관련 설정
$cubic = array();
$cubic['company_table']	= 'company';
$cubic['member_table']	= 'member';
$cubic['send_table']	= 'z_send';
$cubic['manufacture_table']	= 'manufacture';
$cubic['payment_detail_table']	= 'payment_detail';
$cubic['live_web_table']	= 'live_web_info_190716';
$cubic['mes_cast_shot_sub_table']	= 'mes_cast_shot_sub';


// 주야간
$cubic['set_work_shift'] = array(
	"1"=>"주간"
	,"2"=>"야간"
);




?>
