<?php
$sub_menu = "922110";
include_once("./_common.php");
//
auth_check($auth[$sub_menu], 'w');

if(!$bom_idx) alert('제품을 선택해 주세요.');
if(!$prd_start_date) alert('생산시작일을 입력해 주세요.');
if(!$prd_count) alert('지시수량을 입력해 주세요.');

$prd_count = preg_replace("/,/","",$prd_count);
if($prd_memo) $prd_memo = trim(stripslashes($prd_memo));

if($w == 'u'){
    foreach($mms_idxs as $k => $v){
        if(!$v){
            alert('설비를 선택해 주세요.');
        }
    }

    foreach($pri_values as $k => $v){
        if(!$v){
            alert('지시량을 입력해 주세요.');
        }
        $pri_values[$k] = preg_replace("/,/","",$v);
    }

    foreach($pri_memos as $k => $v){
        if($pri_memos) $pri_memos[$k] = trim(stripslashes($v));
    }

    foreach($pri_statuss as $k => $v){
        if(!$v){
            alert('상태값을 선택해 주세요.');
        }
    }
    
    // print_r2($prd_idx);
    // exit;
}

if($w == ''){
    //동일한 조건의 생산계획이 존재하는지 확인하고 있으면 등록거부
    $chk_sql = " SELECT COUNT(*) AS cnt, prd.prd_idx, pri_idx FROM {$g5['production_table']} prd
                    LEFT JOIN {$g5['production_item_table']} pri ON prd.prd_idx = pri.prd_idx
                    LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
                WHERE pri.bom_idx = '{$bom_idx}'
                    AND prd_start_date = '{$prd_start_date}'
                    AND prd_status NOT IN ('trash','delete');
    ";
    // echo $chk_sql;exit;
    $old_prd = sql_fetch($chk_sql);
    if($old_prd['cnt']){
        alert('동일한 조건의 생산계획이 이미 존재합니다.\\n생산계획ID:'.$old_prd['prd_idx'].' 입니다.\\n해당 ID의 데이터를 수정해 주세요.','./production_list.php?sfl=prd.prd_idx&stx='.$old_prd['prd_idx']);
    }

    //먼저 order_practice 데이터부터 등록한다.
    $sql = " INSERT INTO {$g5['production_table']} SET
                com_idx = '{$_SESSION['ss_com_idx']}'
                , ori_idx = '{$ori_idx}'
                , bom_idx = '{$bom_idx}'
                , prd_order_no = '{$prd_order_no}'
                , prd_start_date = '{$prd_start_date}'
                , prd_done_date = '{$prd_done_date}'
                , prd_memo = '{$prd_memo}'
                , prd_status = '{$prd_status}'
                , prd_reg_dt = '".G5_TIME_YMDHIS."'
                , prd_update_dt = '".G5_TIME_YMDHIS."'
    ";

    sql_query($sql,1);
    $prd_idx = sql_insert_id();

    //해당 완제품의 설비번호를 확인하자
    $pmms = sql_fetch(" SELECT mms_idx FROM {$g5['bom_jig_table']} WHERE bom_idx = '{$bom_idx}' AND boj_status = 'ok' LIMIT 1 ");
    //우선 완제품의 생산아이템을 등록하자
    $itm_sql = " INSERT INTO {$g5['production_item_table']} SET
                    prd_idx = '{$prd_idx}'
                    , bom_idx = '{$bom_idx}'
                    , mms_idx = '{$pmms['mms_idx']}'
                    , trm_idx_operation = '0'
                    , trm_idx_line = '0'
                    , pri_value = '{$prd_count}'
                    , pri_memo = ''
                    , pri_status = 'confirm'
                    , pri_reg_dt = '".G5_TIME_YMDHIS."'
                    , pri_update_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($itm_sql,1);
    //해당 완제품의 하위제품들의 정보를 생산아이템이 등록하자
    $sql1 = " SELECT bom.bom_idx, bit.bit_count
                        FROM {$g5['bom_item_table']} AS bit
                            LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                        WHERE bit.bom_idx = '".$bom_idx."'
                        ORDER BY bit.bit_num DESC, bit.bit_reply
                ";
    $res = sql_query($sql1,1);
    if($res->num_rows){
        for($i=0;$row=sql_fetch_array($res);$i++){
            //해당 완제품의 설비번호를 확인하자
            $smms = sql_fetch(" SELECT mms_idx FROM {$g5['bom_jig_table']} WHERE bom_idx = '{$row['bom_idx']}' AND boj_status = 'ok' LIMIT 1 ");
            $sub_count = $prd_count * $row['bit_count'];
            $sub_sql = " INSERT INTO {$g5['production_item_table']} SET
                            prd_idx = '{$prd_idx}'
                            , bom_idx = '{$row['bom_idx']}'
                            , mms_idx = '{$smms['mms_idx']}'
                            , trm_idx_operation = '0'
                            , trm_idx_line = '0'
                            , pri_value = '{$sub_count}'
                            , pri_memo = ''
                            , pri_status = 'confirm'
                            , pri_reg_dt = '".G5_TIME_YMDHIS."'
                            , pri_update_dt = '".G5_TIME_YMDHIS."'
            ";
            sql_query($sub_sql,1);
        }
    }
}
else if($w == 'u'){
    $sql = " UPDATE {$g5['production_table']} SET
                ori_idx = '{$ori_idx}'
                , prd_start_date = '{$prd_start_date}'
                , prd_done_date = '{$prd_done_date}'
                , prd_memo = '{$prd_memo}'
                , prd_status = '{$prd_status}'
                , prd_update_dt = '".G5_TIME_YMDHIS."'
            WHERE prd_idx = '{$prd_idx}'
    ";
    sql_query($sql,1);

    foreach($mms_idxs as $k => $v){
        $sql = " UPDATE {$g5['production_item_table']} SET
                    mms_idx = '{$v}'
                    , pri_value = '{$pri_values[$k]}'
                    , pri_memo = '{$pri_memos[$k]}'
                    , pri_status = '{$pri_statuss[$k]}'
                WHERE pri_idx = '{$k}'
        ";
        sql_query($sql,1);
    }
}


goto_url('./production_form.php?'.$qstr.'&w=u&prd_idx='.$prd_idx, false);