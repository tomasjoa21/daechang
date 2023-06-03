<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

${'active_'.$g5['file_name']} = ' btn_top_menu_active';
$g5['container_sub_title'] = '
<h2 id="container_sub_title">
	<a href="./output.php" class="btn_top_menu '.$active_output.'">생산보고서</a>
	<a href="./alarm.php" class="btn_top_menu '.$active_alarm.'">알람보고서</a>
	<a href="./predict.php" class="btn_top_menu '.$active_predict.'">예지보고서</a>
	<a href="./maintain.php" class="btn_top_menu '.$active_maintain.'">정비및재고</a>
	<a href="./repair.php" class="btn_top_menu '.$active_repair.'">조치보고서</a>
	<a href="./uph.php" class="btn_top_menu '.$active_uph.'">UPH보고서</a>
	<a href="./config_form.php" class="btn_top_menu '.$active_config_form.'">통계설정</a>
</h2>
';