#!/bin/bash
STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://daechang.epcs.co.kr)
#echo $STATUS

if [ $STATUS -eq 502 ]; then
  /etc/init.d/php7.4-fpm restart
#  echo '502'
elif [ $STATUS -eq 504 ]; then
  /etc/init.d/php7.4-fpm restart
 # echo '504'
elif [ $STATUS -ne 200 ]; then
  /etc/init.d/nginx restart
  #echo '200'
fi