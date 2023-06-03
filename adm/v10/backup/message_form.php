<?php
$sub_menu = "950300";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check_menu($auth, $sub_menu, 'r');

$html_title = '메시지';

$msg_idx = isset($_GET['msg_idx']) ? (int) $_GET['msg_idx'] : 0;
$msg = array('msg_idx'=>0, 'msg_subject'=>'');

if ($w == 'u') {
    $html_title .= '수정';
    $readonly = ' readonly';

    $sql = " select * from {$g5['message_table']} where msg_idx = '{$msg_idx}' ";
    $msg = sql_fetch($sql,1);
//    print_r3($msg);
    if (!$msg['msg_idx'])
        alert('등록된 자료가 없습니다.');
} else {
    $html_title .= '입력';
    $msg['msg_type'] = 'email';
}

$g5['title'] = $html_title;
include_once('./_head.php');
?>

<div class="local_desc"><p>내용에 {이름}, {이메일}, {공고제목} 처럼 내용에 삽입하면 해당 내용에 맞게 변환하여 메시지를 발송합니다.</p></div>

<form name="fmailform" id="fmailform" action="./message_form_update.php" onsubmit="return fmailform_check(this);" method="post">
<input type="hidden" name="w" value="<?php echo $w ?>" id="w">
<input type="hidden" name="msg_idx" value="<?php echo $msg['msg_idx'] ?>" id="msg_idx">
<input type="hidden" name="token" value="" id="token">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="msg_subject">제목<strong class="sound_only">필수</strong></label></th>
        <td><input type="text" name="msg_subject" value="<?php echo get_sanitize_input($msg['msg_subject']); ?>" id="msg_subject" required class="required frm_input" size="100"></td>
    </tr>
    <tr>
        <th scope="row">타입</th>
        <td>
        	<input type="radio" name="msg_type" value="email" id="msg_type_email" <?=($msg['msg_type']=='email')?'checked':''?>>
            <label for="msg_type_email">이메일</label>
            <input type="radio" name="msg_type" value="hp" id="msg_type_hp" <?=($msg['msg_type']=='hp')?'checked':''?>>
            <label for="msg_type_hp">문자</label>
        </td>
    </tr>
    <tr class="tr_hp" style="display:<?=($msg['msg_type']=='email')?'none':''?>">
        <th scope="row"><label for="msg_hp_content">문자 내용</label></th>
        <td>
            <textarea name="msg_hp_content" id="msg_hp_content"><?php echo get_text($msg['msg_content']); ?></textarea>
        </td>
    </tr>
    <!-- display:none 하면 editor plugin 이 해당 에디터를 못 찾는 이슈가 있어서 setTimeout처리합니다. -->
    <tr class="tr_email" style="display:<?=($msg['msg_type']=='hp')?'no ne':''?>">
        <th scope="row"><label for="msg_content">메일 내용</label></th>
        <td style="padding-top:<?=($msg['msg_type']=='hp')?'700px':'10px'?>;"><?php echo editor_html("msg_content", get_text(html_purifier($msg['msg_content']), 0)); ?></td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top ">
    <input type="submit" class="btn_submit btn" accesskey="s" value="확인">
</div>
</form>

<script>
$(document).on('click','input[name=msg_type]',function(e){
    if( $(this).val() == 'hp' ) {
        $('.tr_hp').show();
        $('.tr_email').hide();
    }
    else {
        $('.tr_hp').hide();
        $('.tr_email').show();
        $('.tr_email td').css('padding-top','0');
    }    
});
    
<?php
if($msg['msg_type']=='hp') {
?>
    setTimeout(function(){
        $('.tr_email').hide()
    },1000);
<?php
}
?>

    
function fmailform_check(f)
{
    errmsg = "";
    errfld = "";

    check_field(f.msg_subject, "제목을 입력하세요.");
    //check_field(f.msg_content, "내용을 입력하세요.");

    if (errmsg != "") {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    <?php echo get_editor_js("msg_content"); ?>
    <?php // echo chk_editor_js("msg_content"); ?>

    return true;
}

document.fmailform.msg_subject.focus();
</script>

<?php
include_once('./_tail.php');