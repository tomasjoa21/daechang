<?php
$sub_menu = "922140";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');
//print_r2($_REQUEST);
//exit;

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'item';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명

//$qstr .= '&ser_department='.$ser_department.'&ser_mb_name_saler='.$ser_mb_name_saler.'&ser_mb_id_worker='.$ser_mb_id_worker;
// 추가 변수 생성
foreach($_REQUEST as $key => $value ) {
    if(substr($key,0,4)=='ser_') {
    //    print_r3($key.'='.$value);
        if(is_array($value)) {
            foreach($value as $k2 => $v2 ) {
//                print_r3($key.$k2.'='.$v2);
                $qstr .= '&'.$key.'[]='.$v2;
            }
        }
        else {
            $qstr .= '&'.$key.'='.$value;
        }
    }
}

// 변수 자동 설정
for($i=0;$i<sizeof($fields);$i++) {
    // 공백 제거
    $_POST[$fields[$i]] = @trim($_POST[$fields[$i]]);
    // 천단위 제거
    if(preg_match("/_price$/",$fields[$i]) || preg_match("/_point$/",$fields[$i]))
        $_POST[$fields[$i]] = preg_replace("/,/","",$_POST[$fields[$i]]);
}

// 변수 재설정
$bom = get_table('bom','bom_idx',$_POST['bom_idx'],'bom_part_no');
$_POST['com_idx'] = $_SESSION['ss_com_idx'];
$_POST['itm_part_no'] = $bom['bom_part_no'];
$_POST['itm_name'] = $_POST['bom_name'];
$_POST['itm_defect_type'] = ($_POST['itm_status']!='defect') ? '': $_POST['itm_defect_type'];   // 불량타입 초기화
$_POST[$pre.'_update_dt'] = G5_TIME_YMD;


// 공통쿼리
$skips[] = $pre.'_idx';	// 건너뛸 변수 배열
$skips[] = $pre.'_reg_dt';

//$adds[] = $pre.'_sort';	// 포함할 변수 배열
//$adds[] = $pre.'_memo';
//$adds[] = $pre.'_status';

// 공통쿼리
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    //if(in_array($fields[$i],$adds)) {
        $sql_commons[] = " ".$fields[$i]." = '".$_POST[$fields[$i]]."' ";
    //}
}

// after sql_common value setting
// $sql_commons[] = " com_idx = '".$_SESSION['ss_com_idx']."' ";

// 공통쿼리 생성
$sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';


if ($w == '' || $w == 'c') {

    $sql = " INSERT INTO {$g5_table_name} SET
                {$sql_common}
                , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
	";
    sql_query($sql,1);
	${$pre."_idx"} = sql_insert_id();

}
else if ($w == 'u') {

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');

    $sql = "	UPDATE {$g5_table_name} SET
					{$sql_common}
				WHERE ".$pre."_idx = '".${$pre."_idx"}."'
	";
    //echo $sql.'<br>';
    sql_query($sql,1);

}
else if ($w == 'd') {

    $sql = "UPDATE {$g5_table_name} SET
                ".$pre."_status = 'trash'
            WHERE ".$pre."_idx = '".${$pre."_idx"}."'
    ";
    sql_query($sql,1);
    goto_url('./'.$fname.'_list.php?'.$qstr, false);

	//파일만 삭제
	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
	delete_jt_files(array("fle_db_table"=>$table_name, "fle_db_id"=>${$pre}['apc_idx'], "fle_delete_file"=>1));

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');
    

//-- 체크박스 값이 안 넘어오는 현상 때문에 추가, 폼의 체크박스는 모두 배열로 선언해 주세요.
$checkbox_array=array();
for ($i=0;$i<sizeof($checkbox_array);$i++) {
	if(!$_REQUEST[$checkbox_array[$i]])
		$_REQUEST[$checkbox_array[$i]] = 0;
}

//// 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.)
//$fields[] = "mms_zip";	// 건너뛸 변수명 배열
//$fields[] = "mms_sido_cd";	// 건너뛸 변수명 배열
//foreach($_REQUEST as $key => $value ) {
//	// 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트
//	if(!in_array($key,$fields) && substr($key,0,3)==$pre) {
//		//echo $key."=".$_REQUEST[$key]."<br>";
//		meta_update(array("mta_db_table"=>$table_name,"mta_db_id"=>${$pre."_idx"},"mta_key"=>$key,"mta_value"=>$value));
//	}
//}


//exit;
//goto_url('./'.$fname.'_list.php?'.$qstr, false);
goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>
