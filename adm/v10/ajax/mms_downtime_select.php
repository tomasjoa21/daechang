<?php
// 호출 파일들
// /adm/v10/member_select.php 업체선택 버튼
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

// 비가동 타입
$sql = "SELECT mst_idx, mst_name
		FROM {$g5['mms_status_table']}
		WHERE mms_idx = '".$_REQUEST['mms_idx']."'
			AND mst_type = 'offwork'
			AND mst_status = 'ok'
		ORDER BY mst_idx
";
// echo $sql.'<br>';
$rs = sql_query($sql, 1);
while($row = sql_fetch_array($rs)) {
	$response->rows2[] = $row;
}

$response->result = true;
$response->msg = "데이타를 성공적으로 가지고 왔습니다.";

$response->sql = $sql;

echo json_encode($response);
exit;
?>