<?php
$escro_horizon_margin = 'margin';
if($escro_align == 'left'){
	$escro_horizon_margin = 'margin-right';
}else if($escro_align == 'right'){
	$escro_horizon_margin = 'margin-left';
}
?>
<style>
@charset "utf-8";
#<?=$bid?>{position:relative;background:<?=$tail_bg_color?>;color:<?=$tail_font_color?>;font-size:<?=$tail_font_size?>px;}
#<?=$bid?> .bpwg_btn_admin{position:absolute;top:5px;left:5px;}

#<?=$bid?> .tail{border-top:1px solid <?=$tail_topline_color?>;}
#<?=$bid?> .tail .tail_in{width:<?=$tail_default_wd?>px;margin:0 auto;}
#<?=$bid?> .tail .tail_in table{width:100%;}
#<?=$bid?> .tail .tail_in table th{font-size:<?=$title_font_size?>px;color:<?=$tail_titlefont_color?>;padding:20px 0;}
#<?=$bid?> .tail .tail_in table td{vertical-align:top;text-align:center;}
#<?=$bid?> .tail .tail_in table td.td_left{text-align:left;}
#<?=$bid?> .tail .tail_in table td.td1{padding:10px 0 40px;}
#<?=$bid?> .tail .tail_in table td.td1 strong{color:<?=$tail_cstelfont_color?>;font-size:<?=$title_cstelfont_size?>px;}
#<?=$bid?> .tail .tail_in table td.td1 div.info{width:100%;height:60px;background:none;color:<?=$tail_titlefont_color?>;white-space:pre-line;}
#<?=$bid?> .tail .tail_in table td.td2{padding-left:180px;}
#<?=$bid?> .tail .tail_in table td.td2 strong{margin-left:10px;}
#<?=$bid?> .tail .tail_in table td.td2 span{margin-left:3px;}
#<?=$bid?> .tail .tail_in table td.td2 strong.strong_first{margin-left:0;}
#<?=$bid?> .tail .tail_in table td.td3{white-space:pre-line;}

#<?=$bid?> .copy{border-top:1px solid <?=$tail_copyrightline_color?>;}
#<?=$bid?> .copy .copy_in a{color:<?=$tail_font_color?>;font-size:<?=$tail_font_size?>px;}
#<?=$bid?> .copy .copy_in{position:relative;width:<?=$tail_default_wd?>px;margin:0 auto;text-align:center;padding:20px 0;}
#<?=$bid?> .copy .copy_in .com_link{line-height:26px;}
#<?=$bid?> .copy .copy_in .copyright{}
#<?=$bid?> .copy .copy_in .dv_escro{position:absolute;bottom:0;<?=$escro_align?>:0;padding-bottom:<?=$escro_bottom?>px;}
#<?=$bid?> .copy .copy_in .dv_escro form{position:absolute;}
#<?=$bid?> .copy .copy_in .dv_escro a{<?=$escro_horizon_margin?>:<?=$escro_margin?>px;}
#<?=$bid?> .copy .copy_in .dv_escro a img{height:<?=$escro_height?>px;width:auto;}
#<?=$bid?> .ic_move{position:fixed;<?=$scroll_align?>:10px;bottom:10px;z-index:2100;background:rgba(255,255,255,0.8);border-radius:13px;overflow:hidden;box-shadow:0 0 5px #cccccc;}
#<?=$bid?> .ic_move li{cursor:pointer;padding:3px 4px;}
#<?=$bid?> .mobile_mode{position:fixed;<?=$mobile_align?>:10px;bottom:10px;z-index:1800;}
</style>