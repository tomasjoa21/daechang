<?php
define('G5_HTTP_USER_BBS_URL',  https_url2(G5_BBS_DIR, false));
define('G5_HTTPS_USER_BBS_URL', https_url2(G5_BBS_DIR, true));


// URL에서 디렉토리명, 파일명 추출
//echo basename($_SERVER["SCRIPT_FILENAME"]);
$path_info=pathinfo($_SERVER['SCRIPT_FILENAME']);
$path_info['dirname'] = preg_replace("/\\\/", "/", $path_info['dirname']);
$g5['dir_name'] = substr($path_info['dirname'],strrpos($path_info['dirname'],'/')+1,strlen($path_info['dirname']));
$g5['dir_path'] = preg_replace("|".G5_PATH."|", "", $path_info['dirname']);
$g5['file_name'] = $path_info['filename'];
$g5['file_path'] = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], '/'.$g5['file_name']));
$g5['hook_file_path'] = (preg_match("|/adm/|",$g5['file_path'].'/')) ? 
                            preg_replace("|/adm|", "/adm/".G5_USER_ADMIN_DIR."/".G5_HOOK_DIR, $g5['file_path'])
                            : preg_replace("|".G5_PATH."|", G5_PATH."/".G5_USER_DIR."/".G5_HOOK_DIR, $g5['file_path']) ;
// echo $g5['hook_file_path'];

// /adm 디렉토리에 있는 경우 v10 관리자로 넘김
// echo $g5['dir_name'].'<br>';
// echo $g5['file_name'].'<br>';
if($member['mb_id']&&$g5['dir_name']=='adm'&&$g5['file_name']=='index') {
    if(isMobile()){
        goto_url(G5_USER_ADMIN_MOBILE_URL);
    }
    else{
        goto_url(G5_USER_ADMIN_URL);
    }
}

// 디비 테이블 메타 확장 -----------------
//설정 테이블 추출 ($g5['setting'] 과 같은 환경설정 변수를 저장합니다.)
$resutl_sql = "  SELECT com_idx, set_name, set_value
                        FROM {$g5['setting_table']}
                        WHERE set_key IN ('site','manager')
                            AND set_auto_yn = '1'
                            AND (set_country = '".$g5['setting']['set_default_country']."' OR set_country = 'global')
                        ORDER BY com_idx
";
// echo $resutl_sql;exit;
$result = sql_query($resutl_sql,1);
unset($resutl_sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // if($row['set_name']=='set_itm_status') {
    //     print_r3($row);
    // }
    $g5['setting'][$row['set_name']] = $row['set_value'];
    // 동일 변수가 존재하면 이전 거 삭제(업체 개별 설정을 위해서..)
    if($g5[$row['set_name']]) {
        unset($g5[$row['set_name']]);
        unset($g5[$row['set_name'].'_value']);
        unset($g5[$row['set_name'].'_radios']);
        unset($g5[$row['set_name'].'_options']);
        unset($g5[$row['set_name'].'_value_options']);
    }
    // 두줄 이상의 복잡한 구조 변수는 건너뜀
    $set_values = explode("\n", trim($g5['setting'][$row['set_name']]));
    if(sizeof($set_values)>1) {
        continue;
    }
	// A=B 형태를 가지고 있으면 자동 할당
	$set_values = explode(',', preg_replace("/\s+/", "", $g5['setting'][$row['set_name']]));
    foreach ($set_values as $set_value) {
        if( !preg_match("/(_subject|_content)$/",$row['set_name']) ) {
            // if( preg_match("/set_cam_type/",$row['set_name']) ) {   // <<<<<<< test
            // print_r3($row['set_name']);
            // print_r3('----------------');
            // print_r3($set_value);
            // print_r3(' value ----------------');
            //변수가 (,),(=)로 구분되어 있을때
            if( preg_match("/=/",$set_value) ) {
                list($key, $value) = explode('=', $set_value);
                $g5[$row['set_name']][$key] = $value.' ('.$key.')';
                $g5[$row['set_name'].'_key'][$key] = $key;
                $g5[$row['set_name'].'_value'][$key] = $value;
                $g5[$row['set_name'].'_reverse'][$value] = $key;
                $g5[$row['set_name'].'_arr'][] = $key;
                $g5[$row['set_name'].'_value_arr'][] = $value;
                $g5[$row['set_name'].'_radios'] .= '<label for="'.$row['set_name'].'_'.$key.'" class="'.$row['set_name'].'"><input type="radio" id="'.$row['set_name'].'_'.$key.'" name="'.$row['set_name'].'" value="'.$key.'">'.$value.'('.$key.')</label>';
                $g5[$row['set_name'].'_options'] .= '<option value="'.trim($key).'">'.trim($value).' ('.$key.')</option>';
                $g5[$row['set_name'].'_value_options'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
            }
            //변수가 (,)로만 구분되어 있을때
            else {
                $g5[$row['set_name'].'_array'][] = $set_value;
            }
            // }    // <<<<<<< test
        }
    }
    // unset($set_values);unset($set_value);
}
// unset($g5['setting']);
// unset($g5['debug_msg']);
// print_r3($g5);
// exit;

//회원 테이블 메타 확장
if ($_SESSION['ss_mb_id']) { // 로그인중이라면
	$result = sql_query(" SELECT mta_key,mta_value FROM {$g5['meta_table']} WHERE mta_db_table = 'member' AND mta_db_id='".$member['mb_id']."' ");
	for ($i=0; $row=sql_fetch_array($result); $i++)
		$member[$row['mta_key']] = $row['mta_value'];
}
//내용(콘텐츠) 테이블 메타 확장
// $co, $content 변수 둘다 사용하고 있어서 $cont 사용
if ($co_id) {
	$result = sql_query(" SELECT mta_key,mta_value FROM {$g5['meta_table']} WHERE mta_db_table = 'content' AND mta_db_id='".$co_id."' ");
	for ($i=0; $row=sql_fetch_array($result); $i++)
		$cont[$row["mta_key"]] = $row["mta_value"];
}

//업체 정보 추출
if ($_SESSION['ss_com_idx']) {
	$com = get_table('company','com_idx',$_SESSION['ss_com_idx']);
    // print_r3($com);
    $g5['com'] = $com;
}

if (isset($_REQUEST['sfl2']))  {
    $sfl2 = trim($_REQUEST['sfl2']);
    $sfl2 = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "", $sfl2);
    if ($sfl2)
        $qstr .= '&amp;sfl2=' . urlencode($sfl2); // search field (검색 필드)
} else {
    $sfl2 = '';
}


if (isset($_REQUEST['stx2']))  { // search text (검색어)
    $stx2 = get_search_string(trim($_REQUEST['stx2']));
    if ($stx2 || $stx2 === '0')
        $qstr .= '&amp;stx2=' . urlencode(cut_str($stx2, 20, ''));
} else {
    $stx2 = '';
}

if (isset($_REQUEST['sst2']))  {
    $sst2 = trim($_REQUEST['sst2']);
    $sst2 = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "", $sst2);
    if ($sst2)
        $qstr .= '&amp;sst2=' . urlencode($sst2); // search sort (검색 정렬 필드)
} else {
    $sst2 = '';
}

if (isset($_REQUEST['sod2']))  { // search order (검색 오름, 내림차순)
    $sod2 = preg_match("/^(asc|desc)$/i", $sod2) ? $sod2 : '';
    if ($sod2)
        $qstr .= '&amp;sod2=' . urlencode($sod2);
} else {
    $sod2 = '';
}


if (isset($_REQUEST['sst3']))  {
    $sst3 = trim($_REQUEST['sst3']);
    $sst3 = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "", $sst3);
    if ($sst3)
        $qstr .= '&amp;sst3=' . urlencode($sst3); // search sort (검색 정렬 필드)
} else {
    $sst3 = '';
}

if (isset($_REQUEST['sod3']))  { // search order (검색 오름, 내림차순)
    $sod3 = preg_match("/^(asc|desc)$/i", $sod3) ? $sod3 : '';
    if ($sod3)
        $qstr .= '&amp;sod3=' . urlencode($sod3);
} else {
    $sod3 = '';
}

// 로그인을 할 때마다 로그 파일 삭제해야 용량을 확보할 수 있음 
if(basename($_SERVER["SCRIPT_FILENAME"]) == 'login_check.php') {
	// 지난시간을 초로 계산해서 적어주시면 됩니다.
	$del_time_interval = 3600 * 18;	// Default = 18 시간
	$thumb_del_time_interval = 3600 * 240;	// Default = 240 시간 (10일)

	// 세선 파일 삭제 adm/session_file_delete.php 참고했습니다.
	if ($dir=@opendir(G5_DATA_PATH.'/session')) {
	    while($file=readdir($dir)) {
	        if (!strstr($file,'sess_')) continue;
	        if (strpos($file,'sess_')!=0) continue;
	        $session_file = G5_DATA_PATH.'/session/'.$file;

	        if (!$atime=@fileatime($session_file))
	            continue;
	        if (time() > $atime + $del_time_interval)
	            unlink($session_file);
	    }
    }
	
	
	// 캐시 파일 삭제 adm/cache_file_delete.php, captch_file_deelte 참고했습니다.
	if ($dir=@opendir(G5_DATA_PATH.'/cache')) {
		// latest 파일 삭제
		$latest_files = glob(G5_DATA_PATH.'/cache/latest-*');
		if (is_array($latest_files)) {
		    foreach ($latest_files as $latest_file) {
		        if (!$atime=@fileatime($latest_file))
		            continue;
		        if (time() > $atime + $del_time_interval)
		            unlink($latest_file);
		    }
		}

		// captcha 파일 삭제
		$captcha_files = glob(G5_DATA_PATH.'/cache/kcaptcha-*');
		if (is_array($captcha_files)) {
		    foreach ($captcha_files as $captcha_file) {
		        if (!$atime=@fileatime($captcha_file))
		            continue;
		        if (time() > $atime + $del_time_interval)
		            unlink($captcha_file);
		    }
		}

		// banner 파일 삭제
		$banner_files = glob(G5_DATA_PATH.'/cache/banner-*');
		if (is_array($banner_files)) {
		    foreach ($banner_files as $banner_file) {
		        if (!$atime=@fileatime($banner_file))
		            continue;
		        if (time() > $atime + $del_time_interval)
		            unlink($banner_file);
		    }
		}
		
	}

	// 썸네일 파일 삭제 adm/thumbnail_file_delete.php 참고했습니다.
	$directory = array();
	$dl = array('file', 'editor');
	foreach($dl as $val) {
	    if($handle = opendir(G5_DATA_PATH.'/'.$val)) {
	        while(false !== ($entry = readdir($handle))) {
	            if($entry == '.' || $entry == '..')
	                continue;
	
	            $path = G5_DATA_PATH.'/'.$val.'/'.$entry;
	
	            if(is_dir($path))
	                $directory[] = $path;
	        }
	    }
	}
	if (!empty($directory)) {
		foreach($directory as $dir) {
		    $thumb_files = glob($dir.'/thumb-*');
		    if (is_array($thumb_files)) {
		        foreach($thumb_files as $thumb_file) {
			        if (!$atime=@fileatime($thumb_file))
			            continue;
			        if (time() > $atime + $thumb_del_time_interval)
			            unlink($thumb_file);
		        }
		    }
		}
	}
	
}

// Admin mode default hooking
if(defined('G5_IS_ADMIN')){
    add_event('adm_board_form_before', 'u_adm_board_form_before', 10);
	add_event('tail_sub', 'u_tail_sub', 10);
	
	// if(G5_IS_MOBILE){
	// 	add_replace('head_css_url','get_mobile_admin_css',10,1);
	// }
	
    function u_adm_board_form_before(){
		global $g5;
		$column_query_arr = array(
			" SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_1' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_2' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_3' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_4' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_5' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_6' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_7' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_8' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_9' "
			," SHOW COLUMNS FROM `{$g5['board_table']}` LIKE 'bo_10' "
		);
		//print_r3($column_query_arr);
		for($i=0;$i<count($column_query_arr);$i++){
			$n = $i+1;
			${'colt'.$i} = sql_fetch($column_query_arr[$i]);
			if(${'colt'.$i}['Type'] != 'longtext'){
				sql_query(" ALTER TABLE `{$g5['board_table']}` MODIFY `bo_{$n}` longtext ",1);
			}
		}
	}
	function u_tail_sub(){
        global $g5,$member,$default,$config,$board,$menu,$sub_menu,$w,$print_version;

        // 관리자 디버깅 메시지 (있는 경우만 나타남)
        if( is_array($g5['debug_msg']) ) {
            for($i=0;$i<sizeof($g5['debug_msg']);$i++) {
                echo '<div class="debug_msg">'.$g5['debug_msg'][$i].'</div>'.PHP_EOL;
            }
        }
        $dta_types = json_encode($g5['set_data_type_value_arr']);
        echo '<script>'.PHP_EOL;
		echo 'var file_name = "'.$g5['file_name'].'";'.PHP_EOL;
		echo 'var dir_path = "'.$g5['dir_path'].'";'.PHP_EOL;
		echo 'var mb_id = "'.$member['mb_id'].'";'.PHP_EOL;
		echo 'var mb_name = "'.$member['mb_name'].'";'.PHP_EOL;
		echo 'var mb_level = "'.$member['mb_level'].'";'.PHP_EOL;
		echo 'var g5_community_use = "'.G5_COMMUNITY_USE.'";'.PHP_EOL;
		echo 'var g5_user_url = "'.G5_USER_URL.'";'.PHP_EOL;
		echo 'var g5_user_admin_url = "'.G5_USER_ADMIN_URL.'";'.PHP_EOL;
		echo 'var g5_user_admin_ajax_url = "'.G5_USER_ADMIN_AJAX_URL.'";'.PHP_EOL;
		echo 'var g5_user_admin_mobile_url = "'.G5_USER_ADMIN_MOBILE_URL.'";'.PHP_EOL;
		echo 'var g5_print_version = "'.$print_version.'";'.PHP_EOL;
		echo 'var get_device_change_url = "'.get_device_change_url().'";'.PHP_EOL;
		echo 'var cf_company_title = "'.$g5['com']['com_name'].'";'.PHP_EOL;
		echo 'var dta_types = Object.values('.$dta_types.');'.PHP_EOL;
		echo '$(function(e){'.PHP_EOL;
		// Test db display, Need to know what DB is using.
		if(!preg_match("/_www/",G5_MYSQL_DB) && !G5_IS_MOBILE) {
			echo "$('#ft p').prepend('<span style=\"color:darkorange;\">".G5_MYSQL_DB."</span>');".PHP_EOL;
		}
        // 관리자 디버깅 메시지 (있는 경우만 나타남)
        if( is_array($g5['debug_msg']) ) {
            echo '$("#container").prepend( $(".debug_msg") );'.PHP_EOL;
        }
		echo '});'.PHP_EOL;
		echo '</script>'.PHP_EOL;

		
        //기존 admin.css 추가적인 스타일을 위해서 adm.css를 추가
        add_stylesheet('<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" />', 0);
        if(is_file(G5_USER_ADMIN_CSS_PATH.'/adm.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/adm.css">',0);
        if(is_file(G5_USER_ADMIN_CSS_PATH.'/user.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/user.css">',0);
        // 팝업창 관련 css
		if(is_file(G5_USER_ADMIN_CSS_PATH.'/user_popup.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/user_popup.css">',1);
        //날짜픽커의 다크테마를 위한 css
        add_stylesheet('<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/ui-darkness/jquery-ui.css">', 1);
        //jquery-ui structure css
        if(is_file(G5_USER_ADMIN_JS_PATH.'/jquery-ui-1.12.1/jquery-ui.structure.min.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/jquery-ui-1.12.1/jquery-ui.structure.min.css">', 1);
        //타임픽커 css
        if(is_file(G5_USER_ADMIN_JS_PATH.'/bwg_timepicker.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/bwg_timepicker.css">', 1);
        //컬러픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/colpick/colpick.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/colpick/colpick.css">', 1);
        //데이터타임픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/datetimepicker/jquery.datetimepicker.min.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/datetimepicker/jquery.datetimepicker.min.css">',1);
        if( $board['gr_id']=='intra' && ($g5['file_name'] == 'board' || $g5['file_name'] == 'write')) { // 게시판인 경우
            if(is_file(G5_USER_ADMIN_CSS_PATH.'/board.css')) add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/board.css">',1);
        }
        // 사용자 정의 css, 디렉토리명과 같은 css가 있으면 자동으로 추가됨
        if(is_file(G5_USER_ADMIN_CSS_PATH.'/'.$g5['dir_name'].'/style.css')) {
            add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/'.$g5['dir_name'].'/style.css">',1);
        }
        // 사용자 정의 css, 파일명과 같은 css가 있으면 자동으로 추가됨
        if(is_file(G5_USER_ADMIN_CSS_PATH.'/v10/'.$g5['file_name'].'.css')) {
            add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/v10/'.$g5['file_name'].'.css">',1);
        }
        
        // 사용자 정의 css, 파일명과 같은 css가 있으면 자동으로 추가됨
        if(is_file(G5_USER_ADMIN_CSS_PATH.'/'.$g5['file_name'].'.css')) {
            add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/'.$g5['file_name'].'.css">',2);
        }

        // js 추가
        if(is_file(G5_USER_ADMIN_JS_PATH.'/function.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/function.js"></script>',0);
        if(is_file(G5_USER_ADMIN_JS_PATH.'/common.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/common.js"></script>',0);
        add_javascript('<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>',0);
        // 사용자 정의 함수, 파일명과 같은 js가 있으면 자동으로 추가됨
        if(is_file(G5_USER_ADMIN_JS_PATH.'/'.$g5['file_name'].'.js')) echo '<script src="'.G5_USER_ADMIN_JS_URL.'/'.$g5['file_name'].'.js"></script>'.PHP_EOL;
        if(is_file(G5_USER_ADMIN_JS_PATH.'/tail.js')) echo '<script src="'.G5_USER_ADMIN_JS_URL.'/tail.js"></script>'.PHP_EOL;

        //날짜픽커 한국어패치
        // if(is_file(G5_USER_ADMIN_JS_PATH.'/bwg_datepicker-ko.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/bwg_datepicker-ko.js"></script>',1);
        //날짜픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/bwg_datepicker.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/bwg_datepicker.js"></script>',1);
        //타임픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/bwg_timepicker.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/bwg_timepicker.js"></script>',1);
        //컬러픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/colpick/colpick.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/colpick/colpick.js"></script>',1);
        //날짜타임픽커
        if(is_file(G5_USER_ADMIN_JS_PATH.'/datetimepicker/jquery.datetimepicker.full.min.js')) add_javascript('<script src="'.G5_USER_ADMIN_JS_URL.'/datetimepicker/jquery.datetimepicker.full.min.js"></script>',1);

        // 후킹 추가
        @include_once($g5['hook_file_path'].'/'.$g5['file_name'].'.tail.php');

        $kosmolog_key = $g5['setting']['mng_userlog_crtfckey'];
        
        if($kosmolog_key)
            @include_once(G5_USER_ADMIN_PATH.'/_kosmolog.php');

        // 페이지 제일 하단에 공통으로 반영되어야 할 자바스크립트 php 파일
        @include_once(G5_USER_ADMIN_PATH.'/_tail_common_js.php');
	}
	
    function get_mobile_admin_css(){
		// return G5_USER_ADMIN_MOBILE_CSS_URL.'/admin.css?ver='.G5_CSS_VER;
		return G5_ADMIN_URL.'/css/admin.css?ver='.G5_CSS_VER;
	}
}
// User mode default hooking
else {
    add_event('tail_sub', 'u_tail_sub', 10);
	function u_tail_sub(){
		global $g5,$member,$default,$config,$is_admin,$w;
		if($g5['file_name'] == 'content') global $co,$co_id;
        
        // 관리자 디버깅 메시지 (있는 경우만 나타남)
        if( is_array($g5['debug_msg']) ) {
            for($i=0;$i<sizeof($g5['debug_msg']);$i++) {
                echo '<div class="debug_msg">'.$g5['debug_msg'][$i].'</div>'.PHP_EOL;
            }
        }

        // 
        echo '<script>'.PHP_EOL;
        echo 'var file_name = "'.$g5['file_name'].'";'.PHP_EOL;
        echo 'var dir_path = "'.$g5['dir_path'].'";'.PHP_EOL;
        echo 'var mb_id = "'.$member['mb_id'].'";'.PHP_EOL;
        echo 'var mb_name = "'.$member['mb_name'].'";'.PHP_EOL;
        echo 'var mb_level = "'.$member['mb_level'].'";'.PHP_EOL;
        echo 'var g5_community_use = "'.G5_COMMUNITY_USE.'"'.PHP_EOL;
        echo 'var g5_user_url = "'.G5_USER_URL.'"'.PHP_EOL;
        echo 'var g5_user_admin_url = "'.G5_USER_ADMIN_URL.'"'.PHP_EOL;
		echo '$(function(e){'.PHP_EOL;
        // 관리자 디버깅 메시지 (있는 경우만 나타남)
        if( is_array($g5['debug_msg']) ) {
            echo '$("#container").prepend( $(".debug_msg") );'.PHP_EOL;
        }
		echo '});'.PHP_EOL;
        echo '</script>'.PHP_EOL;
        
	}
}


// 수퍼관리자인 경우의 추가 설정
if($member['mb_level']>=9) {
    //운영권한, 정산권한 확보
    $member['mb_manager_yn'] = 1;
    $member['mb_account_yn'] = 1;
    $member['mb_firm_yn'] = 1;
}
if($member['mb_manager_yn']&&$member['mb_account_yn'])
    $member['mb_manager_account_yn'] = $member['mb_manager_and_account'] = 1;
if($member['mb_manager_yn']||$member['mb_account_yn'])
    $member['mb_manager_or_account'] = 1;
if($member['mb_manager_yn']&&$member['mb_account_yn']&&$member['mb_firm_yn'])
    $member['mb_allauth_yn'] = 1;
// 운영권한 없는 사람들의 dom display
if(!$member['mb_manager_yn']&&!$member['mb_account_yn']) {
    $member['mb_manager_account_display'] = 'display:none;';
}


// 회원인 경우 체크사항
if ($is_member) {
    // 읽지 않은 쪽지가 있다면
    $sql = " select count(*) as cnt from {$g5['memo_table']} where me_recv_mb_id = '{$member['mb_id']}' and me_read_datetime = '0000-00-00 00:00:00' ";
    $row = sql_fetch($sql);
    $memo_not_read = $row['cnt'];

    // 관리 권한이 주어졌다면
    $is_auth = false;
    $sql = " select count(*) as cnt from {$g5['auth_table']} where mb_id = '{$member['mb_id']}' ";
    $row = sql_fetch($sql);
    if ($row['cnt']||$member['mb_level']>8)
        $is_auth = true;
}


// 모든 분류 추출, 로딩속도 개선을 위해 캐시 처리, 기본적으로 12시간 (자주 안 바뀜)
$term_cache_time = 12;
if( is_array($g5['set_taxonomies_value']) ) {
    foreach ($g5['set_taxonomies_value'] as $key=>$value) {
        // print_r3($key.'/'.$value);
        // 캐시 파일이 없거나 캐시 시간을 초과했으면 (재)생성
        $term_cache_file = G5_DATA_PATH.'/cache/term-'.$key.'.php';
        @$term_cache_filetime = filemtime($term_cache_file);
        if( !file_exists($term_cache_file) || $term_cache_filetime < (G5_SERVER_TIME - 3600*$term_cache_time) ) {
            @unlink($term_cache_file);
            
            $g5[$key] = array();
            // 조직구조 추출
            $sql = "SELECT 
                        trm_idx term_idx
                        , GROUP_CONCAT(name) term_name
                        , trm_name2 trm_name2
                        , trm_content trm_content
                        , trm_more trm_more
                        , trm_status trm_status
                        , GROUP_CONCAT(cast(depth as char)) depth
                        , GROUP_CONCAT(up_idxs) up_idxs
                        , SUBSTRING_INDEX(SUBSTRING_INDEX(up_idxs, ',', GROUP_CONCAT(cast(depth as char))),',',-1) up1st_idx
                        , SUBSTRING_INDEX(up_idxs, ',', 1) uptop_idx
                        , GROUP_CONCAT(up_names) up_names
                        , GROUP_CONCAT(down_idxs) down_idxs
                        , GROUP_CONCAT(down_names) down_names
                        , REPLACE(GROUP_CONCAT(down_idxs), CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(down_idxs), ',', 1),','), '') down_idxs2
                        , REPLACE(GROUP_CONCAT(down_names), CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(down_names), '|', 1),','), '') down_names2
                        , leaf_node_yn leaf_node_yn
                        , SUM(table_row_count) table_row_count
                    FROM (	(
                            SELECT term.trm_idx
                                , CONCAT( REPEAT('   ', COUNT(parent.trm_idx) - 1), term.trm_name) AS name
                                , term.trm_name2
                                , term.trm_content
                                , term.trm_more
                                , term.trm_status
                                , (COUNT(parent.trm_idx) - 1) AS depth
                                , GROUP_CONCAT(cast(parent.trm_idx as char) ORDER BY parent.trm_left) up_idxs
                                , GROUP_CONCAT(parent.trm_name ORDER BY parent.trm_left SEPARATOR ' > ') up_names
                                , NULL down_idxs
                                , NULL down_names
                                , (CASE WHEN term.trm_right - term.trm_left = 1 THEN 1 ELSE 0 END ) leaf_node_yn
                                , 0 table_row_count
                                , term.trm_left
                                , 1 sw
                            FROM {$g5['term_table']} AS term,
                                    {$g5['term_table']} AS parent
                            WHERE term.trm_left BETWEEN parent.trm_left AND parent.trm_right
                                AND term.trm_taxonomy = '".$key."'
                                AND parent.trm_taxonomy = '".$key."'
                                AND term.trm_status in ('ok','hide') AND parent.trm_status in ('ok','hide')
                                
                                GROUP BY term.trm_idx
                            ORDER BY term.trm_left
                            )
                        UNION ALL
                            (
                            SELECT parent.trm_idx
                                , NULL name
                                , term.trm_name2
                                , term.trm_content
                                , term.trm_more
                                , term.trm_status
                                , NULL depth
                                , NULL up_idxs
                                , NULL up_names
                                , GROUP_CONCAT(cast(term.trm_idx as char) ORDER BY term.trm_left) AS down_idxs
                                , GROUP_CONCAT(term.trm_name ORDER BY term.trm_left SEPARATOR '^') AS down_names
                                , (CASE WHEN parent.trm_right - parent.trm_left = 1 THEN 1 ELSE 0 END ) leaf_node_yn
                                , SUM(term.trm_count) table_row_count
                                , parent.trm_left
                                , 2 sw
                            FROM {$g5['term_table']} AS term
                                    , {$g5['term_table']} AS parent
                            WHERE term.trm_left BETWEEN parent.trm_left AND parent.trm_right
                                AND term.trm_taxonomy = '".$key."'
                                AND parent.trm_taxonomy = '".$key."'
                                AND term.trm_status in ('ok','hide') AND parent.trm_status in ('ok','hide')
                                
                            GROUP BY parent.trm_idx
                            ORDER BY parent.trm_left
                            ) 
                        ) db_table
                    GROUP BY trm_idx
                    ORDER BY trm_left
            ";
            $result = sql_query($sql,1);
//            echo $sql;
            for($i=0; $row=sql_fetch_array($result); $i++) {
                $g5[$key][$i] = $row;
				//-- 지역 키값
                $g5[$key.'_key'][$row['term_idx']] = $row;
                //-- 하위 카테고리 전체
                $g5[$key.'_down_idxs'][$row['term_idx']] = $row['down_idxs'];
                //-- 하위 카테고리 전체 (자기 빼고 하위만)
                $g5[$key.'_down_idxs2'][$row['term_idx']] = $row['down_idxs2'];
                //-- 하위 카테고리 이름 전체
                $g5[$key.'_down_names'][$row['term_idx']] = $row['down_names'];
                //-- 하위 카테고리 이름 전체
                $g5[$key.'_down_names2'][$row['term_idx']] = $row['down_names2'];
                //-- 상위 카테고리 전체
                $g5[$key.'_up_idxs'][$row['term_idx']] = $row['up_idxs'];
                //-- 상위 카테고리 이름 전체
                $g5[$key.'_up_names'][$row['term_idx']] = $row['up_names'];
                //-- 부서 이름
                $g5[$key.'_name'][$row['term_idx']] = trim($row['term_name']);
                //-- 조직코드 정렬 우선순위
                $g5[$key.'_sort'][$row['term_idx']] = $i;
                //-- 바로 상위 카테고리 idx
                $g5[$key.'_up1_idx'][$row['term_idx']] = $row['up1st_idx'];
                //-- 최 상위 카테고리 idx
                $g5[$key.'_uptop_idx'][$row['term_idx']] = $row['uptop_idx'];
                //-- 카테고리 lefa_node 여부
                $g5[$key.'_lefa_yn'][$row['term_idx']] = $row['leaf_node_yn'];
                
                // 추가 부분 unserialize
                $unser = unserialize(stripslashes($row['trm_more']));
                if( is_array($unser) ) {
                    foreach ($unser as $key1=>$value1) {
                        $row[$key1] = htmlspecialchars($value1, ENT_QUOTES | ENT_NOQUOTES); // " 와 ' 를 html code 로 변환
                    }    
                }
                // 삭제조직코드 (공백 제거)
                if($row['trash_idxs'])
                    $g5[$key.'_trash_idxs'][$row['term_idx']] = ','.preg_replace("/\s+/", "", $row['trash_idxs']);
                
            }
			
            // 캐시파일 생성 (다음 접속을 위해서 생성해 둔다.)
            $handle = fopen($term_cache_file, 'w');
            $term_content = "<?php\n";
            $term_content .= "if (!defined('_GNUBOARD_')) exit;\n";
            $term_content .= "\$g5['".$key."_down_idxs']=".var_export($g5[$key.'_down_idxs'], true).";\n";
            $term_content .= "\$g5['".$key."_down_names']=".var_export($g5[$key.'_down_names'], true).";\n";
            $term_content .= "\$g5['".$key."_down_idxs2']=".var_export($g5[$key.'_down_idxs2'], true).";\n";
            $term_content .= "\$g5['".$key."_down_names2']=".var_export($g5[$key.'_down_names2'], true).";\n";
            $term_content .= "\$g5['".$key."_up_idxs']=".var_export($g5[$key.'_up_idxs'], true).";\n";
            $term_content .= "\$g5['".$key."_up_names']=".var_export($g5[$key.'_up_names'], true).";\n";
            $term_content .= "\$g5['".$key."_name']=".var_export($g5[$key.'_name'], true).";\n";
            $term_content .= "\$g5['".$key."_sort']=".var_export($g5[$key.'_sort'], true).";\n";
            $term_content .= "\$g5['".$key."_up1_idx']=".var_export($g5[$key.'_up1_idx'], true).";\n";
            $term_content .= "\$g5['".$key."_uptop_idx']=".var_export($g5[$key.'_uptop_idx'], true).";\n";
            $term_content .= "\$g5['".$key."_lefa_yn']=".var_export($g5[$key.'_lefa_yn'], true).";\n";
            $term_content .= "\$g5['".$key."_trash_idxs']=".var_export($g5[$key.'_trash_idxs'], true).";\n";
            $term_content .= "\$g5['".$key."_key']=".var_export($g5[$key.'_key'], true).";\n";
            $term_content .= "\$g5['".$key."']=".var_export($g5[$key], true).";\n";
            $term_content .= "?>";
            fwrite($handle, $term_content);
            fclose($handle);
        }
        // 캐시 파일 존재한다면..
        else {
            // 캐시 파일 내부에 배열로 department 변수 설정되어 있음
            include_once($term_cache_file);
        }

        // 분류 카테고리 옵션 생성 (다운idxs 포함해서 변수 넘길 때)
        for($i=0; $i<sizeof($g5[$key]); $i++) {
            ${$key.'_select_options'} .= '<option value="'.$g5[$key][$i]['down_idxs'].'">'.$g5[$key][$i]['up_names'].'</option>';	// value 모든 하위값 다 가지고 있어야 함
            ${$key.'_form_options'} .= '<option value="'.$g5[$key][$i]['term_idx'].'">'.$g5[$key][$i]['up_names'].'</option>';		// 수정(등록) 시는 특정값 설정되어야 함
            ${$key.'_form_depth0_options'} .= ($g5[$key][$i]['depth']==0) ? '<option value="'.$g5[$key][$i]['term_idx'].'">'.$g5[$key][$i]['up_names'].'</option>' : '';	// 최상위 단계만
            ${$key.'_radios'} .= '<label for="set_'.$key.'_idx_'.$g5[$key][$i]['term_idx'].'" class="set_'.$key.'_idx"><input type="radio" id="set_'.$key.'_idx_'.$g5[$key][$i]['term_idx'].'" name="set_'.$key.'_idx" value="'.$g5[$key][$i]['term_idx'].'">'.$g5[$key][$i]['term_name'].'</label>';
            ${$key.'_checkboxes'} .= '<label for="set_'.$key.'_idx_'.$g5[$key][$i]['term_idx'].'" class="set_'.$key.'_idx"><input type="checkbox" id="set_'.$key.'_idx_'.$g5[$key][$i]['term_idx'].'" name="set_'.$key.'_idx[]" value="'.$g5[$key][$i]['term_idx'].'">'.$g5[$key][$i]['term_name'].'</label>';
        }
        
    }    
}


// 메뉴번호 설정, 네비 vs 게시판 $g5['navi_menu'] 설정
// 게시물인 경우
if($wr_id) {
    $g5['navi_menu'] = $write['wr_10'];
    if(!$write['wr_10'])
        $g5['navi_menu'] = $board['bo_10'];
}
// 게시판인 경우
else if($bo_table) {
    $g5['navi_menu'] = $board['bo_10'];
}
// 상품상세보기인 경우
else if($it_id) {
    $g5['navi_menu'] = $it['it_10'];
}
// 상품카테고리인 경우
else if($ca_id) {
    $g5['navi_menu'] = $ca['ca_10'];
}
// 내용보기(콘텐츠)인 경우
else if($co_id) {
    $g5['navi_menu'] = $cont['co_10'];
}
// 프로그램 파일인 경우
else if($navi_menu) {
    $g5['navi_menu'] = $navi_menu;
}

//-- 월화수목금토일 한글값
$g5['week_names'] = array(
	"0"=>"일"
	,"1"=>"월"
	,"2"=>"화"
	,"3"=>"수"
	,"4"=>"목"
	,"5"=>"금"
	,"6"=>"토"
);

//사용자 로그 테이블(g5_5_user_log)이 존재하는지 확인하고 없으면 설치
$user_log_tbl = @sql_query(" DESC ".$g5['user_log_table']." ", false);
if(!$user_log_tbl){
	include_once(G5_USER_ADMIN_SQLS_PATH.'/create_user_log.php');
}