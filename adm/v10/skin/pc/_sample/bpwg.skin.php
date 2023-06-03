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
//$g5['bpwidget']['bwgf_media_wd_xl'];
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
	$wid = 'qk_'.$bid.bpwg_uniqid();
	/*
	include($bwgs_skin_path.'/bpwg_style.php');
	include($bwgs_skin_path.'/bpwg_style.head.php');
	include($bwgs_skin_path.'/bpwg_head.skin.php');
	include($bwgs_skin_path.'/bpwg_style.tail.php');
	*/
}
/*//첫번째 한개 데이터와 파일이 필요할 경우 주석해제해서 사용
if(count($bwg_arr['onecont'])){
	//변수충돌을 피하기 위해서
	foreach($bwg_arr['onecont'] as $key=>$val){
		if($key == 'bo_table') ${'bwgc_bo_table'} = $val;
		else if($key == 'wr_id') ${'bwgc_wr_id'} = $val;
		else if($key == 'co_id') ${'bwgc_co_id'} = $val;
		else if($key == 'mb_id') ${'bwgc_mb_id'} = $val;
		else if($key == 'it_id') ${'bwgc_it_id'} = $val;
		else if($key == 'od_id') ${'bwgc_od_id'} = $val;
		else if($key == 'file') ${'bwgc_file'} = $val;
		else ${$key} = $val;
	}
}

$bg_src = G5_URL.$bwg_arr['option']['file']['afile'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['afile'][0]['bwga_name'];
$img1_src = G5_URL.$conarr[0]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[0]['file']['bwcfile'][0]['bwga_name'];
$img2_src = G5_URL.$conarr[1]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[1]['file']['bwcfile'][0]['bwga_name'];


if(count($bwg_arr['onefile'])){
	foreach($bwg_arr['onefile'] as $key=>$val){
		${$key} = $val;
	}
}


for(){
	$bwgc_link0 = (substr($conarr[$i]['bwgc_link0'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link0'])) ? G5_URL.$conarr[$i]['bwgc_link0'] : bpwg_set_http($conarr[$i]['bwgc_link0']);
	$bwgc_link1 = (substr($conarr[$i]['bwgc_link1'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link1'])) ? G5_URL.$conarr[$i]['bwgc_link1'] : bpwg_set_http($conarr[$i]['bwgc_link1']);
	$bwgc_link2 = (substr($conarr[$i]['bwgc_link2'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link2'])) ? G5_URL.$conarr[$i]['bwgc_link2'] : bpwg_set_http($conarr[$i]['bwgc_link2']);
	$bwgc_link3 = (substr($conarr[$i]['bwgc_link3'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link3'])) ? G5_URL.$conarr[$i]['bwgc_link3'] : bpwg_set_http($conarr[$i]['bwgc_link3']);
	$bwgc_link4 = (substr($conarr[$i]['bwgc_link4'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link4'])) ? G5_URL.$conarr[$i]['bwgc_link4'] : bpwg_set_http($conarr[$i]['bwgc_link4']);
	
	$bwgc_img = G5_URL.$conarr[$i]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_name'];
	$bwgc_txt = $conarr[$i]['bwgc_text1'];
}
//옵션변수에서 받아온 링크데이터
$link1 = bwg_g5_url_check($link1);
$link2 = bwg_g5_url_check($link2);
$link3 = bwg_g5_url_check($link3);
*/

$conarr = $bwg_arr['content'];
if(count($conarr)){
	$adid = 'ad_'.$bid.bpwg_uniqid();
	//$sld_ratio = $sld_wd / $sld_ht;
	include($bwgs_skin_path.'/bpwg_style.php');

?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>&bwg_con=1" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<div class="">
	
	</div>
</div>
<?php }else{ 
		include($bwgs_skin_path.'/bpwg_style.php');
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<div class="empty">
	<?=$bwgs_cd?>의 목록이 존재하지 않습니다.<a class="" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>&bwg_con=1" target="_blank" title="<?=$bwgs_cd?> 위젯">위젯관리자</a>
	<?php
	//print_r2($bwg_arr['config']);
	?>
	</div>
</div>
<?php } ?>