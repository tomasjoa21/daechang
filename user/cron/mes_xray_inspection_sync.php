<?php
// 크론 실행을 위해서는 사용자단에 파일이 존재해야 함
// sudo vi /etc/crontab
// sudo systemctl restart cron
// */5 * * * * root wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/mes_cast_shot_sync.php
// [root@web-37 user]# wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/mes_cast_shot_sync.php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '동기화';
include_once('./_head.sub.php');
include_once('./_head.cubic.php');

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 200;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

$table1 = 'MES_XRAY_INSPECTION';

$table2 = 'g5_1_xray_inspection';
$fields2 = sql_field_names($table2);

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
    $search1 = " WHERE SHOT_ID = '".$db_id."' ";
}
// 한달씩
else if($ym) {
    // $search1 = " WHERE EVENT_TIME LIKE '".$ym."' ";
    $search1 = " WHERE WORK_DATE >= '".$ym."-01 00:00:00' AND WORK_DATE <= '".$ym."-31 23:59:59' ";     
}
// 하루씩
else if($ymd) {
    // $search1 = " WHERE EVENT_TIME LIKE '".$ymd."%' ";
    $search1 = " WHERE WORK_DATE >= '".$ymd." 00:00:00' AND WORK_DATE <= '".$ymd." 23:59:59' ";     
    // $search1 = " WHERE CAMP_NO IN ('C0175987','C0175987') ";    // 특정레코드
}
else {
    // 데이터의 마지막 일시 ------
    $sql = " SELECT start_time FROM {$table2} ORDER BY xry_idx DESC LIMIT 1 ";
    $dat = sql_fetch($sql,1);
    $ymdhis = $dat['start_time'];

    $search1 = " WHERE START_TIME > '".$ymdhis."' AND END_TIME != '' ";
    // $search1 = " WHERE START_TIME >= '".$ymdhis."' AND END_TIME != '' "; // uncomment this for testing ------------
    $latest = 1;
}


$sql = "SELECT *
        FROM {$table1}
        {$search1}
        ORDER BY START_TIME
";
// echo $sql.'<br>';
// exit;
$result = $connect_db_pdo->query($sql);
?>

<span style='font-size:9pt;'>
	<p><?=($ym)?$ym:$ymd?> 입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>


<?php
include_once ('./_tail.sub.php');


$status_array = array("00"=>"pending"
    ,"01"=>"pending"
    ,"02"=>"ok"
    ,"10"=>"ok"
    ,"20"=>"pending"
);

// 레드존(7, 10, 11 포인트): 무조건 1등급이어야 OK
// 옐로우존(1,2,3,4,5,6,8,14,15,16,17,18 포인트): 1,2등급이면 OK
// 그린존(9,12,13 포인트): 1,2,3등급이면 OK
$position_result = array("1"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"2"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"3"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"4"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"5"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"6"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"7"=>"1,1"
    ,"8"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"9"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"10"=>"1,1"
    ,"11"=>"1,1"
    ,"12"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"13"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"14"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"15"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"16"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"17"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"18"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
);
// $result_arr = array("OK","OK","OK","NG");
$result_arr = array("OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK"
                    ,"OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK"
                    ,"OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK"
                    ,"OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK"
                    ,"OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","OK","NG"
);


flush();
ob_flush();
ob_end_flush();

$cnt=0;
// 정보 입력
for ($i=0; $row=$result->fetch(PDO::FETCH_ASSOC); $i++) {
	$cnt++;
    // print_r2($row);
    if($demo) {
        if($i >= 2) {break;}
    }

    // table2 변수 추출 $arr
    for($j=0;$j<sizeof($fields2);$j++) {
        // 공백제거 & 따옴표 처리
        $arr[$fields2[$j]] = addslashes(trim($row[strtoupper($fields2[$j])]));
        // 천단위 제거
        if(preg_match("/_price$/",$fields2[$j]))
            $arr[$fields2[$j]] = preg_replace("/,/","",$arr[$fields2[$j]]);
    }

    // table2 입력을 위한 table1 변수 치환
    // $row['EVENT_TIME'] = substr($arr['EVENT_TIME'],0,19);
    // print_r2($arr);
    // 품질 정보 임시 입력
    if($g5['setting']['set_xray_test_yn']) {
        for($j=1;$j<19;$j++) {
            // 해당 포지션의 배열값이 있으면 랜덤선택
            if($position_result[$j]) {
                $position_result_arr = explode(",",$position_result[$j]);
                // print_r2($position_result_arr);
                // echo $j.' - '.$position_result[$j].'<br>';
                $arr['position_'.$j] = $position_result_arr[rand(0,sizeof($position_result_arr)-1)];
            }
            // 결과값도
            $arr['result'] = $result_arr[rand(0,sizeof($result_arr)-1)];
        }
    }

    // table2 입력을 위한 변수배열 일괄 생성 ---------
    // 건너뛸 변수들 설정
    $skips = array('xry_idx');
    for($j=0;$j<sizeof($fields2);$j++) {
        if(in_array($fields2[$j],$skips)) {continue;}
        $arr[$fields2[$j]] = ($fields21[$fields2[$j]]) ? $arr[$fields21[$fields2[$j]]] : $arr[$fields2[$j]];
        $sql_commons[$i][] = " ".strtolower($fields2[$j])." = '".$arr[$fields2[$j]]."' ";
        $sql_field_arr[$i][] = " ".strtolower($fields2[$j])." ";            // for timescaleDB
        $sql_value_arr[$i][] = " '".$arr[$fields2[$j]]."' ";    // for timescaleDB
    }

    // table2 입력을 위한 변수 재선언 (or 생성)
    // $sql_commons[$i][] = " trm_idx_department = '".$mb2['mb_2']."' ";

    // 최종 변수 생성
    $sql_text[$i] = (is_array($sql_commons[$i])) ? implode(",",$sql_commons[$i]) : '';


    // Record update
    $sql3 = "   SELECT xry_idx FROM {$table2}
                WHERE qrcode = '".$arr['qrcode']."'
    ";
    // echo $sql3.'<br>';
    $row3 = sql_fetch($sql3,1);
    // 정보 업데이트
    if($row3['xry_idx']) {
		$sql = "UPDATE {$table2} SET
					$sql_text[$i]
				WHERE xry_idx = '".$row3['xry_idx']."'
		";
        $arr['result'] = '수정';
		if(!$demo) {sql_query($sql,1);}
	    else {echo $sql.'<br><br>';}
    }
    // 정보 입력
    else{
		$sql = "INSERT INTO {$table2} SET
					$sql_text[$i]
		";
        $arr['result'] = '입력';
		if(!$demo) {sql_query($sql,1);}
	    else {echo $sql.'<br><br>';}
    }

    // timescaleDB insert record.
    // 공통쿼리 생성
    $sql_fields[$i] = (is_array($sql_field_arr[$i])) ? "(".implode(",",$sql_field_arr[$i]).")" : '';
    $sql_values[$i] = (is_array($sql_value_arr[$i])) ? "(".implode(",",$sql_value_arr[$i]).")" : '';
    $sql3 = "INSERT INTO {$table2}
                {$sql_fields[$i]} VALUES {$sql_values[$i]} 
            RETURNING xry_idx 
	";
    if(!$demo) {sql_query_pg($sql3,1);}
    else {echo $sql3.'<br><br>';}



    // 주조코드 테스트 입력 (2시간 전에 주조코드가 들어갔다고 가정함)
    // qr_cast_code 테이블에 임시로 입력
    if($g5['setting']['set_dicast_test_yn']) {
        $qr_time = get_qr_time($arr['qrcode']);
        // $cast_time = get_cast_time('825442610','3289922');
        // echo $cast_time.' -------- <br>';
        $cast_time = date("Y-m-d H:i:s", strtotime($qr_time)-3600*2);   // 주조코드가 2시간 전에 입력된 걸로 보고 설정
        $time_cast = get_time2castcode($cast_time); // ex) 2022-01-31 11:32:00 > 2A31B32
        // echo $time_cast.' -------- <br>';
        $mms_idx = substr($time_cast,0,1) + 57; // 58(17호기)....61(20호기)

        $ar['qrcode'] = $arr['qrcode'];
        $ar['cast_code'] = $time_cast;
        $ar['mms_idx'] = $mms_idx;
        $ar['event_time'] = $cast_time;
		if(!$demo) {qr_cast_code_update($ar);unset($ar);}
	    else {print_r2($ar);}
    }



    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$arr['work_date']." (".$arr['production_id'].", ".$arr['qrcode'].") ".$arr['result']." 완료<br>'; </script>\n";

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

include_once ('./_tail.cubic.php');
?>
