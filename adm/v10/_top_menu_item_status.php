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
    <a href="./pallet_today_list.php" class="btn_top_menu '.$active_pallet_today_list.'" style="display:none;">빠레트조회</a>
    <a href="./item_worker_today_list.php" class="btn_top_menu '.$active_item_worker_today_list.'">작업자별현황</a>
    <a href="./item_customer_today_list.php" class="btn_top_menu '.$active_item_customer_today_list.'">고객사별현황</a>
    <a href="./item_today_list.php" class="btn_top_menu '.$active_item_today_list.'">생산현황</a>
</h2>
';
?>
