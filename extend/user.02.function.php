<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// 24시간이 넘어가도 HH:MM 형태로 표현하는 함수, 86400 > 24:00, 86400+14400 > 28:00
if(!function_exists('second_to_hhmm')){
function second_to_hhmm($time) { //convert seconds to hh:mm
    $hour = floor($time / 3600);
    $minute = strval(floor(($time % 3600) / 60));
    if ($minute == 0) {
        $minute = "00";
    } else {
        $minute = $minute;
    }
    $time = sprintf("%02d",$hour) . ":" . sprintf("%02d",$minute);
    return $time;
}
}

/*************************************************************************
PgSQL 관련 함수 모음
*************************************************************************/

// DB 연결
if(!function_exists('sql_connect_pg')){
function sql_connect_pg($host, $user, $pass, $db=G5_PGSQL_DB)
{
    $pg_link = @pg_connect(" host = $host dbname = $db user = $user password = $pass ") or die('PgSQL Host, User, Password, DB 정보에 오류가 있습니다.');
    $stat = pg_connection_status($pg_link);
    if ($stat) {
        die('Connect Error: '.$pg_link);
    } 
    return $pg_link;
}
}

if(!function_exists('sql_query_pg')){
function sql_query_pg($sql, $error=G5_DISPLAY_SQL_ERROR, $link=null)
{
    global $g5;

    if(!$link)
        $link = $g5['connect_pg'];

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);

    if ($error) {
        $result = pg_query($link, $sql) or die("<p>$sql</p> <p>error file : {$_SERVER['SCRIPT_NAME']}</p>");
    } else {
        try {
            $result = @pg_query($link, $sql);
        } catch (Exception $e) {
            $result = null;
        }
    }

    return $result;
}
}

if(!function_exists('sql_num_rows_pg')){
function sql_num_rows_pg($result)
{
    return pg_num_rows($result);
    // return pg_num_rows($result);
}
}

if(!function_exists('sql_field_names_pg')){
function sql_field_names_pg($table, $link=null)
{
    global $g5;

    if(!$link)
        $link = $g5['connect_pg'];

    $columns = array();

    $sql = "SELECT column_name, data_type, character_maximum_length
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = '".$table."'
	";
    $result = sql_query_pg($sql,1);
	while($field = sql_fetch_array_pg($result)) {
		// print_r2($field);
		// echo $field['column_name'].'<br>';
		$columns[] = $field['column_name'];
	}

    return $columns;
}
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
if(!function_exists('sql_fetch_pg')){
function sql_fetch_pg($sql, $error=G5_DISPLAY_SQL_ERROR, $link=null)
{
    global $g5;

    if(!$link)
        $link = $g5['connect_pg'];

    $result = sql_query_pg($sql, $error, $link);
    $row = sql_fetch_array_pg($result);
    return $row;
}
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
if(!function_exists('sql_fetch_array_pg')){
function sql_fetch_array_pg($result)
{
    if( ! $result) return array();

    try {
        $row = @pg_fetch_assoc($result);
    } catch (Exception $e) {
        $row = null;
    }

    return $row;
}
}

// TimescaleDB 
// get_table_pg('g5_shop_item','it_id',215021535,'it_name')	// 4번째 매개변수는 테이블명과 같으면 생략할 수 있다.
if(!function_exists('get_table_pg')){
function get_table_pg($db_table,$db_field,$db_id,$db_fields='*')
{
    global $db;

	if(!$db_table||!$db_field||!$db_id)
		return false;
    
    $table_name = 'g5_1_'.$db_table;
    $sql = " SELECT ".$db_fields." FROM ".$table_name." WHERE ".$db_field." = '".$db_id."' LIMIT 1 ";
    $row = sql_fetch_pg($sql);
    return $row;
}
}
/*************************************************************************/


// make token 함수
if(!function_exists('make_token1')){
function make_token1() {
	// 토큰 생성
	$to[] = rand(10,99);
	$to[] = rand(10,99);
	$to[] = G5_SERVER_TIME;
	$to[] = sprintf("%02d",substr($to[0]+$to[1],-2));
	$token = $to[0].$to[1].random_str(6).$to[2].$to[3];
	//echo $token.'<br>';
	return $token;
}
}


if(!function_exists('random_str')){
function random_str($length) {  
	$characters  = "0123456789";  
	$characters .= "abcdefghijklmnopqrstuvwxyz";  
	$characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
	$characters .= "_";  
		
	$string_generated = "";  
	$nmr_loops = $length;  
	while ($nmr_loops--) {  
		$string_generated .= $characters[mt_rand(0, strlen($characters) - 1)];  
	}
	return $string_generated;  
}
}

//특정범위안에서 정수를 랜덤하게 추출하는 함수
if(!function_exists('get_random_integer')){
function get_random_integer($min,$max){
	return mt_rand($min,$max);
}
}

//HEX색상을 랜덤하게 추출하는 함수
if(!function_exists('get_random_hex_color')){
function get_random_hex_color(){
	return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}
}

// Create table tr (단순한 한줄짜리 tr을 만들 경우에 사용)
// type(input(default), textarea)
// id(필드명), name(필드제목), value(값), value_type(number), required
// width(인풋박스폭), unit(인붓박스끝에단위), help(설명글), none(tr 숨김), colspan
// tr_class, th_class, td_class, tr_style, th_style, td_style, form_style
// -----------------------
// $ar['type'] = 'input';
// $ar['id'] = 'mmi_no';
// $ar['name'] = '기종번호';
// $ar['value'] = $mmi['mmi_no'];
// $ar['value_type'] = '';
// $ar['required'] = 'required';
// $ar['width'] = '';
// $ar['unit'] = '';
// $ar['help'] = 'PLC에서 설정한 생산번호입니다.(디폴트는 0입니다.)';
// $ar['colspan'] = '';
// $ar['th_class'] = '';
// $ar['td_class'] = '';
// $ar['th_style'] = '';
// $ar['td_style'] = '';
// $ar['form_style'] = '';
// $ar['form_script'] = '';
// echo create_tr_input($ar);
// unset($ar);
if(!function_exists('create_tr_input')){
function create_tr_input($arr) {

    if(!$arr['id']||!$arr['name'])
        return false;

    // tr 숨김
    $form_none = ($arr['none']) ? 'display:'.$arr['none'] : '';

    $arr['tr_class'] = $arr['tr_class'] ?: 'tr_'.$arr['id'];

    $ar['type'] = $arr['type'];
    $ar['id'] = $arr['id'];
    $ar['name'] = $arr['name'];
    $ar['value'] = $arr['value'];
    $ar['value_type'] = $arr['value_type'];
    $ar['required'] = $arr['required'];
    $ar['width'] = $arr['width'];
    $ar['unit'] = $arr['unit'];
    $ar['help'] = $arr['help'];
    $ar['colspan'] = $arr['colspan'];
    $ar['th_class'] = $arr['th_class'];
    $ar['td_class'] = $arr['td_class'];
    $ar['th_style'] = $arr['th_style'];
    $ar['td_style'] = $arr['td_style'];
    $ar['form_style'] = $arr['form_style'];
    $ar['form_script'] = $arr['form_script'];
    $td = create_td_input($ar);
    unset($ar);
    
    $str = '<tr class="'.$arr['tr_class'].'" style="'.$arr['tr_style'].';'.$form_none.';">
            '.$td.'
            </tr>';

    return $str;
}
}


// Create table tr
// type(input, textarea)
// id(필드명), name(필드제목), value(값), value_type(number), required
// width(인풋박스폭), unit(인붓박스끝에단위), help(설명글), colspan
// th_class, td_class, th_style, td_style, form_style, form_script
if(!function_exists('create_td_input')){
function create_td_input($arr) {
        
    if(!$arr['id']||!$arr['name'])
        return false;

    // 폭
    $form_width = ($arr['width']) ? 'width:'.$arr['width'] : '';
    // 단위
    $form_unit = ($arr['unit']) ? ' '.$arr['unit'] : '';
    // 설명
    $form_help = ($arr['help']) ? ' '.help($arr['help']) : '';
    // placeholder
    $arr['placeholder'] = ($arr['placeholder']) ? ' placeholder="'.$arr['placeholder'].'"' : '';
    // 한줄 두항목
    $form_span = ($arr['colspan']) ? ' colspan="'.$arr['colspan'].'"' : '';
    // value_type==number
    $arr['value'] = ($arr['value_type']=='number') ? number_format($arr['value']) : $arr['value'];

    // 각 class
    $arr['th_class'] = $arr['th_class'] ?: 'th_'.$arr['id'];
    $arr['td_class'] = $arr['td_class'] ?: 'td_'.$arr['id'];
    
    // textarea
    if($arr['type']=='textarea') {
        $item_form = '<textarea name="'.$arr['id'].'" id="'.$arr['id'].'" style="'.$arr['form_style'].';" '.$arr['form_script'].'>'.$arr['value'].'</textarea>';
    }
	// radio
    else if($arr['type']=='radio') {
		$arr['value'] = $arr['value'] ?: 0;	// default
		$arr_yes = array('1','Y'); 	// yes인 경우
		$arr['chk'] = (in_array((string)$arr['value'],$arr_yes)) ? 1 : 0;
		$arr['checked_'.$arr['chk']] = ' checked="checked"';
		$arr_yn = array('N','Y'); 	// Y,N 형태인 경우를 위해서 체크
		$arr['value_yes'] = (in_array((string)$arr['value'],$arr_yn)) ? 'Y': 1;
		$arr['value_no'] = (in_array((string)$arr['value'],$arr_yn)) ? 'N': 0;
		$item_form = '<input type="radio" name="'.$arr['id'].'" value="'.$arr['value_yes'].'" id="'.$arr['id'].'_yes" '.$arr['checked_1'].'>
						<label for="'.$arr['id'].'_yes">예</label>
						<input type="radio" name="'.$arr['id'].'" value="'.$arr['value_no'].'" id="'.$arr['id'].'_no" '.$arr['checked_0'].'>
						<label for="'.$arr['id'].'_no">아니오</label>';
    }
	// checkbox
    else if($arr['type']=='checkbox') {
		$arr['value'] = $arr['value'] ?: 0;	// default
		$arr_yes = array('1','Y'); // Y,N 형태인 경우를 위해서 체크
		$arr['checked'] = (in_array((string)$arr['value'],$arr_yes)) ? ' checked="checked"' : '';
		$arr_yn = array('N','Y'); 	// Y,N 형태인 경우를 위해서 체크
		$arr['value_yes'] = (in_array((string)$arr['value'],$arr_yn)) ? 'Y': 1;
		$arr['value_no'] = (in_array((string)$arr['value'],$arr_yn)) ? 'N': 0;
		if(!in_array((string)$arr['value'],$arr_yn)) {
			$arr['value'] = ($arr['value']) ? 1 : 0;
		}
		$item_form = '<input type="checkbox" name="'.$arr['id'].'" id="'.$arr['id'].'" '.$arr['checked'].'
						onClick="javascript:if(this.checked){this.form.'.$arr['id'].'_value.value=\''.$arr['value_yes'].'\'}else{this.form.'.$arr['id'].'_value.value=\''.$arr['value_no'].'\'}">
						<label for="'.$arr['id'].'">예</label>
					  <input type="hidden" name="'.$arr['id'].'" id="'.$arr['id'].'_value" value="'.$arr['value'].'">';
    }
    else if($arr['type']=='text') {
        $item_form = $arr['value'];
    }
    // 기본 디폴트는 INPUTBOX
    else {
        $item_form = '<input type="text" name="'.$arr['id'].'" id="'.$arr['id'].'" value="'.$arr['value'].'" '.$arr['required'].' '.$arr['placeholder'].'
                        class="frm_input '.$arr['required'].'" style="'.$arr['form_style'].';'.$form_width.'" '.$arr['form_script'].'>'.$form_unit;
    }

    $str = '<th scope="row" class="'.$arr['th_class'].'" style="'.$arr['th_style'].';">'.$arr['name'].'</th>
            <td class="'.$arr['td_class'].'" style="'.$arr['td_style'].';" '.$form_span.'>
                '.$form_help.'
                '.$item_form.'
            </td>';

    return $str;
}
}


// url에 http:// 를 붙인다
if(!function_exists('strip_http')){
function strip_http($url)
{
    if (!trim($url)) return;
    if (preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url)) {
        $url = preg_replace("/http\:\/\//","",$url);
    }
    return $url;
}
}


// 날짜 만들기
if(!function_exists('make_date')){
function make_date($dt)
{
	return make_dt($dt);
}
}


// 날짜 만들기
if(!function_exists('make_dt')){
function make_dt($dt)
{
    if(strlen($dt)>=19) {
        return substr($dt,0,19);
	}
    else if(strlen($dt)<=10) {
		if( is_numeric($dt) ) {
			return substr($dt,0,4).'-'.substr($dt,4,2).'-'.substr($dt,6,2);
		}
		else {
			return substr($dt,0,10);
		}
	}
    else
        return $dt;
}
}

// 날짜 체크 =='0000-00-00 00:00:00' 으로 체크하는 게 귀찮아서 함수로 체크
if(!function_exists('check_date')){
function check_date($dt)
{
	return check_dt($dt);
}
}


// 날짜 체크 =='0000-00-00 00:00:00' 으로 체크하는 게 귀찮아서 함수로 체크
if(!function_exists('check_dt')){
function check_dt($dt)
{
    if($dt=='0000-00-00 00:00:00'||$dt=='')
        return false;
    else
        return true;
}
}


// 한글 포함 여부 체크
if(!function_exists('includeHangul')){
function includeHangul($str)
{
    $cnt = strlen($str);
    for($i=0; $i<$cnt; $i++)
    {
        $char = ord($str[$i]);
        if($char >= 0xa1 && $char <= 0xfe)
        {
            return true;
        }
    }
    return false;
}
}

// 한글체크 함수
// if(is_hangul_char($c[0])) echo 한글;
if(!function_exists('utf8_ord')){
function utf8_ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = ord($ch[0]);
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h<=0xDF && $len>1) return ($h & 0x1F) << 6 | (ord($ch[1]) & 0x3F);
  if ($h<=0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch[1]) & 0x3F) << 6 | (ord($ch[2]) & 0x3F);
  if ($h<=0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch[1]) & 0x3F) << 12 | (ord($ch[2]) & 0x3F) << 6 | (ord($ch[3]) & 0x3F);
  return false;
}
}

if(!function_exists('is_hangul_char')){
function is_hangul_char($ch) {
  $c = utf8_ord($ch);
  if( 0x1100<=$c && $c<=0x11FF ) return true;
  if( 0x3130<=$c && $c<=0x318F ) return true;
  if( 0xAC00<=$c && $c<=0xD7A3 ) return true;
  return false;
}
}

// is_serialized 함수
if(!function_exists('is_serialized')){
function is_serialized($string) {
    return (@unserialize($string) !== false || $string == 'b:0;');
}
}


// nl2br 함수의 반대
if(!function_exists('br2nl')){
function br2nl($string) {
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}
}

// 관리자단에 상단, 왼편이 가려져 있어서 함수 따로 만들어요.
function print_r3($var)
{
    global $g5;
    if( is_array($var) ) {
        ob_start();
        print_r($var);
        $str = ob_get_contents();
        ob_end_clean();
        $g5['debug_msg'][] = nl2br(str_replace(" ", "&nbsp;", $str));
        //$g5['debug_msg'] = $str;
    }
    else {
        $g5['debug_msg'][] = $var;
	}
	// print_r2($g5['debug_msg']);
}


// 관리자 페이지 referer 체크
if(!function_exists('admin_referer_check2')){
function admin_referer_check2($return=false)
{
    global $g5;
    $referer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
    if(!$referer) {
        $msg = '정보가 올바르지 않습니다.';

        if($return)
            return $msg;
        else
            alert($msg, G5_URL);
    }

    $p = @parse_url($referer);

    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $msg = '';

    if($host != $p['host']) {
        $msg = '올바른 방법으로 이용해 주십시오.';
    }

    if( $p['path'] && ! preg_match( '/\/'.preg_quote(G5_ADMIN_DIR).'\//i', $p['path'] ) ){
        // 인트라 게시판인 경우는 통과
        $bo_table = parse_url2($_SERVER['HTTP_REFERER'],"bo_table");
        if( !in_array($bo_table, $g5['bo_table_intra']) )
            $msg = '올바른 방법으로 이용해 주십시오~';
    }

    if( $msg ){
        if($return) {
            return $msg;
        } else {
            alert($msg, G5_URL);
        }
    }
}
}


// URL에서 각종 변수 배열로 추출
// print_r2(parse_url2($url)); echo parse_url2($url,"bo_table");
if(!function_exists('parse_url2')){
function parse_url2($url,$var="") {
	global $g5;
//	$url = 'http://demo2.ycart.kr/yc_a01/bbs/board.php?bo_table=free&wr_id=86085&aa=bb';
	$ar1 = parse_url($url);
	$ar2 = pathinfo($ar1['path']);
	parse_str(html_entity_decode($ar1['query']), $ar3);
	$ar4 = array_merge($ar1,$ar2,$ar3);
	$path1 = preg_replace("|".G5_URL."|","",$url);
	$ar4['g5_path'] = parse_url3($path1,"dirname");
	//print_r2($ar4);
	if($var)
		return $ar4[$var];
	else
		return $ar4;
}
}


// URL에서 각종 변수 배열로 추출
if(!function_exists('parse_url3')){
function parse_url3($url,$var="") {
	$ar1 = parse_url($url);
	$ar2 = pathinfo($ar1['path']);
	parse_str(html_entity_decode($ar1['query']), $ar3);
	$ar4 = array_merge($ar1,$ar2,$ar3);
	if($var)
		return $ar4[$var];
	else
		return $ar4;
}
}

// 기본 디비 배열 + 확장 meta 배열
// get_table('g5_shop_item','it_id',215021535,'it_name')	// 4번째 매개변수는 테이블명과 같으면 생략할 수 있다.
if(!function_exists('get_table')){
function get_table($db_table,$db_field,$db_id,$db_fields='*')
{
    global $g5;

	if(!$db_table||!$db_field||!$db_id)
		return false;
    
    // 게시판인 경우
    if($db_field=='wr_id') {
        $table_name = $g5['write_prefix'].$db_table;
    }
    else {
        $table_name = $g5[$db_table.'_table'];
    }
    
    $sql = " SELECT ".$db_fields." FROM ".$table_name." WHERE ".$db_field." = '".$db_id."' LIMIT 1 ";
    //print_r3($sql);
    //echo $sql.'<br>';
    $row = sql_fetch($sql);

    return $row;
}
}


// 기본 디비 배열 + 확장 meta 배열
// get_table_meta('g5_shop_item','it_id',215021535,'shop_item')	// 4번째 매개변수는 테이블명과 같으면 생략할 수 있다.
if(!function_exists('get_table_meta')){
function get_table_meta($db_table,$db_field,$db_id,$db_table2='')
{
    global $g5;

	if(!$db_table||!$db_field||!$db_id)
		return false;
    
    // 게시판인 경우
    if($db_field=='wr_id') {
        $table_name = $g5['write_prefix'].$db_table;
    }
    else {
        $table_name = $g5[$db_table.'_table'];
    }
    
	// db_table2가 없으면 db_table과 같은 값
    $db_table2 = (!$db_table2) ? $db_table : $db_table2;
	
    $sql = " SELECT * FROM ".$table_name." WHERE ".$db_field." = '".$db_id."' LIMIT 1 ";
    //print_r3($sql);
    //echo $sql.'<br>';
    $row = sql_fetch($sql);
    $row2 = get_meta($db_table2,$db_id);
	if(is_array($row) && is_array($row2))
        $row = array_merge($row, $row2);	// meta 값을 배열로 만들어서 원배열과 병합
    // print_r2($row);

    return $row;
}
}


// keys를 배열로 반환하는 함수
if(!function_exists('get_keys')){
function get_keys($text,$sep=':')
{
    $a = array();
    $b = explode(",",$text);
    for($i=0;$i<sizeof($b);$i++) {
        $b1 = preg_replace("/".$sep."/","",$b[$i]);
        list($k1, $v1) = explode('=', trim($b1));
        //echo $k1.'='.$v1.'<br>';
        if($k1) {
            $a[$k1] = $v1;
        }
    }
    return $a;
}
}

// keys값 중에서 해당 키,값을 반환하는 함수
if(!function_exists('get_keys_one')){
function get_keys_one($text,$key,$sep=':')
{
    $a = get_keys($text,$sep);
    return $a[$key];
}
}

// 확장 메타값 배열로 반환하는 함수
// serialized 되었다면 각 항목별로 분리해서 배열로 만듦
if(!function_exists('get_meta')){
function get_meta($db_table,$db_id,$code64=1)
{
    global $g5;
	
	if(!$db_table||!$db_id)
		return false;
	//db_table: table name, mta_db_id = xxx_idx or board name
    $sql = " SELECT mta_key, mta_value FROM {$g5['meta_table']} WHERE mta_db_table = '".$db_table."' AND mta_db_id = '".$db_id."' ";
    //echo $sql.'<br>';
	$rs = sql_query($sql,1);
    for($i=0;$row=sql_fetch_array($rs);$i++) {
        $mta2[$row['mta_key']] = $row['mta_value'];
        //echo $row['mta_key'].'='.$row['mta_value'].'<br>';
        if(is_serialized($row['mta_value'])) {
            //unset($mta2[$row['mta_key']]); // serialized된 변수는 제거
            $unser = unserialize($row['mta_value']);
            if( is_array($unser) ) {
                foreach ($unser as $k1=>$v1) {
                    //echo $k1.'='.$v1.' -------- <br>';
                    if($code64)
                        $mta2[$k1] = stripslashes64($v1);
                    else
                        $mta2[$k1] = stripslashes($v1);
                }    
            }
        }
    }
    return $mta2;
}
}

// serialized값 배열로 반환하는 함수
if(!function_exists('get_serialized')){
function get_serialized($text)
{
    $a = array();
    if(is_serialized($text)) {
        $unser = unserialize($text);
        if( is_array($unser) ) {
            foreach ($unser as $k1=>$v1) {
                //echo $k1.'='.stripslashes64($v1);.'<br>';
                $a[$k1] = stripslashes64($v1); // " 와 ' 를 html code 로 변환
            }    
        }
        return $a;
    }
    else
        return false;
}
}


// serialized값을 변경하여 반환하는 함수
// 구조 a:7:{s:5:"ct_id";s:5:"40919";s:7:"com_idx";s:4:"4015";s:11:"mb_id_saler";s:9:"seojh4210";s:13:"mb_name_saler";s:9:"서재현";s:12:"mb_id_worker";s:6:"whatis";s:14:"mb_name_worker";s:9:"강무성";s:25:"trm_idx_department_worker";s:3:"202";}
if(!function_exists('serialized_update')){
function serialized_update($key,$str,$text)
{
    if(!$key)
        return $text;
    
    $a = array();
    if(is_serialized($text)) {
        $unser = unserialize($text);
        if( is_array($unser) ) {
            foreach ($unser as $k1=>$v1) {
                //echo $k1.'='.stripslashes64($v1);.'<br>';
                $ar[] = $k1;
                if($key==$k1)
                    $a[$k1] = addslashes64($str);
                else
                    $a[$k1] = $v1;
            }
            // for를 돌고도 해당 변수가 없으면 생성
            if( !in_array($key,$ar) ) {
                $a[$key] = addslashes64($str);
            }
        }
    }
    else {
        // 해당 변수가 없으므로 생성
        $a[$key] = addslashes64($str);
    }
    return serialize($a);
}
}


// keys 값을 변경하여 반환하는 함수, 구조는 :mb_id_saler=seojh4210:,:mb_name_saler=서재현:,:mb_id_worker=whatis:,:mb_name_worker=강무성:, 형태
if(!function_exists('keys_update')){
function keys_update($key,$str,$text,$sep=':')
{
    if(!$key)
        return $text;
    
    $a = array();
    $b = explode(",",$text);
    for($i=0;$i<sizeof($b);$i++) {
        $b1 = preg_replace("/".$sep."/","",$b[$i]);
        list($k1, $v1) = explode('=', trim($b1));
        //echo $k1.'='.$v1.'<br>';
        $ar[] = $k1;
        if($k1) {
            if($key==$k1)
                $a[$k1] = $sep.$k1.'='.$str.$sep;
            else
                $a[$k1] = $sep.$k1.'='.$v1.$sep;
        }
    }
    // for를 돌고도 해당 변수가 없으면 생성
    if( !in_array($key,$ar) ) {
        $a[$key] = $sep.$key.'='.$str.$sep;
    }
    return implode(",",$a).',';
}
}


// unserialized 한 후 변수 후처리
if(!function_exists('stripslashes64')){
function stripslashes64($str) {
    return stripslashes(base64_decode($str));
}
}

// serialize 하기 전 변수 전처리
if(!function_exists('addslashes64')){
function addslashes64($str) {
    return addslashes(base64_encode($str));
}
}

// base64_encoded 되었는지 아닌지 판별 (이 함수는 사용 불가능!)
// base64_encoded된 것들이 영문자, 숫자와 구별이 안 되요.
if(!function_exists('is_base64_encoded')){
function is_base64_encoded($data)
{
    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data))
        return true;
    else
        return false;
}
}


// 상품 정보 추출
// 대체함수로 갈아타세요. get_table_meta('g5_shop_item','it_id',215021535,'shop_item')	// 4번째 매개변수는 테이블명과 같으면 생략!
if(!function_exists('get_item2')){
function get_item2($db_id)
{
    global $g5;

	if(!$db_id)
		return false;
	$db_id = trim($db_id);

    $row = sql_fetch(" select * from {$g5['g5_shop_item_table']} where it_id = '".$db_id."' ");
    $row2 = get_meta('shop_item',$db_id);
	if(is_array($row2))
		$row = array_merge($row, $row2);	// meta 값을 배열로 만들어서 원배열과 병합

    return $row;
}
}


//--- 메타 테이블 저장 ---//
if(!function_exists('meta_update')){
function meta_update($meta_array)
{
	global $g5;
	
	if(!$meta_array['mta_key'])
		return 0;

	$mta_country = ($meta_array['mta_country'])? $meta_array['mta_country']:$g5['setting']['set_default_country'];

	$row1 = sql_fetch("	SELECT * FROM {$g5['meta_table']} 
							WHERE mta_country = '$mta_country' 
								AND mta_db_table='{$meta_array['mta_db_table']}' 
								AND mta_db_id='{$meta_array['mta_db_id']}' 
								AND mta_key='{$meta_array['mta_key']}' ");
	if($row1['mta_idx']) {
		$sql = " UPDATE {$g5['meta_table']} SET 
					mta_value='{$meta_array['mta_value']}' 
				WHERE mta_idx='".$row1['mta_idx']."' ";
		sql_query($sql);
	}
	else {
		$sql = " INSERT INTO {$g5['meta_table']} SET 
					mta_country = '$mta_country', 
					mta_db_table='{$meta_array['mta_db_table']}', 
					mta_db_id='{$meta_array['mta_db_id']}', 
					mta_key='{$meta_array['mta_key']}', 
					mta_value='{$meta_array['mta_value']}', 
					mta_title='{$meta_array['mta_title']}', 
					mta_reg_dt='".G5_TIME_YMDHIS."' ";
		sql_query($sql);
		$row1['mta_idx'] = sql_insert_id();
	}
	return $row1['mta_idx'];
}
}


//--- 환경설정 변수 저장 ---//
if(!function_exists('setting_update')){
function setting_update($arr)
{
	global $g5,$config;

	$set_country = ($arr['set_country'])? $arr['set_country']:$g5['setting']['set_default_country'];
	$set_key = ($arr['set_key']) ? $arr['set_key']:'site';
	$set_auto_yn = ($arr['set_auto_yn'])? 1:0;
	$set_com_idx = ($arr['com_idx']) ?: 0;

	$row1 = sql_fetch(" SELECT * FROM {$g5['setting_table']} 
						WHERE set_name='{$arr['set_name']}' 
							AND set_country='$set_country' 
							AND com_idx='$set_com_idx' 
							AND set_key = '{$set_key}' ");
	if($row1['set_idx']) {
		sql_query(" UPDATE {$g5['setting_table']} SET 
						set_key='{$set_key}', 
						set_value='{$arr['set_value']}', 
						set_auto_yn='$set_auto_yn' 
					WHERE set_idx='".$row1['set_idx']."' ", 1);
	}
	else {
		sql_query(" INSERT INTO {$g5['setting_table']} SET 
						set_key='{$set_key}', 
						set_name='{$arr['set_name']}', 
						set_value='{$arr['set_value']}', 
						set_country='$set_country', 
						com_idx='$set_com_idx', 
						set_auto_yn='$set_auto_yn' ", 1);
	}
}
}


// Post 정보를 삭제한다.
if(!function_exists('delete_post')){
function delete_post($pst_idx, $delete=0) {
    global $g5;

	//-- 휴지통으로 넣기
	if($delete == 0) {
		$sql = " UPDATE {$g5['post_table']} SET pst_status='trash' WHERE pst_idx='$pst_idx' ";
		sql_query($sql);
	}
	//-- 완전 삭제하기
	else {		
	    //-- 관련 파일 삭제
	    delete_jt_files('post', $pst_idx);

	    //-- 메타 데이타 삭제 --//
		sql_query(" DELETE FROM {$g5['meta_table']} WHERE mta_db_table = 'post' AND mta_db_id='$pst_idx' ");

	    //-- 관련 코멘트 삭제 --//
		sql_query(" DELETE FROM {$g5['comment_table']} WHERE cmt_db_table = 'post' AND cmt_db_id='$pst_idx' ");

	    //-- 용어 연결 레코드 삭제 --//
		sql_query(" DELETE FROM {$g5['term_relation_table']} WHERE tmr_db_table = 'post' AND tmr_db_id='$pst_idx' ");

	    //-- Post 삭제 --//
		sql_query(" DELETE FROM {$g5['post_table']} WHERE pst_idx='$pst_idx' ");
	}

    return true;
}
}

// Post 정보를 얻는다.
if(!function_exists('get_post')){
function get_post($pst_idx, $fields='*')
{
    global $g5;

	//-- 기본 정보 추출 --//
	$pst = sql_fetch(" SELECT $fields FROM {$g5['post_table']} WHERE pst_idx = '$pst_idx' ");

    //-- 메타 데이타 추출 --//
	$result = sql_query(" SELECT mta_key,mta_value FROM {$g5['meta_table']} WHERE mta_db_table = 'post' AND mta_db_id='$pst_idx' ");
	for ($i=0; $row=sql_fetch_array($result); $i++)
		$pst[$row['mta_key']] = $row['mta_value'];
	
    return $pst;
}
}


// Post File 정보 배열을 얻는다.
if(!function_exists('get_jt_file_list')){
function get_jt_file_list($fle_db_table, $fle_db_id)
{
    global $g5;

    $sql = " SELECT * FROM {$g5['file_table']} WHERE fle_db_table = '$fle_db_table' AND fle_db_id = '$fle_db_id' AND fle_status = 'publish' ORDER BY fle_sort, fle_idx DESC ";
    $result = sql_query($sql);
	for($i=0;$row = sql_fetch_array($result);$i++)
	{
		$row['fle_host'] = ($row['fle_host'] == 'localhost') ? G5_URL:$row['fle_host'];

		$file[$row['fle_sort']]['href'] = $row['fle_host'].'/'.$row['fle_path'].'/'.$row['fle_name'];
		$file[$row['fle_sort']]['download'] = $row['fle_down_count'];
		$file[$row['fle_sort']]['path'] = $row['fle_path'];
		$file[$row['fle_sort']]['name'] = $row['fle_name'];
		$file[$row['fle_sort']]['name_orig'] = addslashes($row['fle_name_orig']);
		$file[$row['fle_sort']]['filesize'] = get_filesize($row['fle_filesize']);
		$file[$row['fle_sort']]['reg_dt'] = $row['fle_reg_dt'];
		$file[$row['fle_sort']]['content_orig'] = $row['fle_content'];
		$file[$row['fle_sort']]['content'] = get_text($row['fle_content']);
		$file[$row['fle_sort']]['width'] = $row['fle_width'];
		$file[$row['fle_sort']]['height'] = $row['fle_height'];
		$file[$row['fle_sort']]['type'] = $row['fle_type'];
		$file[$row['fle_sort']]['sort'] = $row['fle_sort'];
		$file[$row['fle_sort']]['mime_type'] = $row['fle_mime_type'];
	}

    return $file;
}
}

// Post File 1개 정보를 얻는다.
if(!function_exists('get_jt_file')){
function get_jt_file($fle_idx, $fields='*')
{
    global $g5;

	//-- 기본 정보 추출 --//
	$pfl = sql_fetch(" SELECT $fields FROM {$g5['file_table']} where fle_idx = '$fle_idx' ");

    //-- 메타 데이타 추출 --//
	$result = sql_query(" SELECT mta_key,mta_value FROM {$g5['meta_table']} WHERE mta_db_table = 'jt_file' AND mta_db_id='$fle_idx' ");
	for ($i=0; $row=sql_fetch_array($result); $i++)
		$pst[$row['mta_key']] = $row['mta_value'];
	
    return $pfl;
}
}


// Post File 디비로부터 썸네일 생성하고 디비 업데이트 or 디비 생성
// 변수: fle_idx, target_file_name, target_width, target_height(초기값=0), fle_type(없으면 이전값), fle_exist(초기값=0,원본삭제)
if(!function_exists('make_jt_file_thumbnail')){
function make_jt_file_thumbnail($fle_array) {
	global $g5;

	// 파일명이 존재하지 않으면 리턴
	if(!$fle_array['target_file_name'])
		return false;

	$pfl = sql_fetch(" SELECT * FROM {$g5['file_table']} WHERE fle_idx = '{$fle_array['fle_idx']}' ");

	// 디비가 존재하지 않으면 리턴
	if(!$pfl['fle_idx'])
		return false;
		
	// 썸네일 생성
    $thumb = thumbnail($pfl['fle_name'], G5_PATH.$pfl['fle_path'], G5_PATH.$pfl['fle_path'], $fle_array['target_width'], $fle_array['target_height'], false, false, 'center', true, $um_value='80/0.5/3');
    	    		
    // 파일 크기 추출
	$dir = G5_PATH.$pfl['fle_path'];
    $file = $dir.'/'.$thumb;
    if(is_file($file)) {
    	$size = @getimagesize($file);
    	$file_size = filesize($file);
		$file_parts = pathinfo($file);
    }
	else
		return false;
    
	// 파일명 변경 (변경하려고 하는 파일명이 있으면 일련번호롤 붙여준다.)
	$target_name = $fle_array['target_file_name'];	// 확장자 없는 파일명
	if(file_exists(rtrim($dir,'/').'/'.$file_parts['filename'])) {
		$a = glob($dir.'/'.$target_name.'*');
		natcasesort($a);
		$i=0;
		foreach($a as $key => $val) {
			$b[$i] = $val;
			$i++;
		}
		if(sizeof($b) > 1) {
			preg_match_all('/(\([0-9]+\))/',$b[sizeof($b)-2],$match);
			$rows = count($match,0);
			$cols = (count($match,1)/count($match,0))-1;
			$file_no = substr($match[$rows-1][$cols-1],1,-1)+1;
		}
		else
			$file_no = 1;
		
		//-- 파일명 재 설정 --//
		$new_thumb = $target_name.'('.$file_no.').'.$file_parts['extension'];
	}
	else
		$new_thumb = $target_name.'.'.$file_parts['extension'];
	
	// 썸네일 파일명 변경
	@copy($dir.'/'.$thumb, $dir.'/'.$new_thumb);
	@unlink($dir.'/'.$thumb);		

	// fle_type 없으면
	if(!$fle_array['fle_type'])
		$fle_array['fle_type'] = $pfl['fle_type'];

	$sql = " INSERT INTO {$g5['file_table']} SET
					mb_id='$pfl[mb_id]'
					, fle_db_table='$pfl[fle_db_table]'
					, fle_db_id='$pfl[fle_db_id]'
					, fle_type='$fle_array[fle_type]'
					, fle_host='$pfl[fle_host]'
					, fle_path='$pfl[fle_path]'
					, fle_name='".$new_thumb."'
					, fle_name_orig='$pfl[fle_name_orig]'
					, fle_width='".$size[0]."'
					, fle_height='".$size[1]."'
					, fle_content='$pfl[fle_content]'
					, fle_password='$pfl[fle_password]'
					, fle_down_level='$pfl[fle_down_level]'
					, fle_down_max='$pfl[fle_max]'
					, fle_expire_date='$pfl[fle_expire_date]'
					, fle_sort='$pfl[fle_sort]'
					, fle_mime_type='$pfl[fle_mime_type]'
					, fle_filesize='".$file_size."'
					, fle_token='$pfl[fle_token]'
					, fle_status='publish'
					, fle_reg_dt='".G5_TIME_YMDHIS."' ";
	sql_query($sql);
	$pfl['fle_idx'] = sql_insert_id();

	// 디비 삭제, 파일 삭제 (기본 동작은 삭제!)
	if(!$fle_array['fle_exist']) {
		delete_jt_file( array("fle_idx"=>$fle_array['fle_idx'],"fle_delete"=>1) );
	}
    
    return array("upfile_name"=>$new_thumb
					,"upfile_width"=>$size[0]
					,"upfile_height"=>$size[1]
					,"upfile_filesize"=>$file_size
					,"upfile_fle_idx"=>$pfl['fle_idx']
					,"upfile_fle_sort"=>$pfl['fle_sort']
					);
}
}


// Post File 이미지 썸네일 생성
// 변수: $fle_path, $fle_file, $target_width, $target_height(초기값=0), $fle_id, $fle_more
if(!function_exists('get_jt_file_thumbnail')){
function get_jt_file_thumbnail($fle_thumb_array)
{
    $str = '';
    $file = G5_PATH.'/'.$fle_thumb_array['fle_path'].'/'.$fle_thumb_array['fle_file'];
    if(is_file($file))
        $size = @getimagesize($file);

    if($size[2] < 1 || $size[2] > 3)
        return '';
    $img_width = $size[0];
    $img_height = $size[1];
    $filename = basename($file);
    $filepath = dirname($file);

	$fle_thumb_array['target_width'] = (!$fle_thumb_array['target_width']) ? $img_width:$fle_thumb_array['target_width'];
	$fle_thumb_array['target_height'] = (!$fle_thumb_array['target_height']) ? 0:$fle_thumb_array['target_height'];

    if($fle_thumb_array['target_width'] && !$fle_thumb_array['target_height']) {
        $fle_thumb_array['target_height'] = round(($fle_thumb_array['target_width'] * $img_height) / $img_width);
    }
	
    $thumb = thumbnail($filename, $filepath, $filepath, $fle_thumb_array['target_width'], $fle_thumb_array['target_height'], false, false, 'center', true, $um_value='80/0.5/3');

    if($thumb) {
        $file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
        $str = '<img src="'.$file_url.'" width="'.$fle_thumb_array['target_width'].'" height="'.$fle_thumb_array['target_height'].'"';
        if($fle_thumb_array['fle_id'])
            $str .= ' id="'.$fle_thumb_array['fle_id'].'"';
        if($fle_thumb_array['fle_more'])
            $str .= ' '.$fle_thumb_array['fle_more'];
        $str .= ' alt="">';
    }

    return $str;
}
}


// jt file 삭제 - 해당 디비 & 해당 row 관련 파일 전체 삭제
//관련 변수: $fle_db_table, $fle_db_id, $fle_type
// $fle_delete (1이면 DB까지 완전삭제)
// $fle_delete (0이면 상태값만 변경)
// -- $fle_delete_file (1이면 파일만 삭제, 상태값 trash)
// ---- $fle_thumb_exist (1이면 썸네일 유지, 상태값 trash & 파일은 존재)
if(!function_exists('delete_jt_files')){
function delete_jt_files($fle_array)
//function delete_jt_files($fle_db_table, $fle_db_id, $fle_delete=0)
{
    global $g5;

    if(!$fle_array['fle_db_table'] || !$fle_array['fle_db_id'])
        return;

	$sql_fle_type = ($fle_array['fle_type']) ? " AND fle_type = '".$fle_array['fle_type']."' " : "" ;

	$sql = "SELECT * FROM {$g5['file_table']} 
			WHERE fle_db_table = '".$fle_array['fle_db_table']."' AND fle_db_id = '".$fle_array['fle_db_id']."' {$sql_fle_type} ORDER BY fle_sort, fle_idx DESC 
	";

    $result = sql_query($sql);
    while ( $row = sql_fetch_array($result) ) {

		//-- 완전삭제
		if($fle_array['fle_delete'] == 1) {
			//-- 해당 파일 삭제
			@unlink(G5_PATH.$row['fle_path'].'/'.$row['fle_name']);
			if(!$fle_array['fle_thumb_exist'])
				delete_jt_file_thumbnail($row['fle_path'], $row['fle_name']);
			sql_query(" DELETE FROM {$g5['file_table']} WHERE fle_idx = '{$row['fle_idx']}' ");
		}
		else {
			//-- 파일만 삭제
			if($fle_array['fle_delete_file'] == 1) {
				//-- 해당 파일 삭제
				@unlink(G5_PATH.$row['fle_path'].'/'.$row['fle_name']);
				if(!$fle_array['fle_thumb_exist'])
					delete_jt_file_thumbnail($row['fle_path'], $row['fle_name']);
			}
			sql_query(" UPDATE {$g5['file_table']} SET fle_status = 'trash' WHERE fle_idx = '{$row['fle_idx']}' ");
		}
    }
}
}

// jt file 삭제 - 한개
//관련 변수: $fle_idx, $fle_db_table, $fle_db_id, $fle_sort, 
// $fle_delete (1이면 DB까지 완전삭제)
// $fle_delete (0이면 상태값만 변경)
// -- $fle_delete_file (1이면 파일만 삭제, 상태값 trash)
// ---- $fle_thumb_exist (1이면 썸네일 유지, 상태값 trash & 파일은 존재)
if(!function_exists('delete_jt_file')){
function delete_jt_file($fle_array)
{
    global $g5;
	
    if(!$fle_array['fle_idx'] && !$fle_array['fle_db_table'])
        return;

    //-- 파일 idx가 있는 경우
    if($fle_array['fle_idx']) {
    	$pfl = sql_fetch(" SELECT * FROM {$g5['file_table']} WHERE fle_idx = '{$fle_array['fle_idx']}' ");
    }
    //-- 없으면 해당 db_table, db_id, sort 조건으로 추출
	else if($fle_array['fle_db_table'] && $fle_array['fle_db_id']) {
    	$pfl = sql_fetch("	SELECT * FROM {$g5['file_table']} 
							WHERE fle_db_table = '{$fle_array['fle_db_table']}' 
								AND fle_db_id = '{$fle_array['fle_db_id']}' 
								AND fle_type = '{$fle_array['fle_type']}' 
								AND fle_sort = '{$fle_array['fle_sort']}' ");
	}

	//-- 완전삭제
	if($fle_array['fle_delete'] == 1) {
		//-- 해당 파일 삭제
		@unlink(G5_PATH.'/'.$pfl['fle_path'].'/'.$pfl['fle_name']);
		if(!$fle_array['fle_thumb_exist'])
			delete_jt_file_thumbnail($pfl['fle_path'], $pfl['fle_name']);
		sql_query(" DELETE FROM {$g5['file_table']} WHERE fle_idx = '{$pfl['fle_idx']}' ");
	}
	else {
		//-- 파일만 삭제
		if($fle_array['fle_delete_file'] == 1) {
			//-- 해당 파일 삭제
			@unlink(G5_PATH.'/'.$pfl['fle_path'].'/'.$pfl['fle_name']);
			if(!$fle_array['fle_thumb_exist'])
				delete_jt_file_thumbnail($pfl['fle_path'], $pfl['fle_name']);
		}
		sql_query(" UPDATE {$g5['file_table']} SET fle_status = 'trash' WHERE fle_idx = '{$pfl['fle_idx']}' ");
	}
}
}

// jt file 관련 썸네일 이미지 삭제
if(!function_exists('delete_jt_file_thumbnail')){
function delete_jt_file_thumbnail($path, $file)
{
    if(!$path || !$file)
        return;
	
	$path = G5_PATH.'/'.$path;

    $filename = preg_replace("/\.[^\.]+$/i", "", $file); // 확장자제거
    $files = glob($path.'/thumb-'.$filename.'*');
    if(is_array($files)) {
        foreach($files as $thumb_file) {
            @unlink($thumb_file);
        }
    }
}
}


// Post File 업로드 함수
//설정 변수: mb_id, fle_src_file, fle_orig_file, fle_mime_type, fle_path, fle_db_table, fle_db_id, fle_sort .... 
if(!function_exists('upload_jt_file')){
function upload_jt_file($fle_array)
{
	global $g5,$config,$member;
	
	//-- 원본 파일명이 없으면 리턴 
    if($fle_array['fle_orig_file'] == "") 
    	return false;
	
	//-- 파일명 재설정, 한글인 경우는 변경
    $fle_array['fle_dest_file'] = preg_replace("/\s+/", "", $fle_array['fle_orig_file']);
    $fle_array['fle_dest_file'] = preg_replace("/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/", "", $fle_array['fle_dest_file']);
    $fle_array['fle_dest_file'] = preg_replace_callback(
                          "/[가-힣]+/",
                          create_function('$matches', 'return base64_encode($matches[0]);'),
                          $fle_array['fle_dest_file']);
	$fle_array['fle_dest_file'] = preg_replace("/\+/", "", $fle_array['fle_dest_file']);	// 한글변환후 + 기호가 있으면 제거해야 함
	$fle_array['fle_dest_file'] = preg_replace("/\//", "", $fle_array['fle_dest_file']);	// 한글변환후 / 기호가 있으면 제거해야 함
	
	// 상태값이 있으면 업데이트
	if($fle_array['fle_status'])
		$sql_status = ", fle_status='".$fle_array['fle_status']."' ";
	else 
		$sql_status = ", fle_status='ok' ";

	//-- 파일 업로드 처리
    // print_r2($fle_array);
	$upload_file = upload_common_file($fle_array['fle_src_file'], $fle_array['fle_dest_file'], $fle_array['fle_path']);
    //print_r2($upload_file);

	$pfl = sql_fetch(" SELECT * FROM {$g5['file_table']} 
						WHERE fle_db_table = '{$fle_array['fle_db_table']}' 
							AND fle_type = '{$fle_array['fle_type']}' 
							AND fle_db_id = '{$fle_array['fle_db_id']}' AND fle_sort = '{$fle_array['fle_sort']}' ");
	if($pfl['fle_idx']) {
		//-- 파일이 존재하면 기존 파일만 삭제 (같은 파일명이 로칼, 서버에 모두 존재하면 삭제가 되어 버리는군.)
		delete_jt_file( array("fle_idx"=>$pfl['fle_idx'],"fle_delete_file"=>1) );
		
		//-- 관련 디비 업데이트
		$sql = " UPDATE {$g5['file_table']} SET 
						fle_name='".$upload_file[0]."'
						, fle_name_orig='{$fle_array['fle_orig_file']}'
						, fle_width='".$upload_file[1]."'
						, fle_height='".$upload_file[2]."'
						, fle_filesize='".$upload_file[3]."'
						{$sql_status}
						WHERE fle_idx='".$pfl['fle_idx']."' ";
		sql_query($sql);
	}
	else {
		
		//-- pst_host 설정
		$fle_array['fle_host'] = ($fle_array['fle_host']) ? $fle_array['fle_host']:'localhost'; 
		
		//-- pst_expire_date 설정
		$fle_array['fle_expire_date'] = ($fle_array['fle_expire_date']) ? $fle_array['fle_expire_date']:'9999-12-31';
		
		// 파일의 mime_type 추출
		if(!$fle_array['fle_mime_type'])
			$fle_array['fle_mime_type'] = mime_content_type($fle_array['filename']); 

		$sql = " INSERT INTO {$g5['file_table']} SET
						mb_id='$fle_array[mb_id]'
						, fle_db_table='$fle_array[fle_db_table]'
						, fle_db_id='$fle_array[fle_db_id]'
						, fle_type='$fle_array[fle_type]'
						, fle_host='$fle_array[fle_host]'
						, fle_path='$fle_array[fle_path]'
						, fle_name='".$upload_file[0]."'
						, fle_name_orig='$fle_array[fle_orig_file]'
						, fle_width='".$upload_file[1]."'
						, fle_height='".$upload_file[2]."'
						, fle_content='$fle_array[fle_content]'
						, fle_password='$fle_array[fle_password]'
						, fle_down_level='$fle_array[fle_down_level]'
						, fle_down_max='$fle_array[fle_max]'
						, fle_expire_date='$fle_array[fle_expire_date]'
						, fle_sort='$fle_array[fle_sort]'
						, fle_mime_type='$fle_array[fle_mime_type]'
						, fle_filesize='".$upload_file[3]."'
						, fle_token='$fle_array[fle_token]'
						{$sql_status}
						, fle_reg_dt='".G5_TIME_YMDHIS."' ";
		sql_query($sql);
		$pfl['fle_idx'] = sql_insert_id();
	}

    //$fle_return[0] = $upload_file[0];
    //$fle_return[1] = $upload_file[1];
    //$fle_return[2] = $upload_file[2];
    //$fle_return[3] = $upload_file[3];
    //$fle_return[4] = $pfl['fle_idx'];
    //return $fle_return;
    return array("upfile_name"=>$upload_file[0]
					,"upfile_width"=>$upload_file[1]
					,"upfile_height"=>$upload_file[2]
					,"upfile_filesize"=>$upload_file[3]
					,"upfile_fle_idx"=>$pfl['fle_idx']
					,"upfile_fle_sort"=>$fle_array['fle_sort']
					);
}
}

// 파일을 업로드 함
if(!function_exists('upload_common_file')){
function upload_common_file($srcfile, $destfile, $dir)
{
    if ($destfile == "") return false;

	// 디렉토리가 없다면 생성 (퍼미션도 변경!)
	@mkdir(G5_PATH.$dir, G5_DIR_PERMISSION);
	@chmod(G5_PATH.$dir, G5_DIR_PERMISSION);

    //-- 디렉토리 재설정
	$dir = G5_PATH.$dir;
	
	//-- 디렉토리내 동일 파일명이 존재하면 일련번호 붙인 형태로 생성하고 파일명 리턴
	$file_parts = pathinfo($dir.'/'.$destfile);
	$file_name = $file_parts['filename'];
	$full_name = $file_name.'.'.$file_parts['extension'];
	$file_name_with_path = rtrim($dir,'/').'/'.$full_name;
	
	if(file_exists($file_name_with_path)) {
		$a = glob($dir.'/'.$file_name.'*');
		natcasesort($a);
		$i=0;
		foreach($a as $key => $val) {
            //echo "/".$file_name."\(/i".'<br>';
            if( preg_match("/".$file_name."\(/i",$val) ) {
                $b[$i] = $val;
                $i++;
            }
		}
		//if(sizeof($b) > 1) {
		if(sizeof($b)) {
			preg_match_all('/(\([0-9]+\))/',$b[sizeof($b)-1],$match);
			$rows = count($match,0);
			$cols = (count($match,1)/count($match,0))-1;
			$file_no = substr($match[$rows-1][$cols-1],1,-1)+1;
		}
		else
			$file_no = 1;
		
		//-- 파일명 재 설정 --//
		$full_name = $file_name.'('.$file_no.').'.$file_parts['extension'];
	}
	else  
		$full_name = $destfile;
    
    // 업로드 한후 , 퍼미션을 변경함
	// echo $srcfile.',  '.$dir.'/'.$full_name.'<br>';
    @move_uploaded_file($srcfile, $dir.'/'.$full_name);
    @chmod($dir.'/'.$full_name, G5_FILE_PERMISSION);

	$size = @getimagesize($dir.'/'.$full_name);
	$file_size = filesize($dir.'/'.$destfile);

    return array($full_name,$size[0],$size[1],$file_size);
}
}

//멀티일반파일(이미지가 아닌 파일[복수파일])업로드
//인수(1:파일배열, )
if(!function_exists('upload_multi_file')){
function upload_multi_file($_files=array(),$tbl='',$idx=0,$fle_type=''){
	global $g5,$config,$member;
	//echo count($_files['name']);
	$f_flag = (!count($_files['name']) || !$_files['name'][0]) ? false : true;
	if($f_flag){
		for($i=0;$i<count($_files['name']);$i++) {
			if ($_files['name'][$i]) {
				$upfile_info = upload_insert_file(array("fle_idx"=>$fle_idx
									,"mb_id"=>$member['mb_id']
									,"fle_src_file"=>$_files['tmp_name'][$i]
									,"fle_orig_file"=>$_files['name'][$i]
									,"fle_mime_type"=>$_files['type'][$i]
									,"fle_content"=>''
									,"fle_path"=>'/data/'.$fle_type		//<---- 저장 디렉토리
									,"fle_db_table"=>$tbl
									,"fle_db_id"=>$idx
									,"fle_type"=>$fle_type
									,"fle_sort"=>$i
				));
				//print_r2($upfile_info);
			}
		}
	}//if($f_flag)
}
}


//fle_idx로 파일삭제하기
if(!function_exists('delete_idx_file')) {
function delete_idx_file($fle_idx_array=array()) {
	global $g5;
	//print_r2($fle_idx_array);
	foreach($fle_idx_array as $k=>$v) {
		$fr = sql_fetch(" SELECT fle_path, fle_name FROM {$g5['file_table']} WHERE fle_idx = '{$v}' ");
		@unlink(G5_PATH.'/'.$fr['fle_path'].'/'.$fr['fle_name']);
		delete_jt_file_thumbnail($fr['fle_path'], $fr['fle_name']);
		sql_query(" DELETE FROM {$g5['file_table']} WHERE fle_idx = '{$v}' ");
	}
}
}

// Post File 업로드 함수
//설정 변수: mb_id, fle_src_file, fle_orig_file, fle_mime_type, fle_path, fle_db_table, fle_db_id, fle_sort .... 
if(!function_exists('upload_insert_file')){
function upload_insert_file($fle_array){
	global $g5,$config,$member;
	
	//-- 원본 파일명이 없으면 리턴 
	if($fle_array['fle_orig_file'] == "") 
		return false;
	
	//-- 파일명 재설정, 한글인 경우는 변경
	$fle_array['fle_dest_file'] = preg_replace("/\s+/", "", $fle_array['fle_orig_file']);
	$fle_array['fle_dest_file'] = preg_replace("/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/", "", $fle_array['fle_dest_file']);
	$fle_array['fle_dest_file'] = preg_replace_callback(
							"/[가-힣]+/",
							create_function('$matches', 'return base64_encode($matches[0]);'),
							$fle_array['fle_dest_file']);
	$fle_array['fle_dest_file'] = preg_replace("/\+/", "", $fle_array['fle_dest_file']);	// 한글변환후 + 기호가 있으면 제거해야 함
	$fle_array['fle_dest_file'] = preg_replace("/\//", "", $fle_array['fle_dest_file']);	// 한글변환후 / 기호가 있으면 제거해야 함
	
	// 상태값이 있으면 업데이트
	if($fle_array['fle_status'])
		$sql_status = ", fle_status='".$fle_array['fle_status']."' ";
	else 
		$sql_status = ", fle_status='ok' ";
	// 파일의 mime_type 추출
	if(!$fle_array['fle_mime_type']){
		$fle_array['fle_mime_type'] = @mime_content_type2($fle_array['fle_orig_file']); 
	}
	//print_r2($fle_array);exit;
	//-- 파일 업로드 처리
	$upload_file = upload_common_file($fle_array['fle_src_file'], $fle_array['fle_dest_file'], $fle_array['fle_path']);
	//print_r2($upload_file);

	
		
	//-- pst_host 설정
	$fle_array['fle_host'] = ($fle_array['fle_host']) ? $fle_array['fle_host']:'localhost'; 
	
	//-- pst_expire_date 설정
	$fle_array['fle_expire_date'] = ($fle_array['fle_expire_date']) ? $fle_array['fle_expire_date']:'9999-12-31';
	

	$sql = " INSERT INTO {$g5['file_table']} SET
					mb_id='$fle_array[mb_id]'
					, fle_db_table='$fle_array[fle_db_table]'
					, fle_db_id='$fle_array[fle_db_id]'
					, fle_type='$fle_array[fle_type]'
					, fle_host='$fle_array[fle_host]'
					, fle_path='$fle_array[fle_path]'
					, fle_name='".$upload_file[0]."'
					, fle_name_orig='$fle_array[fle_orig_file]'
					, fle_width='".$upload_file[1]."'
					, fle_height='".$upload_file[2]."'
					, fle_content='$fle_array[fle_content]'
					, fle_password='$fle_array[fle_password]'
					, fle_down_level='$fle_array[fle_down_level]'
					, fle_down_max='$fle_array[fle_max]'
					, fle_expire_date='$fle_array[fle_expire_date]'
					, fle_sort='$fle_array[fle_sort]'
					, fle_mime_type='$fle_array[fle_mime_type]'
					, fle_filesize='".$upload_file[3]."'
					, fle_token='$fle_array[fle_token]'
					{$sql_status}
					, fle_reg_dt='".G5_TIME_YMDHIS."' ";
	sql_query($sql);
	$fle_idx = sql_insert_id();

	//$fle_return[0] = $upload_file[0];
	//$fle_return[1] = $upload_file[1];
	//$fle_return[2] = $upload_file[2];
	//$fle_return[3] = $upload_file[3];
	//$fle_return[4] = $pfl['fle_idx'];
	//return $fle_return;
	return array("upfile_name"=>$upload_file[0]
					,"upfile_width"=>$upload_file[1]
					,"upfile_height"=>$upload_file[2]
					,"upfile_filesize"=>$upload_file[3]
					,"upfile_fle_idx"=>$fle_idx
					,"upfile_fle_sort"=>$fle_array['fle_sort']
					);
}
}


//--- 용여 관계 변수 저장 ---//
//-- 관련 변수: tmr_db_table, tmr_db_key, trm_idx, tmr_db_id, dup_permit(1->복수허용)
if(!function_exists('term_relation_update')){
function term_relation_update($trm_array)
{
	global $g5,$config;

	$taxonomy1 = sql_fetch(" SELECT trm_taxonomy FROM {$g5['term_table']} WHERE trm_idx='{$trm_array['trm_idx']}' ");
	
	//-- prod_tags 같은 것들은 복수개이어도 괜찮음
	if($trm_array['dup_permit']) {
		$row1 = sql_fetch(" SELECT tmr_idx,trm_idx FROM {$g5['term_relation_table']} 
							WHERE tmr_db_table='{$trm_array['tmr_db_table']}'
								AND tmr_db_key='{$trm_array['tmr_db_key']}'
								AND trm_idx='{$trm_array['trm_idx']}'
								AND tmr_db_id='{$trm_array['tmr_db_id']}' ");
	}
	//-- 복수 허용 안 함, prod_type,prod_cat,dept_cat와 같은 타입은 복수 허용 안함
	else {
		$row1 = sql_fetch(" SELECT tmr_idx,trm_idx FROM {$g5['term_relation_table']} 
							WHERE tmr_db_table='{$trm_array['tmr_db_table']}'
								AND tmr_db_key='{$trm_array['tmr_db_key']}'
								AND tmr_db_id='{$trm_array['tmr_db_id']}'
								AND trm_idx in ( SELECT trm_idx FROM {$g5['term_table']} WHERE trm_taxonomy = '".$taxonomy1['trm_taxonomy']."' ) ");
	}

	if($row1['tmr_idx']) {
		$sql = " UPDATE {$g5['term_relation_table']} SET 
					trm_idx='{$trm_array['trm_idx']}'
					, tmr_db_key='{$trm_array['tmr_db_key']}' 
					, tmr_more='{$trm_array['tmr_more']}' 
				WHERE tmr_idx='".$row1['tmr_idx']."' ";
		sql_query($sql,1);
		
		//-- term 테이블 count 업데이트
		$sql = " UPDATE {$g5['term_table']} SET 
					trm_count = (SELECT count(*) FROM {$g5['term_relation_table']} WHERE trm_idx = '$row1[trm_idx]')
				WHERE trm_idx = '$row1[trm_idx]' ";
		sql_query($sql,1);
		$sql = " UPDATE {$g5['term_table']} SET 
					trm_count = (SELECT count(*) FROM {$g5['term_relation_table']} WHERE trm_idx = '{$trm_array['trm_idx']}') 
				WHERE trm_idx = '{$trm_array['trm_idx']}' ";
		sql_query($sql,1);

	}
	// taxonomy 값이 없으면 입력 안함 (Null값 입력할 필요 없음)
	else if($taxonomy1['trm_taxonomy']){

		$sql = " INSERT INTO {$g5['term_relation_table']} SET 
					tmr_db_table='{$trm_array['tmr_db_table']}'
					, tmr_db_key='{$trm_array['tmr_db_key']}'
					, trm_idx='{$trm_array['trm_idx']}'
					, tmr_db_id='{$trm_array['tmr_db_id']}'
					, tmr_more='{$trm_array['tmr_more']}' 
					, tmr_reg_dt='".G5_TIME_YMDHIS."' ";
		sql_query($sql,1);
		//echo $sql.'<br>';
		
		//-- term 테이블 count 업데이트
		$sql = " UPDATE {$g5['term_table']} SET 
					trm_count = (SELECT count(*) FROM {$g5['term_relation_table']} WHERE trm_idx = '{$trm_array['trm_idx']}') 
				WHERE trm_idx = '{$trm_array['trm_idx']}' ";
		sql_query($sql,1);
	}
	
	return $sql;
	
}
}


// 파일의 mime_type 추출하는 함수
if (!function_exists('mime_content_type')) {
function mime_content_type($filename) {
	$idx = explode( '.', $filename );
	$count_explode = count($idx);
	$idx = strtolower($idx[$count_explode-1]);
 
	$mimet = array(	
		'ai' =>'application/postscript',
		'aif' =>'audio/x-aiff',
		'aifc' =>'audio/x-aiff',
		'aiff' =>'audio/x-aiff',
		'asc' =>'text/plain',
		'atom' =>'application/atom+xml',
		'avi' =>'video/x-msvideo',
		'bcpio' =>'application/x-bcpio',
		'bmp' =>'image/bmp',
		'cdf' =>'application/x-netcdf',
		'cgm' =>'image/cgm',
		'cpio' =>'application/x-cpio',
		'cpt' =>'application/mac-compactpro',
		'crl' =>'application/x-pkcs7-crl',
		'crt' =>'application/x-x509-ca-cert',
		'csh' =>'application/x-csh',
		'css' =>'text/css',
		'dcr' =>'application/x-director',
		'dir' =>'application/x-director',
		'djv' =>'image/vnd.djvu',
		'djvu' =>'image/vnd.djvu',
		'doc' =>'application/msword',
		'dtd' =>'application/xml-dtd',
		'dvi' =>'application/x-dvi',
		'dxr' =>'application/x-director',
		'eps' =>'application/postscript',
		'etx' =>'text/x-setext',
		'ez' =>'application/andrew-inset',
		'gif' =>'image/gif',
		'gram' =>'application/srgs',
		'grxml' =>'application/srgs+xml',
		'gtar' =>'application/x-gtar',
		'hdf' =>'application/x-hdf',
		'hqx' =>'application/mac-binhex40',
		'html' =>'text/html',
		'html' =>'text/html',
		'ice' =>'x-conference/x-cooltalk',
		'ico' =>'image/x-icon',
		'ics' =>'text/calendar',
		'ief' =>'image/ief',
		'ifb' =>'text/calendar',
		'iges' =>'model/iges',
		'igs' =>'model/iges',
		'jpe' =>'image/jpeg',
		'jpeg' =>'image/jpeg',
		'jpg' =>'image/jpeg',
		'js' =>'application/x-javascript',
		'kar' =>'audio/midi',
		'latex' =>'application/x-latex',
		'm3u' =>'audio/x-mpegurl',
		'man' =>'application/x-troff-man',
		'mathml' =>'application/mathml+xml',
		'me' =>'application/x-troff-me',
		'mesh' =>'model/mesh',
		'mid' =>'audio/midi',
		'midi' =>'audio/midi',
		'mif' =>'application/vnd.mif',
		'mov' =>'video/quicktime',
		'movie' =>'video/x-sgi-movie',
		'mp2' =>'audio/mpeg',
		'mp3' =>'audio/mpeg',
		'mpe' =>'video/mpeg',
		'mpeg' =>'video/mpeg',
		'mpg' =>'video/mpeg',
		'mpga' =>'audio/mpeg',
		'ms' =>'application/x-troff-ms',
		'msh' =>'model/mesh',
		'mxu m4u' =>'video/vnd.mpegurl',
		'nc' =>'application/x-netcdf',
		'oda' =>'application/oda',
		'ogg' =>'application/ogg',
		'pbm' =>'image/x-portable-bitmap',
		'pdb' =>'chemical/x-pdb',
		'pdf' =>'application/pdf',
		'pgm' =>'image/x-portable-graymap',
		'pgn' =>'application/x-chess-pgn',
		'php' =>'application/x-httpd-php',
		'php4' =>'application/x-httpd-php',
		'php3' =>'application/x-httpd-php',
		'phtml' =>'application/x-httpd-php',
		'phps' =>'application/x-httpd-php-source',
		'png' =>'image/png',
		'pnm' =>'image/x-portable-anymap',
		'ppm' =>'image/x-portable-pixmap',
		'ppt' =>'application/vnd.ms-powerpoint',
		'ps' =>'application/postscript',
		'qt' =>'video/quicktime',
		'ra' =>'audio/x-pn-realaudio',
		'ram' =>'audio/x-pn-realaudio',
		'ras' =>'image/x-cmu-raster',
		'rdf' =>'application/rdf+xml',
		'rgb' =>'image/x-rgb',
		'rm' =>'application/vnd.rn-realmedia',
		'roff' =>'application/x-troff',
		'rtf' =>'text/rtf',
		'rtx' =>'text/richtext',
		'sgm' =>'text/sgml',
		'sgml' =>'text/sgml',
		'sh' =>'application/x-sh',
		'shar' =>'application/x-shar',
		'shtml' =>'text/html',
		'silo' =>'model/mesh',
		'sit' =>'application/x-stuffit',
		'skd' =>'application/x-koan',
		'skm' =>'application/x-koan',
		'skp' =>'application/x-koan',
		'skt' =>'application/x-koan',
		'smi' =>'application/smil',
		'smil' =>'application/smil',
		'snd' =>'audio/basic',
		'spl' =>'application/x-futuresplash',
		'src' =>'application/x-wais-source',
		'sv4cpio' =>'application/x-sv4cpio',
		'sv4crc' =>'application/x-sv4crc',
		'svg' =>'image/svg+xml',
		'swf' =>'application/x-shockwave-flash',
		't' =>'application/x-troff',
		'tar' =>'application/x-tar',
		'tcl' =>'application/x-tcl',
		'tex' =>'application/x-tex',
		'texi' =>'application/x-texinfo',
		'texinfo' =>'application/x-texinfo',
		'tgz' =>'application/x-tar',
		'tif' =>'image/tiff',
		'tiff' =>'image/tiff',
		'tr' =>'application/x-troff',
		'tsv' =>'text/tab-separated-values',
		'txt' =>'text/plain',
		'ustar' =>'application/x-ustar',
		'vcd' =>'application/x-cdlink',
		'vrml' =>'model/vrml',
		'vxml' =>'application/voicexml+xml',
		'wav' =>'audio/x-wav',
		'wbmp' =>'image/vnd.wap.wbmp',
		'wbxml' =>'application/vnd.wap.wbxml',
		'wml' =>'text/vnd.wap.wml',
		'wmlc' =>'application/vnd.wap.wmlc',
		'wmlc' =>'application/vnd.wap.wmlc',
		'wmls' =>'text/vnd.wap.wmlscript',
		'wmlsc' =>'application/vnd.wap.wmlscriptc',
		'wmlsc' =>'application/vnd.wap.wmlscriptc',
		'wrl' =>'model/vrml',
		'xbm' =>'image/x-xbitmap',
		'xht' =>'application/xhtml+xml',
		'xhtml' =>'application/xhtml+xml',
		'xls' =>'application/vnd.ms-excel',
		'xml xsl' =>'application/xml',
		'xpm' =>'image/x-xpixmap',
		'xslt' =>'application/xslt+xml',
		'xul' =>'application/vnd.mozilla.xul+xml',
		'xwd' =>'image/x-xwindowdump',
		'xyz' =>'chemical/x-xyz',
		'zip' =>'application/zip'
	);

	if (isset( $mimet[$idx] )) {
		return $mimet[$idx];
	}
	else {
		return 'application/octet-stream';
	}
}
}

// 파일의 mime_type 추출하는 함수
if (!function_exists('mime_content_type2')) {
function mime_content_type2($filename = '') {
	$idx = explode( '.', $filename );
	$count_explode = count($idx);
	$idx = strtolower($idx[$count_explode-1]);
	$mimet = array(
		'ai' =>'application/postscript',
		'aif' =>'audio/x-aiff',
		'aifc' =>'audio/x-aiff',
		'aiff' =>'audio/x-aiff',
		'asc' =>'text/plain',
		'atom' =>'application/atom+xml',
		'avi' =>'video/x-msvideo',
		'bcpio' =>'application/x-bcpio',
		'bmp' =>'image/bmp',
		'cdf' =>'application/x-netcdf',
		'cgm' =>'image/cgm',
		'cpio' =>'application/x-cpio',
		'cpt' =>'application/mac-compactpro',
		'crl' =>'application/x-pkcs7-crl',
		'crt' =>'application/x-x509-ca-cert',
		'csh' =>'application/x-csh',
		'css' =>'text/css',
		'dcr' =>'application/x-director',
		'dir' =>'application/x-director',
		'djv' =>'image/vnd.djvu',
		'djvu' =>'image/vnd.djvu',
		'doc' =>'application/msword',
		'dtd' =>'application/xml-dtd',
		'dvi' =>'application/x-dvi',
		'dxr' =>'application/x-director',
		'eps' =>'application/postscript',
		'etx' =>'text/x-setext',
		'ez' =>'application/andrew-inset',
		'gif' =>'image/gif',
		'gram' =>'application/srgs',
		'grxml' =>'application/srgs+xml',
		'gtar' =>'application/x-gtar',
		'hdf' =>'application/x-hdf',
		'hqx' =>'application/mac-binhex40',
		'html' =>'text/html',
		'html' =>'text/html',
		'ice' =>'x-conference/x-cooltalk',
		'ico' =>'image/x-icon',
		'ics' =>'text/calendar',
		'ief' =>'image/ief',
		'ifb' =>'text/calendar',
		'iges' =>'model/iges',
		'igs' =>'model/iges',
		'jpe' =>'image/jpeg',
		'jpeg' =>'image/jpeg',
		'jpg' =>'image/jpeg',
		'js' =>'application/x-javascript',
		'kar' =>'audio/midi',
		'latex' =>'application/x-latex',
		'm3u' =>'audio/x-mpegurl',
		'man' =>'application/x-troff-man',
		'mathml' =>'application/mathml+xml',
		'me' =>'application/x-troff-me',
		'mesh' =>'model/mesh',
		'mid' =>'audio/midi',
		'midi' =>'audio/midi',
		'mif' =>'application/vnd.mif',
		'mov' =>'video/quicktime',
		'movie' =>'video/x-sgi-movie',
		'mp2' =>'audio/mpeg',
		'mp3' =>'audio/mpeg',
		'mpe' =>'video/mpeg',
		'mpeg' =>'video/mpeg',
		'mpg' =>'video/mpeg',
		'mpga' =>'audio/mpeg',
		'ms' =>'application/x-troff-ms',
		'msh' =>'model/mesh',
		'mxu m4u' =>'video/vnd.mpegurl',
		'nc' =>'application/x-netcdf',
		'oda' =>'application/oda',
		'ogg' =>'application/ogg',
		'pbm' =>'image/x-portable-bitmap',
		'pdb' =>'chemical/x-pdb',
		'pdf' =>'application/pdf',
		'pgm' =>'image/x-portable-graymap',
		'pgn' =>'application/x-chess-pgn',
		'php' =>'application/x-httpd-php',
		'php4' =>'application/x-httpd-php',
		'php3' =>'application/x-httpd-php',
		'phtml' =>'application/x-httpd-php',
		'phps' =>'application/x-httpd-php-source',
		'png' =>'image/png',
		'pnm' =>'image/x-portable-anymap',
		'ppm' =>'image/x-portable-pixmap',
		'ppt' =>'application/vnd.ms-powerpoint',
		'ps' =>'application/postscript',
		'qt' =>'video/quicktime',
		'ra' =>'audio/x-pn-realaudio',
		'ram' =>'audio/x-pn-realaudio',
		'ras' =>'image/x-cmu-raster',
		'rdf' =>'application/rdf+xml',
		'rgb' =>'image/x-rgb',
		'rm' =>'application/vnd.rn-realmedia',
		'roff' =>'application/x-troff',
		'rtf' =>'text/rtf',
		'rtx' =>'text/richtext',
		'sgm' =>'text/sgml',
		'sgml' =>'text/sgml',
		'sh' =>'application/x-sh',
		'shar' =>'application/x-shar',
		'shtml' =>'text/html',
		'silo' =>'model/mesh',
		'sit' =>'application/x-stuffit',
		'skd' =>'application/x-koan',
		'skm' =>'application/x-koan',
		'skp' =>'application/x-koan',
		'skt' =>'application/x-koan',
		'smi' =>'application/smil',
		'smil' =>'application/smil',
		'snd' =>'audio/basic',
		'spl' =>'application/x-futuresplash',
		'src' =>'application/x-wais-source',
		'sv4cpio' =>'application/x-sv4cpio',
		'sv4crc' =>'application/x-sv4crc',
		'svg' =>'image/svg+xml',
		'swf' =>'application/x-shockwave-flash',
		't' =>'application/x-troff',
		'tar' =>'application/x-tar',
		'tcl' =>'application/x-tcl',
		'tex' =>'application/x-tex',
		'texi' =>'application/x-texinfo',
		'texinfo' =>'application/x-texinfo',
		'tgz' =>'application/x-tar',
		'tif' =>'image/tiff',
		'tiff' =>'image/tiff',
		'tr' =>'application/x-troff',
		'tsv' =>'text/tab-separated-values',
		'txt' =>'text/plain',
		'ustar' =>'application/x-ustar',
		'vcd' =>'application/x-cdlink',
		'vrml' =>'model/vrml',
		'vxml' =>'application/voicexml+xml',
		'wav' =>'audio/x-wav',
		'wbmp' =>'image/vnd.wap.wbmp',
		'wbxml' =>'application/vnd.wap.wbxml',
		'wml' =>'text/vnd.wap.wml',
		'wmlc' =>'application/vnd.wap.wmlc',
		'wmlc' =>'application/vnd.wap.wmlc',
		'wmls' =>'text/vnd.wap.wmlscript',
		'wmlsc' =>'application/vnd.wap.wmlscriptc',
		'wmlsc' =>'application/vnd.wap.wmlscriptc',
		'wrl' =>'model/vrml',
		'xbm' =>'image/x-xbitmap',
		'xht' =>'application/xhtml+xml',
		'xhtml' =>'application/xhtml+xml',
		'xls' =>'application/vnd.ms-excel',
		'xml xsl' =>'application/xml',
		'xpm' =>'image/x-xpixmap',
		'xslt' =>'application/xslt+xml',
		'xul' =>'application/vnd.mozilla.xul+xml',
		'xwd' =>'image/x-xwindowdump',
		'xyz' =>'chemical/x-xyz',
		'zip' =>'application/zip'
	);
	
	if (isset( $mimet[$idx] )) {
		return $mimet[$idx];
	}
	else {
		return 'application/octet-stream';
	}
}
}

// 파일을 업로드 함
if(!function_exists('upload_file2')){
	function upload_file2($srcfile, $destfile, $dir)
	{
		if ($destfile == "") return false;
		// 업로드 한후 , 퍼미션을 변경함
		@move_uploaded_file($srcfile, $dir.'/'.$destfile);
		@chmod($dir.'/'.$destfile, G5_FILE_PERMISSION);
		return true;
	}
}


//브라우저 식별 함수
function getBrowser()
{
    // check if IE 8 - 11+
    preg_match('/Trident\/(.*)/', $_SERVER['HTTP_USER_AGENT'], $matches);
    if ($matches) {
        $version = intval($matches[1]) + 4;     // Trident 4 for IE8, 5 for IE9, etc
        //return 'Internet Explorer '.($version < 11 ? $version : 'Edge');
		return 'Internet Explorer'.($version < 11 ? $version : '');
    }

    // check if Firefox, Opera, Chrome, Safari
    foreach (array('Firefox', 'OPR', 'Chrome', 'Safari') as $browser) {
        preg_match('/'.$browser.'/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if ($matches) {
            return str_replace('OPR', 'Opera', $browser);   // we don't care about the version, because this is a modern browser that updates itself unlike IE
        }
    }
}

//실제모바일 식별 함수
if (!function_exists('isMobile')) {
function isMobile(){
        $arr_browser = array ("iphone", "android", "ipod", "iemobile", "mobile", "lgtelecom", "ppc", "symbianos", "blackberry", "ipad");
        $httpUserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // 기본값으로 모바일 브라우저가 아닌것으로 간주함
        $mobile_browser = false;
        // 모바일브라우저에 해당하는 문자열이 있는 경우 $mobile_browser 를 true로 설정
        for($indexi = 0 ; $indexi < count($arr_browser) ; $indexi++){
            if(strpos($httpUserAgent, $arr_browser[$indexi]) == true){
                $mobile_browser = true;
                break;
            }
        }
        return $mobile_browser;
}
}

//키오스크 식별 함수
if (!function_exists('isKiosk')) {
function isKiosk(){
	global $g5,$member;
	$kiosk_browser = false;
	if($member['mb_10'] == '1'){
		$kiosk_browser = true;
	}
	return $kiosk_browser;
}
}

//키오스크 모드 셋팅
if (!function_exists('setKioskMode')) {
function setKioskMode(){
	global $g5,$member;
	$sql = " UPDATE {$g5['member_table']} SET mb_10 = '1' WHERE mb_id = '{$member['mb_id']}' ";
	sql_query($sql,1);
}
}

//키오스크 모드 해제
if (!function_exists('outKioskMode')) {
function outKioskMode(){
	global $g5,$member;
	$sql = " UPDATE {$g5['member_table']} SET mb_10 = '' WHERE mb_id = '{$member['mb_id']}' ";
	sql_query($sql,1);
}
}

// URL에 g5_url 다시 붙여주기
if(!function_exists('add_g5_url')){
function add_g5_url($url)
{
    if(!trim($url)) {return;}

    // http 문자가 없으면 G5_URL 을 붙여줍니다.
    $url = (substr($url,0,1)=='/' && !preg_match("/http/i",$url)) ? G5_URL.$url : set_http($url);

    return $url;
}
}

// URL에 g5_url 벗기기
if(!function_exists('strip_g5_url')){
function strip_g5_url($url)
{
    if(!trim($url)) {return;}

    // http 달고 오는 경우
    if(preg_match("/http/i",$url)) {
        $url = preg_replace("~(^https?:\/\/)".preg_replace("~(^https?:\/\/)~","",G5_URL)."~","",$url);
    }
    // http 없이 넘어오는 경우
    else {
        $url = preg_replace("~".preg_replace("~(^https?:\/\/)~","",G5_URL)."~","",$url);
    }
    
    return $url;
}
}



if(!function_exists('category_tree_array')){
	function category_tree_array($cat_code){
		$cat_arr = array();
		$cnt = strlen($cat_code)/2;
		for($i=1;$i<=$cnt;$i++){
			array_push($cat_arr,substr($cat_code,0,$i*2));
		}
		return $cat_arr;
	}
}

// 관리자단 스킨디렉토리를 SELECT 형식으로 얻음
if(!function_exists('get_skin_adm_select')){
function get_skin_adm_select($skin_gubun, $id, $name, $selected = '', $event = ''){
	global $config;
	
	$skins = array();
	if (defined('G5_THEME_PATH') && $config['cf_theme']) {
		$dirs = get_skin_adm_dir($skin_gubun, G5_THEME_PATH . '/' . G5_SKIN_DIR);
		if (!empty($dirs)) {
			foreach ($dirs as $dir) {
				$skins[] = 'theme/' . $dir;
			}
		}
    }
	
    $skins = array_merge($skins, get_skin_adm_dir($skin_gubun));
	
    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i = 0; $i < count($skins); $i++) {
		if ($i == 0) {
			$str .= "<option value=\"\">선택</option>";
        }
        if (preg_match('#^theme/(.+)$#', $skins[$i], $match)) {
			$text = '(테마) ' . $match[1];
        } else {
			$text = $skins[$i];
        }
		
        $str .= option_selected($skins[$i], $selected, $text);
    }
    $str .= "</select>";
    return $str;
}
}

// 스킨경로를 얻는다
if(!function_exists('get_skin_adm_dir')){
function get_skin_adm_dir($skin, $skin_path = G5_SKIN_PATH){
    global $g5;

    $result_array = array();

    $dirname = $skin_path . '/' . $skin . '/';
	
    if (!is_dir($dirname)) {
        return array();
    }

    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        if (is_dir($dirname . $file)) {
			if(!preg_match("/^(adm|admin)_/i",$file)){
				continue;
			}
            $result_array[] = $file;
        }
    }
    closedir($handle);
    sort($result_array);

    return $result_array;
}
}

// 관리자단 게시물 정보($write_row)를 출력하기 위하여 $list로 가공된 정보를 복사 및 가공
if(!function_exists('get_list2')){
function get_list2($write_row, $board, $skin_url, $subject_len=40){
	global $g5, $config, $g5_object;
	global $qstr, $page;

	//$t = get_microtime();

	$g5_object->set('bbs', $write_row['wr_id'], $write_row, $board['bo_table']);

	// 배열전체를 복사
	$list = $write_row;
	unset($write_row);

	$board_notice = array_map('trim', explode(',', $board['bo_notice']));
	$list['is_notice'] = in_array($list['wr_id'], $board_notice);

	if ($subject_len)
		$list['subject'] = conv_subject($list['wr_subject'], $subject_len, '…');
	else
		$list['subject'] = conv_subject($list['wr_subject'], $board['bo_subject_len'], '…');

	if( ! (isset($list['wr_seo_title']) && $list['wr_seo_title']) && $list['wr_id'] ){
		seo_title_update(get_write_table_name($board['bo_table']), $list['wr_id'], 'bbs');
	}

	// 목록에서 내용 미리보기 사용한 게시판만 내용을 변환함 (속도 향상) : kkal3(커피)님께서 알려주셨습니다.
	if ($board['bo_use_list_content'])
	{
		$html = 0;
		if (strstr($list['wr_option'], 'html1'))
			$html = 1;
		else if (strstr($list['wr_option'], 'html2'))
			$html = 2;

		$list['content'] = conv_content($list['wr_content'], $html);
	}

	$list['comment_cnt'] = '';
	if ($list['wr_comment'])
		$list['comment_cnt'] = "<span class=\"cnt_cmt\">".$list['wr_comment']."</span>";

	// 당일인 경우 시간으로 표시함
	$list['datetime'] = substr($list['wr_datetime'],0,10);
	$list['datetime2'] = $list['wr_datetime'];
	if ($list['datetime'] == G5_TIME_YMD)
		$list['datetime2'] = substr($list['datetime2'],11,5);
	else
		$list['datetime2'] = substr($list['datetime2'],5,5);
	// 4.1
	$list['last'] = substr($list['wr_last'],0,10);
	$list['last2'] = $list['wr_last'];
	if ($list['last'] == G5_TIME_YMD)
		$list['last2'] = substr($list['last2'],11,5);
	else
		$list['last2'] = substr($list['last2'],5,5);

	$list['wr_homepage'] = get_text($list['wr_homepage']);

	$tmp_name = get_text(cut_str($list['wr_name'], $config['cf_cut_name'])); // 설정된 자리수 만큼만 이름 출력
	$tmp_name2 = cut_str($list['wr_name'], $config['cf_cut_name']); // 설정된 자리수 만큼만 이름 출력
	if ($board['bo_use_sideview'])
		$list['name'] = get_sideview($list['mb_id'], $tmp_name2, $list['wr_email'], $list['wr_homepage']);
	else
		$list['name'] = '<span class="'.($list['mb_id']?'sv_member':'sv_guest').'">'.$tmp_name.'</span>';

	$reply = $list['wr_reply'];

	$list['reply'] = strlen($reply)*20;

	$list['icon_reply'] = '';
	if ($list['reply'])
		$list['icon_reply'] = '<img src="'.$skin_url.'/img/icon_reply.gif" class="icon_reply" alt="답변글">';

	$list['icon_link'] = '';
	if ($list['wr_link1'] || $list['wr_link2'])
		$list['icon_link'] = '<i class="fa fa-link" aria-hidden="true"></i> ';

	// 분류명 링크
	$list['ca_name_href'] = G5_USER_ADMIN_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;sca='.urlencode($list['ca_name']);

	$list['href'] = G5_USER_ADMIN_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].'&amp;'.$qstr;
	$list['comment_href'] = $list['href'];

	$list['icon_new'] = '';
	if ($board['bo_new'] && $list['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
		$list['icon_new'] = '<img src="'.$skin_url.'/img/icon_new.gif" class="title_icon" alt="새글"> ';

	$list['icon_hot'] = '';
	if ($board['bo_hot'] && $list['wr_hit'] >= $board['bo_hot'])
		$list['icon_hot'] = '<i class="fa fa-heart" aria-hidden="true"></i> ';

	$list['icon_secret'] = '';
	if (strstr($list['wr_option'], 'secret'))
		$list['icon_secret'] = '<i class="fa fa-lock" aria-hidden="true"></i> ';

	// 링크
	for ($i=1; $i<=G5_LINK_COUNT; $i++) {
		$list['link'][$i] = set_http(get_text($list["wr_link{$i}"]));
		$list['link_href'][$i] = G5_USER_ADMIN_BBS_URL.'/link.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].'&amp;no='.$i.$qstr;
		$list['link_hit'][$i] = (int)$list["wr_link{$i}_hit"];
	}

	// 가변 파일
	if ($board['bo_use_list_file'] || ($list['wr_file'] && $subject_len == 255) /* view 인 경우 */) {
		$list['file'] = get_file($board['bo_table'], $list['wr_id']);
	} else {
		$list['file']['count'] = $list['wr_file'];
	}

	if ($list['file']['count'])
		$list['icon_file'] = '<i class="fa fa-download" aria-hidden="true"></i> ';

	return $list;
}
}
	
// 관리자단 게시물 정보($write_row)를 출력하기 위하여 $list로 가공된 정보를 복사 및 가공
if(!function_exists('get_list3')){
function get_list3($write_row, $board, $skin_url, $subject_len=40){
	global $g5, $config, $g5_object;
	global $qstr, $page;

	//$t = get_microtime();

	$g5_object->set('bbs', $write_row['wr_id'], $write_row, $board['bo_table']);

	// 배열전체를 복사
	$list = $write_row;
	unset($write_row);
	
	$board_notice = array_map('trim', explode(',', $board['bo_notice']));
	$list['is_notice'] = in_array($list['wr_id'], $board_notice);
	

	// 분류명 링크
	$list['ca_name_href'] = G5_USER_ADMIN_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;sca='.urlencode($list['ca_name']);

	$list['href'] = G5_USER_ADMIN_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$list['wr_id'].'&amp;'.$qstr;

	$list['icon_new'] = '';
	if ($board['bo_new'] && $list['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
		$list['icon_new'] = '<img src="'.$skin_url.'/img/icon_new.gif" class="title_icon" alt="새글"> ';


	// 가변 파일
	if ($board['bo_use_list_file'] || ($list['wr_file'] && $subject_len == 255) /* view 인 경우 */) {
		$list['file'] = get_file($board['bo_table'], $list['wr_id']);
	} else {
		$list['file']['count'] = $list['wr_file'];
	}

	if ($list['file']['count'])
		$list['icon_file'] = '<i class="fa fa-download" aria-hidden="true"></i> ';

	return $list;
}
}

// 관리자단 해당 wr_id의 게시물 정보가져오기
if(!function_exists('get_view2')){
	function get_view2($write_row, $board, $skin_url){
		return get_list2($write_row, $board, $skin_url, 255);
	}
}


// 짧은 주소 형식으로 만들어서 가져온다.
if(!function_exists('get_pretty_url2')){
function get_pretty_url2($folder, $no='', $query_string='', $action=''){
    global $g5, $config;

    $boards = get_board_names();
    $segments = array();
    $url = $add_query = '';

    if( $url = run_replace('get_pretty_url2', $url, $folder, $no, $query_string, $action) ){
        return $url;
    }

    // use shortten url
    if($config['cf_bbs_rewrite']) {

        $segments[0] = G5_USER_ADMIN_URL;

        if( $folder === 'content' && $no ){     // 내용관리

            $segments[1] = $folder;

            if( $config['cf_bbs_rewrite'] > 1 ){

                $get_content = get_content_db( $no , true);
                $segments[2] = (isset($get_content['co_seo_title']) && $get_content['co_seo_title']) ? urlencode($get_content['co_seo_title']).'/' : urlencode($no);

            } else {
                $segments[2] = urlencode($no);
            }

        } else if(in_array($folder, $boards)) {     // 게시판

            $segments[1] = $folder;

            if($no) {

                if( $config['cf_bbs_rewrite'] > 1 ){

                $get_write = get_write( $g5['write_prefix'].$folder, $no , true);

                $segments[2] = (isset($get_write['wr_seo_title']) && $get_write['wr_seo_title']) ? urlencode($get_write['wr_seo_title']).'/' : urlencode($no);

                } else {
                    $segments[2] = urlencode($no);
                }

            } else if($action) {
                $segments[2] = urlencode($action);
            }

        } else {
            $segments[1] = $folder;
            if($no) {
                $no_array = explode("=", $no);
                $no_value = end($no_array);
                $segments[2] = urlencode($no_value);
            }
        }

        if($query_string) {
            // If the first character of the query string is '&', replace it with '?'.
            if(substr($query_string, 0, 1) == '&') {
                $add_query = preg_replace("/\&amp;/", "?", $query_string, 1);
            } else {
                $add_query = '?'. $query_string;
            }
        }

    } else { // don't use shortten url
        if(in_array($folder, $boards)) {
            $url = G5_USER_ADMIN_BBS_URL. '/board.php?bo_table='. $folder;
            if($no) {
                $url .= '&amp;wr_id='. $no;
            }
            if($query_string) {
                if(substr($query_string, 0, 1) !== '&') {
                    $url .= '&amp;';
                }

                $url .= $query_string;
            }
        } else {
            $url = G5_USER_ADMIN_BBS_URL. '/'.$folder.'.php';
            if($no) {
                $url .= ($folder === 'content') ? '?co_id='. $no : '?'. $no;
            }
            if($query_string) {
                $url .= (!$no ? '?' : '&amp;'). $query_string;
            }
        }

        $segments[0] = $url;
    }

    return implode('/', $segments).$add_query;
}
}


//user관련 로그인 후 이동할 URL
if(!function_exists('login_url2')){
function login_url2($url='')
{
    if (!$url) $url = G5_USER_URL;

    return urlencode(clean_xss_tags(urldecode($url)));
}
}

// user관련 $dir 을 포함하여 https 또는 http 주소를 반환한다.
if(!function_exists('https_url2')){
function https_url2($dir, $https=true)
{
    if ($https) {
        if (G5_HTTPS_DOMAIN) {
            $url = G5_HTTPS_DOMAIN.'/'.$dir;
        } else {
            $url = G5_USER_URL.'/'.$dir;
        }
    } else {
        if (G5_DOMAIN) {
            $url = G5_DOMAIN.'/'.$dir;
        } else {
            $url = G5_USER_URL.'/'.$dir;
        }
    }

    return $url;
}
}

// user관련 $dir 을 포함하여 https 또는 http 주소를 반환한다.
if(!function_exists('https_url3')){
function https_url3($dir, $https=true)
{
    if ($https) {
        if (G5_HTTPS_DOMAIN) {
            $url = G5_HTTPS_DOMAIN.'/'.$dir;
        } else {
            $url = G5_USER_ADMIN_KIOSK_URL.'/'.$dir;
        }
    } else {
        if (G5_DOMAIN) {
            $url = G5_DOMAIN.'/'.$dir;
        } else {
            $url = G5_USER_ADMIN_KIOSK_URL.'/'.$dir;
        }
    }

    return $url;
}
}


//SVG아이콘 표시 echo svg_icon(파일명,클래스,너비,높이,색상)
if(!function_exists('svg_icon')){
function svg_icon($n='_default_icon',$c='svg_icon',$w=24,$h=24,$f='#ffffff'){
	$file = G5_USER_ADMIN_SVG_PHP_PATH.'/'.$n.'.php';
	if (!file_exists($file)) {
		return $file." 파일을 찾을 수 없습니다.";
	} else {
		ob_start();
		include($file);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
}


//USER SVG아이콘 표시 echo svg_icon(파일명,클래스,너비,높이,색상)
if(!function_exists('svg_icon_user')){
function svg_icon_user($n='_default_icon',$c='svg_icon',$w=24,$h=24,$f='#ffffff'){
	$file = G5_USER_SVG_PATH.'/'.$n.'.php';
	if (!file_exists($file)) {
		return $file." 파일을 찾을 수 없습니다.";
	} else {
		ob_start();
		include($file);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
}

if(!function_exists('dt_diff')){
	function dt_diff($fromDay,$toDay,$type='day'){ //$type:'day'날수,'hour'시간,'mim'분,'sec'초
		$start_date = strtotime($fromDay);//날짜계산을 위해 string날짜를 time으로 변경
		$end_date = strtotime($toDay);//날짜계산을 위해 string날짜를 time으로 변경
		$diffTime = $end_date - $start_date;
		$diffdays = floor($diffTime/60/60/24);
		$diffhours = floor($diffTime/60/60);
		$diffmins = floor($diffTime/60);
		$diffsecs = $diffTime;
		
		$time = 0;
		if($type == 'day')
		$time = $diffdays;
		else if($type == 'hour')
		$time = $diffhours;
		else if($type == 'min')
		$time = $diffmins;
		else if($type == 'sec')
		$time = $diffsecs;
		
		return $time;
	}
}

//특정 날짜에 일수를 더한 날짜를 반환해 주는 함수
if(!function_exists('get_dayAddDate')){
function get_dayAddDate($dateInfo,$dayNum){//임채완이 재정의 한 함수(일수계산)
	$dtArr = explode('-',$dateInfo);
	$year_ = $dtArr[0];
	$month_ = $dtArr[1];
	$day_ = $dtArr[2];
	$dt = mktime(0,0,0,$month_,$day_+$dayNum,$year_);

	return date("Y-m-d",$dt);
} 
}

//연관배열을 랜덤하게 재배치해 주는 함수
if(!function_exists('key_array_shuffle')){
function key_array_shuffle($arr){
    //연관배열의 키를 랜덤하게 섞기
    $keys = array_keys($arr);
    shuffle($keys);
    //랜덤하게 섞인 키를 사용하여 새로운 배열 생성
    $shuffle_arr = array();
    foreach($keys as $key) {
        $shuffle_arr[$key] = $arr[$key];
    }
    return $shuffle_arr;
}
}


?>