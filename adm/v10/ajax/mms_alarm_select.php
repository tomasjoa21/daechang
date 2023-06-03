<?php
// 호출 파일들
// /adm/v10/maintain_form.php
header("Content-Type: text/plain; charset=utf-8");
include_once('./_common.php');
if(isset($_SERVER['HTTP_ORIGIN'])){
 header("Access-Control-Allow-Origin:{$_SERVER['HTTP_ORIGIN']}");
 header("Access-Control-Allow-Credentials:true");
 header("Access-Control-Max-Age:86400"); //cache for 1 day
}

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
 if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
  header("Access-Control-Allow-Methods:GET,POST,OPTIONS");
 if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
  header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 exit(0);
}

//-- 디폴트 상태 (실패) --//
$response = new stdClass();
$response->result=false;

// 검색기간이 있는 경우
if ($_REQUEST['ser_period']) {
    if ($_REQUEST['ser_period'] == 'month') {
		$st_date = date("Y-m-d", G5_SERVER_TIME-86400*31);
    }
    else if ($_REQUEST['ser_period'] == 'week') {
		$st_date = date("Y-m-d", G5_SERVER_TIME-86400*7);
    }
    else if ($_REQUEST['ser_period'] == 'today') {
		$st_date = date("Y-m-d", G5_SERVER_TIME);
    }
	$sql_reg_dt = " AND arm_reg_dt >= '".$st_date." 00:00:00' ";
}

// 품질에 영향을 주는 요소가 아닌 것 중에서 (AND cod_quality_yn != '1')
$sql = "SELECT arm.cod_idx,trm_idx_category, arm_cod_code, cod_name, cod_memo, COUNT(arm_idx) AS cnt
		FROM {$g5['alarm_table']} AS arm
			LEFT JOIN {$g5['code_table']} AS cod USING(cod_idx)
		WHERE arm.com_idx = '".$_SESSION['ss_com_idx']."'
			AND arm.mms_idx = '".$_REQUEST['mms_idx']."'
			AND cod_quality_yn != '1'
			{$sql_reg_dt}
		GROUP BY arm_cod_code
		ORDER BY cnt DESC
";
// echo $sql.'<br>';
$rs = sql_query($sql, 1);
while($row = sql_fetch_array($rs)) {
	// print_r2($row);
	$row['code_term'] = get_table_meta('term','trm_idx',$row['trm_idx_category']);
	$row['cnt'] = number_format($row['cnt']);
	$row['cod_memo'] = urlencode($row['cod_memo']);
	$row['cod_trm_name'] = $row['code_term']['trm_name'];
	$response->rows[] = $row;
}

$response->result = true;
$response->msg = "데이타를 성공적으로 가지고 왔습니다.";

$response->sql = $sql;

echo json_encode($response);
exit;
?>