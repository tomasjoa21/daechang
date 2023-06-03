<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//print_r2($bwg_arr['config']);
//print_r2($bwg_arr['option']);
//print_r2($bwg_arr['content']);
if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		${$key} = $val;
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
	
	//위젯객체 id 할당(접두어:로고=lg,헤더=hd...등등)
	$wid = 'sd_'.$bid.bpwg_uniqid();
	$sid = $bid.'_s';
	/*
	include($bwgs_skin_path.'/bpwg_style.php');
	include_once($bwgs_skin_path.'/bpwg_style.head.php');
	include_once($bwgs_skin_path.'/bpwg_head.skin.php');
	include_once($bwgs_skin_path.'/bpwg_style.tail.php');
	*/
}
$conarr = $bwg_arr['content'];
if(count($conarr)){
	$adid = 'ad_'.$bid.bpwg_uniqid();
	bwg_add_css_file('slick',2);
	bwg_add_css_file('slick_theme',2);
	bwg_add_css_file('jquery_mb_ytplayer_min',2);
	bwg_add_css_file('animate',2);
	bwg_add_js_file('slick_min',2);
	//bwg_add_js_file('slick',2);
	bwg_add_js_file('jquery_mb_ytplayer_min',2);
	//bwg_add_js_file('jquery_mb_ytplayer',2);
	bwg_add_js_file('fittext',2);
	bwg_add_js_file('lettering',2);
	bwg_add_js_file('textillate',2);
	include_once($bwgs_skin_path.'/bpwg_style.php');
	
	$sld_ratio = $sld_wd / $sld_ht;
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php //if(false){ ?>
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>&bwg_con=1" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<div id="<?=$sid?>" class="dv_slk">
		<?php for($i=0;$i<count($conarr);$i++){
			//$ytb_bg = '';
			//if($conarr[$i]['bwgc_ytb_url'] && count($conarr[$i]['file']['bwcfile']))
			//	$ytb_bg = 'style="background-image:url('.G5_URL.'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_name'].');"';
			if($conarr[$i]['bwgc_ytb_url']){
				$sld_class = 'sld_ytb';
				$sldrr = explode('/',$conarr[$i]['bwgc_ytb_url']);
				$sld_key = $sldrr[count($sldrr)-1];
				$sld_img_path = G5_PATH.$conarr[$i]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_name'];
				$sld_img_url = G5_URL.$conarr[$i]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_name'];
				$sld_bg = (is_file($sld_img_path)) ? 'style="background-image:url('.$sld_img_url.');"' : 'style="background-image:url(https://img.youtube.com/vi/'.$sld_key.'/maxresdefault.jpg);"';
				$sld_prop = 'ytb_url="'.$conarr[$i]['bwgc_ytb_url'].'" data-property="{videoURL:\''.$conarr[$i]['bwgc_ytb_url'].'\',vol:\'90\',stopMovieBlur:false,containment:\'#'.($sid.$i).'\',showControls:false,startAt:1,anchor:\'bottom, center\',mute:true,autoPlay:true,opacity:1,quality:\'highres\',optimizeDisplay:true}"';
			}else{
				$sld_class = 'sld_img';
				$sld_img_style = 'style="background-image:url('.G5_URL.$conarr[$i]['file']['bwcfile'][0]['bwga_path'].'/'.$conarr[$i]['file']['bwcfile'][0]['bwga_name'].');"';
			}
			
			$bwgc_link0 = (substr($conarr[$i]['bwgc_link0'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link0'])) ? G5_URL.$conarr[$i]['bwgc_link0'] : bpwg_set_http($conarr[$i]['bwgc_link0']);
			$bwgc_link1 = (substr($conarr[$i]['bwgc_link1'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link1'])) ? G5_URL.$conarr[$i]['bwgc_link1'] : bpwg_set_http($conarr[$i]['bwgc_link1']);
			$bwgc_link2 = (substr($conarr[$i]['bwgc_link2'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link2'])) ? G5_URL.$conarr[$i]['bwgc_link2'] : bpwg_set_http($conarr[$i]['bwgc_link2']);
			$bwgc_link3 = (substr($conarr[$i]['bwgc_link3'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link3'])) ? G5_URL.$conarr[$i]['bwgc_link3'] : bpwg_set_http($conarr[$i]['bwgc_link3']);
			$bwgc_link4 = (substr($conarr[$i]['bwgc_link4'],0,1)=='/' && !preg_match("/http/i",$conarr[$i]['bwgc_link4'])) ? G5_URL.$conarr[$i]['bwgc_link4'] : bpwg_set_http($conarr[$i]['bwgc_link4']);
		?>
		<div class="dv_sld" <?=$sld_bg?>>
			<?php if($conarr[$i]['bwgc_ytb_url']){ ?>
			<div id="<?=($sid.$i)?>" class="sld_pic <?=$sld_class?>" <?=$sld_prop?>></div>
			<?php }else{ ?>
			<div id="<?=($sid.$i)?>" class="sld_pic <?=$sld_class?>" <?=$sld_img_style?>></div>
			<?php } ?>
			<div class="dv_blind"></div>
			<div class="dv_con">
				<div class="dv_tbl">
					<div class="dv_td">
						<?php if($conarr[$i]['bwgc_text1'] || $conarr[$i]['bwgc_text2'] || $conarr[$i]['bwgc_text3'] || $conarr[$i]['bwgc_text4']){?>
						<?php if($bwgc_link0){ ?><a href="<?=$bwgc_link0?>" target="<?=$conarr[$i]['bwgc_link0_target']?>"><?php } ?>
						<div class="dv_text dv_text<?=$i?>">
							<?php if($conarr[$i]['bwgc_text1']){ ?>
							<?php if(!$bwgc_link0 && $bwgc_link1){ ?><a href="<?=$bwgc_link1?>" target="<?=$conarr[$i]['bwgc_link1_target']?>"><?php } ?>
							<p class="txt1"><?=$conarr[$i]['bwgc_text1']?></p>
							<?php if(!$bwgc_link0 && $bwgc_link1){ ?></a><?php } ?>
							<?php } ?>
							<?php if($conarr[$i]['bwgc_text2']){ ?>
							<?php if(!$bwgc_link0 && $bwgc_link2){ ?><a href="<?=$bwgc_link2?>" target="<?=$conarr[$i]['bwgc_link2_target']?>"><?php } ?>
							<p class="txt2"><?=$conarr[$i]['bwgc_text2']?></p>
							<?php if(!$bwgc_link0 && $bwgc_link2){ ?></a><?php } ?>
							<?php } ?>
							<?php if($conarr[$i]['bwgc_text3']){ ?>
							<?php if(!$bwgc_link0 && $bwgc_link3){ ?><a href="<?=$bwgc_link3?>" target="<?=$conarr[$i]['bwgc_link3_target']?>"><?php } ?>
							<p class="txt3"><?=$conarr[$i]['bwgc_text3']?></p>
							<?php if(!$bwgc_link0 && $bwgc_link3){ ?></a><?php } ?>
							<?php } ?>
							<?php if($conarr[$i]['bwgc_text4']){ ?>
							<?php if(!$bwgc_link0 && $bwgc_link4){ ?><a href="<?=$bwgc_link4?>" target="<?=$conarr[$i]['bwgc_link4_target']?>"><?php } ?>
							<p class="txt4"><?=$conarr[$i]['bwgc_text4']?></p>
							<?php if(!$bwgc_link0 && $bwgc_link4){ ?></a><?php } ?>
							<?php } ?>
						</div>
						<?php if($bwgc_link0){ ?></a><?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>

<script>
var <?=$bid?>_slk = $('#<?=$bid?> .dv_slk');
var <?=$bid?>_sld = $('#<?=$bid?> .dv_slk .dv_sld');
var <?=$bid?>_sld_cnt = <?=count($conarr)?>;
var <?=$bid?>_autoplay = ('<?=$autoplay?>' == 'yes') ? true : false;
var <?=$bid?>_autoplaySpeed = <?=$autoplaySpeed?> * 1000;
var <?=$bid?>_speed = <?=$speed?> * 1000;
var <?=$bid?>_infinite = ('<?=$infinite?>' == 'yes') ? true : false;
var <?=$bid?>_dots = ('<?=$dots?>' == 'yes') ? true : false;
var <?=$bid?>_arrows = ('<?=$arrows?>' == 'yes') ? true : false;
var <?=$bid?>_fade = ('<?=$fade?>' == 'yes') ? true : false;
var <?=$bid?>_swipe = ('<?=$swipe?>' == 'yes') ? true : false;
var <?=$bid?>_pauseOnFocus = ('<?=$pauseOnFocus?>' == 'yes') ? true : false;
var <?=$bid?>_pauseOnHover = ('<?=$pauseOnHover?>' == 'yes') ? true : false;
var <?=$bid?>_pauseOnDotsHover = ('<?=$pauseOnDotsHover?>' == 'yes') ? true : false;
var <?=$bid?>_slidesToShow = 1;//<?=$slidesToShow?>;
var <?=$bid?>_vertical = ('<?=$vertical?>' == 'yes') ? true : false;
var <?=$bid?>_verticalSwiping = ('<?=$verticalSwiping?>' == 'yes') ? true : false;
var <?=$bid?>_txt1_ani_type = '<?=$text1_ani_type?>';
var <?=$bid?>_txt2_ani_type = '<?=$text2_ani_type?>';
var <?=$bid?>_txt3_ani_type = '<?=$text3_ani_type?>';
var <?=$bid?>_txt4_ani_type = '<?=$text4_ani_type?>';
//console.log(<?=$bid?>_sld_cnt);
<?=$bid?>_slk.on('init',function(event,slick){
	//console.log(slick.loadIndex);
	<?=$bid?>_tani(<?=$bid?>_sld_cnt-1,slick.loadIndex);
});

<?=$bid?>_slk.on('beforeChange',function(event,slick,currentSlide,nextSlide){
	//console.log('beforeChange:'+event+','+slick+','+currentSlide+','+nextSlide);
	//console.log('beforeChange:'+event);
	<?=$bid?>_tani(currentSlide,nextSlide);
});

<?=$bid?>_slk.slick({
	autoplay : <?=$bid?>_autoplay
	,autoplaySpeed : <?=$bid?>_autoplaySpeed
	,speed : <?=$bid?>_speed
	,infinite : <?=$bid?>_infinite
	,dots : <?=$bid?>_dots
	,arrows : <?=$bid?>_arrows
	,fade : <?=$bid?>_fade
	,swipe : <?=$bid?>_swipe
	,pauseOnFocus : <?=$bid?>_pauseOnFocus
	,pauseOnHover : <?=$bid?>_pauseOnHover
	,pauseOnDotsHover : <?=$bid?>_pauseOnDotsHover
	,slidesToShow : <?=$bid?>_slidesToShow
	,vertical : <?=$bid?>_vertical
	,verticalSwiping : <?=$bid?>_verticalSwiping
});

<?=$bid?>_slk.find('.sld_ytb').each(function(){
	$(this).YTPlayer();
});

function <?=$bid?>_tani(bef,nex){
	//이전화면의 문장들 비활성
	if($('#<?=$bid?> .dv_text'+bef).find('.txt1').length > 0){
		if(<?=$bid?>_txt1_ani_type != '') $('#<?=$bid?> .dv_text'+bef).find('.txt1').textillate('stop');
	}
	if($('#<?=$bid?> .dv_text'+bef).find('.txt2').length > 0){
		if(<?=$bid?>_txt2_ani_type != '') $('#<?=$bid?> .dv_text'+bef).find('.txt2').textillate('stop');
	}
	if($('#<?=$bid?> .dv_text'+bef).find('.txt3').length > 0){
		if(<?=$bid?>_txt3_ani_type != '') $('#<?=$bid?> .dv_text'+bef).find('.txt3').textillate('stop');
	}
	if($('#<?=$bid?> .dv_text'+bef).find('.txt4').length > 0){
		if(<?=$bid?>_txt4_ani_type != '') $('#<?=$bid?> .dv_text'+bef).find('.txt4').textillate('stop');
	}
	//다음화면의 문장들 활성
	if($('#<?=$bid?> .dv_text'+nex).find('.txt1').length > 0){
		if(<?=$bid?>_txt1_ani_type != ''){
			$('#<?=$bid?> .dv_text'+nex).show().find('.txt1').textillate({in:{effect:<?=$bid?>_txt1_ani_type}});
			$('#<?=$bid?> .dv_text'+nex).find('.txt1').textillate('start');
		}else{
			$('#<?=$bid?> .dv_text'+nex).show();
		}
	}
	if($('#<?=$bid?> .dv_text'+nex).find('.txt2').length > 0){
		if(<?=$bid?>_txt2_ani_type != ''){
			$('#<?=$bid?> .dv_text'+nex).show().find('.txt2').textillate({in:{effect:<?=$bid?>_txt2_ani_type}});
			$('#<?=$bid?> .dv_text'+nex).find('.txt2').textillate('start');
		}else{
			$('#<?=$bid?> .dv_text'+nex).show();
		}
	}
	if($('#<?=$bid?> .dv_text'+nex).find('.txt3').length > 0){
		if(<?=$bid?>_txt3_ani_type != ''){
			$('#<?=$bid?> .dv_text'+nex).show().find('.txt3').textillate({in:{effect:<?=$bid?>_txt3_ani_type}});
			$('#<?=$bid?> .dv_text'+nex).find('.txt3').textillate('start');
		}else{
			$('#<?=$bid?> .dv_text'+nex).show();
		}
	}
	if($('#<?=$bid?> .dv_text'+nex).find('.txt4').length > 0){
		if(<?=$bid?>_txt4_ani_type != ''){
			$('#<?=$bid?> .dv_text'+nex).show().find('.txt4').textillate({in:{effect:<?=$bid?>_txt4_ani_type}});
			$('#<?=$bid?> .dv_text'+nex).find('.txt4').textillate('start');
		}else{
			$('#<?=$bid?> .dv_text'+nex).show();
		}
	}
}
</script>
<?php
}//if(count($conarr))
//내용목록이 존재하지 않을때
else{
	echo '<div class="bwg_empty" style="text-align:center;padding:100px 0;border:1px solid #ddd;"><a href="'.G5_BPWIDGET_ADMIN_URL.'/bpwidget_form.php?bwgs_idx='.$bwgs_idx.'&w=u&bwg_con=1" target="_blank">['.strtoupper($bwg_arr['config']['bwgs_name']).']</a>의 내용이 존재하지 않습니다.</div>'.PHP_EOL;
}
?>