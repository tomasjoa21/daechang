<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

?>
        </div><!-- //#container -->
    </div><!-- //#container_wr -->
</div><!-- //#wrapper -->
<!-- } 콘텐츠 끝 -->
<!-- 하단 시작 { -->
<div id="ft">
    <div id="ft_copy">Copyright &copy; <b>daechang.epcs.co.kr.</b> All rights reserved.</div>

    <button type="button" id="top_btn">
    	<i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span>
    </button>
    <script>
    $(function() {
        $("#top_btn").on("click", function() {
            $("html, body").animate({scrollTop:0}, '500');
            return false;
        });
    });
    </script>
</div>

<?php
if(G5_DEVICE_BUTTON_DISPLAY && !G5_IS_MOBILE) { ?>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<!-- } 하단 끝 -->

<script>
$(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_USER_PATH."/_tail.sub.php");