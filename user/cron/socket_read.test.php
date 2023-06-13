<?php
// 크론 실행을 위해서는 사용자단에 파일이 존재해야 함
// sudo vi /etc/crontab
// sudo systemctl restart cron
// */2 * * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php (2분 주기)
// * * * * * * root wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.php (1분 주기)
// [root@web-37 user]# wget -O - -q -t 1 http://daechang.epcs.co.kr/user/cron/socket_read.test.php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '소켓정보처리';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo) ? 30 : 150;  // 몇건씩 화면에 보여줄건지?
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)


$table1 = 'g5_1_socket';
$fields1 = sql_field_names_pg($table1);


// prev 비교를 위해서 이전값 2번째 앞에서부터 시작해야 함
$sql_where = " WHERE sck_idx BETWEEN 18946659 AND 18946667 AND sck_ip = '192.168.100.143' ";
$sql = " SELECT * FROM g5_1_socket {$sql_where} ORDER BY sck_dt ";
// echo $sql.BR;
// exit;
$rs = sql_query_pg($sql,1);
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
for($i=0;$row=sql_fetch_array_pg($rs);$i++) {
    // print_r2($row);
    // echo $i.'======='.BR;
	$cnt++;
    if($demo) {
        if($cnt >= 10) {break;}
    }
    // test 상태면 127.0.0.1 에서 들어오는 것만 처리
    if($g5['setting']['set_production_test_yn']) {
        $test_ip = $g5['setting']['set_test_ip'] ?: '127.0.0.1';    // 6번 PLC 할당 (192.168.100.143)
        if($row['sck_ip']!=$test_ip) {continue;}
    }

    // echo '-----------------------------------------------------------------'.BR;
    // echo '<b style="color:darkorange;">'.$row['sck_idx'].'</b> '.$row['sck_ip'].' (P:'.$row['sck_port'].')'.BR;
    // 이전값 --------------------------------------------
    // print_r2($prev[$row['sck_ip']][$row['sck_port']]);
    // 배열값 추출 -----------
    $row['prev']['arr'] = json_decode($prev[$row['sck_ip']][$row['sck_port']]['sck_value'], true);
    // print_r2($row['prev']['arr']);
    // 이전값이 없으면 계산 불가.. break!!
    if($row['prev']['arr'][0]) {
        // echo $i.'======= array exists.. '.BR;
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
                            // echo $arr['sck_ip'].'/'.$arr['sck_port'].'/'.$x1.BR;    // <<<<<<<<<<<<<<<<<<<<<
                            // 해당 배열값의 변화
                            // echo '(prev)'.$row['prev']['arr'][$x1].'> (now)'.$row['now']['arr'][$x1].'-------------------'.BR;
                            // echo '생산통계기준: '.$g5['setting']['mng_statics_std'].BR;
                            $stat_date = statics_date(G5_TIME_YMDHIS);  // 교대시간을 반영한 통계일자
    
                            // 생산수량 (현재적산값 - 이전적산값)
                            $count = $row['now']['arr'][$x1] - $row['prev']['arr'][$x1];
                            // echo $x1.'. '.$count.BR;
                            // 30000 이상에서 다시 초기화되는 부분이 있어서 추가 (터무니 없는 값이면 일단 1로 설정)
                            $count = (abs($count)>10) ? 1 : $count;
                            if($count) {
                                // echo '<div style="color:darkorange;">['.$x1.'] apply to count from '.$row['prev']['arr'][$x1].' > '.$row['now']['arr'][$x1].'</div>';
                                // print_r2($y1);   // 해당 배열의 설비정보
                                // 해당 지그 관련 설비들 및 제품 연결 정보 ====================
                                for($k=0;$k<sizeof($y1);$k++) {
                                    // print_r2($y1[$k]);
                                    // 당일의 해당설비, 해당제품의 production_item이 있으면 생산처리
                                    $sql_ing = ($g5['setting']['set_worker_test_yn']) ? "" : " AND pri_ing = '1' " ;
                                    $sql1 = "   SELECT * FROM {$g5['production_item_table']}
                                                WHERE prd_idx IN (SELECT prd_idx FROM {$g5['production_table']} WHERE prd_start_date = '".$stat_date."')
                                                    AND bom_idx = '".$y1[$k]['bom_idx']."' AND mms_idx = '".$y1[$k]['mms_idx']."' {$sql_ing}
                                    ";
                                    // echo $sql1.BR;
                                    $pri = sql_fetch($sql1,1);
                                    // print_r2($pri);
                                    if($pri['pri_idx']) {
                                        // echo $x1.'. pri_idx ='.$pri['pri_idx'].', bom_idx ='.$y1[$k]['bom_idx'].BR;
                                        // 작업자 생산제품 입력(production_item_count), // material & item 처리!!
                                        $ar['pri_idx'] = $pri['pri_idx'];
                                        $ar['pic_value'] = ($count<0) ? 0 : abs($count);    // -1 이라는 이상한 값이 나올 때가 있구만!!
                                        $ar['sck_dt'] = $row['sck_dt'];
                                        production_count($ar); // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                                        print_r2($ar);
                                        unset($ar);
                                    }
                                    // 없으면 테스트 모드일 때 일단 무작위로라도 만들어 줘야 함 (카운터는 올라왔는데 아무런 생산지시가 없는 경우이므로..)
                                    else if($g5['setting']['set_production_test_yn']) {
                                        // echo $x1.'. no pri_idx ...'.BR;
                                        // 관련 bom_idx 배열 생성해 두고, 있으면 생성 안함
                                        for($z1=0;$z1<sizeof($y1);$z1++) {
                                            // print_r2($y1[$z1]);
                                            $bom_idxs[$k][] = $y1[$z1]['bom_idx'];
                                        }
                                        // print_r2($bom_idxs[$k]);
                                        $sql5 = "   SELECT COUNT(*) AS pri_count FROM {$g5['production_item_table']}
                                                    WHERE prd_idx IN (SELECT prd_idx FROM {$g5['production_table']} WHERE prd_start_date = '".$stat_date."')
                                                        AND bom_idx IN (".implode(",",$bom_idxs[$k]).") AND mms_idx = '".$y1[$k]['mms_idx']."'
                                        ";
                                        // echo $sql5.BR;
                                        $one = sql_fetch($sql5,1);
                                        // print_r2($one);
                                        // 관련 설비, 제품이 없을 때만 한개 생성함
                                        // 수주 생성 (지그 제품 여러개 중에 어떤 거? random 선택...)
                                        // print_r2($g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1]);
                                        if( is_array($g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1]) && !$one['pri_count']) {

                                            $fields = sql_field_names('g5_1_material');
                                            $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
                                            $skips[] = $pre.'_reg_dt';
                                            for($i=0;$i<sizeof($fields);$i++) {
                                                if(in_array($fields[$i],$skips)) {continue;}
                                                $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
                                            }
                                            $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';

                                            // 여러 설비중에서 한개만 랜덤 선택
                                            $ridx = rand(0,sizeof($g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1])-1); // 배열중에 random_idx
                                            // echo $ridx.BR;
                                            $rvalue = rand(80,400);    // 수주량 랜덤
                                            $rand_bom_idx = $g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1][$ridx]['bom_idx'];
                                            $rand_mms_idx = $g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1][$ridx]['mms_idx'];
                                            // echo 'rand_bom_idx='.$rand_bom_idx;
                                            // echo ', rand_mms_idx='.$rand_mms_idx.BR;
                                            $bom = get_table('bom','bom_idx',$rand_bom_idx);

                                            // 수주생성
                                            $ar['table'] = 'g5_1_order_item';
                                            $ar['com_idx'] = $bom['com_idx'];
                                            $ar['cst_idx'] = $bom['cst_idx_customer'];
                                            $ar['bom_idx'] = $bom['bom_idx'];
                                            $ar['ori_count'] = $rvalue;    // 수주량 랜덤
                                            $ar['ori_type'] = 'normal';
                                            $ar['ori_memo'] = '자동생성:'.G5_TIME_YMDHIS;
                                            $ar['ori_status'] = 'ok';
                                            $ar['ori_date'] = G5_TIME_YMD;
                                            $arr['ori_idx'] = update_db($ar);
                                            // print_r3($ar);
                                            unset($ar);

                                            // 생산계획 생성
                                            $ar['table'] = 'g5_1_production';
                                            $ar['com_idx'] = $bom['com_idx'];
                                            $ar['ori_idx'] = $arr['ori_idx'];
                                            $ar['bom_idx'] = $bom['bom_idx'];
                                            $ar['prd_start_date'] = G5_TIME_YMD;
                                            $ar['prd_memo'] = '자동생성:'.G5_TIME_YMDHIS;
                                            $ar['prd_status'] = 'confirm';
                                            $arr['prd_idx'] = update_db($ar);
                                            // print_r3($ar);
                                            unset($ar);

                                            // 작업자 할당 (주간 작업자 중에서 맨 먼저인 사람 default 자동 선택)
                                            $sql2 = "   SELECT bmw_idx, bmw.mms_idx AS mms_idx, mms_name, bmw.mb_id AS mb_id, mb_name, bmw_type
                                                        FROM {$g5['bom_mms_worker_table']} AS bmw
                                                            LEFT JOIN {$g5['mms_table']} AS mms ON mms.mms_idx = bmw.mms_idx
                                                            LEFT JOIN {$g5['member_table']} AS mb ON mb.mb_id = bmw.mb_id
                                                        WHERE bom_idx = '".$rand_bom_idx."' AND bmw.mms_idx = '".$rand_mms_idx."'
                                                        ORDER BY bmw_type LIMIT 1
                                            ";
                                            // echo $sql2.BR;
                                            $bmw = sql_fetch($sql2,1);
                                            // print_r2($bmw);
                                            $ar['table'] = 'g5_1_production_item';
                                            $ar['com_idx'] = $bom['com_idx'];
                                            $ar['prd_idx'] = $arr['prd_idx'];
                                            $ar['bom_idx'] = $bom['bom_idx'];
                                            $ar['mms_idx'] = $g5['socket'][$arr['sck_ip']][$arr['sck_port']][$x1][$ridx]['mms_idx'];
                                            $ar['mb_id'] = $bmw['mb_id'];
                                            $ar['pri_value'] = $rvalue;
                                            $ar['pri_ing'] = 1;
                                            $ar['pri_memo'] = '자동생성:'.G5_TIME_YMDHIS;
                                            $ar['pri_status'] = 'confirm';
                                            $arr['pri_idx'] = update_db($ar);
                                            // print_r3($ar);
                                            unset($ar);
                                            
                                            // 자재 없으면 생성해 두고..
                                            // 모든 하위 구조 추출
                                            $sql1 = "SELECT bom.bom_idx, bom.bom_type, bom.bom_name, bom_part_no, bom_price, bom_status, cst_idx_provider, cst_idx_customer
                                                        , bit.bit_idx, bit.bom_idx AS bit_bom_idx, bit.bit_main_yn, bit.bom_idx_child, bit.bit_reply, bit.bit_count
                                                    FROM {$g5['bom_item_table']} AS bit
                                                        LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                                                    WHERE bit.bom_idx = '".$bom['bom_idx']."'
                                                    ORDER BY bit.bit_reply
                                            ";
                                            // echo $sql1.BR;
                                            $rs1 = sql_query($sql1,1);
                                            for ($j=0; $row1=sql_fetch_array($rs1); $j++) {
                                                // print_r2($row1);
                                                if($row1['bom_type']=='material') {
                                                    // 생산 갯수만큼 준비되어 있지 않으면 미리 생성해 둔다.
                                                    $sql3 = " SELECT COUNT(mtr_idx) AS mtr_sum FROM {$g5['material_table']}
                                                                WHERE bom_idx = '".$row1['bom_idx']."' AND mtr_status = 'ok'
                                                    ";
                                                    // echo $sql3.BR;
                                                    $mtr = sql_fetch($sql3,1);
                                                    // print_r2($mtr); // 현재 재고량
                                                    $arr['mtr_due_count'] = $rvalue * $row1['bit_count']; // 필요수량
                                                    // echo "rvalue ".$rvalue."* bit_count ".$row1['bit_count']."=".$arr['mtr_due_count'].BR;
                                                    if($mtr['mtr_sum']<$arr['mtr_due_count']) {
                                                        $arr['mtr_need_count'] = $arr['mtr_due_count'] - $mtr['mtr_sum'];   // 생성해야 하는 수량
                                                        // echo 'mtr_need_count='.$arr['mtr_need_count'].BR;    // <<<<<<<<< need count
                                                        $bom1 = get_table('bom','bom_idx',$row1['bom_idx']);
                                                        $sql_material = array();
                                                        for ($z=0; $z<$arr['mtr_need_count']; $z++) {
                                                            $sql_material[] = "(NULL, '".$_SESSION['ss_com_idx']."', '".$bom1['cst_idx_provider']."', '".$bom1['cst_idx_customer']."', '".$bom1['bom_idx']."', '".$bom1['bom_part_no']."', '".addslashes($bom1['bom_name'])."', 'material', 1, '".$bom1['bom_price']."', 'ok', '".G5_TIME_YMDHIS."', '".G5_TIME_YMDHIS."')";
                                                        }
                                                        if($sql_material[0]) {
                                                            $sql4 = " INSERT INTO {$g5['material_table']} (mtr_idx, com_idx, cst_idx_provider, cst_idx_customer, bom_idx, mtr_part_no, mtr_name, mtr_type, mtr_value, mtr_price, mtr_status, mtr_reg_dt, mtr_update_dt) VALUES
                                                                        ".implode(",",$sql_material) ;
                                                            // echo $sql4.BR;
                                                            sql_query($sql4,1);
                                                        }
            
                                                    }
                                                }
                                            }

                                            // 작업자 생산제품 입력(production_item_count), // material & item 처리!!
                                            $ar['pri_idx'] = $pri['pri_idx'];
                                            $ar['pic_value'] = $count;
                                            $ar['sck_dt'] = $row['sck_dt'];
                                            production_count($ar);
                                            unset($ar);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else {
        // echo '이전 배열 정보 없음';
    }

    // 알람데이터 처리

    // echo '----------------'.BR;

    // 알람 정보 입력 (현재값 기반으로 입력)



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
                        mta_reg_dt='".$arr['sck_dt']."',
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
                    mta_reg_dt='".$arr['sck_dt']."',
                    mta_update_dt='".G5_TIME_YMDHIS."'
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
    else {
        $sql = "UPDATE {$g5['meta_table']} SET 
                    mta_db_id='".$arr['sck_idx']."',
                    mta_reg_dt='".$arr['sck_dt']."',
                    mta_update_dt='".G5_TIME_YMDHIS."'
                WHERE mta_idx = '".$row1['mta_idx']."' 
        ";
        // echo $sql.BR.BR;
        sql_query($sql);
    }
}

// pgsql은 일주일치만 가지고 있는다.
$sql = " DELETE FROM g5_1_socket WHERE sck_dt < '".date("Y-m-d H:i:s", G5_SERVER_TIME-86400*10)."' ";
// echo $sql.BR.BR;
sql_query_pg($sql,1);



?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
</script>
