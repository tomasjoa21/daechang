<?php
$sub_menu = "918110";
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

// print_r2($_POST);
// exit;
auth_check($auth[$sub_menu], 'w');

check_admin_token();

if ($_POST['act_button'] == "선택수정") {

    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // 천단위 제거
        $_POST['ori_price'][$k] = preg_replace("/,/","",$_POST['ori_price'][$k]);
        $_POST['ori_lead_time'][$k] = preg_replace("/,/","",$_POST['ori_lead_time'][$k]);
        $_POST['ori_min_cnt'][$k] = preg_replace("/,/","",$_POST['ori_min_cnt'][$k]);

        $sql = "UPDATE {$g5['order_item_table']} SET
                    ori_name = '".sql_real_escape_string($_POST['ori_name'][$k])."',
                    ori_price = '".$_POST['ori_price'][$k]."',
                    ori_lead_time = '".$_POST['ori_lead_time'][$k]."',
                    ori_min_cnt = '".$_POST['ori_min_cnt'][$k]."'
                WHERE ori_idx = '".$_POST['ori_idx'][$k]."'
        ";
        // echo $sql.'<br>';
        sql_query($sql,1);

    }

} else if ($_POST['act_button'] == "선택삭제") {
    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // 
        $sql = "UPDATE {$g5['order_item_table']} SET
                    ori_status = 'trash'
                    , ori_memo = CONCAT(ori_memo,'\n삭제 by ".$member['mb_name'].", ".G5_TIME_YMDHIS."')
                WHERE ori_idx = '".$_POST['ori_idx'][$k]."'
        ";
        // echo $sql.'<br>';
        sql_query($sql,1);
    }
} else if ($_POST['act_button'] == "선택출하"){
    //ori_idx[],cst_idx[],ori_count[],shp_dt[]
    //기존에 등록된 출하데이터가 있으면 뒤로가기.
    foreach($chk as $k=>$v){
        $shp = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['shipment_table']}
                WHERE ori_idx = '{$ori_idx[$v]}'
                    AND shp_status NOT IN ('trash','delete')
        ");
        if($shp['cnt'])
            alert('수주ID ['.$ori_idx[$v].']번으로 이미 등록된 출하데이터가 있습니다.');
    }

    foreach ($chk as $k=>$v) {
        $sql = " INSERT INTO {$g5['shipment_table']}
                SET com_idx = '{$_SESSION['ss_com_idx']}'
                    , cst_idx = '{$cst_idx[$v]}'
                    , ori_idx = '{$ori_idx[$v]}'
                    , shp_count = '{$ori_count[$v]}'
                    , shp_dt = '{$shp_dt[$v]}'
                    , shp_status = 'pending'
                    , shp_reg_dt = '".G5_TIME_YMDHIS."'
                    , shp_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
    }
} else if ($_POST['act_button'] == "선택생산"){
    //ori_idx[],bom_idx[],ori_count[],shp_dt[],prd_date[]
    //기존에 등록된 생산계획데이터가 있으면 뒤로가기.
    foreach($chk as $k=>$v){
        $shp = sql_fetch(" SELECT COUNT(*) AS cnt, prd.prd_idx 
                FROM {$g5['production_table']} prd
                    LEFT JOIN {$g5['bom_table']} bom ON prd.bom_idx = bom.bom_idx
                WHERE prd.bom_idx = '{$bom_idx[$v]}'
                    AND prd_start_date = '{$prd_date[$v]}'
                    AND prd_status NOT IN ('trash','delete')
        ");
        if($shp['cnt']){
            // alert('동일한 조건의 생산계획이 이미 존재합니다.\\n생산계획ID:'.$shp['prd_idx'].' 입니다.\\n해당 ID의 데이터를 수정해 주세요.','./production_list.php?sfl=prd.prd_idx&stx='.$old_prd['prd_idx']);p
            alert('동일한 조건의 생산계획이 이미 존재합니다.\\n생산계획ID:'.$shp['prd_idx'].' 입니다.\\n해당 생산계획ID의 데이터를 수정해 주세요.');
        }
    }

    foreach($chk as $k=>$v) {
        //먼저 production_table 데이터부터 등록한다.
        $prd_order_no = "PRD-".strtoupper(wdg_uniqid());
        $sql = " INSERT INTO {$g5['production_table']} SET
            com_idx = '{$_SESSION['ss_com_idx']}'
            , ori_idx = '{$ori_idx[$v]}'
            , bom_idx = '{$bom_idx[$v]}'
            , prd_order_no = '{$prd_order_no}'
            , prd_start_date = '{$prd_date[$v]}'
            , prd_status = 'confirm'
            , prd_reg_dt = '".G5_TIME_YMDHIS."'
            , prd_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $prd_idx = sql_insert_id();

        //해당 완제품의 설비번호를 확인하자
        $pmms = sql_fetch(" SELECT mms_idx FROM {$g5['bom_jig_table']} WHERE bom_idx = '{$bom_idx[$v]}' AND boj_status = 'ok' LIMIT 1 ");
        //우선 완제품의 생산아이템을 등록하자
        $itm_sql = " INSERT INTO {$g5['production_item_table']} SET
                        prd_idx = '{$prd_idx}'
                        , bom_idx = '{$bom_idx[$v]}'
                        , mms_idx = '{$pmms['mms_idx']}'
                        , pri_value = '{$ori_count[$v]}'
                        , pri_status = 'confirm'
                        , pri_reg_dt = '".G5_TIME_YMDHIS."'
                        , pri_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($itm_sql,1);
        //해당 완제품의 하위제품들의 정보를 생산아이템이 등록하자
        $sql1 = " SELECT bom.bom_idx, bit.bit_count
            FROM {$g5['bom_item_table']} AS bit
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
            WHERE bit.bom_idx = '{$bom_idx[$v]}'
            ORDER BY bit.bit_reply
        ";
        $res = sql_query($sql1,1);
        if($res->num_rows){
            for($i=0;$row=sql_fetch_array($res);$i++){
                //해당 제품의 설비번호를 확인하자
                $smms = sql_fetch(" SELECT mms_idx FROM {$g5['bom_jig_table']} WHERE bom_idx = '{$row['bom_idx']}' AND boj_status = 'ok' LIMIT 1 ");
                $sub_count = $ori_count[$v] * $row['bit_count'];
                $sub_sql = " INSERT INTO {$g5['production_item_table']} SET
                                prd_idx = '{$prd_idx}'
                                , bom_idx = '{$row['bom_idx']}'
                                , mms_idx = '{$smms['mms_idx']}'
                                , pri_value = '{$sub_count}'
                                , pri_status = 'confirm'
                                , pri_reg_dt = '".G5_TIME_YMDHIS."'
                                , pri_update_dt = '".G5_TIME_YMDHIS."'
                ";
                sql_query($sub_sql,1);
            }
        }
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

// exit;
$qstr .= '&ser_cst_idx='.$ser_cst_idx.'&st_date='.$st_date.'&en_date='.$en_date;
goto_url('./order_list.php?'.$qstr);
?>
