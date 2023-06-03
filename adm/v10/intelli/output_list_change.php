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

// 레드존(7, 10, 11 포인트): 무조건 1등급이어야 OK
// 옐로우존(1,2,3,4,5,6,8,14,15,16,17,18 포인트): 1,2등급이면 OK
// 그린존(9,12,13 포인트): 1,2,3등급이면 OK
$position_result = array("1"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"2"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"3"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"4"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"5"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"6"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"8"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"9"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"12"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"13"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,3"
    ,"14"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"15"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"16"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"17"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
    ,"18"=>"1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2"
);


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

    // table2 변수 추출 $arr
    for($j=0;$j<sizeof($fields2);$j++) {
        // echo $fields2[$j].'<br>';
        // 공백제거 & 따옴표 처리
        $arr[$fields2[$j]] = addslashes(trim($row[$fields2[$j]]));
        // 천단위 제거
        if(preg_match("/_price$/",$fields2[$j]))
            $arr[$fields2[$j]] = preg_replace("/,/","",$arr[$fields2[$j]]);
    }
    // print_r2($arr);

    // table2 입력을 위한 table1 변수 치환
    // $row['EVENT_TIME'] = substr($arr['EVENT_TIME'],0,19);
    // print_r2($arr);

    // table2 입력을 위한 변수배열 일괄 생성 ---------
    // 변수 설정
    for($j=1;$j<19;$j++) {
        // 해당 포지션의 배열값이 있으면 랜덤선택
        if($position_result[$j]) {
            $position_result_arr = explode(",",$position_result[$j]);
            // print_r2($position_result_arr);
            // echo $j.' - '.$position_result[$j].'<br>';
            $arr['position_'.$j] = $position_result_arr[rand(0,sizeof($position_result_arr)-1)];
        }
        $sql_field_arr[$i][] = " position_".$j." ";
        $sql_value_arr[$i][] = " '".$arr['position_'.$j]."' ";
    }

    // table2 입력을 위한 변수 재선언 (or 생성)
    // $sql_commons[$i][] = " trm_idx_department = '".$mb2['mb_2']."' ";

    // 공통쿼리 생성
    $sql_fields = (is_array($sql_field_arr[$i])) ? "(".implode(",",$sql_field_arr[$i]).")" : '';
    $sql_values = (is_array($sql_value_arr[$i])) ? "(".implode(",",$sql_value_arr[$i]).")" : '';

 
    $sql = "UPDATE {$table2} SET 
                {$sql_fields} = {$sql_values}
            WHERE xry_idx = '".$arr['xry_idx']."' 
	";
    if(!$demo) {sql_query_pg($sql,1);}
    else {echo $sql.'<br><br>';}

    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$arr['start_time']." (".$arr['production_id'].", ".$arr['qrcode'].") ".$arr['result']." 완료<br>'; </script>\n";

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
