<style>
@charset "utf-8";
#<?=$bid?>{position:relative;}
#<?=$bid?> #<?=$adid?>{}
#<?=$bid?> .dv_slk{min-width:<?=$mbnr_default_wd?>px;height:<?=$sld_ht?>px;}
#<?=$bid?> .dv_slk .slick-list{height:100%;}
#<?=$bid?> .dv_slk .slick-list .slick-track{height:100%;}
#<?=$bid?> .dv_slk .slick-list .slick-track .slick-slide{margin-bottom:-4px;height:100%;}
#<?=$bid?> .dv_slk .slick-list .slick-track .slick-slide > div{height:100%;}
#<?=$bid?> .dv_slk .dv_sld{height:100%;position:relative;background-repeat:no-repeat;background-position:center center;background-size:cover;}
#<?=$bid?> .dv_slk .dv_sld .inline-YTPlayer{height:100%;width:100% !important;max-width:none !important;}
#<?=$bid?> .dv_slk .dv_sld .inline-YTPlayer .inlinePlayButton{display:none !important;}
#<?=$bid?> .dv_slk .dv_sld .sld_pic{height:100% !important;}
#<?=$bid?> .dv_slk .dv_sld .sld_img{background-repeat:no-repeat;background-position:center center;background-size:cover;}
#<?=$bid?> .dv_slk .dv_sld .dv_blind{display:no ne;position:absolute;top:0;left:0;width:100%;height:100%;z-index:2;background:<?=$bgBlind?>;}
#<?=$bid?> .dv_slk .dv_sld .dv_con{position:absolute;left:0;right:0;margin:auto;top:0;bottom:0;width:<?=$mbnr_default_wd?>px;z-index:3;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl{display:table;width:100%;height:100%;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td{display:table-cell;vertical-align:middle;text-align:center;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text{display:none;color:#fff;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text a{color:#fff;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text .txt1{color:<?=$text1_color?>;font-size:<?=$text1_font_size?>px;font-weight:bold;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text .txt2{color:<?=$text2_color?>;font-size:<?=$text2_font_size?>px;margin-top:<?=$text2_margin_top?>px;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text .txt3{color:<?=$text3_color?>;font-size:<?=$text3_font_size?>px;margin-top:<?=$text3_margin_top?>px;}
#<?=$bid?> .dv_slk .dv_sld .dv_con .dv_tbl .dv_td .dv_text .txt4{color:<?=$text4_color?>;font-size:<?=$text4_font_size?>px;margin-top:<?=$text4_margin_top?>px;}

#<?=$bid?> .dv_slk .slick-arrow{z-index:50;width:30px;height:30px;}
#<?=$bid?> .dv_slk .slick-arrow::before{color:<?=$arrow_color?>;font-size:30px;}
#<?=$bid?> .dv_slk .slick-arrow:hover::before{color:<?=$arrow_hover_color?>;}
#<?=$bid?> .dv_slk .slick-prev{left:30px;}
#<?=$bid?> .dv_slk .slick-next{right:30px;}
#<?=$bid?> .dv_slk .slick-dots{bottom:20px;}
#<?=$bid?> .dv_slk .slick-dots li{width:16px;height:16px;margin:0 7px;transition:width .4s ease-in-out;}
#<?=$bid?> .dv_slk .slick-dots li.slick-active{width:42px;}
#<?=$bid?> .dv_slk .slick-dots li button{transition:width .4s ease-in-out;border:3px solid <?=$dot_color?>;border-radius:8px;width:16px;height:16px;text-indent:-99999px;}
#<?=$bid?> .dv_slk .slick-dots li:hover button{border:3px solid <?=$dot_active_color?>;background:<?=$dot_color?>;}
#<?=$bid?> .dv_slk .slick-dots li.slick-active button{width:42px;border:3px solid <?=$dot_active_color?>;background:<?=$dot_active_color?>;}
#<?=$bid?> .dv_slk.slick-dotted.slick-slider{margin-bottom:0;}
</style>