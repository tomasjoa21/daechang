<?php
define('G5_IS_ADMIN', true);
define('G5_IS_V01', true);
include_once ('../../../common.php');

if ($member['mb_level'] < 4)
    alert('승인된 회원만 접근 가능합니다.',G5_URL);
    
include_once(G5_ADMIN_PATH.'/admin.lib.php');

?>