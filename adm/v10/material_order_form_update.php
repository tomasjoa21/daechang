<?php
$sub_menu = "922150";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');
// print_r2($_POST);exit;
// 추가로 확장해서 넘겨야 할 변수들
if($mtyp){
    $qstr .= '&mtyp='.$mtyp; 
}
if($sch_from_date){
    $qstr .= '&sch_from_date='.$sch_from_date; 
}
if($sch_to_date){
    $qstr .= '&sch_to_date='.$sch_to_date; 
}

if($mtyp == 'mto'){
    //addslashes($row2['mto_memo'])
    if(!$cst_idx)
        alert('공급업체를 반드시 선택해 주세요.');
    if(!$mto_input_date)
        alert('납기일을 반드시 설정해 주세요.');
    
    $mto_memo = addslashes($mto_memo);
    if($w == ''){
        $mto_sql = " INSERT INTO {$g5['material_order_table']}
                        SET com_idx = '{$_SESSION['ss_com_idx']}'
                            , cst_idx = '{$cst_idx}'
                            , mb_id = '{$member['mb_id']}'
                            , mto_input_date = '{$mto_input_date}'
                            , mto_memo = '{$mto_memo}'
                            , mto_status = '{$mto_status}'
                            , mto_reg_dt = '".G5_TIME_YMDHIS."'
                            , mto_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($mto_sql,1);
        $mto_idx = sql_insert_id();
    }
    else if($w == 'u'){
        $mto_sql = " UPDATE {$g5['material_order_table']}
                SET cst_idx = '{$cst_idx}'
                    , mto_type = '{$mto_type}'
                    , mto_input_date = '{$mto_input_date}'
                    , mto_memo = '{$mto_memo}'
                    , mto_status = '{$mto_status}'
                    , mto_update_dt = '".G5_TIME_YMDHIS."'
            WHERE mto_idx = '{$mto_idx}'
        ";
        sql_query($mto_sql,1);
    }
}
else if($mtyp == 'moi'){
    if(!$mto_idx)
        alert('발주ID를 반드시 선택해 주세요.');
    if(!$bom_idx)
        alert('제품을을 반드시 선택해 주세요.');
    if(!$moi_count)
        alert('발주수량을 반드시 설정해 주세요.');
    if(!$moi_input_date)
        alert('납기일을 반드시 설정해 주세요.');
    
    $bom = sql_fetch(" SELECT * FROM {$g5['bom_table']} WHERE bom_idx = '{$bom_idx}' ");
    $bom_stock_check_yn = $bom['bom_stock_check_yn'];
    
    $moi_count = preg_replace("/,/","",$moi_count);
    $moi_memo = addslashes($moi_memo);
    if($w == ''){
        //동일한 발주ID에 동일한 제품이 존재하면 반려
        $chk_sql = " SELECT COUNT(*) AS cnt FROM {$g5['material_order_item_table']}
                WHERE mto_idx = '{$mto_idx}'
                    AND bom_idx = '{$bom_idx}'
                    AND moi_status NOT IN ('trash','delete');
        ";
        $chk = sql_fetch($chk_sql);

        if($chk['cnt'])
            alert('동일한 발주ID에 동일한 제품이 이미 등록되어 있습니다.');
        $bom = sql_fetch(" SELECT bom_price FROM {$g5['bom_table']} WHERE bom_idx = '{$bom_idx}' ");
        $moi_checked_yn = ($bom_stock_check_yn) ? '0' : '1';
        $moi_sql = " INSERT INTO {$g5['material_order_item_table']}
                        SET mto_idx = '{$mto_idx}'
                            , bom_idx = '{$bom_idx}'
                            , moi_count = '{$moi_count}'
                            , moi_price = '{$bom['bom_price']}'
                            , mb_id_driver = '{$mb_id_driver}'
                            , mb_id_check = '{$mb_id_check}'
                            , moi_input_date = '{$moi_input_date}'
                            , moi_input_dt = '{$moi_input_dt}'
                            , moi_check_yn = '{$moi_checked_yn}'
                            , moi_memo = '{$moi_memo}'
                            , moi_check_text = '{$moi_check_text}'
                            , moi_status = '{$moi_status}'
                            , moi_reg_dt = '".G5_TIME_YMDHIS."'
                            , moi_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($moi_sql,1);
        $moi_idx = sql_insert_id();
    }
    else if($w == 'u'){
        $moi_sql = " UPDATE {$g5['material_order_item_table']}
                        SET moi_count = '{$moi_count}'
                            , mb_id_driver = '{$mb_id_driver}'
                            , mb_id_check = '{$mb_id_check}' 
                            , moi_input_date = '{$moi_input_date}'
                            , moi_input_dt = '{$moi_input_dt}'
                            , moi_check_yn = '{$moi_check_yn}'
                            , moi_memo = '{$moi_memo}'
                            , moi_history = CONCAT(moi_history,'\n{$moi_status}|".G5_TIME_YMDHIS."')
                            , moi_check_text = '{$moi_check_text}'
                            , moi_status = '{$moi_status}'
                            , moi_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE moi_idx = '{$moi_idx}'
        ";
        sql_query($moi_sql,1);
    }
}
//변동사항이 있을 수 있으므로 무조건 mto_price 갱신하자
$moi = sql_fetch(" SELECT SUM(moi_price * moi_count) AS mto_price
                FROM {$g5['material_order_item_table']}
            WHERE mto_idx = '{$mto_idx}'
                AND moi_status IN('pending','ok','used','delivery','scrap')
            GROUP BY mto_idx
");

$sql = " UPDATE {$g5['material_order_table']}
            SET mto_price = '{$moi['mto_price']}'
        WHERE mto_idx = '{$mto_idx}'
";
sql_query($sql,1);

$qstr .= '&w=u&'.$mtyp.'_idx='.${$mtyp.'_idx'};
// echo $qstr;exit;
goto_url('./material_order_form.php?'.$qstr,false);