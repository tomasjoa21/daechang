<?php
// 크론 실행을 위해서는 사용자단에 파일이 존재해야 함
// sudo vi /etc/crontab
// sudo systemctl restart cron
// */2 * * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php (2분 주기)
// */5 * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php (5분 주기)
// * * * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php (1분 주기)
// [root@web-37 user]# wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php
include_once('./_common.php');

$demo = 1;  // 데모모드 = 1

$g5['title'] = '소켓정보처리';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo) ? 30 : 150;  // 몇건씩 화면에 보여줄건지?
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)


$table1 = 'g5_1_socket';
$fields1 = sql_field_names_pg($table1);

// meta 데이터가 있으면 마지막 이후 10분까지만 (cron 10분 주기, 나중에 혹시 누락 일괄 처리할 때 디비 부하가 너무 높으면 안 됨, 10분씩 끊어서 입력할 것!!) ------
$sql = " SELECT * FROM g5_5_meta WHERE mta_db_table = 'pgsql/socket' AND mta_key = 'sck_idx_last' ";
$one = sql_fetch($sql,1);
// print_r2($one);
if($one['mta_idx']) {
    $sql_end_dt = date("Y-m-d H:i:s", strtotime($one['mta_reg_dt'])+600);   // 10 minutes from now
    $sql_where = " WHERE sck_idx > '".$one['mta_db_id']."' AND sck_dt <= '".$sql_end_dt."' ";
}
else {
    $sql_start_dt = date("Y-m-d H:i:s", G5_SERVER_TIME-600);   // 10 minutes ago
    $sql_where = " WHERE sck_dt >= '".$sql_start_dt."' ";
}

// for loop 첫번째 항목(들)의 $prev 배열값 만들어 둬야 오차를 줄일 수 있음!
// print_r2($prev['192.168.100.139']['20480']);
$sql = " SELECT * FROM g5_5_meta WHERE mta_db_table = 'pgsql/socket' AND mta_key = 'sck_prev_info' ";
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++) {
    // print_r2($row);
    $prev[$row['mta_title']][$row['mta_number']] = array('sck_idx'=>$row['mta_db_id'],'sck_dt'=>$row['mta_reg_dt'],'sck_value'=>$row['mta_value']);
}
// print_r2($prev);

$sql = " SELECT * FROM g5_1_socket {$sql_where} ";
// echo $sql.BR;
// exit;
$rs = sql_query_pg($sql,1);
?>

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
    // echo $i.'<br>';
	$cnt++;
    if($demo) {
        if($cnt >= 10) {break;}
    }
    // if($row['sck_ip']!='192.168.100.143') {continue;}
    if($row['sck_ip']!='127.0.0.1') {continue;}
    
    echo '-----------------------------------------------------------------'.BR;
    echo '<b style="color:darkorange;">'.$row['sck_idx'].'</b> '.$row['sck_ip'].' (P:'.$row['sck_port'].')'.BR;
    // 이전값 --------------------------------------------
    // print_r2($prev[$row['sck_ip']][$row['sck_port']]);
    // 배열값 추출 -----------
    $row['prev']['arr'] = json_decode($prev[$row['sck_ip']][$row['sck_port']]['sck_value'], true);
    // 알람데이터 비트 단위로 분리
    for($j=470;$j<sizeof($row['prev']['arr']);$j++) {
        $row['prev']['arr'][$j] = str_split($row['prev']['arr'][$j]);
    }
    // echo 'prev ...........'.BR;
    // print_r2($row['prev']['arr']);
    
    // 현재값 --------------------------------------------
    $now[$row['sck_ip']][$row['sck_port']] = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt'],'sck_value'=>$row['sck_value']);
    // print_r2($now[$row['sck_ip']][$row['sck_port']]);
    // 배열값 추출
    $row['now']['arr'] = json_decode($now[$row['sck_ip']][$row['sck_port']]['sck_value'], true);
    for($j=470;$j<sizeof($row['now']['arr']);$j++) {
        $row['now']['arr'][$j] = str_split($row['now']['arr'][$j]);
    }
    // echo 'now ...........'.BR;
    // print_r2($row['now']['arr']);

    // 가동시간 처리

    // 생산카운터 처리, 관심있는 카운터 영역만 배열로 추출해서 비교
    // print_r2($g5['socket']); // data/cache/socket-setting.php 참고
    if(is_array($g5['socket'])) {
        foreach($g5['socket'] as $k1=>$v1) {
            $arr = array(); // reset
            // echo $k1.'/'.$v1.BR; // $k1=sck_ip
            $arr['sck_ip'] = $k1;
            foreach($v1 as $k2=>$v2) {
                // echo $k2.'/'.$v2.BR; // $k2=sck_port
                $arr['sck_port'] = $k2;
                foreach($v2 as $k3=>$v3) {
                    // echo $k3.'/'.$v3.BR; // $k3=sck_idx, $v3=sck_value
                    $arr[$k3] = $v3;
                }
            }
            // print_r2($arr);
            // 변화가 생긴 배열값에 대해서만 처리!!
            if(is_array($arr)) {
                foreach($arr as $x1=>$y1) {
                    // echo $x1.$y1.BR;
                    if(is_array($y1)) {
                        // 해당 목적 포트 및 배열번호에 대해서만 체크해서 생산카운터 계산하면 됩니다.
                        echo $arr['sck_ip'].'/'.$arr['sck_port'].'/'.$x1.BR;    // <<<<<<<<<<<<<<<<<<<<<
                        // 해당 배열값의 변화
                        echo '(prev)'.$row['prev']['arr'][$x1].'> (now)'.$row['now']['arr'][$x1].BR;

                        // 생산수량 (현재적산값 - 이전적산값)
                        $row['sck_count'] = $row['now']['arr'][$x1] - $row['prev']['arr'][$x1];
                        // 30000 이상에서 다시 초기화되는 부분이 있어서 추가 (터무니 없는 값이면 일단 1로 설정)
                        $row['sck_count'] = (abs($row['sck_count'])>10) ? 1 : $row['sck_count'];
                        if($row['sck_count']) {
                            // echo '<div style="color:darkorange;">apply to count.</div>';
                        }
                        // print_r2($y1);
                        // 해당 지그 관련 설비 및 제품 연결 정보
                        for($k=0;$k<sizeof($y1);$k++) {
                            // print_r2($y1[$k]);
                        }
                    }
                }
            }
            // // 메타 정보 입력
            // $sql = "SELECT * FROM {$g5['meta_table']} 
            //         WHERE mta_db_table='pgsql/socket'
            //             AND mta_key='sck_prev_info'
            //             AND mta_title='".$arr['sck_ip']."'
            //             AND mta_number='".$arr['sck_port']."'
            // ";
            // // echo $sql.BR.BR;
            // $row1 = sql_fetch($sql,1);
            // if(!$row1['mta_idx']) {
            //     $sql = " INSERT INTO {$g5['meta_table']} SET 
            //                 mta_db_table='pgsql/socket',
            //                 mta_db_id='".$arr['sck_idx']."',
            //                 mta_key='sck_prev_info',
            //                 mta_value='".$arr['sck_value']."',
            //                 mta_title='".$arr['sck_ip']."',
            //                 mta_number='".$arr['sck_port']."',
            //                 mta_reg_dt='".$arr['sck_dt']."',
            //                 mta_update_dt='".G5_TIME_YMDHIS."'
            //     ";
            //     // echo $sql.BR.BR;
            //     sql_query($sql);
            // }
            // else {
            //     $sql = "UPDATE {$g5['meta_table']} SET 
            //                 mta_db_id='".$arr['sck_idx']."',
            //                 mta_value='".$arr['sck_value']."',
            //                 mta_update_dt='".G5_TIME_YMDHIS."'
            //             WHERE mta_idx = '".$row1['mta_idx']."'
            //     ";
            //     // echo $sql.BR.BR;
            //     sql_query($sql);
            // }
        }
    }

    // 알람데이터 처리

    echo '----------------'.BR;


    // 알람 정보 입력 (현재값 기반으로 입력)

    // // insert from table (이전값과 다른 경우만 입력)
    // if($now[$row['mms_idx']] && $prev[$row['mms_idx']] && $now[$row['mms_idx']] != $prev[$row['mms_idx']]) {
    //     if($demo) {
    //         echo $now[$row['mms_idx']] .">". $prev[$row['mms_idx']].BR;
    //     }
    //     // counter = 현재값 - 이전값
    //     $row['mrk_count'] = $now[$row['mms_idx']] - $prev[$row['mms_idx']];
    //     // 30000 이상에서 다시 초기화되는 부분이 있어서 추가
    //     $row['mrk_count'] = (abs($row['mrk_count'])>10) ? 1 : $row['mrk_count'];

    //     // 존재 여부 체크
    //     $sql = " SELECT mrk_idx, mrk_value FROM {$table1} WHERE mms_idx = '".$row['mms_idx']."' ORDER BY mrk_idx DESC LIMIT 1 ";
    //     // echo $sql.BR;
    //     $one = sql_fetch($sql,1);
    //     if($one['mrk_value']!=$now[$row['mms_idx']]) {
    //         $sql = "INSERT INTO {$table1} SET
    //                     mms_idx = '".$row['mms_idx']."'
    //                     , sck_idx = '".$row['sck_idx']."'
    //                     , mrk_value = '".$row['sck_value']."'
    //                     , mrk_count = '".$row['mrk_count']."'
    //                     , mrk_reg_dt = '".$row['sck_dt']."'
    //         ";
    //         if(!$demo) {sql_query($sql,1);}
    //         else {echo $sql.'<br><br>';}
    //     }
    // }

    // 이전값과 비교하기 위해서 배열 저장
    $prev[$row['sck_ip']][$row['sck_port']] = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt'],'sck_value'=>$row['sck_value']);
    // print_r2($prev);
    // print_r2($prev['192.168.100.139']['20480']);

    // 다음 cron 실행 시 쿼리 속도를 위해서 마지막 번호 저장
    $sck_arr[$row['sck_ip']][$row['sck_port']] = array('sck_idx'=>$row['sck_idx'],'sck_dt'=>$row['sck_dt'],'sck_value'=>$row['sck_value']);
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

// 각 아이피, 포트별 마지막 이전 정보 저장했다가 다른 쿼리 돌 때 비교해야 함
// print_r2($sck_arr);
if(is_array($sck_arr)) {
    foreach($sck_arr as $k1=>$v1) {
        $arr = array(); // reset
        // echo $k1.'/'.$v1.BR; // $k1=sck_ip
        $arr['sck_ip'] = $k1;
        foreach($v1 as $k2=>$v2) {
            // echo $k2.'/'.$v2.BR; // $k2=sck_port
            $arr['sck_port'] = $k2;
            foreach($v2 as $k3=>$v3) {
                // echo $k3.'/'.$v3.BR; // $k3=sck_idx, $v3=sck_value
                $arr[$k3] = $v3;
            }    
        }
        // print_r2($arr);
        // 메타 정보 입력
        $sql = "SELECT * FROM {$g5['meta_table']} 
                WHERE mta_db_table='pgsql/socket'
                    AND mta_key='sck_prev_info'
                    AND mta_title='".$arr['sck_ip']."'
                    AND mta_number='".$arr['sck_port']."'
        ";
        // echo $sql.BR.BR;
        $row1 = sql_fetch($sql,1);
        if(!$row1['mta_idx']) {
            $sql = " INSERT INTO {$g5['meta_table']} SET 
                        mta_db_table='pgsql/socket',
                        mta_db_id='".$arr['sck_idx']."',
                        mta_key='sck_prev_info',
                        mta_value='".$arr['sck_value']."',
                        mta_title='".$arr['sck_ip']."',
                        mta_number='".$arr['sck_port']."',
                        mta_reg_dt='".$arr['sck_dt']."',
                        mta_update_dt='".G5_TIME_YMDHIS."'
            ";
            // echo $sql.BR.BR;
            sql_query($sql);
        }
        else {
            $sql = "UPDATE {$g5['meta_table']} SET 
                        mta_db_id='".$arr['sck_idx']."',
                        mta_value='".$arr['sck_value']."',
                        mta_update_dt='".G5_TIME_YMDHIS."'
                    WHERE mta_idx = '".$row1['mta_idx']."'
            ";
            // echo $sql.BR.BR;
            sql_query($sql);
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
            WHERE mta_db_table='pgsql/socket'
                AND mta_key='sck_idx_last'
    ";
    // echo $sql.BR;
    $row1 = sql_fetch($sql,1);
    if(!$row1['mta_idx']) {
        $sql = " INSERT INTO {$g5['meta_table']} SET 
                    mta_db_table='pgsql/socket',
                    mta_db_id='".$arr['sck_idx']."',
                    mta_key='sck_idx_last',
                    mta_value='".$arr['sck_value']."',
                    mta_reg_dt='".$arr['sck_dt']."'
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
    else {
        $sql = " UPDATE {$g5['meta_table']} SET mta_db_id='".$arr['sck_idx']."', mta_reg_dt='".$arr['sck_dt']."' WHERE mta_idx = '".$row1['mta_idx']."' ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
}


?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
</script>
