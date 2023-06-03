<?php
$align0_css = ($plximg_horizontal == 'center') ? 'center' : 'left';
$width0_css = ($plximg_horizontal == 'center') ? 'width:100%;' : '';

if($plximg_horizontal == 'left') $posx0_css = 'left:'.$plximg_x_pos.'px;';
else if($plximg_horizontal == 'right') $posx0_css = 'right:'.$plximg_x_pos.'px;';
else $posx0_css = 'left:0px;';

$align1_css = ($text_horizontal == 'center') ? 'center' : 'left';
$align2_css = ($text2_horizontal == 'center') ? 'center' : 'left';
$align3_css = ($text3_horizontal == 'center') ? 'center' : 'left';
$align4_css = ($text4_horizontal == 'center') ? 'center' : 'left';

$width1_css = ($text_horizontal == 'center') ? 'width:100%;' : '';
$width2_css = ($text2_horizontal == 'center') ? 'width:100%;' : '';
$width3_css = ($text3_horizontal == 'center') ? 'width:100%;' : '';
$width4_css = ($text4_horizontal == 'center') ? 'width:100%;' : '';

if($text_horizontal == 'left') $posx1_css = 'left:'.$text_x_pos.'px;';
else if($text_horizontal == 'right') $posx1_css = 'right:'.$text_x_pos.'px;';
else $posx1_css = 'left:0px;';

if($text2_horizontal == 'left') $posx2_css = 'left:'.$text2_x_pos.'px;';
else if($text2_horizontal == 'right') $posx2_css = 'right:'.$text2_x_pos.'px;';
else $posx2_css = 'left:0px;';

if($text3_horizontal == 'left') $posx3_css = 'left:'.$text3_x_pos.'px;';
else if($text3_horizontal == 'right') $posx3_css = 'right:'.$text3_x_pos.'px;';
else $posx3_css = 'left:0px;';

if($text4_horizontal == 'left') $posx4_css = 'left:'.$text4_x_pos.'px;';
else if($text4_horizontal == 'right') $posx4_css = 'right:'.$text4_x_pos.'px;';
else $posx4_css = 'left:0px;';

?>
<style>
@charset "utf-8";
#<?=$bid?>{margin-top:<?=$bnr_top_interval?>px;}
#<?=$bid?> .plx_img{height:<?=$sld_ht?>px;position:relative;background-repeat:no-repeat;background-position:center center;background-size:cover;}
#<?=$bid?> .plx_img .inline-YTPlayer{position:absolute !important;left:0 !important;top:0 !important;z-index:0 !important;height:100% !important;width:100% !important;max-width:none !important;}
#<?=$bid?> .plx_img .inline-YTPlayer .inlinePlayButton{display:none !important;}

#<?=$bid?> .plx_img .plxbg{}
#<?=$bid?> .plx_img .plxblind{display:no ne;position:absolute;left:0;top:0;width:100%;height:100%;z-index:2;background:<?=$blind_color?>;}
#<?=$bid?> .plx_img #plxytb{position:relative;width:100% !important;height:100% !important;top:0;display:block !important;}
#<?=$bid?> .plx_img #plxpic{position:absolute;left:0;top:0;z-index:0;width:100% !important;height:100% !important;background-repeat:repeat-y;background-position:center center;background-size:100% auto;background-attachment:fixed;}
#<?=$bid?> .plx_img .plx_wrap{position:relative;z-index:3;width:<?=$mbnr_default_wd?>px;height:100%;margin:0 auto;}

#<?=$bid?> .plx_img .plx_wrap .txt{position:absolute;}
#<?=$bid?> .plx_img .plx_wrap .txt1{color:<?=$text1_color?>;font-size:<?=$text1_font_size?>px;font-weight:600;text-align:<?=$align1_css?>;<?=$width1_css?><?=$posx1_css?>top:<?=$text_y_pos?>px;z-index:4;display:none;}
#<?=$bid?> .plx_img .plx_wrap .txt2{color:<?=$text2_color?>;font-size:<?=$text2_font_size?>px;font-weight:400;text-align:<?=$align2_css?>;<?=$width2_css?><?=$posx2_css?>top:<?=$text2_y_pos?>px;z-index:5;display:none;}
#<?=$bid?> .plx_img .plx_wrap .txt3{color:<?=$text3_color?>;font-size:<?=$text3_font_size?>px;font-weight:400;text-align:<?=$align3_css?>;<?=$width3_css?><?=$posx3_css?>top:<?=$text3_y_pos?>px;z-index:6;display:none;}
#<?=$bid?> .plx_img .plx_wrap .txt4{color:<?=$text4_color?>;font-size:<?=$text4_font_size?>px;font-weight:400;text-align:<?=$align4_css?>;<?=$width4_css?><?=$posx4_css?>top:<?=$text4_y_pos?>px;z-index:7;display:none;}
<?php if($bwgc_text4 && !$bwgc_link0 && $bwgc_link4){ ?>
	#<?=$bid?> .plx_img .plx_wrap .txt4_a .txt4{width:<?=$text4btn_width_size?>px;height:<?=$text4btn_height_size?>px;line-height:<?=($text4btn_height_size-2)?>px;background:<?=$text4btn_bg_color?>;}
	<?php if($text4_horizontal == 'left'){ ?>
		#<?=$bid?> .plx_img .plx_wrap .txt4_a .txt4{left:<?=$text4_x_pos?>px;}
	<?php }else if($text4_horizontal == 'right'){ ?>
		#<?=$bid?> .plx_img .plx_wrap .txt4_a .txt4{right:<?=$text4_x_pos?>px;}
	<?php }else{ ?>
		#<?=$bid?> .plx_img .plx_wrap .txt4_a .txt4{left:50%;margin-left:<?=(-$text4btn_width_size/2)?>px;}
	<?php } ?>
<?php } ?>
</style>