<?php
$sub_menu = "910140";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$g5['title'] = '관리자설정';
include_once('./_top_menu_manager.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 개별 업체 설정은 별도로 가지고 와야 합니다.
// $sql = "SELECT com_idx, set_name, set_value
// 		FROM {$g5['setting_table']}
// 		WHERE com_idx = '".$_SESSION['ss_com_idx']."'
// 			AND set_key = 'manager'
// ";
// $result = sql_query($sql,1);
// for ($i=0; $row=sql_fetch_array($result); $i++) {
// 	// print_r3($row);
//     $g5['setting'][$_SESSION['ss_com_idx'].'_'.$row['set_name']] = $row['set_value'];
// 	${$row['set_name'].'_check'} = 1;
// 	// 원래 공통 변수는 따로 가지고 와야 함
// 	$one = sql_fetch("SELECT set_value FROM {$g5['setting_table']} WHERE com_idx = '0' AND set_name = '".$row['set_name']."' ",1);
//     $g5['setting'][$row['set_name']] = $one['set_value'];

// }



$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_default">기본설정</a></li>
    <li><a href="#anc_cf_sms">SMS 설정</a></li>
</ul>';

if (!$config['cf_icode_server_ip'])   $config['cf_icode_server_ip'] = '211.172.232.124';
if (!$config['cf_icode_server_port']) $config['cf_icode_server_port'] = '7295';

if ($config['cf_sms_use'] && $config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
}
?>
<style>
.check_company {position:absolute;top:10px;right:5px;}
</style>
<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">

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
			<th scope="row">로그인실패 횟수</th>
			<td colspan="3">
				<?php echo help('로그인 실패 허용 횟수를 기입하세요. 예) 5') ?>
				<input type="number" name="mng_loginfail" value="<?php echo (($g5['setting']['mng_loginfail'])?$g5['setting']['mng_loginfail']:'0') ?>" id="mng_loginfail" required class="required frm_input" style="width:60px;">
			</td>
		</tr>
		<tr>
			<th scope="row">재로그인가능시간(분)</th>
			<td colspan="3">
				<?php echo help('로그인실패후 재로그인 가능한 시간 분으로 기입하세요. 예) 10') ?>
				<input type="number" name="mng_relogin_min" value="<?php echo (($g5['setting']['mng_relogin_min'])?$g5['setting']['mng_relogin_min']:'0') ?>" id="mng_relogin_min" required class="required frm_input" style="width:60px;">
			</td>
		</tr>
		<tr>
			<th scope="row">입고(납기)장소</th>
			<td colspan="3">
				<?php echo help('예) a=본사,b=냉천') ?>
				<input type="text" name="mng_input_location" value="<?=$g5['setting']['mng_input_location']?>" id="mng_input_location" required class="required frm_input" style="width:200px;">
			</td>
		</tr>
        <tr>
            <th scope="row"><label for="mng_statics_std">생산통계기준</label></th>
            <td>
                <?php echo help("예) shift=교대기준, date=날짜기준"); ?>
                <select id="mng_statics_std" name="mng_statics_std">
                    <option value="shift">교대기준</option>
                    <option value="date">날짜기준</option>
                </select>
                <script>
                var statics_std = '<?=(($g5['setting']['mng_statics_std'])?$g5['setting']['mng_statics_std']:'shift')?>';
                $('#mng_statics_std').val(statics_std);
                </script>
            </td>
        </tr>
        </tbody>
		</table>
	</div>
</section>
   
<section id="anc_cf_sms">
	<h2 class="h2_frm">SMS 설정</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SMS 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="mng_sms_use">SMS 사용</label></th>
            <td>
                <?php echo help("SMS  서비스 회사를 선택하십시오. 서비스 회사를 선택하지 않으면, SMS 발송 기능이 동작하지 않습니다.<br>뿌리오는 무료 문자메세지 발송 테스트 환경을 지원합니다.<br><a href=\"".G5_USER_ADMIN_URL."/config_form.php#anc_cf_sms\">기본환경설정 &gt; SMS</a> 설정과 동일합니다."); ?>
                <select id="mng_sms_use" name="mng_sms_use">
                    <option value="" <?php echo get_selected($g5['setting']['mng_sms_use'], ''); ?>>사용안함</option>
                    <option value="ppurio" <?php echo get_selected($g5['setting']['mng_sms_use'], 'icode'); ?>>뿌리오문자서비스</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mng_sms_type">SMS 전송유형</label></th>
            <td>
                <?php echo help("전송유형을 SMS로 선택하시면 최대 80바이트까지 전송하실 수 있으며<br>LMS로 선택하시면 90바이트 이하는 SMS로, 그 이상은 1500바이트까지 LMS로 전송됩니다.<br>요금은 건당 SMS는 12원, LMS는 32원, MMS는 100원입니다."); ?>
                <select id="mng_sms_type" name="mng_sms_type">
                    <option value="" <?php echo get_selected($g5['setting']['mng_sms_type'], ''); ?>>SMS</option>
                    <option value="LMS" <?php echo get_selected($g5['setting']['mng_sms_type'], 'LMS'); ?>>LMS</option>
                    <option value="MMS" <?php echo get_selected($g5['setting']['mng_sms_type'], 'MMS'); ?>>MMS</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mng_sms_hp">관리자 휴대폰번호</label></th>
            <td>
                <?php echo help("주문서작성시 쇼핑몰관리자가 문자메세지를 받아볼 번호를 숫자만으로 입력하세요. 예) 0101234567"); ?>
                <input type="text" name="mng_sms_hp" value="<?php echo get_sanitize_input($g5['setting']['mng_sms_hp']); ?>" id="mng_sms_hp" class="frm_input" size="20">
            </td>
        </tr>
        <tr class="ppurio_version">
            <th scope="row"><label for="mng_ppurio_id">뿌리오 회원아이디</label></th>
            <td>
                <?php echo help("뿌리오에서 사용하시는 회원아이디를 입력합니다."); ?>
                <input type="text" name="mng_ppurio_id" value="<?php echo get_sanitize_input($g5['setting']['mng_ppurio_id']); ?>" id="mng_ppurio_id" class="frm_input" size="20">
            </td>
        </tr>
        <tr class="ppurio_version">
            <th scope="row"><label for="mng_ppurio_pw">뿌리오 비밀번호</label></th>
            <td>
                <?php echo help("뿌리오에서 사용하시는 비밀번호를 입력합니다."); ?>
                <input type="password" name="mng_ppurio_pw" value="<?php echo get_sanitize_input($g5['setting']['mng_ppurio_pw']); ?>" class="frm_input" id="mng_ppurio_pw">
            </td>
        </tr>
        <tr class="ppurio_json_version">
            <th scope="row"><label for="mng_ppurio_token_key">뿌리오 토큰키<br>(JSON버전)</label></th>
            <td>
                <?php echo help("뿌리오 JSON 버전의 경우 뿌리오 토큰키를 입력시 실행됩니다.<br>SMS 전송유형을 LMS로 설정시 90바이트 이내는 SMS, 90 ~ 2000 바이트는 LMS 그 이상은 절삭 되어 LMS로 발송됩니다."); ?>
                <input type="text" name="mng_ppurio_token_key" value="<?php echo get_sanitize_input($g5['setting']['mng_ppurio_token_key']); ?>" id="mng_ppurio_token_key" class="frm_input" size="40">
                <?php echo help("뿌리오 사이트 -> 토큰키관리 메뉴에서 생성한 토큰키를 입력합니다."); ?>
                <br>
                서버아이피 : <?php echo $_SERVER['SERVER_ADDR']; ?>
            </td>
        </tr>
         </tbody>
        </table>
    </div>

	<section id="scf_sms_pre">
        <h3>사전에 정의된 SMS프리셋</h3>
        <div class="local_desc01 local_desc">
            <dl>
                <dt>생산계획 단체</dt>
                <dd>{제품명} {품목코드} {회사명}</dd>
                <dt>완제품 개별</dt>
                <dd>{제품명} {보낸분} {받는분} {품목코드} {지시수량}</dd>
                <dt>각제품별</dt>
                <dd>{제품명} {품목코드} {지시수량}</dd>
                <dt>기타문자</dt>
                <dd>{제품명} {품목코드} {기타내용}</dd>
            </dl>
           <p><?php echo help('주의! 80 bytes 까지만 전송됩니다. (영문 한글자 : 1byte , 한글 한글자 : 2bytes , 특수문자의 경우 1 또는 2 bytes 임)'); ?></p>
        </div>

        <div id="scf_sms">
            <?php
            $scf_sms_title = array (1=>"생산계획 단체발송", "생산계획 완제품 개별 발송", "생산계획 제품별 발송", "기타문자2 발송", "기타문자2 발송");
            for ($i=1; $i<=5; $i++) {
            ?>
            <section class="scf_sms_box">
                <h4><?php echo $scf_sms_title[$i]; ?></h4>
                <input type="checkbox" name="mng_sms_use<?php echo $i; ?>" value="1" id="mng_sms_use<?php echo $i; ?>" <?php echo ($g5['setting']["mng_sms_use".$i] ? " checked" : ""); ?>>
                <label for="mng_sms_use<?php echo $i; ?>"><span class="sound_only"><?php echo $scf_sms_title[$i]; ?></span>사용</label>
                <div class="scf_sms_img">
                    <textarea id="mng_sms_cont<?php echo $i; ?>" name="mng_sms_cont<?php echo $i; ?>" ONKEYUP="byte_check('mng_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');"><?php echo html_purifier($g5['setting']['mng_sms_cont'.$i]); ?></textarea>
                </div>
                <span id="byte<?php echo $i; ?>" class="scf_sms_cnt">0 / 80 바이트</span>
            </section>

            <script>
            byte_check('mng_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');
            </script>
            <?php } ?>
        </div>
    </section>
</section>

<div class="btn_fixed_top btn_confirm">
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>
$(function(){

});

function fconfigform_submit(f) {

    <?php ;//echo get_editor_js("mng_msg_content"); ?>
    <?php ;//echo chk_editor_js("mng_msg_content"); ?>

    f.action = "./config_manager_form_update.php";
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
