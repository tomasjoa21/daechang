<?php
// campaign 디비를 복제해서 추가함
// 실행주소: http://test2.seoulouba.kr/adm/v10/convert/campaign_add1.php
// 로컬실행: http://localhost/souba3/www/adm/v10/convert/campaign_add1.php
include_once('./_common.php');

$g5['title'] = '캠페인 정보 추가';
include_once(G5_PATH.'/head.sub.php');
?>
<div class="" style="padding:10px;">
	<span style='display:block;'>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
include_once(G5_PATH.'/tail.sub.php');
?>


<?php
//-- 화면 표시
$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 50; // 몇건씩 화면에 보여줄건지?

// 변수 설정
$table1 = 'g5_1_campaign';
$fields1 = sql_field_names($table1);
$pre1 = substr($fields1[0],0,strpos($fields1[0],'_'));

$table2 = 'g5_1_campaign';
$fields2 = sql_field_names($table2);
$pre2 = substr($fields2[0],0,strpos($fields2[0],'_'));

// 대체필드: 정의한 필드 대체 (table1 에서 1:1 관계로 대체할 필드를 배열로 선언)
$fields21 = array();


// 대상 디비 전체 추출
$sql = " SELECT * FROM {$table1} ";
//echo $sql;
$result = sql_query($sql,1);


flush();
ob_flush();

$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
	$cnt++;
    $arr = array();
	// if($i > 2)
	// 	break;
	
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
    $skips = array('cam_idx');
    for($j=0;$j<sizeof($fields2);$j++) {
        if(in_array($fields2[$j],$skips)) {continue;}
        $arr[$fields2[$j]] = ($fields21[$fields2[$j]]) ? $arr[$fields21[$fields2[$j]]] : $arr[$fields2[$j]];
        $sql_commons[$i][] = " ".strtolower($fields2[$j])." = '".$arr[$fields2[$j]]."' ";
    }

    // 변수 재설정 or 변환





    // 최종 변수 생성
    $sql_text[$i] = (is_array($sql_commons[$i])) ? implode(",",$sql_commons[$i]) : '';

    
    // // 중복체크
    // $sql2 = "   SELECT mb_no, mb_id FROM {$table2}
    //             WHERE mb_id = '".$arr['RVWER_ID']."'
    //                AND mb_email = '".$arr['EMAIL']."'
    // ";
    // //echo $sql2.'<br>';
    // $row2 = sql_fetch($sql2,1);
    // // 정보 업데이트
    // if($row2['mb_id']) {

    //     $sql = "UPDATE {$table2} SET 
    //                 {$sql_text[$i]}
    //             WHERE mb_id = '".$row2['mb_id']."'
    //     ";
    //     echo $sql.'<br>';
    //     // sql_query($sql,1);

    // }
    // // 정보 입력
    // else{

    //     $sql = "INSERT INTO {$table2} SET 
    //                 {$sql_text[$i]}
    //     ";
    //     // echo $sql.'<br>';
    //     sql_query($sql,1);

    // }
	

    for($j=0;$j<30;$j++) {
        $sql = "INSERT INTO {$table2} SET 
            {$sql_text[$i]}
        ";
        // echo $sql.'<br>';
        sql_query($sql,1);
    }



    // 메시지 보임
	echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['od_id']." 처리됨<br>'; </script>".PHP_EOL;
	
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
