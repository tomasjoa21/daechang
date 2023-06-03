<?php
include_once('./_common.php');
include_once('../adm.lib.php');

$msg = '';
if(!$wga_idxs) $msg = '첨부파일 코드(bwga_idx)값이 제대로 넘어오지 않았습니다.';
else{
	$wga_idxs_arr = ($wga_idxs) ? explode(',',$wga_idxs) : array();
	//$wga_arr = array();
	for($i=0;$i<count($wga_idxs_arr);$i++){
		$row = sql_fetch(" SELECT wga_idx,wgs_idx,wga_type,wga_path,wga_name FROM {$g5['wdg_file_table']} WHERE wga_idx = '{$wga_idxs_arr[$i]}' ");
		
		if(count($row) > 0){
			//array_push($bwga_arr,$row);
			@unlink(G5_PATH.$row['wga_path'].'/'.$row['wga_name']);
			delete_wdg_thumbnail($row['wgs_idx'], $row['wga_type'], $row['wga_name']);
			$sql = " DELETE FROM {$g5['wdg_file_table']} WHERE wga_idx = '{$row['wga_idx']}' ";
			sql_query($sql);
		}
	}
}
echo $msg;