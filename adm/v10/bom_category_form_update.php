<?php
$sub_menu = '940120';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, "w");

$bct_idx = isset($_REQUEST['bct_idx']) ? preg_replace('/[^0-9a-z]/i', '', $_REQUEST['bct_idx']) : '';

if ($w == "u" || $w == "d")
    check_demo();

auth_check_menu($auth, $sub_menu, "d");

check_admin_token();

$sql_common = " bct_name            = '$bct_name'
                , com_idx           = '".$_SESSION['ss_com_idx']."'
                , bct_order         = '$bct_order'
                , bct_update_dt     = '".G5_TIME_YMDHIS."'
";


if ($w == "")
{
    if (!trim($bct_idx))
        alert("분류 코드가 없으므로 분류를 추가하실 수 없습니다.");

    // 소문자로 변환
    $bct_idx = strtolower($bct_idx);

    $sql = " insert {$g5['bom_category_table']}
                set bct_idx = '$bct_idx',
                    {$sql_common}
                    , bct_reg_dt   = '".G5_TIME_YMDHIS."'
    ";
    sql_query($sql,1);
}
else if ($w == "u")
{
    $sql = "UPDATE {$g5['bom_category_table']}
                SET {$sql_common}
            WHERE bct_idx = '$bct_idx'
    ";
    sql_query($sql,1);

    // 하위분류를 똑같은 설정으로 반영
    if (isset($_POST['sub_category']) && $_POST['sub_category']) {
        $len = strlen($bct_idx);
        $sql = " update {$g5['bom_category_table']}
                    set bct_order = '$bct_order'
                  where SUBSTRING(bct_idx,1,$len) = '$bct_idx'
        ";
        sql_query($sql);
    }
}
else if ($w == "d")
{
    // 분류의 길이
    $len = strlen($bct_idx);

    $sql = " select COUNT(*) as cnt from {$g5['bom_category_table']}
              where SUBSTRING(bct_idx,1,$len) = '$bct_idx'
                and bct_idx <> '$bct_idx'
                AND com_idx = '".$_SESSION['ss_com_idx']."'                
    ";
    $row = sql_fetch($sql);
    if ($row['cnt'] > 0)
        alert("이 분류에 속한 하위 분류가 있으므로 삭제 할 수 없습니다.\\n\\n하위분류를 우선 삭제하여 주십시오.");

    $str = $comma = "";
    $sql = " select bom_idx from {$g5['bom_table']} where bct_idx = '$bct_idx' ";
    $result = sql_query($sql);
    $i=0;
    while ($row = sql_fetch_array($result))
    {
        $i++;
        if ($i % 10 == 0) $str .= "\\n";
        $str .= "$comma{$row['bom_idx']}";
        $comma = " , ";
    }

    if ($str)
        alert("이 분류와 관련된 상품이 총 {$i} 건 존재하므로 상품을 삭제한 후 분류를 삭제하여 주십시오.\\n\\n$str");

    // 분류 삭제
    $sql = "DELETE FROM {$g5['bom_category_table']} WHERE bct_idx = '$bct_idx' AND com_idx = '".$_SESSION['ss_com_idx']."' ";
    sql_query($sql,1);
}

//파일 삭제처리
$merge_del = array();
$del_arr = array();
for($j=1;$j<=6;$j++){
    $file_del = 'file'.$j.'_del';
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

//print_r2($_FILES);
for($i=1;$i<=6;$i++){
    //print_r2($_FILES['cat_f'.$i]);
    upload_multi_file($_FILES['cat_f'.$i],'bom_category',$bct_idx,'file'.$i);
}
//exit;
if ($w == "" || $w == "u")
{
    goto_url("./bom_category_form.php?w=u&amp;bct_id=$bct_idx&amp;$qstr");
} else {
    goto_url("./bom_category_list.php?$qstr");
}