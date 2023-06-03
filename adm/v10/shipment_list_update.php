<?php
$sub_menu = "918120";
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
        // echo 'shp_idx: '.$_REQUEST['shp_idx'][$k].BR;
        // echo 'shp_status: '.$_REQUEST['shp_status'][$k].BR;

        // 천단위 제거
        $_POST['shp_count'][$k] = preg_replace("/,/","",$_POST['shp_count'][$k]);

        $sql = "UPDATE {$g5['shipment_table']} SET
                    mb_id = '".$_POST['mb_id'][$k]."',
                    shp_count = '".$_POST['shp_count'][$k]."',
                    shp_status = '".$_POST['shp_status'][$k]."',
                    shp_update_dt = '".G5_TIME_YMDHIS."'
                WHERE shp_idx = '".$_POST['shp_idx'][$k]."'
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
        $sql = "UPDATE {$g5['shipment_table']} SET
                    shp_status = 'trash'
                    , shp_memo = CONCAT(shp_memo,'\n삭제 by ".$member['mb_name'].", ".G5_TIME_YMDHIS."'),
                    shp_update_dt = '".G5_TIME_YMDHIS."'
                WHERE shp_idx = '".$_POST['shp_idx'][$k]."'
        ";
        echo $sql.'<br>';
        sql_query($sql,1);
    }
}

if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

// exit;
$qstr .= '&ser_cst_idx='.$ser_cst_idx.'&st_date='.$st_date.'&en_date='.$en_date;
goto_url('./shipment_list.php?'.$qstr);
?>
