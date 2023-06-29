<?php
if (!defined('_GNUBOARD_')) exit;

// if($member['mb_id'] == 'super') {
//     $_SERVER['HTTP_USER_AGENT'] = 'super_common_agent';
// }

// common 후킹
add_event('common_header','u_common_header',10);
function u_common_header(){
    global $board,$board_skin_path,$board_skin_url;

    // 관리자단 게시판 스킨 설정
    $fr_adm = preg_match("/\/adm\/v10/",$_SERVER['HTTP_REFERER']);
    if (defined('G5_IS_ADMIN') || $fr_adm) {
        // 관리자 스킨
        $unser = unserialize(stripslashes($board['bo_7']));
        // print_r3($unser);
        if( is_array($unser) ) {
            foreach ($unser as $k1=>$v1) {
                // print_r3($k1.'/'.$v1);
                $board[$k1] = htmlspecialchars($v1, ENT_QUOTES | ENT_NOQUOTES); // " 와 ' 를 html code 로 변환
            }    
        }
        // print_r3($board);
        // 모바일은 없음
        if($board['set_skin_adm']) {
            $board_skin_path    = get_skin_path('board', 'theme/'.$board['set_skin_adm']);
            $board_skin_url     = get_skin_url('board', 'theme/'.$board['set_skin_adm']);
        }
    }    
}

// Modify for converting PC mode automatically when mobile logout. It should be stayed in Mobile mode.
add_event('member_logout','u_member_logout',10);
function u_member_logout(){
    if(G5_IS_MOBILE) {
        goto_url(G5_URL.'?device=mobile');
    }
}

// 로그인 페이지로 오면 메인으로 다시 돌려보내기
add_event('member_login_tail','u_member_login_tail',10);
function u_member_login_tail(){
    global $g5;

    if($_GET['url']){
        if(preg_match("/kiosk=1/",$_GET['url']))
            set_session('ss_kiosk_yn', 1);
    }

    if($g5['file_name']=='login') {
        goto_url(G5_URL.(($_GET['url'])?'?url='.urlencode($_GET['url']):''));
    }
}

add_event('member_login_check','u_member_login_check',10);
function u_member_login_check(){
    global $g5, $mb, $link;
    
    // for a manager without mb_4, then assign default_com_idx
    if($mb['mb_level']>=6 && !$mb['mb_4']) {
        $com_idx = $g5['setting']['set_com_idx'];
    }
    // for normal member 
    else {
        $com_idx = $mb['mb_4'];
    }
    
    $c_sql = sql_fetch(" SELECT com_kosmolog_key FROM {$g5['company_table']} WHERE com_idx = '$com_idx' ");
    $com_kosmolog_key = $c_sql['com_kosmolog_key'];
    set_session('ss_com_idx', $com_idx);
    set_session('ss_com_kosmolog_key',$com_kosmolog_key);

    // 로그인 기록을 남겨요.
    $tmp_sql = " INSERT INTO {$g5['login_table']} SET
             lo_ip = '".G5_SERVER_TIME."'
             , mb_id = '{$mb['mb_id']}'
             , lo_datetime = '".G5_TIME_YMDHIS."'
             , lo_location = '".$mb['mb_name']."'
             , lo_url = '".$_SERVER['REMOTE_ADDR']."'
    ";
    sql_query($tmp_sql, FALSE);

    //모바일에서 특정URL로 로그인후 이동하려고 하니 ?url=~~~다음에 &outlogin_skin_path=가 붙는 문제가 있었다.
    if(isMobile()){
        $str = $link;
        // 우선'?url='를 찾아라
        $search_word = "production_list.php?mms_idx=";
        $url_pos = strpos($str, $search_word);
        
        if ($url_pos !== false) {
            //그 다음 '&outlogin_skin_path='를 찾고 그 이후의 문자열을 제거한 나머지를 반환
            $sch_word = "&amp;outlogin_skin_path=";
            
            $outlogin_pos = strpos($str,$sch_word);
            if($outlogin_pos !== false){
                // 특정 단어 이후의 문자열을 제거합니다.
                $result = substr($str, 0, $outlogin_pos);
                $link = $result;
            }
        }
    }

    //kosmo에 사용현황 log 전송 함수(extend/suer.02.function.php에 정의)
	// send_kosmo_log();
}

// 그누보드 5.5.8.1.2 업데이트 이후 토큰에러나는 스크립트 오류 문제 전역에 설정
add_event('tail_sub','g5_admin_csrf_token_key_global');
function g5_admin_csrf_token_key_global(){
    $var = function_exists('admin_csrf_token_key') ? admin_csrf_token_key() : '';
    $script = <<< _SCRIPT_
<script>var g5_admin_csrf_token_key = '{$var}';</script>
_SCRIPT_;
    add_javascript($script, 1);
    //echo $script;
}


// 메일 발송이 잘 안 되서 메일을 다음, Gmail쪽으로 설정함
add_replace("mail_options", "u_mail_options", G5_HOOK_DEFAULT_PRIORITY, 10);
function u_mail_options($mail, $fname, $fmail, $to, $subject, $content, $type, $file, $cc, $bcc){
    global $g5;
    // daum SMTP 이용
    // $mail->Host = "smtp.daum.net";
    // $mail->Timeout = 10;
    // $mail->SMTPAuth = true;
    // $mail->Username = "kookple";
    // $mail->Password = "khp15442549";
    // $mail->SMTPSecure = "ssl";
    // $mail->Port = 465;

    // Gmail SMTP
    $mail->IsHTML(true);
    // $mail->SMTPDebug = SMTP::DEBUG_CLIENT;
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.gmail.com'; //Set the hostname of the mail server
    $mail->Port = 587;  //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->SMTPAuth = true; //Whether to use SMTP authentication
    $mail->Username = $g5['setting']['set_gmail_address']; //Gmail Username to use for SMTP authentication
    $mail->Password = $g5['setting']['set_gmail_password'];   //Gmail Password to use for SMTP authentication
    $mail->setFrom($fmail, $fname);  //Set who the message is to be sent from (발신메일)
    $mail->addReplyTo($fmail, $fname);    //Set an alternative reply-to address (받는 메일주소를 다른 서버, 도메인으로 설정 가능)
}

