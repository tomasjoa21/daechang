<?php
define('G5_IS_ADMIN', true);
define('G5_IS_V01', true);
include_once ('../../../common.php');
set_session('ss_kiosk_yn',1);
include_once(G5_USER_ADMIN_KIOSK_LIB_PATH.'/admin.lib.php');
include_once(G5_ADMIN_PATH.'/shop_admin/admin.shop.lib.php');
