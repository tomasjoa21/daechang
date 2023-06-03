<?php
// 업체 테이블 생성 160301 손지식
include_once('./_common.php');

$g5['title'] = '업체 정보 수정 페이지';
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
$sql = "	SELECT * FROM {$g5['company_table']} ORDER BY com_idx DESC ";
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

	// 회원의 소속 조직
	$trm1 = sql_fetch("SELECT tmr_db_id, trm_idx, mb_name
							FROM {$g5['term_relation_table']} AS tmr
								LEFT JOIN {$g5['member_table']} AS mbr ON mbr.mb_id = tmr.tmr_db_id
							WHERE tmr_db_table = 'member' 
									AND tmr_db_key = 'department'
							ORDER BY RAND() LIMIT 1
	");
	$row['mb_id_salers'] = '^'.$trm1['tmr_db_id'].'^'.$trm1['mb_name'].'^,';
	
	//-- 정보 입력 --//
	$sql1 = " INSERT INTO u_company_member SET			".
			"	mb_id_saler	= '".$trm1['tmr_db_id']."'					".
			"	, trm_idx_department = '".$trm1['trm_idx']."'	".
			"	, com_idx = '".$row['com_idx']."'					".
			"	, cmm_status = 'ok'								".
			"	, cmm_reg_dt = now()								";
	sql_query($sql1,1);	//<===============================
	//echo $sql1.'<br><br>';

	//-- 정보 입력 --//
	$sql1 = " UPDATE u_company SET			".
			"	mb_id_salers	= '".$row['mb_id_salers']."'		".
			"	WHERE com_idx = '".$row['com_idx']."'	";
	sql_query($sql1,1);	//<===============================
	
	
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
