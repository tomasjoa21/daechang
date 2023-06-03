<?php
$sub_menu = "925900";
include_once("./_common.php");

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '폼';
include_once(G5_PATH.'/head.sub.php');
?>
<script src="<?php echo G5_ADMIN_URL ?>/admin.js?ver=<?php echo G5_JS_VER; ?>"></script>

<form name="form03" id="form03" onsubmit="return form03_submit(this);" method="post">
    <input type="text" name="mst_name" value="" id="mst_name" class="frm_input" style="width:100px;">
    <a href="javascript:" id="btn_add" class="btn btn_02">등록</a>
    <a href="javascript:" class="btn btn_02 btn_cancel">취소</a>
</form>

<script>
$(document).on('click','.btn_cancel',function(){
    $("#mst_idx", parent.document).show();
    $("#mst_idx", parent.document).closest('td').find('iframe').hide();
    $('#mst_idx', parent.document).val('');
});

//비가동 타입 등록 클릭
$('#btn_add').on('click',function(e){
    e.preventDefault();
    if($('#mst_name').val()==''){
        alert('비가동 유형을 입력해 주세요.');
        return false;
    }

    $.ajax({
        url: '<?= G5_USER_ADMIN_AJAX_URL ?>/mms_offwork_add.php',
        data: {
            'mms_idx': <?=$_REQUEST['mms_idx']?>
            , 'mst_name': $('#mst_name').val()
        },
        dataType: 'json',
        timeout: 10000,
        beforeSend: function() {},
        success: function(res) {
            // console.log(res);
            //var prop1; for(prop1 in res.rows) { console.log( prop1 +': '+ res.rows[prop1] ); }
            if(res.result == true) {
                $('#mst_idx', parent.document).children('option:not(:first)').remove(); // 항목 초기화

                // 불량타입 재구성
                $.each(res.rows2, function(i, v) {
                    // console.log(i+':'+v);
                    // console.log(v['mst_idx']+':'+v['mst_name']);
                    $('#mst_idx', parent.document).append(
                        "<option value='" + v['mst_idx'] + "'>" + v['mst_name'] + "</option>"
                    );
                });
                // 추가항목 선택상태로 설정
                $('#mst_idx', parent.document).children('option:last').attr('selected','selected');
                // 직접입력 항목 추가
                $('#mst_idx', parent.document).append(
                    "<option value='direct'>직접입력</option>"
                );
                // show, hide DOM items.
                $('#mst_idx', parent.document).show();
                $("#mst_idx", parent.document).closest('td').find('iframe').hide();
            }
            else {
                alert(res.msg);
            }
        },
        error: function(xmlRequest) {
            alert('Status: ' + xmlRequest.status + ' \n\rstatusText: ' + xmlRequest.statusText +
                ' \n\rresponseText: ' + xmlRequest.responseText);
        }
    });
});

function form03_submit(f) {

    return false;
}
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');