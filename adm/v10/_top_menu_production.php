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
    <a href="./production_list.php" class="btn_top_menu '.$active_production_list.'">생산계획</a>
    <a href="./predict_amount_list.php" class="btn_top_menu '.$active_predict_amount_list.'">자재소요량산출</a>
</h2>
';
?>
