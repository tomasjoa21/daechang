<?php
include_once('./_common.php');
include_once('./_head.php');

?>
<div id="main" class="<?=$main_type_class?>">
    <div id="inp_box">
        <span id="qr_ttl">스캔을 시작하세요.</span>
        <input type="text" name="qr_scan" value="" id="qr_scan" class="frm_input" style="width:150px;">
        <stron class="qr_status"></stron>
    </div>
</div>
<script>
$(function(e){
    $('#qr_scan').select().focus();

    // 값이 바뀌면 처리하고 다시 포커스
    // $(document).on('change','#qr_scan',function(e){
    //     var qr_result = $('#qr_scan').val();
    //     var qr_parsed = urlParaToJSON2(qr_result);
    //     $('#qr_result').text( qr_result );
    //     $('#qr_qstr').text( JSON.stringify(qr_parsed) );
    //     $('#qr_alert').text( 'ajax로 변수처리해 주심 되겠어요.' );
    //     setTimeout(() => {
    //         $('#qr_scan').val('').select().focus();
    //     }, 500);
    // });

    $('#qr_scan').on('input',function(){
        alert($(this).val());
    });
});
</script>
<?php
include_once('./_tail.php');
