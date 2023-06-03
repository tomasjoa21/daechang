<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

// 최고관리자인 경우만
if($member['mb_level']>=9) {
    // $sub_title_list = ' <a href="'.G5_BBS_URL.'/board.php?bo_table=setting1" class="btn_top_menu '.$active_term_list.'">환경설정게시판</a>
    // ';
}


${'active_'.$g5['file_name']} = ' btn_top_menu_active';

// 그래프인 경우에 활성표시
if(preg_match("/_graph$/",$g5['file_name'])) {
    $fname = preg_replace("/_graph/","_list",$g5['file_name']);
    ${'active_'.$fname} = ' btn_top_menu_active';
}
else if(preg_match("/_multi$/",$g5['file_name'])) {
    $fname = preg_replace("/_multi/","_list",$g5['file_name']);
    ${'active_'.$fname} = ' btn_top_menu_active';
}

$g5['container_sub_title'] = '
<h2 id="container_sub_title">
    <a href="./tsdb_shot_list.php" class="btn_top_menu '.$active_tsdb_shot_list.'">주조공정</a>
    <a href="./tsdb_shot_sub_list.php" class="btn_top_menu '.$active_tsdb_shot_sub_list.'">온도(주조공정(SUB))</a>
    <a href="./tsdb_shot_pressure_list.php" class="btn_top_menu '.$active_tsdb_shot_pressure_list.'">검출압력</a>
    <a href="./tsdb_qrcode_list.php" class="btn_top_menu '.$active_tsdb_qrcode_list.'">QRCode</a>
    <a href="./tsdb_xray_inspection_list.php" class="btn_top_menu '.$active_tsdb_xray_inspection_list.'">X-Ray검사</a>
    <a href="./tsdb_factory_temphum_list.php" class="btn_top_menu '.$active_tsdb_factory_temphum_list.'">공장온습도</a>
    <a href="./tsdb_charge_in_list.php" class="btn_top_menu '.$active_tsdb_charge_in_list.'">장입현황</a>
    <a href="./tsdb_charge_out_list.php" class="btn_top_menu '.$active_tsdb_charge_out_list.'">출탕현황</a>
    <a href="./tsdb_melting_temp_list.php" class="btn_top_menu '.$active_tsdb_melting_temp_list.'">용해온도</a>
    <a href="./rdb_charge_in_list.php" class="btn_top_menu '.$active_rdb_charge_in_list.'">장입현황(RDB)</a>
	'.$sub_title_list.'
</h2>
';
?>
