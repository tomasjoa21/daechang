<?php
include_once('./_common.php');
//$menu_cd,$menu_mta_idx,$grid_width,$grid_height,$grid_padding
/*
{$g5['dash_grid_table']}dsg_idx,mta_idx,dsg_width_num,dsg_height_num,dsg_order,dsg_status,dsg_reg_dt,dsg_update_dt
{$g5['meta_table']}mta_idx,mta_country,mta_db_table=member,mta_db_id=super,mta_key=dashboard_layout,mta_value=,mta_title=,mta_number=,mta_status=,mta_reg_dt=,mta_update_dt=
*/

//member_dash 상태값 trash
$mbd_trash_sql = " DELETE FROM {$g5['member_dash_table']}
                    WHERE mta_idx = '{$mta_idx}'
                        AND dsg_idx = '{$dsg_idx}'
";
// $mbd_trash_sql = " UPDATE {$g5['member_dash_table']} SET
//                         mbd_status = 'trash'
//                         ,mbd_update_dt = '".G5_TIME_YMDHIS."'
//                     WHERE mta_idx = '{$mta_idx}'
//                         AND dsg_idx = '{$dsg_idx}'
// ";
// echo $mbd_trash_sql.'<br>';
sql_query($mbd_trash_sql,1);
//dash_grid 상태값 trash
$dsg_trash_sql = " DELETE FROM {$g5['dash_grid_table']}
                    WHERE mta_idx = '{$mta_idx}'
                        AND dsg_idx = '{$dsg_idx}'
";
// $dsg_trash_sql = " UPDATE {$g5['dash_grid_table']} SET
//                         dsg_status = 'trash'
//                         ,dsg_update_dt = '".G5_TIME_YMDHIS."'
//                     WHERE mta_idx = '{$mta_idx}'
//                         AND dsg_idx = '{$dsg_idx}'
// ";
// echo $dsg_trash_sql.'<br>';
sql_query($dsg_trash_sql,1);


// 남은 dsg 레코드의 dsg_order 순서를 재지정
$sql = " SELECT dsg_idx FROM {$g5['dash_grid_table']}
            WHERE mta_idx = '{$mta_idx}'
                AND dsg_status = 'ok'
            ORDER BY dsg_order, dsg_idx
";
$result = sql_query($sql,1);
if($result->num_rows){
    $n = 0;
    for($i=0;$row=sql_fetch_array($result);$i++){
        $n = $i+1;
        $dsg_sql = " UPDATE {$g5['dash_grid_table']} SET
                        dsg_order = '{$n}'
                        ,dsg_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE mta_idx = '{$mta_idx}'
                        AND dsg_idx = '{$row['dsg_idx']}'
        ";
        sql_query($dsg_sql,1);
    }
}


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();
