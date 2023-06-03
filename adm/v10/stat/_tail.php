<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 

@include_once (G5_USER_ADMIN_STAT_PATH.'/chart/chart_'.$g5['file_name'].'.php');

include_once(G5_USER_ADMIN_PATH.'/admin.tail.php');
?>