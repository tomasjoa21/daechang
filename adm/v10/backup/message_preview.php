<?php
$sub_menu = "200300";
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

auth_check_menu($auth, $sub_menu, 'r');

$msg_idx = isset($_REQUEST['msg_idx']) ? (int) $_REQUEST['msg_idx'] : 0;

$se = sql_fetch("select msg_subject, msg_content from {$g5['message_table']} where msg_idx = '{$msg_idx}' ");

$subject = $se['msg_subject'];
//$content = conv_content($se['msg_content'], 1) . "<hr size=0><p><span style='font-size:9pt; font-family:굴림'>▶ 더 이상 정보 수신을 원치 않으시면 [<a href='".G5_BBS_URL."/email_stop.php?mb_id=***&amp;mb_md5=***' target='_blank'>수신거부</a>] 해 주십시오.</span></p>";
$content = conv_content($se['msg_content'], 1);
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo $config['cf_title'] ?> 메일발송 테스트</title>
</head>

<body>

<h1><?php echo $subject; ?></h1>

<p>
    <?php echo $content; ?>
</p>

<p>
    <strong>주의!</strong> 이 화면에 보여지는 디자인은 실제 내용이 발송되었을 때 디자인과 다를 수 있습니다. 확인용으로만 참고하세요.
</p>

</body>
</html>