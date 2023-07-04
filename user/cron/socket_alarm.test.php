<?php
// 크론 실행을 위해서는 사용자단에 파일이 존재해야 함
// sudo vi /etc/crontab
// sudo systemctl restart cron
// 5초 단위 실행 (기본 1분이므로 sh 프로그램 짜서 돌립니다.)
// * * * * * root /home/daechang/www/user/cron/s.sh
// [root@web-37 user]# wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_alarm.test.php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '소켓알람처리';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo) ? 30 : 150;  // 몇건씩 화면에 보여줄건지?
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)


$table1 = 'g5_1_socket';
$fields1 = sql_field_names_pg($table1);

// meta 데이터가 있으면 마지막 이후 5초까지만 (cron 5초 주기, 나중에 혹시 누락 일괄 처리할 때 디비 부하가 너무 높으면 안 됨, 5초씩 끊어서 입력할 것!!) ------
$sql = " SELECT * FROM g5_5_meta WHERE mta_db_table = 'pgsql/socket/alarm' AND mta_key = 'sck_idx_last' ";
$one = sql_fetch($sql,1);
// print_r2($one);
if($one['mta_idx']) {
    // print_r2($one);
    $sql_end_dt = date("Y-m-d H:i:s", strtotime($one['mta_reg_dt'])+7);   // 7 seconds from now (5초보다 조금 더 넉넉하게..)
    // echo $sql_end_dt.BR;
    $sql_where = " WHERE sck_idx > '".$one['mta_db_id']."' AND sck_dt <= '".$sql_end_dt."' ";
    // if time difference is too much(2 hour), start recent ones.
    if( strtotime($one['mta_reg_dt'])+3600*2 < G5_SERVER_TIME ) {
        $one['mta_idx'] = 0;
    }
}
if(!$one['mta_idx']) {
    $sql_start_dt = date("Y-m-d H:i:s", G5_SERVER_TIME-5);   // 5 seconds ago
    $sql_where = " WHERE sck_dt >= '".$sql_start_dt."' ";
}

// $sql_where = " WHERE sck_dt >= '2023-05-22 10:00:21' ";     // Test <<<<<<<<<<<<<<<<<<<<
$sql_where = " WHERE sck_idx >= 25734352 AND sck_idx <= 25734356 ";     // Test <<<<<<<<<<<<<<<<<<<<
$sql = " SELECT * FROM g5_1_socket {$sql_where} ORDER BY sck_dt ";
// echo $sql.BR;
// exit;
$rs = sql_query_pg($sql,1);
?>
<style>
#hd_login_msg {display:none;}
</style>

<span style='font-size:9pt;'>
	<p><?=($db_id)?$db_id:$ym?> 입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>

<?php
include_once ('./_tail.sub.php');


flush();
ob_flush();
ob_end_flush();

$cnt=0;
// 정보 입력
for($i=0;$row=sql_fetch_array_pg($rs);$i++) {
    // print_r2($row);
    // echo $i.'======='.BR;
	$cnt++;
    if($demo) {
        if($cnt >= 10) {break;}
    }
    // test 상태면 127.0.0.1 에서 들어오는 것만 처리
    if($g5['setting']['set_production_test_yn']) {
        $test_ip = $g5['setting']['set_test_ip'] ?: '127.0.0.1';    // 6번 PLC 할당 (192.168.100.143)
        if($row['sck_ip']!=$test_ip) {continue;}
    }

    // 현재값 --------------------------------------------
    $arr_sck = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt'],'sck_value'=>$row['sck_value']);
    // print_r2($arr_sck);
    // echo $row['sck_ip'].', port='.$row['sck_port'].BR;
    // 배열값 추출
    $arr = json_decode($arr_sck['sck_value'], true);
    // 알람데이터 비트 단위로 분리
    for($j=470;$j<sizeof($arr);$j++) {
        // echo $j.'. '.$arr[$j].BR;
        $arr[$j] = str_split($arr[$j]);
        // print_r2($arr[$j]);
        for($k=0;$k<sizeof($arr[$j]);$k++) {
            // echo $arr[$j][$k].BR;
            if($arr[$j][$k]) {
                // echo $i.' / '.$j.' / '.$k.' / '.$row['sck_ip'].' / '.$row['sck_port'].' / '.$row['sck_dt'].BR;
                $alarm[$i][$row['sck_ip']][$row['sck_port']][$j][$k] = $row['sck_dt'];
            }
        }
    }
    // echo 'now ...........'.BR;
    // print_r2($arr);
 
    // 다음 cron 실행 시 쿼리 속도를 위해서 마지막 번호 저장
    $sck_idx_last = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt']);

    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['sck_idx']." (".$row['sck_dt'].") ".$row['sck_ip'].", ".$row['sck_port']." 완료<br>'; </script>\n";

    flush();
    @ob_flush();
    @ob_end_flush();
    usleep($sleepsec);

	// 보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
	if ($cnt % $countgap == 0)
		echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";

	// 화면 정리! 부하를 줄임 (화면 싹 지움)
	if ($cnt % $maxscreen == 0)
		echo "<script> document.all.cont.innerHTML = ''; </script>\n";

}
// 알람데이터 처리 (5초동안 alarm이 있었던 것들만 디비 저장)
// print_r2($alarm);
// print_r2($g5['socket_alarm']);
foreach($alarm as $k0=>$v0) {
    $arr = array(); // reset
    // echo $k0.'/'.$v0.BR; // $k0=일련번호
    foreach($v0 as $k1=>$v1) {
        $arr['sck_ip'] = $k1;
        foreach($v1 as $k2=>$v2) {
            // echo $k2.'/'.$v2.BR; // $k2=sck_port
            $arr['sck_port'] = $k2;
            foreach($v2 as $k3=>$v3) {
                // echo $k3.'/'.$v3.BR; // $k3=sck_no
                foreach($v3 as $k4=>$v4) {
                    // echo $k4.'/'.$v4.BR; // $k4=sck_bit, $v4=sck_value
                    // print_r2($v4);
                    // echo 'ip='.$k1.', port='.$k2.', arrno='.$k3.', bit='.$k4.', dt='.$v4.BR; // $k4=sck_bit, $v4=sck_value
                    // print_r2($g5['socket_alarm'][$k1][$k2][$k3][$k4]);
                    $ar['table']  = 'g5_1_alarm';
                    $ar['com_idx']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['com_idx'];
                    $ar['mms_idx']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['mms_idx'];
                    $ar['cod_idx']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_idx'];
                    $ar['arm_cod_code']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_code'];
                    $ar['arm_cod_type']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_type'];
                    $ar['arm_send_type']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_send_type'];
                    $ar['cod_interval']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_interval'];
                    $ar['cod_count']  = $g5['socket_alarm'][$k1][$k2][$k3][$k4]['cod_count'];
                    $ar['arm_keys']  = '~mms_idx='.$ar['mms_idx'].'~,~cod_code='.$ar['arm_cod_code'].'~,~cod_interval='.$ar['cod_interval'].'~,~cod_count='.$ar['cod_count'].'~,';
                    $ar['arm_status']  = 'ok';
                    $ar['arm_reg_dt']  = $v4;
                    // print_r2($ar);
                    $arm_idx = update_db($ar);
                    unset($ar);
                }
            }
        }
    }
}

// 마지막 디비 입력번호
// print_r2($sck_idx_last);
if(is_array($sck_idx_last)) {
    $arr = array(); // reset
    foreach($sck_idx_last as $k1=>$v1) {
        // echo $k1.'/'.$v1.BR; // $k1=sck_ip
        $arr[$k1] = $v1;
    }
    // print_r2($arr);
    // 메타 정보 입력
    $sql = "SELECT * FROM {$g5['meta_table']} 
            WHERE mta_db_table='pgsql/socket/alarm'
                AND mta_key='sck_idx_last'
    ";
    // echo $sql.BR;
    $row1 = sql_fetch($sql,1);
    if(!$row1['mta_idx']) {
        $sql = " INSERT INTO {$g5['meta_table']} SET 
                    mta_db_table='pgsql/socket/alarm',
                    mta_db_id='".$arr['sck_idx']."',
                    mta_key='sck_idx_last',
                    mta_value='".$arr['sck_value']."',
                    mta_reg_dt='".$arr['sck_dt']."',
                    mta_update_dt='".G5_TIME_YMDHIS."'
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
    else {
        $sql = "UPDATE {$g5['meta_table']} SET 
                    mta_db_id='".$arr['sck_idx']."',
                    mta_reg_dt='".$arr['sck_dt']."',
                    mta_update_dt='".G5_TIME_YMDHIS."'
                WHERE mta_idx = '".$row1['mta_idx']."' 
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
}



?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
</script>
