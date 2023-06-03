<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="<?=G5_URL?>/_make_data/_css/common.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<style>
ul{list-style:none;padding:0;margin:0;}
#nav{background:#555;padding:0 10px;}
#nav:after{display:block;visibility:hidden;clear:both;content:'';}
#nav li.nav_adm a{background:blue;}
#nav li{float:left;padding-right:10px;}
#nav li a{display:block;padding:10px;text-decoration:none;}
#nav li a.focus{background:brown;}
</style>
<ul id="nav">
    <li class="nav_li nav_adm"><a href="<?php echo G5_USER_ADMIN_URL ?>" class="">관리자홈</a></li>
    <li class="nav_li nav_home"><a href="<?php echo G5_URL ?>/_make_data" class="<?=($g5['dir_name'] == '_make_data')?' focus':''?>">MakeData홈</a></li>
    <li class="nav_li"><a href="<?php echo G5_URL ?>/_make_data/mms_qrcode/mms_qrcode.php" class="<?=($g5['dir_name'] == 'mms_qrcode')?' focus':''?>">설비QR코드</a></li>
    <li class="nav_li"><a href="<?php echo G5_URL ?>/_make_data/data_mtr/mtr_add.php" class="<?=($g5['dir_name'] == 'data_mtr' && $g5['file_name'] == 'mtr_add')?' focus':''?>">자재등록</a></li>
    <li class="nav_li"><a href="<?php echo G5_URL ?>/_make_data/data_item/item_sum.php" class="<?=($g5['dir_name'] == 'data_item' && $g5['file_name'] == 'item_sum')?' focus':''?>">재고등록</a></li>
    <li class="nav_li"><a href="<?php echo G5_URL ?>/_make_data/data_pallet/pallet.php" class="<?=($g5['dir_name'] == 'data_pallet')?' focus':''?>">파렛트등록</a></li>
</ul>