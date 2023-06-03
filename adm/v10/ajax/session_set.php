<?php
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

if($cam_idx) {
	set_session('ss_cam_idx', $cam_idx);
	set_session('ss_db_table', 'campaign');
}
if($com_idx) {
	set_session('ss_com_idx', $com_idx);
	set_session('ss_db_table', 'company');
}
if($mb_id) {
	set_session('ss_mbr_id', $mb_id);
	set_session('ss_db_table', 'member');
}
if($search_detail) {
	$flag = ($search_detail=='open') ? 1 : 0;
	set_session('ss_campaign_search_open', $flag);
}
if($search_mb_detail) {
	$flag = ($search_mb_detail=='open') ? 1 : 0;
	set_session('ss_member_search_open', $flag);
}
if($mb_list_type) {
	set_session('ss_mb_list_type', $mb_list_type);
}
if($fname) {
	$flag = ($flag=='open') ? 1 : 0;
	set_session('ss_'.$fname.'_open', $flag);
}

$response->result = true;
$response->flag = $flag;
$response->msg = "세션을 설정했습니다.";

echo json_encode($response);
exit;
?>