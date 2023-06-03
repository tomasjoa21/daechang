<?php
include_once('./_common.php');
$mta_mod_sql = " UPDATE {$g5['meta_table']} SET
                    mta_title = '{$mta_title}'
                    ,mta_update_dt = '".G5_TIME_YMDHIS."'
                WHERE mta_db_table = 'member'
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                    AND mta_idx = '{$mta_idx}'
                    AND mta_status = 'ok'
";
sql_query($mta_mod_sql);


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();
