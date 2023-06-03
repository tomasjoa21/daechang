<?php
$sub_menu = "922130";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

auth_check($auth[$sub_menu], 'w');

check_admin_token();

foreach($_POST['chk'] as $bom_idx_v) {
    if(!$input_cnt[$bom_idx_v]){
        alert('입고량을 입력해 주세요.');
    }

    $input_cnt[$bom_idx_v] = preg_replace("/,/","",$input_cnt[$bom_idx_v]);
}


foreach($_POST['chk'] as $bom_idx_v){
    $mtr_sql = " INSERT INTO {$g5['material_table']}
        (com_idx, cst_idx_provider, cst_idx_customer, bom_idx, mtr_name, mtr_part_no, mtr_price, mtr_value, mtr_date, mtr_type, mtr_status, mtr_auth_dt, mtr_reg_dt, mtr_update_dt) VALUES
    ";
    for($i=0;$i<$input_cnt[$bom_idx_v];$i++){
        $mtr_sql .= ($i==0) ? '':',';
        $mtr_sql .= "('{$_SESSION['ss_com_idx']}','{$cst_idx_provider[$bom_idx_v]}','{$cst_idx_customer[$bom_idx_v]}','{$bom_idx_v}','{$bom_name[$bom_idx_v]}','{$bom_part_no[$bom_idx_v]}','{$bom_price[$bom_idx_v]}','1','".G5_TIME_YMD."','material','ok','".G5_TIME_YMDHIS."','".G5_TIME_YMDHIS."','".G5_TIME_YMDHIS."')";
    }
    // echo $mtr_sql;
    sql_query($mtr_sql,1);
}
// exit;
goto_url('./material_input_list.php?'.$qstr);