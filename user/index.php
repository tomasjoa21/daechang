<?php
include_once('./_common.php');
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['title'] = '대창사용자메인';
include_once(G5_USER_PATH.'/_head.php');
?>
<div class="d-grid gap-2 col-6 mx-auto">
  <a href="" class="btn btn-primary" type="button">로그인</a>
  <a href="<?=G5_USER_URL?>/mms_barcode.php?mms_idx=96" class="btn btn-secondary" type="button">40호기바코드</a>
  <a href="<?=G5_USER_URL?>/my_production.php?mms_idx=96" class="btn btn-success" type="button">40호기-나의 생산계획</a>
</div>

<?php
include_once(G5_USER_PATH.'/_tail.php');