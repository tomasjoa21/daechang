<?php
$sub_menu = "950100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'applicant';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_update/","",$g5['file_name']); // _update을 제외한 파일명
$qstr .= '&st_date='.$st_date; // 추가로 확장해서 넘겨야 할 변수들


if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] == "선택수정") {
    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        ${$pre} = get_table_meta($table_name, $pre.'_idx', $_POST[$pre.'_idx'][$k]);
        if (!${$pre}[$pre.'_idx'])
            $msg .= ${$pre}[$pre.'_idx'].': 자료가 존재하지 않습니다.\\n';
        else {
            $sql = "	UPDATE {$g5_table_name} SET 
                            ".$pre."_status = '".$_POST[$pre.'_status'][$k]."'
                        WHERE ".$pre."_idx = '".$_POST[$pre.'_idx'][$k]."' 
            ";
			sql_query($sql,1);
        }
    }

}
// 삭제할 때
else if ($_POST['act_button'] == "선택삭제") {
    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        
        ${$pre} = get_table_meta($table_name, $pre.'_idx', $_POST[$pre.'_idx'][$k]);
        //print_r2(${$pre});
        if (!${$pre}[$pre.'_idx'])
            $msg .= ${$pre}[$pre.'_idx'].': 자료가 존재하지 않습니다.\\n';
        else {
//            // 상태값 변경
//            $sql = "	UPDATE {$g5_table_name} SET 
//                            ".$pre."_status = 'trash'
//                        WHERE ".$pre."_idx = '".$_POST[$pre.'_idx'][$k]."'
//            ";
            // 디비 삭제
            $sql = "	DELETE FROM {$g5_table_name}
                        WHERE ".$pre."_idx = '".$_POST[$pre.'_idx'][$k]."'
            ";
//			  echo $sql;
            //  exit;
			sql_query($sql,1);
        }

        //파일만 삭제
        delete_jt_files(array("fle_db_table"=>$table_name, "fle_db_id"=>${$pre}[$pre.'_idx'], "fle_delete_file"=>1));
    }
}
// 선택담기
else if ($_POST['act_button'] == "선택담기") {
    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        
        ${$pre} = get_table_meta($table_name, $pre.'_idx', $_POST[$pre.'_idx'][$k]);
        //print_r2(${$pre});
        if (!${$pre}[$pre.'_idx'])
            $msg .= ${$pre}[$pre.'_idx'].': 자료가 존재하지 않습니다.\\n';
        else {
            $apc_idxs_new[] = ${$pre}[$pre.'_idx'];
        }
    }

    // meta 변수 추출
    $sql = "SELECT * FROM {$g5['meta_table']} 
            WHERE mta_db_table='member/applicant'
                AND mta_db_id='".$member['mb_id']."'
                AND mta_key='applicant_select'
            LIMIT 1
    ";
    $row = sql_fetch($sql,1);
	$apc_idxs_old = $row['mta_value'] ? explode(',', preg_replace("/\s+/", "", $row['mta_value'] )) : [];
//    print_r2($apc_idxs_old);
    
    // 배열 합쳐서 중복 제거
    $apc_idxs = array_merge($apc_idxs_new,$apc_idxs_old);
    $apc_idxs = array_unique($apc_idxs);
//    print_r2($apc_idxs);
    
    // meta 변수에 담기
    $ar['mta_db_table'] = 'member/applicant';
    $ar['mta_db_id'] = $member['mb_id'];
    $ar['mta_key'] = 'applicant_select';
    $ar['mta_value'] = implode(",",$apc_idxs);
    meta_update($ar);
    unset($ar);
    
}
//exit;

if ($msg)
    alert($msg);
    //echo '<script> alert("'.$msg.'"); </script>';
	
goto_url('./'.$fname.'.php?'.$qstr, false);
?>