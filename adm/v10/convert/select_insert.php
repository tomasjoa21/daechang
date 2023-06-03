<?
// 공통파일 추가
include_once("./_common.php");

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
//-- 화면 표시 
$countgap = 10; // 몇건씩 보낼지 설정
$maxscreen = 40; // 몇건씩 화면에 보여줄건지?
$sleepsec = 200;  // 천분의 몇초간 쉴지 설정

//-- 설정값
$pg_array = array('offline','kcp','lgu','paypal','ksnet','etc');
$status_array = array('pending','ok','ok','ok','cancel');
$ampm_array = array('am','pm');

    
//// 상품 10개 생성
//for($i=0;$i<10;$i++) {
//	// 데모 테스트용은 6개만 보여주세요.
////	if($i<1)
////		break;
////	echo "<br><br>";
//
//	//-- 회원아이디 설정
//	$prd_name[$i] = 'K'.$i;
//	$prd_brief[$i] = 'brief'.rand(11,99).'brief'.rand(11,99).'brief'.rand(11,99);
//	$prd_content[$i] = 'contents'.rand(11,99).'contents'.rand(11,99).'contents'.rand(11,99).'contents'.rand(11,99);
//	$prd_price[$i] = rand(11,99)*1000;
//	$prd_price_tex[$i] = $prd_price[$i]*0.1;
//
//	//-- 회원정보 입력 --//
//	$sql1 = " INSERT INTO u_product SET											".
//			"	prd_name	= '".$prd_name[$i]."'								".
//			"	, prd_brief = '".$prd_brief[$i]."'								".
//			"	, prd_content = '".$prd_content[$i]."'							".
//			"	, prd_price = '".$prd_price[$i]."'								".
//			"	, prd_price_tex = '".$prd_price_tex[$i]."'						".
//			"	, prd_status = 'ok'												".
//			"	, prd_reg_dt = now()											";
//	sql_query($sql1,1);	//<===============================
//	//echo $sql1.'<br><br>';
//
//	
//
//	//-- DOM 보도 자바스크립트가 좀 빨라서 0,1번이 안 보이고 2번부터 보이는 표현상 에러가 있음, 동작에는 무리가 없습니다.
//	echo "<script> document.all.cont.innerHTML += '".($i+1).". ".addslashes($sql1)."<br>'; </script>\n";
//	//echo "+";
//    flush();
//    ob_flush();
//    ob_end_flush();
//    usleep($sleepsec);
//    if ($i % $countgap == 0)
//    {
//        echo "<script> document.all.cont.innerHTML += '<br>'; document.body.scrollTop += 1000; </script>\n";
//    }
//
//    // 화면을 지운다... 부하를 줄임
//    if ($i % $maxscreen == 0)
//        echo "<script> document.all.cont.innerHTML = ''; document.body.scrollTop += 1000; </script>\n";
//
//}
    

// 변수 설정, 필드 구조 및 prefix 추출
//$table_name = 'recruit';
$table_name = 'applicant';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));

// 입력 필드 배열 설정
$skips[] = $pre.'_idx';	// 건너뛸 변수 배열
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
        $sql_fields[] = $fields[$i];
    //}
}

$sql = "INSERT INTO {$g5_table_name} (".implode(",",$sql_fields).")
        SELECT ".implode(",",$sql_fields)." FROM {$g5_table_name}
        WHERE (1)
";
//echo $sql.'<br>';
sql_query($sql,1);
?>

<script> document.all.cont.innerHTML += "<br><br><br>총 <?php echo number_format($i) ?>건 작업 완료<br><br><font color=crimson><b>[끝]</b></font>"; document.body.scrollTop += 1000; </script>
</body>
</html>
