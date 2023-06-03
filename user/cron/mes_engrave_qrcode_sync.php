<?php
// 크론 실행을 위해서는 사용자단에 파일이 존재해야 함
// sudo vi /etc/crontab
// sudo systemctl restart cron
// */5 * * * * root wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/mes_engrave_qrcode_sync.php
// [root@web-37 user]# wget -O - -q -t 1 http://ing.icmms.co.kr/php/hanjoo/user/cron/mes_engrave_qrcode_sync.php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '동기화';
include_once('./_head.sub.php');
include_once('./_head.cubic.php');

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 200;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

$table1 = 'MES_ENGRAVE_QRCODE';

$table2 = 'g5_1_engrave_qrcode';
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
    $sql = " SELECT event_time FROM {$table2} ORDER BY eqr_idx DESC LIMIT 1 ";
    $dat = sql_fetch($sql,1);
    $ymdhis = $dat['event_time'];

    $search1 = " WHERE EVENT_TIME > '".$ymdhis."' ";
    $latest = 1;
}


$sql = "SELECT *
        FROM {$table1} AS cam
        {$search1}
        ORDER BY EVENT_TIME
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

    // table2 입력을 위한 변수배열 일괄 생성 ---------
    // 건너뛸 변수들 설정
    $skips = array('eqr_idx');
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
    $sql3 = "   SELECT eqr_idx FROM {$table2}
                WHERE production_id = '".$arr['production_id']."'
    ";
    // echo $sql3.'<br>';
    $row3 = sql_fetch($sql3,1);
    // 정보 업데이트
    if($row3['eqr_idx']) {
		$sql = "UPDATE {$table2} SET
					$sql_text[$i]
				WHERE eqr_idx = '".$row3['eqr_idx']."'
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

    // 공통쿼리 생성
    $sql_fields[$i] = (is_array($sql_field_arr[$i])) ? "(".implode(",",$sql_field_arr[$i]).")" : '';
    $sql_values[$i] = (is_array($sql_value_arr[$i])) ? "(".implode(",",$sql_value_arr[$i]).")" : '';
    // timescaleDB insert record.
    $sql3 = "INSERT INTO {$table2}
                {$sql_fields[$i]} VALUES {$sql_values[$i]} 
            RETURNING eqr_idx 
	";
    if(!$demo) {sql_query_pg($sql3,1);}
    else {echo $sql3.'<br><br>';}

    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$arr['work_date']." (".$arr['event_time'].", ".$arr['qrcode'].") ".$arr['result']." 완료<br>'; </script>\n";

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
