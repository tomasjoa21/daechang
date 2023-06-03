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
} else if ($_POST['act_button'] == "선택생산계획"){
    // print_r2($chk);exit;
    //ori_idx[],bom_idx[],ori_count[],shp_dt[],prd_date[]
    //기존에 등록된 생산계획데이터가 있으면 뒤로가기.
    foreach($chk as $k=>$v){
        $shp = sql_fetch(" SELECT COUNT(*) AS cnt, prd.prd_idx 
                FROM {$g5['production_table']} prd
                    LEFT JOIN {$g5['bom_table']} bom ON prd.bom_idx = bom.bom_idx
                WHERE prd.bom_idx = '{$bom_idx[$v]}'
                    AND ori_idx = '{$ori_idx[$v]}'
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
        // production 업데이트
        $ar['table']  = 'g5_1_production';
        $ar['com_idx']  = $_SESSION['ss_com_idx'];
        $ar['ori_idx']  = $ori_idx[$v];
        $ar['bom_idx']  = $bom_idx[$v];
        $ar['prd_order_no']  = $prd_order_no;
        $ar['prd_start_date']  = $prd_date[$v];
        $ar['prd_status']  = 'confirm';

        $prd_idx = update_db($ar);
        unset($ar);

        $prd = get_table('production','prd_idx',$prd_idx);
        $prd['prd_value'] = $ori_count[$v];

        // 생산아이템이 없으면 생성
        $sql = " SELECT * FROM {$g5['production_item_table']} WHERE prd_idx = '".$prd['prd_idx']."' ";
        $rs = sql_query($sql,1);
        $row['rows'] = sql_num_rows($rs);
        // 구성품이 없는 경우는 BOM 구조를 따라서 생성
        if(!$row['rows']) {
            $list = get_production_item($prd);
            // print_r3($list);
        }
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

if($ser_cst_idx){
    $qstr .= '&ser_cst_idx='.$ser_cst_idx;
}
if($st_date){
    $qstr .= '&st_date='.$st_date;
}
if($en_date){
    $qstr .= '&en_date='.$en_date;
}
if($cat){
    $qstr .= '&cat='.$cat;
}

// exit;
goto_url('./order_list.php?'.$qstr);
?>
