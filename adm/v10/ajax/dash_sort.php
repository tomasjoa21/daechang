<?php
include_once('./_common.php');

if($sorted){
$sort_arr = explode(',',$sorted);
echo json_encode($sort_arr);
for($i=1;$i<=count($sort_arr);$i++){
    $mta_mod_sql = " UPDATE {$g5['meta_table']} SET
                        mta_number = {$i}
                        ,mta_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE mta_db_table = 'member' 
                        AND mta_db_id = '{$member['mb_id']}'
                        AND mta_key = 'dashboard_menu'
                        AND mta_status = 'ok'
                        AND mta_idx = '{$sort_arr[$i-1]}'
    ";
    sql_query($mta_mod_sql);
}    
}


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();
