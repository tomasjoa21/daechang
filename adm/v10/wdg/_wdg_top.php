<?php
$filename = "../../../data/dbconfig.php";
$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
$temp_db['g5_mysql_host'] = '';
$temp_db['g5_mysql_user'] = '';
$temp_db['g5_mysql_password'] = '';
$temp_db['g5_mysql_db'] = '';
$i = 0;
while(!feof($fp)){
	if($i === 6) break;
	$dbstr = fgets($fp);
	if(preg_match('/G5_MYSQL_HOST/',$dbstr)){
		$dbstrArr = array();
		//echo $dbstr."<br>";
		if(preg_match('/\',\'/',$dbstr)){
			$dbstrArr = explode("','",$dbstr);
		}else if(preg_match('/\', \'/',$dbstr)){
			$dbstrArr = explode("', '",$dbstr);
		}
		$temp_db['g5_mysql_host'] = trim(substr($dbstrArr[1],0,-4));
	}
	else if(preg_match('/G5_MYSQL_USER/',$dbstr)){
		$dbstrArr = array();
		//echo $dbstr."<br>";
		if(preg_match('/\',\'/',$dbstr)){
			$dbstrArr = explode("','",$dbstr);
		}else if(preg_match('/\', \'/',$dbstr)){
			$dbstrArr = explode("', '",$dbstr);
		}
		$temp_db['g5_mysql_user'] = trim(substr($dbstrArr[1],0,-4));
	}
	else if(preg_match('/G5_MYSQL_PASSWORD/',$dbstr)){
		$dbstrArr = array();
		//echo $dbstr."<br>";
		if(preg_match('/\',\'/',$dbstr)){
			$dbstrArr = explode("','",$dbstr);
		}else if(preg_match('/\', \'/',$dbstr)){
			$dbstrArr = explode("', '",$dbstr);
		}
		$temp_db['g5_mysql_password'] = trim(substr($dbstrArr[1],0,-4));
	}
	else if(preg_match('/G5_MYSQL_DB/',$dbstr)){
		$dbstrArr = array();
		//echo $dbstr."<br>";
		if(preg_match('/\',\'/',$dbstr)){
			$dbstrArr = explode("','",$dbstr);
		}else if(preg_match('/\', \'/',$dbstr)){
			$dbstrArr = explode("', '",$dbstr);
		}
		$temp_db['g5_mysql_db'] = trim(substr($dbstrArr[1],0,-4));
	}
	$i++;
}
fclose($fp);

include_once('./_tmp_sql.lib.php');
?>