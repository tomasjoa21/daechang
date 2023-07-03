<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

${'active_'.$g5['file_name']} = ' btn_top_menu_active';

// 최고관리자인 경우만
if($member['mb_manager_yn']) {
    // $sub_title_list = ' <a href="./config_form.php" class="btn_top_menu '.$active_config_form.'">솔루션설정</a>
    //                     <a href="./config_schedule.php" class="btn_top_menu '.$active_config_schedule.'">프로젝트일정</a>
    // ';
}
$ex_member_yn = ($member['mb_8'] && $member['mb_8'] != $_SESSION['ss_com_idx']) ? true : false;
$g5['container_sub_title'] = ($ex_member_yn) ? '
<h2 id="container_sub_title">
    '.$sub_title_list.'
    <a href="./material_order_list.php" class="btn_top_menu '.$active_material_order_list.'">발주관리</a>
</h2>' :
'
<h2 id="container_sub_title">
    '.$sub_title_list.'
    <a href="./material_order_list.php" class="btn_top_menu '.$active_material_order_list.'">발주관리</a>
    <a href="./predict_amount_list.php" class="btn_top_menu '.$active_predict_amount_list.'">자재소요량산출</a>
</h2>
';
?>
