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
	$wid = 'plx_'.$bid.bpwg_uniqid();
	/*
	include($bwgs_skin_path.'/bpwg_style.php');
	include_once($bwgs_skin_path.'/bpwg_style.head.php');
	include_once($bwgs_skin_path.'/bpwg_head.skin.php');
	include_once($bwgs_skin_path.'/bpwg_style.tail.php');
	*/
}
//첫번째 한개 데이터와 파일이 필요할 경우 주석해제해서 사용
if(count($bwg_arr['onecont'])){
	//변수충돌을 피하기 위해서
	foreach($bwg_arr['onecont'] as $key=>$val){
		if(substr($key,0,9) == 'bwgc_link' && strlen($key) == 10 && $val){
			$val = (substr($val,0,1)=='/' && !preg_match("/http/i",$val)) ? G5_URL.$val : bpwg_set_http($val);
		}
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

if(count($bwg_arr['onefile'])){
	foreach($bwg_arr['onefile'] as $key=>$val){
		${$key} = $val;
	}
}
if($bwgc_ytb_url){
	//bwg_add_css_file('jquery_mb_ytplayer_min',2);
	//bwg_add_js_file('jquery_mb_ytplayer',2);
}
//$js_name,$g5['bwg_js_file']
//$tnam = 'tswp';
//if($tnam)

if(count($bwg_arr['onecont'])){
	include($bwgs_skin_path.'/bpwg_style.php');
	
	if($text1_ani_type || $text2_ani_type || $text3_ani_type || $text4_ani_type){
		bwg_add_css_file('animate',2);
		bwg_add_js_file('fittext',2);
		bwg_add_js_file('lettering',2);
		bwg_add_js_file('textillate',2);
	}
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>&bwg_con=1" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<?php
	$sld_img_url = '';
	if($bwgc_ytb_url){
		$sldrr = explode('/',$bwgc_ytb_url);
		$sld_key = $sldrr[count($sldrr)-1];
		$sld_img_path = G5_PATH.$bwga_path.'/'.$bwga_name;
		$sld_img_url = G5_URL.$bwga_path.'/'.$bwga_name;
		$sld_bg = (is_file($sld_img_path)) ? 'style="background-image:url('.$sld_img_url.');"' : 'style="background-image:url(https://img.youtube.com/vi/'.$sld_key.'/maxresdefault.jpg);"';
		$sld_prop = 'ytb_url="'.$bwgc_ytb_url.'" data-property="{videoURL:\''.$bwgc_ytb_url.'\',vol:\'90\',stopMovieBlur:false,containment:\'#rollytb\',showControls:false,startAt:1,anchor:\'bottom, center\',mute:true,autoPlay:true,opacity:1,quality:\'highres\',playOnlyIfVisible:false,optimizeDisplay:true}"';
	}else{
		$sld_bg = '';
		$sld_img_url = G5_URL.$bwga_path.'/'.$bwga_name;
		$sld_img_style = 'style="background-image:url('.G5_URL.$bwga_path.'/'.$bwga_name.');"';
	}
	?>
	<div class="plx_img" <?=$sld_bg?>>
		<?php if($bwgc_ytb_url){ ?>
		<div class="plxbg" id="plxytb" <?=$sld_prop?>></div>
		<?php }else{ ?>
		<div class="plxbg" id="plxpic" <?=$sld_img_style?>></div>
		<?php } ?>
		<div class="plxblind"></div>
		<div class="plx_wrap">
		<?php if($bwgc_link0 = false){ ?><a href="<?=$bwgc_link0?>" target="<?=$bwgc_link0_target?>"><?php } ?>
		<?php if($bwgc_text1){ ?>
			<?php if(!$bwgc_link0 && $bwgc_link1){ ?><a href="<?=$bwgc_link1?>" target="<?=$bwgc_link1_target?>"><?php } ?>
			<p class="txt txt1"><?=$bwgc_text1?></p>
			<?php if(!$bwgc_link0 && $bwgc_link1){ ?></a><?php } ?>
		<?php } ?>
		<?php if($bwgc_text2){ ?>
			<?php if(!$bwgc_link0 && $bwgc_link2){ ?><a href="<?=$bwgc_link2?>" target="<?=$bwgc_link2_target?>"><?php } ?>
			<p class="txt txt2"><?=$bwgc_text2?></p>
			<?php if(!$bwgc_link0 && $bwgc_link2){ ?></a><?php } ?>
		<?php } ?>
		<?php if($bwgc_text3){ ?>
			<?php if(!$bwgc_link0 && $bwgc_link3){ ?><a href="<?=$bwgc_link3?>" target="<?=$bwgc_link3_target?>"><?php } ?>
			<p class="txt txt3"><?=$bwgc_text3?></p>
			<?php if(!$bwgc_link0 && $bwgc_link3){ ?></a><?php } ?>
		<?php } ?>
		<?php if($bwgc_text4){ ?>
			<?php if(!$bwgc_link0 && $bwgc_link4){ ?><a class="txt4_a" href="<?=$bwgc_link4?>" target="<?=$bwgc_link4_target?>"><?php } ?>
			<p class="txt txt4"><?=$bwgc_text4?></p>
			<?php if(!$bwgc_link0 && $bwgc_link4){ ?></a><?php } ?>
		<?php } ?>
		<?php if($bwgc_link0 = false){ ?></a><?php } ?>
		</div><!--//plx_wrap-->
	</div><!--//.plx_img-->
</div><!--//#$bid-->
<script>
var <?=$bid?>_text_delay_time = <?=($text_delay_time * 1000)?>;
var <?=$bid?>_txt1_ani_type = '<?=$text1_ani_type?>';
var <?=$bid?>_txt2_ani_type = '<?=$text2_ani_type?>';
var <?=$bid?>_txt3_ani_type = '<?=$text3_ani_type?>';
var <?=$bid?>_txt4_ani_type = '<?=$text4_ani_type?>';
//alert(<?=$bid?>_auto_direct);
$(function(){
	<?php if($bwgc_ytb_url){ ?>
	//var <?=$bid?>_ytb = $('#<?=$bid?> #plxytb');
	//<?=$bid?>_ytb.YTPlayer();
	<?php } ?>

	if($('#<?=$bid?> .txt1').length > 0 || $('#<?=$bid?> .txt2').length > 0 || $('#<?=$bid?> .txt3').length > 0 || $('#<?=$bid?> .txt4').length > 0){
		setTimeout(function(){
			if($('#<?=$bid?> .txt1').length > 0){
				if(<?=$bid?>_txt1_ani_type != ''){
					$('#<?=$bid?> .txt1').show().textillate({in:{effect:<?=$bid?>_txt1_ani_type}});
					$('#<?=$bid?> .txt1').show().textillate('start');
				}else{
					$('#<?=$bid?> .txt1').show();
				}
			}
			if($('#<?=$bid?> .txt2').length > 0){
				if(<?=$bid?>_txt2_ani_type != ''){
					$('#<?=$bid?> .txt2').show().textillate({in:{effect:<?=$bid?>_txt2_ani_type}});
					$('#<?=$bid?> .txt2').show().textillate('start');
				}else{
					$('#<?=$bid?> .txt2').show();
				}
			}
			if($('#<?=$bid?> .txt3').length > 0){
				if(<?=$bid?>_txt3_ani_type != ''){
					$('#<?=$bid?> .txt3').show().textillate({in:{effect:<?=$bid?>_txt3_ani_type}});
					$('#<?=$bid?> .txt3').show().textillate('start');
				}else{
					$('#<?=$bid?> .txt3').show();
				}
			}
			if($('#<?=$bid?> .txt4').length > 0){
				if(<?=$bid?>_txt4_ani_type != ''){
					$('#<?=$bid?> .txt4').show().textillate({in:{effect:<?=$bid?>_txt4_ani_type}});
					$('#<?=$bid?> .txt4').show().textillate('start');
				}else{
					$('#<?=$bid?> .txt4').show();
				}
			}
		},<?=$bid?>_text_delay_time);
	}
});
</script>
<?php } //if(count($bwg_arr['onecont'])) ?>
