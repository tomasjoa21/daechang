<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
run_event('pre_head');

include_once(G5_USER_PATH.'/_head.sub.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
?>
<!-- 상단 시작 { -->
<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <?=svg_icon_user('3bars','top_bars',40,40,'#ffffff')?>
    <a id="hd_logo" class="navbar-brand" href="<?=G5_USER_URL?>">
      <strong>EPCS</strong>
    </a>
    <ul>
      <li><?=svg_icon_user('search','top_search',34,34,'#ffffff')?></li>
      <li><?=svg_icon_user('bell','top_bell',34,34,'#ffffff')?></li>
      <li><?=svg_icon_user('person','top_person',34,34,'#ffffff')?></li>
    </ul>
  </div>
</nav>
<!-- } 상단 끝 -->
<!-- 콘텐츠 시작 { -->
<div id="wrapper">
    <div id="container_wr">
        <div id="container">
            <?php if (!defined("_INDEX_")) { ?><h2 id="container_title"><span title="<?php echo get_text($g5['title']); ?>"><?php echo get_head_title($g5['title']); ?></span></h2><?php }