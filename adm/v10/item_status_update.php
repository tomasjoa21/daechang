<?php
$sub_menu = "922145";
include_once('./_common.php');

// print_r2($auth);exit;
check_demo();
auth_check($auth[$sub_menu], 'w');

// check_admin_token();

/*
$_POST['sst'] => orp_start_date
$_POST['sod'] => desc
$_POST['sst2'] => , oop.oop_idx
$_POST['sod2'] => desc
$_POST['sfl'] => mtr_name
$_POST['stx'] => 
$_POST['page'] => 1
$_POST['token'] => 59606c5a9c45a68bfa3184157dae7800
$_POST['oop_idx'] => 44
$_POST['bom_part_no'] => 2004340
$_POST['bom_idx'] => 1034
$_POST['bom_name'] => 외륜
$_POST['plus_modify'] => plus / modify
$_POST['from_status'] => stock
$_POST['to_status'] => finish
$_POST['count'] => 20
*/
// print_r2($_POST);
// echo $is_admin;
// exit;
if(!$pri_idx || !$bom_part_no || !$bom_idx || !$bom_name){alert('생산계획을 선택해 주세요.');}
if($plus_modify == 'modify'){
    if(!$from_status){alert('기존상태값을 선택해 주세요.');}
    if(!$to_status){alert('목표상태값을 선택해 주세요.');}
}
else{
    if(!$to_status){alert('목표상태값을 선택해 주세요.');}
}
if(!$count){alert('갯수를 입력해 주세요.');}
/*
$_POST['pri_idx'] => 44
$_POST['bom_part_no'] => 2004340
$_POST['bom_idx'] => 1034
$_POST['bom_name'] => 외륜
$_POST['plus_modify'] => plus / modify
$_POST['from_status'] => stock
$_POST['to_status'] => finish
$_POST['count'] => 20

$_POST['forge_mms_idx']
$_POST['itm_weight']
$_POST['itm_heat']

$error_search = (preg_match('/^error_/', $_POST['itm_status'][$itm_idx_v])) ? ", itm_defect = '1', itm_defect_type = '".$g5['set_itm_status_ng2_reverse'][$_POST['itm_status'][$itm_idx_v]]."' " : ", itm_defect = '0', itm_defect_type = '0' ";
$delivery_search = ($_POST['itm_status'][$itm_idx_v] == 'delivery') ? ", itm_delivery = '1' " : ", itm_delivery = '0' ";

*/
// print_r2($_POST);exit;
if($to_status == 'delivery'){
    $itm_delivery_dt = ($itm_delivery_dt != '0000-00-00 00:00:00') ? $itm_delivery_dt : $itm_update_dt;
}
$itm_delivery_dt2 = ($to_status == 'delivery') ? $itm_delivery_dt : '0000-00-00 00:00:00';
$itm_defect_type = (preg_match('/^error_/', $to_status)) ? preg_replace('/^error_/','',$to_status) : '';
$to_status = (preg_match('/^error_/', $to_status)) ? 'defect' : $to_status;



if($plus_modify == 'plus'){

    $sql = " INSERT INTO {$g5['item_table']}
    (com_idx,mms_idx,bom_idx,prd_idx,pri_idx,itm_part_no,itm_name,itm_defect_type,itm_delivery_dt,itm_status,itm_date,itm_reg_dt,itm_update_dt) VALUES ";
    $vals = " ('{$_SESSION['ss_com_idx']}','{$mms_idx}','{$bom_idx}','{$prd_idx}','{$pri_idx}','{$bom_part_no}','{$bom_name}','{$itm_defect_type}','{$itm_delivery_dt2}','{$to_status}','".$itm_date."','".$itm_reg_dt."','".$itm_update_dt."') ";
    for($i=0;$i<$count;$i++){
        $sql .= ($i==0)?$vals:','.$vals;
    }
}
else if($plus_modify == 'modify'){
    $defect_where = '';
    
    if(preg_match('/^error_/', $from_status)){
        $from_status = preg_replace('/^error_/','',$from_status);
        $defect_where .= " AND itm_defect_type = '{$from_status}'  ";
    }
    else{
        $defect_where .= " AND itm_status = '{$from_status}' ";
    }

    $condition = " WHERE com_idx = '{$_SESSION['ss_com_idx']}' 
                    AND pri_idx = '{$pri_idx}'
                    AND itm_part_no = '{$bom_part_no}'
                    AND bom_idx = '{$bom_idx}'
                    {$defect_where} ";
                    
    //변경할 기존 절단재 재고가 있는지 확인
    $exist_sql = " SELECT COUNT(*) AS cnt FROM {$g5['item_table']} {$condition} ";
    // echo $exist_sql;exit;
    $exist = sql_fetch($exist_sql);

    if(!$exist['cnt']) 
        alert('변경할 재고데이터가 없습니다.');

    $mod_cnt = ($exist['cnt'] < $count) ? $exist['cnt'] : $count;


    $sql = " UPDATE {$g5['item_table']} SET
            itm_defect_type = '{$itm_defect_type}'
            , itm_delivery_dt = '{$itm_delivery_dt2}'
            , itm_status = '{$to_status}'
        {$condition} 
        LIMIT {$mod_cnt}
    ";
    // echo $sql;exit;
}
sql_query($sql,1);

// $qstr .= '&forge_mms_idx='.$forge_mms_idx; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./item_status_list.php?'.$qstr);