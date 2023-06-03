<?php
$sub_menu = "935900";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '통계설정';
include_once('./_top_menu_stat.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];

?>
<style>
</style>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">

<section id="anc_cf_default">
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>기본설정</caption>
		<colgroup>
			<col class="grid_4">
			<col>
			<col class="grid_4">
			<col>
		</colgroup>
		<tbody>
		<tr>
			<th scope="row">UPH 통계 기준</th>
			<td colspan="3">
                <div style="visibility:hid den;">
                <label for="set_uph_worktime_outout">
                    <input type="radio" name="set_uph_worktime" value="output" id="set_uph_worktime_outout" <?=($g5['setting']['set_uph_worktime']=='output')?'checked':''?>> 생산시간기준
                </label> &nbsp;&nbsp;
                <label for="set_uph_worktime_machine">
                    <input type="radio" name="set_uph_worktime" value="machine" id="set_uph_worktime_machine" <?=($g5['setting']['set_uph_worktime']=='machine')?'checked':''?>> 설비가동시간기준
                </label>
                </div>
			</td>
		</tr>
        <tr>
            <th scope="row">관리자메모</th>
            <td>
                <?php echo help('통계 설정 관련 메모입니다.') ?>
                <textarea name="set_memo_statistics"><?php echo get_text($g5['setting']['set_memo_statistics']); ?></textarea>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<div class="btn_fixed_top btn_confirm">
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>
$(function(){

});

function fconfigform_submit(f) {

    f.action = "./config_form_update.php";
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
