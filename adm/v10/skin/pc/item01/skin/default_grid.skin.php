<?php
if (!defined('_GNUBOARD_')) exit;

$it_margin = 20;
$dif_width = $it_margin * ($item_mod_cnt - 1);
$it_ratio = $thumb_width_size / $thumb_height_size;
$it_width = ($bsic_width_size - $dif_width) / $item_mod_cnt;
$it_height = $it_width / $it_ratio;
?>
<style>
#<?=$bid?> .itdiv{padding-bottom:20px;}
#<?=$bid?> .itdiv .itul{width:<?=$bsic_width_size?>px;margin:0 auto;}
#<?=$bid?> .itdiv .itul:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .itdiv .itul .itli{float:left;width:<?=$it_width?>px;margin-top:<?=$it_margin?>px;margin-left:<?=$it_margin?>px;}
#<?=$bid?> .itdiv .itul .itli:first-child{margin-left:0;}
#<?=$bid?> .itdiv .itul .itli.first_row{margin-left:0;}
#<?=$bid?> .itdiv .itul .itli.first_top{margin-top:0;}
#<?=$bid?> .itdiv .itul .itli > a .itimg{border:1px solid #c7c7c7;overflow:hidden;}
#<?=$bid?> .itdiv .itul .itli > a .itimg img{width:100%;height:auto;transform:scale(1);-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transition:all 0.3s ease-in-out;}
#<?=$bid?> .itdiv .itul .itli:hover > a .itimg img{overflow:hidden;transform:scale(1.2);-webkit-transform:scale(1.2);-moz-transform:scale(1.2);-ms-transform:scale(1.2);-o-transform:scale(1.2);}
#<?=$bid?> .itdiv .itul .itli .itinfo{background:<?=$basic_bg_color?>;text-align:center;padding:5px 10px;height:<?=$info_height_size?>px;overflow:hidden;border:1px solid #c7c7c7;border-top:0px;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_icon{text-align:center;margin-top:5px;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_icon .sit_icon{}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_icon .sit_icon .shop_icon{text-align:center;padding:0 2px;font-size:1em;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_id{color:<?=$id_font_color?>;font-size:<?=$id_font_size?>px;margin-top:10px;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_name{color:<?=$name_font_color?>;font-size:<?=$name_font_size?>px;font-weight:600;margin-top:12px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_basic{color:<?=$basic_font_color?>;font-size:<?=$basic_font_size?>px;margin-top:10px;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;height:1.5em;line-height:1.5em;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_cust_price{color:<?=$custprice_font_color?>;font-size:<?=$custprice_font_size?>px;margin-top:10px;text-decoration:line-through;}
#<?=$bid?> .itdiv .itul .itli .itinfo > a .it_price{color:<?=$price_font_color?>;font-size:<?=$price_font_size?>px;margin-top:10px;font-weight:600;}
#<?=$bid?> .itdiv .itul .itli .itinfo .itsns{text-align:center;margin-top:6px;}
#<?=$bid?> .itdiv .itul .itli .itinfo .itsns a{}
#<?=$bid?> .itdiv .itul .itli .itinfo .itsns a img{width:20px;height:20px;margin-left:10px;margin-top:5px;}
#<?=$bid?> .itdiv .itul .itli .itinfo .itsns a:first-child img{margin-left:0px;}
#<?=$bid?> .itdiv .it_btn_box{text-align:center;padding-top:<?=$listlink_top_interval?>px;padding-bottom:<?=$listlink_bottom_interval?>px;}
#<?=$bid?> .itdiv .it_btn_box .it_btn{display:inline-block;color:<?=$listlink_font_color?>;font-size:<?=$listlink_font_size?>px;background:<?=$listlink_bg_color?>;width:<?=$listlink_width_size?>px;height:<?=$listlink_height_size?>px;line-height:<?=$listlink_height_size?>px;}
</style>
<div class="itdiv" data-aos="fade-up">
	<ul class="itul">
	<?php for($i=0; $row=sql_fetch_array($itresult); $i++){ 
		//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false
		$it_img = bpwg_get_it_image($row['it_id'], $thumb_width_size, $thumb_height_size,false,'',$row['it_name'],true);
		$href = shop_item_url($row['it_id']);
		$first_row_class = ($i % $item_mod_cnt == 0) ? ' first_row' : '';
		$first_top_class = ($i < $item_mod_cnt) ? ' first_top' : '';
		
	?>
		<li class="itli<?=$first_row_class.$first_top_class?>">
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
		</li><!--//.itli-->
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
