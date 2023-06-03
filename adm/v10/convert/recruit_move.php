<?php
// 테이블: g5_write_b03_01 > recruit 테이블로 이관
// 실행주소: people0702.cafe24.com/adm/v10/convert/recruit_move.php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '채용정보이전';
include_once(G5_PATH.'/head.sub.php');
?>
<div class="" style="padding:10px;">
	<span style=''>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
include_once(G5_PATH.'/tail.sub.php');
?>


<?php
// 변수 설정
$table1 = 'g5_write_b03_01';
$fields1 = sql_field_names($table1);
$pre1 = substr($fields1[0],0,strpos($fields1[0],'_'));

$table2 = 'g5_1_recruit';
$fields2 = sql_field_names($table2);
$pre2 = substr($fields2[0],0,strpos($fields2[0],'_'));
//print_r2($fields2);

// 대체필드: 정의한 필드 대체 (table1 에서 1:1 관계로 대체할 필드를 배열로 선언)
$fields21 = array(
    'rct_subject'=>'wr_subject'
    ,'rct_reg_dt'=>'wr_datetime'
    ,'rct_com_name'=>'wr_1'
    ,'rct_pay'=>'wr_4'
    ,'rct_school'=>'wr_9'
    ,'rct_career'=>'wr_10'
);




$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정 (20000하면 좀 느림)
$maxscreen = 50; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

// 대상 디비 전체 추출
$sql = " SELECT * FROM {$table1} WHERE wr_datetime > '2021-07-01 00:00:00' ";
//echo $sql.'<br>';
$result = sql_query($sql,1);
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
	$cnt++;
    $arr = array();
//    print_r2($row);
//	 if($i > 4)
//	 	break;
    
    // 변수 재설정
    for($j=0;$j<sizeof($fields1);$j++) {
        // 공백 제거
        $arr[$fields1[$j]] = trim($row[$fields1[$j]]);
        // 따옴표 처리
        $arr[$fields1[$j]] = addslashes($arr[$fields1[$j]]);
        // 천단위 제거
        if(preg_match("/_price$/",$fields1[$j]))
            $arr[$fields1[$j]] = preg_replace("/,/","",$arr[$fields1[$j]]);
    }

    // 변수 치환
    // $arr['JOB_CD'] = '1234';
    

    // 공통쿼리
    $skips = array('rct_idx','rct_update_dt','rct_work_place','rct_type','rct_expire_date','rct_homepage','rct_content','rct_mobile_content','rct_status');
    for($j=0;$j<sizeof($fields2);$j++) {
        if(in_array($fields2[$j],$skips)) {continue;}
        $arr[$fields2[$j]] = ($fields21[$fields2[$j]]) ? $arr[$fields21[$fields2[$j]]] : $arr[$fields2[$j]];
        $sql_commons[$i][] = " ".strtolower($fields2[$j])." = '".$arr[$fields2[$j]]."' ";
    }

    // 변수 재설정 or 변환
    $sql_commons[$i][] = " rct_work_place = '".$arr['wr_13']." ".$arr['wr_3']."' ";
    $sql_commons[$i][] = " rct_type = '".array_search($arr['wr_5'],$g5['set_rct_type_value'])."' ";
    $sql_commons[$i][] = ($arr['wr_2']!='비공개') ? " rct_homepage = '".$arr['wr_2']."' " : " rct_homepage = '' ";
    $arr['expire_date'] = $arr['wr_7'] ? substr($arr['wr_7'],0,4)."-".substr($arr['wr_7'],4,2)."-".substr($arr['wr_7'],6,2) : ''; 
    $sql_commons[$i][] = " rct_expire_date = '".$arr['expire_date']."' ";

    // 시리얼로 생성할 변수
//    $mb_10[$i] = ''; // 초기화 or 기존값
//    $mb_10[$i] = serialized_update('INS_CHK',$arr['INS_CHK'],$mb_10[$i]);   // 휴대폰 인증 여부
//    $mb_10[$i] = serialized_update('KTALK_ID',$arr['KTALK_ID'],$mb_10[$i]);   // 카톡아이디
//    $sql_commons[$i][] = " mb_10 = '".$mb_10[$i]."' ";
    // 내용
    $sql3 = " SELECT * FROM g5_board_file WHERE bo_table = 'b03_01' AND wr_id = '".$arr['wr_id']."' AND bf_no = 0 ";
    $bf = sql_fetch($sql3);
//    print_r2($bf);
    $arr['content'] = $bf['bf_file'] ? '<img src="https://people0702.cafe24.com/data/file/b03_01/'.$bf['bf_file'].'">' : ''; 
    $sql_commons[$i][] = $arr['content'] ? " rct_content = '".$arr['content']."', rct_mobile_content = '".$arr['content']."' " 
                                        : " rct_content = '', rct_mobile_content = '' ";
    

    // 최종 변수 생성
    $sql_text[$i] = (is_array($sql_commons[$i])) ? implode(",",$sql_commons[$i]) : '';

    $sql2 = "SELECT *
            FROM {$g5['recruit_table']}
            WHERE rct_subject = '".$arr['wr_subject']."'
                AND rct_com_name = '".$arr['wr_1']."'
    ";
    $row2 = sql_fetch($sql2,1);
    // 없으면 등록
    if(!$row2['rct_idx']) {
        $sql = " INSERT INTO {$g5['recruit_table']} SET
                    {$sql_text[$i]} 
                    , rct_status = 'ok'
                    , rct_update_dt = '".G5_TIME_YMDHIS."'
        ";
        if(!$demo) {sql_query($sql,1);}
        $row2['rct_idx'] = sql_insert_id();
    }
    // 있으면 수정
    else {
        $sql = "UPDATE {$g5['recruit_table']} SET
                    {$sql_text[$i]}
                    , rct_update_dt = '".G5_TIME_YMDHIS."'
                WHERE rct_idx = '".$row2['rct_idx']."'
        ";
        if(!$demo) {sql_query($sql,1);}
    }
    if($demo) {echo $sql.'<br><br>';}

	
	// 메시지 보임
	echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['wr_subject']." 처리됨<br>'; </script>".PHP_EOL;
	
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
?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($i) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>
