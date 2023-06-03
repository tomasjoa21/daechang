<?php
$sub_menu = "200300";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'w');
check_demo();

$g5['title'] = '메시지 테스트';

$name = get_text($member['mb_name']);
$nick = $member['mb_nick'];
$mb_id = $member['mb_id'];
$mb_name = $member['mb_name'];
$mb_hp = $member['mb_hp'];
$email = $member['mb_email'];
$msg_idx = isset($_REQUEST['msg_idx']) ? (int) $_REQUEST['msg_idx'] : 0;

$sql = "select msg_subject, msg_content, msg_type from {$g5['message_table']} where msg_idx = '{$msg_idx}' ";
$msg = sql_fetch($sql);

$subject = $msg['msg_subject'];

if($msg['msg_type']=='email') {

    if (!$config['cf_email_use'])
        alert('환경설정에서 \'메일발송 사용\'에 체크하셔야 메일을 발송할 수 있습니다.');
    
    include_once(G5_LIB_PATH.'/mailer.lib.php');
    
    $content = $msg['msg_content'];
    $content = preg_replace("/{이름}/", $name, $content);
    $content = preg_replace("/{닉네임}/", $nick, $content);
    $content = preg_replace("/{회원아이디}/", $mb_id, $content);
    $content = preg_replace("/{이메일}/", $email, $content);

    $mb_md5 = md5($member['mb_id'].$member['mb_email'].$member['mb_datetime']);

    //$content = $content . '<p>더 이상 정보 수신을 원치 않으시면 [<a href="'.G5_BBS_URL.'/email_stop.php?mb_id='.$mb_id.'&amp;mb_md5='.$mb_md5.'" target="_blank">수신거부</a>] 해 주십시오.</p>';

    mailer($config['cf_title'], $member['mb_email'], $member['mb_email'], $subject, $content, 1);
    
    alert($member['mb_nick'].'('.$member['mb_email'].')님께 테스트 메일을 발송하였습니다. 확인하여 주십시오.');
    
}
else if($msg['msg_type']=='hp') {
    
    $_api_url = 'https://message.ppurio.com/api/send_utf8_json.php';     // UTF-8 인코딩과 JSON 응답용 호출 페이지
    // $_api_url = 'https://message.ppurio.com/api/send_utf8_xml.php';   // UTF-8 인코딩과 XML 응답용 호출 페이지
    // $_api_url = 'https://message.ppurio.com/api/send_utf8_text.php';  // UTF-8 인코딩과 TEXT 응답용 호출 페이지
    // $_api_url = 'https://message.ppurio.com/api/send_euckr_json.php'; // EUC-KR 인코딩과 JSON 응답용 호출 페이지
    // $_api_url = 'https://message.ppurio.com/api/send_euckr_xml.php';  // EUC-KR 인코딩과 XML 응답용 호출 페이지
    // $_api_url = 'https://message.ppurio.com/api/send_euckr_text.php'; // EUC-KR 인코딩과 TEXT 응답용 호출 페이지

    /*
     * 요청값
     */
    $_param['userid'] = trim($g5['setting']['set_ppurio_userid']);           // [필수] 뿌리오 아이디
    $_param['callback'] = hyphen_hp_remove(trim($g5['setting']['set_ppurio_callback']));    // [필수] 발신번호 - 숫자만
    $_param['phone'] = hyphen_hp_remove($mb_hp);       // [필수] 수신번호 - 여러명일 경우 |로 구분 '010********|010********|010********'
    $_param['msg'] = '[*이름*]님 안녕하세요. 테스트 발송입니다.';   // [필수] 문자내용 - 이름(names)값이 있다면 [*이름*]가 치환되서 발송됨
    $_param['names'] = $mb_name;            // [선택] 이름 - 여러명일 경우 |로 구분 '홍길동|이순신|김철수'
    //$_param['appdate'] = '20190502093000';  // [선택] 예약발송 (현재시간 기준 10분이후 예약가능)
    //$_param['subject'] = '테스트';          // [선택] 제목 (30byte)
    //$_param['file1'] = '@이미지파일경로;type=image/jpg'; // [선택] 포토발송 (jpg, jpeg만 지원  300 K  이하)

    $_curl = curl_init();
    curl_setopt($_curl,CURLOPT_URL,$_api_url);
    curl_setopt($_curl,CURLOPT_POST,true);
    curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($_curl,CURLOPT_POSTFIELDS,$_param);
    $_result = curl_exec($_curl);
    curl_close($_curl);

    $_result = json_decode($_result);

    print_r2($_result);
    alert($member['mb_name'].'('.$mb_hp.')님께 메시지를 발송하였습니다. 확인하여 주십시오.');
    
}



