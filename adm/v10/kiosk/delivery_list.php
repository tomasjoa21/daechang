<?php
include_once('./_common.php');
include_once('./_head.php');

?>
<div id="main" style="padding-left:100px;">
    <br><br><br><br>
    <div>스캔을 시작하세요.</div>
    <input type="text" name="qr_scan" value="" id="qr_scan" class="frm_input" style="width:150px;">
    <div id="qr_result"></div>
    <div id="qr_qstr"></div>
    <div id="qr_alert"></div>
</div>
<script>
$(function(e){
    $('#qr_scan').select().focus();

    // 값이 바뀌면 처리하고 다시 포커스
    $(document).on('change','#qr_scan',function(e){
        var qr_result = $('#qr_scan').val();
        var qr_parsed = urlParaToJSON2(qr_result);
        $('#qr_result').text( qr_result );
        $('#qr_qstr').text( JSON.stringify(qr_parsed) );
        $('#qr_alert').text( 'ajax로 변수처리해 주심 되겠어요.' );
        setTimeout(() => {
            $('#qr_scan').val('').select().focus();
        }, 500);
    });
});
</script>
<?php
include_once('./_tail.php');
