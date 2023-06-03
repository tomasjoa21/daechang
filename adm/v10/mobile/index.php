<?php
include_once('./_common.php');
include_once('./_head.php');
?>
<style>
#main #box{
    grid-template-columns:repeat(<?=(count($menu_datas) % 2)?>, 1fr);
}
</style>
<div id="main">
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