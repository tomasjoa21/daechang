<?
/*=================================================================================
    작성자 : 손지식
    작성일 : 2015-09-19
===================================================================================*/
error_reporting(E_ALL ^ E_NOTICE);
//데이타 베이스 연결
$vGdbHost="db.dreampath.co.kr";
$vGdbUser="dreampath";
$vGdbPass="ftppw@dreampath";
$vGdbName="dbdreampath";
$iGconn  = @mysql_connect($vGdbHost,$vGdbUser,$vGdbPass) or die("DB connent Error... Check your database configuration file (config.lib.php)");
@mysql_query(" set names utf8 ");
mysql_query("SET @@group_concat_max_len = 4096", $iGconn);
mysql_select_db($vGdbName,$iGconn);


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
//for($i=0;$i<10;$i++) {
//for($i=0;$i<300;$i++) {	// 눈에 딱 보이네
for($i=0;$i<2000;$i++) {

	//-- 임의 날짜 및 시간 추출 (오늘 이후 3일~120일 사이 아무 날짜)
	$time = rand(86400*3,86400*120);
	$server_time = time() + $time;
	$time_ymdhis = date('Y-m-d H:i:s', $server_time);
	$time_ymd = substr($time_ymdhis, 0, 10);
	$time_his = substr($time_ymdhis, 11, 8);
	
	// 참여 학생수
	$student_count = rand(20,200);
	
	//-- 아이템 정보 임의 추출
	$query1 = mysql_query(" SELECT * FROM d1_item WHERE itm_status = 'ok' ORDER BY RAND() LIMIT 1 ");
	$itm = mysql_fetch_assoc($query1);

	//-- 프로그램 정보 임의 추출
	$query3 = mysql_query(" SELECT * FROM d1_program WHERE itm_idx = '$itm[itm_idx]' ORDER BY RAND() LIMIT 1 ");
	$pgm = mysql_fetch_assoc($query3);
	
	// 프로그램 옵션 정보 추출(여러개일 수도 있음)
	$query4 = mysql_query(" SELECT * FROM d1_program_option WHERE pgm_idx = '$pgm[pgm_idx]' ");
	
	//-- 프로그램 시간 정보 임의 추출
	$query6 = mysql_query(" SELECT * FROM d1_program_time WHERE pgm_idx = '$pgm[pgm_idx]' ORDER BY RAND() LIMIT 1 ");
	$pgt = mysql_fetch_assoc($query6);

	// 회원 임의 추출
	$query2 = mysql_query(" SELECT * FROM g5_member WHERE mb_level = 3 ORDER BY RAND() LIMIT 1 ");
	$mbr = mysql_fetch_assoc($query2);

	
	//-- 주문 디비 저장, 양쪽 끝에 따옴표 필요함 --//
	$sql4 = " INSERT INTO d1_program_order SET									".
			"	mb_id 					= '{$mbr['mb_id']}'						".
			"	, por_name = '{$mbr['mb_name']}'            					".
			"	, por_email = '{$mbr['mb_email']}'            					".
			"	, por_hp = '{$mbr['mb_hp']}'            						".
             "	, por_status = '{$status_array[array_rand($status_array, 1)]}'	".
            "   , por_reg_dt = '".$time_ymdhis."'								";
	mysql_query($sql4) or die($sql4);	//<===============================
	$insert_por_idx = mysql_insert_id();
	
	// AM, PM 설정
	if($itm['itm_idx'] == 1) {
		$ampm_yn = $ampm_array[array_rand($ampm_array, 1)];
	}
	else {
		$ampm_yn = '';
	}
	
	// 총가격
	$total_price = rand(100000,1000000);

	// 카트 입력(일단 입력하고 나중에 총가격 및 기간 업데이트)
	$sql6 = " INSERT INTO d1_program_cart SET	".
			"	por_idx = '$insert_por_idx' 		".
			"	, pgm_idx = '$pgm[pgm_idx]'         ".
			"	, pgt_idx = '$pgt[pgt_idx]'         ".
			"	, pgc_due_date = '".$time_ymd."'	".
			"	, pgc_ampm = '".$ampm_yn."'			".
			"	, pgc_student_count = '".$student_count."'	".
			"	, pgc_teacher_count = '".rand(2,7)."'	".
			"	, pgc_class_count = '".rand(1,10)."'	".
			"	, pgc_each_price = '$pgm[pgm_price]'	".
			"	, pgc_option_price = '6000'	".
			"	, pgc_total_price = '".$total_price."'	".
			"	, pgc_status = 'ok'			".
			"	, pgc_reg_dt = now()                ";
	mysql_query($sql6) or die($sql6);
	//echo $sql6.'<br>';
	$insert_pgc_idx = mysql_insert_id();


	// 장바구니 - 옵션 정보 입력
	for($j=0;$row4=mysql_fetch_array($query4);$j++) {
		$sql5 = " INSERT INTO d1_program_cart_option SET					".
				"	pgc_idx = '{$insert_pgc_idx}'							".
				"	, pgo_idx = '{$row4[pgo_idx]}'            				".
				"	, pco_each_price = '{$row4[pgo_price]}'					".
				"	, pco_student_count = '".$student_count."'				";
		mysql_query($sql5) or die($sql5);
	}
	
	
	// 주문 가격 업데이트
	$sql8 = " UPDATE d1_program_order SET por_price = '$total_price' WHERE por_idx = '$insert_por_idx' ";
	mysql_query($sql8) or die($sql8);
	//echo $sql8.'<br>';

	

	//-- DOM 보도 자바스크립트가 좀 빨라서 0,1번이 안 보이고 2번부터 보이는 표현상 에러가 있음, 동작에는 무리가 없습니다.
	echo "<script> document.all.cont.innerHTML += '".($i+1).". ".addslashes($sql4)."<br>'; </script>\n";
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
