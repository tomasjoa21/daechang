<?php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = 'Quality data manipulate';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 200;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

$table2 = 'g5_1_xray_inspection';
$fields2 = sql_field_names($table2);
// print_r2($fields2);
$table3 = 'g5_1_qr_cast_code';
$fields3 = sql_field_names($table3);
// print_r2($fields3);

// NEXT YMD Default
if($ym) {
    // 다음달
    $sql = " SELECT DATE_ADD('".$ym."-01' , INTERVAL +1 MONTH) AS ym FROM dual ";
    $dat = sql_fetch($sql,1);
    $ym_next = substr($dat['ym'],0,7);
    // echo $ym.'<br>';
    // echo $ym_next.'<br>';
    // exit;
}
else if($ymd) {
    // 다음일
    $sql = " SELECT DATE_ADD('".$ymd."' , INTERVAL +1 DAY) AS ymd FROM dual ";
    $dat = sql_fetch($sql,1);
    $ymd_next = substr($dat['ymd'],0,10);
    // echo $ymd.'<br>';
    // echo $ymd_next.'<br>';
    // exit;
}

// if db_id exists.
if($db_id) {
    $search1 = " WHERE xry_idx = '".$db_id."' ";
}
// 한달씩
else if($ym) {
    // $search1 = " WHERE EVENT_TIME LIKE '".$ym."' ";
    $search1 = " WHERE start_time >= '".$ym."-01 00:00:00' AND start_time <= '".$ym."-31 23:59:59' ";     
}
// 하루씩
else if($ymd) {
    // $search1 = " WHERE EVENT_TIME LIKE '".$ymd."%' ";
    $search1 = " WHERE start_time >= '".$ymd." 00:00:00' AND start_time <= '".$ymd." 23:59:59' ";     
    // $search1 = " WHERE CAMP_NO IN ('C0175987','C0175987') ";    // 특정레코드
}
else {
    // 데이터의 마지막 일시 ------
    $sql = " SELECT start_time FROM {$table2} ORDER BY xry_idx DESC LIMIT 1 ";
    $dat = sql_fetch($sql,1);
    $ymdhis = $dat['start_time'];

    $search1 = " WHERE start_time > '".$ymdhis."' AND END_TIME != '' ";
    $latest = 1;
}


$sql = "SELECT *
        FROM {$table2}
        {$search1}
        ORDER BY start_time
";
// echo $sql.'<br>';
// exit;
$result = sql_query_pg($sql,1);
?>

<span style='font-size:9pt;'>
	<p><?=($ym)?$ym:$ymd?> 입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>


<?php
include_once ('./_tail.sub.php');

$mms_idx_array = array(1,2,3,4);
$grade_OK_array = array(1,2,3,4,1,2,3,4,1,2,3,4,1,2,3,4,1,2,3,4,1,2,3,4);
$grade_NG_array = array(10,11,13,14,15);


flush();
ob_flush();
ob_end_flush();

$cnt=0;
// 정보 입력
for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
	$cnt++;
    // print_r2($row);
    if($demo) {
        if($i >= 2) {break;}
    }

    // 변수생성
    $mms_idx = $mms_idx_array[rand(0,sizeof($mms_idx_array)-1)] + 57; // 58(17호기)....61(20호기)
    $grade = ${'grade_'.$row['result'].'_array'}[rand(0,sizeof(${'grade_'.$row['result'].'_array'})-1)];
    $qr_time = get_qr_time($row['qrcode']);
    $cast_time = date("Y-m-d H:i:s", strtotime($qr_time)-3600*2);   // 주조코드가 2시간 전에 입력된 걸로 보고 설정
    $row['cast_code'] = get_time2castcode($cast_time); // ex) 2022-01-31 11:32:00 > 2A31B32

    // 기존 거 지우고 
    $sql1 = " DELETE FROM {$table3} WHERE qrcode = '".$row['qrcode']."' ";
    sql_query($sql1,1);    

    // 새로 입력
    $ar['qrcode'] = $row['qrcode'];
    $ar['cast_code'] = $row['cast_code'];
    $ar['mms_idx'] = $mms_idx;
    $ar['event_time'] = $cast_time;
    $ar['qrc_grade'] = $grade;
    $ar['qrc_result'] = $row['result'];
    // print_r2($ar);
    if(!$demo) {qr_cast_code_update($ar);}
    else {print_r2($ar);}
    unset($ar);


    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['qrcode']." (".$row['cast_code'].") ".$row['result']." 완료<br>'; </script>\n";

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

// Terminate in case of db_id found.
if($db_id) {
?>
    <script>
    	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
    </script>
    <?php
}
// 월간 처리
else {
    if($ym_next > date("Y-m") || $ymd_next > date("Y-m-d") || $demo || $latest) {
    ?>
    <script>
        document.all.cont.innerHTML += "<br><br><?=($ym)?$ym:$ymd?> 완료<br><font color=crimson><b>[끝]</b></font>";
    </script>
    <?php
    }
    // 다음 페이지가 있는 경우는 3초 후 이동
    else {
    ?>
    <script>
        document.all.cont.innerHTML += "<br><br><?=($ym)?$ym:$ymd?> 완료 <br><font color=crimson><b>2초후</b></font> 다음 페이지로 이동합니다.";
        setTimeout(function(){
            self.location='?ym=<?=$ym_next?>&ymd=<?=$ymd_next?>';
        },2000);
    </script>
    <?php
    }
}
?>
