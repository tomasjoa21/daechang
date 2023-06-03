<?php
$sub_menu = "925900";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$g5['title'] = '통합설비관리설정';
include_once('./_top_menu_setting.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_default">통합설비관리설정</a></li>
    <li><a href="#anc_cf_robot">로봇관련설정</a></li>
    <li><a href="#anc_cf_manager">관리설정</a></li>
</ul>';
?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">
<input type="hidden" name="fname" value="<?=$g5['file_name']?>">

<section id="anc_cf_default">
	<h2 class="h2_frm">기본설정</h2>
	<?php echo $pg_anchor ?>
	
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>기본설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
		<tbody>
		<tr>
			<th scope="row">주조기설정</th>
			<td colspan="3">
				<?php echo help('주조기 번호와 설비DB고유번호(mms고유번호)를 매칭합니다. LPM05=60(17호기), LPM04=61(18호기), LPM03=62(19호기), LPM02=63(20호기)') ?>
				<input type="text" name="set_cast_no" value="<?php echo $g5['setting']['set_cast_no'] ?>" id="set_status" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">한주 압력 번호</th>
			<td colspan="3">
				<input type="text" name="set_data_pressure_no" value="<?php echo $g5['setting']['set_data_pressure_no']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
        </tbody>
		</table>
	</div>
</section>


    
<section id="anc_cf_robot">
    <h2 class="h2_frm">로봇관련설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>로봇관련설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
		<tr>
			<th scope="row">한주 압력 번호</th>
			<td colspan="3">
				<input type="text" name="set_data_pressure_no" value="<?php echo $g5['setting']['set_data_pressure_no']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		</tbody>
		</table>
	</div>
</section>

<section id="anc_cf_manager">
    <h2 class="h2_frm">관리설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc" style="display:none;">
        <p>관리자 설정입니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>관리설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">관리자메모</th>
            <td>
                <?php echo help('관리자 메모입니다.') ?>
                <textarea name="set_memo_system" id="set_memo_super"><?php echo get_text($g5['setting']['set_memo_system']); ?></textarea>
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
