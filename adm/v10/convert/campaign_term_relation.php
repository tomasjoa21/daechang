<?
// 공통파일 추가
include_once("./_common.php");

//-- 화면 표시 
$countgap = 10; // 몇건씩 보낼지 설정
$maxscreen = 40; // 몇건씩 화면에 보여줄건지?
$sleepsec = 200;  // 천분의 몇초간 쉴지 설정


//-- 설정값
$pg_array = array('offline','kcp','lgu','paypal','ksnet','etc');
$status_array = array('pending','ok','ok','ok','cancel');
$ampm_array = array('am','pm');
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>자료 입력</title>
</head>
<body>
<span style='font-size:9pt;'>
	<p>입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>


<?
// 디비 추출
$sql = "SELECT * FROM u_campaign WHERE cam_idx > 59652 ";
$rs  = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++) {
	
	// 데모 테스트 갯수 조정
//	if($i>6)
//		break;
	
	// 카테고리 랜덤 추출 (2차 카테고리만)
	$trm1 = sql_fetch(" SELECT * FROM jt_term WHERE trm_taxonomy = 'category' AND trm_idx_parent <> 0 ORDER BY RAND() LIMIT 1 ");

	// term_relation 입력
	$sql1 = " INSERT INTO jt_term_relation SET	".
		"	trm_idx	= '".$trm1['trm_idx']."'			".
		"	, tmr_db_table = 'campaign'			".
		"	, tmr_db_key = 'category'			".
		"	, tmr_db_id = '".$row['cam_idx']."'		".
		"	, tmr_sort = '1'							".
		"	, tmr_reg_dt = now()					";
	sql_query($sql1,1);	//<===============================
	//echo $sql1.'<br><br>';


	
	// 상권 추출 (2차만)
	$trm2 = sql_fetch(" SELECT * FROM jt_term WHERE trm_taxonomy = 'salesarea' AND trm_idx NOT IN (21,38) ORDER BY RAND() LIMIT 1 ");

	// term_relation 입력
	$sql2 = " INSERT INTO jt_term_relation SET	".
		"	trm_idx	= '".$trm2['trm_idx']."'			".
		"	, tmr_db_table = 'campaign'			".
		"	, tmr_db_key = 'salesarea'			".
		"	, tmr_db_id = '".$row['cam_idx']."'		".
		"	, tmr_sort = '1'							".
		"	, tmr_reg_dt = now()					";
	sql_query($sql2,1);	//<===============================
	//echo $sql2.'<br><br>';
	
	
	
	//-- DOM 보도 자바스크립트가 좀 빨라서 0,1번이 안 보이고 2번부터 보이는 표현상 에러가 있음, 동작에는 무리가 없습니다.
	echo "<script> document.all.cont.innerHTML += '".($i+1).". ".addslashes($sql1)."<br><br>'; </script>\n";
	//echo "+";
    flush();
    ob_flush();
    ob_end_flush();
    usleep($sleepsec);
    if ($i % $countgap == 0)
    {
        echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
    }

    // 화면을 지운다... 부하를 줄임
    if ($i % $maxscreen == 0)
        echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
	
	
}
?>

<script> document.all.cont.innerHTML += "<br><br><br>총 <?php echo number_format($i) ?>건 작업 완료<br><br><font color=crimson><b>[끝]</b></font>"; document.body.scrollTop += 1000; </script>
</body>
</html>
