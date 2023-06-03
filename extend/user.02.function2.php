<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//--- BP위젯 환경설정 변수 저장 ---//
if(!function_exists('wdg_config_update')){
function wdg_config_update($wdg_array)
{
    global $g5;

    $wgf_country = ($wdg_array['wgf_country'])? $wdg_array['wgf_country']:'ko_KR';
    $wgf_key = ($wdg_array['wgf_key']) ? $wdg_array['wgf_key']:'common';
    $wgf_auto_yn = ($wdg_array['wgf_auto_yn']) ? 1:0;

    $row1 = sql_fetch(" SELECT * FROM {$g5['wdg_config_table']}
                        WHERE wgf_name='{$wdg_array['wgf_name']}'
                            AND wgf_country='$wgf_country'
                            AND wgf_key = '{$wgf_key}' ");
    if($row1['wgf_idx']) {
        sql_query(" UPDATE {$g5['wdg_config_table']} SET
                        wgf_key='{$wgf_key}',
                        wgf_value='{$wdg_array['wdg_value']}',
                        wgf_auto_yn='$wgf_auto_yn'
                    WHERE wgf_idx='".$row1['wgf_idx']."' ", 1);
    }
    else {
        sql_query(" INSERT INTO {$g5['wdg_config_table']} SET
                        wgf_key='{$wgf_key}',
                        wgf_name='{$wdg_array['wgf_name']}',
                        wgf_value='{$wdg_array['wgf_value']}',
                        wgf_country='$wgf_country',
                        wgf_auto_yn='$wgf_auto_yn' ", 1);
    }
}
}

//접속한 브라우저의 이름/버전을 반환해 주는 함수
if (!function_exists('wdg_browserCheck')){
function wdg_browserCheck(){
    /*
    크롬 : Chrome/Safari
    파폭 : Firefox
    익11 : Trident
    익10 : MSIE
    훼일 : Chrome/Whale/Safari
    엣지 : Chrome/Safari/Edge
    */
    $userAgent = $_SERVER["HTTP_USER_AGENT"];
    //echo $userAgent;
    if ( preg_match("/MSIE*/", $userAgent) ) {
        // 익스플로러
        if ( preg_match("/MSIE 6.0[0-9]*/", $userAgent) ) {
            $browser = "ie6"; //"explorer6";
        }else if ( preg_match("/MSIE 7.0*/", $userAgent) ) {
            $browser = "ie7"; //"explorer7";
        }else if ( preg_match("/MSIE 8.0*/", $userAgent) ) {
            $browser = "ie8"; //"explorer8";
        }else if ( preg_match("/MSIE 9.0*/", $userAgent) ) {
            $browser = "ie9"; //"explorer9";
        }else if ( preg_match("/MSIE 10.0*/", $userAgent) ) {
            $browser = "ie10"; //"explorer10";
        }else{
            // 익스플로러 기타
            $browser = "ie100"; //"explorerETC";
        }
    }
    else if(preg_match("/Trident*/", $userAgent) && preg_match("/rv:11.0*/", $userAgent) && preg_match("/Gecko*/", $userAgent)){
        $browser = "ie11"; //"explorer11";
    }

    else if ( preg_match("/Edge*/", $userAgent) ) {
        // 엣지
        $browser = "edge";
    }
    else if ( preg_match("/Firefox*/", $userAgent) ) {
        // 모질라 (파이어폭스)
        $browser = "firefox";
    }
    //else if ( preg_match("/(Mozilla)*/", $userAgent) ) {
    // // 모질라 (파이어폭스)
    // $browser = "mozilla";
    //}
    //else if ( preg_match("/(Nav|Gold|X11|Mozilla|Nav|Netscape)*/", $userAgent) ) {
    // // 네스케이프, 모질라(파이어폭스)
    // $browser = "Netscape/mozilla";
    //}
    else if ( preg_match("/Safari*/", $userAgent) && preg_match("/WOW/", $userAgent) ) {
        // 사파리
        $browser = "safari";
    }
    else if ( preg_match("/OPR*/", $userAgent) ) {
        // 오페라
        $browser = "opera";
    }
    else if ( preg_match("/DaumApps*/", $userAgent) ) {
        // daum
        $browser = "daum";
    }
    else if ( preg_match("/KAKAOTALK*/", $userAgent) ) {
        // kakaotalk
        $browser = "kakaotalk";
    }
    else if ( preg_match("/NAVER*/", $userAgent) ) {
        // kakaotalk
        $browser = "naver";
    }
    else if ( preg_match("/Whale*/", $userAgent) ) {
        // 크롬
        $browser = "whale";
    }
    else if ( preg_match("/Chrome/", $userAgent) 
        && !preg_match("/Whale/", $userAgent) 
        && !preg_match("/WOW/", $userAgent) 
        && !preg_match("/OPR/", $userAgent) 
        && !preg_match("/DaumApps/", $userAgent) 
        && !preg_match("/KAKAOTALK/", $userAgent) 
        && !preg_match("/NAVER/", $userAgent) 
        && !preg_match("/Edge/", $userAgent) ) {
        // 크롬
        $browser = "chrome";
    }
    
    else{
        $browser = "other";
    }
    return $browser; //$userAgent;//$browser;
}
}

//ie브라우저인지 확인해 주는 함수 위 browserCheck()함수 사용함
if (!function_exists('wdg_is_explorer')){
function wdg_is_explorer(){
    /*
    크롬 : Chrome/Safari
    파폭 : Firefox
    익11 : Trident
    익10 : MSIE
    훼일 : Chrome/Whale/Safari
    엣지 : Chrome/Safari/Edge
    */
    $browser_name = wdg_browserCheck();
    $ie_flag = false;
    if(preg_match("/ie/", $browser_name)){
        $ie_flag = true;
    }

    return $ie_flag;
}
}

//접속한 디바이스 타입
if (!function_exists('wdg_deviceCheck')){
function wdg_deviceCheck(){
    if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
        $device = "ipad";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') ||
        strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
        $device = "iphone";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'ipod') ||
        strstr($_SERVER['HTTP_USER_AGENT'],'ipod') ) {
        $device = "ipod";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
        $device = "blackberry";
    } else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
        $device = "android";
    } else {
        $device = "etc";
    }
    return $device;
}
}

//BP위젯 상품이미지 썸네일 생성한 후 썸네일의 URL을 반환하는 함수
if(!function_exists('wdg_get_it_thumbnail_url')){
function wdg_get_it_thumbnail_url($img, $width, $height=0, $id='', $is_crop=false){
    
    $file_url = '';

    if ( $replace_tag = run_replace('get_it_thumbnail_tag', $str='', $img, $width, $height, $id, $is_crop) ){
        return $replace_tag;
    }

    $file = G5_DATA_PATH.'/item/'.$img;
    if(is_file($file))
        $size = @getimagesize($file);

    if($size[2] < 1 || $size[2] > 3)
        return '';

    $img_width = $size[0];
    $img_height = $size[1];
    $filename = basename($file);
    $filepath = dirname($file);

    if($img_width && !$height) {
        $height = round(($width * $img_height) / $img_width);
    }

    $thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, $is_crop, 'center', true, $um_value='85/3.4/15');
    
    if($thumb) {
        $file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
    }

    return $file_url;
}
}

// 입력 폼 안내문
if(!function_exists('wdg_help')){	
function wdg_help($help="",$iup=0,$bgcolor='#ffffff',$fontcolor='#555555'){
    global $g5;
    $iupclass = ($iup) ? "iup" : 'idown';
    $str = ($help) ? '<div class="wdg_info_box"><p class="wdg_info '.$iupclass.'" style="background:'.$bgcolor.';color:'.$fontcolor.';">'.str_replace("\n", "<br>", $help).'</p></div>' : '';
    return $str;
}
}

//환경선택박스
if(!function_exists('wdg_select_selected')){
function wdg_select_selected($field, $name, $val, $no_val=0, $required=0, $disable=0){ //인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status','ok',0,1)
    $bwgf_values = explode(',', preg_replace("/\s+/", "", $field));
    if(!count($bwgf_values)) return false;
    $readonly_str = ($disable) ? 'readonly onFocus="this.initialSelect=this.selectedIndex;" onChange="this.selectedIndex=this.initialSelect;"' : '';
    if($disable)
        $select_tag = '<select '.$readonly_str.' name="'.$name.'" id="'.$name.'"'.(($required) ? ' required':'').' class="'.(($required) ? 'required':'').'">'.PHP_EOL;
    else
        $select_tag = '<select name="'.$name.'" id="'.$name.'"'.(($required) ? ' required':'').' class="'.(($required) ? 'required':'').'">'.PHP_EOL;
        
    $i = 0;
    if($no_val){ //값없는 항목이 존재할때
        $select_tag .= '<option value=""'.((!$val) ? ' selected="selected"' : '').'>선택안됨</option>'.PHP_EOL;
        $i++;
    }
    foreach ($bwgf_values as $bwgf_value) {
        list($key, $value) = explode('=', $bwgf_value);
        $selected = '';
        if($val){ //수정값이 존재하면
            if(is_int($key)){
                $selected = ((int) $val===$key) ? ' selected="selected"' : '';
            }else{
                $selected = ($val===$key) ? ' selected="selected"' : '';
            }
        }else{ //등록 또는 수정값이 존재하지 않은면
            if(!$no_val){//값없는 항목이 존재하지 않을때
                if($i == 0) $selected = ' selected="selected"';
            }
        }
        $select_tag .= '<option value="'.trim($key).'"'.$selected.'>'.trim($value).'</option>'.PHP_EOL;
        $i++;
    }
    $select_tag .= '</select>'.PHP_EOL;
    $i = 0;
    return $select_tag;
}
}

//색상/투명도 설정 input form 생성 함수
if(!function_exists('wdg_input_color')){
function wdg_input_color($name='',$value='#333333',$w='',$alpha_flag=0){
    global $g5,$config,$default,$member,$is_admin;
    
    //if($name == '') return '컬러픽커 name값이 없습니다.';
    
    $aid = wdg_get_random_string('az',4).'_'.wdg_uniqid();
    $bid = wdg_get_random_string('az',4).'_'.wdg_uniqid();
    $cid = wdg_get_random_string('az',4).'_'.wdg_uniqid();
    //그외 랜덤id값
    $did = wdg_get_random_string('az',4).'_'.wdg_uniqid();
    $eid = wdg_get_random_string('az',4).'_'.wdg_uniqid();
    
    
    if($alpha_flag){
        if(substr($value,0,1) == '#') $value = 'rgba('.wdg_rgb2hex2rgb($value).',1)';
        $input_color = (isset($value)) ? $value : 'rgba(51, 51, 51, 1)';
        //echo $value;
        $bgrgba = substr(substr($input_color,5),0,-1);//처음에 'rgba('를 잘라낸뒤 반환하고, 그다음 끝에 ')'를 잘라내고 '255, 0, 0, 0'를 반환
        $rgba_arr = explode(',',$bgrgba);
        $bgrgb = trim($rgba_arr[0]).','.trim($rgba_arr[1]).','.trim($rgba_arr[2]);
        $bga = trim($rgba_arr[3]);
        //echo $bga;
        $bg16 = ($w == 'u') ? wdg_rgb2hex2rgb($bgrgb) : '#333333';//#FF0000
    }
    else{
        if(substr($value,0,4) == 'rgba'){
            $rgb_str_arr = explode(',',substr(substr($value,5),0,-1));
            $rgb_str = $rgb_str_arr[0].','.$rgb_str_arr[1].','.$rgb_str_arr[2];
            $value = wdg_rgb2hex2rgb($rgb_str);
        }
        $input_color = ($value) ? $value : '#333333';
    }
    
    ob_start();
    include G5_USER_ADMIN_SKIN_PATH.'/form/input_color.skin.php';
    $input_content = ob_get_contents();
    ob_end_clean();

    return $input_content;
}
}

//유니크값을 반환하는 함수
if(!function_exists('wdg_uniqid')){
function wdg_uniqid(){
    $start_ran = mt_rand(0,38);
    $cnt_ran = mt_rand(4,7);
    $uniq = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    $uniq2 = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    //$uniq3 = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    //return wdg_get_random_string('az',3).$uniq.$uniq2.$uniq3;
    return wdg_get_random_string('az',3).$uniq.$uniq2;
}	
}

//랜덤문자열 생성하는 함수
/*
기본---------------------------get_random_string() . '
숫자만-------------------------get_random_string('09') . '
숫자만 30글자------------------get_random_string('09', 30) . '
소문자만-----------------------get_random_string('az') . '
대문자만-----------------------get_random_string('AZ') . '
소문자+대문자------------------get_random_string('azAZ') . '
소문자+숫자--------------------get_random_string('az09') . '
대문자+숫자--------------------get_random_string('AZ09') . '
소문자+대문자+숫자-------------get_random_string('azAZ09') . '
특수문자만---------------------get_random_string('$') . '
숫자+특수문자------------------get_random_string('09$') . '
소문자+특수문자----------------get_random_string('az$') . '
대문자+특수문자----------------get_random_string('AZ$') . '
소문자+대문자+특수문자---------get_random_string('azAZ$') . '
소문자+대문자+숫자+특수문자----get_random_string('azAZ09$') . '
*/
if(!function_exists('wdg_get_random_string')){
function wdg_get_random_string($type = '', $len = 10) {
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numeric = '0123456789'; 
    $special = '`~!@#$%^&*()-_=+\\|[{]};:\'",<.>/?';
    $key = '';
    $token = '';
    if ($type == '') {
        $key = $lowercase.$uppercase.$numeric;
    } else {
        if (strpos($type,'09') > -1) $key .= $numeric;
        if (strpos($type,'az') > -1) $key .= $lowercase; 
        if (strpos($type,'AZ') > -1) $key .= $uppercase;
        if (strpos($type,'$') > -1) $key .= $special;
    }
    
    for ($i = 0; $i < $len; $i++) {
        $token .= $key[mt_rand(0, strlen($key) - 1)];
    }
    return $token;
}
}

//색상코드 16진수를 rgb로, rgb를 16진수로 반환해주는 함수
if(!function_exists('wdg_rgb2hex2rgb')){
function wdg_rgb2hex2rgb($color){ //인수에 '#ff0000' 또는 '255,0,0'를 넣어 호출하면 된다.
    if(!$color) return false; 
    $color = trim($color); 
    $result = false; 
    if(preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)){
        $hex = str_replace('#','', $color);
        if(!$hex) return false;
        if(strlen($hex) == 3):
            $result['r'] = hexdec(substr($hex,0,1).substr($hex,0,1));
            $result['g'] = hexdec(substr($hex,1,1).substr($hex,1,1));
            $result['b'] = hexdec(substr($hex,2,1).substr($hex,2,1));
        else:
            $result['r'] = hexdec(substr($hex,0,2));
            $result['g'] = hexdec(substr($hex,2,2));
            $result['b'] = hexdec(substr($hex,4,2));
        endif;
        $result = $result['r'].','.$result['g'].','.$result['b']; //텍스트(255,0,0)로 표시하고 싶으면 주석 해제해라
    }elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)){ 
        $color = str_replace(' ','',$color);
        $rgbstr = str_replace(array(',',' ','.'), ':', $color); 
        $rgbarr = explode(":", $rgbstr);
        $result = '#';
        $result .= str_pad(dechex($rgbarr[0]), 2, "0", STR_PAD_LEFT);
        $result .= str_pad(dechex($rgbarr[1]), 2, "0", STR_PAD_LEFT);
        $result .= str_pad(dechex($rgbarr[2]), 2, "0", STR_PAD_LEFT);
        $result = strtoupper($result); 
    }else{
        $result = false;
    }

    return $result; 
}
}

//범위(range) input form 생성 함수
if(!function_exists('wdg_input_range')){
function wdg_input_range($rname='',$val='1',$w='',$min='0',$max='1',$step='0.1',$width='100',$padding_right=29,$unit=''){
    global $g5,$config,$default,$member,$is_admin;
    
    if(preg_match("/%/", $width)){
        $width = substr($width,0,-1);
        $wd_class = ' bp_wdp'.$width;
    }else{
        $wd_class = ' bp_wdx'.$width;
    }
    
    $output_show = '';
    if(!$padding_right || $padding_right == '0'){
        $output_show = 'display:none;';
        $padding_right_style='';
        $wd_class = '';
    }else{
        $padding_right_style = 'padding-right:'.$padding_right.'px;';
    }
    
    $rid = 'r_'.wdg_uniqid();
    $rinid = 'rin_'.wdg_uniqid();
    $rotid = 'rot_'.wdg_uniqid();
    
    ob_start();
    include G5_USER_ADMIN_SKIN_FORM_PATH.'/input_range.skin.php';
    $input_content = ob_get_contents();
    ob_end_clean();

    return $input_content;
}	
}

//유니크값을 반환하는 함수
if(!function_exists('wdg_uniqid')){
function wdg_uniqid(){
    $start_ran = mt_rand(0,38);
    $cnt_ran = mt_rand(4,7);
    $uniq = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    $uniq2 = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    //$uniq3 = substr(uniqid(md5(rand())),$start_ran,$cnt_ran);
    //return wdg_get_random_string('az',3).$uniq.$uniq2.$uniq3;
    return wdg_get_random_string('az',3).$uniq.$uniq2;
}	
}

//위젯 해당 첨부파일의 썸네일 삭제
if(!function_exists('delete_wdg_thumbnail')){
function delete_wdg_thumbnail($wgs_idx, $wga_type, $file)
{
    if(!$wgs_idx || !$wga_type || !$file)
        return;

    $fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
    $files = glob(G5_WDG_DATA_PATH.'/file/'.$wgs_idx.'/'.$wga_type.'/thumb-'.$fn.'*');
    if (is_array($files)) {
        foreach ($files as $filename)
            unlink($filename);
    }
}
}