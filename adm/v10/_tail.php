<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 
if($g5['dir_name'] == 'v10' && preg_match('/^index(\.)*/',$g5['file_name'])){
    include_once(G5_USER_ADMIN_PATH.'/_dashboard_menu_javascript.php');
}

//모달관련
if(is_file(G5_USER_ADMIN_MODAL_PATH.'/css/default_modal.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_MODAL_URL.'/css/default_modal.css">',0);

if(is_file(G5_USER_ADMIN_MODAL_PATH.'/css/'.$g5['file_name'].'_modal.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_MODAL_URL.'/css/'.$g5['file_name'].'_modal.css">',0);
@include_once(G5_USER_ADMIN_MODAL_PATH.'/'.$g5['file_name'].'_modal.php');

include_once(G5_USER_ADMIN_PATH.'/admin.tail.php');
?>