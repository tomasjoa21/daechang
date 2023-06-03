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
.div_result {font-size:1.5em;font-weight:bold;}
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


$found = json_decode(stripslashes($found),true);
$found = is_array($found) ? $found : array(); // 찾았다면 다음 페이지에 배열 변수를 계속 넘김 $found = array(58,59) 이런 형태가 되겠네!
// print_r2($found);
// exit;
$success=0; // set for all mms_idxs are found.
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
        $found[] = $g5['set_dicast_mms_idxs_array'][$j];
        continue;
    }

    // echo $g5['set_dicast_mms_idxs_array'][$j].' ------------ <br>';
    // Initiate.
    $xry_list = array();
    $cnt=$oks=0;

    // 하루범위씩 추적
    $sql = "SELECT *
            FROM g5_1_xray_inspection AS xry
                LEFT JOIN g5_1_qr_cast_code AS qrc USING(qrcode)
            WHERE mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                AND end_time >= '".$ymd." 00:00:00' AND end_time <= '".$ymd." 23:59:59'
            ORDER BY xry_idx DESC
    ";
    // echo $sql.'<br>';
    // exit;
    $result = sql_query($sql,1);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $cnt++;
        // print_r2($row);
        if($demo) {
            if($i >= 10) {break;}
        }

        // table2 입력을 위한 변수배열 일괄 생성 ---------
        // 각 포인트별 등급 합계
        for($k=1;$k<19;$k++) {
            $position_sum[$i] += $row['position_'.$k];
            $positions[$i] .= $row['position_'.$k].' ';
        }
        // echo $position_sum[$i].'<br>';

        // 각 등급의 합계가 범위를 벗어나면 다시 처음부터 카운팅, 변수 초기화
        // echo $position_sum[$i] .'<'. $set_ok_sum_min .'||'. $position_sum[$i] .'>'. $set_ok_sum_max.'<br>';
        if( $position_sum[$i] < $set_ok_sum_min || $position_sum[$i] > $set_ok_sum_max ) {
            $oks = 0;
            $xry_list = array();
            // echo "<script> document.all.cont.innerHTML += '<br>".$cnt."번째에서 등급미달 (".$row['production_id'].", ".$row['qrcode'].")<br>'; </script>\n";
            // echo "<script> document.all.cont.innerHTML += '&nbsp;ㄴ&nbsp;".$positions[$i]." -> 추적정보 리셋<br>'; </script>\n";
            // sleep(1);    // 0.5초 쉼
            continue;
        }
        $oks++;
        $xry_list[] = $row['xry_idx'];

        // 기준 그룹핑 숫자 이상이 되면 $xry_list[]배열에서 중간값 확보
        if($oks >= $set_parameter_group_count) {
            $sql = "SELECT *
                    FROM g5_1_xray_inspection AS xry
                        LEFT JOIN g5_1_qr_cast_code AS qrc USING(qrcode)
                    WHERE xry_idx = '".$xry_list[$set_parameter_idx]."'
            ";
            // echo $sql.'<br>';
            $xry = sql_fetch($sql,1);
            $xry['mms_name'] = $g5['mms'][$g5['set_dicast_mms_idxs_array'][$j]]['mms_name'];
            // print_r2($xry);
            // 주조시각(best spot paremater) 입력
            $sql = "SELECT *
                    FROM g5_1_data_measure_best
                    WHERE mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                        AND dmb_dt = '".$xry['event_time']."'
            ";
            // echo $sql.'<br>';
            $dmb1 = sql_fetch($sql,1);
            if(!$dmb1['dmb_idx']) {
                $sql = "INSERT INTO g5_1_data_measure_best SET
                            mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                            , dmb_dt = '".$xry['event_time']."'
                            , dmb_group_count = '".$set_parameter_group_count."'
                            , dmb_min = '".$set_ok_sum_min."'
                            , dmb_max = '".$set_ok_sum_max."'
                            , dmb_reg_dt = '".G5_TIME_YMDHIS."'
                ";
                // echo $sql.'<br>';
                if(!$demo) {sql_query($sql,1);}
                else {echo $sql.'<br><br>';}
            }

            // 찾은 설비는 배열값 설정
            $found[] = $g5['set_dicast_mms_idxs_array'][$j];

            echo "<script> document.all.cont.innerHTML += '<div class=\'div_result\'>".$xry['mms_name']." - 최적 파라메타 추출 성공</div>'; </script>\n";
            echo "<script> document.all.cont.innerHTML += '<div class=\'div_result2\'>event_time=".$xry['event_time']."</div>'; </script>\n";
            // echo "<script> document.all.cont.innerHTML += '<div class=\'div_result2\'>qrcode=".$row['qrcode'].", production_id=".$row['production_id']."</div>'; </script>\n";
            // exit;
            break;  // 상위 for 문장을 빠져나감
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
    
}
// 찾은 설비 표시
$found = array_unique($found);
if($found[0]) {
// 한개이상이면 표시함
foreach($found as $k1=>$v1) {
    // echo $k1.'-'.$v1.'<br>';
    $mms_names .= '<div class=\"div_result2\">'.$g5['mms'][$v1]['mms_name'].' - 추출 완료됨</div>';
}
?>
<script>
    document.all.cont.innerHTML += "<br><br><br><?=$mms_names?>";
</script>
<?php
}
// 전부 다 찾았는지 체크
if( sizeof($found) == sizeof($g5['set_dicast_mms_idxs_array']) ) {
    $success = 1;
}
// exit; // =======================================================================

// if no more job.
if($ymd_next < date("Y-m-d", G5_SERVER_TIME - 86400*$set_parameter_max_day)
    || $demo || $success) 
{
    // echo $ymd_next.' ymd_next<br>';
    // echo date("Y-m-d", G5_SERVER_TIME - 86400*$set_parameter_max_day).' ymd_next date<br>';
?>
<script>
    document.all.cont.innerHTML += "<br><?=$ymd?> 완료<br><font color=crimson><b>[끝]</b></font>";
</script>
<?php
}
// 다음 페이지가 있는 경우는 3초 후 이동
else {
?>
<script>
    document.all.cont.innerHTML += "<br><?=$ymd?> 완료 <br><font color=crimson><b>3초후</b></font> 다음 페이지로 이동합니다.";
    setTimeout(function(){
        self.location='?ymd=<?=$ymd_next?>&found=<?=json_encode($found)?>';
    },2000);
</script>
<?php
}
?>
