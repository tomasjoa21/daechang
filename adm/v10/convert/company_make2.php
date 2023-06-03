<?
// 회원 + 업체 테이블 생성 160317 손지식
include_once("./_common.php");

$g5['title'] = '회원 + 업체 등록 페이지';
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
//-- 화면 표시 
$countgap = 10; // 몇건씩 보낼지 설정
$maxscreen = 40; // 몇건씩 화면에 보여줄건지?
$sleepsec = 200;  // 천분의 몇초간 쉴지 설정


//-- 설정값
$pg_array = array('offline','kcp','lgu','paypal','ksnet','etc');
$status_array = array('pending','ok','ok','ok','cancel');
$ampm_array = array('am','pm');

flush();
ob_flush();

// 회원 100명 생성
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=100022;$i<300000;$i++) {
	// 데모 테스트용은 6개만 보여주세요.
//	if($i>100021)
//		break;

	$cnt++;

	//-- 회원아이디 설정
	$mb_id[$i] = 'company'.$i;
	$mb_name[$i] = '업체회원'.$i;
	$mb_nick[$i] = '업체닉'.$i;
	$mb_email[$i] = 'company'.$i.'@company.com';
	$mb_hp[$i] = '010-'.rand(1111,9999).'-'.rand(1111,9999);
	$mb_level[$i] = '4';	// 업체 레벨
	
	$mb_zip1[$i] = '122';
	$mb_zip2[$i] = '35';
	$mb_addr1[$i] = '경기 남양주시 경춘로 883-36';
	$mb_addr2[$i] = '3번지';
	$mb_addr3[$i] = '(금곡동, 마을공동회관)';
	$mb_addr_jibeon[$i] = 'R';
	$mb_login_ip[$i] = '106.245.225.250';
	$mb_ip[$i] = '106.245.225.250';
	$mb_email_certify[$i] = date("Y-m-d H:i:s");
	$mb_mailling[$i] = '1';
	$mb_sms[$i] = '1';
	$mb_open[$i] = '1';

	//-- 업체 회원정보 입력 --//
	$sql1 = " INSERT INTO g5_member SET											".
			"	mb_id	= '".$mb_id[$i]."'										".
			"	, mb_password = '*00A51F3F48415C7D4E8908980D443C29C69B60C9'		".
			"	, mb_name = '".$mb_name[$i]."'									".
			"	, mb_nick = '".$mb_nick[$i]."'									".
			"	, mb_nick_date = now()											".
			"	, mb_email = '".$mb_email[$i]."'								".
			"	, mb_hp = '".$mb_hp[$i]."'										".
			"	, mb_level = '".$mb_level[$i]."'								".
			"	, mb_zip1 = '".$mb_zip1[$i]."'									".
			"	, mb_zip2 = '".$mb_zip2[$i]."'									".
			"	, mb_addr1 = '".$mb_addr1[$i]."'								".
			"	, mb_addr2 = '".$mb_addr2[$i]."'								".
			"	, mb_addr3 = '".$mb_addr3[$i]."'								".
			"	, mb_addr_jibeon = '".$mb_addr_jibeon[$i]."'					".
			"	, mb_login_ip = '".$mb_login_ip[$i]."'							".
			"	, mb_datetime = now()											".
			"	, mb_ip = '".$mb_ip[$i]."'										".
			"	, mb_email_certify = '".$mb_email_certify[$i]."'				".
			"	, mb_mailling = '".$mb_mailling[$i]."'							".
			"	, mb_sms = '".$mb_sms[$i]."'									".
			"	, mb_open = '".$mb_open[$i]."'									".
			"	, mb_open_date = now()											";
	sql_query($sql1,1);	//<===============================
	//echo $sql1.'<br><br>';

	//-- 입력값 설정
	$com_name[$i] = '업체명'.$i;
	$com_names[$i] = '업체명 히스토리'.$i;
	$com_president[$i] = '대표자'.$i;
	$com_tel[$i] = '070-'.rand(1111,9999).'-'.rand(1111,9999);
	$com_manager[$i] = '업체 담당자'.$i;
	$com_manager_hp[$i] = '010-'.rand(1111,9999).'-'.rand(1111,9999);

	//-- 업체정보 입력 --//
	$sql2 = " INSERT INTO u_company SET											".
			"	mb_id	= '".$row['mb_id']."'										".
			"	, com_name = '".$com_name[$i]."'									".
			"	, com_names = '".$com_names[$i]."'									".
			"	, com_president = '".$com_president[$i]."'									".
			"	, com_tel = '".$com_tel[$i]."'									".
			"	, com_manager = '".$com_manager[$i]."'								".
			"	, com_manager_hp = '".$com_manager_hp[$i]."'										".
			"	, com_status = 'pending'									".
			"	, com_reg_dt = now()											";
	sql_query($sql2,1);	//<===============================
	$com_idx[$i] = sql_insert_id();
	//echo $sql2.'<br><br>';
	
	// 영업자 아이디 2명
	$sql3 = "SELECT mb_id,mb_name
					, ( SELECT trm_idx FROM jt_term_relation 
						WHERE tmr_db_table = 'member' AND tmr_db_key = 'department' AND tmr_db_id = mbr.mb_id ) AS trm_idx
				FROM g5_member AS mbr WHERE mb_level = '6' ORDER BY RAND() LIMIT 2
	";
	$result3 = sql_query($sql3,1);
	for($i1=0;$row=sql_fetch_array($result3);$i1++) {
		$mb_id_saler[$i] .= '^'.$row['mb_id'].'^'.$row['mb_name'].'^'.$row['trm_idx'].'^,';

		//-- 디비 입력 --//
		$cmm_memo[$i] = $row['mb_name'].'('.$row['mb_id'].') - '.$com_name[$i].'('.$com_idx[$i].')';
		$sql4 = " INSERT INTO u_company_member SET					".
				"	mb_id	= '".$row['mb_id']."'										".
				"	, com_idx = '".$com_idx[$i]."'										".
				"	, cmm_memo = '".$cmm_memo[$i]."'									".
				"	, cmm_status = 'ok'									".
				"	, cmm_reg_dt = now()											";
		sql_query($sql4,1);	//<===============================
		//echo $sql4.'<br><br>';

	}

	//-- 업체정보 업데이트 --//
	$sql5 = " UPDATE u_company SET mb_id_saler	= '".$mb_id_saler[$i]."' WHERE com_idx = '".$com_idx[$i]."' ";
	sql_query($sql5,1);	//<===============================
	//echo $sql5.'<br><br>';

	// 메시지 보임
	echo "<script> document.all.cont.innerHTML += '".$cnt.". ".addslashes($sql1)." 처리됨<br>'; </script>\n";

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
