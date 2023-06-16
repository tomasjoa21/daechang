<?php
$sub_menu = "922160";
require_once './_common.php';

check_demo();

auth_check_menu($auth, $sub_menu, 'w');

check_admin_token();

// $chk; $plt_check_yn
foreach($chk as $ck => $cv){
    $chk_yn = ($plt_check_yn[$ck]) ? $plt_check_yn[$ck] : $cv;
    $sql = " UPDATE {$g5['pallet_table']} SET
            plt_check_yn = '{$chk_yn}'
            WHERE plt_idx = '{$ck}'
    ";
    sql_query($sql,1);
}

goto_url('./pallet_list.php?'.$qstr);