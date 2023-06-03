<?php
$temp_db_connect = mysqli_connect($temp_db['g5_mysql_host'],$temp_db['g5_mysql_user'],$temp_db['g5_mysql_password'],$temp_db['g5_mysql_db']);
$temp_sql = " SELECT wgf_value FROM g5_5_wdg_config WHERE wgf_name = 'wgf_sub_menu' ";
$temp_result = mysqli_query($temp_db_connect,$temp_sql);
$temp_sub_name = mysqli_fetch_assoc($temp_result);
$wdg_sub_menu = ($temp_sub_name['wgf_value']) ? $temp_sub_name['wgf_value'] : '910130';

$temp_sql1 = " SELECT wgf_value FROM g5_5_wdg_config WHERE wgf_name = 'wgf_sub_menu1' ";
$temp_result1 = mysqli_query($temp_db_connect,$temp_sql1);
$temp_sub_name1 = mysqli_fetch_assoc($temp_result1);
$wdg_sub_menu1 = ($temp_sub_name1['wgf_value']) ? $temp_sub_name1['wgf_value'] : '910140';

$temp_sql2 = " SELECT wgf_value FROM g5_5_wdg_config WHERE wgf_name = 'wgf_sub_menu2' ";
$temp_result2 = mysqli_query($temp_db_connect,$temp_sql2);
$temp_sub_name2 = mysqli_fetch_assoc($temp_result2);
$wdg_sub_menu2 = ($temp_sub_name2['wgf_value']) ? $temp_sub_name2['wgf_value'] : '910150';