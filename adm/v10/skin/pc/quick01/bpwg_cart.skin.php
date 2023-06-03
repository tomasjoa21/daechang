<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>
<!-- 장바구니 간략 보기 시작 { -->
<div id="sbsk2" class="sbsk nano-content">
    <form name="skin_frmcartlist" id="skin_sod_bsk_list" method="post" action="<?php echo G5_SHOP_URL.'/cartupdate.php'; ?>">
    <ul>
    <?php
    $cart_datas = get_boxcart_datas(true);
    $i = 0;
    $prd_img_wd = 65;
    $prd_img_ht = 65;
    foreach($cart_datas as $row)
    {
        if( !$row['it_id'] ) continue;

        echo '<li>';
        $it_name = get_text($row['it_name']);
        // 이미지로 할 경우//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false, $is_url=false
        $it_img = bpwg_get_it_image($row['it_id'], $prd_img_wd, $prd_img_ht, true,'','',true,false);
		echo '<div class="prd_img">'.$it_img.'</div>';
		echo '<div class="prd_cnt">';
        echo '<div class="prd_dv">';
        echo '<a href="'.G5_SHOP_URL.'/cart.php" class="prd_name">'.$it_name.'</a>';
        echo '<span class="prd_cost">';
		echo number_format($row['ct_price']).PHP_EOL;
        echo '</span>'.PHP_EOL;
		echo '<button class="cart_del" type="button" data-it_id="'.$row['it_id'].'"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="sound_only">삭제</span></button>'.PHP_EOL;
		echo '</div>';
        echo '<input type="hidden" name="act" value="buy">';
        echo '<input type="hidden" name="ct_chk['.$i.']" value="1">';
        echo '<input type="hidden" name="it_id['.$i.']" value="'.$row['it_id'].'">';
        echo '<input type="hidden" name="it_name['.$i.']" value="'.$it_name.'">';
		echo '</div>';
        echo '</li>';
        
        $i++;
    }   //end foreach

    ?>
    </ul>
    <?php if($i == 0){ ?>
    <p class="li_empty">장바구니 상품 없음</p>
    <?php } ?>
    <?php if($i){ ?>
    <div class="btn_buy">
        <button type="submit" class="go go_half go_buy">구매하기</button>
        <a href="<?php echo G5_SHOP_URL; ?>/cart.php" class="go go_half go_cart">장바구니</a>
    </div>
    <?php } ?>
    </form>
</div>
<script>
jQuery(function ($) {
    $("#<?=$bid?> #sbsk2").on("click", ".cart_del", function(e) {
        e.preventDefault();

        var it_id = $(this).data("it_id");
        var $wrap = $(this).closest("li");

        $.ajax({
            url: g5_theme_shop_url+"/ajax.action.php",
            type: "POST",
            data: {
                "it_id" : it_id,
                "action" : "cart_delete"
            },
            dataType: "json",
            async: true,
            cache: false,
            success: function(data, textStatus) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                $wrap.remove();
            }
        });
    });
});
</script>
<!-- } 장바구니 간략 보기 끝 -->