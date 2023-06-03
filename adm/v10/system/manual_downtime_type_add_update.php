<?php
include_once('./_common.php');
$downtime_chk = (int)$downtime_chk;
$mst_name = trim($mst_name);
//불량유형 중복체크를 목적으로 접속했을때 (아작스처리)
if($downtime_chk){
    if($mst_name){
        $sql = " SELECT COUNT(*) AS cnt FROM {$g5['mms_status_table']} WHERE mst_name = '{$mst_name}' AND mms_idx = '{$mms_idx}' AND mst_type = 'offwork' AND mst_status NOT IN('delete','del','trash','cancel')  ";
        $res = sql_fetch($sql);
        if($res['cnt']){
            $msg = 'used';
        }
        else {
            $msg = 'ok';
        }
    }
    else{
        $msg = 'empty';
    }
    echo $msg;
}
//새로운 불량유형을 등록하기 위한 목적으로 접속했을때
else{
    if($mst_name) {
        $sql = " SELECT COUNT(*) AS cnt FROM {$g5['mms_status_table']} WHERE mst_name = '{$mst_name}' AND mms_idx = '{$mms_idx}' AND mst_type = 'offwork' AND mst_status NOT IN('delete','del','trash','cancel')  ";
        $res = sql_fetch($sql);
        if($res['cnt']){
            alert('이미 등록된 불량유형입니다.\n다시 확인하시고 등록하시기 바랍니다.');
            $msg = 'overlab';
        }
        else {
            $sql = " INSERT INTO {$g5['mms_status_table']} SET
                       mms_idx = '{$mms_idx}'
                       ,mst_type = 'offwork'
                       ,mst_name = '{$mst_name}'
                       ,mst_memo = '{$mst_name}'
                       ,mst_status = 'ok'
                       ,mst_reg_dt = '".G5_TIME_YMDHIS."'
                       ,mst_update_dt = '".G5_TIME_YMDHIS."'
            ";
            sql_query($sql,1);
            $msg = 'ok';
        }
    }
    else{
        $msg = 'empty';
    }

    echo $msg;
}