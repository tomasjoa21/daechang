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

$tq_array = array('tq1'=>'토크1','tq2'=>'토크2','tq3'=>'토크3','tq4'=>'토크4','tq5'=>'토크5','tq6'=>'토크6');
$et_array = array('et1'=>'온도1','et2'=>'온도2','et3'=>'온도3','et4'=>'온도4','et5'=>'온도5','et6'=>'온도6');

$fields = sql_field_names('g5_1_robot_setup');

$sql = " SELECT * FROM g5_1_robot_setup ORDER BY rst_robot_no, rst_type ";
// echo $sql.'<br>';
$result = sql_query($sql,1);
?>

<form name="form01" id="form01" action="./config_form_robot_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="token" value="">

<section id="anc_cf_default">
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>기본설정</caption>
		<colgroup>
			<col class="grid_4">
			<col>
		</colgroup>
		<tbody>
		<?php
		for ($i=0; $row=sql_fetch_array($result); $i++) {
			// print_r2($row);
			$row['rst_type_text'] = ($row['rst_type']=='A') ? '<span style="color:yellow;">경고</span>':'<span style="color:darkorange;">정지</span>';

			//
			${'rst_sleep_time'.$row['rst_robot_no']} = $row['rst_sleep_time'];
		?>
		<tr>
		<input type="hidden" name="rst_idx[<?php echo $i ?>]" value="<?php echo $row['rst_idx'] ?>" id="rst_idx_<?php echo $i ?>">
		<input type="hidden" name="rst_robot_no[<?php echo $i ?>]" value="<?php echo $row['rst_robot_no'] ?>" id="rst_idx_<?php echo $i ?>">
		<input type="hidden" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
			<th scope="row">로봇#<?=$row['rst_robot_no']?> <?=$row['rst_type_text']?> 설정</th>
			<td colspan="3">
				<?php echo help('로봇 작동 '.$row['rst_type_text'].' 기준값을 설정합니다.') ?>
				<?php
				for($j=1;$j<7;$j++) {
				?>
				<div style="margin-bottom:5px;">
					토크<?=$j?>:
					<input type="text" name="rst_tql<?=$j?>[<?=$i?>]" value="<?=$row['rst_tql'.$j]?>" class="tbl_input" style="width:50px;"> 이상
					<span style="width:20px;display:inline-block;"></span>
					온도<?=$j?>:
					<input type="text" name="rst_etl<?=$j?>[<?=$i?>]" value="<?=$row['rst_etl'.$j]?>" class="tbl_input" style="width:50px;"> 이상
				</div>
				<?php
				}
				?>
			</td>
		</tr>
		<?php
		}
		if ($i == 0)
			echo "<tr><td colspan='6' class=\"empty_table\">자료가 없습니다.</td></tr>";
		?>
		<tr>
			<th scope="row">로봇재시작 설정</th>
			<td colspan="3">
				<?php echo help('로봇 동작이 멈춘 후 다시 재시작할 시간을 분 단위로 설정합니다.') ?>
                로봇1:
				<input type="text" name="rst_sleep_time1" value="<?php echo $rst_sleep_time1 ?>" id="set_monitor_reload" class="frm_input" style="width:50px;"> 분 후
                <div style="height:5px;"></div>
                로봇2:
				<input type="text" name="rst_sleep_time2" value="<?php echo $rst_sleep_time2 ?>" id="set_monitor_reload" class="frm_input" style="width:50px;"> 분 후
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

function form01_submit(f) {

    // f.action = "./config_form_robot_update.php";
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
