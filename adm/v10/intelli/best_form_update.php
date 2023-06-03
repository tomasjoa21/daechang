<?php
$sub_menu = "920130";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'data_measure_best';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명
$qstr .= '&ser_mms_idx='.$ser_mms_idx.'&ser_type_no='.$ser_type_no; // 추가로 확장해서 넘겨야 할 변수들

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
$skips = array($pre.'_idx',$pre.'_reg_dt',$pre.'_update_dt');
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    $sql_commons[] = " ".$fields[$i]." = '".$_POST[$fields[$i]]."' ";
}

// after sql_common value setting
// $sql_commons[] = " com_idx = '".$_SESSION['ss_com_idx']."' ";

// 공통쿼리 생성
$sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';

if ($w == ''||$w == 'c') {
    
    $sql = "INSERT INTO {$g5_table_name} SET 
               {$sql_common} 
                , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
	";
    sql_query($sql,1);
	${$pre."_idx"} = sql_insert_id();
    
}
else if ($w == 'u') {

    $sql = "UPDATE {$g5_table_name} SET 
                {$sql_common}
            WHERE ".$pre."_idx = '".${$pre."_idx"}."' 
	";
    //echo $sql.'<br>';
    sql_query($sql,1);

}
else if ($w == 'd') {

    $sql = "DELETE FROM {$g5_table_name} WHERE ".$pre."_idx = '".${$pre."_idx"}."' ";
    sql_query($sql,1);
    goto_url('./'.$fname.'_list.php?'.$qstr, false);

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


// echo $sql.'<br>';
// exit;
goto_url('./'.$fname.'_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
// goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>