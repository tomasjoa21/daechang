<?php
$sub_menu = "940120";
include_once('./_common.php');


auth_check($auth[$sub_menu],'w');


$g5['title'] = 'BOM 엑셀관리';
include_once('./_top_menu_bom.php');
include_once('./_head.php');
echo $g5['container_sub_title'];
?>
<style>
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="w">
<input type="hidden" name="token" value="">

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>엑셀 등록 시 시간이 5분 이상(또는 더 많이) 걸리는 경우는 등록이 불가능한 경우입니다. (참조 파일로 복잡하게 얽혀 있거나 파일 크기가 너무 큰 경우입니다.)</p>
    <p><span style="color:darkorange;">참조가 없는 단순 파일</span>로 등록하시거나 <span style="color:darkorange;">파일 크기를 나누어서</span>등록해 주시기 바랍니다.</p>
    <p>엑셀등록 안 될 때 새 문서를 만드는 방법은 동영상을 참고하세요. <a href="https://youtu.be/fu3zsiemJPc" target="_blank">[동영상보기]</a></p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4" style="width:15%;">
		<col style="width:85%;">
    </colgroup>
	<tbody>
	<tr>
        <th scope="row">표준엑셀</th>
        <td>
            <input type="hidden" name="excel_type" value="01">
            <?=help('표준 엑셀과 동일한 구조의 파일을 등록해 주셔야 합니다. 표준엑셀은 아래에서 확인하세요.')?>
            <div style="display:none;">
            <a href="https://docs.google.com/spreadsheets/d/1M8_cbgo5z8QV6qjmIsmUuHsKtoI0QLiN5kxAw5liRbo/edit?usp=sharing" target="_blank">US4 11P</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1LHFgMHCxUlLTw_QynpqpeB9yugDSzux6JB7yPHkGhNQ/edit?usp=sharing" target="_blank">US4 9P</a>
            <br><a href="https://docs.google.com/spreadsheets/d/18-CTlKX0Ldfg4BW0TzYhFOVAQaoauPnO3iDgL-nt-iI/edit?usp=sharing" target="_blank">US4 7P</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1cTWJ6_pbTvlK1lmjejAVjdDxxeFYH9YBR0RFA7aw9co/edit?usp=sharing" target="_blank">US4 2P</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1lEznrWIsNeCTDhzBNOwmu-8ZUggpR3KM6Iw4ZUNZt_k/edit?usp=sharing" target="_blank">TM</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1bK4mdzxRjlnI5Lu4bKHjo50YhVIkg3nxm6pEbpHHgow/edit?usp=sharing" target="_blank">RS4</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1ufG5XB5uHOwynlRdHKIlUOklKTudMb4aZZVjMJeH0XU/edit?usp=sharing" target="_blank">RG3(완료)</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1_WBhBTKdEq5itg_wH3YobyIIaottR02cJhj5-BoAzug/edit?usp=sharing" target="_blank">OS</a>
            <br><a href="https://docs.google.com/spreadsheets/d/1AMwhGYI7UM1WNoO12UV2AZcaaooxRUHSlq5pIaQVwW0/edit?usp=sharing" target="_blank">NX4</a>
            <br><a href="https://docs.google.com/spreadsheets/d/11IGtCOSyj1upYXeJZKiCQw5HcwZXv45uNQ03enyTiqE/edit?usp=sharing" target="_blank"><span style="color:darkorange;">NE(완료)</span></a>
            </div>
            <br><a href="https://docs.google.com/spreadsheets/d/1nsWf9z9qQUuDsMkLRqCMPh98soWiPW7ybaOZVjqED0I/edit?usp=sharing" target="_blank">LX2</a>
            <div style="display:none;">
            <br><a href="https://docs.google.com/spreadsheets/d/1-kHdFq1lVRx3rSzmbTQo9VLqW4BUZUKWvJRIT3WnMQQ/edit?usp=sharing" target="_blank">기타 TR8 外 5개차종</a>
            </div>
            <div style="background-color:#142341;padding:5px 10px;margin-top:5px;">
                <span style="color:darkorange;">참고만 하세요. (엑셀 등록하지 마세요.)</span><br>
                <p><a href="https://docs.google.com/spreadsheets/d/1nsWf9z9qQUuDsMkLRqCMPh98soWiPW7ybaOZVjqED0I/edit?usp=sharing" target="_blank">대창공업 ITEM LIST_REV1(23.1.27)-개발이범희GJ_REV12(최종) -> 최종파일 아님</a></p>
            </div>
            <!-- <select name="excel_type" id="excel_type">
                <option value="">엑셀 선택</option>
                <option value="01">대창공업 ITEM LIST_REV1(22.12.22)-개발이범희GJ_REV6.xlxs</option>
                <option value="03">대창공업</option>
            </select>
            <script>$('#excel_type').val('01')</script> -->
        </td>
    </tr>
	<tr>
        <th scope="row">엑셀 파일</th>
        <td>
            <input type="file" name="file_excel" class="frm_input">
        </td>
    </tr>
	</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function() {

});

function form01_submit(f) {


    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
