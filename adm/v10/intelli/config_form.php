<?php
$sub_menu = "920900";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '주조파라메타설정';
// include_once('./_top_menu_db.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];
?>

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
			<th scope="row">양품 그룹핑 수</th>
			<td colspan="3">
				<?php echo help('양품 그룹핑 숫자를 입력하세요. 양품이 집단적으로 모여 있는 갯수를 설정하세요.<br>설정한 숫자 이상인 경우의 중간값을 최적 파라메타로 설정합니다.') ?>
				<input type="text" name="set_parameter_group_count" value="<?php echo $g5['setting']['set_parameter_group_count'] ?>" id="set_monitor_reload" required class="required frm_input" style="width:50px;">
			</td>
		</tr>
		<tr>
			<th scope="row">등급 합계 기준</th>
			<td colspan="3">
				<?php echo help('1~18등급까지의 등급 합계에 대한 기준값을 설정합니다.<br>등급합계가 설정값 범위를 벗어나면 초기화를 한 다음 다시 최적 파라메타를 추적합니다.') ?>
				<input type="text" name="set_ok_sum_min" value="<?php echo $g5['setting']['set_ok_sum_min'] ?>" id="set_monitor_reload" required class="required frm_input" style="width:50px;">
				~
				<input type="text" name="set_ok_sum_max" value="<?php echo $g5['setting']['set_ok_sum_max'] ?>" id="set_monitor_reload" required class="required frm_input" style="width:50px;">
			</td>
		</tr>
		<tr>
			<th scope="row">추적 최대 일수</th>
			<td colspan="3">
				<?php echo help('최적 파라메타 추적 기간을 일수로 설정합니다. 최대 몇 일전까지 추적할 지 숫자로 입력하세요.') ?>
				<input type="text" name="set_parameter_max_day" value="<?php echo $g5['setting']['set_parameter_max_day'] ?>" required class="required frm_input" style="width:35px;"> 일
			</td>
		</tr>
		<tr>
			<th scope="row">주조기 설비번호들</th>
			<td colspan="3">
				<?php echo help('설비들 중에서 주조기 설비번호만 입력하세요.') ?>
				<input type="text" name="set_dicast_mms_idxs" value="<?php echo $g5['setting']['set_dicast_mms_idxs'] ?>" required class="required frm_input" style="width:50%;">
			</td>
		</tr>
        <tr>
            <th scope="row">관리자메모</th>
            <td>
                <?php echo help('주조파라메타 설정 관련 메모입니다.') ?>
                <textarea name="set_memo_dicasting" id="set_memo_super"><?php echo get_text($g5['setting']['set_memo_dicasting']); ?></textarea>
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
