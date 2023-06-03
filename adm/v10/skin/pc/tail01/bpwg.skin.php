<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$bwgs_skin_url.'/bpwg_style.css">', 3);
//print_r2($bwg_arr);
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
	//푸터객체 id 할당
	$fid = 'ft_'.$bid.bpwg_uniqid();
	
	include($bwgs_skin_path.'/bpwg_style.php');
	include($bwgs_skin_path.'/bpwg_tail.skin.php');
}else{
	echo '<div class="bwg_empty" style="text-align:center;padding:100px 0;border:1px solid #ddd;"><a href="'.G5_BPWIDGET_ADMIN_URL.'/bpwidget_form.php?bwgs_idx='.$bwgs_idx.'&w=u&bwg_con=1" target="_blank">['.strtoupper($bwg_arr['config']['bwgs_name']).']</a>의 내용이 존재하지 않습니다.</div>'.PHP_EOL;
}
?>
