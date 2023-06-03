<?php
include_once('./_common.php');
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$mms_idx)
    alert('설비번호가 제대로 넘어오지 않았습니다.');

$g5['title'] = '40호기-QR코드';
include_once(G5_USER_PATH.'/_head.php');
$url = urlencode(G5_USER_URL.'/my_production.php?mms_idx=96');
?>
<div class="alert alert-dark" role="alert">
  40호기 QR코드
</div>
<div class="mx-auto" style="width: 400px;">
    <img src="https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=<?=$url?>" style="display:inline-block;"/>
</div>
<div class="" style="text-align:center;">
    <a href="<?=G5_USER_URL?>/my_production.php?mms_idx=96" target="_blank">바코드 페이지로 이동</a>
</div>
<?php
include_once(G5_USER_PATH.'/_tail.php');