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

// production 업데이트
$ar['table']  = 'g5_1_production';
$ar['com_idx']  = $_SESSION['ss_com_idx'];
$ar['ori_idx']  = $ori_idx;
$ar['bom_idx']  = $bom_idx;
$ar['prd_order_no']  = $prd_order_no;
$ar['prd_start_date']  = $prd_start_date;
$ar['prd_done_date']  = $prd_done_date;
$ar['prd_memo']  = $prd_memo;
$ar['prd_status']  = $prd_status;
// print_r2($ar);
$prd_idx = update_db($ar);
unset($ar);
// echo $prd_idx;
$prd = get_table('production','prd_idx',$prd_idx);
$prd['prd_value'] = $prd_count;

// 등록모드일때
if($w == ''){

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
//수정모드일때
else if($w == 'u'){
    // print_r2($_REQUEST);

    if ($_POST['act_button'] == "초기화") {
        // 작업자아이템이 정보 초기화
        $sql = "DELETE FROM {$g5['production_member_table']} 
                WHERE pri_idx IN (SELECT pri_idx FROM {$g5['production_item_table']} WHERE prd_idx = '".$prd_idx."') 
        ";
        // echo $sql.BR;
        sql_query($sql,1);
        // 생산아이템이 정보 초기화
        $sql = " DELETE FROM {$g5['production_item_table']} WHERE prd_idx = '".$prd_idx."' ";
        // echo $sql.BR;
        sql_query($sql,1);

        // BOM 구조를 따라서 관련 정보 생성
        $list = get_production_item($prd);
    }
    // 초기화가 아니면 정보 업데이트
    else {

        if(is_array($_REQUEST['chk'])) {
            foreach($_REQUEST['chk'] as $k1=>$v1)
            {
                // echo $k1.'/'.$v1.BR;
                // echo 'pri_idx: '.$_REQUEST['pri_idxs'][$k1].BR;
                // echo 'prm_idx: '.$_REQUEST['prm_idxs'][$k1].BR;
        
                // 천단위 제거
                $_REQUEST['prm_values'][$k1] = preg_replace("/,/","",$_REQUEST['prm_values'][$k1]);
        
                // 생산아이템 정보 입력 ---------------------------------------------------------------
                $sql3 = "   UPDATE {$g5['production_item_table']} SET
                                pri_status = '".$_REQUEST['prm_statuss'][$k1]."'
                                , pri_update_dt = '".G5_TIME_YMDHIS."'
                            WHERE pri_idx = '".$_REQUEST['pri_idxs'][$k1]."'
                ";
                // echo $sql3.BR;
                sql_query($sql3,1);
                // 작업자아이템 정보 입력 ---------------------------------------------------------------
                $sql3 = "   UPDATE {$g5['production_member_table']} SET
                                mms_idx = '".$_REQUEST['mms_idxs'][$k1]."'
                                , mb_id = '".$_REQUEST['mb_ids'][$k1]."'
                                , prm_value = '".$_REQUEST['prm_values'][$k1]."'
                                , prm_status = '".$_REQUEST['prm_statuss'][$k1]."'
                                , prm_update_dt = '".G5_TIME_YMDHIS."'
                            WHERE prm_idx = '".$_REQUEST['prm_idxs'][$k1]."'
                ";
                // echo $sql3.BR;
                sql_query($sql3,1);

            }
        }
    }

}

// exit;
goto_url('./production_form.php?'.$qstr.'&w=u&prd_idx='.$prd_idx, false);