<?php
include_once('./_common.php');
if(!defined('_GNUBOARD_')) exit;//개별 페이지 접근 불가
include_once('./_head.php');
?>
<style>
  
</style>
<div id="main" class="<?=$main_type_class?>">
    <div id="box">
        <?php foreach($menu_datas as $row){ ?>
        <div class="menu">
            <a href="<?=$row['me_link']?>">
            <i class="fa <?=$row['me_icon']?>" aria-hidden="true"></i>
            </a>
            <div><a href="<?=$row['me_link']?>"><?=$row['me_name']?></a></div>
        </div>
        <?php } ?>
    </div>
</div>
<?php
include_once('./_tail.php');