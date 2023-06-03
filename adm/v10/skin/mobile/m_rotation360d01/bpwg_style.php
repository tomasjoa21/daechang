<?php
$rollimg_x_pos = ($rollimg_x_pos) ? $rollimg_x_pos : 0;
$rollimg_y_pos = ($rollimg_y_pos) ? $rollimg_y_pos : 0;

if($rollimg_horizontal == 'center' && $rollimg_vertical == 'middle'){
	$rollbox_td_style = 'text-align:center;vertical-align:middle;';
}else if($rollimg_horizontal == 'center' && $rollimg_vertical != 'middle'){
	$rollbox_td_style = 'text-align:center;';
	if($rollimg_vertical == 'top'){
		$rollbox_td_style .= 'vertical-align:top;';
	}else{
		$rollbox_td_style .= 'vertical-align:bottom;';
	}
	$rollbox_td_style .= 'padding-'.$rollimg_vertical.':'.$rollimg_y_pos.'%;';
}else if($rollimg_horizontal != 'center' && $rollimg_vertical == 'middle'){
	$rollbox_td_style = 'vertical-align:middle;';
	if($rollimg_horizontal == 'left'){
		$rollbox_td_style .= 'text-align:left;';
	}else{
		$rollbox_td_style .= 'text-align:right;';
	}
	$rollbox_td_style .= 'padding-'.$rollimg_horizontal.':'.$rollimg_x_pos.'%;';
}else{
	$rollbox_td_style = '';
	if($rollimg_vertical == 'top'){
		$rollbox_td_style .= 'vertical-align:top;';
	}else{
		$rollbox_td_style .= 'vertical-align:bottom;';
	}
	if($rollimg_horizontal == 'left'){
		$rollbox_td_style .= 'text-align:left;';
	}else{
		$rollbox_td_style .= 'text-align:right;';
	}
	$rollbox_td_style .= 'padding-'.$rollimg_vertical.':'.$rollimg_y_pos.'%;';
	$rollbox_td_style .= 'padding-'.$rollimg_horizontal.':'.$rollimg_x_pos.'%;';
}


$align1_css = ($text_horizontal == 'center') ? 'center' : 'left';
$align2_css = ($text2_horizontal == 'center') ? 'center' : 'left';
$align3_css = ($text3_horizontal == 'center') ? 'center' : 'left';
$align4_css = ($text4_horizontal == 'center') ? 'center' : 'left';

$width1_css = ($text_horizontal == 'center') ? 'width:100%;' : '';
$width2_css = ($text2_horizontal == 'center') ? 'width:100%;' : '';
$width3_css = ($text3_horizontal == 'center') ? 'width:100%;' : '';
$width4_css = ($text4_horizontal == 'center') ? 'width:100%;' : '';


$text_x_pos = ($text_x_pos) ? $text_x_pos : 0;
$text2_x_pos = ($text2_x_pos) ? $text2_x_pos : 0;
$text3_x_pos = ($text3_x_pos) ? $text3_x_pos : 0;
$text4_x_pos = ($text4_x_pos) ? $text4_x_pos : 0;

$text_y_pos = ($text_y_pos) ? $text_y_pos : 0;
$text2_y_pos = ($text2_y_pos) ? $text2_y_pos : 0;
$text3_y_pos = ($text3_y_pos) ? $text3_y_pos : 0;
$text4_y_pos = ($text4_y_pos) ? $text4_y_pos : 0;

if($text_horizontal == 'left') $posx1_css = 'left:'.$text_x_pos.'%;';
else if($text_horizontal == 'right') $posx1_css = 'right:'.$text_x_pos.'%;';
else $posx1_css = 'left:0%;';

if($text2_horizontal == 'left') $posx2_css = 'left:'.$text2_x_pos.'%;';
else if($text2_horizontal == 'right') $posx2_css = 'right:'.$text2_x_pos.'%;';
else $posx2_css = 'left:0%;';

if($text3_horizontal == 'left') $posx3_css = 'left:'.$text3_x_pos.'%;';
else if($text3_horizontal == 'right') $posx3_css = 'right:'.$text3_x_pos.'%;';
else $posx3_css = 'left:0%;';

if($text4_horizontal == 'left') $posx4_css = 'left:'.$text4_x_pos.'%;';
else if($text4_horizontal == 'right') $posx4_css = 'right:'.$text4_x_pos.'%;';
else $posx4_css = 'left:0%;';
//-----------------------------
if($text_vertical == 'top') $posy1_css = 'top:'.$text_y_pos.'%;';
else if($text_vertical == 'bottom') $posy1_css = 'bottom:'.$text_y_pos.'%;';
else $posy1_css = 'top:0%;';

if($text2_vertical == 'top') $posy2_css = 'top:'.$text2_y_pos.'%;';
else if($text2_vertical == 'bottom') $posy2_css = 'bottom:'.$text2_y_pos.'%;';
else $posy2_css = 'top:0%;';

if($text3_vertical == 'top') $posy3_css = 'top:'.$text3_y_pos.'%;';
else if($text3_vertical == 'bottom') $posy3_css = 'bottom:'.$text3_y_pos.'%;';
else $posy3_css = 'top:0%;';

if($text4_vertical == 'top') $posx4_css = 'top:'.$text4_x_pos.'%;';
else if($text4_vertical == 'bottom') $posy4_css = 'bottom:'.$text4_y_pos.'%;';
else $posy4_css = 'top:0%;';

?>
<style>
@charset "utf-8";
#<?=$bid?>{overflow:hidden;overflow:hidden !important;}
#<?=$bid?> .ro_img{height:<?=$sld_ht?>px;position:relative;background-repeat:no-repeat;background-position:center center;background-size:cover;}
#<?=$bid?> .ro_img .inline-YTPlayer{position:absolute !important;left:0 !important;top:0 !important;z-index:0 !important;height:100% !important;width:100% !important;max-width:none !important;}
#<?=$bid?> .ro_img .inline-YTPlayer .inlinePlayButton{display:none !important;}

#<?=$bid?> .ro_img .rollbg{}
#<?=$bid?> .ro_img .rollblind{display:no ne;position:absolute;left:0;top:0;width:100%;height:100%;z-index:2;background:<?=$blind_color?>;}
#<?=$bid?> .ro_img #rollytb{position:relative;width:100% !important;height:100% !important;}
#<?=$bid?> .ro_img #rollpic{position:absolute;left:0;top:0;z-index:0;width:100% !important;height:100% !important;background-repeat:no-repeat;background-position:center center;background-size:cover;}
#<?=$bid?> .ro_img .ro_wrap{position:relative;z-index:3;width:<?=$mbnr_default_wd?>px;height:100%;margin:0 auto;}

#<?=$bid?> .ro_img .ro_wrap .txt{position:absolute;}
#<?=$bid?> .ro_img .ro_wrap .txt1{color:<?=$text1_color?>;font-size:<?=$text1_font_size?>px;font-weight:700;text-align:<?=$align1_css?>;<?=$width1_css?><?=$posx1_css?><?=$posy1_css?>z-index:4;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt2{color:<?=$text2_color?>;font-size:<?=$text2_font_size?>px;font-weight:600;text-align:<?=$align2_css?>;<?=$width2_css?><?=$posx2_css?><?=$posy2_css?>z-index:5;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt3{color:<?=$text3_color?>;font-size:<?=$text3_font_size?>px;font-weight:500;text-align:<?=$align3_css?>;<?=$width3_css?><?=$posx3_css?><?=$posy3_css?>z-index:6;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt4{color:<?=$text4_color?>;font-size:<?=$text4_font_size?>px;font-weight:400;text-align:<?=$align4_css?>;<?=$width4_css?><?=$posx4_css?><?=$posy4_css?>z-index:7;display:none;}


#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl{display:table;width:100%;height:100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td{display:table-cell;position:relative;overflow:hidden;<?=$rollbox_td_style?>}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox{position:relative;display:inline-block;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .rollul{position:relative;display:inline-block;width:<?=$rollimg_wd?>px;height:<?=$rollimg_ht?>px;overflow:hidden;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .rollul .rolli{display:none;position:absolute;top:0;left:0;width:100%;height:100%;background-repeat:no-repeat;background-position:center center;background-size:100% 100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .rollul .rolli.focus{display:block;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading{position:absolute;top:0;left:0;z-index:5;width:100%;height:100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl{display:table;width:100%;height:100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl .loading_td{display:table-cell;text-align:center;vertical-align:middle;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl .loading_td .bar{position:relative;display:inline-block;width:140px;height:16px;border-radius:8px;overflow:hidden;background:#777777;box-shadow:0 0 5px #fff;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl .loading_td .bar strong{position:absolute;top:0;right:0;display:block;height:16px;line-height:14px;padding:0 10px;color:#fff;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl .loading_td .bar strong span{font-size:14px;margin-right:3px;}
#<?=$bid?> .ro_img .ro_wrap .rollbox_tbl .rollbox_td .rollbox .loading .loading_tbl .loading_td .bar .progress{width:0%;height:100%;background:#e89805;border-radius:8px;}
</style>