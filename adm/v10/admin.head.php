<?php
if (!defined('_GNUBOARD_')) exit;
if(isMobile()){
    goto_url(G5_USER_ADMIN_MOBILE_URL);
}
else if(get_session('ss_kiosk_yn')){
    goto_url(G5_USER_ADMIN_KIOSK_URL);
}
$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

$files = glob(G5_ADMIN_PATH.'/css/admin_extend_*');
if (is_array($files)) {
    foreach ((array) $files as $k=>$css_file) {
        
        $fileinfo = pathinfo($css_file);
        $ext = $fileinfo['extension'];
        
        if( $ext !== 'css' ) continue;
        
        $css_file = str_replace(G5_ADMIN_PATH, G5_ADMIN_URL, $css_file);
        add_stylesheet('<link rel="stylesheet" href="'.$css_file.'">', $k);
    }
}

// include_once(G5_PATH.'/head.sub.php');
include_once(G5_USER_ADMIN_PATH.'/_head.sub_hook.php');

function print_menu1($key, $no='')
{
    global $menu;

    $str = print_menu2($key, $no);

    return $str;
}

function print_menu2($key, $no='')
{
    global $menu, $amenu, $auth_menu, $is_admin, $auth, $g5, $sub_menu;
    
    $str = "<ul".(($key == 'menu915')?' id="ul_dash_submenu"':'').">";
    for($i=1; $i<count($menu[$key]); $i++)
    {
        if( ! isset($menu[$key][$i]) ){
            continue;
        }
        // echo $menu['menu915'][$i][0]."<br>";

        if ($is_admin != 'super' && $key != 'menu915' && (!array_key_exists($menu[$key][$i][0],$auth) || !strstr($auth[$menu[$key][$i][0]], 'r')))
            continue;
        
        $gnb_grp_div = $gnb_grp_style = '';

        if (isset($menu[$key][$i][4])){
            if (($menu[$key][$i][4] == 1 && $gnb_grp_style == false) || ($menu[$key][$i][4] != 1 && $gnb_grp_style == true)) $gnb_grp_div = ' gnb_grp_div';

            if ($menu[$key][$i][4] == 1) $gnb_grp_style = ' gnb_grp_style';
        }

        $current_class = '';
        if ($menu[$key][$i][0] == $sub_menu){
            $current_class = ' on';
        }

        $str .= '<li data-menu="'.$menu[$key][$i][0].'"'.(($key != 'menu915')?:' class="li_dash_submenu"').(($key != 'menu915')?:' mta_idx="'.$menu[$key][$i][4].'"').(($key != 'menu915')?:' sort="'.$menu[$key][$i][3].'"').'><a href="'.$menu[$key][$i][2].'" class="gnb_2da'.$gnb_grp_style.$gnb_grp_div.$current_class.'"'.((!$menu[$key][$i][3])?:' id="dash_'.$menu[$key][$i][3].'"').'>'.$menu[$key][$i][1].'</a></li>';
        // $str .= '<li data-menu="'.$menu[$key][$i][0].'"><a href="'.$menu[$key][$i][2].'" class="gnb_2da '.$gnb_grp_style.' '.$gnb_grp_div.$current_class.'">'.$menu[$key][$i][1].'</a></li>';

        $auth_menu[$menu[$key][$i][0]] = $menu[$key][$i][1];
    }
    $str .= "</ul>";
    if($key == 'menu915'){
        $str .= "<ul id='ul_dash_add'>";
        $str .= "</ul>";
    }

    return $str;
}

$adm_menu_cookie = array(
'container' => '',
'gnb'       => '',
'btn_gnb'   => '',
);

if( ! empty($_COOKIE['g5_admin_btn_gnb']) ){
    $adm_menu_cookie['container'] = 'container-small';
    $adm_menu_cookie['gnb'] = 'gnb_small';
    $adm_menu_cookie['btn_gnb'] = 'btn_gnb_open';
}
// 대시보드 공통 파일
if($g5['dir_name'] == 'v10' && preg_match("/^index/",$g5['file_name'])) include_once('./_dashboard_top_submenu.php');
?>

<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{

    menu(id);

    var el_id = document.getElementById(id);

    //submenu = eval(name+".style");
    submenu = el_id.style;
    submenu.left = tempX - ( w + 11 );
    submenu.top  = tempY - ( h / 2 );

    selectBoxVisible();

    if (el_id.style.display != 'none')
        selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container">본문 바로가기</a></div>

<header id="hd">
    <h1><?php echo $config['cf_title'] ?></h1>
    <div id="hd_top">
        <button type="button" id="btn_gnb" class="btn_gnb_close <?php echo $adm_menu_cookie['btn_gnb'];?>">메뉴</button>
       <div id="logo"><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>"><img src="<?php echo G5_ADMIN_URL ?>/img/logo.png" alt="<?php echo get_text($config['cf_title']); ?> 관리자"></a></div>

        <div id="tnb">
            <ul>
                <li class="tnb_li"><a href="<?php echo G5_SHOP_URL ?>/" class="tnb_shop" target="_blank" title="쇼핑몰 바로가기">쇼핑몰 바로가기</a></li>
                <li class="tnb_li"><a href="<?php echo G5_URL ?>/" class="tnb_community" target="_blank" title="커뮤니티 바로가기">커뮤니티 바로가기</a></li>
                <li class="tnb_li"><a href="<?php echo G5_ADMIN_URL ?>/service.php" class="tnb_service">부가서비스</a></li>
                <li class="tnb_li"><button type="button" class="tnb_mb_btn">관리자<span class="./img/btn_gnb.png">메뉴열기</span></button>
                    <ul class="tnb_mb_area">
                        <li><a href="<?php echo G5_ADMIN_URL ?>/member_form.php?w=u&amp;mb_id=<?php echo $member['mb_id'] ?>">관리자정보</a></li>
                        <li id="tnb_logout"><a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <nav id="gnb" class="gnb_large <?php echo $adm_menu_cookie['gnb']; ?>">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
            <?php
            $jj = 1;
            foreach($amenu as $key=>$value) {
                $href1 = $href2 = '';

                if ($menu['menu'.$key][0][2]) {
                    $href1 = '<a href="'.$menu['menu'.$key][0][2].'" class="gnb_1da">';
                    $href2 = '</a>';
                } else {
                    continue;
                }

                $current_class = "";
                if (isset($sub_menu) && (substr($sub_menu, 0, 3) == substr($menu['menu'.$key][0][0], 0, 3)))
                    $current_class = " on";

                $button_title = $menu['menu'.$key][0][1];
            ?>
            <li class="gnb_li<?php echo $current_class;?>">
                <button type="button" class="btn_op menu-<?php echo $key; ?> menu-order-<?php echo $jj; ?>" title="<?php echo ($is_admin)?$button_title.'('.$key.')':$button_title; ?>"><?php echo $button_title;?></button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3><?php echo $menu['menu'.$key][0][1];?></h3>
                        <?php echo print_menu1('menu'.$key, 1); ?>
                    </div>
                </div>
            </li>
            <?php
            $jj++;
            }     //end foreach
            ?>
        </ul>
    </nav>

</header>
<script>
jQuery(function($){

    var menu_cookie_key = 'g5_admin_btn_gnb';

    $(".tnb_mb_btn").click(function(){
        $(".tnb_mb_area").toggle();
    });

    $("#btn_gnb").click(function(){
        
        var $this = $(this);

        try {
            if( ! $this.hasClass("btn_gnb_open") ){
                set_cookie(menu_cookie_key, 1, 60*60*24*365);
            } else {
                delete_cookie(menu_cookie_key);
            }
        }
        catch(err) {
        }

        $("#container").toggleClass("container-small");
        $("#gnb").toggleClass("gnb_small");
        $this.toggleClass("btn_gnb_open");

    });

    // Action from CLICK to HOVER
    $(".gnb_ul > li").on({
        mouseenter: function () {
            // console.log( $(this).attr('title') );
            // if($(this).closest('ul').find("li[init=on]").length<=0) {
            //     $(this).attr("init","on");
            // }
            $(this).addClass("on").siblings().removeClass("on");
        },
        mouseleave: function () {
            $('a.gnb_2da.on').closest('li.gnb_li').addClass("on").siblings().removeClass("on");
            // $(this).closest('ul').find('li[init=on]').addClass("on").siblings().removeClass("on");
        }
    });

});
</script>

<script>
// iFrame높이 자동(자식창의 높이에 따라 맞춤)
function receiveMessage(event) {
    // if localhost com.
    if( /localhost/.test(event.origin) ) {
        $("#iframe01").height(event.data + 10); 
    }
    else {
        if (event.origin !== "<?=G5_URL?>")	// 자식창의 URL 주소
        return;
        $("#iframe01").height(event.data + 10); 
    }
}
if ('addEventListener' in window){ 
    window.addEventListener('message', receiveMessage, false); 
}
else if ('attachEvent' in window) { //IE
    window.attachEvent('onmessage', receiveMessage); 
} 
</script>


<div id="wrapper">

    <div id="container" class="<?php echo $adm_menu_cookie['container']; ?>">

        <h1 id="container_title">
            <?php echo $g5['title'] ?>
            <?php if($g5['dir_name'] == 'v10' && $g5['file_name'] == 'index'){ include_once('./_dashboard_right_top.php'); } ?>
        </h1>
        <div class="container_wr">