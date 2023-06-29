<?php
$sub_menu = "922110";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

auth_check($auth[$sub_menu], 'w');

check_admin_token();

$cst_idx = $cst_idx_provider[$_POST['chk'][0]];
$mto_input_date = get_dayAddDate(G5_TIME_YMD,3);
$mto_price = 0;

foreach($_POST['chk'] as $bom_idx_v){
    if(!$moi_count[$bom_idx_v]){
        alert('발주량을 입력해 주세요.');
    }
    if(!$cst_idx_provider[$bom_idx_v]){
        alert('공급업체idx가 제대로 넘어오지 않았습니다.');
    }
    $moi_count[$bom_idx_v] = preg_replace("/,/","",$moi_count[$bom_idx_v]);
    $mto_price += $bom_price[$bom_idx_v];

    $chk_moi = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['material_order_item_table']}
        WHERE bom_idx = '{$bom_idx_v}'
            AND moi_input_date = '{$mto_input_date}'
            AND moi_location = 'a'
            AND moi_type = 'normal'
            AND moi_status != 'trash'
    ");

    if($chk_moi['cnt']){
        alert('동일한 제품이 동일한 납기장소/발주유형/입고예정일로 발주되어 있습니다.');
    }
}

$mto_sql = " INSERT INTO {$g5['material_order_table']}
                SET com_idx = '{$_SESSION['ss_com_idx']}'
                    , cst_idx = '{$cst_idx}'
                    , mb_id = '{$member['mb_id']}'
                    , mto_price = '{$mto_price}'
                    , mto_input_date = '{$mto_input_date}'
                    , mto_location = 'a'
                    , mto_type = 'normal'
                    , mto_status = 'ok'
                    , mto_reg_dt = '".G5_TIME_YMDHIS."'
                    , mto_update_dt = '".G5_TIME_YMDHIS."'
";
sql_query($mto_sql,1);
$mto_idx = sql_insert_id();

foreach($_POST['chk'] as $bom_idx_v){
    $bom = sql_fetch(" SELECT bom_stock_check_yn FROM {$g5['bom_table']} WHERE bom_idx = '{$bom_idx_v}' ");
    $bom_stock_check_yn = $bom['bom_stock_check_yn'];
    $moi_checked_yn = ($bom_stock_check_yn) ? '0' : '1';
    $moi_sql = " INSERT INTO {$g5['material_order_item_table']}
                    SET mto_idx = '{$mto_idx}'
                        , bom_idx = '{$bom_idx_v}'
                        , moi_count = '{$moi_count[$bom_idx_v]}'
                        , moi_price = '{$bom_price[$bom_idx_v]}'
                        , moi_input_date = '{$mto_input_date}'
                        , mb_id_check = '{$moi_checked_yn}'
                        , moi_status = 'pending'
                        , moi_reg_dt = '".G5_TIME_YMDHIS."'
                        , moi_update_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($moi_sql,1);
}

goto_url('./predict_amount_list.php?'.$qstr);