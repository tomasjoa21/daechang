<?php
include_once('./_common.php');
$meta_sql = " SELECT mta_idx FROM {$g5['meta_table']} 
                WHERE mta_db_table = 'member' 
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                    AND mta_status = 'ok'
                ORDER BY mta_number, mta_idx
";
$result = sql_query($meta_sql);
$cd_arr = array();
for($j=11;$j<90;$j++){
    $cd_arr[$j-10] = $j;
}
// echo json_encode($cd_arr);
// result->num_rows
$sort_num = 0;
if($result->num_rows){
$sort_num = $result->num_rows;
for($i=1;$row=sql_fetch_array($result);$i++){
    $mta_mod_sql = " UPDATE {$g5['meta_table']} SET
                        mta_value = '915{$cd_arr[$i]}0'
                        ,mta_number = {$i}
                        ,mta_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE mta_db_table = 'member' 
                        AND mta_db_id = '{$member['mb_id']}'
                        AND mta_key = 'dashboard_menu'
                        AND mta_idx = '{$row['mta_idx']}'
                        AND mta_status = 'ok'
    ";
    sql_query($mta_mod_sql);
}    
}
//array('915000', '대시보드', ''.G5_USER_ADMIN_URL.'/index.php', 'index');

$sort_num++;
echo $sort_num.':'.$cd_arr[$sort_num];
$sql = " INSERT INTO {$g5['meta_table']} SET
            mta_db_table = 'member'
            ,mta_db_id = '{$member['mb_id']}'
            ,mta_key = 'dashboard_menu'
            ,mta_value = '915{$cd_arr[$sort_num]}0'
            ,mta_title = '대시보드{$sort_num}'
            ,mta_number = '{$sort_num}'
            ,mta_status = 'ok'
            ,mta_reg_dt = '".G5_TIME_YMDHIS."'
            ,mta_update_dt = '".G5_TIME_YMDHIS."'
";
sql_query($sql);


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();
