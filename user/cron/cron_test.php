<?php
// */5 * * * * wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/cron_test.php (working??)
// wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/cron_test.php (working!!)
include_once('./_common.php');


$ar['mta_db_table'] = 'test';
$ar['mta_db_id'] = time();
$ar['mta_key'] = 'cron';
$ar['mta_value'] = 'mes_charge_in_sysn';
meta_update($ar);
unset($ar);

?>
