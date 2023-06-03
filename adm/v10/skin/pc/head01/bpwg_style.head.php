<?php
$toplink_ht = 36;
$sch_ht = 24;
$sch_top = ($toplink_ht - $sch_ht - 2) / 2;

$logo_area_width = $logo_width + ($logo_width / 2);
//$logo_area_height = $logo_height * 2.5;

$svg_down_wd = 14;
$svg_down_ht = 7;
$n1_svg_y = ($g5['is_explorer']) ? 0 : ($menu1_ht - $svg_down_ht) / 2;
$svg_side_wd = 6;
$svg_side_ht = 12;
$n2_svg_y = ($g5['is_explorer']) ? 0 : ($menu2_ht - $svg_side_ht) / 2;
//is_explorer
//print_r2($g5['is_explorer']);
?>
<style>
@charset "utf-8";
#hd{background:none;}
#<?=$bid?> input[type=text],#<?=$bid?> input[type=password],#<?=$bid?> textarea {
#<?=$bid?> -webkit-transition:none;
#<?=$bid?> -moz-transition:none;
#<?=$bid?> -ms-transition:none;
#<?=$bid?> -o-transition:none;
#<?=$bid?> outline:none;
}
#<?=$bid?> input[type=text]:focus,#<?=$bid?> input[type=password]:focus,#<?=$bid?> textarea:focus,select:focus {
#<?=$bid?> -webkit-box-shadow:none;
#<?=$bid?> -moz-box-shadow:none;
#<?=$bid?> box-shadow:none;
}
/*
#<?=$bid?> input[type=text],input[type=password], textarea {
-webkit-transition:all 0.30s ease-in-out;
-moz-transition:all 0.30s ease-in-out;
-ms-transition:all 0.30s ease-in-out;
-o-transition:all 0.30s ease-in-out;
outline:none;
}
#<?=$bid?> input[type=text]:focus,#<?=$bid?> input[type=password]:focus,#<?=$bid?> textarea:focus,select:focus {
#<?=$bid?> -webkit-box-shadow:0 0 5px #9ed4ff;
#<?=$bid?> -moz-box-shadow:0 0 5px #9ed4ff;
#<?=$bid?> box-shadow:0 0 5px #9ed4ff;
#<?=$bid?> border:1px solid #558ab7 !important;
}
*/
<?php if(defined('_INDEX_')){ ?>
#<?=$bid?>{position:relative;background:<?=$toppos_color?>;border-bottom:1px solid <?=$toppos_line_color?> !important;}
#<?=$bid?> .tlnk{display:block;background:<?=$toplink_top_color?>;border-bottom:1px solid <?=$toplink_line_color?>;}
<?php }else{ ?>
#<?=$bid?>{position:relative;background:<?=$sclpos_color?>;border-bottom:1px solid <?=$sclpos_line_color?> !important;}
#<?=$bid?> .tlnk{display:block;background:<?=$toplink_low_color?>;border-bottom:1px solid <?=$toplink_line_color?>;}
<?php } ?>
/*탑링크 영역*/
#<?=$bid?> .bpwg_btn_admin{position:absolute;top:5px;left:5px;}
#hd.head_sticky{position:fixed;top:0;left:0;width:100%;}
#hd.head_sticky #<?=$bid?>{background:<?=$sclpos_color?>;border-bottom:1px solid <?=$sclpos_line_color?> !important;}
#hd.head_sticky #<?=$bid?> .tlnk{display:none;}
<?php if($head_shadow == 'yes'){ ?>
#hd.head_sticky{box-shadow:<?=$head_shadow_x?>px <?=$head_shadow_y?>px <?=$head_shadow_size?>px <?=$head_shadow_color?>;}
<?php } ?>

#<?=$bid?> .tlnk .tlnk_in{width:<?=$head_default_wd?>px;margin:0 auto;}
#<?=$bid?> .tlnk .tlnk_in:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .tlnk .tlnk_in .tlnk_ul{}
#<?=$bid?> .tlnk .tlnk_in .tlnk_ul:after{display:block;visibility:hidden;clear:both:content:"";}
#<?=$bid?> .tlnk .tlnk_in .tlnk_ul li{float:left;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_ul li a{color:<?=$toplinkfont_color?>;display:block;padding:0px 10px;height:<?=$toplink_ht?>px;line-height:<?=$toplink_ht?>px;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left{float:left;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li{}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch{display:inline-block;position:relative;top:<?=$sch_top?>px;height:<?=$toplink_ht?>px;line-height:<?=$toplink_ht?>px;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form{background:<?=$schbg_color?>;position:relative;border:1px solid <?=$sch_color?>;border-radius:<?=($sch_ht/2)?>px;padding:0 34px 0 13px;overflow:hidden;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form input{background:none;float:left;border:0;padding:0 5px;width:170px;height:<?=$sch_ht?>px;line-height:<?=$sch_ht?>px;color:<?=$schfont_color?>;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form input::placeholder{color:<?=$schfont_color?>;font-weight:300;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form input:active,
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form input:focus{-moz-outline:none;outline:none;ie-dummy:expression(this.hideFocus=true);}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li .sch form button{background:none;position:absolute;top:2px;right:8px;border:0;padding:0;float:left;width:<?=($sch_ht-6)?>px;height:<?=($sch_ht-6)?>px;line-height:<?=($sch_ht-6)?>px;text-align:center;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li a{}
#<?=$bid?> .tlnk .tlnk_in .tlnk_left li:first-child a{padding-left:0;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_right{float:right;}
#<?=$bid?> .tlnk .tlnk_in .tlnk_right li{}
#<?=$bid?> .tlnk .tlnk_in .tlnk_right li a{}
#<?=$bid?> .tlnk .tlnk_in .tlnk_right li:last-child a{padding-right:0;}

#<?=$bid?> .logonav{}
#<?=$bid?> .logonav .logonav_in{position:relative;width:<?=$head_default_wd?>px;margin:0 auto;}
#<?=$bid?> .logonav .logonav_in table{display:table;border-collapse:collapse;border-spacing:0;width:100%;}
#<?=$bid?> .logonav .logonav_in table td{vertical-align:middle;height:<?=$logo_area_height?>px;}
#<?=$bid?> .logonav .logonav_in table td.td_logo{text-align:left;width:<?=$logo_area_width?>px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav{background:<?=$nav_bg_color?>;padding:0px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav{text-align:<?=$nav_align?>;margin-bottom:-3px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .nav_empty{text-align:center;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1{display:inline-block;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1{float:left;position:relative;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 > a{position:relative;display:block;width:<?=$menu1_wd?>px;height:<?=$menu1_ht?>px;line-height:<?=$menu1_ht?>px;text-align:center;background:<?=$menu1_bg_color?>;color:<?=$menu1_font_color?>;font-size:<?=$menu1_font_size?>px;font-weight:<?=$menu1_font_weight?>;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1:hover > a{background:<?=$menu1_bg_hover_color?>;color:<?=$menu1_font_hover_color?>;animation:<?=$bid?>_color1 .4s;-moz-animation:<?=$bid?>_color1 .4s;-webkit-animation:<?=$bid?>_color1 .4s;-o-animation:<?=$bid?>_color1 .4s;}


#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 > a > svg{position:absolute;top:<?=($n1_svg_y-5)?>px;right:10px;opacity:0;width:<?=$svg_down_wd?>px;height:auto;animation:<?=$bid?>_arrow1 2s infinite;-moz-animation:<?=$bid?>_arrow1 2s infinite;-webkit-animation:<?=$bid?>_arrow1 2s infinite;-o-animation:<?=$bid?>_arrow1 2s infinite;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 > a > svg line{stroke:<?=$menu1_icon_color?>;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1:hover > a > svg line{stroke:<?=$menu1_font_hover_color?>;}


<?php if($menu1_gubun == 'yes'){?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 > a line{position:absolute;height:<?=$menu1_gubun_ht?>px;left:0px;top:<?=(($menu1_ht-$menu1_gubun_ht)/2)?>px;border-left:1px solid <?=$menu1_gubun_color?>;}
<?php } ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2{display:none;position:absolute;width:<?=$menu2_wd?>px;left:0;top:<?=$menu1_ht?>px;}
<?php if($menu2_shadow == 'yes'){ ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2{box-shadow:<?=$menu2_shadow_x?>px <?=$menu2_shadow_y?>px <?=$menu2_shadow_size?>px <?=$menu2_shadow_color?>;}
<?php } ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1:hover .ul_nav2{display:block;animation:<?=$bid?>_fadein .5s;-moz-animation:<?=$bid?>_fadein .5s;-webkit-animation:<?=$bid?>_fadein .5s;-o-animation:<?=$bid?>_fadein .3s;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2{position:relative;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 > a{display:block;position:relative;text-align:<?=$menu2_align?>;padding:0 10px 0 <?=$menu2_indent?>px;border-top:1px solid <?=$menu2_line_color?>;background:<?=$menu2_bg_color?>;font-size:<?=$menu2_font_size?>px;font-weight:<?=$menu2_font_weight?>;color:<?=$menu2_font_color?>;height:<?=$menu2_ht?>px;line-height:<?=$menu2_ht?>px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2:first-child > a{border-top:0;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2:hover > a{background:<?=$menu2_bg_hover_color?>;color:<?=$menu2_font_hover_color?>;animation:<?=$bid?>_color2 .4s;-moz-animation:<?=$bid?>_color2 .4s;-webkit-animation:<?=$bid?>_color2 .4s;-o-animation:<?=$bid?>_color2 .4s;}


#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 > a > svg{position:absolute;top:<?=$n2_svg_y?>px;left:5px;right:auto;opacity:1;width:<?=$svg_side_wd?>px;height:auto;animation:<?=$bid?>_arrow2_to_right 1s infinite;-moz-animation:<?=$bid?>_arrow2_to_right 1s infinite;-webkit-animation:<?=$bid?>_arrow2_to_right 1s infinite;-o-animation:<?=$bid?>_arrow2_to_right 1s infinite;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2.last_li2 > a > svg{position:absolute;top:<?=$n2_svg_y?>px;left:auto;right:5px;animation:<?=$bid?>_arrow2_to_left 1s infinite;-moz-animation:<?=$bid?>_arrow2_to_left 1s infinite;-webkit-animation:<?=$bid?>_arrow2_to_left 1s infinite;-o-animation:<?=$bid?>_arrow2_to_left 1s infinite;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 > a > svg line{stroke:<?=$menu2_icon_color?>;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2:hover > a > svg line{stroke:<?=$menu2_font_hover_color?>;}


#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3{display:none;position:absolute;top:0px;left:<?=$menu2_wd?>px;width:<?=$menu3_wd?>px;}
<?php if($menu3_shadow == 'yes'){ ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3{box-shadow:<?=$menu3_shadow_x?>px <?=$menu3_shadow_y?>px <?=$menu3_shadow_size?>px <?=$menu3_shadow_color?>;}
<?php } ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3.last_nav3{left:<?=(0-$menu3_wd)?>px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2:hover .ul_nav3{display:block;animation:<?=$bid?>_fadein .5s;-moz-animation:<?=$bid?>_fadein .5s;-webkit-animation:<?=$bid?>_fadein .5s;-o-animation:<?=$bid?>_fadein .3s;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3 .li_nav3{width:100%;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3 .li_nav3 a{display:block;text-align:<?=$menu3_align?>;padding:0 10px 0 <?=$menu3_indent?>px;border-top:1px solid <?=$menu3_line_color?>;background:<?=$menu3_bg_color?>;font-size:<?=$menu3_font_size?>px;font-weight:<?=$menu3_font_weight?>;color:<?=$menu3_font_color?>;height:<?=$menu3_ht?>px;line-height:<?=$menu3_ht?>px;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3 .li_nav3:first-child a{border-top:0;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1 .ul_nav2 .li_nav2 .ul_nav3 .li_nav3:hover a{background:<?=$menu3_bg_hover_color?>;color:<?=$menu3_font_hover_color?>;animation:<?=$bid?>_color3 .4s;-moz-animation:<?=$bid?>_color3 .4s;-webkit-animation:<?=$bid?>_color3 .4s;-o-animation:<?=$bid?>_color3 .4s;}


<?php if($menu1_first == 'yes'){ ?>
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1.first_nav1{}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1.first_nav1 a{background:<?=$menu1_first_bg_color?>;color:<?=$menu1_first_font_color?>;}
#<?=$bid?> .logonav .logonav_in table td.td_nav .nav .ul_nav1 .li_nav1.first_nav1 a:hover{background:<?=$menu1_first_hover_bg_color?>;color:<?=$menu1_first_hover_font_color?>;animation:<?=$bid?>_first_color1 .4s;-moz-animation:<?=$bid?>_first_color1 .4s;-webkit-animation:<?=$bid?>_first_color1 .4s;-o-animation:<?=$bid?>_first_color1 .4s;}
<?php } ?>

/*==================1차메뉴 다운 화살표===============*/
@keyframes <?=$bid?>_arrow1{
	0%{top:<?=($n1_svg_y-5)?>px;opacity:0;}
	60%{top:<?=$n1_svg_y?>px;opacity:1;}
	100%{top:<?=$n1_svg_y?>px;opacity:0;}
}
@-moz-keyframes <?=$bid?>_arrow1{
	0%{top:<?=($n1_svg_y-5)?>px;opacity:0;}
	60%{top:<?=$n1_svg_y?>px;opacity:1;}
	100%{top:<?=$n1_svg_y?>px;opacity:0;}
}
@-webkit-keyframes <?=$bid?>_arrow1{
	0%{top:<?=($n1_svg_y-5)?>px;opacity:0;}
	60%{top:<?=$n1_svg_y?>px;opacity:1;}
	100%{top:<?=$n1_svg_y?>px;opacity:0;}
}
@-o-keyframes <?=$bid?>_arrow1{
	0%{top:<?=($n1_svg_y-5)?>px;opacity:0;}
	60%{top:<?=$n1_svg_y?>px;opacity:1;}
	100%{top:<?=$n1_svg_y?>px;opacity:0;}
}
/*==================2차메뉴 오른쪽으로 화살표===============*/
@keyframes <?=$bid?>_arrow2_to_right{
	0%{left:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{left:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{left:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-moz-keyframes <?=$bid?>_arrow2_to_right{
	0%{left:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{left:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{left:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-webkit-keyframes <?=$bid?>_arrow2_to_right{
	0%{left:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{left:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{left:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-o-keyframes <?=$bid?>_arrow2_to_right{
	0%{left:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{left:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{left:<?=($menu2_wd-10)?>px;opacity:0;}
}
/*==================2차메뉴 왼쪽으로 화살표===============*/
@keyframes <?=$bid?>_arrow2_to_left{
	0%{right:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{right:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{right:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-moz-keyframes <?=$bid?>_arrow2_to_left{
	0%{right:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{right:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{right:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-webkit-keyframes <?=$bid?>_arrow2_to_left{
	0%{right:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{right:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{right:<?=($menu2_wd-10)?>px;opacity:0;}
}
@-o-keyframes <?=$bid?>_arrow2_to_left{
	0%{right:<?=($menu2_wd-15)?>px;opacity:0;}
	60%{right:<?=($menu2_wd-10)?>px;opacity:1;}
	100%{right:<?=($menu2_wd-10)?>px;opacity:0;}
}
/*================== 페이드인 ====================*/
@keyframes <?=$bid?>_fadein{
	0%{opacity:0;}
	100%{opacity:1;}
}
@-moz-keyframes <?=$bid?>_fadein{
	0%{opacity:0;}
	100%{opacity:1;}
}
@-webkit-keyframes <?=$bid?>_fadein{
	0%{opacity:0;}
	100%{opacity:1;}
}
@-o-keyframes <?=$bid?>_fadein{
	0%{opacity:0;}
	100%{opacity:1;}
}
/*================== first - 1차메뉴 색상변환 ====================*/
@keyframes <?=$bid?>_first_color1{
	0%{background:<?=$menu1_first_bg_color?>;}
	100%{background:<?=$menu1_first_bg_hover_color?>;}
}
@-moz-keyframes <?=$bid?>_first_color1{
	0%{opacity:<?=$menu1_first_bg_color?>;}
	100%{opacity:<?=$menu1_first_bg_hover_color?>;}
}
@-webkit-keyframes <?=$bid?>_first_color1{
	0%{opacity:<?=$menu1_first_bg_color?>;}
	100%{opacity:<?=$menu1_first_bg_hover_color?>;}
}
@-o-keyframes <?=$bid?>_first_color1{
	0%{opacity:<?=$menu1_first_bg_color?>;}
	100%{opacity:<?=$menu1_first_bg_hover_color?>;}
}
/*================== 1차메뉴 색상변환 ====================*/
@keyframes <?=$bid?>_color1{
	0%{background:<?=$menu1_bg_color?>;}
	100%{background:<?=$menu1_bg_hover_color?>;}
}
@-moz-keyframes <?=$bid?>_color1{
	0%{opacity:<?=$menu1_bg_color?>;}
	100%{opacity:<?=$menu1_bg_hover_color?>;}
}
@-webkit-keyframes <?=$bid?>_color1{
	0%{opacity:<?=$menu1_bg_color?>;}
	100%{opacity:<?=$menu1_bg_hover_color?>;}
}
@-o-keyframes <?=$bid?>_color1{
	0%{opacity:<?=$menu1_bg_color?>;}
	100%{opacity:<?=$menu1_bg_hover_color?>;}
}
/*================== 2차메뉴 색상변환 ====================*/
@keyframes <?=$bid?>_color2{
	0%{background:<?=$menu2_bg_color?>;}
	100%{background:<?=$menu2_bg_hover_color?>;}
}
@-moz-keyframes <?=$bid?>_color2{
	0%{opacity:<?=$menu2_bg_color?>;}
	100%{opacity:<?=$menu2_bg_hover_color?>;}
}
@-webkit-keyframes <?=$bid?>_color2{
	0%{opacity:<?=$menu2_bg_color?>;}
	100%{opacity:<?=$menu2_bg_hover_color?>;}
}
@-o-keyframes <?=$bid?>_color2{
	0%{opacity:<?=$menu2_bg_color?>;}
	100%{opacity:<?=$menu2_bg_hover_color?>;}
}
/*================== 3차메뉴 색상변환 ====================*/
@keyframes <?=$bid?>_color3{
	0%{background:<?=$menu3_bg_color?>;}
	100%{background:<?=$menu3_bg_hover_color?>;}
}
@-moz-keyframes <?=$bid?>_color3{
	0%{opacity:<?=$menu3_bg_color?>;}
	100%{opacity:<?=$menu3_bg_hover_color?>;}
}
@-webkit-keyframes <?=$bid?>_color3{
	0%{opacity:<?=$menu3_bg_color?>;}
	100%{opacity:<?=$menu3_bg_hover_color?>;}
}
@-o-keyframes <?=$bid?>_color3{
	0%{opacity:<?=$menu3_bg_color?>;}
	100%{opacity:<?=$menu3_bg_hover_color?>;}
}
</style>