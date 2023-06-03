<?php
include_once('./_common.php');
include_once('../adm.lib.php');
//$w
//$category;
$arr = get_skin_dir($device,G5_USER_ADMIN_SKIN_PATH);
$skin_basic_path = G5_USER_ADMIN_SKIN_PATH.'/'.$device;
$skin_basic_url = G5_USER_ADMIN_SKIN_URL.'/'.$device;
$select_html = "<select device=\"".$device."\" name=\"wgs_skin\" id=\"wgs_skin\" required class=\"required\">";

for ($i=0; $i<count($arr); $i++) {
	$bwg_skin_url = $skin_basic_url.'/'.$arr[$i];
	$bwg_skin_path = $skin_basic_path.'/'.$arr[$i];
	
	if ($i == 0) $select_html .=  "<option thumb=\"".$bwg_skin_url."/img/no_choice.gif\" value=\"\">사용안함</option>";
	
	$bwg_thumb =  (is_file($bwg_skin_path.'/screenicon.gif')) ? $bwg_skin_url.'/screenicon.gif' : $bwg_skin_url.'/img/default.gif';
	$bwg_skin_name = '';
	$text = $bwg_skin_path.'/readme.txt';
	
	if(is_file($text)){
		$content = file($text, false);
		$content = array_map('trim', $content);
		preg_match('#^Category:(.+)$#i', $content[0], $m0);
		preg_match('#^Skin Name:(.+)$#i', $content[1], $m1);
		$bwg_category = trim($m0[1]);
		$bwg_skin_name = trim($m1[1]);
	}
	
	$select_html .= ($category == $bwg_category) ? "<option thumb=\"".$bwg_thumb."\" value=\"".$arr[$i]."\"".(($skin != '') ? get_selected($skin, $arr[$i]) : '').">".$arr[$i]."</option>\n" : "";
	
}	

$select_html .= "</select>";
echo $select_html;