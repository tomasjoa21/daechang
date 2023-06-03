<?php
$pnl_width = 200;
$layout_padding = 10;
$inn_width = $pnl_width - ($layout_padding * 2);
?>
<style>
@charset "utf-8";
#<?=$bid?> input[type="text"],#<?=$bid?> input[type="password"],#<?=$bid?> input[type="url"],#<?=$bid?> input[type="number"]{
	display:inline-block;height:26px;line-height:26px;position:relative;border:1px solid #e1e1e1;padding:0 3px;border-radius:3px;
}

#<?=$bid?> .label_checkbox{display:inline-block;cursor:pointer;margin-left:7px;position:relative;top:-2px;}
#<?=$bid?> .label_checkbox.first_child{margin-left:0;}
#<?=$bid?> .label_checkbox:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .label_checkbox input{display:none;}
#<?=$bid?> .label_checkbox strong{display:block;width:16px;height:16px;background:url(<?=G5_BPWIDGET_IMG_URL?>/c_off.png) no-repeat center center;background-size:100% 100%;float:left;}
#<?=$bid?> .label_checkbox input:checked + strong{width:16px;height:16px;background:url(<?=G5_BPWIDGET_IMG_URL?>/c_on.png) no-repeat center center;background-size:100% 100%;}
#<?=$bid?> .label_checkbox span{display:block;float:right;height:16px;line-height:16px;padding-left:3px;font-size:1.1em;color:#777;}
#<?=$bid?> .label_checkbox input:checked + strong + span{color:#000;}

#<?=$bid?>_bg{position:fixed;left:0;top:0;width:100%;height:100%;background:<?=$blind_bg_color?>;z-index:1900;display:none;}
#<?=$bid?>_bg.show{display:block;}
#<?=$bid?>{position:fixed;right:-200px;transition:right .2s ease;top:0;bottom:0;width:200px;z-index:2000;background:<?=$basic_color?>;box-shadow:none;}
#<?=$bid?> .bpwg_btn_admin{}
#<?=$bid?>.show{right:0px;transition:right .2s ease;box-shadow:0px 0px 5px <?=$shadow_color?>;}
#<?=$bid?> .open_toggle{position:absolute;background:<?=$toggle_bg_color?>;box-shadow:-3px 0 3px <?=$shadow_color?>;width:30px;height:46px;top:50%;margin-top:-23px;left:-30px;border-top-left-radius:10px;border-bottom-left-radius:10px;cursor:pointer;}
#<?=$bid?> .open_toggle svg{position:relative;}

/*퀙 타이틀 영역*/
#<?=$bid?> .sd_ttl{text-align:center;background:<?=$ttl_bg?>;padding:10px 0;}
#<?=$bid?> .sd_ttl p{}
#<?=$bid?> .sd_ttl p span{color:<?=$ttl_small_font?>;border-bottom:1px solid <?=$ttl_gubunline?>;display:inline-block;padding-bottom:3px;}
#<?=$bid?> .sd_ttl h3{color:<?=$ttl_big_font?>;padding-top:0px;font-size:22px;}

/*로그[인]아웃*/
#<?=$bid?> .ol{padding:10px;}
#<?=$bid?> .ol input[type='text'],#<?=$bid?> .ol input[type='password']{width:100%;color:<?=$login_input_font?>;background:<?=$login_input_bg?>;border:1px solid <?=$login_input_line?>;}
#<?=$bid?> .ol input[type='password']{margin-top:5px;}
#<?=$bid?> .ol #ol_svc{margin-top:5px;}
#<?=$bid?> .ol #ol_svc:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .ol #ol_svc .ol_btn_login{float:left;width:100%;background:none;text-align:center;border:0;height:26px;line-height:26px;border-radius:3px;cursor:pointer;color:<?=$login_font?>;background:<?=$login_bg?>;}
#<?=$bid?> .ol #ol_svc .ol_btn_login:hover{color:<?=$login_hover_font?>;background:<?=$login_hover_bg?>;}
#<?=$bid?> .ol #ol_svc .ol_btn{display:block;float:left;background:none;width:48.5%;height:26px;line-height:26px;text-align:center;margin-top:5px;border-radius:3px;}
#<?=$bid?> .ol #ol_svc .join{margin-right:3%;}
#<?=$bid?> .ol #ol_svc .joinfind{color:<?=$regfind_font?>;background:<?=$regfind_bg?>;}
#<?=$bid?> .ol #ol_svc .joinfind:hover{color:<?=$regfind_hover_font?>;background:<?=$regfind_hover_bg?>;}
#<?=$bid?> .ol #ol_auto{margin-top:5px;}
/*로그인[아웃]*/
#<?=$bid?> .ol #ol_after_hd{padding:5px 0 10px;}
#<?=$bid?> .ol #ol_after_hd table{width:100%;}
#<?=$bid?> .ol #ol_after_hd table td.td_first{width:60px;}
#<?=$bid?> .ol #ol_after_hd table td{}
#<?=$bid?> .ol #ol_after_hd .profile_img{position:relative;display:inline-block;}
#<?=$bid?> .ol #ol_after_hd .profile_img img{border-radius:50%;overflow:hidden;}
#<?=$bid?> .ol #ol_after_hd .profile_img a{position:absolute;bottom:0px;right:0px;color:#888;}
#<?=$bid?> .ol #ol_after_hd strong{margin-left:5px;}
#<?=$bid?> .ol #ol_after_hd span{color:#888;}
#<?=$bid?> .ol .mb_btn_box:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .ol .mb_btn_box .ol_btn{display:block;float:left;background:none;width:48.5%;height:26px;line-height:26px;text-align:center;border-radius:3px;}
#<?=$bid?> .ol .mb_btn_box .btn_logout{color:<?=$logout_font?>;background:<?=$logout_bg?>;}
#<?=$bid?> .ol .mb_btn_box .btn_logout:hover{color:<?=$logout_hover_font?>;background:<?=$logout_hover_bg?>;}
#<?=$bid?> .ol .mb_btn_box .btn_infomodify{color:<?=$infomf_font?>;background:<?=$infomf_bg?>;}
#<?=$bid?> .ol .mb_btn_box .btn_infomodify:hover{color:<?=$infomf_hover_font?>;background:<?=$infomf_hover_bg?>;}
#<?=$bid?> .ol .mb_btn_box #s_ol_after_info{margin-left:2%;}
#<?=$bid?> .ol #ol_after_private{margin-top:10px;border-left:1px solid <?=$basic_color?>;border-top:1px solid <?=$basic_color?>;}
#<?=$bid?> .ol #ol_after_private:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .ol #ol_after_private li{float:left;width:50%;border-right:1px solid <?=$basic_color?>;border-bottom:1px solid <?=$basic_color?>;padding:6px 4px;background:<?=$grid_bg?>;}
#<?=$bid?> .ol #ol_after_private li:hover{background:<?=$grid_hover_bg?>;}
#<?=$bid?> .ol #ol_after_private li a{color:<?=$grid_font?>;display:block;text-align:center;font-size:0.8em;} 
#<?=$bid?> .ol #ol_after_private li:hover a{color:<?=$grid_hover_font?>;}
#<?=$bid?> .ol #ol_after_private li a:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> .ol #ol_after_private li a .sp_ttl{display:block;float:left;}
#<?=$bid?> .ol #ol_after_private li a strong{display:block;float:right;}
/*qick_list*/
#<?=$bid?> #ul_qck{padding:0 10px 10px;} 
#<?=$bid?> #ul_qck .li_qck{border-top:1px solid <?=$basic_color?>;} 
#<?=$bid?> #ul_qck .li_qck:first-child{border-top:0;} 
#<?=$bid?> #ul_qck .li_qck .bt_qck{background:<?=$accordion_bg?>;color:<?=$accordion_font?>;width:100%;text-align:left;padding:10px;} 
#<?=$bid?> #ul_qck .li_qck .bt_qck:hover{background:<?=$accordion_hover_bg?>;color:<?=$accordion_hover_font?>;}
#<?=$bid?> #ul_qck .li_qck .bt_qck:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> #ul_qck .li_qck .bt_qck span{margin-left:5px;position:relative;top:0px;color:<?=$accordion_font?>;}
#<?=$bid?> #ul_qck .li_qck .bt_qck:hover span{color:<?=$accordion_hover_font?>;}
#<?=$bid?> #ul_qck .li_qck .bt_qck svg{float:right;}
#<?=$bid?> #ul_qck .li_qck .qck_pg{display:none;}
#<?=$bid?> #ul_qck .li_qck .qck_pg.show{display:block;max-height:200px;}
/* 오늘 본 상품 */
#<?=$bid?> #stv2 {position:relative;height:100%;}
#<?=$bid?> #stv2 .li_empty {text-align:center;line-height:100px;background:#efefef;}
#<?=$bid?> .stv_item2 {display:none;padding:8px 0;word-break:break-all;border-bottom:1px solid #f6f6f6}
#<?=$bid?> .stv_item2 .prd_img {text-align:center;}
#<?=$bid?> .stv_item2 .prd_cnt {}
#<?=$bid?> .stv_item2 .prd_cnt span {display:block;max-width:105px}
#<?=$bid?> .stv_item2 .prd_cnt .prd_name {font-weight:bold;margin-bottom:3px;padding-right:5px;}
#<?=$bid?> #stv_bottom{position:relative;}
#<?=$bid?> #stv_bottom #stv_btn2 {position:absolute;top:0;left:0;width:100%;}
#<?=$bid?> #stv_bottom #prev2 {position:absolute;top:5px;left:15px;}
#<?=$bid?> #stv_bottom #next2 {position:absolute;top:5px;right:15px;}
#<?=$bid?> #stv_bottom #stv_pg2 {display:block;text-align:center;height:40px;padding-top:6px;background:#fff;}
/*장바구니*/
#<?=$bid?> #sbsk2 {position:relative;height:100%;}
#<?=$bid?> #sbsk2 ul{}
#<?=$bid?> #sbsk2 ul li {position:relative;border-bottom:1px solid #f6f6f6;}
#<?=$bid?> #sbsk2 ul li:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> #sbsk2 ul li > div{float:left;}
#<?=$bid?> #sbsk2 .li_empty {text-align:center;line-height:100px;background:#efefef;}
#<?=$bid?> #sbsk2 .btn_buy{padding:5px 0 10px;}
#<?=$bid?> #sbsk2 .btn_buy:after {display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> #sbsk2 .btn_buy .go{background:#e1e1e1;text-align:center;padding:5px 0;}
#<?=$bid?> #sbsk2 .btn_buy .go_half{float:left;width:49%;}
#<?=$bid?> #sbsk2 .btn_buy .go_buy{}
#<?=$bid?> #sbsk2 .btn_buy .go_cart{margin-left:2%;}
#<?=$bid?> #sbsk2 .btn_buy .go_all{display:block;width:100%;}
#<?=$bid?> #sbsk2 ul .prd_img {padding:5px 0;width:40%;padding-right:5px;}
#<?=$bid?> #sbsk2 ul .prd_img img{width:100%;}
#<?=$bid?> #sbsk2 ul .prd_cnt {padding:5px 0;width:60%;height:100%;}
#<?=$bid?> #sbsk2 ul .prd_cnt .prd_dv{}
#<?=$bid?> #sbsk2 ul .prd_cnt .prd_dv a, #sbsk .prd_cnt .prd_dv span {display:block;}
#<?=$bid?> #sbsk2 ul .prd_cnt .prd_dv .prd_name {padding-right:5px;font-weight:bold;margin-bottom:3px;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;word-wrap:break-word;line-height:1.2em;height: 2.4em;}
#<?=$bid?> #sbsk2 ul .prd_cnt .prd_dv .cart_del {border:0;width:25px;height:25px;text-align:center;position:absolute;right:5px;bottom:5px;color:#c5c8ca;background:#fff;font-size:1.25em}
/*위시리스트*/
#<?=$bid?> #wish2 {position:relative;height:100%;border-bottom:1px solid #eee;}	
#<?=$bid?> #wish2 ul{}
#<?=$bid?> #wish2 ul li{position:relative;border-bottom:1px solid #f6f6f6;}
#<?=$bid?> #wish2 ul li:after{display:block;visibility:hidden;clear:both;content:"";}
#<?=$bid?> #wish2 ul .prd_img {float:left;padding:5px 0;width:40%;padding-right:5px;}
#<?=$bid?> #wish2 ul .prd_img img{width:100%;}
#<?=$bid?> #wish2 ul .prd_cnt {float:left;padding:5px 0;width:60%;height:100%;}
#<?=$bid?> #wish2 ul .prd_cnt a {display:block;}
#<?=$bid?> #wish2 ul .prd_cnt .prd_name {padding-right:5px;font-weight:bold;margin-bottom:3px;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;word-wrap:break-word;line-height:1.2em;height: 2.4em;}
#<?=$bid?> #wish2 .li_empty {text-align:center;line-height:100px;background:#efefef;}
</style>
