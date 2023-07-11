<?php
include_once('./_common.php');
$moi_check_text = ($moi_check_yn) ? '' : trim($moi_check_text);
$moi_status = ($moi_check_text) ? 'reject' : 'ready';
$sql = " UPDATE {$g5['material_order_item_table']} 
            SET moi_check_yn = '{$moi_check_yn}'
                , moi_check_text = '{$moi_check_text}'
                , moi_status = '{$moi_status}'
                , moi_update_dt = '".G5_TIME_YMDHIS."'
        WHERE moi_idx = '{$moi_idx}' ";
sql_query($sql,1);

goto_url('./input_check.php?moi_idx='.$moi_idx);