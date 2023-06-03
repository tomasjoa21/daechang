<?php
include_once('../../../../../../common.php');

//-- 디폴트 상태 (실패) --//
$response = new stdClass();
$response->result=false;

// print_r2($_REQUEST);


// 수량 변경
if ($aj == "set") {
    $count = ($flag=='plus') ? $parts_count+1 : $parts_count-1;
    $sql = " UPDATE {$write_table} SET
                wr_4 = '".$count."'
            WHERE wr_id = '".$_REQUEST['wr_id']."'
    ";
    if( is_numeric($parts_count) )
        sql_query($sql,1);
    $response->result = true;
	$response->parts_count = $count;
	$response->msg = "수량 적용 성공";

}
else {
	$response->err_code='E00';
	$response->msg = "잘못된 접근입니다. \n정상적인 방법으로 이용해 주세요.";
}


$response->sql = $sql;

echo json_encode($response);
exit;