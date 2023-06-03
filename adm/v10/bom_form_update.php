<?php
$sub_menu = "940120";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'bom';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form_update/","",$g5['file_name']); // _form_update를 제외한 파일명
$qstr .= '&ser_bct_idx='.$ser_bct_idx.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들

// 변수 재설정
for($i=0;$i<sizeof($fields);$i++) {
    // 공백 제거
    $_POST[$fields[$i]] = trim($_POST[$fields[$i]]);
    // 천단위 제거
    if(preg_match("/_price$/",$fields[$i]) || $fields[$i]=='bom_moq' || $fields[$i]=='bom_lead_time')
        $_POST[$fields[$i]] = preg_replace("/,/","",$_POST[$fields[$i]]);
}

// prior post value setting
$_POST['com_idx'] = $_SESSION['ss_com_idx'];


// 공통쿼리
$skips = array($pre.'_idx',$pre.'_reg_dt',$pre.'_update_dt',$pre.'_part_nos');
for($i=0;$i<sizeof($fields);$i++) {
    if(in_array($fields[$i],$skips)) {continue;}
    $sql_commons[] = " ".$fields[$i]." = '".$_POST[$fields[$i]]."' ";
}

// after sql_common value setting
// $sql_commons[] = " com_idx = '".$_SESSION['ss_com_idx']."' ";


// 공통쿼리 생성
$sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';


if ($w == '') {
    
    $sql = "INSERT INTO {$g5_table_name} SET 
               {$sql_common} 
                , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
                , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
	";
    sql_query($sql,1);
	${$pre."_idx"} = sql_insert_id();
    
}
else if ($w == 'u') {

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');

    // part_no 히스토리
    if(${$pre}['bom_part_no'] != $bom_part_no) {
        $sql_common .= ", bom_part_nos = '".${$pre}['bom_part_nos'].${$pre}['bom_part_no']." (~".substr(G5_TIME_YMD,2).")|'";
    }
 
    $sql = "UPDATE {$g5_table_name} SET 
                {$sql_common}
                , ".$pre."_update_dt = '".G5_TIME_YMDHIS."'
            WHERE ".$pre."_idx = '".${$pre."_idx"}."' 
	";
    // echo $sql.'<br>';
    sql_query($sql,1);


    // print_r2($_FILES);exit;
    //파일 삭제처리
    $merge_del = array();
    $del_arr = array();
    for($j=1;$j<=count($_FILES);$j++){
        $file_del = 'bomf'.$j.'_del';
        if(@count(${$file_del})){
            foreach(${$file_del} as $k=>$v){
                $merge_del[$k] = $v;
            }
        }
    }
    if(@count($merge_del)){
        foreach($merge_del as $k=>$v) {
            array_push($del_arr,$k);
        }
    }
    //exit;
    //print_r2($del_arr);exit;
    if(@count($del_arr)) delete_idx_file($del_arr);

    // print_r2($_FILES);exit;
    for($i=1;$i<=count($_FILES);$i++){
        //print_r2($_FILES['cat_f'.$i]);
        upload_multi_file($_FILES['bom_f'.$i],'bom',${$pre."_idx"},'bomf'.$i);
    }

    // 대표제품 제거인 경우 
    if($bom_main_delete_yn) {
        $sql = " UPDATE {$g5['bom_item_table']} SET bit_main_yn = 0 WHERE bom_idx = '".${$pre."_idx"}."' ";
        sql_query($sql,1);
    }



    //계층구조를 확인할 수 있는 뷰테이블을 기존테이블 있으면 삭제하고 다시 생성
    $drop_v_sql = " DROP VIEW {$g5['v_bom_item_table']} ";
    @sql_query($drop_v_sql);

    $create_v_sql = " CREATE VIEW IF NOT EXISTS {$g5['v_bom_item_table']} 
        AS
        SELECT bom.bom_idx
            , cst_idx_provider
            , bom.bom_name
            , bom_part_no
            , bom_type
            , bom_price
            , bom_status
            , cst_name
            , bit.bit_idx
            , bit.bom_idx AS bom_idx_product
            , bit.bit_main_yn
            , bit.bom_idx_child
            , bit.bit_reply
            , bit.bit_count
        FROM {$g5['bom_item_table']} AS bit
            LEFT JOIN {$g5['bom_table']} bom ON bom.bom_idx = bit.bom_idx_child
            LEFT JOIN {$g5['customer_table']} cst ON cst.cst_idx = bom.cst_idx_provider
        ORDER BY bit.bom_idx, bit.bit_reply
    ";
    @sql_query($create_v_sql);

}
else if ($w == 'd') {

    $sql = "UPDATE {$g5_table_name} SET
                ".$pre."_status = 'trash'
            WHERE ".$pre."_idx = '".${$pre."_idx"}."'
    ";
    // sql_query($sql,1);
    goto_url('./'.$fname.'_list.php?'.$qstr, false);
    
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');





//한국수지만을 위한 추가내용(완제품 등록/수정/삭제)시 자동으로 (반제품 등록/수정/삭제)처리가 동반되도록하는 기능 (다른 업체에서는 주석 또는 삭제 해야함)
// if(preg_match('/korsuji/i',$config['cf_title']) || preg_match('/한국수지/',$config['cf_title']) || preg_match('/korea\ssuji/i',$config['cf_title'])){
//     include_once('./bom_form_korsuji_update.php');
// }


// Update price table info. Update for same price and date, Insert for not existing.
if($_POST['bom_start_date']) {
    $ar['bom_idx'] = ${$pre."_idx"};
    $ar['bom_start_date'] = $_POST['bom_start_date'];
    $ar['bom_price'] = $_POST['bom_price'];
    bom_price_history($ar);
    print_r2($ar);
    unset($ar);
}

// set the correct price accoring to date defined in bom_price table.
set_bom_price(${$pre."_idx"});


//-- 체크박스 값이 안 넘어오는 현상 때문에 추가, 폼의 체크박스는 모두 배열로 선언해 주세요.
$checkbox_array=array();
for ($i=0;$i<sizeof($checkbox_array);$i++) {
	if(!$_REQUEST[$checkbox_array[$i]])
		$_REQUEST[$checkbox_array[$i]] = 0;
}

//-- 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.) --//
$fields[] = "bom_main_delete_yn";	// 건너뛸 변수명은 배열로 추가해 준다.
foreach($_REQUEST as $key => $value ) {
	//-- 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트 --//
	if(!in_array($key,$fields) && substr($key,0,3)==$pre) {
		//echo $key."=".$_REQUEST[$key]."<br>";
		meta_update(array("mta_db_table"=>$table_name,"mta_db_id"=>${$pre."_idx"},"mta_key"=>$key,"mta_value"=>$value));
	}
}

// exit;
// goto_url('./'.$fname.'_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>