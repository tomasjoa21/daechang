<?php
include_once('./_common.php');
//include_once('../../../../../adm/adm.lib.php');
//include_once('../../../../../../../adm/adm.lib.php');
//echo $it_id; 
//$g5['g5_shop_item_table'] 
//$g5['bpwidget_turn_option_table'] 
//$g5['bpwidget_turn_file_table']
$it_arr = array('msg'=>'','it_id'=>$it_id,'width'=>0,'height'=>0);
$itsql = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['g5_shop_item_table']} WHERE it_id = '{$it_id}' AND it_use = '1' ");
$it_flag = ($itsql['cnt'] > 0) ? 1 : 0;
if($it_flag){
	$rosql = sql_fetch(" SELECT bwto_idx FROM {$g5['bpwidget_turn_option_table']} WHERE bwto_status = 'ok' AND bwto_db_type = 'shop' AND bwto_db_name = 'item' AND bwto_db_id = '{$it_id}' ");
	$bwto_idx = $rosql['bwto_idx'];
	if($bwto_idx){
		$rfsql = sql_fetch(" SELECT bwgt_width,bwgt_height FROM {$g5['bpwidget_turn_file_table']} WHERE bwgt_status = 'ok' AND bwto_idx = '{$bwto_idx}' ORDER BY bwgt_sort,bwgt_idx LIMIT 1 ");
		if($rfsql['bwgt_width'] && $rfsql['bwgt_height']){
			//$it_arr['msg'] = '';
			$it_arr['width'] = $rfsql['bwgt_width'];
			$it_arr['height'] = $rfsql['bwgt_height'];
			echo json_encode($it_arr);
		}else{
			$it_arr['msg'] = '회전이미지 파일에 문제발생';
			echo json_encode($it_arr);
		}
	}else{
		//$it_arr['msg'] = " SELECT bwto_idx FROM {$g5['bpwidget_turn_option_table']} WHERE bwto_status = 'ok' AND bwto_db_type = 'shop' AND bwto_db_name = 'item' AND bwto_db_id = '{$it_id}' ";
		$it_arr['msg'] = '회전이미지 등록하셨나요?';
		echo json_encode($it_arr);
	}
}else{
	$it_arr['msg'] = '상품관리 설정을 확인하세요.';
	echo json_encode($it_arr);
}
?>