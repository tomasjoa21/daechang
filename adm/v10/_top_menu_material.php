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

$g5['container_sub_title'] = '
<h2 id="container_sub_title">
    '.$sub_title_list.'
    <a href="./material_stock_list.php" class="btn_top_menu '.$active_material_stock_list.'">자재관리</a>
    <a href="./material_list.php" class="btn_top_menu '.$active_material_list.'">자재현황</a>
    <a href="./material_order_input_list.php" class="btn_top_menu '.$active_material_order_input_list.'">발주제품별입고</a>
    <a href="./material_input_list.php" class="btn_top_menu '.$active_material_input_list.'">자재별입고</a>
</h2>
';
?>
