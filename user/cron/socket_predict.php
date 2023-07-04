<?php
// sudo vi /etc/crontab
// sudo systemctl restart cron
// once per minute execution
// * * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_predict.php
// [root@web-37 user]# wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_predict.php
// 이것도 5초마다 제대로 실행이 안 되서 shell 프로그램 짜서 돌리게 되었습니다.
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '예지처리';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo) ? 30 : 150;  // 몇건씩 화면에 보여줄건지?
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)


$table1 = 'g5_1_alarm';
$fields1 = sql_field_names($table1);

// meta 데이터가 있으면 마지막 이후 5초까지만 (cron 5초 주기, 나중에 혹시 누락 일괄 처리할 때 디비 부하가 너무 높으면 안 됨, 5초씩 끊어서 입력할 것!!) ------
$sql = " SELECT * FROM g5_5_meta WHERE mta_db_table = 'alarm/predict' AND mta_key = 'arm_idx_last' ";
$one = sql_fetch($sql,1);
// print_r2($one);
if($one['mta_idx']) {
    // print_r2($one);

    $sql_end_dt = date("Y-m-d H:i:s", strtotime($one['mta_reg_dt'])+70);   // 70 seconds from now (1분보다 조금 더 넉넉하게..)
    // echo $sql_end_dt.BR;
    $sql_where = " WHERE arm_idx > '".$one['mta_db_id']."' AND arm_reg_dt <= '".$sql_end_dt."' "; // 
    // $sql_where = " WHERE arm_idx > '".$one['mta_db_id']."' ";
    // if time difference is too much(1 hour), start recent ones.
    if( strtotime($one['mta_reg_dt'])+3600*1 < G5_SERVER_TIME ) {
        $one['mta_idx'] = 0;
    }
}
if(!$one['mta_idx']) {
    $sql_start_dt = date("Y-m-d H:i:s", G5_SERVER_TIME-60);   // 60 seconds ago
    $sql_where = " WHERE arm_reg_dt >= '".$sql_start_dt."' ";
}

// $sql_where = " WHERE arm_reg_dt >= '2023-05-22 10:00:21' ";     // Test <<<<<<<<<<<<<<<<<<<<
$sql = " SELECT * FROM g5_1_alarm {$sql_where} ORDER BY arm_reg_dt ";
echo $sql.BR;
exit;
$rs = sql_query($sql,1);
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
for($i=0;$row=sql_fetch_array($rs);$i++) {
    // print_r2($row);
    // echo $i.'======='.BR;
	$cnt++;
    if($demo) {
        if($cnt >= 10) {break;}
    }

    // 최근의 알람 발생값과 비교
    $arr_predict = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt'],'sck_value'=>$row['sck_value']);

    $predict = 0;

    // print_r2($arr);
 
    // 다음 cron 실행 시 쿼리 속도를 위해서 마지막 번호 저장
    $arm_idx_last = array('arm_idx'=>$row['arm_idx'],'arm_reg_dt'=>$row['arm_reg_dt']);

    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['arm_idx']." (".$row['arm_reg_dt'].") ".$row['arm_count'].", ".$row['arm_send_flag']." 완료<br>'; </script>\n";

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
// 마지막 디비 입력번호
// print_r2($arm_idx_last);
if(is_array($arm_idx_last)) {
    $arr = array(); // reset
    foreach($arm_idx_last as $k1=>$v1) {
        // echo $k1.'/'.$v1.BR; // $k1=sck_ip
        $arr[$k1] = $v1;
    }
    // print_r2($arr);
    // 메타 정보 입력
    $sql = "SELECT * FROM {$g5['meta_table']} 
            WHERE mta_db_table='alarm/predict'
                AND mta_key='arm_idx_last'
    ";
    // echo $sql.BR;
    $row1 = sql_fetch($sql,1);
    if(!$row1['mta_idx']) {
        $sql = " INSERT INTO {$g5['meta_table']} SET 
                    mta_db_table='alarm/predict',
                    mta_db_id='".$arr['arm_idx']."',
                    mta_key='arm_idx_last',
                    mta_value='".$arr['sck_value']."',
                    mta_reg_dt='".$arr['arm_reg_dt']."',
                    mta_update_dt='".G5_TIME_YMDHIS."'
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
    else {
        $sql = "UPDATE {$g5['meta_table']} SET 
                    mta_db_id='".$arr['arm_idx']."',
                    mta_reg_dt='".$arr['arm_reg_dt']."',
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
