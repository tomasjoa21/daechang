<?php
// 업체 테이블 생성 160420 손지식
include_once('./_common.php');

$g5['title'] = '통계 업데이트 페이지';
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
$countgap = 25; // 몇건씩 보낼지 설정
$sleepsec = 20000;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 50; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

// 대상 디비 전체 추출
$sql = "	SELECT * FROM {$g5['statistics_table']} WHERE sta_idx > 100000 ORDER BY sta_idx ASC ";
$result = sql_query($sql,1);
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
//	if($i > 10)
//		break;

	$cnt++;

	//-- down_idxs 값
	$dep_down_idxs[$i] = $department_down_idxs[$row['sta_department_idx']];
	$dep_sort[$i] = $department_sort[$row['sta_department_idx']];
	
	//-- 정보 UPDATE --//
	$sql1 = " UPDATE u_statistics SET											".
			"	sta_department_down_idxs	= '".$dep_down_idxs[$i]."'			".
			"	, sta_department_sort = '".$dep_sort[$i]."'						".
			" WHERE sta_idx = '".$row['sta_idx']."'						";
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
