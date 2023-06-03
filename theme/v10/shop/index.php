<?php
include_once('./_common.php');

if($is_member){
  if($is_auth){
    if(isMobile()){
      goto_url(G5_USER_ADMIN_MOBILE_URL);
    }
    else if(get_session('ss_kiosk_yn')){
      goto_url(G5_USER_ADMIN_KIOSK_URL);
    }
    else{
      goto_url(G5_USER_ADMIN_URL);
    }
    return;

  }
  else{
    if($member['mb_level'] >= 4){
      if(isMobile()){
        goto_url(G5_USER_ADMIN_MOBILE_URL);
      }
      else if(get_session('ss_kiosk_yn')){
        goto_url(G5_USER_ADMIN_KIOSK_URL);
      }
      return;
    }
  }
}

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MSHOP_PATH.'/index.php');
    return;
}

if(! defined('_INDEX_')) define('_INDEX_', TRUE);

include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
?>
<style>
#hd{position:fixed;width:100%;top:0;left:0;}
#wrapper{background:#45b0cf;position:fixed;width:100%;height:100%;top:0;left:0;z-index:0;}
#container{width:100%;background:rgba(0,0,0,0.7);position:fixed;display:flex;justify-content:center;align-items:center;width:100vw;height:100vh;}


@media (max-width:960px){
  #container{padding:0 50px;}  
}
</style>
<?php echo outlogin('theme/shop_basic'); // 외부 로그인, 테마의 스킨을 사용하려면 스킨을 theme/basic 과 같이 지정 ?>
<?php
include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
