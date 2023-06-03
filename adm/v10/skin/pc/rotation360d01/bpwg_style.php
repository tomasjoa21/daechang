<?php
$align0_css = ($rollimg_horizontal == 'center') ? 'center' : 'left';
$width0_css = ($rollimg_horizontal == 'center') ? 'width:100%;' : '';

if($rollimg_horizontal == 'left') $posx0_css = 'left:'.$rollimg_x_pos.'px;';
else if($rollimg_horizontal == 'right') $posx0_css = 'right:'.$rollimg_x_pos.'px;';
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
#<?=$bid?> .ro_img .ro_wrap .txt1{color:<?=$text1_color?>;font-size:<?=$text1_font_size?>px;font-weight:700;text-align:<?=$align1_css?>;<?=$width1_css?><?=$posx1_css?>top:<?=$text_y_pos?>px;z-index:4;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt2{color:<?=$text2_color?>;font-size:<?=$text2_font_size?>px;font-weight:600;text-align:<?=$align2_css?>;<?=$width2_css?><?=$posx2_css?>top:<?=$text2_y_pos?>px;z-index:5;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt3{color:<?=$text3_color?>;font-size:<?=$text3_font_size?>px;font-weight:500;text-align:<?=$align3_css?>;<?=$width3_css?><?=$posx3_css?>top:<?=$text3_y_pos?>px;z-index:6;display:none;}
#<?=$bid?> .ro_img .ro_wrap .txt4{color:<?=$text4_color?>;font-size:<?=$text4_font_size?>px;font-weight:400;text-align:<?=$align4_css?>;<?=$width4_css?><?=$posx4_css?>top:<?=$text4_y_pos?>px;z-index:7;display:none;}

#<?=$bid?> .ro_img .ro_wrap .rollbox{position:absolute;z-index:3;text-align:<?=$align0_css?>;<?=$width0_css?><?=$posx0_css?>top:<?=$rollimg_y_pos?>px;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .rollul{position:relative;display:inline-block;width:<?=$rollimg_wd?>px;height:<?=$rollimg_ht?>px;overflow:hidden;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .rollul .rolli{display:none;position:absolute;top:0;left:0;width:<?=$rollimg_wd?>px;height:<?=$rollimg_ht?>px;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .rollul .rolli.focus{display:block;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading{position:absolute;top:0;left:0;z-index:5;width:100%;height:100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl{display:table;width:100%;height:100%;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl .loading_td{display:table-cell;text-align:center;vertical-align:middle;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl .loading_td .bar{position:relative;display:inline-block;width:140px;height:16px;border-radius:8px;overflow:hidden;background:#777777;box-shadow:0 0 5px #fff;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl .loading_td .bar strong{position:absolute;top:0;right:0;display:block;height:16px;line-height:14px;padding:0 10px;color:#fff;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl .loading_td .bar strong span{font-size:14px;margin-right:3px;}
#<?=$bid?> .ro_img .ro_wrap .rollbox .loading .loading_tbl .loading_td .bar .progress{width:0%;height:100%;background:#e89805;border-radius:8px;}
</style>