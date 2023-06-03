<?php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = 'Optimum parameters tracing';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

// default date.
$ymd = $ymd ?: date("Y-m-d");

// NEXT YMD Default = yester day (It goes one day prior continuely till the set day.(30days))
$sql = " SELECT DATE_ADD('".$ymd."' , INTERVAL -1 DAY) AS ymd FROM dual ";
$dat = sql_fetch($sql,1);
$ymd_next = substr($dat['ymd'],0,10);
// echo $ymd.'<br>';
// echo $ymd_next.'<br>';
// exit;
?>
<style>
#hd_login_msg {display:none;}
.div_result {font-size:2.2em;font-weight:bold;}
.div_result2 {font-size:1.4em;}
</style>

<span style='font-size:9pt;'>
	<p><?=($ym)?$ym:$ymd?> 추적시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>


<?php
include_once ('./_tail.sub.php');

// 추적 최대 날짜
$set_parameter_max_day = $g5['setting']['set_parameter_max_day'] ? $g5['setting']['set_parameter_max_day'] : 30;

// 등급합계 범위
$set_ok_sum_min = $g5['setting']['set_ok_sum_min'] ? $g5['setting']['set_ok_sum_min'] : 18;
$set_ok_sum_max = $g5['setting']['set_ok_sum_max'] ? $g5['setting']['set_ok_sum_max'] : 19;

// 양품 그룹핑 수
$set_parameter_group_count = $g5['setting']['set_parameter_group_count'] ? $g5['setting']['set_parameter_group_count'] : 100;
$set_parameter_idx = (int)$set_parameter_group_count/2; // 가장 가운데 있는 값

// 레드존(7, 10, 11 포인트): 무조건 1등급이어야 OK
// 옐로우존(1,2,3,4,5,6,8,14,15,16,17,18 포인트): 1,2등급이면 OK
// 그린존(9,12,13 포인트): 1,2,3등급이면 OK

flush();
ob_flush();
ob_end_flush();


// Rotating all mms_idx for tracing best parameters
for($j=0;$j<sizeof($g5['set_dicast_mms_idxs_array']);$j++) {
    // if the best parameter of due mms_idx is existed, continue for next mms_idx
    $sql = "SELECT *
            FROM g5_1_data_measure_best
            WHERE mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                AND dmb_dt >= '".$ymd." 00:00:00'
            ORDER BY dmb_idx DESC
            LIMIT 1
    ";
    // echo $sql.'<br>';
    $dmb = sql_fetch($sql,1);
    if($dmb['dmb_idx']) {
        continue;
    }

    // echo $g5['set_dicast_mms_idxs_array'][$j].' ------------ <br>';
    $sql = "SELECT *
            FROM g5_1_xray_inspection AS xry
                LEFT JOIN g5_1_qr_cast_code AS qrc USING(qrcode)
            WHERE mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                AND end_time >= '".$ymd." 00:00:00' AND end_time <= '".$ymd." 23:59:59'
            ORDER BY xry_idx DESC
    ";
    echo $sql.'<br>';
    // $rs = sql_query_pg($sql,1);
    // for($j=0;$row=sql_fetch_array_pg($rs);$j++) {
    //     // print_r2($row);
    // }
}
exit;


// 하루범위씩 추적
$sql = "SELECT *
        FROM g5_1_xray_inspection
        WHERE end_time >= '".$ymd." 00:00:00' AND end_time <= '".$ymd." 23:59:59'
        ORDER BY end_time DESC
";
// echo $sql.'<br>';
// exit;
$result = sql_query_pg($sql,1);


$xry_list = array();
$cnt=$oks=$success=0;
// 정보 입력
for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
	$cnt++;
    // print_r2($row);
    if($demo) {
        if($i >= 10) {break;}
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

    // table2 입력을 위한 변수배열 일괄 생성 ---------
    // 각 포인트별 등급 합계 계산
    for($j=1;$j<19;$j++) {
        $position_sum[$i] += $arr['position_'.$j];
        $positions[$i] .= $arr['position_'.$j].' ';
    }
    // echo $position_sum[$i].'<br>';

    // 각 등급의 합계가 범위를 벗어나면 다시 처음부터 카운팅
    // echo $position_sum[$i] .'<'. $set_ok_sum_min .'||'. $position_sum[$i] .'>'. $set_ok_sum_max.'<br>';
    if( $position_sum[$i] < $set_ok_sum_min || $position_sum[$i] > $set_ok_sum_max ) {
        $oks = 0;
        $xry_list = array();
        // echo "<script> document.all.cont.innerHTML += '<br>".$cnt."번째에서 등급미달 (".$arr['production_id'].", ".$arr['qrcode'].")<br>'; </script>\n";
        // echo "<script> document.all.cont.innerHTML += '&nbsp;ㄴ&nbsp;".$positions[$i]." -> 추적정보 리셋<br>'; </script>\n";
        // sleep(1);    // 0.5초 쉼
        continue;
    }
    $oks++;
    $xry_list[] = $arr['xry_idx'];

    // 기준 그룹핑 숫자 이상이 되면 $xry_list[]배열에서 중간값 확보
    if($oks >= $set_parameter_group_count) {
        $sql = "SELECT * FROM {$table2} WHERE xry_idx = '".$xry_list[$set_parameter_idx]."' ";
        // echo $sql.'<br>';
        $xry = sql_fetch_pg($sql,1);
        // print_r2($xry);

        // QR코드 각인 시간 정보 추출 (약 30시간 이상 차이가 남)
        $sql = "SELECT * FROM g5_1_engrave_qrcode WHERE qrcode = '".$xry['qrcode']."' ";
        // echo $sql.'<br>';
        $eqr = sql_fetch_pg($sql,1);
        // print_r2($eqr);

        // QR코드 각인시간 +-5분
        $start_dt = date("Y-m-d H:i:s", strtotime($eqr['event_time'])-60*5);
        $end_dt = date("Y-m-d H:i:s", strtotime($eqr['event_time'])+60*5);

        // 현재는 QR코드 각인시간 +-5분 전쯤 근처값을 추정합니다. 주조기 설비 전체를 돌면서 저장
        for($j=0;$j<sizeof($g5['set_dicast_mms_idxs_array']);$j++) {
            // echo $g5['set_dicast_mms_idxs_array'][$j].'<br>';
            // echo $mms[$g5['set_dicast_mms_idxs_array'][$j]].' ------------ <br>';
            $sql = "SELECT dta_type, dta_no, AVG(dta_value) AS dta_value
                    FROM g5_1_data_measure_".$g5['set_dicast_mms_idxs_array'][$j]."
                    WHERE dta_type IN (1,8)
                    AND dta_dt >= '".$start_dt."' AND dta_dt <= '".$end_dt."'
                    GROUP BY dta_type, dta_no
                    ORDER BY dta_type, dta_no ASC
            ";
            echo $sql.'<br>';
            // $rs = sql_query_pg($sql,1);
            // for($j=0;$row=sql_fetch_array_pg($rs);$j++) {
            //     // print_r2($row);
            // }
        }

        $arr['result_data'] = '<div class="div_data"><b>보온로 온도:</b> </div>';
        echo "<script> document.all.cont.innerHTML += '<div class=\'div_result\'>최적 파라메타 추출 성공</div>'; </script>\n";
        echo "<script> document.all.cont.innerHTML += '<div class=\'div_result2\'>xry_idx=".$arr['xry_idx'].", end_time=".$arr['end_time']."</div>'; </script>\n";
        echo "<script> document.all.cont.innerHTML += '<div class=\'div_result2\'>qrcode=".$arr['qrcode'].", production_id=".$arr['production_id']."</div>'; </script>\n";
        $success = 1;
        break;
    }

    echo "<script> document.all.cont.innerHTML += ' . '; </script>\n";

    flush();
    ob_flush();
    ob_end_flush();
    usleep($sleepsec);

	// 보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
	if ($cnt % $countgap == 0)
		echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";

	// 화면 정리! 부하를 줄임 (화면 싹 지움)
	if ($cnt % $maxscreen == 0)
		echo "<script> document.all.cont.innerHTML = ''; </script>\n";

}
// 데이터 추척 실패 표시
if(!$success) {
    $ymd_date = $ym?$ym:$ymd;
    echo "<script> document.all.cont.innerHTML += '<div class=\'div_result\'>".$ymd_date.": 최적 파라메타 없음</div>'; </script>\n";
}


// Terminate in case of db_id found.
if($db_id) {
?>
    <script>
    	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
    </script>
    <?php
}
// 일간 처리
else {
    if($ymd_next < date("Y-m-d", G5_SERVER_TIME - 86400*$set_parameter_max_day)
        || $demo || $latest || $success) {
        // echo $ym_next.' ym_next<br>';
        // echo date("Y-m", G5_SERVER_TIME - 86400*$set_parameter_max_day).' ym_next_date<br>';
        // echo $ymd_next.' ymd_next<br>';
        // echo date("Y-m-d", G5_SERVER_TIME - 86400*$set_parameter_max_day).' ymd_next date<br>';
        // echo $demo.' demo<br>';
        // echo $laest.' laest<br>';
        // echo $success.' success<br>';
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
        document.all.cont.innerHTML += "<br><br><?=($ym)?$ym:$ymd?> 완료 <br><font color=crimson><b>3초후</b></font> 다음 페이지로 이동합니다.";
        setTimeout(function(){
            self.location='?ym=<?=$ym_next?>&ymd=<?=$ymd_next?>';
        },2000);
    </script>
    <?php
    }
}
?>
