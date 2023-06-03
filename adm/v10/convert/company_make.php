<?php
// 업체 테이블 생성 160301 손지식
include_once('./_common.php');

$g5['title'] = '업체 등록 페이지';
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
$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 20000;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 50; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

// 대상 디비 전체 추출
$sql = "	SELECT * FROM {$g5['member_table']} WHERE mb_level = '4' AND mb_id NOT IN (SELECT mb_id FROM u_company) ";
//$sql = "	SELECT * FROM {$g5['member_table']} WHERE mb_level = '4' AND mb_id NOT IN (SELECT mb_id FROM u_company) AND mb_no < 300 ";
//echo $sql;
$result = sql_query($sql,1);
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
//	if($i > 10)
//		break;

	$cnt++;

	//-- 입력값 설정
	$com_name[$i] = '업체명'.$i;
	$com_names[$i] = '업체명 히스토리'.$i;
	$com_president[$i] = '대표자'.$i;
	$com_tel[$i] = '070-'.rand(1111,9999).'-'.rand(1111,9999);
	$com_manager[$i] = '업체 담당자'.$i;
	$com_manager_hp[$i] = '010-'.rand(1111,9999).'-'.rand(1111,9999);
	
	// 영업자 아이디
	$mb2 = sql_fetch(" SELECT mb_id FROM g5_member WHERE mb_level = '6' ORDER BY RAND() LIMIT 1 ");
	
	//-- 회원정보 입력 --//
	$sql1 = " INSERT INTO u_company SET											".
			"	mb_id	= '".$row['mb_id']."'										".
			"	, mb_id_saler = '".$mb2['mb_id']."'										".
			"	, com_name = '".$com_name[$i]."'									".
			"	, com_names = '".$com_names[$i]."'									".
			"	, com_president = '".$com_president[$i]."'									".
			"	, com_tel = '".$com_tel[$i]."'									".
			"	, com_manager = '".$com_manager[$i]."'								".
			"	, com_manager_hp = '".$com_manager_hp[$i]."'										".
			"	, com_status = 'pending'									".
			"	, com_reg_dt = now()											";
	sql_query($sql1,1);	//<===============================
	//echo $sql1.'<br><br>';
	
	
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
