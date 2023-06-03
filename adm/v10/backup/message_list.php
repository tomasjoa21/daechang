<?php
$sub_menu = '950300';
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from {$g5['message_table']} ";

// 테이블의 전체 레코드수만 얻음
$sql = " select COUNT(*) as cnt {$sql_common} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page = 1;

$sql = " select * {$sql_common} order by msg_idx desc ";
$result = sql_query($sql);

$g5['title'] = '메시지관리';
include_once('./_head.php');

$colspan = 8;
?>
<style>
    .td_mng_adm {width:130px;}
</style>

<div class="local_desc01 local_desc">
    <p>
        <b>테스트 메시지 발송</b>은 현재 로그인된 관리자 휴대폰 또는 이메일로 테스트 메시지가 발송합니다.<br>
        현재 등록된 메일은 총 <?php echo $total_count ?>건입니다.<br>
        <strong>주의) 대량 메일을 발송하면 호스팅 업체에서 계정을 차단할 수 있습니다. 작은단위(최대 수십건)로 발송해 주십시오.</strong>
    </p>
</div>


<form name="fmaillist" id="fmaillist" action="./message_delete.php" method="post">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" title="현재 페이지 목록 전체선택" onclick="check_all(this.form)"></th>
        <th scope="col">번호</th>
        <th scope="col">메시지타입</th>
        <th scope="col">제목</th>
        <th scope="col">작성일시</th>
        <th scope="col">테스트</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $s_vie = '<a href="./message_preview.php?msg_idx='.$row['msg_idx'].'" target="_blank" class="btn btn_03 btn_preview">미리보기</a>';
        $s_mod = '<a href="./message_form.php?'.$qstr.'&w=u&msg_idx='.$row['msg_idx'].'" class="btn btn_03">수정</a>';

        $num = number_format($total_count - ($page - 1) * $config['cf_page_rows'] - $i);

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo $row['msg_subject']; ?> 메일</label>
            <input type="checkbox" id="chk_<?php echo $i ?>" name="chk[]" value="<?php echo $row['msg_idx'] ?>">
        </td>
        <td class="td_num_c"><?php echo $num ?></td>
        <td class="td_datetime"><?php echo $g5['set_msg_type_value'][$row['msg_type']] ?></td>
        <td class="td_left"><a href="./message_form.php?w=u&amp;msg_idx=<?php echo $row['msg_idx'] ?>"><?php echo $row['msg_subject'] ?></a></td>
        <td class="td_datetime"><?php echo $row['msg_time'] ?></td>
        <td class="td_test"><a href="./message_test.php?msg_idx=<?php echo $row['msg_idx'] ?>">테스트</a></td>
        <td class="td_mng_adm"><?php echo $s_vie.$s_mod; ?></td>
    </tr>

    <?php
    }
    if (!$i)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>
<div class="btn_fixed_top">
    <input type="submit" value="선택삭제" class="btn btn_02">
    <a href="./message_form.php" id="message_add" class="btn btn_01">추가하기</a>
</div>
</form>

<script>
$(function() {
    $(document).on('click','.btn_preview',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winPreview = window.open(href, "winPreview", "left=100,top=100,width=600,height=600,scrollbars=1");
        winPreview.focus();
        return false;
    });
    
    
    $('#fmaillist').submit(function() {
        if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            if (!is_checked("chk[]")) {
                alert("선택삭제 하실 항목을 하나 이상 선택하세요.");
                return false;
            }

            return true;
        } else {
            return false;
        }
    });
});
</script>

<?php
include_once ('./_tail.php');