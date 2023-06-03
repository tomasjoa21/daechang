<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$bwgs_skin_url.'/bpwg_style.css">', 3);
//print_r2($bwg_arr);
//$bwg_arr['config']) -> bwgs
//$bwg_arr['option']) -> bwgo
//$bwg_arr['content']) -> bwgc(콘텐트 목록을 담는다.)
//$boc -> bwgc(한 개 콘텐트만 담는다.)
//$bocf -> bwgc(한 개 콘텐트의 첨부파일만 담는다.) $bocf[bwcfile][0]['bwgc_idx']
if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		if($key == 'mb_id') ${'bwgs_mb_id'} = $val; //변수 충돌을 피하기 위해서
		else ${$key} = $val;
	}
}
$option_flag = (count($bwg_arr['option'])) ? true : false;
if($option_flag){
	foreach($bwg_arr['option'] as $key=>$val){
		${$key} = $val;
		if($key == 'file'){
			foreach($file as $k=>$v){
				${$k} = $v;
			}
		}
	}
	//관리자버튼에 할당할 id값
	$adid = 'ad_'.$bid.bpwg_uniqid();
	//위젯객체 id 할당(접두어:로고=lg,헤더=hd...등등)
	$fid = 'ft_'.$bid.bpwg_uniqid();
	include($bwgs_skin_path.'/bpwg_style.php');
	include($bwgs_skin_path.'/bpwg_tail.skin.php');
}
?>