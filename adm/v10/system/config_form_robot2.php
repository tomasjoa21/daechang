<?php
$sub_menu = "925140";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$g5['title'] = '로봇설정';
include_once('./_top_menu_robot.php');
include_once('./_head.php');
echo $g5['container_sub_title'];
?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">
<input type="hidden" name="fname" value="<?=$g5['file_name']?>">

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
			<th scope="row">로봇#1 경고 설정</th>
			<td colspan="3">
				<?php echo help('로봇 작동 경고 기준값을 설정합니다.') ?>
				<?php
				for($i=1;$i<7;$i++) {
				?>
				<div style="margin-bottom:5px;">
					토크<?=$i?>:
					<input type="text" name="set_robot_alarm_torque1<?=$i?>" value="<?php echo $g5['setting']['set_robot_alarm_torque1'.$i] ?>" class="frm_input" style="width:50px;"> 이상
					<span style="width:20px;display:inline-block;"></span>
					온도<?=$i?>:
					<input type="text" name="set_robot_alarm_temperature1<?=$i?>" value="<?php echo $g5['setting']['set_robot_alarm_temperature1'.$i] ?>" class="frm_input" style="width:50px;"> 이상
				</div>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th scope="row">로봇#2 경고 설정</th>
			<td colspan="3">
				<?php echo help('로봇 작동 경고 기준값을 설정합니다.') ?>
				<?php
				for($i=1;$i<7;$i++) {
				?>
				<div style="margin-bottom:5px;">
					토크<?=$i?>:
					<input type="text" name="set_robot_alarm_torque2<?=$i?>" value="<?php echo $g5['setting']['set_robot_alarm_torque2'.$i] ?>" class="frm_input" style="width:50px;"> 이상
					<span style="width:20px;display:inline-block;"></span>
					온도<?=$i?>:
					<input type="text" name="set_robot_alarm_temperature2<?=$i?>" value="<?php echo $g5['setting']['set_robot_alarm_temperature2'.$i] ?>" class="frm_input" style="width:50px;"> 이상
				</div>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th scope="row">로봇#1 <span style="color:darkorange;">중지</span> 설정</th>
			<td colspan="3">
				<?php echo help('로봇 작동 경고 기준값을 설정합니다.') ?>
				<?php
				for($i=1;$i<7;$i++) {
				?>
				<div style="margin-bottom:5px;">
					토크<?=$i?>:
					<input type="text" name="set_robot_stop_torque1<?=$i?>" value="<?php echo $g5['setting']['set_robot_stop_torque1'.$i] ?>" class="frm_input" style="width:50px;"> 이상
					<span style="width:20px;display:inline-block;"></span>
					온도<?=$i?>:
					<input type="text" name="set_robot_stop_temperature1<?=$i?>" value="<?php echo $g5['setting']['set_robot_stop_temperature1'.$i] ?>" class="frm_input" style="width:50px;"> 이상
				</div>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th scope="row">로봇#2 <span style="color:darkorange;">중지</span> 설정</th>
			<td colspan="3">
				<?php echo help('로봇 작동 경고 기준값을 설정합니다.') ?>
				<?php
				for($i=1;$i<7;$i++) {
				?>
				<div style="margin-bottom:5px;">
					토크<?=$i?>:
					<input type="text" name="set_robot_stop_torque2<?=$i?>" value="<?php echo $g5['setting']['set_robot_stop_torque2'.$i] ?>" class="frm_input" style="width:50px;"> 이상
					<span style="width:20px;display:inline-block;"></span>
					온도<?=$i?>:
					<input type="text" name="set_robot_stop_temperature2<?=$i?>" value="<?php echo $g5['setting']['set_robot_stop_temperature2'.$i] ?>" class="frm_input" style="width:50px;"> 이상
				</div>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<th scope="row">로봇재시작 설정</th>
			<td colspan="3">
				<?php echo help('로봇 동작이 멈춘 후 다시 재시작할 시간을 분 단위로 설정합니다.') ?>
                로봇1:
				<input type="text" name="set_robot_restart1" value="<?php echo $g5['setting']['set_robot_restart1'] ?>" id="set_monitor_reload" class="frm_input" style="width:50px;"> 분 후
                <div style="height:5px;"></div>
                로봇2:
				<input type="text" name="set_robot_restart2" value="<?php echo $g5['setting']['set_robot_restart2'] ?>" id="set_monitor_reload" class="frm_input" style="width:50px;"> 분 후
			</td>
		</tr>
        <tr>
            <th scope="row">관리자메모</th>
            <td>
                <?php echo help('로봇 설정 관련 메모입니다.') ?>
                <textarea name="set_memo_robot" id="set_memo_super"><?php echo get_text($g5['setting']['set_memo_robot']); ?></textarea>
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
