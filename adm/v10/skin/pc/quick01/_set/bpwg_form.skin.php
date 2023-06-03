<?php
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//adm/ajax/bpwidget_call_skin_config.php
//아작스로 이 페이지가 호출될때는 add_stylesheet() OR add_javascript()함수가 반영되지 않는다.
//add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_form_style.css">', 1);
//이미지 업로드를 사용하려면 1로 변경하세요.
/*
$file_upload_flag = 1;


if($w == 'u'){
	//이미지 수정모드에서의 작음 섬네일 사이즈
	$thumb_wd = 80;
	$thumb_ht = 50;
	//이미지 수정팝에창에서의 중간 섬네일 사이즈
	$thumb_m_wd = 200;
	$thumb_m_ht = 120;
	
	$grpsql = " SELECT bwga_array FROM {$g5['bpwidget_attachment_table']}  WHERE bwga_type = 'option' AND bwgs_idx = '{$bwgs_idx}' GROUP BY bwga_array ";
	$grp_result = sql_query($grpsql,1);
	$optfile_group = array();
	//어떤종류의 파일배열이 있는지 총 종류를 뽑아내는 루프
	for($i=0;$grow=sql_fetch_array($grp_result);$i++){
		array_push($optfile_group,$grow['bwga_array']);
		${$grow['bwga_array']} = array();
	}
	//해당 위젯idx(bwgs_idx)의 option에 해당하는 파일 레코드를 전부 추출
	$optfsql = " SELECT * FROM {$g5['bpwidget_attachment_table']} WHERE bwga_type = 'option' AND bwgs_idx = '{$bwgs_idx}' ORDER BY bwga_array,bwga_sort,bwga_idx ";
	$otpf_result = sql_query($optfsql,1);
	for($i=0;$frow=sql_fetch_array($otpf_result);$i++){
		//등록 이미지 섬네일 생성
		$thumbf = thumbnail($frow['bwga_name'],G5_PATH.$frow['bwga_path'],G5_PATH.$frow['bwga_path'],$thumb_wd,$thumb_ht,false,true,'center');
		$thumbf_url = G5_URL.$frow['bwga_path'].'/'.$thumbf;
		$frow['thumb_url'] = $thumbf_url;
		if(preg_match("/\.svg/i",$frow['bwga_name'])){
			$frow['thumb_url'] = G5_URL.$frow['bwga_path'].'/'.$frow['bwga_name'];
		}
		//수정팝업에서의 중간크기 이미지 섬네일 생성
		$thumbfm = thumbnail($frow['bwga_name'],G5_PATH.$frow['bwga_path'],G5_PATH.$frow['bwga_path'],$thumb_m_wd,$thumb_m_ht,false,true,'center');
		$thumbfm_url = G5_URL.$frow['bwga_path'].'/'.$thumbfm;
		$frow['thumb_m_url'] = $thumbfm_url;
		if(preg_match("/\.svg/i",$frow['bwga_name'])){
			$frow['thumb_m_url'] = G5_URL.$frow['bwga_path'].'/'.$frow['bwga_name'];
		}
		//상단에 파일배열 종류에 해당하는 배열에 분류되어 파일레코드 요소를 담는다.
		array_push(${$frow['bwga_array']},$frow);
	}
	
	//파일배열 종류별 몇개씩 들어있는지와, 종류별 어떤 bwga_idx를 가지고 있는지 추출한다.
	for($i=0;$i<count($optfile_group);$i++){
		${$optfile_group[$i].'_bwga_idxs'} = '';
		for($j=0;$j<count(${$optfile_group[$i]});$j++){
			${$optfile_group[$i].'_bwga_idxs'} .= (($j == 0) ? '':',').${$optfile_group[$i]}[$j]['bwga_idx'];
		}
	}
}
//링크 체크
$logo_url = ($logo_url) ? bwg_g5_url_check($logo_url) : '';

//패스 애니메이션 사용여부 설정
$logo_new_1 = ($logo_new == '_blank') ? 'checked="checked"' : '';
$logo_new_0 = ($logo_new == '' || $logo_new == '_self') ? 'checked="checked"' : '';
*/

$colspan7=7;
$colspan5=5;
$colspan3=3;

$color_basic = '#fae3d9';
$color_top_title = '#e8f9e9';
$color_login = '#f3ecad';
$color_logout = '#f6eec9';
$color_middle = '#eef9bf';
$color_accordion_btn = '#f6e7e6';
?>
<link rel="stylesheet" href="<?=$bwg_skin_set_url?>/bpwg_form_style.css">
<div id="bwg_skin_set">
<h2 class="h2_frm">퀵(QUICK)스킨 옵션설정</h2>
<!--p>
<strong style="color:red;">핑크영역</strong>은 <strong style="color:blue;">텍스트SVG 패스(선) 애니메이션</strong>을 위한 설정내용입니다.<br>
일반 이미지는 SVG관련설정이 반영되지 않습니다.<br>
<strong><span style="color:red;">주의 : </span><span>SVG제작시 사용하는 폰트(서체)는 반드시 이미지로 변환(서체 깨트리기:Create Outline)해서 저장해 주세요.</span></strong><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(폰트(서체) 그대로 저장하시면 애니메이션 효과를 줄 수 없을 뿐만 아니라, 원하는 서체로 표시 되지 않을 수 있습니다.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;반드시 폰트(서체)를 테두리(선)와 면으로 변환해 주세요.)
</p><br-->
<table class="tbl_frm" id="bwg_skin_opt">
	<colgroup>
		<col span="1" width="97">
		<col span="1" width="173">
		<col span="1" width="97">
		<col span="1" width="173">
		<col span="1" width="97">
		<col span="1" width="173">
		<col span="1" width="97">
		<col span="1" width="173">
	</colgroup>
	<tbody>
		<tr>
			<th style="background:<?=$color_basic?>;">기본배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("퀵패널 영역의 기본배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[basic_color]',$basic_color,$w,0); ?>
			</td>
			<th style="background:<?=$color_basic?>;">토글버튼<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("토글버튼의 아이콘 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[toggle_icon]',$toggle_icon,$w,0); ?>
			</td>
			<th style="background:<?=$color_basic?>;">퀵패널<br>그림자색상</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("퀵패널 영역의 그림자 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[shadow_color]',$shadow_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_basic?>;">토글버튼<br>배경색상</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("퀵패널의 토글버튼 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[toggle_bg_color]',$toggle_bg_color,$w,1); ?>
			</td>
			<th style="background:<?=$color_basic?>;">블라인드<br>배경색상</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("퀵패널의 블라인드 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[blind_bg_color]',$blind_bg_color,$w,1); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_top_title?>;">상단타이틀(소)</th>
			<td class="bwg_help">
				<?php echo bwg_help("제일 상단 타이틀의 작은 문자의 내용을 입력하세요.",1,'#555555','#eeeeee'); ?>
				<input type="text" name="bwo[ttl_small]" class="bp_wdp100" value="<?=$ttl_small?>">
			</td>
			<th style="background:<?=$color_top_title?>;">상단타이틀(대)</th>
			<td class="bwg_help">
				<?php echo bwg_help("제일 상단 타이틀의 큰 문자의 내용을 입력하세요.",1,'#555555','#eeeeee'); ?>
				<input type="text" name="bwo[ttl_big]" class="bp_wdp100" value="<?=$ttl_big?>">
			</td>
			<th style="background:<?=$color_top_title?>;">타이틀<br>구분선색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("제일 상단 작은 문자 아래의 구분선 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[ttl_gubunline]',$ttl_gubunline,$w,0); ?>
			</td>
			<th style="background:<?=$color_top_title?>;">타이틀<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("제일 상단 타이틀 영역의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[ttl_bg]',$ttl_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_top_title?>;">작은 타이틀<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("제일 상단 작은 타이틀 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[ttl_small_font]',$ttl_small_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_top_title?>;">큰 타이틀<br>폰트색</th>
			<td colspan="<?=$colspan5?>" class="bwg_help">
				<?php echo bwg_help("제일 상단 큰 타이틀 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[ttl_big_font]',$ttl_big_font,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_login?>;">로그인입력란<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인입력란의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_input_font]',$login_input_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">로그인입력란<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인입력란의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_input_bg]',$login_input_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">로그인입력란<br>테두리색</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("로그인입력란의 테두리 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_input_line]',$login_input_line,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_login?>;">로그인버튼<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인버튼의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_font]',$login_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">로그인버튼<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인버튼의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_bg]',$login_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">로그인버튼<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_hover_font]',$login_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">로그인버튼<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그인버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[login_hover_bg]',$login_hover_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_login?>;">등록/찾기<br>버튼 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("등록/찾기버튼의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[regfind_font]',$regfind_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">등록/찾기<br>버튼 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("등록/찾기버튼의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[regfind_bg]',$regfind_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">등록/찾기<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("등록/찾기버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[regfind_hover_font]',$regfind_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_login?>;">등록/찾기<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("등록/찾기버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[regfind_hover_bg]',$regfind_hover_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_logout?>;">로그아웃버튼<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그아웃버튼의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[logout_font]',$logout_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">로그아웃버튼<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그아웃버튼의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[logout_bg]',$logout_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">로그아웃버튼<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그아웃버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[logout_hover_font]',$logout_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">로그아웃버튼<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("로그아웃버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[logout_hover_bg]',$logout_hover_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_logout?>;">정보수정버튼<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("정보수정버튼의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[infomf_font]',$infomf_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">정보수정버튼<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("정보수정버튼의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[infomf_bg]',$infomf_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">정보수정버튼<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("정보수정버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[infomf_hover_font]',$infomf_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_logout?>;">정보수정버튼<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("정보수정버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[infomf_hover_bg]',$infomf_hover_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_middle?>;">그리드버튼<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("그리드버튼의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[grid_font]',$grid_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_middle?>;">그리드버튼<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("그리드버튼의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[grid_bg]',$grid_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_middle?>;">그리드버튼<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("그리드버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[grid_hover_font]',$grid_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_middle?>;">그리드버튼<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("그리드버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[grid_hover_bg]',$grid_hover_bg,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$color_accordion_btn?>;">아코디언버튼<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("하단 아코디언버튼 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[accordion_font]',$accordion_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_accordion_btn?>;">아코디언버튼<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("하단 아코디언버튼 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[accordion_bg]',$accordion_bg,$w,0); ?>
			</td>
			<th style="background:<?=$color_accordion_btn?>;">아코디언버튼<br>롤오버 폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("하단 아코디언버튼 롤오버시의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[accordion_hover_font]',$accordion_hover_font,$w,0); ?>
			</td>
			<th style="background:<?=$color_accordion_btn?>;">아코디언버튼<br>롤오버 배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("하단 아코디언버튼 롤오버시의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[accordion_hover_bg]',$accordion_hover_bg,$w,0); ?>
			</td>
		</tr>
	</tbody>
</table>
<script>
$('#bwgs_tab li a:contains(내용목록)').attr('href','javascript:');
var w = '<?=$w?>';
var rgba = '';
var bwgs_idx = <?=(($bwgs_idx)?$bwgs_idx:0)?>;
$(function(){
	
});

//반드시 존재해야 하는 함수
function fbpwidgetoptionform_submit(){
	/*
	var format_url = /^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i;
	//var format_tel_sms = /^(tel|sms)\:[0-9]{2,3}\-[0-9]{3,4}\-[0-9]{4}/gm;
	//var format_email = /^(mailto)\:[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
	//var format_inner = /^(\#)[0-9a-zA-Z\_\-]*$/i;
	var logo_url = '';
	if($('input[name="bwo[logo_url]"]').val() != ''){
		logo_url = $('input[name="bwo[logo_url]"]').val();
		//if(!logo_url.match(format_url) && !logo_url.match(format_tel_sms) && !logo_url.match(format_email) && !logo_url.match(format_inner)){
		if(!logo_url.match(format_url)){
			alert('로고URL이 올바른 링크형식이 아닙니다.');
			$('input[name="bwo[logo_url]"]').focus();
			return false;
		}
	}
	*/
	return true;
}
</script>
</div><!--#bwg_skin_set-->