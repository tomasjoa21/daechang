<?php
$sub_menu = "960150";
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '복제';
include_once('./_head.sub.php');
require './vendor/autoload.php';

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

// # You can generate an API token from the "API Tokens Tab" in the UI
$token = 'YpGSir4-oaJVmJ5eAk1bYXfAARIq8yddmkVtfqjT8Dlc9A1VGabu-FZ1hthgZybiDg2yqu7CFJbMgH6kppFVFg==';
// $org = 'ing';
// $bucket = 'ing';
$org = 'hanjoo';
$bucket = 'mes';

$client = new Client([
    "url" => "http://py.websiteman.kr:8086",
    "token" => $token,
]);

$writeApi = $client->createWriteApi();

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 200;     // 천분의 몇초간 쉴지 설정 (1sec=1000)


// $table1 = 'g5_1_cast_shot_sub_bak';
$table1 = 'g5_1_cast_shot_sub';
$fields1 = sql_field_names($table1);

// 하루씩 끊어서 입력, Default first YYYY-MM of first record for no $ym
if(!$ymd) {
    $sql = " SELECT event_time AS ymd FROM {$table1} ORDER BY event_time LIMIT 1 ";
    $dat = sql_fetch($sql,1);
    // print_r2($dat);
    $ymd = substr($dat['ymd'],0,10);
}

// 다음날
$sql = " SELECT DATE_ADD('".$ymd."' , INTERVAL +1 DAY) AS ymd FROM dual ";
$dat = sql_fetch($sql,1);
$ymd_next = substr($dat['ymd'],0,10);
// echo $ymd.'<br>';
// echo $ymd_next.'<br>';
// exit;


// if db_id exists.
if($db_id) {
    $search1 = " WHERE SHOT_ID = '".$db_id."' ";
}
// 하루
else {
    $search1 = " WHERE event_time >= '".$ymd." 00:00:00' AND event_time <= '".$ymd." 23:59:59' ";
    // $search1 = " WHERE CAMP_NO IN ('C0175987','C0175987') ";    // 특정레코드
}

$sql = "SELECT *
        FROM {$table1} AS cam
        {$search1}
        ORDER BY event_time
";
// echo $sql.'<br>';
// exit;
$result = sql_query($sql,1);
?>

<span style='font-size:9pt;'>
	<p><?=($db_id)?$db_id:$ymd?> 입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>


<?php
include_once ('./_tail.sub.php');


flush();
ob_flush();
ob_end_flush();

$cnt=0;
// 캠페인 정보 입력
for ($i=0; $row=sql_fetch_array($result); $i++) {
	$cnt++;
    if($demo) {
        if($cnt >= 15) {break;}
    }
    // print_r2($row);

    // table1 변수 추출 $arr
    for($j=0;$j<sizeof($fields1);$j++) {
        // 공백제거 & 따옴표 처리
        $arr[$fields1[$j]] = $row[$fields1[$j]];
        // 시간
        if(preg_match("/_time$/",$fields1[$j]))
            $arr[$fields1[$j]] = strtotime($arr[$fields1[$j]]);
    }

    $skips = array('mcs_idx','shot_id','event_time');
    for($j=0;$j<sizeof($fields1);$j++) {
        if(in_array($fields1[$j],$skips)) {continue;}
        $sql_commons[$i][] = strtolower($fields1[$j])."=".$arr[$fields1[$j]];
    }

    // // Option 1: Use InfluxDB Line Protocol to write data
    // $data = "cast_shot_sub,host=host2 used_percent=22.43234543 15770000";
    $data = "cast_shot_sub,shot_id=".$arr['shot_id']." ".implode(",",$sql_commons[$i])." ".$arr['event_time'];
    if(!$demo) {
        $writeApi->write($data, WritePrecision::S, $bucket, $org);
    }
    else {echo $data.'<br><br>';}



    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['event_time']." 완료<br>'; </script>\n";

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
if($db_id||$i<=0) {
?>
    <script>
    	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
    </script>
    <?php
}
// 마지막 페이지인 경우는 종료
else {
    if($ymd_next > date("Y-m-d")||$demo) {
    ?>
    <script>
        document.all.cont.innerHTML += "<br><br><?=$ymd?> 완료<br><font color=crimson><b>[끝]</b></font>";
    </script>
    <?php
    }
    // 다음 페이지가 있는 경우는 3초 후 이동
    else {
    ?>
    <script>
        document.all.cont.innerHTML += "<br><br><?=$ymd?> 완료 <br><font color=crimson><b>2초후</b></font> 다음 페이지로 이동합니다.";
        setTimeout(function(){
            self.location='?ymd=<?=$ymd_next?>';
        },2000);
    </script>
    <?php
    }
}

$client->close();
?>
