<?php
include_once('./_common.php');
//$menu_cd,$menu_mta_idx,$grid_width,$grid_height,$grid_padding
/*
{$g5['dash_grid_table']}dsg_idx,mta_idx,dsg_width_num,dsg_height_num,dsg_order,dsg_status,dsg_reg_dt,dsg_update_dt
{$g5['meta_table']}mta_idx,mta_country,mta_db_table=member,mta_db_id=super,mta_key=dashboard_layout,mta_value=,mta_title=,mta_number=,mta_status=,mta_reg_dt=,mta_update_dt=
*/

for($i=0;$i<count($pos_json_str);$i++){
    $n = $i+1;
    $sql = " UPDATE {$g5['dash_grid_table']} SET
                dsg_order = '{$n}'
            WHERE mta_idx = '{$mta_idx}'
                AND dsg_idx = '{$pos_json_str[$i]['attr']}'
    ";
    sql_query($sql);
}


//일주일이 지난 trash상태값의 대시보드 관련 모든 데이터를 삭제
dash_delete();