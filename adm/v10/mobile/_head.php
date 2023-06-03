<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once('./_head.sub.php');
$menu_datas = array(
    // array(
    //     'me_name' => '배차정보'
    //     , 'me_link' => G5_USER_ADMIN_MOBILE_URL.'/shipment_list.php'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-truck'
    // ),
    array(
        'me_name' => '생산작업'
        , 'me_link' => G5_USER_ADMIN_MOBILE_URL.'/production_list.php'
        , 'me_target' => '_self'
        , 'me_icon' => 'fa-industry'
     )//,
    // array(
    //     'me_name' => '빠레트정보'
    //     , 'me_link' => G5_USER_ADMIN_MOBILE_URL.'/pallet_list.php'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-qrcode'
    // ),
    // array(
    //     'me_name' => '수입검사'
    //     , 'me_link' => G5_USER_ADMIN_MOBILE_URL.'/input_check_list.php'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-stethoscope'
    // )
);
?>
<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div class="to_content"><a href="#container">본문 바로가기</a></div>

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?php echo G5_USER_ADMIN_MOBILE_URL ?>"><img src="<?php echo G5_USER_ADMIN_MOBILE_IMG_URL ?>/logo_bright4.png" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

        <button type="button" id="gnb_open" class="hd_opener"><i class="fa fa-bars" aria-hidden="true"></i><span class="sound_only"> 메뉴열기</span></button>

        <div id="gnb" class="hd_div">
            <button type="button" id="gnb_close" class="hd_closer"><span class="sound_only">메뉴 닫기</span><i class="fa fa-times" aria-hidden="true"></i></button>
            <ul id="gnb_1dul">
            <?php if(count($menu_datas) && $is_member){ ?>
            <li class="gnb_1dli gnb_ttl">
                <?=$member['mb_name']?>님 안녕하세요.
            </li>
            <?php } ?>
            <?php
            // $menu_datas = get_menu_db(1, true);
			$i = 0;
			foreach( $menu_datas as $row ){
				if( empty($row) ) continue;
            ?>
                <li class="gnb_1dli">
                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                    <?php
                    $k = 0;
                    foreach( (array) $row['sub'] as $row2 ){
						if( empty($row2) ) continue;
                        if($k == 0)
                            echo '<button type="button" class="btn_gnb_op"><span class="sound_only">하위분류</span></button><ul class="gnb_2dul">'.PHP_EOL;
                    ?>
                        <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><span></span><?php echo $row2['me_name'] ?></a></li>
                    <?php
					$k++;
                    }	//end foreach $row2

                    if($k > 0)
                        echo '</ul>'.PHP_EOL;
                    ?>
                </li>
            <?php
			$i++;
            }	//end foreach $row

            if ($i == 0) {  ?>
                <li id="gnb_empty">메뉴 준비 중입니다.<?php if ($is_admin) { ?> <br><a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하세요.<?php } ?></li>
            <?php } ?>
            </ul>
            <?php if($is_member){ ?>
            <div id="gnb_ft">
                <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="btn03 logout">로그아웃</a>
            </div>
            <?php } ?>
        </div>

        <!-- <button type="button" id="user_btn" class="hd_opener"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">사용자메뉴</span></button> -->
        <div class="hd_div" id="user_menu">
            <button type="button" id="user_close" class="hd_closer"><span class="sound_only">메뉴 닫기</span><i class="fa fa-times" aria-hidden="true"></i></button>
            <div id="hd_sch">
                <h2>사이트 내 전체검색</h2>
                <form name="fsearchbox" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
                <input type="hidden" name="sfl" value="wr_subject||wr_content">
                <input type="hidden" name="sop" value="and">
                <input type="text" name="stx" id="sch_stx" placeholder="검색어를 입력해주세요" required maxlength="20">
                <button type="submit" value="검색" id="sch_submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
                </form>

                <script>
                function fsearchbox_submit(f)
                {
                    var stx = f.stx.value.trim();
                    if (stx.length < 2) {
                        alert("검색어는 두글자 이상 입력하십시오.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
                    var cnt = 0;
                    for (var i = 0; i < stx.length; i++) {
                        if (stx.charAt(i) == ' ')
                            cnt++;
                    }

                    if (cnt > 1) {
                        alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }
                    f.stx.value = stx;

                    return true;
                }
                </script>
            </div>
        </div>

        <script>
        $(function () {

            $(".hd_opener").on("click", function() {
                var $this = $(this);
                var $hd_layer = $this.next(".hd_div");

                if($hd_layer.is(":visible")) {
                    $hd_layer.hide();
                    $this.find("span").text("열기");
                } else {
                    var $hd_layer2 = $(".hd_div:visible");
                    $hd_layer2.prev(".hd_opener").find("span").text("열기");
                    $hd_layer2.hide();

                    $hd_layer.show();
                    $this.find("span").text("닫기");
                }
            });

            $("#container").on("click", function() {
                $(".hd_div").hide();

            });

            $(".btn_gnb_op").click(function(){
                $(this).toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);
                
            });

            $(".hd_closer").on("click", function() {
                var idx = $(".hd_closer").index($(this));
                $(".hd_div:visible").hide();
                $(".hd_opener:eq("+idx+")").find("span").text("열기");
            });
        });
        </script>
    </div>
</header>

<div id="wrapper">
    <div id="container">
    <?php if (!defined("_INDEX_")) { ?>
    	<h2 id="container_title" class="top" title="<?php echo get_text($g5['title']); ?>">
    		<a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i><span class="sound_only">뒤로가기</span></a> <?php echo get_head_title($g5['title']); ?>
    	</h2>
    <?php } ?>
    <div id="box">
        <h3 id="box_title"><?=$g5['box_title']?></h3>