<style>
@charset "utf-8";

#<?=$bid?>{}
#<?=$bid?> *{box-sizing:border-box;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;-o-box-sizing:border-box;}
#<?=$bid?> .lt_box{}
#<?=$bid?> .lt_box h3{position:relative;padding:5px;border-bottom:2px solid <?=$title_line_color?>;}
#<?=$bid?> .lt_box h3:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .lt_box h3 a{font-size:<?=$title_font_size?>px;font-weight:<?=$title_font_weight?>;}
#<?=$bid?> .lt_box h3 a span{float:right;}

#<?=$bid?> .lt_box .lt_con{height:<?=$lst_height?>px;background:<?=$lst_bg_color?>;border-bottom:1px solid <?=$lst_line_color?>;}
#<?=$bid?> .lt_box .lt_con .lt_list{}
#<?=$bid?> .lt_box .lt_con .lt_empty{display:table;width:100%;height:100%;}
#<?=$bid?> .lt_box .lt_con .lt_empty .lt_empty_con{display:table-cell;text-align:center;vertical-align:middle;}
</style>