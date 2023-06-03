<?php
include_once('./_common.php');

// echo $aj;
// exit;

if ($aj == "c1") {

	$sql = " UPDATE {$g5['member_table']} SET mb_4 = '".$com_idx."' WHERE mb_id = '".$mb_id."' ";
	sql_query($sql,1);

    set_session('ss_com_idx', $com_idx);
	
	$msg = "업체를 변경하였습니다.";

}
else {
	$msg = "잘못된 접근입니다. \n정상적인 방법으로 이용해 주세요.";
}

echo $msg;
exit;
?>