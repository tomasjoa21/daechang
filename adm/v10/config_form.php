<?php
$sub_menu = "910110";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if(!$config['cf_faq_skin']) $config['cf_faq_skin'] = "basic";
if(!$config['cf_mobile_faq_skin']) $config['cf_mobile_faq_skin'] = "basic";

$g5['title'] = '솔루션설정';
include_once('./_top_menu_setting.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 개별 업체 설정은 별도로 가지고 와야 합니다.
$sql = "SELECT com_idx, set_name, set_value
		FROM {$g5['setting_table']}
		WHERE com_idx = '".$_SESSION['ss_com_idx']."'
			AND set_key = 'site'
";
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
	// print_r3($row);
    $g5['setting'][$row['set_name'].'_'.$_SESSION['ss_com_idx']] = $row['set_value'];
	${$row['set_name'].'_check'} = 1;
	// 공통 변수는 따로 가지고 와야 되네...
	$one = sql_fetch("SELECT set_value FROM {$g5['setting_table']} WHERE com_idx = '0' AND set_name = '".$row['set_name']."' ",1);
    $g5['setting'][$row['set_name']] = $one['set_value'];

}
// echo $g5['setting']['set_itm_status_13'].BR;



$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_default">기본설정</a></li>
    <li><a href="#anc_cf_message">메시지설정</a></li>
    <li><a href="#anc_cf_secure">관리설정</a></li>
</ul>';

if (!$config['cf_icode_server_ip'])   $config['cf_icode_server_ip'] = '211.172.232.124';
if (!$config['cf_icode_server_port']) $config['cf_icode_server_port'] = '7295';

if ($config['cf_sms_use'] && $config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
}
?>
<style>
.tbl_wrap td {position:relative;}
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
			<col class="grid_4">
			<col>
		</colgroup>
		<tbody>
		<tr>
			<th scope="row">주조기설정</th>
			<td colspan="3">
				<?php echo help('주조기 이름 & DB고유번호(mms번호) 매칭 ex) LPM05=58(17호기), LPM04=59(18호기), LPM03=60(19호기), LPM02=61(20호기)<br>MES db index는 MMS 관리번호를 참조합니다.') ?>
				<input type="text" name="set_cast_no" value="<?php echo $g5['setting']['set_cast_no'] ?>" id="set_status" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">디폴트상태값</th>
			<td colspan="3">
				<?php echo help('pending=대기,auto-draft=자동저장,ok=정상,hide=숨김,trash=삭제') ?>
				<input type="text" name="set_status" value="<?php echo $g5['setting']['set_status'] ?>" id="set_status" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">모니터별업로드이미지개수</th>
			<td colspan="3">
				<?php echo help('예) 3 : 최대 3장 업로드 가능') ?>
				<input type="text" name="set_monitor_cnt" value="<?php echo $g5['setting']['set_monitor_cnt'] ?>" id="set_monitor_cnt" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">모니터이미지로테이션시간</th>
			<td colspan="3">
				<?php echo help('예) 3000 : 3초') ?>
				<input type="text" name="set_monitor_time" value="<?php echo $g5['setting']['set_monitor_time'] ?>" id="set_monitor_time" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">모니터페이지리로딩간격시간</th>
			<td colspan="3">
				<?php echo help('예) 10000 : 10초') ?>
				<input type="text" name="set_monitor_reload" value="<?php echo $g5['setting']['set_monitor_reload'] ?>" id="set_monitor_reload" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">분류(카테고리) terms</th>
			<td colspan="3">
				<?php echo help('') ?>
				<input type="text" name="set_taxonomies" value="<?php echo $g5['setting']['set_taxonomies'] ?>" id="set_taxonomies" required class="required frm_input" style="width:80%;">
			</td>
		</tr>
		<tr>
			<th scope="row">회원레벨명 mb_level</th>
			<td colspan="3">
				<input type="text" name="set_mb_levels" value="<?php echo $g5['setting']['set_mb_levels'] ?>" id="set_mb_levels" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">직책(권한) mb_1</th>
			<td colspan="3">
				<?php echo help('1=지원팀,4=팀원,6=팀장,8=센터장,10=부서장,20=운영관리') ?>
				<input type="text" name="set_mb_positions" value="<?php echo $g5['setting']['set_mb_positions'] ?>" id="set_mb_positions" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">직급(직위) mb_3</th>
			<td colspan="3">
				<?php echo help('2=파트타임............50=팀장,60=과장,70=차장,80=부장,90=센터장,100=본부장,110=실장,120=이사,130=부사장,140=대표') ?>
				<input type="text" name="set_mb_ranks" value="<?php echo $g5['setting']['set_mb_ranks'] ?>" id="set_mb_ranks" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">사원유형</th>
			<td colspan="3">
				<?php echo help('normal=일반사원,driver=운송기사') ?>
				<input type="text" name="set_cmm_type" value="<?php echo $g5['setting']['set_cmm_type'] ?>" id="set_cmm_type" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">업체분류</th>
			<td colspan="3">
				<?php echo help('electricity=전기,electronic=전자,facility=설비,food=식품,parts=자재') ?>
				<input type="text" name="set_com_type" value="<?php echo $g5['setting']['set_com_type'] ?>" id="set_com_type" required class="required frm_input" style="width:90%;">
			</td>
		</tr>
		<tr>
			<th scope="row">거래처분류</th>
			<td colspan="3">
				<input type="text" name="set_cst_type" value="<?php echo $g5['setting']['set_cst_type'] ?>" required class="required frm_input" style="width:90%;">
			</td>
		</tr>
		<tr>
			<th scope="row">업체상태값 설정</th>
			<td colspan="3">
				<?php echo help('ok=정상,pending=대기,trash=휴지통,delete=삭제,hide=숨김,prohibit=영업금지업체') ?>
				<input type="text" name="set_com_status" value="<?php echo $g5['setting']['set_com_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
        <tr>
            <th scope="row">업체-영업자 상태값 설정</th>
            <td colspan="3">
                <input type="text" name="set_cms_status" value="<?php echo $g5['setting']['set_cms_status']; ?>" class="frm_input" style="width:60%;">
            </td>
        </tr>
        <tr>
            <th scope="row">업체-회원 상태 설정</th>
            <td colspan="3">
                <input type="text" name="set_cmm_status" value="<?php echo $g5['setting']['set_cmm_status']; ?>" class="frm_input" style="width:60%;">
            </td>
        </tr>
		<tr>
			<th scope="row">정산 업데이트 기준일</th>
			<td colspan="3">
				<?php echo help('디폴트(한달): -1 MONTH (설정 기간 이후의 매출정산을 업데이트한다.) -1 DAY, -1 WEEK, -2 MONTH 등등'); ?>
				<input type="text" name="set_sales_update_interval" value="<?php echo $g5['setting']['set_sales_update_interval']; ?>" class="frm_input" style="width:80%;">
			</td>
		</tr>
        <tr>
            <th scope="row">IMP 묶음단위</th>
            <td>
                <input type="text" name="set_imp_count" value="<?php echo $g5['setting']['set_imp_count']; ?>" class="frm_input" style="width:30px;"> 개
            </td>
        </tr>
        <tr>
            <th scope="row">데이타 타입</th>
            <td>
				<?php echo help('1. 정.온도(도), temperature: 범위 -20~1500 / 5초
2. 비.토크(%), torque: -300~300 / 1초~30
3. 비.전류(A), current: 0~1000 / 1초~30
4. 비.전압(V), voltage: 0~1000 / 1초~30
5. 비.진동(Hz), vibration: 20~2000
6. 비.소리(dB), sound: 0~150
7. 정.습도(%), humidity: 0~100
8. 비.압력(psi), pressure: 0~100
9. 비.속도(r/min), rpm: 0~3000
...
...
태그값은 계속 추가될 수 있습니다. 100만개~'); ?>
                <input type="text" name="set_data_type" value="<?php echo $g5['setting']['set_data_type']; ?>" class="frm_input" style="width:80%;">
            </td>
        </tr>
		<tr>
			<th scope="row">데이타 그룹 설정</th>
			<td colspan="3">
				<?php echo help('err=에러,pre=예지,run=가동시간,product=생산,mea=측정...(err+pre=에러테이블, run+product=가동테이블, mea=측정테이블)'); ?>
				<input type="text" name="set_data_group" value="<?php echo $g5['setting']['set_data_group']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">데이타 그래프 값</th>
			<td colspan="3">
				<?php echo help('각 데이터 그룹별로 그래프 초기값을 설정하세요. 3개값을 쉽표로 구분하여 입력하세요.
형식: <span class="color_red">검색항목, 단위값, 갯수, 값타입</span> 형식으로 입력합니다. 아래 예제를 참고하세요.
minute,5,600 = 분단위,5분단위,5분*600개표시=50시간,avg(평균)
second,10,600 = 초단위,10초단위,10초*600개=100분,sum(합계)
monthly,1,12 = 월별,1개월단위,12개월치,sum(합계)
daily,1,30 = 일별,1일단위,30일치,sum(합계)
yearly,1,10 = 연도별,1년단위,10년치,sum(합계)'); ?>
                <?php
                $set_values = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_data_group']));
                foreach ($set_values as $set_value) {
                    list($key, $value) = explode('=', trim($set_value));
                    echo ' <input type="text" name="set_graph_'.$key.'" value="'.$g5['setting']['set_graph_'.$key].'" class="frm_input" style="width:150px;margin-bottom:5px;"> ('.$value.' <span class="color_gray">'.$key.'</span> 그래프 초기값)<br>'.PHP_EOL;
                }
                unset($set_values);unset($set_value);
                ?>
			</td>
		</tr>
		<tr>
			<th scope="row">그룹별 JSON 호출파일</th>
			<td colspan="3">
				<?php echo help('각 데이터 그룹별로 호출하는 JSON파일명을 입력하세요.'); ?>
                <?php
                $set_values = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_data_group']));
                foreach ($set_values as $set_value) {
                    list($key, $value) = explode('=', trim($set_value));
                    echo ' <input type="text" name="set_json_file_'.$key.'" value="'.$g5['setting']['set_json_file_'.$key].'" class="frm_input" style="width:150px;margin-bottom:5px;"> ('.$value.' <span class="color_gray">'.$key.'</span>)<br>'.PHP_EOL;
                }
                unset($set_values);unset($set_value);
                ?>
			</td>
		</tr>
		<tr>
			<th scope="row">데이타 수집 기준</th>
			<td colspan="3">
				<?php echo help('shift=교대기준,date=날짜기준 (기본 디폴트 = shift, 설정값이 없으면 교대기준이라고 봅니다.)'); ?>
				<input type="text" name="set_mms_set_data" value="<?php echo $g5['setting']['set_mms_set_data']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
        <tr>
            <th scope="row">그래프 시간단위</th>
            <td>
				<?php echo help('그래프에서 시간 검색 범위를 선택(timepicker)할 때의 분단위 간격을 숫자로 입력하세요.'); ?>
                <input type="text" name="set_time_step" value="<?php echo $g5['setting']['set_time_step']; ?>" class="frm_input" style="width:30px;"> 분
            </td>
        </tr>
        <tr>
            <th scope="row">그래프 좌표갯수 최대</th>
            <td>
				<?php echo help('좌표갯수 max값에 따라 그래프 로딩시간이 오래 걸릴 수 있습니다. 그래프의 로딩 시간을 봐 가면서 좌표갯수 max값을 조정해 주세요. '); ?>
                <input type="text" name="set_graph_max" value="<?php echo $g5['setting']['set_graph_max']; ?>" class="frm_input" style="width:40px;"> 개
            </td>
        </tr>
		<tr>
			<th scope="row">그래프 단위 설정</th>
			<td colspan="3">
				<?php echo help('daily=일별,weekly=주간별,monthly=월별,yearly=연도별,minute=분,second=초'); ?>
				<input type="text" name="set_graph_unit" value="<?php echo $g5['setting']['set_graph_unit']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">그래프 단위별 초기값</th>
			<td colspan="3">
				<?php echo help('각 그래프 단위별로 디폴트 단위 갯수값을 설정하세요. 2개값을 쉽표로 구분하여 입력하세요.
형식: <span class="color_red">단위값, 갯수</span> 형식으로 입력합니다. 아래 예제를 참고하세요.
5,600 = 분단위인 경우 5분단위 600개이므로 5분*600개표시=50시간이 그래프 초기 범위가 됩니다.
30,200 = 분단위인 경우 30분단위 200개이므로 30분*200개표시=100시간이 그래프 초기 범위가 됩니다.
1,12 = 월별인 경우 1개월 단위 12개=1년이 그래프 초기 범위가 됩니다.
1,31 = 일별인 경우 1일단위 31개=1달이 그래프 초기 범위가 됩니다.'); ?>
                <?php
                $set_values = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_graph_unit']));
                foreach ($set_values as $set_value) {
                    list($key, $value) = explode('=', trim($set_value));
                    echo ' <input type="text" name="set_graph_'.$key.'" value="'.$g5['setting']['set_graph_'.$key].'" class="frm_input" style="width:60px;margin-bottom:5px;"> ('.$value.' <span class="color_gray">'.$key.'</span> 단위 선택 시 초기값)<br>'.PHP_EOL;
                }
                unset($set_values);unset($set_value);
                ?>
			</td>
		</tr>
        <tr>
            <th scope="row">디폴트업체번호</th>
            <td>
				<?php echo help('수퍼관리자가 로그인할 때 디폴트 업체 번호입니다. (com_idx)'); ?>
                <input type="text" name="set_com_idx" value="<?php echo $g5['setting']['set_com_idx']; ?>" class="frm_input" style="width:40px;">
            </td>
        </tr>
		<tr>
			<th scope="row">대시보드 그리드 너비/높이<br>사이즈 - 단위(%)</th>
			<td colspan="3">
				<?php echo help('1=25,2=50,3=75,4=100'); ?>
				<input type="text" name="set_pkr_size" value="<?php echo $g5['setting']['set_pkr_size']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">대시보드 그리드 패딩<br>(padding)<br>사이즈 - 단위(px)</th>
			<td colspan="3">
				<?php echo help('3=3px,4=4px,5=5px,6=6px,7=7px,8=8px,9=9px,10=10px'); ?>
				<input type="text" name="set_pkr_padding" value="<?php echo $g5['setting']['set_pkr_padding']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">코드타입설정</th>
			<td colspan="3">
				<?php echo help('r=기록, a=알람, p=예지'); ?>
				<input type="text" name="set_cod_type" value="<?php echo $g5['setting']['set_cod_type']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">코드그룹명 설정</th>
			<td colspan="3">
				<?php echo help('err=일반알림, pre=PLC예지'); ?>
				<input type="text" name="set_cod_group" value="<?php echo $g5['setting']['set_cod_group']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">코드상태 설정</th>
			<td colspan="3">
				<?php echo help('stop=중지,ok=정상') ?>
				<input type="text" name="set_cod_status" value="<?php echo $g5['setting']['set_cod_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">예지주기 설정</th>
			<td colspan="3">
				<?php echo help('3600=1시간, 86400=1일, 604800=주간, 2592000=월간') ?>
				<input type="text" name="set_cod_interval" value="<?php echo $g5['setting']['set_cod_interval']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">메시지발송수단</th>
			<td colspan="3">
				<?php echo help('email=이메일, sms=문자, push=푸시') ?>
				<input type="text" name="set_send_type" value="<?php echo $g5['setting']['set_send_type']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">가동상태</th>
			<td colspan="3">
				<input type="text" name="set_run_status" value="<?php echo $g5['setting']['set_run_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
            <th scope="row">ONESIGNAL APP ID</th>
            <td colspan="3">
                <input type="text" name="set_onesignal_id" value="<?php echo $g5['setting']['set_onesignal_id']; ?>" class="frm_input" style="width:60%;">
            </td>
        </tr>
		<tr>
            <th scope="row">ONESIGNAL REST API KEY</th>
            <td colspan="3">
                <?php echo help('OneSignal > Settings > Keys & IDs : REST API KEY'); ?>
                <input type="text" name="set_onesignal_key" value="<?php echo $g5['setting']['set_onesignal_key']; ?>" class="frm_input" style="width:60%;">
            </td>
        </tr>
		<tr>
            <th scope="row">1차분류(카테고리1)</th>
            <td>
                <?php echo help('1차분류를 ex)cat1_name=카테고리1설명 이와같이 작성하고 구분은 줄바꿈으로 세로로 나열하세요.<br>(가능한 기존의 작성 순서를 지켜주시고 만약 순서를 변경 하였을시 각 제품(BOM)의 설정된 카테고리도 변경해야 할 수 있습니다.)') ?>
                <textarea name="set_cat_1" id="set_cat_1" style="width:50%;height:200px;"><?php echo get_text($g5['setting']['set_cat_1']); ?></textarea>
            </td>
        </tr>
		<tr>
            <th scope="row">2차분류(카테고리2)</th>
            <td>
                <?php echo help('2차분류를 ex)cat2_name=카테고리2설명 이와같이 작성하고 구분은 줄바꿈으로 세로로 나열하세요.<br>(가능한 기존의 작성 순서를 지켜주시고 만약 순서를 변경 하였을시 각 제품(BOM)의 설정된 카테고리도 변경해야 할 수 있습니다.)') ?>
                <textarea name="set_cat_2" id="set_cat_2" style="width:50%;height:200px;"><?php echo get_text($g5['setting']['set_cat_2']); ?></textarea>
            </td>
        </tr>
		<tr>
            <th scope="row">3차분류(카테고리3)</th>
            <td>
                <?php echo help('3차분류를 ex)cat3_name=카테고리3설명 이와같이 작성하고 구분은 줄바꿈으로 세로로 나열하세요.<br>(가능한 기존의 작성 순서를 지켜주시고 만약 순서를 변경 하였을시 각 제품(BOM)의 설정된 카테고리도 변경해야 할 수 있습니다.)') ?>
                <textarea name="set_cat_3" id="set_cat_3" style="width:50%;height:200px;"><?php echo get_text($g5['setting']['set_cat_3']); ?></textarea>
            </td>
        </tr>
		<tr>
            <th scope="row">4차분류(카테고리4)</th>
            <td>
                <?php echo help('4차분류를 ex)cat4_name=카테고리4설명 이와같이 작성하고 구분은 줄바꿈으로 세로로 나열하세요.<br>(가능한 기존의 작성 순서를 지켜주시고 만약 순서를 변경 하였을시 각 제품(BOM)의 설정된 카테고리도 변경해야 할 수 있습니다.)') ?>
                <textarea name="set_cat_4" id="set_cat_4" style="width:50%;height:200px;"><?php echo get_text($g5['setting']['set_cat_4']); ?></textarea>
            </td>
        </tr>
		<tr>
			<th scope="row">메뉴권한종류 mb_8</th>
			<td colspan="3">
				<?php echo help('adm=총괄관리권한,adm_production=생산관리권한,adm_quality=품질관리권한,normal=일반사원권한') ?>
				<input type="text" name="set_mb_auth" value="<?php echo $g5['setting']['set_mb_auth'] ?>" id="set_mb_auth" required class="required frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
            <th scope="row">총괄관리메뉴권한</th>
            <td>
                <?php echo help('총괄관리자의 메뉴 접근권한입니다.') ?>
                <textarea name="set_admin_auth" id="set_admin_auth" style="width:50%;"><?php echo get_text($g5['setting']['set_admin_auth']); ?></textarea>
            </td>
        </tr>
		<tr>
            <th scope="row">생산관리메뉴권한</th>
            <td>
                <?php echo help('생산관리자의 메뉴 접근권한입니다.') ?>
                <textarea name="set_admin_production_auth" id="set_admin_production_auth" style="width:50%;"><?php echo get_text($g5['setting']['set_admin_production_auth']); ?></textarea>
            </td>
        </tr>
		<tr>
            <th scope="row">품질관리메뉴권한</th>
            <td>
                <?php echo help('품질관리자의 메뉴 접근권한입니다.') ?>
                <textarea name="set_admin_quality_auth" id="set_admin_quality_auth" style="width:50%;"><?php echo get_text($g5['setting']['set_admin_quality_auth']); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">사원 메뉴권한</th>
			<td colspan="3">
                <?php echo help('사원이 등록될 때 디폴트 메뉴 접근권한입니다.') ?>
                <textarea name="set_employee_auth" id="set_employee_auth" style="width:50%;"><?php echo get_text($g5['setting']['set_employee_auth']); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 메뉴권한</th>
			<td colspan="3">
                <?php echo help('모바일 회원등록될 때 디폴트 메뉴 접근권한입니다.') ?>
                <textarea name="set_mobile_auth" id="set_mobile_auth" style="width:50%;"><?php echo get_text($g5['setting']['set_mobile_auth']); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">품질정보입력시차</th>
            <td>
				<?php echo help('교대 시간이 바뀌어도 시차 간격을 두고 품질 정보를 입력합니다.'); ?>
                <input type="text" name="set_quality_input_time" value="<?php echo $g5['setting']['set_quality_input_time']; ?>" class="frm_input" style="width:40px;"> 시간
            </td>
        </tr>
		<tr>
			<th scope="row">설비상태 설정</th>
			<td colspan="3">
				<?php echo help('quality=품질, offwork=비가동'); ?>
				<input type="text" name="set_mst_type" value="<?php echo $g5['setting']['set_mst_type']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">설비지그 상태 설정</th>
			<td colspan="3">
				<?php echo help('ok=카운터, no=무시, trash=휴지통'); ?>
				<input type="text" name="set_boj_status" value="<?php echo $g5['setting']['set_boj_status']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">로그인 첫페이지</th>
			<td colspan="3">
				<?php echo help('index.php=대시보드, manual_quality_input.php=품질정보입력페이지'); ?>
				<input type="text" name="set_first_page" value="<?php echo $g5['setting']['set_first_page']; ?>" class="frm_input" style="width:70%;">
			</td>
		</tr>
        <tr>
            <th scope="row">비가동정보입력시차</th>
            <td>
				<?php echo help('설정 시간 이전의 비가동 정보는 입력할 수 없습니다.'); ?>
                <input type="text" name="set_downtime_input_time" value="<?php echo $g5['setting']['set_downtime_input_time']; ?>" class="frm_input" style="width:40px;"> 시간
            </td>
        </tr>
		<tr>
			<th scope="row">원가설정타입</th>
			<td colspan="3">
				<?php echo help('electricity=전기, consumable=소모품, oil=장비유류대, worker=현장작업자, engineer=장비기사'); ?>
				<input type="text" name="set_csc_type" value="<?php echo $g5['setting']['set_csc_type']; ?>" class="frm_input" style="width:70%;">
			</td>
		</tr>
		<tr>
			<th scope="row">발주타입</th>
			<td colspan="3">
				<?php echo help('normal=일반,sagub=사급'); ?>
				<input type="text" name="set_mto_type" value="<?php echo $g5['setting']['set_mto_type']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_mto_type_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_mto_type_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_mto_type_check)?'':'none'?>;">
                <input type="hidden" name="set_mto_type_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_mto_type_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_mto_type_check)?'checked':''?> id="set_mto_type_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_mto_type_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_mto_type_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_mto_type_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_mto_type_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_mto_type_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">발주상태</th>
			<td colspan="3">
				<?php echo help('pending=대기,ok=발주완료,cancel=취소,trash=삭제'); ?>
				<input type="text" name="set_mto_status" value="<?php echo $g5['setting']['set_mto_status']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_mto_status_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_mto_status_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_mto_status_check)?'':'none'?>;">
                <input type="hidden" name="set_mto_status_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_mto_status_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_mto_status_check)?'checked':''?> id="set_mto_status_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_mto_status_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_mto_status_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_mto_status_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_mto_status_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_mto_status_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">발주제품상태</th>
			<td colspan="3">
				<?php echo help('pending=대기,ok=발주완료,ready=준비,input=입고완료,reject=반려,cancel=취소,trash=삭제'); ?>
				<input type="text" name="set_moi_status" value="<?php echo $g5['setting']['set_moi_status']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_moi_status_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_moi_status_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_moi_status_check)?'':'none'?>;">
                <input type="hidden" name="set_moi_status_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_moi_status_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_moi_status_check)?'checked':''?> id="set_moi_status_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_moi_status_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_moi_status_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_moi_status_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_moi_status_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_moi_status_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">BOM타입</th>
			<td colspan="3">
				<?php echo help('product=완성품,half=반제품,material=자재'); ?>
				<input type="text" name="set_bom_type" value="<?php echo $g5['setting']['set_bom_type']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_bom_type_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_bom_type_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_bom_type_check)?'':'none'?>;">
                <input type="hidden" name="set_bom_type_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_bom_type_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_bom_type_check)?'checked':''?> id="set_bom_type_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_bom_type_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_bom_type_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_bom_type_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_bom_type_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_bom_type_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">자재타입</th>
			<td colspan="3">
				<?php echo help('material=자재,goods=상품'); ?>
				<input type="text" name="set_mtr_type" value="<?php echo $g5['setting']['set_mtr_type']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_mtr_type_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_mtr_type_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_mtr_type_check)?'':'none'?>;">
                <input type="hidden" name="set_mtr_type_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_mtr_type_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_mtr_type_check)?'checked':''?> id="set_mtr_type_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_mtr_type_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_mtr_type_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_mtr_type_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_mtr_type_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_mtr_type_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">재고타입</th>
			<td colspan="3">
				<?php echo help('product=완성품,half=반제품,goods=상품'); ?>
				<input type="text" name="set_itm_type" value="<?php echo $g5['setting']['set_itm_type']; ?>" class="frm_input" style="width:70%;">
				<input type="text" name="set_itm_type_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_itm_type_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:70%;margin-top:5px;display:<?=($set_itm_type_check)?'':'none'?>;">
                <input type="hidden" name="set_itm_type_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_itm_type_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_itm_type_check)?'checked':''?> id="set_itm_type_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_itm_type_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_itm_type_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_itm_type_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_itm_type_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_itm_type_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">BOM구성 표시</th>
			<td colspan="3">
				<?php echo help('제품사양 정보 목록에서 BOM 구조를 표시할 BOM타입을 입력하세요. 쉼표로 구분하고 영문만 입력하세요. ex)product,half '); ?>
				<input type="text" name="set_bom_type_display" value="<?php echo $g5['setting']['set_bom_type_display']; ?>" class="frm_input" style="width:70%;">
			</td>
		</tr>
		<tr>
			<th scope="row">BOM상태</th>
			<td colspan="3">
				<input type="text" name="set_bom_status" value="<?php echo $g5['setting']['set_bom_status']; ?>" class="frm_input" style="width:70%;">
			</td>
		</tr>
        <tr>
            <th scope="row">KPI 서브메뉴</th>
			<td colspan="3">
                <?php echo help('KPI 통계 페이지 서브메뉴 설정입니다.') ?>
                <textarea name="set_kpi_menu" id="set_kpi_menu" style="width:70%;"><?php echo get_text($g5['setting']['set_kpi_menu']); ?></textarea>
            </td>
        </tr>
		<tr>
			<th scope="row">태그구분</th>
			<td colspan="3">
				<?php echo help('quality=품질, offwork=비가동'); ?>
				<input type="text" name="set_tgc_type" value="<?php echo $g5['setting']['set_tgc_type']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">태그예지상태</th>
			<td colspan="3">
				<?php echo help('pending=대기, stop=중지, ok=설정완료, tassh=휴지통'); ?>
				<input type="text" name="set_tgc_status" value="<?php echo $g5['setting']['set_tgc_status']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">태그예지주기 설정</th>
			<td colspan="3">
				<?php echo help('60=1분, 600=10분, 1800=30분, 3600=1시간, 86400=1일, 604800=주간') ?>
				<input type="text" name="set_tgc_interval" value="<?php echo $g5['setting']['set_tgc_interval']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">태그 값설정 기준</th>
			<td colspan="3">
				<?php echo help('>:초과, >=:이상, <=:이하, <:미만') ?>
				<input type="text" name="set_tgc_minmax" value="<?php echo $g5['setting']['set_tgc_minmax']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">문자발송 번호</th>
			<td colspan="3">
				<?php echo help('아이코드에 등록된 발신자 번호') ?>
				<input type="text" name="set_from_number" value="<?php echo $g5['setting']['set_from_number']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">임계치 문자설정</th>
			<td colspan="3">
				<?php echo help('임계치 변수 문자 설정입니다.') ?>
				<input type="text" name="set_tgc_range_text1" value="<?php echo $g5['setting']['set_tgc_range_text1']; ?>" class="frm_input" style="width:60%;">
				<br>
				<input type="text" name="set_tgc_range_text2" value="<?php echo $g5['setting']['set_tgc_range_text2']; ?>" class="frm_input" style="width:60%;margin-top:3px;">
			</td>
		</tr>
		<tr>
			<th scope="row">작업자타입</th>
			<td colspan="3">
				<input type="text" name="set_bmw_type" value="<?php echo $g5['setting']['set_bmw_type']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">수주상태</th>
			<td colspan="3">
				<input type="text" name="set_ori_status" value="<?php echo $g5['setting']['set_ori_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">출하상태</th>
			<td colspan="3">
				<input type="text" name="set_shp_status" value="<?php echo $g5['setting']['set_shp_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">생산계획상태</th>
			<td colspan="3">
				<input type="text" name="set_prd_status" value="<?php echo $g5['setting']['set_prd_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">재고상태</th>
			<td colspan="3">
				<input type="text" name="set_itm_status" value="<?php echo $g5['setting']['set_itm_status']; ?>" class="frm_input" style="width:80%;">
				<input type="text" name="set_itm_status_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_itm_status_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:80%;margin-top:5px;display:<?=($set_itm_status_check)?'':'none'?>;">
                <input type="hidden" name="set_itm_status_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_itm_status_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_itm_status_check)?'checked':''?> id="set_itm_status_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_itm_status_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_itm_status_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_itm_status_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_itm_status_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_itm_status_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">불량타입</th>
			<td colspan="3">
				<input type="text" name="set_defect_type" value="<?php echo $g5['setting']['set_defect_type']; ?>" class="frm_input" style="width:80%;">
			</td>
		</tr>
		<tr>
			<th scope="row">자재상태</th>
			<td colspan="3">
				<input type="text" name="set_mtr_status" value="<?php echo $g5['setting']['set_mtr_status']; ?>" class="frm_input" style="width:80%;">
				<input type="text" name="set_mtr_status_<?=$_SESSION['ss_com_idx']?>" value="<?=$g5['setting']['set_mtr_status_'.$_SESSION['ss_com_idx']]?>" class="frm_input" style="width:80%;margin-top:5px;display:<?=($set_mtr_status_check)?'':'none'?>;">
                <input type="hidden" name="set_mtr_status_<?=$_SESSION['ss_com_idx']?>_check" value="<?=($set_mtr_status_check)?'1':'0'?>">
                <label class="check_company"><input type="checkbox" <?=($set_mtr_status_check)?'checked':''?> id="set_mtr_status_check"> 개별설정</label>
                <script>
                $(document).on('click','#set_mtr_status_check',function(e){
                    if($(this).is(':checked')) {
						$('input[name=set_mtr_status_<?=$_SESSION['ss_com_idx']?>_check]').val(1);
						$('input[name=set_mtr_status_<?=$_SESSION['ss_com_idx']?>').show();
					}
                    else {
						$('input[name=set_mtr_status_<?=$_SESSION['ss_com_idx']?>_check]').val(0);
						$('input[name=set_mtr_status_<?=$_SESSION['ss_com_idx']?>').hide();
					}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">자재불량타입</th>
			<td colspan="3">
				<input type="text" name="set_mtr_defect_type" value="<?php echo $g5['setting']['set_mtr_defect_type']; ?>" class="frm_input" style="width:80%;">
			</td>
		</tr>
		<tr>
			<th scope="row">파레트상태</th>
			<td colspan="3">
				<input type="text" name="set_plt_status" value="<?php echo $g5['setting']['set_plt_status']; ?>" class="frm_input" style="width:60%;">
			</td>
		</tr>
		<tr>
			<th scope="row">포장용기</th>
			<td colspan="3">
				<input type="text" name="set_bom_packing" value="<?php echo $g5['setting']['set_bom_packing']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">한 페이지 리스트수</th>
			<td colspan="3">
				<p><input type="text" name="set_item_worker_today_list_page_rows" value="<?php echo $g5['setting']['set_item_worker_today_list_page_rows']; ?>" class="frm_input" style="width:50px;"> 작업자별 현황 페이지 (item_worker_today_list.php)</p>
				<p style="margin-top:5px;"><input type="text" name="set_item_customer_today_list_page_rows" value="<?php echo $g5['setting']['set_item_customer_today_list_page_rows']; ?>" class="frm_input" style="width:50px;"> 고객사별 현황 페이지 (item_customer_today_list.php)</p>
			</td>
		</tr>
		<tr>
			<th scope="row">주야간 생산분배</th>
			<td colspan="3">
				<?php echo help('주간, 야간 생산량 분배를 %비율로 입력하세요. ex)day=76, night=33') ?>
				<input type="text" name="set_bmw_type_share" value="<?php echo $g5['setting']['set_bmw_type_share']; ?>" class="frm_input" style="width:50%;">
			</td>
		</tr>
		<tr>
			<th scope="row">생산정보 입력</th>
			<td colspan="3">
                <input type="hidden" name="set_production_test_yn" value="<?=($g5['setting']['set_production_test_yn'])?'1':''?>">
                <label><input type="checkbox" <?=($g5['setting']['set_production_test_yn'])?'checked':''?> id="set_production_test_yn"> 테스트 입력</label>
                <script>
                $(document).on('click','#set_production_test_yn',function(e){
                    if($(this).is(':checked')) {$('input[name=set_production_test_yn]').val(1);}
                    else {$('input[name=set_production_test_yn]').val(0);}
                });
                </script>
				&nbsp;
				<input type="text" name="set_test_ip" value="<?php echo $g5['setting']['set_test_ip']; ?>" class="frm_input" style="width:130px;" placeholder="test IP">
			</td>
		</tr>
		<tr>
			<th scope="row">작업자 QR 입력</th>
			<td colspan="3">
				<?php echo help('작업자 QR코드가 정상 입력되는 상황에서 테스트 모드를 해제하세요. 테스트 모드에서는 생산량이 2~3배 더 많이 나올 수도 있습니다.<br>지그 하나에 여러 제품이 생산되기 때문입니다.') ?>
                <input type="hidden" name="set_worker_test_yn" value="<?=($g5['setting']['set_worker_test_yn'])?'1':''?>">
                <label><input type="checkbox" <?=($g5['setting']['set_worker_test_yn'])?'checked':''?> id="set_worker_test_yn"> 테스트 모드</label>
                <script>
                $(document).on('click','#set_worker_test_yn',function(e){
                    if($(this).is(':checked')) {$('input[name=set_worker_test_yn]').val(1);}
                    else {$('input[name=set_worker_test_yn]').val(0);}
                });
                </script>
			</td>
		</tr>
		<tr>
			<th scope="row">문자발송 테스트</th>
			<td colspan="3">
				<?php echo help('테스트 발송인 경우 한 사람에게만 문자가 발송됩니다. 실제로 문자가 날아가면 혼란이 생길 수 있습니다.') ?>
                <input type="hidden" name="set_hp_test_yn" value="<?=($g5['setting']['set_hp_test_yn'])?'1':''?>">
                <label><input type="checkbox" <?=($g5['setting']['set_hp_test_yn'])?'checked':''?> id="set_hp_test_yn"> 테스트 발송</label>
                <script>
                $(document).on('click','#set_hp_test_yn',function(e){
                    if($(this).is(':checked')) {$('input[name=set_hp_test_yn]').val(1);}
                    else {$('input[name=set_hp_test_yn]').val(0);}
                });
                </script>
				&nbsp;
				<input type="text" name="set_test_hp" value="<?php echo $g5['setting']['set_test_hp']; ?>" class="frm_input" style="width:130px;" placeholder="test cell number">
			</td>
		</tr>
		<tr>
			<th scope="row">데이타 테스트 입력</th>
			<td colspan="3">
				<?php echo help('데이터를 테스트로 생성할 때 체크하세요. 정상 데이터가 입력되면 체크를 제거하세요.') ?>
                <input type="hidden" name="set_data_test_yn" value="<?=($g5['setting']['set_data_test_yn'])?'1':''?>">
                <label><input type="checkbox" <?=($g5['setting']['set_data_test_yn'])?'checked':''?> id="set_data_test_yn"> 테스트 입력</label>
                <script>
                $(document).on('click','#set_data_test_yn',function(e){
                    if($(this).is(':checked')) {$('input[name=set_data_test_yn]').val(1);}
                    else {$('input[name=set_data_test_yn]').val(0);}
                });
                </script>
			</td>
		</tr>
        </tbody>
		</table>
	</div>
</section>

<section id="anc_cf_message">
    <h2 class="h2_frm">메시지설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>메시지설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">코드별 전송 메일</th>
            <td colspan="3">
                <?php echo help('치환 변수: {제목} {업체명} {이름} {설비명} {코드} {만료일} {년월일} {남은기간} {HOME_URL}'); ?>
                <input type="text" name="set_error_subject" value="<?php echo $g5['setting']['set_error_subject']; ?>" class="frm_input" style="width:80%;" placeholder="메일제목">
                <?php echo editor_html("set_error_content", get_text($g5['setting']['set_error_content'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">태그별 전송 메일</th>
            <td colspan="3">
                <?php echo help('치환 변수: {제목} {업체명} {이름} {설비명} {코드} {만료일} {년월일} {남은기간} {HOME_URL}'); ?>
                <input type="text" name="set_tag_subject" value="<?php echo $g5['setting']['set_tag_subject']; ?>" class="frm_input" style="width:80%;" placeholder="메일제목">
                <?php echo editor_html("set_tag_content", get_text($g5['setting']['set_tag_content'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">계획정비 메일</th>
            <td colspan="3">
                <?php echo help('치환 변수: {제목} {업체명} {이름} {설비명} {만료일} {년월일} {남은기간} {HOME_URL}'); ?>
                <input type="text" name="set_maintain_plan_subject" value="<?php echo $g5['setting']['set_maintain_plan_subject']; ?>" class="frm_input" style="width:80%;" placeholder="메일제목">
                <?php echo editor_html("set_maintain_plan_content", get_text($g5['setting']['set_maintain_plan_content'], 0)); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">게시판 new 아이콘</th>
            <td>
                <input type="text" name="set_new_icon_hour" value="<?php echo $g5['setting']['set_new_icon_hour']; ?>" class="frm_input" style="width:20px;"> 시간동안 new 아이콘 표시
            </td>
            <th scope="row">new 아이콘 주말포함</th>
            <td>
                <div style="visibility:hidden;">
                <label for="set_new_icon_holiday_yn_1">
                    <input type="radio" name="set_new_icon_holiday_yn" value="1" id="set_new_icon_holiday_yn_1" <?php echo ($g5['setting']['set_new_icon_holiday_yn']) ? 'checked':'' ?>> 영업일만 포함
                </label> &nbsp;&nbsp;
                <label for="set_new_icon_holiday_yn_0">
                    <input type="radio" name="set_new_icon_holiday_yn" value="0" id="set_new_icon_holiday_yn_0" <?php echo ($g5['setting']['set_new_icon_holiday_yn']) ? '':'checked' ?>> 주말까지 포함
                </label>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">만료공지 메일</th>
            <td colspan="3">
                <?php echo help('치환 변수: {법인명} {업체명} {담당자} {년월일} {승인명} {남은기간} {HOME_URL} {연락처} {이메일}'); ?>
                <input type="text" name="set_expire_email_subject" value="<?php echo $g5['setting']['set_expire_email_subject']; ?>" class="frm_input" style="width:80%;" placeholder="메일제목">
                <?php echo editor_html("set_expire_email_content", get_text($g5['setting']['set_expire_email_content'], 0)); ?>
            </td>
        </tr>
		</tbody>
		</table>
	</div>
</section>

<section id="anc_cf_secure">
    <h2 class="h2_frm">관리설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
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
            <th scope="row">디비구조 다운로드</th>
            <td>
				<a href="./db_table_excel.php" class="btn btn_02">다운로드</a>
            </td>
        </tr>
        <tr>
            <th scope="row">디비테이블 명칭</th>
            <td>
                <?php echo help('엑셀 다운로드 시 디비테이블 명칭을 설정합니다.') ?>
                <textarea name="set_db_table_name" id="set_db_table_name"><?php echo get_text($g5['setting']['set_db_table_name']); ?></textarea>
            </td>
        </tr>
		<tr>
			<th scope="row">엑셀에서 건너뛸 디비</th>
			<td colspan="3">
                <?php echo help('엑셀 다운로드 시 다운로드 필요없는 디비테이블을 설정합니다.') ?>
                <textarea name="set_db_table_skip" id="set_db_table_skip"><?php echo get_text($g5['setting']['set_db_table_skip']); ?></textarea>
			</td>
		</tr>
        <tr>
            <th scope="row">관리자메모</th>
            <td>
                <?php echo help('관리자 메모입니다.') ?>
                <textarea name="set_memo_super" id="set_memo_super"><?php echo get_text($g5['setting']['set_memo_super']); ?></textarea>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<div class="btn_fixed_top btn_confirm">
    <?php if($member['mb_manager_yn']) { ?>
 	   <a href="./qr_print_test.php" id="btn_print" class="btn btn_03">프린트</a>
 	   <a href="./qr_scan_test.php" id="btn_scan" class="btn btn_03" style="margin-right:20px;">스캐너</a>
    <?php } ?>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>

</form>

<script>
$(function(){
    // 프린트
    $(document).on('click','#btn_print',function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
        winPrint = window.open(href,"winPrint","left=50,top=100,width=120,height=120,scrollbars=1");
        winPrint.focus();
    });
    // 스캐너
    $(document).on('click','#btn_scan',function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
        winScan = window.open(href,"winScan","left=50,top=100,width=500,height=500,scrollbars=1");
        winScan.focus();
    });
});

function fconfigform_submit(f) {

    <?php echo get_editor_js("set_expire_email_content"); ?>
    <?php echo chk_editor_js("set_expire_email_content"); ?>
    <?php echo get_editor_js("set_maintain_plan_content"); ?>
    <?php echo chk_editor_js("set_maintain_plan_content"); ?>
    <?php echo get_editor_js("set_error_content"); ?>
    <?php echo chk_editor_js("set_error_content"); ?>
    <?php echo get_editor_js("set_tag_content"); ?>
    <?php echo chk_editor_js("set_tag_content"); ?>

    f.action = "./config_form_update.php";
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
