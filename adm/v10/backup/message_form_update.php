<?php
$sub_menu = "950300";
include_once('./_common.php');

if ($w == 'u' || $w == 'd')
    check_demo();

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

$msg_idx = isset($_POST['msg_idx']) ? (int) $_POST['msg_idx'] : 0;
$msg_subject = isset($_POST['msg_subject']) ? strip_tags(clean_xss_attributes($_POST['msg_subject'])) : '';
$msg_content = isset($_POST['msg_content']) ? $_POST['msg_content'] : '';

$msg_content = ($_POST['msg_type']=='hp') ? $_POST['msg_hp_content'] : $_POST['msg_content'];

if ($w == '')
{
    $sql = " insert {$g5['message_table']}
                set msg_subject = '{$msg_subject}',
                     msg_type = '{$msg_type}',
                     msg_content = '{$msg_content}',
                     msg_time = '".G5_TIME_YMDHIS."',
                     msg_ip = '{$_SERVER['REMOTE_ADDR']}' ";
    sql_query($sql);
}
else if ($w == 'u')
{
    $sql = " update {$g5['message_table']}
                set msg_subject = '{$msg_subject}',
                     msg_type = '{$msg_type}',
                     msg_content = '{$msg_content}',
                     msg_time = '".G5_TIME_YMDHIS."',
                     msg_ip = '{$_SERVER['REMOTE_ADDR']}'
                where msg_idx = '{$msg_idx}' ";
    sql_query($sql);
}
else if ($w == 'd')
{
	$sql = " delete from {$g5['message_table']} where msg_idx = '{$msg_idx}' ";
    sql_query($sql);
}

goto_url('./message_list.php');