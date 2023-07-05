<?php
include_once('./_common.php');
$res = array('ok' => true);
// echo json_encode($_POST);
if(!$mb_id_driver){
    $res['ok'] = false;
    $res['msg'] = '배송자 아이디가 제대로 넘어오지 않았습니다.';
}

if(!$moi_idx){
    $res['ok'] = false;
    $res['msg'] = '발주제품ID번호가 넘어오지 않았습니다.';
}

if(!$moi_count && $w != 'c'){
    $res['ok'] = false;
    $res['msg'] = '발주갯수가 넘어오지 않았습니다.';
}

if($res['ok']){
    //신규등록일때
    if($w == ''){
        //moi_idx번호가 존재하는지 확인
        $chk_sql = " SELECT COUNT(*) AS cnt, bom_idx FROM {$g5['material_order_item_table']} WHERE moi_idx = '{$moi_idx}' AND moi_check_yn = '1' AND moi_status IN ('ok','ready') ";
        $chk = sql_fetch($chk_sql);
        $bom = sql_fetch(" SELECT * FROM {$g5['bom_table']} WHERE bom_idx = '{$chk['bom_idx']}' ");
        //입고처리 불가능할때
        if(!$chk['cnt']){
            $res['ok'] = false;
            $res['msg'] = '검사대기중 또는 입고완료상태일 수 있습니다.';
        }
        //입고처리 가능할때
        else{
            $moi_sql = " UPDATE {$g5['material_order_item_table']} SET
                        mb_id_driver = '{$mb_id_driver}'
                        , moi_history = CONCAT(moi_history,'\ninput|".G5_TIME_YMDHIS."')
                        , moi_status = 'input'
                        , moi_input_dt = '".G5_TIME_YMDHIS."'
                        , moi_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE moi_idx = '{$moi_idx}'
            ";
            sql_query($moi_sql,1);
    
            $mtr_sql = " INSERT INTO {$g5['material_table']}
                (com_idx, cst_idx_provider, cst_idx_customer, bom_idx, moi_idx, mtr_name, mtr_part_no, mtr_price, mtr_value, mtr_date, mtr_type, mtr_status, mtr_auth_dt, mtr_reg_dt, mtr_update_dt) VALUES
            ";

            for($i=0;$i<$moi_count;$i++){
                $mtr_sql .= ($i==0) ? '':',';
                $mtr_sql .= "('{$_SESSION['ss_com_idx']}','{$bom['cst_idx_provider']}','{$bom['cst_idx_customer']}','{$bom['bom_idx']}', '{$moi_idx}','{$bom['bom_name']}','{$bom['bom_part_no']}','{$bom['bom_price']}','1','".G5_TIME_YMD."','material','ok','".G5_TIME_YMDHIS."','".G5_TIME_YMDHIS."','".G5_TIME_YMDHIS."')";
            }
            sql_query($mtr_sql,1);
        }
    }
    //취소일때
    else if($w == 'c'){
        $moi_sql = " UPDATE {$g5['material_order_item_table']} SET
                    mb_id_driver = ''
                    , moi_history = CONCAT(moi_history,'\nready|".G5_TIME_YMDHIS."')
                    , moi_status = 'ready'
                    , moi_input_dt = '0000-00-00 00:00:00'
                    , moi_update_dt = '".G5_TIME_YMDHIS."'
                WHERE moi_idx = '{$moi_idx}'
                    AND mb_id_driver = '{$mb_id_driver}'
        ";
        sql_query($moi_sql,1);

        $mtr_sql = " DELETE FROM {$g5['material_table']} WHERE moi_idx = '{$moi_idx}'
        ";
        sql_query($mtr_sql,1);
    }
}

echo json_encode($res);