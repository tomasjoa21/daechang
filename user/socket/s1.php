#!/usr/bin/php -q
<?php
// 무한정 실행하기 위해 시간한계를 0으로 설정한다. 
set_time_limit (0);

// 대기할 IP 주소와 포트번호를 설정한다
// $address = '192.168.0.2';
$address = '192.168.0.2';
// $address = '127.0.0.1';
$port = 20480;

// TCP 소켓을 만든다.
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
// IP 주소와 포트번호를 소켓에 결합
socket_bind($sock, $address, $port) or die("Could not bind to address\n");
// 접속을 위해 대기를 시작한다
socket_listen($sock);

$client = socket_accept($sock);

// 클라이언트가 입력한 1024 바이트를 읽는다.
$input = socket_read($client, 1024);
print($input);

// 입력받은 문자열에서 공백을 제거한다.
$output = preg_replace("/[ tnr]/","",$input).chr(0);

// 클라이언드에 출력을 보낸다.
socket_write($client, $output);

// 자식 프로세스를 닫는다
socket_close($client);

// 주 소켓을 닫는다
socket_close($sock);
?>
