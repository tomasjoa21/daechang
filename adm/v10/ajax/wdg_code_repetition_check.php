<?php
include_once('./_common.php');

$bpwcd_sch = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['wdg_table']} WHERE wgs_cd = '{$wgs_cd}' ");
echo $bpwcd_sch['cnt'];