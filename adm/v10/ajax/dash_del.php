<?php
include_once('./_common.php');
$cd_arr = array();
for($j=11;$j<90;$j++){
    $cd_arr[$j-10] = $j;
}

//$mta_idx 값으로 개인별 packery 순서값도 삭제하도록 쿼리 추가해야 한다.(packery 개발완료후)


//해당 sub_menu코드의 메뉴를 삭제한다.
$mta_trash_sql = " DELETE FROM {$g5['meta_table']}
                WHERE mta_db_table = 'member'
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                    AND mta_idx= '{$mta_idx}'
";
// $mta_trash_sql = " UPDATE {$g5['meta_table']} SET
//                     mta_status = 'trash'
//                     ,mta_update_dt = '".G5_TIME_YMDHIS."'
//                 WHERE mta_db_table = 'member'
//                     AND mta_db_id = '{$member['mb_id']}'
//                     AND mta_key = 'dashboard_menu'
//                     AND mta_idx= '{$mta_idx}'
// ";
sql_query($mta_trash_sql,1);
//해당 g5_1_dash_grid 테이블의 레코드도 trash 상태로 변경
$dsg_trash_sql = " DELETE FROM {$g5['dash_grid_table']} WHERE mta_idx = '{$mta_idx}' ";
// $dsg_trash_sql = " UPDATE {$g5['dash_grid_table']} SET 
//                         dsg_status = 'trash'
//                         ,dsg_update_dt = '".G5_TIME_YMDHIS."'
//                     WHERE mta_idx = '{$mta_idx}'
// ";
sql_query($dsg_trash_sql,1);
//해당 g5_1_member_dash 테이블의 레코드도 trash 상태로 변경
$mbd_trash_sql = " DELETE FROM {$g5['member_dash_table']} WHERE mta_idx = '{$mta_idx}' ";
// $mbd_trash_sql = " UPDATE {$g5['member_dash_table']} SET 
//                         mbd_status = 'trash'
//                         ,mbd_update_dt = '".G5_TIME_YMDHIS."'
//                     WHERE mta_idx = '{$mta_idx}'
// ";
sql_query($mbd_trash_sql,1);



//남은 sub_menu코드의 메뉴들을 조회한다.
$mta_sql = " SELECT mta_idx FROM {$g5['meta_table']} 
                WHERE mta_db_table = 'member' 
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                    AND mta_status = 'ok'
                ORDER BY mta_number, mta_idx
";
$result = sql_query($mta_sql);
$str = '';
if($result->num_rows){
    for($i=1;$row=sql_fetch_array($result);$i++){
        $mta_mod_sql = " UPDATE {$g5['meta_table']} SET
                            mta_value = '915{$cd_arr[$i]}0'
                            ,mta_number = '{$i}'
                            ,mta_update_dt = '".G5_TIME_YMDHIS."'
                        WHERE mta_db_table = 'member'
                            AND mta_db_id = '{$member['mb_id']}'
                            AND mta_key = 'dashboard_menu'
                            AND mta_idx = '{$row['mta_idx']}'
                            AND mta_status = 'ok'
        ";
        $str .= $mta_mod_sql;
        sql_query($mta_mod_sql);
    }
}
// echo $str;


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();

