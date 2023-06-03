<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

${'active_'.$g5['file_name']} = ' btn_top_menu_active';
$g5['container_sub_title'] = '
<h2 id="container_sub_title">
	<a href="./bom_list.php" class="btn_top_menu '.$active_bom_list.'">BOM관리</a>
	<a href="./bom_category_list.php" class="btn_top_menu '.$active_bom_category_list.'">카테고리</a>
	<a href="./bom_excel_form.php" class="btn_top_menu '.$active_bom_excel_form.'">BOM엑셀관리</a>
	<a href="./bom_jig_excel_form.php" class="btn_top_menu '.$active_bom_jig_excel_form.'">지그엑셀관리</a>
	<a href="./bom_plc_excel_form.php" class="btn_top_menu '.$active_bom_plc_excel_form.'">PLC엑셀관리</a>
</h2>
';
?>
