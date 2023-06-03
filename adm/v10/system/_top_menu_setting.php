<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

${'active_'.$g5['file_name']} = ' btn_top_menu_active';

$g5['container_sub_title'] = '
<h2 id="container_sub_title">
	<a href="./config_form.php" class="btn_top_menu '.$active_config_form.'">설비관리설정</a>
	<a href="./offwork_list.php" class="btn_top_menu '.$active_offwork_list.'">계획정지관리</a>
	<a href="./manual_downtime_list.php" class="btn_top_menu '.$active_manual_downtime_list.'">비가동관리</a>
</h2>
';