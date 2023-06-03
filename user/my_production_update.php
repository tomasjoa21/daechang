<?php
include_once('./_common.php');

//print_r2($_POST);
/*
$mb_id
$prm_pair_code
$pri_idxs
$act_button
*/
if(!$mb_id)
    alert('You must select a worker.');

if(!$pri_idxs)
    alert('You need to select a product to work on.');

$pris_arr = explode(',',$pri_idxs);
$total_pris_arr = explode(',',$total_pri_idxs);
$cnt = count($pris_arr);

$act_button = strtolower($act_button);
// $uid = wdg_get_random_string('AZ09',15);

//먼저 기존 데이터가 있는지 확인
$old_sql = " SELECT prm_idx
                , mb_id
                , prm_pair_code
                , pri_idx
                , prm_status
            FROM {$g5['production_member_table']} prm
            WHERE mb_id = '{$mb_id}'
                AND pri_idx IN ({$total_pri_idxs})
                AND prm_update_dt LIKE '".G5_TIME_YMD."%'
";

$old_res = sql_query($old_sql);
$old_cnt = $old_res->num_rows;
$old_arr = array();
for($l=0;$orow=sql_fetch_array($old_res);$l++){
    // array_push($old_arr,$orow);
    $old_arr[$orow['pri_idx']] = array(
        'prm_idx' => $orow['prm_idx']
        , 'mb_id' => $orow['mb_id']
        , 'prm_status' => $orow['prm_status']
    );
}
// print_r2($_POST);
// print_r2($old_arr);
// echo $cnt."<br>";
// echo $old_cnt."<br>";exit;
// exit;
//기존 데이터가 있으면 업데이트 작업을 해야 한다.
if($old_cnt){
    $old = sql_fetch(" SELECT prm_idx
                            , prm_pair_code 
                        FROM {$g5['production_member_table']} prw
                            LEFT JOIN {$g5['production_item_table']} pri ON prw.pri_idx = pri.pri_idx
                        WHERE mb_id = '{$mb_id}'
                            AND mms_idx = '{$mms_idx}'
                            AND prm_update_dt LIKE '".G5_TIME_YMD."%'
                        ORDER BY prm_update_dt DESC LIMIT 1 ");
    // print_r2($old);exit;

    $usql = " UPDATE {$g5['production_member_table']} SET 
                prm_status = 'end'
                , prm_update_dt = DATE_SUB( prm_update_dt, INTERVAL 1 SECOND )
            WHERE mb_id != '{$mb_id}'
                AND prm_status = 'start'
                AND pri_idx IN ({$total_pri_idxs})
                AND prm_update_dt LIKE '".G5_TIME_YMD."%'
    ";
    sql_query($usql);

    //$cnt가 더 클때
    if($cnt > $old_cnt){
        for($i=0;$i<$cnt;$i++){
            //있으면 업데이트
            if(array_key_exists($pris_arr[$i],$old_arr)){
                $usql = " UPDATE {$g5['production_member_table']} SET 
                            prm_status = '{$act_button}'
                            , prm_update_dt = '".G5_TIME_YMDHIS."'
                        WHERE prm_idx = '{$old_arr[$pris_arr[$i]]['prm_idx']}'
                ";
                // echo $usql."<br>";
                sql_query($usql);
            }
            //없으면 INSERT
            else{
                $isql =" INSERT INTO {$g5['production_member_table']} SET 
                        mb_id = '{$mb_id}'
                        , pri_idx = '{$pris_arr[$i]}'
                        , prm_pair_code = '{$old['prm_pair_code']}'
                        , prm_status = '{$act_button}'
                        , prm_reg_dt = '".G5_TIME_YMDHIS."'
                        , prm_update_dt = '".G5_TIME_YMDHIS."'
                ";
                // echo $old['prm_pair_code']."<br>";
                // echo $isql;
                sql_query($isql);
            }
        }
        // exit;
    }
    //$cnt와 같거나 $cnt보다 작을때
    else{
        // print_r2($old_arr);exit;
        foreach($old_arr as $ok => $ov){
            $osql = " SELECT prm_idx
                    , pri_idx
                    , prm_pair_code 
                FROM {$g5['production_member_table']}
                WHERE prm_idx = '{$ov['prm_idx']}'
            ";
            $old = sql_fetch($osql);
            //pri_idx가 있으면 UPDATE
            if(in_array($ok,$pris_arr)){
                $usql = " UPDATE {$g5['production_member_table']} SET 
                            prm_status = '{$act_button}'
                            , prm_update_dt = '".G5_TIME_YMDHIS."'
                        WHERE prm_idx = '{$ov['prm_idx']}'
                ";
                // echo $usql."<br>";
                sql_query($usql, 1);
            }
            //pri_idx가 없으면 DELETE
            else{
                $dsql = " DELETE FROM {$g5['production_member_table']}
                    WHERE prm_idx = '{$ov['prm_idx']}'
                ";
                // echo $dsql."<br>";
                sql_query($dsql, 1);
            }
        }
        // exit;
    }
}
//기존 데이터가 없으면 추가작업을 해야 한다.
else{
    $uid = wdg_get_random_string('AZ09',15);
    // print_r2($_POST);
    // print_r2($pris_arr);exit;
    //혹시라도 오늘 날짜의 이전 작업자의 데이터가 start로 되어 있는것을 전부 end로 변경한다.
    $usql = " UPDATE {$g5['production_member_table']} SET 
                prm_status = 'end'
                , prm_update_dt = DATE_SUB( NOW(), INTERVAL 1 SECOND )
            WHERE mb_id != '{$mb_id}'
                AND prm_status = 'start'
                AND pri_idx IN ({$total_pri_idxs})
                AND prm_update_dt LIKE '".G5_TIME_YMD."%'
    ";
    sql_query($usql);

    for($i=0;$i<count($pris_arr);$i++){
        $isql =" INSERT INTO {$g5['production_member_table']} SET 
                mb_id = '{$mb_id}'
                , pri_idx = '{$pris_arr[$i]}'
                , prm_pair_code = '{$uid}'
                , prm_status = '{$act_button}'
                , prm_reg_dt = '".G5_TIME_YMDHIS."'
                , prm_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($isql);
    }
}
// exit;
goto_url('./my_production.php?mms_idx='.$mms_idx.'&mb_id='.$mb_id, false);