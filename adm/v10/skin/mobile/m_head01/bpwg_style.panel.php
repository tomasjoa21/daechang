<?php
$pnl_close_algin = ($panel_align == 'left') ? 'right' : 'left';
$pnl_close_x = ($panel_top_height - $panel_top_icon_size) / 2;
$pnl_close_y = ($panel_top_height - $panel_top_icon_size) / 2;
$pnl_top_img_ht = $panel_top_height * 0.7;
$pnl_top_img_y = ($panel_top_height - $pnl_top_img_ht) / 2;
$pnl_top_td_padding = ($is_member) ? $pnl_top_img_ht+20 : 10;
?>
<style>
#<?=$pnl?>{position:fixed;left:0;top:0;bottom:0;width:100%;z-index:3000;overflow-x:hidden;display:none;}
#<?=$pnl?>.show{display:block;}
#<?=$pnl?> .pnl_bg{position:relative;0;width:100%;height:100%;background:<?=$panel_close_blind_color?>;z-index:0;}
#<?=$pnl?> .pnl_con{background:<?=$panel_basic_bg_color?>;position:absolute;<?=$panel_align?>:-1300px;top:0;height:100%;width:<?=$panel_width?><?=$panel_width_unit?>;border-<?=$pnl_close_algin?>:1px solid <?=$panel_basic_line_color?>;overflow-y:auto;}
<?php if($panel_width == 100 && $panel_width_unit == '%'){ ?>
#<?=$pnl?> .pnl_con{border-<?=$pnl_close_algin?>:0px solid <?=$panel_basic_line_color?>;}
<?php } ?>
/*로그인전*/
#<?=$pnl?> .pnl_con .pnl_top{position:relative;height:<?=$panel_top_height?>px;background:<?=$panel_top_bg_color?>;padding-<?=$pnl_close_algin?>:<?=$panel_top_height?>px;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_close{display:block;position:absolute;top:0;<?=$pnl_close_algin?>:0;width:<?=$panel_top_height?>px;height:<?=$panel_top_height?>px;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_close svg{position:absolute;<?=$pnl_close_algin?>:<?=$pnl_close_x?>px;top:<?=$pnl_close_y?>px;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con{width:100%;height:100%;color:<?=$panel_top_font_color?>;font-size:<?=$panel_top_font_size?>px;font-weight:500;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con .pnl_top_tbl{width:100%;height:100%;table-layout:fixed;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con .pnl_top_tbl .pnl_top_td{position:relative;vertical-align:middle;padding-<?=$panel_align?>:<?=$pnl_top_td_padding?>px;text-align:<?=$panel_align?>;text-overflow:ellipsis; overflow:hidden}
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con .pnl_top_tbl a{position:relative;color:<?=$panel_top_font_color?>;}
/*로그인후*/
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con .pnl_top_tbl .pnl_top_td span{position:absolute;top:<?=$pnl_top_img_y?>px;display:inline-block;<?=$panel_align?>:10px;width:<?=$pnl_top_img_ht?>px;height:<?=$pnl_top_img_ht?>px;border-radius:50%;overflow:hidden;}
#<?=$pnl?> .pnl_con .pnl_top .pnl_top_con .pnl_top_tbl .pnl_top_td span img{width:100%;height:100%;}
/*아이콘 영역*/
#<?=$pnl?> .pnl_con .ic_tbl{width:100%;border-collapse:collapse;border-spacing:0px;border:1px solid <?=$panel_grid_line_color?>;}
#<?=$pnl?> .pnl_con .ic_tbl td{width:33.333%;text-align:center;vertical-align:middle;border:1px solid <?=$panel_grid_line_color?>;overflow-x:hidden;padding:20px 0;background:<?=$panel_grid_bg_color?>;}
#<?=$pnl?> .pnl_con .ic_tbl td a{}
#<?=$pnl?> .pnl_con .ic_tbl td a span{font-size:<?=$panel_grid_font_size?>px;color:<?=$panel_grid_font_color?>;margin-top:5px;display:inline-block;}
/*메뉴 영역*/
/*----------------------1차메뉴----------------------*/
<?php
$svg1_pos = ($menu1_ht - $menu1_icon_size) / 2;
?>
#<?=$pnl?> .pnl_con .n1_ul{}
#<?=$pnl?> .pnl_con .n1_ul .n_svg{outline:none;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li{}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n1_dv{padding-left:10px;position:relative;height:<?=$menu1_ht?>px;line-height:<?=$menu1_ht?>px;background:<?=$menu1_bg_color?>;border-bottom:1px solid <?=$menu1_line_color?>;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n1_dv .n1_a{color:<?=$menu1_font_color?>;font-size:<?=$menu1_font_size?>px;font-weight:<?=$menu1_font_weight?>;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n1_dv .n1_btn{position:absolute;top:0;right:0;width:<?=$menu1_ht?>px;height:<?=$menu1_ht?>px;overflow:hidden;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n1_dv .n1_btn svg{position:absolute;left:<?=$svg1_pos?>px;top:<?=$svg1_pos?>px;}

/*----------------------2차메뉴----------------------*/
<?php
$svg2_pos = ($menu2_ht - $menu2_icon_size) / 2;
?>
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul{display:none;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul.show{display:block;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li{}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n2_dv{padding-left:<?=$menu2_indent?>px;position:relative;height:<?=$menu2_ht?>px;line-height:<?=$menu2_ht?>px;background:<?=$menu2_bg_color?>;border-bottom:1px solid <?=$menu2_line_color?>;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n2_dv .n2_a{color:<?=$menu2_font_color?>;font-size:<?=$menu2_font_size?>px;font-weight:<?=$menu2_font_weight?>;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n2_dv .n2_btn{position:absolute;top:0;right:0;width:<?=$menu2_ht?>px;height:<?=$menu2_ht?>px;overflow:hidden;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n2_dv .n2_btn svg{position:absolute;left:<?=$svg2_pos?>px;top:<?=$svg2_pos?>px;}

/*----------------------3차메뉴----------------------*/
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n3_ul{display:none;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n3_ul.show{display:block;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n3_ul .n3_li{}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n3_ul .n3_li .n3_dv{padding-left:<?=$menu3_indent?>px;position:relative;height:<?=$menu3_ht?>px;line-height:<?=$menu3_ht?>px;background:<?=$menu3_bg_color?>;border-bottom:1px solid <?=$menu3_line_color?>;}
#<?=$pnl?> .pnl_con .n1_ul .n1_li .n2_ul .n2_li .n3_ul .n3_li .n3_dv .n3_a{color:<?=$menu3_font_color?>;font-size:<?=$menu3_font_size?>px;font-weight:<?=$menu3_font_weight?>;}
/*------------------ 판넬 푸터 정보 --------------------*/
#<?=$pnl?> .pnl_con .pnl_ft_info{text-align:center;padding:<?=$ft_top_interval?>px 10px <?=$ft_bottom_interval?>px;color:<?=$ft_font_color?>;font-size:<?=$ft_font_size?>px;}
#<?=$pnl?> .pnl_con .pnl_ft_info .p_btn_group{padding-bottom:10px;}
#<?=$pnl?> .pnl_con .pnl_ft_info .p_btn_group a.btn_pv{display:inline-block;padding:5px 10px;border:1px solid <?=$ft_font_color?>;color:<?=$ft_font_color?>;}
#<?=$pnl?> .pnl_con .pnl_ft_info .p_group p{margin-top:<?=$ft_line_interval?>px;}
#<?=$pnl?> .pnl_con .pnl_ft_info a{color:<?=$ft_font_color?>;}
</style>