<?php
/*
$menu["menu915"] = array (
    array('915000', '대시보드', ''.G5_USER_ADMIN_URL.'/index.php', 'index'),
    array('915110', '대시보드', ''.G5_USER_ADMIN_URL.'/index.php', 'index'),
    array('940115', '제1대시보드', ''.G5_USER_ADMIN_URL.'/index1.php', 'index1'),
    array('915130', '제2대시보드', ''.G5_USER_ADMIN_URL.'/index2.php', 'index2'),
    array('915900', '대시보드설정', ''.G5_USER_ADMIN_URL.'/config_dashboard_form.php', 'config_dashboard_form')
);
*/
$meta_sql = " SELECT mta_idx,mta_value,mta_title,mta_number FROM {$g5['meta_table']} 
                WHERE mta_db_table = 'member' 
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                    AND mta_status = 'ok'
                ORDER BY mta_number
";
$mta_result = sql_query($meta_sql,1);
$dsb_arr = array();
$dsb_mns = array();

if($mta_result->num_rows){
    for($m=0;$mta_row=sql_fetch_array($mta_result);$m++){
        array_push($dsb_arr,array('idx'=>$mta_row['mta_idx'],'code'=>$mta_row['mta_value'],'name'=>$mta_row['mta_title'],'sort'=>$mta_row['mta_number']));
    }
    
    for($m=0;$m<count($dsb_arr);$m++){
        array_push($dsb_mns,array($dsb_arr[$m]['code'],$dsb_arr[$m]['name'],G5_USER_ADMIN_URL.'/index.php?idx='.$dsb_arr[$m]['idx'],$dsb_arr[$m]['sort'],$dsb_arr[$m]['idx']));
    }
}

// print_r3($dsb_mns);

$menu915_first = array('915000', '대시보드', ''.G5_USER_ADMIN_URL.'/index.php', 'index');
$menu915_last = array('915900', '<span>대시보드추가</span> <i class="fa fa-plus" aria-hidden="true"></i>', 'javascript:', 'add_dashboard');
$menu915_last_else = array('915900', '<i class="fa fa-slack" aria-hidden="true"></i> DASH-BOARD', ''.G5_USER_ADMIN_URL.'/index.php', 'to_dashboard');
$menu["menu915"] = $dsb_mns;//array();
array_unshift($menu["menu915"], $menu915_first);
if($g5['dir_name'] == 'v10' && preg_match('/^index(\d)*/',$g5['file_name'])){
    array_push($menu["menu915"], $menu915_last);
} else {
    if(count($menu['menu915']) == 1){
        array_push($menu["menu915"], $menu915_last_else);
    }
}
?>