<?php
define('G5_IS_ADMIN', true);
define('G5_IS_V01', true);
include_once ('../../common.php');

if ($member['mb_level'] < 3)
    alert('승인된 회원만 접근 가능합니다.',G5_URL);
    
include_once(G5_ADMIN_PATH.'/admin.lib.php');
include_once(G5_ADMIN_PATH.'/shop_admin/admin.shop.lib.php');

// 회원리스트 타입을 세션으로 구워서 사용 (비슷한 페이지인데 따로 페이지를 만들 필요가 없을 거 같아서..) member, company, employee
if($mb_list_type) {
	set_session('ss_mb_list_type', $mb_list_type);
    $_SESSION['ss_mb_list_type'] = $mb_list_type;
}

// 변수 재설정.. 서울오빠 파일내부 변수들을 $s3 로 사용합니다.
if($_SESSION['ss_mb_list_type']=='company') {   // 업체계정관리
    $s3['title'] = "업체계정관리";
    $s3['sql_defalut'] = " mb_level = 4 ";
}
else if($_SESSION['ss_mb_list_type']=='employee') { // 직원관리
    $s3['title'] = "직원관리";
    $s3['sql_defalut'] = " mb_level IN (6,7) ";
}
else {
    $s3['title'] = "회원관리";
    $s3['sql_defalut'] = " mb_level IN (2,3) ";
}
?>