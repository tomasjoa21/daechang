<?php
include_once ('./_common.php');
//include_once('../adm.lib.php');

if($w == 'u'){
	//$device,$skin,w,bwgs_idx
	$sql = " SELECT * FROM {$g5['wdg_option_table']} WHERE wgs_idx = '{$wgs_idx}' ";
	$result = sql_query($sql,1);
	for($i=0;$row=sql_fetch_array($result);$i++){
		${$row['wgo_name']} = get_text(stripslashes($row['wgo_value']));
	}
}
$skin_set_ajax_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin.'/_set/_ajax';
$skin_setform_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin.'/_set/bpwg_form.skin.php';
$bwg_skin_set_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin.'/_set';
$bwg_skin_set_url = G5_USER_ADMIN_SKIN_URL.'/'.$device.'/'.$skin.'/_set';
$bwg_skin_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin;
$bwg_skin_url = G5_USER_ADMIN_SKIN_URL.'/'.$device.'/'.$skin;
$bwg_skin_skin_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin.'/skin';
$bwg_skin_skin_url = G5_USER_ADMIN_SKIN_URL.'/'.$device.'/'.$skin.'/skin';
$bwg_skin_img_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device.'/'.$skin.'/img';
$bwg_skin_img_url = G5_USER_ADMIN_SKIN_URL.'/'.$device.'/'.$skin.'/img';
if(is_file($skin_setform_path))
	include_once($skin_setform_path);