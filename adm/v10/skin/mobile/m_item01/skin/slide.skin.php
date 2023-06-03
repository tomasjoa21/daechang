<?php
if (!defined('_GNUBOARD_')) exit;

bwg_add_css_file('slick',2);
bwg_add_css_file('slick_theme',2);
bwg_add_js_file('slick_min',2);

$it_margin = $item_horizontal_interval;
$dif_width = $it_margin * ($item_mod_cnt - 1);
$it_ratio = $thumb_width_size / $thumb_height_size;
?>
<style>
#<?=$bid?> .itdiv{padding-bottom:20px;}
#<?=$bid?> .itdiv .itul{}
#<?=$bid?> .itdiv .itul .itlidv .itli_blind{position:absolute;display:block;left:0;top:0;width:100%;height:100%;z-index5;background:<?=$blind_color?>;}
#<?=$bid?> .itdiv .itul .slick-slide.slick-current.slick-active.slick-center .itli_blind{display:none;}
#<?=$bid?> .itdiv .itul .itli{padding:0 <?=$item_horizontal_interval?>px;}
#<?=$bid?> .itdiv .itul .itli .itlidv{position:relative;border:1px solid <?=$list_line_color?>;background:<?=$basic_bg_color?>;}
#<?=$bid?> .itdiv .itul .itli .itlidv > a .itimg{overflow:hidden;}
#<?=$bid?> .itdiv .itul .itli .itlidv > a .itimg img{border-bottom:1px solid #c7c7c7;width:100%;height:auto;}
/*#<?=$bid?> .itdiv .itul .itli .itlidv > a .itimg img{width:100%;height:auto;transform:scale(1);-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transition:all 0.3s ease-in-out;}
#<?=$bid?> .itdiv .itul .itli:hover .itlidv > a .itimg img{overflow:hidden;transform:scale(1.2);-webkit-transform:scale(1.2);-moz-transform:scale(1.2);-ms-transform:scale(1.2);-o-transform:scale(1.2);}*/
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo{text-align:center;padding:5px 10px;height:<?=$info_height_size?>px;overflow:hidden;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_icon{text-align:center;margin-top:5px;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_icon .sit_icon{}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_icon .sit_icon .shop_icon{text-align:center;padding:0 2px;font-size:1em;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_id{color:<?=$id_font_color?>;font-size:<?=$id_font_size?>px;margin-top:8px;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_name{color:<?=$name_font_color?>;font-size:<?=$name_font_size?>px;font-weight:600;margin-top:8px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_basic{color:<?=$basic_font_color?>;font-size:<?=$basic_font_size?>px;margin-top:8px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_cust_price{color:<?=$custprice_font_color?>;font-size:<?=$custprice_font_size?>px;margin-top:8px;text-decoration:line-through;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo > a .it_price{color:<?=$price_font_color?>;font-size:<?=$price_font_size?>px;margin-top:8px;font-weight:600;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo .itsns{text-align:right;margin-top:6px;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo .itsns a{}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo .itsns a img{width:20px;height:20px;margin-left:10px;margin-top:5px;}
#<?=$bid?> .itdiv .itul .itli .itlidv .itinfo .itsns a:first-child img{margin-left:0px;}

#<?=$bid?> .itdiv .itul .slick-arrow{z-index:100;width:30px;height:30px;text-indent:-999px;overflow:hidden;}
#<?=$bid?> .itdiv .itul .slick-arrow svg{position:absolute;top:0;}
#<?=$bid?> .itdiv .itul .slick-prev{left:1%;}
#<?=$bid?> .itdiv .itul .slick-prev svg{left:0;}
#<?=$bid?> .itdiv .itul .slick-next{right:1%;}
#<?=$bid?> .itdiv .itul .slick-next svg{right:0;}

#<?=$bid?> .itdiv .it_btn_box{text-align:center;padding-top:<?=$listlink_top_interval?>px;padding-bottom:<?=$listlink_bottom_interval?>px;}
#<?=$bid?> .itdiv .it_btn_box .it_btn{display:inline-block;color:<?=$listlink_font_color?>;font-size:<?=$listlink_font_size?>px;background:<?=$listlink_bg_color?>;width:<?=$listlink_width_size?>px;height:<?=$listlink_height_size?>px;line-height:<?=$listlink_height_size?>px;}

#<?=$bid?> .itdiv .arrow_box{display:none;}
</style>
<div class="itdiv" data-aos="fade-down">
	<div class="itul">
	<?php for($i=0; $row=sql_fetch_array($itresult); $i++){ 
		//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false
		$it_img = bpwg_get_it_image($row['it_id'], $thumb_width_size, $thumb_height_size,false,'',$row['it_name'],true);
		$href = shop_item_url($row['it_id']);
		$first_row_class = '';//($i % $item_mod_cnt == 0) ? ' first_row' : '';
		$first_top_class = '';//($i < $item_mod_cnt) ? ' first_top' : '';
		
	?>
		<div class="itli<?=$first_row_class.$first_top_class?>">
			<div class="itlidv">
				<div class="itli_blind"></div>
				<a href="<?=$href?>">
					<div class="itimg<?=$lst_class?>">
					<?php echo $it_img; ?>
					</div>
				</a>
				<div class="itinfo">
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
					//echo bpwg_get_sns_share_link('googleplus', $sns_url, $sns_title, G5_BPWIDGET_SV_URL.'/sns_icon_googleplus_color.svg');
					echo '</div>'.PHP_EOL;
					} ?>
				</div>
			</div><!--//.itlidv-->
		</div><!--//.itli-->
	<?php } ?>
	</div><!--//.itul-->
	<?php if($listlink_show == 'show' && $item_show_type != 7){ ?>
	<div class="it_btn_box">
		<?php
		$item_list_url = ($item_show_type < 6) ? G5_SHOP_URL.'/listtype.php?type='.$item_show_type : G5_SHOP_URL.'/list.php?ca_id='.$ca_id;
		?>
		<a class="it_btn" href="<?=$item_list_url?>" target="_self">해당목록페이지</a>
	</div>
	<?php } ?>
	<div class="arrow_box">
	<?php
	bpwg_icon('arrow_left',$arrow_icon_color,$arrow_icon_size,$arrow_icon_size,false,'');
	bpwg_icon('arrow_right',$arrow_icon_color,$arrow_icon_size,$arrow_icon_size,false,'');
	?>
	</div>
</div><!--//.itdiv-->
<script>
var <?=$bid?>_slk = $('#<?=$bid?> .itdiv .itul');
<?=$bid?>_slk.slick({
	centerMode : true
	,cetnerPadding : '60px'
	,slidesToShow : 1
});
$('#<?=$bid?> .icon_arrow_left').appendTo('#<?=$bid?> .itdiv .itul .slick-prev');
$('#<?=$bid?> .icon_arrow_right').appendTo('#<?=$bid?> .itdiv .itul .slick-next');

/*
,responsive : [{
	breakpoint : 768
	,settings : {
		arrows : true
		,centerMode : true
		,centerPadding : '40px'
		,slidesToShow : 1
	}
},{
	breakpoint : 480
	,settings : {
		arrows : true
		,centerMode : true
		,centerPadding : '40px'
		,slidesToShow : 3
	}
}]
*/

</script>