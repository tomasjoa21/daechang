<?php
// include_once("./_common.php");

//data폴더에 위젯관련 폴더(각종파일을 저장하는 디렉토리) 생성
$data_wdg_dir_path = G5_DATA_PATH.'/wdg';
$wdg_permision_str = "chmod 707 -R ".$data_wdg_dir_path;
if(!is_dir($data_wdg_dir_path)){
	@mkdir($data_wdg_dir_path, G5_DATA_WDG_PERMISSION);
	@chmod($data_wdg_dir_path, G5_DATA_WDG_PERMISSION);
	
	$data_wdg_config_dir_path = $data_wdg_dir_path.'/config';
	@mkdir($data_wdg_config_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_config_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_file_dir_path = $data_wdg_dir_path.'/file';
	@mkdir($data_wdg_file_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_file_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_board_dir_path = $data_wdg_dir_path.'/board';
	@mkdir($data_wdg_board_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_board_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_content_dir_path = $data_wdg_dir_path.'/content';
	@mkdir($data_wdg_content_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_content_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_seo_dir_path = $data_wdg_dir_path.'/seo';
	@mkdir($data_wdg_seo_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_seo_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_shop_dir_path = $data_wdg_dir_path.'/shop';
	@mkdir($data_wdg_shop_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_shop_dir_path, G5_DIR_PERMISSION);
	
	$data_wdg_temp_dir_path = $data_wdg_dir_path.'/temp';
	@mkdir($data_wdg_temp_dir_path, G5_DIR_PERMISSION);
	@chmod($data_wdg_temp_dir_path, G5_DIR_PERMISSION);
	
	$fp = fopen($data_wdg_dir_path.'/temp.json','w');
	//fwrite($fp,json_encode($polyArr));
	fclose($fp);
	
	exec($wdg_permision_str);
}
//만약 기존의 data폴더 안에 wdg폴더가 있으면 그안에 혹시 새로운 파일/폴더가 있을수 있으니 하위파일들까지 권한 재설정을 하자
else{
	exec($wdg_permision_str);
}

//위젯환경설정 테이블 추출 ($g5['wdg'] 과 같은 환경설정 변수를 저장합니다.)
$result = sql_query(" SELECT wgf_name,wgf_value FROM {$g5['wdg_config_table']} WHERE wgf_auto_yn = '1' AND wgf_country = 'ko_KR' ");
for ($i=0; $row=sql_fetch_array($result); $i++){
	$g5['wdg'][$row['wgf_name']] = $row['wgf_value'];
	// A=B 형태를 가지고 있으면 자동 할당
	$wgf_values = explode(',', preg_replace("/\s+/", "", $g5['wdg'][$row['wgf_name']]));
	foreach($wgf_values as $wgf_value){
		if(preg_match("/=/",$wgf_value)){
			list($key, $value) = explode('=', $wgf_value);
			$g5[$row['wgf_name']][$key] = $value.' ('.$key.')';
			$g5[$row['wgf_name'].'_value'][$key] = $value;
			$g5[$row['wgf_name'].'_reverse'][$value] = $key;
			$g5[$row['wgf_name'].'_radios'] .= '<label for="'.$row['wgf_name'].'_'.$key.'" class="'.$row['wgf_name'].'"><input type="radio" id="'.$row['wgf_name'].'_'.$key.'" name="'.$row['wgf_name'].'" value="'.$key.'">'.$value.'('.$key.')</label>';
			$g5[$row['wgf_name'].'_options'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
			$g5[$row['wgf_name'].'_value_options'] .= '<option value="'.trim($key).'">'.trim($value).' ('.$key.')</option>';
		}
	}
}


//브라우저
$g5['browser_name'] = wdg_browserCheck();
//echo $g5['browser_name'];
//익스여부
$g5['is_explorer'] = wdg_is_explorer();
//echo $g5['is_explorer'];
//익스버전
$g5['ie_version'] = 0;
if (preg_match("/ie/", $g5['browser_name']) && $g5['is_explorer']){
	$g5['ie_version'] = (int) substr($g5['browser_name'],2);
}

//실제모바일 디바이스여부
$g5['is_real_mobile'] = is_mobile();

//브라우저 기본정보
$g5['user_agent'] = $_SERVER["HTTP_USER_AGENT"];
//echo $g5['user_agent'];

//디바이스 타입
$g5['device_type'] = wdg_deviceCheck();
//echo $g5['device_type'];

//PC유사한 디바이스인가?
$g5['is_device_etc'] = ($g5['device_type'] == 'etc') ? 1 : 0;

//안드로이드 디바이스인가?
$g5['is_device_android'] = ($g5['device_type'] == 'android') ? 1 : 0;

//iphone 디바이스인가?
$g5['is_device_iphone'] = ($g5['device_type'] == 'iphone') ? 1 : 0;

//ipad 디바이스인가?
$g5['is_device_ipad'] = ($g5['device_type'] == 'ipad') ? 1 : 0;

//ipod 디바이스인가?
$g5['is_device_ipod'] = ($g5['device_type'] == 'ipod') ? 1 : 0;

//ios 디바이스인가?
$g5['is_device_ios'] = ($g5['device_type'] == 'iphone' || $g5['device_type'] == 'ipad' || $g5['device_type'] == 'ipod') ? 1 : 0;

//blackberry 디바이스인가?
$g5['is_device_blackberry'] = ($g5['device_type'] == 'blackberry') ? 1 : 0;

//오픈그래프
$og_width = 1200;//1200,600,200
$og_height = 627;//627,315,200

$fsize_192 = 192;
$fsize_180 = 180;
/*
$fsize_152 = 152;
$fsize_144 = 144;
$fsize_120 = 120;
$fsize_114 = 114;
$fsize_96 = 96;
$fsize_76 = 76;
$fsize_72 = 72;
$fsize_60 = 60;
$fsize_57 = 57;
$fsize_32 = 32;
$fsize_16 = 16;
*/
$fsize_192_str = '192x192';
$fsize_180_str = '180x180';
/*
$fsize_152_str = '152x152';
$fsize_144_str = '144x144';
$fsize_120_str = '120x120';
$fsize_114_str = '114x114';
$fsize_96_str = '96x96';
$fsize_76_str = '76x76';
$fsize_72_str = '72x72';
$fsize_60_str = '60x60';
$fsize_57_str = '57x57';
$fsize_32_str = '32x32';
$fsize_16_str = '16x16';
*/

$lg1_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo1' ");
$lg2_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo2' ");
$lg3_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo3' ");
$lg4_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo4' ");
$lg5_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo5' ");
$lg6_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo6' ");
$lg7_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo7' ");
$lg8_image = sql_fetch(" SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' AND wga_array = 'logo8' ");

if($lg1_image['wga_idx']){
	$lg1_image['wga_download'] = (is_file(G5_PATH.$lg1_image['wga_path'].'/'.$lg1_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg1_image['wga_path'].'/'.$lg1_image['wga_name']).'&file_name_orig='.$lg1_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg1_image['wga_src'] = G5_URL.$lg1_image['wga_path'].'/'.$lg1_image['wga_name'];
	$lg1_image['wga_img'] = '<img src="'.$lg1_image['wga_src'].'">';
}
if($lg2_image['wga_idx']){
	$lg2_image['wga_download'] = (is_file(G5_PATH.$lg2_image['wga_path'].'/'.$lg2_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg2_image['wga_path'].'/'.$lg2_image['wga_name']).'&file_name_orig='.$lg2_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg2_image['wga_src'] = G5_URL.$lg2_image['wga_path'].'/'.$lg2_image['wga_name'];
	$lg2_image['wga_img'] = '<img src="'.$lg2_image['wga_src'].'">';
}
if($lg3_image['wga_idx']){
	$lg3_image['wga_download'] = (is_file(G5_PATH.$lg3_image['wga_path'].'/'.$lg3_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg3_image['wga_path'].'/'.$lg3_image['wga_name']).'&file_name_orig='.$lg3_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg3_image['wga_src'] = G5_URL.$lg3_image['wga_path'].'/'.$lg3_image['wga_name'];
	$lg3_image['wga_img'] = '<img src="'.$lg3_image['wga_src'].'">';
}
if($lg4_image['wga_idx']){
	$lg4_image['wga_download'] = (is_file(G5_PATH.$lg4_image['wga_path'].'/'.$lg4_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg4_image['wga_path'].'/'.$lg4_image['wga_name']).'&file_name_orig='.$lg4_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg4_image['wga_src'] = G5_URL.$lg4_image['wga_path'].'/'.$lg4_image['wga_name'];
	$lg4_image['wga_img'] = '<img src="'.$lg4_image['wga_src'].'">';
}
if($lg5_image['wga_idx']){
	$lg5_image['wga_download'] = (is_file(G5_PATH.$lg5_image['wga_path'].'/'.$lg5_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg5_image['wga_path'].'/'.$lg5_image['wga_name']).'&file_name_orig='.$lg5_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg5_image['wga_src'] = G5_URL.$lg5_image['wga_path'].'/'.$lg5_image['wga_name'];
	$lg5_image['wga_img'] = '<img src="'.$lg5_image['wga_src'].'">';
}
if($lg6_image['wga_idx']){
	$lg6_image['wga_download'] = (is_file(G5_PATH.$lg6_image['wga_path'].'/'.$lg6_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg6_image['wga_path'].'/'.$lg6_image['wga_name']).'&file_name_orig='.$lg6_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg6_image['wga_src'] = G5_URL.$lg6_image['wga_path'].'/'.$lg6_image['wga_name'];
	$lg6_image['wga_img'] = '<img src="'.$lg6_image['wga_src'].'">';
}
if($lg7_image['wga_idx']){
	$lg7_image['wga_download'] = (is_file(G5_PATH.$lg7_image['wga_path'].'/'.$lg7_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg7_image['wga_path'].'/'.$lg7_image['wga_name']).'&file_name_orig='.$lg7_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg7_image['wga_src'] = G5_URL.$lg7_image['wga_path'].'/'.$lg7_image['wga_name'];
	$lg7_image['wga_img'] = '<img src="'.$lg7_image['wga_src'].'">';
}
if($lg8_image['wga_idx']){
	$lg8_image['wga_download'] = (is_file(G5_PATH.$lg8_image['wga_path'].'/'.$lg8_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$lg8_image['wga_path'].'/'.$lg8_image['wga_name']).'&file_name_orig='.$lg8_image['wga_name_orig'].'">파일다운로드</a>':'';
	$lg8_image['wga_src'] = G5_URL.$lg8_image['wga_path'].'/'.$lg8_image['wga_name'];
	$lg8_image['wga_img'] = '<img src="'.$lg8_image['wga_src'].'">';
}
$g5['logo1'] = $lg1_image;
$g5['logo2'] = $lg2_image;
$g5['logo3'] = $lg3_image;
$g5['logo4'] = $lg4_image;
$g5['logo5'] = $lg5_image;
$g5['logo6'] = $lg6_image;
$g5['logo7'] = $lg7_image;
$g5['logo8'] = $lg8_image;

$og_image = sql_fetch(" SELECT * FROM {$g5['bpwidget_attachment_table']} WHERE wga_type = 'config' AND wga_array = 'file_og' ");
$fv_image = sql_fetch(" SELECT * FROM {$g5['bpwidget_attachment_table']} WHERE wga_type = 'config' AND wga_array = 'favicon' ");
if($og_image['wga_idx']){
	$og_image['wga_download'] = (is_file(G5_PATH.$og_image['wga_path'].'/'.$og_image['wga_name'])) ? 
						'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_LIB_URL.'/download.php?file_fullpath='.urlencode(G5_PATH.$og_image['wga_path'].'/'.$og_image['wga_name']).'&file_name_orig='.$og_image['wga_name_orig'].'">파일다운로드</a>':'';
	$og_image['wga_src'] = G5_URL.$og_image['wga_path'].'/'.$og_image['wga_name'];
	$og_image['wga_img'] = '<img src="'.$og_image['wga_src'].'">';
	$og_image['wga_thumbnail'] = thumbnail(
									$og_image['wga_name']
									, G5_PATH.$og_image['wga_path']
									, G5_PATH.$og_image['wga_path'],
									$og_width, $og_height,
									false, true, 'center', false, $um_value='100/0.5/3');	// is_create, is_crop, crop_mode
	$og_image['wga_thumbnail_src'] = G5_URL.$og_image['wga_path'].'/'.$og_image['wga_thumbnail'];
	$og_image['wga_thumbnail_img'] = (is_file(G5_PATH.$og_image['wga_path'].'/'.$og_image['wga_name'])) ? 
										'<br><br><img src="'.$og_image['wga_thumbnail_src'].'" class="og_image">'
										:'';
}
if($fv_image['wga_idx'] && is_file(G5_PATH.$fv_image['wga_path'].'/'.$fv_image['wga_name'])){
	$fv_image['wga_fv192'] = thumbnail($fv_image['wga_name'], G5_PATH.$fv_image['wga_path'], G5_PATH.$fv_image['wga_path'],$fsize_192, $fsize_192,false, false, 'center', false, $um_value='100/0.5/3');
	$fv_image['wga_fv180'] = thumbnail($fv_image['wga_name'], G5_PATH.$fv_image['wga_path'], G5_PATH.$fv_image['wga_path'],$fsize_180, $fsize_180,false, false, 'center', false, $um_value='100/0.5/3');

	$fv_image['wga_fv192_src'] = G5_URL.$fv_image['wga_path'].'/'.$fv_image['wga_fv192'];
	$fv_image['wga_fv180_src'] = G5_URL.$fv_image['wga_path'].'/'.$fv_image['wga_fv180'];
}

$g5['favicon'] = '<link rel="apple-touch-icon" sizes="'.$fsize_180_str.'" href="'.$fv_image['wga_fv180_src'].'" />'.PHP_EOL;
$g5['favicon'] .= '<link rel="icon" type="image/png" sizes="'.$fsize_192_str.'" href="'.$fv_image['wga_fv192_src'].'" />'.PHP_EOL;

if($g5['file_name'] == 'board' && $wr_id && $board['bo_read_level'] < 3){
	$thumb = get_list_thumbnail($board['bo_table'], $wr_id, $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);
	$wr_arr = sql_fetch(" SELECT * FROM `".$g5['write_prefix'].$board['bo_table']."` WHERE wr_id='".$wr_id."' ");
	
	$g5['og_header'] = '<meta name="google-site-verification" content="'.$g5['bpwidget']['bwgf_google_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="naver-site-verification" content="'.$g5['bpwidget']['bwgf_naver_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:type" content="website" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:url" content="'.((($_SERVER['SERVER_PORT'] != '80') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" />'.PHP_EOL;
	//$g5['og_header'] .= '<meta property="og:url" content="'.((($_SERVER['SERVER_PORT'] != '80') ? 'https://' : 'http://').$_SERVER['HTTP_HOST']).'/bbs/board.php" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:title" content="'.get_text($wr_arr['wr_seo_title']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image" content="'.(($thumb['src']) ? $thumb['src'] : $og_image['wga_thumbnail_src']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:width" content="'.(($thumb['src']) ? $board['bo_gallery_width'] : $og_width).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:height" content="'.(($thumb['src']) ? $board['bo_gallery_height'] : $og_height).'" />'.PHP_EOL;
}
else if($g5['file_name'] == 'content' && $co_id){
	$g5['og_header'] = '<meta name="google-site-verification" content="'.$g5['bpwidget']['bwgf_google_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="naver-site-verification" content="'.$g5['bpwidget']['bwgf_naver_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:type" content="website" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:url" content="'.G5_URL.'/index.php" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:title" content="'.get_text($g5['bpwidget']['bwgf_og_title']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image" content="'.$og_image['wga_thumbnail_src'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:width" content="'.$og_width.'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:height" content="'.$og_height.'" />'.PHP_EOL;
}
else if($g5['file_name'] == 'item' && $it_id){
	$it_arr = sql_fetch(" SELECT * FROM {$g5['g5_shop_item_table']} WHERE it_id='{$it_id}' ");
	$it_image['wga_thumbnail_src'] = wdg_get_it_thumbnail_url($it_arr['it_img1'], $default['de_mimg_width'], $default['de_mimg_height']);
	$it_og_ttl = ($it_arr['it_basic']) ? $it_arr['it_basic'] : $g5['bpwidget']['bwgf_og_description'];
	$g5['og_header'] = '<meta name="google-site-verification" content="'.$g5['bpwidget']['bwgf_google_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="naver-site-verification" content="'.$g5['bpwidget']['bwgf_naver_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="description" content="'.get_text($it_og_ttl).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:type" content="website" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:url" content="'.((($_SERVER['SERVER_PORT'] != '80') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:title" content="'.get_text($it_arr['it_seo_title']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:description" content="'.get_text($it_og_ttl).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image" content="'.$it_image['wga_thumbnail_src'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:width" content="'.$default['de_mimg_width'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:height" content="'.$default['de_mimg_height'].'" />'.PHP_EOL;
}else{
	$g5['og_header'] = '<meta name="google-site-verification" content="'.$g5['bpwidget']['bwgf_google_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="naver-site-verification" content="'.$g5['bpwidget']['bwgf_naver_site_verification'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta name="description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:type" content="website" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:url" content="'.G5_URL.'/index.php" />'.PHP_EOL;	
	$g5['og_header'] .= '<meta property="og:title" content="'.get_text($g5['bpwidget']['bwgf_og_title']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:description" content="'.get_text($g5['bpwidget']['bwgf_og_description']).'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image" content="'.$og_image['wga_thumbnail_src'].'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:width" content="'.$og_width.'" />'.PHP_EOL;
	$g5['og_header'] .= '<meta property="og:image:height" content="'.$og_height.'" />'.PHP_EOL;
}

//나의 스크랩 카운트
$scrarr = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['scrap_table']} WHERE mb_id = '{$member['mb_id']}' ");
$g5['scr_count'] = $scrarr['cnt'];
