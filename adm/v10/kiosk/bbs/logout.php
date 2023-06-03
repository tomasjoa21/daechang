<?php
include_once('../../../../common.php');

if(function_exists('social_provider_logout')){
    social_provider_logout();
}

// 이호경님 제안 코드
session_unset(); // 모든 세션변수를 언레지스터 시켜줌
session_destroy(); // 세션해제함

// 자동로그인 해제 --------------------------------
set_cookie('ck_mb_id', '', 0);
set_cookie('ck_auto', '', 0);
// 자동로그인 해제 end --------------------------------

$link = G5_USER_ADMIN_KIOSK_URL.'/index.php?kiosk=1';

goto_url($link);