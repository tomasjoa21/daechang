<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 

include_once(G5_USER_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
if($g5['dir_name'] == 'v10' && preg_match("/^index[0-9]?/",$g5['file_name'])){
    add_javascript('<script src="https://unpkg.com/packery@2.1/dist/packery.pkgd.min.js"></script>',2); 
    add_javascript('<script src="https://unpkg.com/draggabilly@3/dist/draggabilly.pkgd.min.js"></script>',2);
    include(G5_USER_ADMIN_CSS_PATH.'/packery_index_style.php');
}
?>