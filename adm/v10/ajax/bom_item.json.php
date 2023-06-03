<?php
// http://daechang.epcs.co.kr/adm/v10/ajax/bom_item.json.php?aj=del&bop_idx=3

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

// change main product among the sub products under specific bom_idx
if ($aj == "c1") {

    if($bom_idx&&$bit_idx) {
        // 기존 main_yn 값을 제거
        $sql = " UPDATE {$g5['bom_item_table']} SET bit_main_yn = 0 WHERE bom_idx = '".$bom_idx."' ";
        sql_query($sql,1);

        // 새로운 main_yn 설정
        $sql = " UPDATE {$g5['bom_item_table']} SET bit_main_yn = 1 WHERE bit_idx = '".$bit_idx."' ";
        sql_query($sql,1);
    
        $response->result = true;
        $response->bom_idx = $bom_idx;
        $response->bit_idx = $bit_idx;
        $response->msg = "정보 처리 성공!";
    }
    else {
        $response->err_code='E10';
        $response->msg = "변수값이 제대로 넘어오지 않았습니다.";
    }
}
else {
	$response->err_code='E00';
	$response->msg = "잘못된 접근입니다. \n정상적인 방법으로 이용해 주세요.";
}


$response->sql = $sql;

echo json_encode($response);
exit;
?>