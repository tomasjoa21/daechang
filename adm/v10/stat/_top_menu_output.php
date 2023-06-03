<?php
if (!defined('_GNUBOARD_')) exit;

${'active_'.$g5['file_name']} = ' btn_top_menu_active';
$g5['container_sub_title'] = '
<h2 id="container_sub_title">
	<a href="./output.php" class="btn_top_menu '.$active_output.'">생산보고서</a>
	<a href="./uph.php" class="btn_top_menu '.$active_uph.'">UPH보고서</a>
	<a href="./return.php" class="btn_top_menu '.$active_return.'">반품율보고서</a>
</h2>
';