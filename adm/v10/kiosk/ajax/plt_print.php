<?php
include_once('./_common.php');

$res = array("ok" => true);

if(!$mb_id_worker){
    $res['ok'] = false;
    $res['msg'] = '작업자 아이디가 넘어오지 않았습니다.';
}

if(!$mms_idx){
    $res['ok'] = false;
    $res['msg'] = '설비번호가 넘어오지 않았습니다.';
}

$boms = array();
$bom_str_arr = explode(',',$boms_str);
foreach($bom_str_arr as $bsa){
    $bom_array = explode('=',$bsa);
    $boms[$bom_array[0]] = $bom_array[1];
}

if(!count($boms)){
    $res['ok'] = false;
    $res['msg'] = '제품데이터가 넘어오지 않았습니다.';
}

//현 재고량 확인후 적재량보다 재고량이 부족하면 안된다.
foreach($boms as $bk => $bv){
    $sql2 = " SELECT SUM(itm_value) AS itm_total
    FROM {$g5['item_table']}
    WHERE com_idx = '{$_SESSION['ss_com_idx']}'
        AND bom_idx = '{$bk}' 
        AND plt_idx = '0'
        AND itm_status IN ('finish','check')
    ";
    $stk = sql_fetch($sql2);
    if($bv > $stk['itm_total']){
        $res['ok'] = false;
        $res['msg'] = '실제로 적재되지 않은 재고량이 부족합니다.'; 
    }
}
// echo json_encode($res);
// exit;
//신규인쇄/발행
if($res['ok']){
    //신규 plt_idx등록
    $chk_yn = ($plt_status == 'ok') ? '1' : '0';
    $ins_sql = " INSERT INTO {$g5['pallet_table']} SET
                   com_idx = '{$_SESSION['ss_com_idx']}'
                   , mb_id_worker = '{$mb_id_worker}'
                   , mms_idx = '{$mms_idx}'
                   , plt_check_yn = '{$chk_yn}'
                   , plt_status = 'ok'
                   , plt_reg_dt = '".G5_TIME_YMDHIS."'
                   , plt_update_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($ins_sql,1);
    $plt_idx = sql_insert_id();

    //완제품 자재에 갯수만큼 plt_idx정보 저장
    foreach($boms as $bk => $bv){
        $sql = " UPDATE {$g5['item_table']} SET
                    plt_idx = '{$plt_idx}'
                    , itm_update_dt = '".G5_TIME_YMDHIS."'
                WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                    AND plt_idx = '0'
                    AND bom_idx = '{$bk}'
                    AND itm_status = 'finish'
                ORDER BY itm_idx
                LIMIT {$bv}
        ";
        sql_query($sql,1);
    }

    if($plt_idx){
        $res['plt_idx'] = $plt_idx;
        $res['print_dt'] = G5_TIME_YMDHIS;
    }
}


echo json_encode($res,true);