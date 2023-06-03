<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// timescale DB connect
// define('G5_PGSQL_HOST', '61.83.89.15');
define('G5_PGSQL_HOST', 'localhost');
define('G5_PGSQL_USER', 'postgres');
define('G5_PGSQL_PASSWORD', 'super@ingglobal*');
define('G5_PGSQL_DB', 'daechang_www');

$connect_pg = sql_connect_pg(G5_PGSQL_HOST, G5_PGSQL_USER, G5_PGSQL_PASSWORD) or die('PgSQL Connect Error!!!');
$g5['connect_pg'] = $connect_pg;

