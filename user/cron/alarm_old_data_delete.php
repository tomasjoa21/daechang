<?php
/*
vi /etc/crontab
10 7 * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/alarm_old_data_delete.php
*/
include_once('./_common.php');

$exist_cnt = 1000;
$sql = " DELETE FROM {$g5['alarm_table']}
            WHERE arm_idx NOT IN (
                SELECT arm_idx FROM (
                    SELECT arm_idx FROM {$g5['alarm_table']}
                        ORDER BY arm_reg_dt DESC
                    LIMIT {$exist_cnt}
                ) t
            )
";

sql_query($sql);