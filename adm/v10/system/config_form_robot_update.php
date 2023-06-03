<?php
$sub_menu = "925140";
include_once('./_common.php');

// print_r2($_REQUEST);
// exit;
auth_check($auth[$sub_menu], 'w');

check_admin_token();

$fields = sql_field_names('g5_1_robot_setup');

for ($i=0; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k = $_POST['chk'][$i];

    // 쿼리
    $skips = array('rst_idx','rst_time','rst_robot_no','rst_type','rst_enabled','rst_sleep_time');
    for($j=0;$j<sizeof($fields);$j++) {
        if(in_array($fields[$j],$skips)) {continue;}
        $sql_commons[$i][] = " ".$fields[$j]." = '".$_POST[$fields[$j]][$k]."' ";
    }
    $sql_common[$i] = (is_array($sql_commons[$i])) ? implode(",",$sql_commons[$i]) : '';

    $sql = "UPDATE g5_1_robot_setup SET
                {$sql_common[$i]}
                , rst_sleep_time = '".$_POST['rst_sleep_time'.$_POST['rst_robot_no'][$k]]."'
            WHERE rst_idx = '".$_POST['rst_idx'][$k]."'
    ";
    // echo $sql.'<br><br>';
    sql_query($sql,1);

}


// exit;
$qstr .= '&sca='.$sca.'&ser_rst_type='.$ser_rst_type; // 추가로 확장해서 넘겨야 할 변수들
goto_url('./config_form_robot.php?'.$qstr);
?>
