<?php
// 측정값 태그 이름을 일괄 변경합니다. 설비1 기준으로 나머지를 수정하는 겁니다.
// 실행주소: http://hanjoo.epcs.co.kr/adm/v10/convert/meta_change1.php
include_once('./_common.php');

$g5['title'] = '메타 정보 변경';
include_once(G5_PATH.'/head.sub.php');
?>
<div class="" style="padding:10px;">
	<span style='display:block;'>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
include_once(G5_PATH.'/tail.sub.php');


$mms = get_table_meta('mms', 'mms_idx', 58);
// print_r2($mms);


$mms_array = array(59,60,61);
for($x=0;$x<sizeof($mms_array);$x++) {

// 대상 디비 전체 추출
$sql = "SELECT mta_idx, mta_key, mta_value
		, SUBSTRING_INDEX(SUBSTRING_INDEX(mta_key,'-',-2),'-',1) AS dta_type
		, SUBSTRING_INDEX(mta_key,'-',-1) AS dta_no
		FROM g5_5_meta
		WHERE mta_key LIKE 'dta_type_label%' 
		AND mta_db_table = 'mms' AND mta_db_id = '".$mms_array[$x]."'
		ORDER BY convert(dta_type, decimal), convert(dta_no, decimal)
";
// echo $sql;
// exit;
$result = sql_query($sql,1);
$cnt=0; // 카운터를 세는 이유가 있네 (이거 안 하니까 자꾸 두번째부터 보임!)
for($i=0;$row=sql_fetch_array($result);$i++) {
	$cnt++;
	// if($i > 5)
	// 	break;

	// 변수 재설정{ =====================

    // 정보 입력
	// $sql1 = "   UPDATE {$table2} SET
	// 				it_keys = '".$row['it_keys']."'
	// 			--    ,it_more	= '".$row['it_more']."'
	// 			WHERE it_id = '".$row['it_id']."'
	// ";
	// sql_query($sql1,1);

	if($mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]) {
		$ar['mta_db_table'] = 'mms';
		$ar['mta_db_id'] = $mms_array[$x];
		$ar['mta_key'] = 'dta_type_label-'.$row['dta_type'].'-'.$row['dta_no'];
		$ar['mta_value'] = $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']];
		meta_update($ar);
		// print_r2($ar);
		unset($ar);

		// 메시지 보임
		echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$mms_array[$x]."설비 ".$row['dta_type'].'-'.$row['dta_no']." ".$mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]." 처리됨<br>'; </script>".PHP_EOL;
	}

	
}

}
?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($i) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>
