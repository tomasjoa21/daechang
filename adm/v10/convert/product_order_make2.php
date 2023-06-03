<?
// 신청 테이블 생성 160318 손지식
include_once("./_common.php");

$g5['title'] = '신청 등록 페이지';
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
$dmn_price_array = array('33000','33000','33000','77000','22000');
$company_array = array('nct','nct','nct','nct','nct','nctsolution','shinsa');
$channel_array = array('offline','offline','offline','offline','offline','offline','kcp','lgu','paypal','ksnet','etc');
$method_array = array('creditcard','creditcard','creditcard','creditcard','creditcard','nobankdeposit','virtualaccount','offlinepay','cash');
$position_array = array('사원','사원','주임','주임','대리','대리','대리','팀장','팀장','센터장');


flush();
ob_flush();

$cnt=0;
for($i=0;$i<100000;$i++) {
	// 데모 테스트용은 6개만 보여주세요.
//	if($i>6)
//		break;
	$cnt++;

	// 업체 회원 선택
	$mb1 = sql_fetch(" SELECT mb_id FROM g5_member WHERE mb_level = '4' ORDER BY RAND() LIMIT 1 ");
	// 업체 선택
	$com1 = sql_fetch(" SELECT com_idx, com_name FROM u_company WHERE mb_id = '".$mb1['mb_id']."' ORDER BY RAND() LIMIT 1 ");
	
	// 영업자 회원 선택
	$mb2 = sql_fetch(" SELECT mb_id,mb_name FROM g5_member WHERE mb_level = '6' ORDER BY RAND() LIMIT 1 ");
	// 영업자 메타 확장값(들) 추출
	$sql = " SELECT GROUP_CONCAT(CONCAT(mta_key, '=', COALESCE(mta_value, 'NULL'))) AS mbr_metas FROM {$g5['meta_table']} 
				WHERE mta_db_table = 'member' AND mta_db_id = '".$mb2['mb_id']."' ";
	$mta1 = sql_fetch($sql,1);
	$pieces = explode(',', $mta1['mbr_metas']);
	foreach ($pieces as $piece) {
		list($key, $value) = explode('=', $piece);
		$mb2[$key] = $value;
	}
	unset($pieces);unset($piece);

	// 영업자 조직 선택
	$trm1 = sql_fetch(" SELECT trm_idx FROM jt_term_relation 
						WHERE tmr_db_table = 'member' AND tmr_db_key = 'department' AND tmr_db_id = '".$mb2['mb_id']."' ");

	// 상품 선택
	$prd1 = sql_fetch(" SELECT * FROM u_product WHERE prd_status = 'ok' ORDER BY RAND() LIMIT 1 ");

	//-- 임의 날짜 및 시간 추출
	$time = rand(86400*-200, 86400*1);	// -200일 ~ 내일 사이
	$server_time = time() + $time;
	$server_time2 = time() + $time + 86400;	// +1일 후
	$server_time9 = time() + $time - 86400*31;	// -31일 전
	$time_ymdhis = date('Y-m-d H:i:s', $server_time);
	$time_ymdhis2 = date('Y-m-d H:i:s', $server_time2);
	$time_ymd9 = date('Y-m-d H:i:s', $server_time9);
	$time_ymd = substr($time_ymdhis, 0, 10);
	$time_his = substr($time_ymdhis, 11, 8);

	//-- 주문 정보 입력 --//
	$sql1 = " INSERT INTO u_product_order SET							".
			"	prd_idx	= '".$prd1['prd_idx']."'								".
			"	, mb_id	= '".$mb1['mb_id']."'								".
			"	, mb_id_saler	= '".$mb2['mb_id']."'						".
			"	, pdo_prd_name = '".$prd1['prd_name']."'					".
			"	, pdo_prd_times = '".$prd1['prd_times']."'					".
			"	, pdo_recruit_count = '".$prd1['prd_recruit_count']."'		".
			"	, pdo_price = '".$prd1['prd_price']."'							".
			"	, pdo_pay_status = 'partial'									".
			"	, pdo_payall_dt = '".$time_ymdhis."'							".
			"	, pdo_status = 'ok'											".
			"	, pdo_reg_dt = '".$time_ymdhis."'								";
	sql_query($sql1,1);	//<===============================
	$pdo_idx[$i] = sql_insert_id();
	//echo $sql1.'<br><br>';

	// 결제 정보 입력, 두번으로 나누어서 결제한 걸로 보자. 1차-50%, 2차-완불
	for($j=0;$j<2;$j++) {
		if($j==0) {
			$opa_patrial_yn = 1;
			$opa_amount = 100000;
			$opa_pay_dt = $time_ymdhis;
			$sta_share = 0;
		}
		else {
			$opa_patrial_yn = 0;
			$opa_amount = $prd1['prd_price'] - 100000;
			$opa_pay_dt = $time_ymdhis2;
			$sta_share = $prd1['prd_share'];

			// 주문상품 정보 결제완료로 업데이트
			$sql3 = " UPDATE u_product_order SET pdo_pay_status = 'payall', pdo_payall_dt = '".$time_ymdhis2."' WHERE pdo_idx = '".$pdo_idx[$i]."' ";
			sql_query($sql3,1);
			
		}
		$sql2 = " INSERT INTO u_order_payment SET								".
				"	pdo_idx	= '".$pdo_idx[$i]."'									".
				"	, mb_id_saler	= '".$mb2['mb_id']."'							".
				"	, trm_idx	= '".$trm1['trm_idx']."'								".
				"	, opa_type	= 'payment'										".
				"	, opa_amount	= '".$opa_amount."'							".
				"	, opa_partial_yn	= '".$opa_partial_yn."'						".
				"	, opa_company	= '".$company_array[rand(0,sizeof($company_array)-1)]."'	".
				"	, opa_channel	= '".$channel_array[rand(0,sizeof($channel_array)-1)]."'	".
				"	, opa_method	= '".$method_array[rand(0,sizeof($method_array)-1)]."'	".
				"	, opa_card_no	= '1111-2222-3333-4444'						".
				"	, opa_card_owner	= '홍길동'									".
				"	, opa_card_valid	= '03/20'									".
				"	, opa_status = 'ok'												".
				"	, opa_pay_dt = '".$opa_pay_dt."'								".
				"	, opa_reg_dt = '".$opa_pay_dt."'								";
		sql_query($sql2,1);	//<===============================
		$opa_idx[$i][$j] = sql_insert_id();
		//echo $sql2.'<br><br>';
		
		// 매출 통계 입력	============
		$sql7 = " INSERT INTO u_statistics SET												".
				"	mb_id = '".$mb1['mb_id']."'												".
				"	, mb_id_saler = '".$mb2['mb_id']."'									".
				"	, mb_id_manager = '".$mb2['mb_id']."'								".
				"	, prd_idx = '".$prd1['prd_idx']."'											".
				"	, pdo_idx = '".$pdo_idx[$i]."'											".
				"	, opa_idx = '".$opa_idx[$i][$j]."'											".
				"	, trm_idx_department = '".$trm1['trm_idx']."'								".
				"	, sta_department = '".$department_name[$trm1['trm_idx']]."'				".
				"	, sta_department_up_name = '".$department_up_name[$trm1['trm_idx']]."'		".
				"	, sta_saler_name = '".$mb2['mb_name']."'								".
				"	, sta_saler_position = '".$position_array[rand(0,sizeof($position_array)-1)]."'	".
				"	, sta_saler_enter_date = '".$time_ymd9."'						".
				"	, sta_manager_name = '".$mb2['mb_name']."'							".
				"	, sta_prd_name = '".$prd1['prd_name']."'							".
				"	, sta_prd_type = '".$prd1['prd_type']."'									".
				"	, sta_opa_type = 'payment'									".
				"	, sta_opa_amount = '".$opa_amount."'								".
				"	, sta_share = '".$sta_share."'											".
				"	, sta_opa_company = '".$company_array[rand(0,sizeof($company_array)-1)]."'	".
				"	, sta_opa_channel = '".$channel_array[rand(0,sizeof($channel_array)-1)]."'	".
				"	, sta_opa_method = '".$method_array[rand(0,sizeof($method_array)-1)]."'	".
				"	, sta_status = 'ok'													".
				"	, sta_pay_dt = '".$opa_pay_dt."'									".
				"	, sta_update_dt = '".$opa_pay_dt."'									".
				"	, sta_reg_dt = '".$opa_pay_dt."'									";
		sql_query($sql7,1);
		
	}
	
	// 원페이지(사이트)가 있는 경우 입력
	if($prd1['prd_onepage_type'] != 'none') {
		// 만료일 추출
		$server_time3 = time() + $time + 86400*31*$prd1['prd_onepage_period'];	// 서비스 개월 수 계산
		$server_time4 = time() + $time + 86400*31;	// +1달
		$time_ymd3 = date('Y-m-d', $server_time3);
		$time_ymd4 = date('Y-m-d', $server_time4);
		$sql4 = " INSERT INTO u_site SET											".
				"	pdo_idx	= '".$pdo_idx[$i]."'									".
				"	, com_idx	= '".$com1['com_idx']."'								".
				"	, mb_id	= '".$mb1['mb_id']."'									".
				"	, mb_id_saler	= '".$mb2['mb_id']."'							".
				"	, sit_id	= '".$mb1['mb_id']."'									".
				"	, sit_name	= '".$com1['com_name']."'							".
				"	, sit_type	= 'onepage'										".
				"	, sit_server_ip	= '192.168.0.12'									".
				"	, sit_expire_date	= '".$time_ymd3."'							".
				"	, sit_setting_price = '".$prd1['prd_setting_price']."'				".
				"	, sit_make_price = '".$prd1['prd_make_price']."'					".
				"	, sit_day_price = '".$prd1['prd_day_price']."'						".
				"	, sit_status = 'running'											".
				"	, sit_start_date	= '".$time_ymd4."'								".
				"	, sit_reg_dt = '".$time_ymdhis."'									";
		sql_query($sql4,1);	//<===============================
		$sit_idx[$i] = sql_insert_id();
		//echo $sql4.'<br><br>';
		
		// 도메인 정보 입력
		$sql5 = " INSERT INTO u_domain SET										".
				"	pdo_idx	= '".$pdo_idx[$i]."'									".
				"	, com_idx	= '".$com1['com_idx']."'								".
				"	, dmn_domain	= '".$mb1['mb_id'].".com'						".
				"	, dmn_price = '".$dmn_price_array[rand(0,sizeof($dmn_price_array)-1)]."'	".
				"	, dmn_expire_date	= '".$server_time3."'							".
				"	, dmn_status = 'ok'												".
				"	, dmn_reg_dt = '".$time_ymdhis."'								";
		sql_query($sql5,1);	//<===============================
		$dmn_idx[$i] = sql_insert_id();
		//echo $sql5.'<br><br>';
		
		// 사이트-도메인 정보 입력
		$sql6 = " INSERT INTO u_site_domain SET									".
				"	sit_idx	= '".$sit_idx[$i]."'										".
				"	, dmn_idx	= '".$dmn_idx[$i]."'									".
				"	, std_domain	= 'www.".$mb1['mb_id'].".com'						".
				"	, std_ip	= '192.168.0.12'											".
				"	, std_status = 'ok'												".
				"	, std_reg_dt = '".$time_ymdhis."'								";
		sql_query($sql6,1);	//<===============================
		//echo $sql5.'<br><br>';
		
	}

	
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
