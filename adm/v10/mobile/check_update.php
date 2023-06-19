<?php
include_once('./_common.php');

$sql = " UPDATE {$g5['pallet_table']} 
            SET plt_check_yn = '{$plt_check_yn}'
                , plt_status = 'ok'
                , plt_update_dt = '".G5_TIME_YMDHIS."'
        WHERE plt_idx = '{$plt_idx}' ";
sql_query($sql,1);

goto_url('./check.php?plt_idx='.$plt_idx);