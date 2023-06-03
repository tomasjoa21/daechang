<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

?>
<!-- 위시리스트 간략 보기 시작 { -->
<div id="wish2" class="side-wish nano-content">
    <ul>
    <?php
    $wishlist_datas = get_wishlist_datas($member['mb_id'], true);
    $i = 0;
    $wish_img_wd = 65;
    $wish_img_ht = 65;
    foreach( (array) $wishlist_datas as $row )
    {
        if( !$row['it_id'] ) continue;
        
        $item = get_shop_item($row['it_id'], true);
        
        if( !$item['it_id'] ) continue;

        echo '<li>';
        $it_name = get_text($item['it_name']);

        // 이미지로 할 경우//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false, $is_url=false
        $it_img = get_it_image($row['it_id'], $wish_img_wd, $wish_img_ht, true,'','',true,false);
        echo '<div class="prd_img" style="width:'.$wish_img_wd.'px;">'.$it_img.'</div>';
		echo '<div class="prd_cnt">';
        echo '<a href="'.shop_item_url($row['it_id']).'" class="prd_name">'.$it_name.'</a>';
        echo '<div class="prd_price">'.display_price(get_price($item), $item['it_tel_inq']).'</div>';
		echo '</div>'.PHP_EOL;
        echo '</li>';
        $i++;
    }   //end foreach

	?>
    </ul>
    <?php if($i == 0){?>
    <p class="li_empty">위시리스트 없음</p>
    <?php } ?>
</div>
<!-- } 위시리스트 간략 보기 끝 -->