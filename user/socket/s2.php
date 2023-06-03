#!/usr/bin/php -q
<?php
// 끝없이 실행하기 위해 시간 한계를 0으로 설정한다
set_time_limit (0);

// 서버가 대기할 ip 주소와 port 번호를 설정한다.
$address = '192.168.0.2';
$port = 20480;
// 동시에 접속할 수 있는 사용자를 10명으로 한정한다.
$max_clients = 10;

// 클라이언드 정보를 얻을 배열
// 다시 말하자면, 사용자가 10명을 동시에 받아들이겠다면,
// 배열 크기를 10개로 잡아야 합니다.
$clients = Array();

// TCP 스트림 소켓 생성
$sock = socket_create(AF_INET, SOCK_STREAM, 0);

// 소켓을 아이피주소/포트에 결합
socket_bind($sock, $address, $port) or die("Could not bind to the address.\n");

// 접속을 위해 대기 시작
socket_listen($sock);

// 무한 루프 실행
while (true) {
    // 읽기위해 클라이언트 대기 소켓을 설정한다
    $read[0] = $sock;
    for ($i = 0; $i < $max_clients; $i++)
    {
      if ($client[$i]['sock'] != null)
        $read[$i + 1] = $client[$i]['sock'] ;
    }
    // // socket_select()에 블럭킹 호출을 설정한다.
    // $write = array();
    // $exception = array();
    // $ready = socket_select($read,$write,$exception,0);

    
    // if (in_array($sock, $read)) {
    //   for ($i = 0; $i < $max_clients; $i++)
    //   {
    //     if ($client[$i]['sock'] == null) {
    //       $client[$i]['sock'] = socket_accept($sock);
    //       break;
    //     }
    //     elseif ($i == $max_clients - 1)
    //       print ("너무 많은 사용자");
    //   }
    //   if (--$ready <= 0)
    //     continue;
    // } // 조건문 if in_array의 끝
    
    // // 만약 클라이언트가 쓰기를 시도하면, 바로 그것을 처리한다
    // for ($i = 0; $i < $max_clients; $i++) // for each client
    // {
    //   if (in_array($client[$i]['sock'] , $read))
    //   { // 사용자로부터 입력을 받아서..
    //     $input = socket_read($client[$i]['sock'] , 1024);
    //   // 만약 입력이 없으면...
    //     if ($input == null) {
    //       // Zero length string meaning disconnected
    //       unset($client[$i]);
    //     }
    //     $n = trim($input);
    //   // 만약 클라이언트가 'exit'를 입력하면,
    //     if ($input == 'exit') {
    //     // 요청에 따라 연결을 종료한다
    //       socket_close($client[$i]['sock']);
    //               // 만약 아니면...
    //     } elseif ($input) {
    //     // 공백문자를 제거하고,
    //       $output = preg_replace("/[ tnr]/","",$input).chr(0);
    //       // 사용에게 소켓 스트림을 통하여 문자열을 보낸다.
    //       socket_write($client[$i]['sock'],$output);
    //     }
    //   } else {
    //     // 소켓 종료
    //     socket_close($client[$i]['sock']);
    //     unset($client[$i]);
    //   }
    // }
    // sleep(1);
    print("----------------------------------\n");
} // while문 끝

// 주 소켓 종료
socket_close($sock);
?>