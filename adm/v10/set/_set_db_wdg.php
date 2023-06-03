<?php
// include_once("./_common.php");

/*
$g5['wdg_config_table']             = USER_TABLE_PREFIX . 'wdg_config';
$g5['wdg_file_table']         		= USER_TABLE_PREFIX . 'wdg_file';
$g5['wdg_content_table']            = USER_TABLE_PREFIX . 'wdg_content';
$g5['wdg_content_extend_table']     = USER_TABLE_PREFIX . 'wdg_content_extend';
$g5['wdg_option_table']             = USER_TABLE_PREFIX . 'wdg_option';
$g5['wdg_user_option_table']        = USER_TABLE_PREFIX . 'wdg_user_option';
*/
//DB에 위젯테이블(wdg)이 존재하는지 확인하고 없으면 설치
$result_wdg = @sql_query(" DESC ".$g5['wdg_table']." ", false);
if(!$result_wdg){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg.php');
}

//DB에 위젯환경설정테이블(wdg_config)이 존재하는지 확인하고 없으면 설치
$result_wdg_config = @sql_query(" DESC ".$g5['wdg_config_table']." ", false);
if(!$result_wdg_config){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_config.php');
}

//DB에 위젯첨부파일설정테이블(wdg_file)이 존재하는지 확인하고 없으면 설치
$result_wdg_file = @sql_query(" DESC ".$g5['wdg_file_table']." ", false);
if(!$result_wdg_file){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_file.php');
}

//DB에 위젯내용설정테이블(wdg_content)이 존재하는지 확인하고 없으면 설치
$result_wdg_content = @sql_query(" DESC ".$g5['wdg_content_table']." ", false);
if(!$result_wdg_content){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_content.php');
}

//DB에 위젯내용의 확장설정테이블(wdg_content_extend)이 존재하는지 확인하고 없으면 설치
$result_wdg_extend = @sql_query(" DESC ".$g5['wdg_content_extend_table']." ", false);
if(!$result_wdg_extend){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_content_extend.php');
}

//DB에 위젯옵션설정테이블(wdg_option)이 존재하는지 확인하고 없으면 설치
$result_wdg_option = @sql_query(" DESC ".$g5['wdg_option_table']." ", false);
if(!$result_wdg_option){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_option.php');
}

//DB에 위젯사용자옵션설정테이블(wdg_user_option)이 존재하는지 확인하고 없으면 설치
$result_wdg_user_option = @sql_query(" DESC ".$g5['wdg_user_option_table']." ", false);
if(!$result_wdg_user_option){
	// 테이블 생성 ------------------------------------
	include_once(G5_USER_ADMIN_SET_PATH.'/set_db_wdg_user_option.php');
}