<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once('./_head.sub.php');
$menu_datas = array(
    array(
        'me_name' => '라벨출력'
        , 'me_link' => G5_USER_ADMIN_KIOSK_URL.'/label_production_list.php'
        , 'me_target' => '_self'
        , 'me_icon' => 'fa-print'
        , 'me_file' => 'label_production_list'
        , 'me_keyword' => 'label_production'
    )
    ,
    array(
        'me_name' => '입고처리'
        , 'me_link' => G5_USER_ADMIN_KIOSK_URL.'/input_list.php'
        , 'me_target' => '_self'
        , 'me_icon' => 'fa-cubes'
        , 'me_file' => 'input_list'
        , 'me_keyword' => 'input'
    ),
    array(
        'me_name' => '출하처리'
        , 'me_link' => G5_USER_ADMIN_KIOSK_URL.'/delivery_list.php'
        , 'me_target' => '_self'
        , 'me_icon' => 'fa-truck'
        , 'me_file' => 'delivery_list'
        , 'me_keyword' => 'delivery'
    ),
    // ,
    // array(
    //     'me_name' => '불량라벨출력'
    //     , 'me_link' => G5_USER_ADMIN_KIOSK_URL.'/label_defect_list.php'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-exclamation-triangle'
    //     , 'me_file' => 'label_defect_list'
    // )

    // array(
    //     'me_name' => '프린터테스트'
    //     , 'me_link' => G5_USER_ADMIN_KIOSK_URL.'/label_test_form.php'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-print'
    //     , 'me_file' => 'label_test_form'
    //     , 'me_keyword' => 'label_test'
    // )

    // array(
    //     'me_name' => 'READY'
    //     , 'me_link' => 'javascript:'
    //     , 'me_target' => '_self'
    //     , 'me_icon' => 'fa-coffee'
    //     , 'me_file' => 'ready'
    //     , 'me_keyword' => 'ready'
    // )
);
$main_type_class = ($g5['file_name'] == 'index') ? 'index_main' : 'sub_main';
?>
<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>
    <div class="to_content"><a href="#container">본문 바로가기</a></div>

    <div id="hd_wrapper">
        <?php if($is_member){ ?>
        <div id="hd_left_box">
            <a href="javascript:history.back();"><i class="fa fa-chevron-left" aria-hidden="true"></i><span class="sound_only">뒤로가기</span></a> <?php echo get_head_title($g5['title']); ?>
            <?=$member['mb_name']?>님 안녕하세요.
        </div>
        <?php } ?>
        <div id="logo">
            <a href="<?php echo G5_USER_ADMIN_KIOSK_URL ?>"><img src="<?php echo G5_USER_ADMIN_KIOSK_IMG_URL ?>/epcs_logo.png" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>
        <?php if($is_member){ ?>
        <div id="hd_right_box">
            <span><?=G5_TIME_YMD?></span>
            <a href="<?php echo G5_USER_ADMIN_KIOSK_BBS_URL ?>/logout.php" class="btn03 logout"><i class="fa fa-sign-out"></i><span>로그아웃</span></a>
        </div>
        <?php } ?>
    </div>
    <?php if($g5['file_name'] != 'index') { ?>
    <div id="hd_tab">
        <ul>
            <?php foreach($menu_datas as $row){ ?>
            <li><a href="<?=$row['me_link']?>" class="<?=((preg_match("/^".$row['me_keyword']."/",$g5['file_name']))?'focus':'')?>"><i class="fa <?=$row['me_icon']?>" aria-hidden="true"></i><span><?=$row['me_name']?></span></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
</header>

<div id="wrapper">
    <div id="container">