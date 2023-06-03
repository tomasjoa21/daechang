<?php
$sub_menu = "910110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

// print_r2($_REQUEST);
// exit;

//-- 필드명 추출 mb_ 와 같은 앞자리 3자 추출 --//
$r = sql_query(" desc {$g5['setting_table']} ");
while ( $d = sql_fetch_array($r) ) {$db_fields[] = $d['Field'];}
$db_prefix = substr($db_fields[0],0,3);

//-- 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.) --//
$db_fields[] = "set_bg_pattern";	// 건너뛸 변수명은 배열로 추가해 준다.
$db_fields[] = "var_name";
foreach($_REQUEST as $key => $value ) {
	//-- 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트, array 타입 변수들도 저장 안 함 --//
	$db_fields[] = $key.'_'.$_SESSION['ss_com_idx']; // 개별 설정은 아래쪽에서 저장함
	$db_fields[] = $key.'_'.$_SESSION['ss_com_idx']."_check"; // 개별 설정을 위한 check는 저장할 필요 없음!!
	if(!in_array($key,$db_fields) && substr($key,0,3)==$db_prefix && gettype($value) != 'array') {
		// echo $key."=".$_REQUEST[$key]."<br>";
		setting_update(array(
			"set_key"=>"",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
			"set_name"=>$key,
			"set_value"=>$value,
			"set_auto_yn"=>1
		));
	}

	// 개별설정인 경우... set_itm_status_13에서 변수를 추출
	if(substr($key,-3)=='_'.$_SESSION['ss_com_idx'] && gettype($value) != 'array') {
		// echo $key."=".$_REQUEST[$key]."<br>";
		$key2 = substr($key.'_'.$_SESSION['ss_com_idx'],0,strpos($key,'_'.$_SESSION['ss_com_idx']));
		// echo $key2.BR;
		// echo $_REQUEST['set_itm_status_'.$_SESSION['ss_com_idx'].'_check'].BR;
		// check가 있는 경우 업데이트
		if($_REQUEST[$key2.'_'.$_SESSION['ss_com_idx'].'_check']) {
			// echo $key."=".$_REQUEST[$key].BR;
			setting_update(array(
				"set_key"=>"",
				"com_idx"=>$_SESSION['ss_com_idx'],
				"set_name"=>$key2,
				"set_value"=>$value,
				"set_auto_yn"=>1
			));
		}
		// check가 해제되면 삭제
		else {
			$sql = " DELETE FROM {$g5['setting_table']} WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND set_name = '".$key2."' ";
			sql_query($sql,1);
		}
	}

	// 쇼핑몰 설정 변수 de_ 로 시작
	if(substr($key,0,3)=='de_' && gettype($value) != 'array') {
		$sql_defaults[] = " ".$key." = '".$value."' ";
	}

}

// 쇼핑몰 설정 변수 업데이트
if($sql_defaults[0]) {
	$sql_default = (is_array($sql_defaults)) ? implode(",",$sql_defaults) : '';
	$sql = " UPDATE {$g5['g5_shop_default_table']} SET {$sql_default} ";
	// echo $sql.'<br>';
	sql_query($sql,1);
}




// exit;
goto_url('./config_form.php?'.$qstr, false);
?>