<link rel="stylesheet" href="<?=G5_USER_ADMIN_KIOSK_URL?>/modal/css/input_number.css">
<div class="mdl" id="mdl_num">
<div class="mdl_bg" id="mdl_num_bg"></div><!-- .mdl_bg -->
<div class="mdl_box" id="mdl_num_box">
<?php echo svg_icon($n='close',$c='mdl_close mdl_num_close',$w=50,$h=50,$f='#ffffff'); ?>
<div class="mdl_cont">
   <div class="mdl_display"><input type="text" id="input_num"></div>
   <div class="btn_box">
        <div class="btn_area">
            <div class="btn_pos">
                <div class="btn0 btn_num num_1">1</div>
                <div class="btn0 btn_num num_2">2</div>
                <div class="btn0 btn_num num_3">3</div>
                <div class="btn0 btn_num num_4">4</div>
                <div class="btn0 btn_num num_5">5</div>
                <div class="btn0 btn_num num_6">6</div>
                <div class="btn0 btn_num num_7">7</div>
                <div class="btn0 btn_num num_8">8</div>
                <div class="btn0 btn_num num_9">9</div>
                <div class="btn0 btn_fn num_del">Del</div>
                <div class="btn0 btn_num num_0">0</div>
                <div class="btn0 btn_fn num_cancel">Cancel</div>
            </div>
            <div class="btn_bot">
                <div class="btn0 btn_fn num_input">OK</div>
            </div>
        </div>
   </div>
</div>
</div><!-- .mdl_box -->
</div><!-- .mdl -->
<script>
$('.mdl_num_close, #mdl_num_bg, .num_cancel').click(function(){
    $('#mdl_num').hide();
    $('#input_num').val('');
    $('.input_cnt_on').removeClass('input_cnt_on');
});

$('.btn_num').on('click',function(){
    var num = $(this).text();
    var input_num = $('#input_num').val();
    if(num == '0' && input_num == '') return false;
    $('#input_num').val(input_num+num);
});
$('.num_del').on('click',function(){
    $('#input_num').val('');
});
$('.num_input').on('click',function(){
    var input_num = $('#input_num').val();
    $('#input_num').val('');
    $('.input_cnt_on').val(input_num);
    $('.input_cnt_on').removeClass('input_cnt_on');
    $('#mdl_num').hide();
});
//숫자만 입력
$('#input_num').on('keyup',function(){
    var cnt = $(this).val().replace(/[^0-9]/g,"");
    cnt = (cnt == '0') ? '' : cnt;
    $(this).val(cnt);
});

</script>