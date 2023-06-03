Lets make login message clean.
지저분한 로그인 메시지를 깔끔하게 해 버리자.

# sudo chmod -x /etc/update-motd.d/*

relogin and you will see clean page.


Ubuntu setting refered by site https://www.pincoin.co.kr/book/1/
Just follow the manual of the site.


01. nginx install
$ su -
(to to super root.)
$ sudo apt-get install nginx
$ exit
(get back to normal user.)


02. 서버 블록 설정
서버 블록 설정은 아파치의 가상 호스팅 설정과 흡사하다.

kr.co.epcs.hanjoo 주소를 위한 문서 루트 디렉토리를 생성한다.

$ sudo mkdir -p /home/hanjoo/www
웹루트 파일 및 문서를 sudo 사용자 소유권으로 하지 않고 일반 사용자 hanjoo 일반 그룹 spam 권한으로 한다.

$ sudo chown hanjoo:root /home/hanjoo/www
로그 디렉토리 생성
$ sudo mkdir -p /home/hanjoo/www/logs
웹루트 파일 및 문서를 sudo 사용자 소유권으로 하지 않고 일반 사용자 hanjoo 그리고 웹 그룹 www-data 권한으로 한다.

$ sudo chown hanjoo:www-data /home/hanjoo/www/logs
웹 문서 생성
$ sudo su hanjoo
hanjoo 사용자로 변경하여 HTML 문서를 만든다.

/home/hanjoo/www/index.html 파일을 추가한다.

<html>
    <head>
        <title>example.com</title>
    </head>
    <body>
        <h1>example.com</h1>
    </body>
</html>
서버 블록 설정
사이트 정보 파일 추가
설치 시 존재하는 default 파일을 복사해 kr.co.epcs.hanjoo을 새로 만든다.

$ sudo su hanjoo
$ sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/kr.co.epcs.hanjoo
주석을 제외하면 kr.co.epcs.hanjoo 파일의 내용은 다음과 같다.

$ sudo vi /etc/nginx/sites-available/default
------------------------------
# kr.co.epcs.hanjoo 서버 설정
#
server {
        listen 80 default_server;
        listen [::]:80 default_server;

        server_name hanjoo.epcs.co.kr;
        root /home/hanjoo/www;
        index index.html index.php
        charset utf-8;

        access_log /home/hanjoo/www/logs/access.log;
        error_log /home/hanjoo/www/logs/error.log;

        location / {
                try_files $uri $uri/ =404;
        }
        # /php and under folders have php files..
        location /php/ {
                location ~ \.php$ {
                        include snippets/fastcgi-php.conf;
                        fastcgi_pass unix:/run/php/php7.2-fpm.sock;
                        fastcgi_read_timeout 5000;
                        client_max_body_size 0;
                }
        }

}

server {
        server_name test.hanjoo.epcs.co.kr;
        root /home/hanjoo/test;
        index index.html index.php
        charset utf-8;

        access_log /home/hanjoo/test/logs/access.log;
        error_log /home/hanjoo/test/logs/error.log;

        location / {
                try_files $uri $uri/ =404;
        }
        # /php and under folders have php files..
        location /php/ {
                location ~ \.php$ {
                        include snippets/fastcgi-php.conf;
                        fastcgi_pass unix:/run/php/php7.2-fpm.sock;
                        fastcgi_read_timeout 5000;
                        client_max_body_size 0;
                }
        }

}------------------------------
하나의 서버에는 default_server 옵션을 가진 서버 블록은 유일해야 한다.
/home/hanjoo/www와 같이 정확한 문서 루트 경로 지정한다.
example.com과 kr.co.epcs.hanjoo두 주소로 접속할 수 있도록 서버 블록을 설정했다.
서버 블록 등록
$ sudo ln -s /etc/nginx/sites-available/kr.co.epcs.hanjoo /etc/nginx/sites-enabled/
$ sudo ln -s /etc/nginx/sites-available/kr.co.epcs.hanjoo.test /etc/nginx/sites-enabled/
$ sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

NGINX 설정확인
$ sudo nginx -t

NGINX 재시작
$ sudo systemctl restart nginx

사이트 접속
http://hanjoo.epcs.co.kr/
사이트가 뜨면 정상



04. PHP
파이썬/Django를 위한 우분투이지만 휴대폰본인인증 또는 결제를 위해서는 특정 디렉토리에 제한하여 PHP를 실행시킬 수도 있습니다.
본인인증 팝업을 띄우고 실행하는 과정은 PHP로 동작하고 완료 후에 콜백 URL로 Django쪽 페이지를 호출하는 방법으로 구현 가능합니다.

설치
$ su -
$ sudo apt-get install php-fpm php-pgsql
단순히 본인인증 팝업창을 띄워서 PostgreSQL 데이터베이스에 인증 정보를 저장하기 위한 최소한의 패키지 설치입니다.
error happened.
And I refered to this site.
https://askubuntu.com/questions/1190638/ubuntu-18-04-following-packages-have-unmet-dependencies-php7-2-fpm-how-t
It seems like to installed without problems.

sudo add-apt-repository ppa:ondrej/php
....
Traceback (most recent call last):
  File "/usr/bin/add-apt-repository", line 12, in <module>
    from softwareproperties.SoftwareProperties import SoftwareProperties, shortcut_handler
ModuleNotFoundError: No module named 'softwareproperties'
...
해당 파일을 python 버전을 맞춰주니까 되네..
참고: https://stackoverflow.com/questions/42386097/python-add-apt-repository-importerror-no-module-named-apt-pkg
참고 페이지 맨 하단 참고했습니다.
ls -lha /usr/bin/python*
....
$ sudo vi /usr/bin/add-apt-repository
----------
#!/usr/bin/python3.6
...
---------
retry...
sudo add-apt-repository ppa:ondrej/php

sudo apt update
sudo apt install apt-transport-https lsb-release ca-certificates
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
sudo apt update
dpkg -l | grep php | tee packages.txt
(check the content)
sudo apt install php7.2 php7.2-common php7.2-cli php7.2-fpm
sudo apt install -y php7.2-bz2 php7.2-common php7.2-cgi php7.2-cli php7.2-dba php7.2-dev libphp7.2-embed php7.2-bcmath php7.2-fpm php7.2-gmp php7.2-mysql php7.2-tidy php7.2 php7.2-sqlite3 php7.2-json php7.2-opcache php7.2-sybase php7.2-curl php7.2-ldap php7.2-phpdbg php7.2-imap php7.2-xml php7.2-xsl php7.2-intl php7.2-zip php7.2-odbc php7.2-mbstring php7.2-readline php7.2-gd php7.2-interbase php7.2-snmp php7.2-xmlrpc php7.2-soap php7.2-pspell php7.2-pgsql php7.2-enchant php7.2-recode


16.04는 7.0 버전을 설치하고 18.04는 7.2 버전을 설치합니다.
버전이 같지 않으면 본인인증 모듈의 PHP 라이브러리가 동작하지 않으므로 잘 확인해야 합니다.

PHP 허용 하위 디렉토리 설정
/home/hanjoo/www/php
디렉토리 안에서만 PHP 파일을 실행하고 싶다.

아래와 같이 /php 하위 디렉토리 위치를 정의하고 그 안에서 PHP 실행 가능하도록 잡아준다.
-----------
...
   location / {
       try_files $uri $uri/ =404;
   }

   # /php and under folders have php files..
   location /php/ {
           root /home/hanjoo/www;
           index index.php;
           location ~ \.php$ {
                   include snippets/fastcgi-php.conf;
                   fastcgi_pass unix:/run/php/php7.2-fpm.sock;
                   fastcgi_read_timeout 5000;
                   client_max_body_size 0;
           }
   }
...
-----------
location에서 이미 /php 디렉토리를 잡아줬기 때문에
root 설정에서 굳이 /home/hanjoo/www/php 디렉토리를 잡아줄 필요가 없다.

당연히 /home/hanjoo/www/php 디렉토리 안에 파일이 존재해야 404 에러가 발생하지 않는다.

# sudo mkdir -p /home/hanjoo/www/php
# cd /home/hanjoo/www
# sudo chown hanjoo.root php

PHP 재시작
아파치와 달리 NGINX가 아니라 PHP를 재시작해야 변경사항이 반영된다.

$ sudo service php7.2-fpm restart

백도어 검색
# grep -iR 'c99' /var/www/
# grep -iR 'r57' /var/www/
# find /var/www/ -name \*.php -type f -print0 | xargs -0 grep c99
# grep -RPn "(passthru|shell_exec|system|base64_decode|fopen|fclose|eval)" /var/www/


06.02 MySQL
패키지 설치
$ su -
$ sudo apt install mysql-server
$ sudo mysql_secure_installation
pass: super@ingglobal
$ exit
(get back to normal user.)


MySQL 5.7 이후 버전에서는 MySQL의 root 사용자는 auth_socket 플러그인을 이용하여 인증 로그인합니다.
$ sudo mysql

예전 버전처럼 MySQL의 root 사용자에게 비밀번호를 부여하고 로그인을 허용하기 위한 방법을 살펴봅니다.

mysql> SELECT user,authentication_string,plugin,host FROM mysql.user;
+------------------+-------------------------------------------+-----------------------+-----------+
| user             | authentication_string                     | plugin                | host      |
+------------------+-------------------------------------------+-----------------------+-----------+
| root             |                                           | auth_socket           | localhost |
| mysql.session    | *THISISNOTAVALIDPASSWORDTHATCANBEUSEDHERE | mysql_native_password | localhost |
| mysql.sys        | *THISISNOTAVALIDPASSWORDTHATCANBEUSEDHERE | mysql_native_password | localhost |
| debian-sys-maint | *DD2DA5469C071342060E617E66AA503D7A335FB0 | mysql_native_password | localhost |
+------------------+-------------------------------------------+-----------------------+-----------+
4 rows in set (0.00 sec)
예시에서 보면 root 사용자의 플러그인이 auth_socket으로 설정되어 있는 것을 확인할 수 있습니다.
이제 이것을 mysql_native_password로 변경합니다.

mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'super@ingglobal';
mysql> FLUSH PRIVILEGES;
mysql> SELECT user,authentication_string,plugin,host FROM mysql.user;
mysql> exit
이제 MySQL의 root 사용자로 아래와 같이 비밀번호를 입력으로 로그인할 수 있습니다.

$ mysql -uroot -p

/home/hanjoo/www/php/myadmin 설치
접속해 보았더니 어라..
index.php 파일을 다운로드 받네..

참고: https://mosei.tistory.com/entry/Nginx-conf-%EC%84%A4%EC%A0%95-%ED%9B%84-indexphp-%EC%A0%91%EC%86%8D%EC%8B%9C-%ED%8C%8C%EC%9D%BC%EC%9D%B4-%EB%8B%A4%EC%9A%B4%EB%A1%9C%EB%93%9C-%EB%90%A0%EB%95%8C
# sudo vi /etc/nginx/nginx.conf
-------
    # default_type application/octet-stream;
    default_type text/html;
------
다시 시작하니까 되네.
$ sudo service nginx restart
$ sudo service php7.2-fpm restart

그리고는 다시 nginx도 재시작
$ sudo systemctl restart nginx


여기까지만 하고..
Django + vue.js 개발 환경으로 세팅을 해 봐야겠다.

https://dailyheumsi.tistory.com/21?category=799302
https://leffept.tistory.com/280?category=950490


# m h dom mon dow user  command

#*/2 *  * * *   root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/cron_test.php
2 */1   * * *   root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_charge_in_sync.php
4 */1   * * *   root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_charge_out_sync.php
6 */1   * * *   root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_melting_temp_sync.php
8 */1   * * *   root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_cast_shot_sync.php
10 */1   * * *  root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_cast_shot_sub_sync.php
12 */1   * * *  root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_cast_shot_pressure_sync.php
14 */1   * * *  root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_engrave_qrcode_sync.php
16 */1   * * *  root    wget -O - -q -t 1 http://hanjoo.epcs.co.kr/php/hanjoo/user/cron/mes_xray_inspection_sync.php


CREATE TABLE `g5_1_xray_inspection` (
  `xry_idx` bigint(20) NOT NULL,
  `work_date` date DEFAULT '0000-00-00' COMMENT '작업일',
  `work_shift` int(11) NOT NULL COMMENT '주(1)/야(2)',
  `start_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '시작시각',
  `end_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '종료시각',
  `qrcode` varchar(30) NOT NULL COMMENT 'QRCode',
  `production_id` int(11) NOT NULL COMMENT '생산품ID',
  `machine_id` double NOT NULL COMMENT '설비ID',
  `machine_no` double NOT NULL COMMENT '설비번호',
  `position_1` double NOT NULL COMMENT '위치1',
  `position_2` double NOT NULL COMMENT '위치2',
  `position_3` double NOT NULL COMMENT '위치3',
  `position_4` double NOT NULL COMMENT '위치4',
  `position_5` double NOT NULL COMMENT '위치5',
  `position_6` double NOT NULL COMMENT '위치6',
  `position_7` double NOT NULL COMMENT '위치7',
  `position_8` double NOT NULL COMMENT '위치8',
  `position_9` double NOT NULL COMMENT '위치9',
  `position_10` double NOT NULL COMMENT '위치10',
  `position_11` double NOT NULL COMMENT '위치11',
  `position_12` double NOT NULL COMMENT '위치12',
  `position_13` double NOT NULL COMMENT '위치13',
  `position_14` double NOT NULL COMMENT '위치14',
  `position_15` double NOT NULL COMMENT '위치15',
  `position_16` double NOT NULL COMMENT '위치16',
  `position_17` double NOT NULL COMMENT '위치17',
  `position_18` double NOT NULL COMMENT '위치18'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

`mms_install_date` date DEFAULT '0000-00-00' COMMENT '도입일자',