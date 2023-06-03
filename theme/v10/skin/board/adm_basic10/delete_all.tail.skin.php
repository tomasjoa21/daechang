<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

delete_cache_latest($bo_table);

$redirect_url = G5_USER_ADMIN_URL.'/bbs/board.php?bo_table='.$bo_table.'&amp;page='.$page.$qstr;
goto_url($redirect_url);
