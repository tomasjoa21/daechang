<?php
if (!defined('_GNUBOARD_')) exit;

$first_flag = false;
$hoz_padding = 15;
?>
<style>
#<?=$bid?> .itdiv{padding-bottom:20px;}
#<?=$bid?> .itdiv .itul{margin:0 auto;}
#<?=$bid?> .itdiv .itul:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .itdiv .itul .itli{float:left;}
#<?=$bid?> .itdiv .itul .itli:after{display:bock;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .itdiv .itul .itli.it_imgtxt{}
#<?=$bid?> .itdiv .itul .itli .con_box{position:relative;}
#<?=$bid?> .itdiv .itul .itli .img_box{overflow:hidden;}
#<?=$bid?> .itdiv .itul .itli .img_box img{width:100%;height:auto;}

#<?=$bid?> .itdiv .itul .itli .con_box .tbl{display:table;width:100%;height:100%;position:absolute;top:0;left:0;}
#<?=$bid?> .itdiv .itul .itli .con_box .tbl .td{display:table-cell;}
#<?=$bid?> .itdiv .itul .itli .img_box .tbl{opacity:0}
#<?=$bid?> .itdiv .itul .itli .img_box .tbl .td{text-align:center;vertical-align:middle;background:<?=$blind_color?>;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td{vertical-align:middle;padding:0 <?=$hoz_padding?>px;}

#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td p{display:block;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_icon .shop_icon{text-align:center;padding:0 2px;font-size:1em;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_id{color:<?=$id_font_color?>;font-size:<?=$id_font_size?>px;margin-top:8px;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_name{color:<?=$name_font_color?>;font-size:<?=$name_font_size?>px;font-weight:600;margin-top:8px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_basic{color:<?=$basic_font_color?>;font-size:<?=$basic_font_size?>px;margin-top:8px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_cust_price{color:<?=$custprice_font_color?>;font-size:<?=$custprice_font_size?>px;margin-top:8px;text-decoration:line-through;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .it_price{color:<?=$price_font_color?>;font-size:<?=$price_font_size?>px;margin-top:8px;font-weight:600;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .itsns a img{width:20px;height:20px;margin-left:5px;margin-top:8px;}
#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td .itsns a:first-child img{margin-left:0px;}

#<?=$bid?> .itdiv .itul .itli .txt_box{background:<?=$basic_bg_color?>;overflow:hidden;}
#<?=$bid?> .itdiv .itul .itli.it_imgtxt .img_box{float:left;}
#<?=$bid?> .itdiv .itul .itli.it_imgtxt .txt_box{float:right;text-align:left;}
#<?=$bid?> .itdiv .itul .itli.it_txtimg{}
#<?=$bid?> .itdiv .itul .itli.it_txtimg .txt_box{float:left;text-align:right;}
#<?=$bid?> .itdiv .itul .itli.it_txtimg .img_box{float:right;}

#<?=$bid?> .itdiv .it_btn_box{text-align:center;padding-top:<?=$listlink_top_interval?>px;padding-bottom:<?=$listlink_bottom_interval?>px;}
#<?=$bid?> .itdiv .it_btn_box .it_btn{display:inline-block;color:<?=$listlink_font_color?>;font-size:<?=$listlink_font_size?>px;background:<?=$listlink_bg_color?>;width:<?=$listlink_width_size?>px;height:<?=$listlink_height_size?>px;line-height:<?=$listlink_height_size?>px;}
</style>
<div class="itdiv" data-aos="fade-right">
	<ul class="itul">
	<?php for($i=0; $row=sql_fetch_array($itresult); $i++){ 
		//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false
		$it_img = bpwg_get_it_image($row['it_id'], $thumb_width_size, $thumb_width_size,false,'',$row['it_name'],true);
		$href = shop_item_url($row['it_id']);
		
		if($i > 0 && $i % $item_mod_cnt == 0){
			$first_flag = !$first_flag;
		}
		
		if(!$first_flag){
			$chess_class = " it_imgtxt";
		}else{
			$chess_class = " it_txtimg";
		}
	?>
	<li class="itli<?=$chess_class?>">
		<div class="con_box img_box" title="<?=$row['it_name']?>">
			<a href="<?=$href?>">
				<?=$it_img?>
				<div class="tbl">
					<div class="td"><?=bpwg_icon('search',$blind_icon_color,false,50,50)?></div>
				</div>
			</a>
		</div>
		<div class="con_box txt_box">
			<div class="tbl">
				<div class="td">
					<a href="<?=$href?>">
						<?php if($icon_show == 'show'){ ?><p class="it_icon"><?=bpwg_item_icon($row)?></p><?php } ?>
						<?php if($id_show == 'show'){ ?><p class="it_id">[<?=$row['it_id']?>]</p><?php } ?>
						<?php if($name_show == 'show'){ ?><p class="it_name"><?=$row['it_name']?></p><?php } ?>
						<?php if($basic_show == 'show' && $row['it_basic']){ ?><p class="it_basic"><?=$row['it_basic']?></p><?php } ?>
						<?php if($custprice_show == 'show' && $row['it_cust_price']){ ?><p class="it_cust_price"><?php echo display_price($row['it_cust_price']); ?></p><?php } ?>
						<?php if($price_show == 'show' && $row['it_price']){ ?><p class="it_price"><?php echo display_price(get_price($row), $row['it_tel_inq']); ?></p><?php } ?>
					</a>
					<?php if($sns_show == 'show'){
					echo '<div class="itsns">'.PHP_EOL;
					$sns_url  = $href;
					$sns_title = get_text($row['it_name']).' | '.get_text($config['cf_title']);
					echo bpwg_get_sns_share_link('kakaotalk', $sns_url, $sns_title, G5_BPWIDGET_SVG_URL.'/sns_icon_kakao_mono.svg');
					echo bpwg_get_sns_share_link('facebook', $sns_url, $sns_title, G5_BPWIDGET_SVG_URL.'/sns_icon_facebook_mono.svg');
					echo bpwg_get_sns_share_link('twitter', $sns_url, $sns_title, G5_BPWIDGET_SVG_URL.'/sns_icon_twitter_mono.svg');
					//echo bpwg_get_sns_share_link('googleplus', $sns_url, $sns_title, G5_BPWIDGET_SVG_URL.'/sns_icon_googleplus_color.svg');
					echo '</div>'.PHP_EOL;
					} ?>
				</div>
			</div>
		</div>
	</li>
	<?php } ?>
	</ul><!--//.itul-->
	<?php if($listlink_show == 'show' && $item_show_type != 7){ ?>
	<div class="it_btn_box">
		<?php
		$item_list_url = ($item_show_type < 6) ? G5_SHOP_URL.'/listtype.php?type='.$item_show_type : G5_SHOP_URL.'/list.php?ca_id='.$ca_id;
		?>
		<a class="it_btn" href="<?=$item_list_url?>" target="_self">해당목록페이지</a>
	</div>
	<?php } ?>
</div><!--//.itdiv-->
<script>
var <?=$bid?>_itul = $('#<?=$bid?> .itdiv .itul');
var <?=$bid?>_itli = $('#<?=$bid?> .itdiv .itul .itli');
var <?=$bid?>_con_box = $('#<?=$bid?> .itdiv .itul .itli .con_box');
var <?=$bid?>_td_p = $('#<?=$bid?> .itdiv .itul .itli .txt_box .tbl .td p');
var <?=$bid?>_hoz_padding = 15;
var <?=$bid?>_wd = $('body').width() - <?=($total_horizontal_interval*2)?>;
var <?=$bid?>_itli_wd = <?=$bid?>_wd / <?=$item_mod_cnt?>;
var <?=$bid?>_con_wd = <?=$bid?>_itli_wd / 2;
<?=$bid?>_itul.css({'width':<?=$bid?>_wd});
<?=$bid?>_itli.css({'width':<?=$bid?>_itli_wd});
<?=$bid?>_con_box.css({'width':<?=$bid?>_con_wd,'height':<?=$bid?>_con_wd});
<?=$bid?>_td_p.css({'width':(<?=$bid?>_con_wd - (<?=$bid?>_hoz_padding * 2))});
$(window).resize(function(){
	<?=$bid?>_wd = $('body').width() - <?=($total_horizontal_interval*2)?>;
	<?=$bid?>_itli_wd = <?=$bid?>_wd / <?=$item_mod_cnt?>;
	<?=$bid?>_con_wd = <?=$bid?>_itli_wd / 2;
	<?=$bid?>_itul.css({'width':<?=$bid?>_wd});
	<?=$bid?>_itli.css({'width':<?=$bid?>_itli_wd});
	<?=$bid?>_con_box.css({'width':<?=$bid?>_con_wd,'height':<?=$bid?>_con_wd});
	<?=$bid?>_td_p.css({'width':(<?=$bid?>_con_wd - (<?=$bid?>_hoz_padding * 2))});
});
</script>