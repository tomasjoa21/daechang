<?
// 공통파일 추가
include_once("./_common.php");

if($member['mb_level'] < 8)
	alert('관리자로 로그인해 주세요.',G5_URL.'/_u/convert/excel_member2.php');

//-- 엑셀 변환 파일 --//
require_once "./reader.php";
$data = new Spreadsheet_Excel_Reader();

// 엘셀 소스 파일
$data->setOutputEncoding('euc-kr'); // 한글화! (서버마다 좀 다르네요.)
//$data->read('./excel/1center.xls');
$data->read('./excel/2center.xls');
error_reporting(E_ALL ^ E_NOTICE);

$g5['title'] = '엑셀 입력 페이지';
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

for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	// 데모 테스트용은 6개만 보여주세요.
//	if($i>20)
//		break;

	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		//echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
		//echo iconv('euc-kr','utf-8',$data->sheets[0]['cells'][$i][$j])." / ";
		$item[$i][$j] = iconv('euc-kr','utf-8',$data->sheets[0]['cells'][$i][$j]);
		//echo $data->sheets[0]['cells'][$i][$j].' / '.$item[$i][$j].'<br>';
	}

	//-- 첫번째 항목이 숫자인 경우만, 제대로 된 항목이라고 본다. --//
	if(!is_numeric( $item[$i][1] ))
//	if($i<=1)
		continue;
	else {
		$cnt++;

		// 엑셀 입력
		$sql =	" INSERT INTO g5_member SET			".
					"	mb_id = '".$item[$i][3]."'		".
					"	,mb_password = '".sql_password($item[$i][4])."'		".
					"	,mb_name = '".$item[$i][2]."'		".
					"	,mb_nick = '".$item[$i][2]."'		".
					"	,mb_email = '".$item[$i][6]."'		".
					"	,mb_level = '6'		".
					"	,mb_hp = '".$item[$i][5]."'		".
					"	,mb_certify = 'hp'		".
					"	,mb_datetime = now()		".
					"	,mb_email_certify = now()		".
					"	,mb_mailling = 1		".
					"	,mb_sms = 1		".
					"	,mb_open = 1		";
		sql_query($sql,1);
//		echo $sql.'<br>';

		// 조직 정보 입력
		sql_query(" INSERT INTO jt_term_relation SET trm_idx = '".$item[$i][9]."', tmr_db_id = '".$item[$i][3]."', tmr_db_table = 'member', tmr_db_key = 'department', tmr_reg_dt = now() ");	//
//		echo " INSERT INTO jt_term_relation SET trm_idx = '".$item[$i][9]."', tmr_db_id = '".$item[$i][3]."', tmr_db_table = 'member', tmr_db_key = 'department', tmr_reg_dt = now() ".'<br>';
		
		// 메타 정보 입력
		sql_query(" INSERT INTO jt_meta SET mta_db_id = '".$item[$i][3]."', mta_key = 'mb_enter_date', mta_value = '2016-05-30', mta_country = 'ko_KR', mta_db_table = 'member', mta_reg_dt = now() ");	//
//		echo " INSERT INTO jt_meta SET mta_db_id = '".$item[$i][3]."', mta_value = '2016-05-30', mta_country = 'ko_KR', mta_db_table = 'member', mta_db_key = 'mb_enter_date', mta_reg_dt = now() ".'<br>';
		sql_query(" INSERT INTO jt_meta SET mta_db_id = '".$item[$i][3]."', mta_key = 'mb_position', mta_value = '".$item[$i][11]."', mta_country = 'ko_KR', mta_db_table = 'member', mta_reg_dt = now() ");	//
//		echo " INSERT INTO jt_meta SET mta_db_id = '".$item[$i][3]."', mta_key = 'mb_position', mta_value = '".$item[$i][11]."', mta_country = 'ko_KR', mta_db_table = 'member', mta_reg_dt = now() ".'<br>';
		
		// 메뉴권한
		$emp_auth[$i] = ($item[$i][11] >= 6) ? 'r,w,d' : 'w,d'; // 사원관리 권한
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '710110', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '710120', '".$emp_auth[$i]."') ");	// 사원관리 권한 설정
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '720090', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '720100', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '740100', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '740110', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '740120', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '740130', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '750100', 'r,w,d') ");
		sql_query(" INSERT INTO `g5_auth` VALUES('".$item[$i][3]."', '770200', 'r,w,d') ");
		

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
}
?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>
