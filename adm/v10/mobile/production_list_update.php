<?php
include_once('./_common.php');
if(!$call){

    if(!$mms_idx) alert('설비정보가 제대로 넘어오지 않았습니다.');
    if(!$prd_idx) alert('생산계획정보가 제대로 넘어오지 않았습니다.');
    if(!$pri_idx) alert('생산계획제품정보가 제대로 넘어오지 않았습니다.');
    if(!$bom_idx) alert('제품 정보가 제대로 넘어오지 않았습니다.');
    if(!$member['mb_id']) alert('작업자 정보가 제대로 넘어오지 않았습니다.');
    
    // print_r2($_POST);
    
    //시작상태에서 넘어왔으므로 pri_ing = 0 으로 만들어야 한다.
    if($pri_ing){
        $sql = " UPDATE {$g5['production_item_table']} 
                    SET pri_ing = '0'
                        , pri_update_dt = '".G5_TIME_YMDHIS."'
            WHERE pri_idx = '{$pri_idx}'
        ";
        sql_query($sql,1);
    }
    //종료상태에서 넘어왔으므로 pri_ing = 1 으로 만들어야 하는데
    //내가 아닌 다른 작업자가 있으면 나를 '시작'상태로 수정할 수 없다.
    else{
        //동일한 설비와 제품의 내가 아닌 다른 작업작가 작업중인지 확인한다.
        $chk = sql_fetch(" SELECT COUNT(*) cnt, mb_id FROM {$g5['production_item_table']} 
                WHERE prd_idx = '{$prd_idx}'
                    AND bom_idx = '{$bom_idx}'
                    AND mms_idx = '{$mms_idx}'
                    AND mb_id != '{$member['mb_id']}'
                    AND pri_ing = '1'
        ");
        $pre_mb = ($chk['cnt'])?get_member($chk['mb_id']):'';
        if($chk['cnt']){
            alert($pre_mb['mb_name'].'님이 아직 작업을 종료하지 않았습니다.\\n'.$pre_mb['mb_name'].'님 또는 관리자에게 [작업종료]를 요청해 주세요.');
        }
        else{
            $sql = " UPDATE {$g5['production_item_table']} 
                        SET pri_ing = '1'
                            , pri_update_dt = '".G5_TIME_YMDHIS."'
                WHERE pri_idx = '{$pri_idx}'
            ";
            sql_query($sql,1);
        }
    }
}
else if($call){
    if(!$mms_idx) alert('설비정보가 제대로 넘어오지 않았습니다.');
    if(!$prd_idx) alert('생산계획정보가 제대로 넘어오지 않았습니다.');
    if(!$pri_idx) alert('생산계획제품정보가 제대로 넘어오지 않았습니다.');
    if(!$bom_idx) alert('제품 정보가 제대로 넘어오지 않았습니다.');
    if(!$member['mb_id']) alert('작업자 정보가 제대로 넘어오지 않았습니다.');

    $sql = " UPDATE {$g5['mms_table']} 
                SET mms_call_yn = '{$call_yn}' 
                    , mms_update_dt = '".G5_TIME_YMDHIS."'
            WHERE mms_idx = '{$mms_idx}' ";
    sql_query($sql,1);
}

goto_url('./production_list.php?mms_idx='.$mms_idx);