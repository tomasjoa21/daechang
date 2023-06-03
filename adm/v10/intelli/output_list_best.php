<?php
// 빠르게 최적파마메터를 생성하기 위한 임시파일입니다. 
// 정식파일은 /user/cron/output_list_parameters.php 파일을 참고하세요.
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = 'Optimum parameters tracing';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo||$db_id) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo||$db_id) ? 30 : 100;  // 몇건씩 화면에 보여줄건지?/
$sleepsec = 10000;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

// default date.
$ymd = $ymd ?: date("Y-m-d");

// 다음일
$sql = " SELECT DATE_ADD('".$ymd."' , INTERVAL -1 DAY) AS ymd FROM dual ";
$dat = sql_fetch($sql,1);
$ymd_next = substr($dat['ymd'],0,10);
// echo $ymd.'<br>';
// echo $ymd_next.'<br>';
// exit;

// $search1 = " AND EVENT_TIME LIKE '".$ymd."%' ";
$search1 = " AND dta_dt >= '".$ymd." 00:00:00' AND dta_dt <= '".$ymd." 23:59:59' ";     
// $search1 = " AND CAMP_NO IN ('C0175987','C0175987') ";    // 특정레코드

?>
<style>
#hd_login_msg {display:none;}
.div_result {font-size:2.2em;font-weight:bold;margin-top:20px;}
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

$date_week = date('w', strtotime($ymd));

// 주조기 설비 전체를 돌면서..
for($j=0;$j<sizeof($g5['set_dicast_mms_idxs_array']);$j++) {
    // echo $g5['set_dicast_mms_idxs_array'][$j].'<br>';
    // echo $mms[$g5['set_dicast_mms_idxs_array'][$j]].' ------------ <br>';
    // 주말은 건너뜀(0,6)
    if(in_array($date_week,array(0,6))) {
        break;
    }
    $mms_name[$j] = $g5['mms'][$g5['set_dicast_mms_idxs_array'][$j]]['mms_name'];
    // echo $mms_name[$j].' ------------- <br>';

    // 온도, 압력 부분만 저장(1,8) - This is just temporary. These are just one day average for values.
    $sql = "SELECT dta_type, dta_no, AVG(dta_value) AS dta_value, MIN(dta_idx) AS dta_idx
            FROM g5_1_data_measure_".$g5['set_dicast_mms_idxs_array'][$j]."
            WHERE dta_type IN (1,8)
                {$search1}
            GROUP BY dta_type, dta_no
            ORDER BY dta_type, dta_no ASC
    ";
    // echo $sql.'<br>';
    $rs = sql_query_pg($sql,1);
    for($i=0;$row=sql_fetch_array_pg($rs);$i++) {
        // print_r2($row);
       $sql = "SELECT * FROM g5_1_data_measure_best
                WHERE mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                    AND dta_idx = '".$row['dta_idx']."'
        ";
        // echo $sql.'<br>';
        $one = sql_fetch($sql,1);
        if(!$one['dmb_idx']) {
            $sql = "INSERT INTO g5_1_data_measure_best SET
                        mms_idx = '".$g5['set_dicast_mms_idxs_array'][$j]."'
                        , dta_idx = '".$row['dta_idx']."'
                        , dta_type = '".$row['dta_type']."'
                        , dta_no = '".$row['dta_no']."'
                        , dta_value = '".$row['dta_value']."'
                        , dmb_reg_dt = '".G5_TIME_YMDHIS."'
            ";
            // echo $sql.'<br>';
            sql_query($sql,1);
        }
    }
    echo "<script> document.all.cont.innerHTML += '<div class=\'div_result\'>".$mms_name[$j].": 최적 파라메타 추출 성공</div>'; </script>\n";

    $success = 1;

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
// 월간 처리
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
        },3000);
    </script>
    <?php
    }
}
?>
