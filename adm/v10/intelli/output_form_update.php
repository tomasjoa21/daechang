<?php
$sub_menu = "920130";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'xray_inspection';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들

// 변수 설정
for($i=0;$i<sizeof($fields);$i++) {
    // 공백 제거
    $_POST[$fields[$i]] = trim($_POST[$fields[$i]]);
    // 천단위 제거
    if(preg_match("/_price$/",$fields[$i]) || $fields[$i]=='rct_moq' || $fields[$i]=='rct_lead_time')
        $_POST[$fields[$i]] = preg_replace("/,/","",$_POST[$fields[$i]]);
}

// 변수 재설정


// 공통쿼리
$skips = array($pre.'_idx',$pre.'_reg_dt',$pre.'_update_dt','event_timestamp');
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    $sql_field_arr[] = " ".$fields[$i]." ";
    $sql_value_arr[] = " '".$_POST[$fields[$i]]."' ";
}

// after sql_common value setting
// $sql_commons[] = " com_idx = '".$_SESSION['ss_com_idx']."' ";

// 공통쿼리 생성
$sql_fields = (is_array($sql_field_arr)) ? "(".implode(",",$sql_field_arr).")" : '';
$sql_values = (is_array($sql_value_arr)) ? "(".implode(",",$sql_value_arr).")" : '';

if ($w == ''||$w == 'c') {
    
    $sql = "INSERT INTO {$g5_table_name}
                {$sql_fields} VALUES {$sql_values} 
            RETURNING xry_idx 
	";
    // sql_query_pg($sql,1);
    // $sql = " SELECT currval(pg_get_serial_sequence('g5_1_cast_shot_sub','xry_idx')) ";
    $stmt = $db->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // print_r2($row);
	// ${$pre."_idx"} = sql_insert_id();
    
}
else if ($w == 'u') {

	${$pre} = get_table_pg($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
 
    $sql = "UPDATE {$g5_table_name} SET 
                {$sql_fields} = {$sql_values}
            WHERE ".$pre."_idx = '".${$pre."_idx"}."' 
	";
    // echo $sql.'<br>';
    sql_query_pg($sql,1);

}
else if ($w == 'd') {

    $sql = "DELETE FROM {$g5_table_name} WHERE ".$pre."_idx = '".${$pre."_idx"}."' ";
    sql_query_pg($sql,1);
    goto_url('./'.$fname.'_list.php?'.$qstr, false);

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


// echo $sql.'<br>';
// exit;
goto_url('./'.$fname.'_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
// goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>