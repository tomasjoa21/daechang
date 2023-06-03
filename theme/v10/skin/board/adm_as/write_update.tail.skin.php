<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//include_once($board_skin_path.'/_common.php'); // common.php를 불러오면 wr_id 값이 초기화되어 버려서 안 되요. 

// 게시판 환경설정값 추출
if ($bo_table) {
    $board = get_board($bo_table);

    // wr_id 가 있으면 $write 배열 확장(+serialized 변수들)
    // if($wr_id && is_serialized($write['wr_9'])) {
    //     $write = array_merge($write, get_serialized($write['wr_9']));
    // }
}

// report people array to json
// $wr_alarmlist['r_name'] = $_REQUEST['r_name'];
// $wr_alarmlist['r_role'] = $_REQUEST['r_role'];
// $wr_alarmlist['r_hp'] = $_REQUEST['r_hp'];
// $wr_alarmlist['r_email'] = $_REQUEST['r_email'];
// $wr_alarms = json_encode( $wr_alarmlist, JSON_UNESCAPED_UNICODE );
// $_REQUEST['wr_alarm_list'] = $wr_alarms;
// print_r2($_REQUEST);
// print_r2($wr_alarms);exit;

// wr_send_type
/*
for($i=0;$i<sizeof($wr_send_type);$i++) {
	// echo $wr_send_type[$i].'<br>';
	$wr_send_type_arr[] = $wr_send_type[$i];
}
$_REQUEST['wr_send_type'] = implode(",",$wr_send_type_arr);
*/
// echo $_REQUEST['wr_send_type'].'<br>';

/*
wr_subject : 업체명
wr_contnt : 주소
wr_1 : 업체번호
wr_2 : 설비번호
wr_5 : 담당자명
wr_6 : 전화번호
wr_7 : 이메일
*/
// 공통 쿼리
$sql_common = " wr_1 = '".trim($_POST['com_idx'])."'
				, wr_2 = '".trim($_POST['mms_idx'])."'
				, wr_3 = '".trim($_POST['wr_3'])."'
				, wr_4 = '".trim($_POST['wr_4'])."'
				, wr_5 = '".trim($_POST['wr_5'])."'
				, wr_6 = '".trim($_POST['wr_6'])."'
				, wr_7 = '".trim($_POST['wr_7'])."'
				, wr_8 = '".trim($_POST['wr_8'])."'
				, wr_9 = '".trim($_POST['wr_9'])."'
				, wr_10 = '".trim($_POST['wr_10'])."'
";
if ($w == 'u') {
	$sql = "UPDATE {$write_table} SET 
                {$sql_common}
            WHERE wr_id = '{$wr['wr_id']}'
	";
    // echo $sql.'<br>';
    sql_query($sql,1);
    // exit;
}
else if ($w == '') {
    // 초기 입력값 설정
	$sql = "UPDATE {$write_table} SET 
                {$sql_common}
            WHERE wr_id = '{$wr_id}'
	";
    //echo $sql.'<br>';
	sql_query($sql,1);
}



//-- 필드명 추출 wr_ 와 같은 앞자리 3자 추출 --//
$r = sql_query(" desc {$write_table} ");
while ( $d = sql_fetch_array($r) ) {$db_fields[] = $d['Field'];}
$db_prefix = substr($db_fields[0],0,2);

//-- 체크박스 값이 안 넘어오는 현상 때문에 추가, 폼의 체크박스는 모두 배열로 선언해 주세요.
$checkbox_array=array();
for ($i=0;$i<sizeof($checkbox_array);$i++) {
	if(!$_REQUEST[$checkbox_array[$i]])
		$_REQUEST[$checkbox_array[$i]] = 0;
}

	//-- 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.) --//
	$db_fields[] = "wr_zip";	// 건너뛸 변수명은 배열로 추가해 준다.
	$db_fields[] = "wr_sido_cd";	// 건너뛸 변수명은 배열로 추가해 준다.
	foreach($_REQUEST as $key => $value ) {
		//-- 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트 --//
		if(!in_array($key,$db_fields) && substr($key,0,2)==$db_prefix) {
			echo $key."=".$_REQUEST[$key]."<br>";
			meta_update(array("mta_db_table"=>"board/".$bo_table,"mta_db_id"=>$wr_id,"mta_key"=>$key,"mta_value"=>$value));
		}
	}


delete_cache_latest($bo_table);

// 무조건 리스트로 이동
goto_url(G5_USER_ADMIN_BBS_URL.'/board.php?bo_table='.$bo_table.'&'.$qstr);
exit;