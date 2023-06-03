<?php
// 직원 작업관리 권한 추가 160621 손지식
include_once('./_common.php');

$g5['title'] = '직원작업관리 권한 추가 페이지';
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
$sql = "	SELECT * FROM {$g5['member_table']} WHERE mb_level = '6' AND mb_leave_date = '' ";
//$sql = "	SELECT * FROM {$g5['member_table']} WHERE mb_level = '6' AND mb_id NOT IN (SELECT mb_id FROM u_company) AND mb_no < 300 ";
//echo $sql;
$result = sql_query($sql,1);
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
//	if($i > 10)
//		break;

	$cnt++;

	//-- 입력값 설정
	$com_name[$i] = '업체명'.$i;

	// 작업관리 메뉴 있는지 확인
	$au1 = sql_fetch("SELECT mb_id FROM {$g5['auth_table']}
							WHERE mb_id = '".$row['mb_id']."' 
									AND au_menu = '750115' LIMIT 1
	");
	
	//-- 정보 입력 --//
	if(!$au1['mb_id']) {
		$sql = " INSERT INTO {$g5['auth_table']} SET		".
				"	mb_id	= '".$row['mb_id']."'					".
				"	, au_menu = '750115'						".
				"	, au_auth = 'r,w,d'							";
		sql_query($sql,1);	//<===============================
		//echo $sql.'<br><br>';
	}

	// 메시지 보임
	echo "<script> document.all.cont.innerHTML += '".$cnt.". ".addslashes($sql)." 처리됨<br>'; </script>\n";
	
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
