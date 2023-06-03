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
// 회원 100명 생성
for($i=0;$i<100000;$i++) {
//for($i=0;$i<10;$i++) {
	// 데모 테스트용은 6개만 보여주세요.
//	if($i>0)
//		break;
//	echo "<br><br>";
	
	//-- 임의 날짜 및 시간 추출 (오늘 이후 3일~120일 사이 아무 날짜)
	$time = rand(86400*-120,86400*120);
	$server_time = time() + $time;
	$time_ymdhis = date('Y-m-d H:i:s', $server_time);
	$time_ymd = substr($time_ymdhis, 0, 10);
	$time_his = substr($time_ymdhis, 11, 8);

	//get_dayAddDate($dateInfo,$dayNum)
	/*
	echo $time_ymdhis."<br>";
	echo $time_ymd."<br>";
	echo $time_his."<br>";
	echo "//=======<br>";
	echo get_dayAddDate($time_ymd,5)."<br>";
	echo get_dayAddDate($time_ymd,5)." ".$time_his."<br>";
	*/
	
	// 업체회원
	$mb_4 = sql_fetch(" SELECT * FROM g5_member WHERE mb_level = 4 ORDER BY RAND() LIMIT 1 ");
	$mb_id_company[$i] = $mb_4['mb_id'];
	$cam_mb_name[$i] = $mb_4['mb_name'];
	$cam_mb_email[$i] = $mb_4['mb_email'];
	$cam_mb_hp[$i] = $mb_4['mb_hp'];
	$cam_company_tel[$i] = $mb_4['mb_hp'];
	$cam_company_name[$i] = "회사이름".($i+1);
	$cam_president_name[$i] = "대표자".($i+1);

	// 영업자 회원정보
	$mb_6 = sql_fetch(" SELECT * FROM g5_member WHERE mb_level = 6 ORDER BY RAND() LIMIT 1 ");
	$mb_id_saler[$i] = $mb_6['mb_id'];
	$cam_mb_saler[$i] = $mb_6['mb_name'];

	// 상품 정보
	$prd_id = sql_fetch(" SELECT prd_idx,prd_name,prd_times FROM u_product WHERE prd_status = 'ok' ORDER BY RAND() LIMIT 1 ");
	$prd_idx[$i] = $prd_id['prd_idx'];
	$prd_name[$i] = $prd_id['prd_name'];
	$prd_times[$i] = $prd_id['prd_times'];

	
	if($i % 2 == 0)
		$cam_type[$i] = "visit";
	else
		$cam_type[$i] = "delivery";
	

	$cam_name[$i] = "캠페인명".($i+1);
	$cam_brief[$i] = "cam_brief".($i+1);
	$cam_reviewer_yn[$i] = '1';
	$cam_recruit_count[$i] = rand(1,9);
	$cam_info_reg_dt[$i] = $time_ymdhis; //캠페인정도 등록일
	$cam_recruit_start_dt[$i] = $time_ymdhis; //크리에이터 모집 시작일
	$cam_recruit_end_dt[$i] = get_dayAddDate($time_ymd,5)." ".$time_his;
	$cam_notice_dt[$i] = get_dayAddDate(date(substr($cam_recruit_end_dt[$i], 0, 10)),2)." ".$time_his;
	$cam_review_start_dt[$i] = get_dayAddDate(date(substr($cam_notice_dt[$i], 0, 10)),1)." ".$time_his;
	$cam_review_end_dt[$i] = get_dayAddDate(date(substr($cam_review_start_dt[$i], 0, 10)),5)." ".$time_his;
	$cam_select_dt[$i] = get_dayAddDate(date(substr($cam_review_end_dt[$i], 0, 10)),1)." ".$time_his;
	$cam_zip1[$i] = '122';
	$cam_zip2[$i] = '35';
	$cam_addr1[$i] = '경기 남양주시 경춘로 883-36';
	$cam_addr2[$i] = '3번지';
	$cam_addr3[$i] = '(금곡동, 마을공동회관)';
	$cam_addr_jibeon[$i] = 'R';

	// 캠페인 정보 입력
	$sql1 = " INSERT INTO u_campaign SET										".
			"	mb_id_company	= '".$mb_id_company[$i]."'						".
			"	, mb_id_saler = '".$mb_id_saler[$i]."'							".
			"	, prd_idx = '".$prd_idx[$i]."'									".
			"	, prd_times_count = '1'											".
			"	, cam_mb_name = '".$cam_mb_name[$i]."'							".
			"	, cam_mb_tel = '".$cam_mb_hp[$i]."'							".
			"	, cam_mb_email = '".$cam_mb_email[$i]."'						".
			"	, cam_mb_saler = '".$cam_mb_saler[$i]."'						".
			"	, cam_company_name = '".$cam_company_name[$i]."'				".
			"	, cam_company_tel = '".$cam_company_tel[$i]."'					".
			"	, cam_president_name = '".$cam_president_name[$i]."'			".
			"	, prd_name = '".$prd_name[$i]."'								".
			"	, cam_type = '".$cam_type[$i]."'								".
			"	, cam_name = '".$cam_name[$i]."'								".
			"	, cam_brief = '".$cam_brief[$i]."'								".
			"	, cam_reviewer_yn = '".$cam_reviewer_yn[$i]."'					".
			"	, cam_recruit_count = '".$cam_recruit_count[$i]."'				".
			"	, cam_info_reg_dt = '".$cam_info_reg_dt[$i]."'					".
			"	, cam_recruit_start_dt = '".$cam_recruit_start_dt[$i]."'		".
			"	, cam_recruit_end_dt = '".$cam_recruit_end_dt[$i]."'			".
			"	, cam_notice_dt = '".$cam_notice_dt[$i]."'						".
			"	, cam_review_start_dt = '".$cam_review_start_dt[$i]."'			".
			"	, cam_review_end_dt = '".$cam_review_end_dt[$i]."'				".
			"	, cam_select_dt = '".$cam_select_dt[$i]."'						".
			"	, cam_point_type = 'group'										".
			"	, cam_notice_status = 'pending'									".
			"	, cam_zip1 = '".$cam_zip1[$i]."'								".
			"	, cam_zip2 = '".$cam_zip2[$i]."'								".
			"	, cam_addr1 = '".$cam_addr1[$i]."'								".
			"	, cam_addr2 = '".$cam_addr2[$i]."'								".
			"	, cam_addr3 = '".$cam_addr3[$i]."'								".
			"	, cam_addr_jibeon = '".$cam_addr_jibeon[$i]."'					".
			"	, cam_status = 'pending'										".
			"	, cam_reg_dt = '".$time_ymdhis."'								";
	sql_query($sql1,1);	//<===============================
	//echo $sql1.'<br><br>';

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

//특정 날짜에 일수를 더한 날짜를 반환해 주는 함수
function get_dayAddDate($dateInfo,$dayNum){//임채완이 재정의 한 함수(일수계산)
	$dtArr = explode('-',$dateInfo);
	$year_ = $dtArr[0];
	$month_ = $dtArr[1];
	$day_ = $dtArr[2];
	$dt = mktime(0,0,0,$month_,$day_+$dayNum,$year_);

	return date("Y-m-d",$dt);
} 
?>

<script> document.all.cont.innerHTML += "<br><br><br>총 <?php echo number_format($i) ?>건 작업 완료<br><br><font color=crimson><b>[끝]</b></font>"; document.body.scrollTop += 1000; </script>
</body>
</html>
