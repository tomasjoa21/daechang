<?php
include_once('./_common.php');
include_once('../adm.lib.php');

$msg = '';
if(!$wga_idx) $msg = '첨부파일 코드(bwga_idx)값이 제대로 넘어오지 않았습니다.';

$wga = sql_fetch(" SELECT wgs_idx,wga_type,wga_path,wga_name FROM {$g5['wdg_file_table']} WHERE wga_idx = '{$wga_idx}' ");
//해당코드의 첨부파일 데이터가 존재하지 않으면
if(!count($wga)){
	$msg = "해당코드의 첨부파일이 존재하지 않습니다.";
}
//해당코드의 첨부파일 데이터가 존재하면
else{
	@unlink(G5_PATH.$wga['wga_path'].'/'.$wga['wga_name']);
	delete_wdg_thumbnail($wga['wgs_idx'], $wga['wga_type'], $wga['wga_name']);
	$sql = " DELETE FROM {$g5['wdg_file_table']} WHERE wga_idx = '{$wga_idx}' ";
	sql_query($sql);
}
echo $msg;