<?php
include_once('./_common.php');
$res = array('ok' => true);
// echo json_encode($_POST);
if(!$mb_id_delivery){
    $res['ok'] = false;
    $res['msg'] = '배송자 아이디가 제대로 넘어오지 않았습니다.';
}

if(!$plt_idx){
    $res['ok'] = false;
    $res['msg'] = '파레트번호가 넘어오지 않았습니다.';
}

if($res['ok']){
    //신규등록일때
    if($w == ''){
        //plt_idx번호가 존재하는지 확인
        $chk_sql = " SELECT COUNT(*) AS cnt FROM {$g5['pallet_table']} WHERE plt_idx = '{$plt_idx}' AND plt_check_yn = '1' AND plt_status = 'ok' ";
        $chk = sql_fetch($chk_sql);
        //파레트 출하처리 불가능할때
        if(!$chk['cnt']){
            $res['ok'] = false;
            $res['msg'] = '검사대기중 또는 출하상태인 파레트일 수 있습니다.';
        }
        //파레트 출하처리 가능할때
        else{
            $plt_sql = " UPDATE {$g5['pallet_table']} SET
                        mb_id_delivery = '{$mb_id_delivery}'
                        , plt_history = CONCAT(plt_history,'\ndelivery|".G5_TIME_YMDHIS."')
                        , plt_status = 'delivery'
                        , plt_date = '".G5_TIME_YMD."'
                        , plt_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE plt_idx = '{$plt_idx}'
            ";
            sql_query($plt_sql,1);
    
            $itm_sql = " UPDATE {$g5['item_table']} SET
                        itm_history = CONCAT(itm_history,'\ndelivery|".G5_TIME_YMDHIS."')
                        , itm_status = 'delivery'
                        , itm_delivery_dt = '".G5_TIME_YMDHIS."'
                        , itm_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE plt_idx = '{$plt_idx}'
            ";
            sql_query($itm_sql,1);
        }
    }
    //취소일때
    else if($w == 'c'){
        $plt_sql = " UPDATE {$g5['pallet_table']} SET
                    mb_id_delivery = ''
                    , plt_history = CONCAT(plt_history,'\nok|".G5_TIME_YMDHIS."')
                    , plt_status = 'ok'
                    , plt_date = '0000-00-00'
                    , plt_update_dt = '".G5_TIME_YMDHIS."'
                WHERE plt_idx = '{$plt_idx}'
                    AND mb_id_delivery = '{$mb_id_delivery}'
        ";
        sql_query($plt_sql,1);

        $itm_sql = " UPDATE {$g5['item_table']} SET
                    itm_history = CONCAT(itm_history,'\nfinish|".G5_TIME_YMDHIS."')
                    , itm_status = 'finish'
                    , itm_delivery_dt = '0000-00-00 00:00:00'
                    , itm_update_dt = '".G5_TIME_YMDHIS."'
                WHERE plt_idx = '{$plt_idx}'
        ";
        sql_query($itm_sql,1);
    }
}

echo json_encode($res);