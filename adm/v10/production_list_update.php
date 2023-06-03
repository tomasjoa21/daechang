<?php
$sub_menu = "922110";
include_once('./_common.php');

check_demo();

if (!@count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

// print_r2($_REQUEST);
// exit;

auth_check($auth[$sub_menu], 'w');

check_admin_token();

// print_r2($_POST);exit;
if ($_POST['act_button'] == "선택수정") {
    // print_r2($_POST);exit;
    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $prd = get_table('production','prd_idx',$_POST['prd_idx'][$k]);
        // echo 'prd_idx: '.$_REQUEST['prd_idx'][$k].BR;
        // echo 'prd_start_date: '.$_REQUEST['prd_start_date'][$k].BR;

        $sql = "UPDATE {$g5['production_table']} SET
                    prd_start_date = '".$_POST['prd_start_date'][$k]."',
                    prd_status = '".$_POST['prd_status'][$k]."'
                WHERE prd_idx = '".$_POST['prd_idx'][$k]."'
        ";
        // echo $sql.BR;
        sql_query($sql,1);

    }
}
else if ($_POST['act_button'] == "선택문자전송") {


}
else if ($_POST['act_button'] == "선택삭제") {
    for ($i=0; $i<count($_POST['chk']); $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // 작업자아이템이 정보 초기화
        $sql = "DELETE FROM {$g5['production_item_count_table']} 
                WHERE pri_idx IN (SELECT pri_idx FROM {$g5['production_item_table']} WHERE prd_idx = '".$_POST['prd_idx'][$k]."') 
        ";
        // echo $sql.BR;
        sql_query($sql,1);
        // 생산아이템이 정보 초기화
        $sql = " DELETE FROM {$g5['production_item_table']} WHERE prd_idx = '".$_POST['prd_idx'][$k]."' ";
        // echo $sql.BR;
        sql_query($sql,1);
        // 생산계획 정보 초기화
        $sql = " DELETE FROM {$g5['production_table']} WHERE prd_idx = '".$_POST['prd_idx'][$k]."' ";
        // echo $sql.BR;
        sql_query($sql,1);

    }
}


if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

// exit;
// $qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./production_list.php?'.$qstr);
?>
