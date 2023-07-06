<?php
$sub_menu = "910140";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if(!$mms_idx){
    alert($act_button.'하실 설비를 선택해 주세요.');
}

if ($act_button == '표시') $mms_pos_yn = '1';
else if ($act_button == '비표시') $mms_pos_yn = '0';
else alert('표시/비표시 버튼을 제대로 눌러주세요.');

if($mms_pos_yn == '1'){
    $chk_sql = " SELECT COUNT(*) AS cnt FROM {$g5['mms_table']}
                 WHERE  mms_idx = '{$mms_idx}'
                    AND mms_pos_yn = '{$mms_pos_yn}'
    ";
    $chk_res = sql_fetch($chk_sql);

    if($chk_res['cnt']){
        alert('이미 설비가 표시되어 있습니다.');
    }
}

$mms_pos_xy = "";
if($mms_pos_yn == '0'){
    $mms_pos_xy = ", mms_pos_x = '0', mms_pos_y = '0' ";
}

$sql = " UPDATE {$g5['mms_table']}
		SET mms_pos_yn = '{$mms_pos_yn}'
            {$mms_pos_xy}
		WHERE mms_idx = '{$mms_idx}'
";

sql_query($sql,1);

goto_url('./config_mms_pos_form.php');