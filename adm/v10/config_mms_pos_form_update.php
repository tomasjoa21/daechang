<?php
$sub_menu = "910140";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$sql = " UPDATE {$g5['mms_table']}
		SET mms_pos_x = '{$mms_pos_x}'
			, mms_pos_y = '{$mms_pos_y}'
		WHERE mms_idx = '{$mms_idx}'
";

sql_query($sql,1);

echo 'ok';