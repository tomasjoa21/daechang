<?php
$nv_btn_y = ($head_height - $menu_icon_size) / 2;
$lg_btn_y = ($head_height - $logo_height) / 2;
$sh_btn_y = ($head_height - $sch_open_icon_size) / 2;
$sch_close_icon_align = ($sch_icon_align == 'left') ? 'right' : 'left';
$sh_smt_y = ($head_height - $sch_icon_size) / 2;
$sh_smt_x = ($head_height - $sch_icon_size) / 2;
$sh_cls_y = ($head_height - $sch_close_icon_size) / 2;
$sh_cls_x = ($head_height - $sch_close_icon_size) / 2;
$sh_txt_y = ($head_height - $sch_icon_size) / 2;
?>
<style>
@charset "utf-8";
#<?=$bid?> .hd_area{position:relative;text-align:center;overflow:hidden;height:<?=$head_height?>px;background:<?=$head_bg_color?>;border-bottom:1px solid <?=$head_line_color?>;}
#<?=$bid?> .hd_area .nav_btn{position:absolute;<?=$menu_icon_align?>:<?=$menu_icon_x?>px;top:<?=$nv_btn_y?>px;z-index:1;}
#<?=$bid?> .hd_area #<?=$lgid?>{position:absolute;left:0px;top:<?=($lg_btn_y-1)?>px;width:100% !important;text-align:center;z-index:0;}
#<?=$bid?> .hd_area #<?=$lgid?> img,#<?=$bid?> .hd_area #<?=$lgid?> svg{height:<?=$logo_height?>px;width:auto;}
#<?=$bid?> .hd_area .sch_btn{position:absolute;<?=$sch_open_icon_align?>:<?=$sch_open_icon_x?>px;top:<?=$sh_btn_y?>px;z-index:3;}
#<?=$bid?> .hd_area .sch{position:absolute;left:0;top:0;width:100%;height:100%;z-index:4;background:<?=$sch_bg_color?>;padding:0 <?=$head_height?>px;display:none;}
#<?=$bid?> .hd_area .sch.show{display:block;}
#<?=$bid?> .hd_area .sch #sch_str,#<?=$bid?> .hd_area .sch #sch_stx{position:relative;top:<?=$sh_txt_y?>px;width:100%;height:<?=$sch_icon_size?>px;line-height:<?=$sch_icon_size?>px;font-size:<?=$sch_font_size?>px;color:<?=$sch_font_color?>;border-bottom:1px solid <?=$sch_line_color?>;padding:0 10px;outline:none !important;}
#<?=$bid?> .hd_area .sch #sch_str::-webkit-input-placeholder,#<?=$bid?> .hd_area .sch #sch_stx::-webkit-input-placeholder{color:<?=$sch_font_color?>;}
#<?=$bid?> .hd_area .sch #sch_str::-moz-input-placeholder,#<?=$bid?> .hd_area .sch #sch_stx::-moz-input-placeholder{color:<?=$sch_font_color?>;}
#<?=$bid?> .hd_area .sch #sch_str::-ms-input-placeholder,#<?=$bid?> .hd_area .sch #sch_stx::-ms-input-placeholder{color:<?=$sch_font_color?>;}
#<?=$bid?> .hd_area .sch #sch_str::-o-input-placeholder,#<?=$bid?> .hd_area .sch #sch_stx::-o-input-placeholder{color:<?=$sch_font_color?>;}
#<?=$bid?> .hd_area .sch #sch_str:focus,#<?=$bid?> .hd_area .sch #sch_stx:focus{outline:none !important;}
#<?=$bid?> .hd_area .sch #sch_submit{position:absolute;<?=$sch_icon_align?>:0;top:0;width:<?=$head_height?>px;height:<?=$head_height?>px;overflow:hidden;}
#<?=$bid?> .hd_area .sch #sch_submit svg{position:absolute;top:<?=$sh_smt_y?>px;<?=$sch_icon_align?>:<?=$sh_smt_x?>px;}
#<?=$bid?> .hd_area .sch .sch_close{display:block;position:absolute;<?=$sch_close_icon_align?>:0;top:0;width:<?=$head_height?>px;height:<?=$head_height?>px;overflow:hidden;}
#<?=$bid?> .hd_area .sch .sch_close svg{position:absolute;top:<?=$sh_cls_y?>px;<?=$sch_close_icon_align?>:<?=$sh_cls_x?>px;}
</style>