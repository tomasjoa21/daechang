<?php
$sub_menu = "960100";
include_once("./_common.php");

if(!$member['mb_manager_yn']) {
    alert('접근이 금지된 페이지입니다.');
}


$mb = get_table_meta('member','mb_id',$mb_id);

// 회원아이디 세션 생성
set_session('ss_mb_id', $mb_id);
// FLASH XSS 공격에 대응하기 위하여 회원의 고유키를 생성해 놓는다. 관리자에서 검사함 - 110106
set_session('ss_mb_key', md5($mb['mb_datetime'] . get_real_client_ip() . $_SERVER['HTTP_USER_AGENT']));
// 회원의 토큰키를 세션에 저장한다. /common.php 에서 해당 회원의 토큰값을 검사한다.
if(function_exists('update_auth_session_token')) update_auth_session_token($mb['mb_datetime']);


// exit;
goto_url(G5_URL);
?>