<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		if($key == 'mb_id') ${'bwgs_mb_id'} = $val;
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
	$wid = 'rot_'.$bid.bpwg_uniqid();
	
}

if(count($bwg_arr['onecont'])){
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
	$todt = sql_fetch(" SELECT bwto_idx FROM {$g5['bpwidget_turn_option_table']} WHERE bwto_status = 'ok' AND bwto_db_id = '{$it_id}' AND bwto_db_type = 'shop' AND bwto_db_name = 'item' AND bwto_array = 'rofile' ");
	$bwto_idx = $todt['bwto_idx'];
	$orig_wd = 0;
	$orig_ht = 0;
	
	//if(!($orig_wd > $rollimg_wd && $orig_ht > $rollimg_ht)){
	//	$rollimg_wd = $orig_wd;
	//	$rollimg_ht = $orig_ht;
	//}
	
	$rofile = array();
	$rflesql = " SELECT * FROM {$g5['bpwidget_turn_file_table']}  WHERE bwto_idx = '{$bwto_idx}' ORDER BY bwgt_sort,bwgt_idx ";
	$rf_result = sql_query($rflesql,1);
	if($rf_result->num_rows){
		for($i=0;$rfrow=sql_fetch_array($rf_result);$i++){
			if($i==0) $orig_wd = $rfrow['bwgt_width']; $orig_ht = $rfrow['bwgt_height'];
			//$rollimg_wd,$rollimg_ht
			if($orig_wd > $rollimg_wd && $orig_ht > $rollimg_ht){
				$thumb_wd = $rollimg_wd;
				$thumb_ht = $rollimg_ht;
				
				$thumbf = thumbnail($rfrow['bwgt_name'],G5_PATH.$rfrow['bwgt_path'],G5_PATH.$rfrow['bwgt_path'],$thumb_wd,$thumb_ht,false,true,'center');
				$thumbf_url = G5_URL.$rfrow['bwgt_path'].'/'.$thumbf;
			}else{
				$rollimg_wd = $orig_wd;
				$rollimg_ht = $orig_ht;
				$thumbf_url = G5_URL.$rfrow['bwgt_path'].'/'.$rfrow['bwgt_name'];
			}
			
			
			$rar = array();
			//$rar['bwgt_path'] = $rfrow['bwgt_path'];
			//$rar['bwgt_name'] = $rfrow['bwgt_name'];
			//$rar['bwgt_width'] = $rfrow['bwgt_width'];
			//$rar['bwgt_height'] = $rfrow['bwgt_height'];
			$rar['bwgt_thumb_url'] = $thumbf_url;
			
			array_push($rofile,$rar['bwgt_thumb_url']);
		}	
	}
	//echo $rollimg_wd.','.$rollimg_ht.'<br>';
	//print_r2($rofile);
}

if(count($bwg_arr['onefile'])){
	foreach($bwg_arr['onefile'] as $key=>$val){
		${$key} = $val;
	}
}

if($bwgc_ytb_url){
	bwg_add_css_file('jquery_mb_ytplayer_min',2);
	bwg_add_js_file('jquery_mb_ytplayer',2);
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
	
	if(count($rofile)){
		bwg_add_js_file('troll_min',3);
	}
	
	$bg_ratio = $sld_wd / $sld_ht;
	$rl_ratio = $rollimg_wd / $rollimg_ht;
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>&bwg_con=1" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<?php
	if($bwgc_ytb_url){
		$sldrr = explode('/',$bwgc_ytb_url);
		$sld_key = $sldrr[count($sldrr)-1];
		$sld_img_path = G5_PATH.$bwga_path.'/'.$bwga_name;
		$sld_img_url = G5_URL.$bwga_path.'/'.$bwga_name;
		$sld_bg = (is_file($sld_img_path)) ? 'style="background-image:url('.$sld_img_url.');"' : 'style="background-image:url(https://img.youtube.com/vi/'.$sld_key.'/maxresdefault.jpg);"';
		$sld_prop = 'ytb_url="'.$bwgc_ytb_url.'" data-property="{videoURL:\''.$bwgc_ytb_url.'\',vol:\'90\',stopMovieBlur:false,containment:\'#rollytb\',showControls:false,startAt:1,anchor:\'top, center\',mute:true,autoPlay:true,opacity:1,quality:\'highres\',playOnlyIfVisible:false,optimizeDisplay:true}"';
	}else{
		$sld_bg = '';
		$sld_img_style = 'style="background-image:url('.G5_URL.$bwga_path.'/'.$bwga_name.');"';
	}
	?>
	<div class="ro_img" <?=$sld_bg?>>
		<?php if($bwgc_ytb_url){ ?>
		<div class="rollbg" id="rollytb" <?=$sld_prop?>></div>
		<?php }else{ ?>
		<div class="rollbg" id="rollpic" <?=$sld_img_style?>></div>
		<?php } ?>
		<div class="rollblind"></div>
		<div class="ro_wrap">
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
			<?php if(!$bwgc_link0 && $bwgc_link4){ ?><a href="<?=$bwgc_link4?>" target="<?=$bwgc_link4_target?>"><?php } ?>
			<p class="txt txt4"><?=$bwgc_text4?></p>
			<?php if(!$bwgc_link0 && $bwgc_link4){ ?></a><?php } ?>
		<?php } ?>
		<?php if($bwgc_link0 = false){ ?></a><?php } ?>
		<?php if(count($rofile) && $rollimg_show == 'show'){ ?>
			<div class="rollbox_tbl">
				<div class="rollbox_td">
					<div class="rollbox">
						<ul class="rollul" style="opacity:0;">
							<?php for($i=0;$i<count($rofile);$i++){ ?>
							<li class="rolli" style="background-image:url(<?=$rofile[$i]?>);"></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		<?php } ?>
		</div><!--//ro_wrap-->
	</div><!--//.ro_img-->
</div><!--//#$bid-->
<script>
/*
$rollimg_horizontal : left,center,right
$rollimg_vertical : top,middle,bottom
$rollimg_x_pos : x_pos%
$rollimg_y_pos : y_pos%
$rollimg_gijun : width,height
$rollimg_rate : width_size% height_auto OR height_size% width_auto
*/
var <?=$bid?>_ratio = <?=$bg_ratio?>;
var <?=$bid?>_wd = $('body').width();
var <?=$bid?>_ht = <?=$bid?>_wd / <?=$bid?>_ratio;

var <?=$bid?>_r_gijun = '<?=$rollimg_gijun?>';
var <?=$bid?>_r_gijun_rate = <?=$rollimg_rate?>;
var <?=$bid?>_r_ratio = <?=$rl_ratio?>;
var <?=$bid?>_r_wd_ = <?=$bid?>_wd * (<?=$bid?>_r_gijun_rate / 100);
var <?=$bid?>_r_ht_ = <?=$bid?>_ht * (<?=$bid?>_r_gijun_rate / 100);
if(<?=$bid?>_r_gijun == 'width'){
	<?=$bid?>_r_wd = <?=$bid?>_r_wd_;
	<?=$bid?>_r_ht = <?=$bid?>_r_wd / <?=$bid?>_r_ratio;
}else{
	<?=$bid?>_r_ht = <?=$bid?>_r_ht_;
	<?=$bid?>_r_wd = <?=$bid?>_r_ht * <?=$bid?>_r_ratio;
}

var <?=$bid?>_ro_img = $('#<?=$bid?> .ro_img');
<?=$bid?>_ro_img.css({'width':<?=$bid?>_wd,'height':<?=$bid?>_ht});

var <?=$bid?>_ro_ul = $('#<?=$bid?> .rollbox .rollul');
<?=$bid?>_ro_ul.css({'width':<?=$bid?>_r_wd,'height':<?=$bid?>_r_ht});

var <?=$bid?>_autoplay = false;
<?php if(isset($rollimg_autoplay) && $rollimg_autoplay == "auto"){?>
<?=$bid?>_autoplay = true;
<?php } ?>
var <?=$bid?>_direct = '<?=$rollimg_mouse_direct?>';
var <?=$bid?>_auto_direct = 'a<?=$rollimg_autoplay_direct?>';
var <?=$bid?>_roll_speed = <?=($rollimg_speed * 1000)?>;
var <?=$bid?>_roll_loading_time = <?=($rollimg_loading_time * 1000)?>;
var <?=$bid?>_text_delay_time = <?=($text_delay_time * 1000)?>;
var <?=$bid?>_txt1_ani_type = '<?=$text1_ani_type?>';
var <?=$bid?>_txt2_ani_type = '<?=$text2_ani_type?>';
var <?=$bid?>_txt3_ani_type = '<?=$text3_ani_type?>';
var <?=$bid?>_txt4_ani_type = '<?=$text4_ani_type?>';
//alert(<?=$bid?>_auto_direct);
$(function(){
	<?php if($bwgc_ytb_url){ ?>
	var <?=$bid?>_ytb = $('#<?=$bid?> #rollytb');
	<?=$bid?>_ytb.YTPlayer();
	<?php } ?>
	<?php if(count($rofile) && $rollimg_show == 'show'){ ?>
	turn_obj('#<?=$bid?> .rollul',<?=$bid?>_direct,<?=$bid?>_autoplay,<?=$bid?>_auto_direct,<?=$bid?>_roll_speed,<?=$bid?>_roll_loading_time);//타겟객체,회전방향,자동회전여부,자동회전방향,속도,로딩시간
	//turn_obj('.roll_box','cw',true,'accw',200);//타겟객체,회전방향,자동회전여부,자동회전방향,속도,로딩시간
	//turn_obj('.roll_box2','ccw',true,'accw',400);//타겟객체,회전방향,자동회전여부,자동회전방향,속도,로딩시간
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

$(window).resize(function(){
	<?=$bid?>_wd = $('body').width();
	<?=$bid?>_ht = <?=$bid?>_wd / <?=$bid?>_ratio;
	<?=$bid?>_ro_img.css({'width':<?=$bid?>_wd,'height':<?=$bid?>_ht});
	
	<?=$bid?>_r_wd_ = <?=$bid?>_wd * (<?=$bid?>_r_gijun_rate / 100);
	<?=$bid?>_r_ht_ = <?=$bid?>_ht * (<?=$bid?>_r_gijun_rate / 100);
	if(<?=$bid?>_r_gijun == 'width'){
		<?=$bid?>_r_wd = <?=$bid?>_r_wd_;
		<?=$bid?>_r_ht = <?=$bid?>_r_wd / <?=$bid?>_r_ratio;
	}else{
		<?=$bid?>_r_ht = <?=$bid?>_r_ht_;
		<?=$bid?>_r_wd = <?=$bid?>_r_ht * <?=$bid?>_r_ratio;
	}
	<?=$bid?>_ro_ul.css({'width':<?=$bid?>_r_wd,'height':<?=$bid?>_r_ht});
});
</script>
<?php } //if(count($bwg_arr['onecont'])) ?>

