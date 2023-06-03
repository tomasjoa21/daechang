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
$pg_array = array('offline','kcp','lgu','paypal','ksnet','etc');
$status_array = array('pending','ok','ok','ok','cancel');
$ampm_array = array('am','pm');

flush();
ob_flush();

$cnt=0;
for($i=0;$i<30000;$i++) {
	// 데모 테스트용은 6개만 보여주세요.
//	if($i>6)
//		break;
	$cnt++;

	// 업체 회원 선택
	$mb1 = sql_fetch(" SELECT mb_id FROM g5_member WHERE mb_level = '4' ORDER BY RAND() LIMIT 1 ");
	
	// 영업자 회원 선택
	$mb2 = sql_fetch(" SELECT mb_id FROM g5_member WHERE mb_level = '6' ORDER BY RAND() LIMIT 1 ");

	// 영업자 조직 선택
	$trm1 = sql_fetch(" SELECT trm_idx FROM jt_term_relation 
						WHERE tmr_db_table = 'member' AND tmr_db_key = 'department' AND tmr_db_id = '".$mb2['mb_id']."' ");

	// 상품 선택
	$prd1 = sql_fetch(" SELECT prd_idx, prd_price FROM u_product WHERE prd_status = 'ok' ORDER BY RAND() LIMIT 1 ");

	//-- 임의 날짜 및 시간 추출 (오늘 이후 3일~120일 사이 아무 날짜)
	$time = rand(86400*-150, 86400*1);
	$server_time = time() + $time;
	$time_ymdhis = date('Y-m-d H:i:s', $server_time);
	$time_ymd = substr($time_ymdhis, 0, 10);
	$time_his = substr($time_ymdhis, 11, 8);

	//-- 주문 정보 입력 --//
	$sql1 = " INSERT INTO u_product_order SET											".
			"	prd_idx	= '".$prd1['prd_idx']."'										".
			"	, mb_id	= '".$mb1['mb_id']."'										".
			"	, mb_id_saler	= '".$mb2['mb_id']."'										".
			"	, pdo_price = '".$prd1['prd_price']."'									".
			"	, pdo_pay_status = 'payall'									".
			"	, pdo_payall_dt = '".$time_ymd."'									".
			"	, pdo_status = 'ok'									".
			"	, pdo_reg_dt = '".$time_ymd."'											";
	sql_query($sql1,1);	//<===============================
	$pdo_idx[$i] = sql_insert_id();
	//echo $sql1.'<br><br>';

	// 결제 정보 입력 ......

	$sql2 = " INSERT INTO u_order_payment SET											".
			"	pdo_idx	= '".$pdo_idx[$i]."'										".
			"	, mb_id_saler	= '".$mb2['mb_id']."'										".
			"	, trm_idx	= '".$trm1['trm_idx']."'										".
			"	, opa_type	= 'payment'										".
			"	, opa_amount	= '".$prd1['prd_price']."'										".
			"	, opa_company	= 'nct'										".
			"	, opa_channel	= 'offline'										".
			"	, opa_method	= 'creditcard'										".
			"	, opa_status = 'ok'									".
			"	, opa_pay_dt = '".$time_ymd."'									".
			"	, opa_reg_dt = '".$time_ymd."'									";
	sql_query($sql2,1);	//<===============================
	//echo $sql2.'<br><br>';
	
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
