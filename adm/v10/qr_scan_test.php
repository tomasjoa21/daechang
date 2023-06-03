<?php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

$g5['title'] = 'QR Test';
include_once(G5_PATH.'/head.sub.php');

?>
<style>
body {color:#818181;}
#qr_result {margin-top:10px;}
</style>

<div clsas="pr_wrapper" style="text-align:center">
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
include_once(G5_PATH.'/tail.sub.php');