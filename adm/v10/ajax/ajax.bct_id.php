<?php
include_once('./_common.php');

$bct_idx = isset($_POST['bct_idx']) ? trim($_POST['bct_idx']) : '';
if (preg_match("/[^0-9a-z]/i", $bct_idx)) {
    die("{\"error\":\"분류코드는 영문자 숫자 만 입력 가능합니다.\"}");
}

$sql = "select bct_name 
        from {$g5['bom_category_table']}
        where bct_idx = '{$bct_idx}' AND bct_status NOT IN ('delete','trash') AND com_idx ='".$_SESSION['ss_com_idx']."' 
";
$row = sql_fetch($sql);
if (isset($row['bct_name']) && $row['bct_name']) {
    $bct_name = addslashes($row['bct_name']);
    die("{\"error\":\"이미 등록된 분류코드 입니다.\\n\\n항목명 : {$bct_name}\"}");
}

die("{\"error\":\"\"}"); // 정상;