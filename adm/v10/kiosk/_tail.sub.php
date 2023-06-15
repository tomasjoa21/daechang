<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<?php include_once('./modal/_mdl_area.php'); ?>
<div id="loading_box" class=""><img src="<?=G5_USER_ADMIN_KIOSK_IMG_URL?>/loading.gif"></div>
<script src="<?php echo G5_USER_ADMIN_KIOSK_JS_URL ?>/admin.js?ver=<?php echo G5_JS_VER; ?>"></script>
</body>
</html>
<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다.