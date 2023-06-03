#!/usr/bin/php
<?
// 채팅 Client로 잘 동작하네요. 여러개를 실행해도 됩니다.
// 실행: /usr/bin/php ./server3.php
set_time_limit(0);

$fh = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
       or die("create error!!n");
socket_connect($fh, "211.254.156.189", 10000) or die("connect error!!\n");

$pid = pcntl_fork();
if($pid == -1){die("could not fork");}

if($pid == 0){
       while(true){
               $in = fopen("php://stdin", "\r");
               $line = fgets($in, 255);
               $line = trim($line);
               if($line){socket_write($fh, $line);}
       }
}
else{
       while(true){
               print socket_read($fh, 1024);
       }
}
?>