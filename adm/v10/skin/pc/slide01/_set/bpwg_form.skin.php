<?php
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//adm/ajax/bpwidget_call_skin_config.php
//아작스로 이 페이지가 호출될때는 add_stylesheet() OR add_javascript()함수가 반영되지 않는다.
// add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_form_style.css">', 1);
//이미지 업로드를 사용하려면 1로 변경하세요.
$file_upload_flag = 0;


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
		//print_r2($frow);
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
//자동재생여부설정
$autoplay_1 = ($autoplay == '' || $autoplay == 'yes') ? 'checked="checked"' : '';
$autoplay_0 = ($autoplay == 'no') ? 'checked="checked"' : '';

//무한루프여부설정
$infinite_1 = ($infinite == '' || $infinite == 'yes') ? 'checked="checked"' : '';
$infinite_0 = ($infinite == 'no') ? 'checked="checked"' : '';

//점버튼여부설정
$dots_1 = ($dots == '' || $dots == 'yes') ? 'checked="checked"' : '';
$dots_0 = ($dots == 'no') ? 'checked="checked"' : '';

//방향(화살표)버튼여부설정
$arrows_1 = ($arrows == '' || $arrows == 'yes') ? 'checked="checked"' : '';
$arrows_0 = ($arrows == 'no') ? 'checked="checked"' : '';

//동작 FADE여부설정
$fade_1 = ($fade == 'yes') ? 'checked="checked"' : '';
$fade_0 = ($fade == '' || $fade == 'no') ? 'checked="checked"' : '';

//스와이프여부설정
$swipe_1 = ($swipe == '' || $swipe == 'yes') ? 'checked="checked"' : '';
$swipe_0 = ($swipe == 'no') ? 'checked="checked"' : '';

//pauseOnFocus 포커스시 일시정지 사용여부 설정
$pauseOnFocus_1 = ($pauseOnFocus == '' || $pauseOnFocus == 'yes') ? 'checked="checked"' : '';
$pauseOnFocus_0 = ($pauseOnFocus == 'no') ? 'checked="checked"' : '';

//pauseOnHover 마우스오버시 일시정지 사용여부 설정
$pauseOnHover_1 = ($pauseOnHover == '' || $pauseOnHover == 'yes') ? 'checked="checked"' : '';
$pauseOnHover_0 = ($pauseOnHover == 'no') ? 'checked="checked"' : '';

//pauseOnDotsHover 점버튼에 올렸을때 일시정지 사용여부 설정
$pauseOnDotsHover_1 = ($pauseOnDotsHover == '' || $pauseOnDotsHover == 'yes') ? 'checked="checked"' : '';
$pauseOnDotsHover_0 = ($pauseOnDotsHover == 'no') ? 'checked="checked"' : '';

//세로방향 슬라이드 사용여부설정
$vertical_1 = ($vertical == 'yes') ? 'checked="checked"' : '';
$vertical_0 = ($vertical == '' || $vertical == 'no') ? 'checked="checked"' : '';

//verticalSwiping 세로방향 스와이프 사용여부설정
$verticalSwiping_1 = ($verticalSwiping == 'yes') ? 'checked="checked"' : '';
$verticalSwiping_0 = ($verticalSwiping == '' || $verticalSwiping == 'no') ? 'checked="checked"' : '';

$colspan7=7;
$colspan5=5;
$colspan3=3;

//print_r2();
?>
<link rel="stylesheet" href="<?=$bwg_skin_set_url?>/bpwg_form_style.css">
<div id="bwg_skin_set">
<h2 class="h2_frm">슬라이드 옵션설정</h2>
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
			<th>기본너비(폭)</th>
			<td class="bwg_help">
				<?php echo bwg_help("메인배너 영역의 기본너비를 설정.<br>(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$mbnr_default_wd = (isset($mbnr_default_wd)) ? $mbnr_default_wd : 1080;
				echo bpwg_input_range('bwo[mbnr_default_wd]',$mbnr_default_wd,$w,900,1300,10,'147',45,'px');
				?>
			</td>
			<th>영상/이미지<br>너비(폭)</th>
			<td class="bwg_help">
				<?php echo bwg_help("영상및 이미지의 너비(폭)를 설정하세요. 실제 표시할 사이즈가 아닌<br>가로/세로 비율계산을 위한 데이터 입니다.<br>(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$sld_wd = (isset($sld_wd)) ? $sld_wd : 1920;
				echo bpwg_input_range('bwo[sld_wd]',$sld_wd,$w,700,2000,10,'165',45,'px');
				?>
			</td>
			<th>영상/이미지<br>높이</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("영상및 이미지의 높이를 설정하세요. 실제 표시할 사이즈가 아닌<br>가로/세로 비율계산을 위한 데이터 입니다.<br>(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$sld_ht = (isset($sld_ht)) ? $sld_ht : 780;
				echo bpwg_input_range('bwo[sld_ht]',$sld_ht,$w,300,1200,10,'165',45,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>자동재생</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드 자동재생 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<div>
					<label for="bwo_autoplay_1" class="label_radio first_child bwo_autoplay_1">
						<input type="radio" id="bwo_autoplay_1" name="bwo[autoplay]" value="yes" <?=$autoplay_1?>>
						<strong></strong>
						<span>네</span>
					</label>
					<label for="bwo_autoplay_0" class="label_radio bwo_autoplay_0">
						<input type="radio" id="bwo_autoplay_0" name="bwo[autoplay]" value="no" <?=$autoplay_0?>>
						<strong></strong>
						<span>아니오</span>
					</label>
				</div>
			</td>
			<th>재생간격시간</th>
			<td class="bwg_help">
				<?php echo bwg_help("자동재생시 슬라이드간 정지 시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
				<div>
					<?php
					$autoplaySpeed = (isset($autoplaySpeed)) ? $autoplaySpeed : 3;
					echo bpwg_input_range('bwo[autoplaySpeed]',$autoplaySpeed,$w,0,10,1,'100%',38,'초');
					?>
				</div>
			</td>
			<th>이동속도</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드가 이동하는 속도 또는 변화하는 속도를 의미합니다.",1,'#555555','#eeeeee'); ?>
				<div>
					<?php
					$speed = (isset($speed)) ? $speed : 0.3;
					echo bpwg_input_range('bwo[speed]',$speed,$w,0.1,1,0.1,'100%',38,'초');
					?>
				</div>
			</td>
			<th>무한루프여부</th>
			<td class="bwg_help">
				<?php echo bwg_help("무한반복의 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_infinite_1" class="label_radio first_child bwo_infinite_1">
					<input type="radio" id="bwo_infinite_1" name="bwo[infinite]" value="yes" <?=$infinite_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_infinite_0" class="label_radio bwo_infinite_0">
					<input type="radio" id="bwo_infinite_0" name="bwo[infinite]" value="no" <?=$infinite_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
		</tr>
		<tr>
			<th>점버튼표시</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드 갯수만큼의 점버튼 표시여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_dots_1" class="label_radio first_child bwo_dots_1">
					<input type="radio" id="bwo_dots_1" name="bwo[dots]" value="yes" <?=$dots_1?>>
					<strong></strong>
					<span>표시</span>
				</label>
				<label for="bwo_dots_0" class="label_radio bwo_dots_0">
					<input type="radio" id="bwo_dots_0" name="bwo[dots]" value="no" <?=$dots_0?>>
					<strong></strong>
					<span>숨김</span>
				</label>
			</td>
			<th>방향버튼표시</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드 방향버튼의 표시여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_arrows_1" class="label_radio first_child bwo_arrows_1">
					<input type="radio" id="bwo_arrows_1" name="bwo[arrows]" value="yes" <?=$arrows_1?>>
					<strong></strong>
					<span>표시</span>
				</label>
				<label for="bwo_arrows_0" class="label_radio bwo_arrows_0">
					<input type="radio" id="bwo_arrows_0" name="bwo[arrows]" value="no" <?=$arrows_0?>>
					<strong></strong>
					<span>숨김</span>
				</label>
			</td>
			<th>동작유형</th>
			<td class="bwg_help">
				<?php echo bwg_help("[fade:false(bwo[fade]:1/)]슬라이드 동작유형을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_fade_0" class="label_radio first_child bwo_fade_0">
					<input type="radio" id="bwo_fade_0" name="bwo[fade]" value="no" <?=$fade_0?>>
					<strong></strong>
					<span>SLIDE</span>
				</label>
				<label for="bwo_fade_1" class="label_radio bwo_fade_1">
					<input type="radio" id="bwo_fade_1" name="bwo[fade]" value="yes" <?=$fade_1?>>
					<strong></strong>
					<span>FADE</span>
				</label>
			</td>
			<th>스와이프여부</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드 터치스와이프 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_swipe_1" class="label_radio first_child bwo_swipe_1">
					<input type="radio" id="bwo_swipe_1" name="bwo[swipe]" value="yes" <?=$swipe_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_swipe_0" class="label_radio bwo_swipe_0">
					<input type="radio" id="bwo_swipe_0" name="bwo[swipe]" value="no" <?=$swipe_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
		</tr>
		<tr>
			<th>포커스정지</th>
			<td class="bwg_help">
				<?php echo bwg_help("슬라이드가 포커스 되었을때 일시정지 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_pauseOnFocus_1" class="label_radio first_child bwo_pauseOnFocus_1">
					<input type="radio" id="bwo_pauseOnFocus_1" name="bwo[pauseOnFocus]" value="yes" <?=$pauseOnFocus_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_pauseOnFocus_0" class="label_radio bwo_pauseOnFocus_0">
					<input type="radio" id="bwo_pauseOnFocus_0" name="bwo[pauseOnFocus]" value="no" <?=$pauseOnFocus_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
			<th>마우스오버정지</th>
			<td class="bwg_help">
				<?php echo bwg_help("마우스를 올렸을때 일시정지 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_pauseOnHover_1" class="label_radio first_child bwo_pauseOnHover_1">
					<input type="radio" id="bwo_pauseOnHover_1" name="bwo[pauseOnHover]" value="yes" <?=$pauseOnHover_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_pauseOnHover_0" class="label_radio bwo_pauseOnHover_0">
					<input type="radio" id="bwo_pauseOnHover_0" name="bwo[pauseOnHover]" value="no" <?=$pauseOnHover_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
			<th>점버튼정지</th>
			<td class="bwg_help">
				<?php echo bwg_help("점버튼에 마우스를 올렸을때 일시정지 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_pauseOnDotsHover_1" class="label_radio first_child bwo_pauseOnDotsHover_1">
					<input type="radio" id="bwo_pauseOnDotsHover_1" name="bwo[pauseOnDotsHover]" value="yes" <?=$pauseOnDotsHover_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_pauseOnDotsHover_0" class="label_radio bwo_pauseOnDotsHover_0">
					<input type="radio" id="bwo_pauseOnDotsHover_0" name="bwo[pauseOnDotsHover]" value="no" <?=$pauseOnDotsHover_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
			<th>슬라이드갯수</th>
			<td class="bwg_help">
				<?php echo bwg_help("한 번에 보여 줄 슬라이드 갯수를 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$slidesToShow = (isset($slidesToShow)) ? $slidesToShow : 1;
				echo bpwg_input_range('bwo[slidesToShow]',$slidesToShow,$w,1,20,1,'100%',34,'개');
				?>
			</td>
		</tr>
		<tr>
			<th>블라인드</th>
			<td colspan="<?=$colspan7?>" class="bwg_help">
				<?php echo bwg_help("배경블라인드의 색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[bgBlind]',$bgBlind,$w,1); ?>
			</td>
			<!--th>세로슬라이드</th>
			<td class="bwg_help">
				<?php //echo bwg_help("세로방향 슬라이드의 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_vertical_1" class="label_radio first_child bwo_vertical_1">
					<input type="radio" id="bwo_vertical_1" name="bwo[vertical]" value="yes" <?=$vertical_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_vertical_0" class="label_radio bwo_vertical_0">
					<input type="radio" id="bwo_vertical_0" name="bwo[vertical]" value="no" <?=$vertical_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td>
			<th>세로스와이프</th>
			<td class="bwg_help">
				<?php //echo bwg_help("세로방향 스와이프의 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<label for="bwo_verticalSwiping_1" class="label_radio first_child bwo_verticalSwiping_1">
					<input type="radio" id="bwo_verticalSwiping_1" name="bwo[verticalSwiping]" value="yes" <?=$verticalSwiping_1?>>
					<strong></strong>
					<span>사용</span>
				</label>
				<label for="bwo_verticalSwiping_0" class="label_radio bwo_verticalSwiping_0">
					<input type="radio" id="bwo_verticalSwiping_0" name="bwo[verticalSwiping]" value="no" <?=$verticalSwiping_0?>>
					<strong></strong>
					<span>사용안함</span>
				</label>
			</td-->
		</tr>
		<tr>
			<th>방향 버튼<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("방향버튼의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[arrow_color]',$arrow_color,$w,0); ?>
			</td>
			<th>방향 버튼<br>롤오버 색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("방향버튼의 롤오버 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[arrow_hover_color]',$arrow_hover_color,$w,0); ?>
			</td>
			<th>점 버튼<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("점버튼의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[dot_color]',$dot_color,$w,0); ?>
			</td>
			<th>점 버튼<br>선택 색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("선택된 점버튼의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[dot_active_color]',$dot_active_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>텍스트#1<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#1 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[text1_color]',$text1_color,$w,0); ?>
			</td>
			<th>텍스트#1<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#1의 폰트크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text1_font_size = (isset($text1_font_size)) ? $text1_font_size : 50;
				echo bpwg_input_range('bwo[text1_font_size]',$text1_font_size,$w,18,70,1,'100%',34,'px');
				?>
			</td>
			<th>텍스트#1<br>애니유형</th>
			<td colspan="<?=$colspan3?>">
				<?php
				$txt1_ani_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_text_animation_type'], 'bwo[text1_ani_type]', $text1_ani_type, 1, 0,$txt1_ani_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
			</td>
		</tr>
		<tr>
			<th>텍스트#2<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#2 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[text2_color]',$text2_color,$w,0); ?>
			</td>
			<th>텍스트#2<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#2의 폰트크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text2_font_size = (isset($text2_font_size)) ? $text2_font_size : 40;
				echo bpwg_input_range('bwo[text2_font_size]',$text2_font_size,$w,18,70,1,'100%',34,'px');
				?>
			</td>
			<th>텍스트#2<br>애니유형</th>
			<td>
				<?php
				$txt2_ani_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_text_animation_type'], 'bwo[text2_ani_type]', $text2_ani_type, 1, 0,$txt2_ani_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
			</td>
			<th>텍스트#2<br>상단마진간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#2의 상단마진간격을 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text2_margin_top = (isset($text2_margin_top)) ? $text2_margin_top : 10;
				echo bpwg_input_range('bwo[text2_margin_top]',$text2_margin_top,$w,5,100,5,'100%',34,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>텍스트#3<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#3 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[text3_color]',$text3_color,$w,0); ?>
			</td>
			<th>텍스트#3<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#3의 폰트크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text3_font_size = (isset($text3_font_size)) ? $text3_font_size : 30;
				echo bpwg_input_range('bwo[text3_font_size]',$text3_font_size,$w,18,70,1,'100%',34,'px');
				?>
			</td>
			<th>텍스트#3<br>애니유형</th>
			<td>
				<?php
				$txt3_ani_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_text_animation_type'], 'bwo[text3_ani_type]', $text3_ani_type, 1, 0,$txt3_ani_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
			</td>
			<th>텍스트#3<br>상단마진간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#3의 상단마진간격을 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text3_margin_top = (isset($text3_margin_top)) ? $text3_margin_top : 10;
				echo bpwg_input_range('bwo[text3_margin_top]',$text3_margin_top,$w,5,100,5,'100%',34,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>텍스트#4<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#4 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[text4_color]',$text4_color,$w,0); ?>
			</td>
			<th>텍스트#4<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#4의 폰트크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text4_font_size = (isset($text4_font_size)) ? $text4_font_size : 20;
				echo bpwg_input_range('bwo[text4_font_size]',$text4_font_size,$w,18,70,1,'100%',34,'px');
				?>
			</td>
			<th>텍스트#4<br>애니유형</th>
			<td>
				<?php
				$txt4_ani_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_text_animation_type'], 'bwo[text4_ani_type]', $text4_ani_type, 1, 0,$txt4_ani_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
			</td>
			<th>텍스트#4<br>상단마진간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("텍스트#4의 상단마진간격을 설정하세요.",1,'#555555','#eeeeee'); ?>
				
				<?php
				$text4_margin_top = (isset($text4_margin_top)) ? $text4_margin_top : 10;
				echo bpwg_input_range('bwo[text4_margin_top]',$text4_margin_top,$w,5,100,5,'100%',34,'px');
				?>
			</td>
		</tr>
		<?php if($file_upload_flag){ ?>
		<tr>
			<th class="">이미지</th>
			<td colspan="<?=$colspan7?>">
				<?php
				$file_name = 'afile';
				${$file_name.'_maxlength'} = 1;
				${$file_name.'_accept'} = 'png|jpg|jpeg|gif|svg';
				${$file_name.'_data_maxfile'} = $g5['bpwidget']['bwgf_filesize'];
				${$file_name.'_uploaded'} = count(${$file_name});
				${$file_name.'_remain_cnt'} = ${$file_name.'_maxlength'} - ${$file_name.'_uploaded'};
				echo "<p style='color:#223390;'>총 ".${$file_name.'_maxlength'}."개 까지만 가능하고, 업로드 가능한 파일수는 ".${$file_name.'_remain_cnt'}."개 입니다.</p>".PHP_EOL;
				if(${$file_name.'_remain_cnt'}){
				?>
				<input type="file" name="<?=$file_name?>[]" id="<?=$file_name?>" multiple class="with-preview" maxlength="<?=${$file_name.'_remain_cnt'}?>" accept="<?=${$file_name.'_accept'}?>" data-maxfile="<?=${$file_name.'_data_maxfile'}?>">
				<?php } ?>
				<?php if(count(${$file_name})){?>
				<div class="uploaded" uploaded_cnt="<?=(count(${$file_name}))?>">
					<div class="total_del_box">
						<div class="total_del_tbl">
							<div class="total_del_td">
								<img class="row_files_del" bwga_idxs="<?=${$file_name.'_bwga_idxs'}?>" width="20" height="20" src="<?=G5_BPWIDGET_IMG_URL?>/close_b.png" title="전체삭제">
							</div>
						</div>
					</div>
					<ul>
						<?php for($i=0;$i<count(${$file_name});$i++){?>
							<li>
								<img class="thumb" width="<?=$thumb_wd?>" height="<?=$thumb_ht?>" bwga_idx="<?=${$file_name}[$i]['bwga_idx']?>" bwga_title="<?=${$file_name}[$i]['bwga_title']?>" bwga_rank="<?=${$file_name}[$i]['bwga_rank']?>" bwga_sort="<?=${$file_name}[$i]['bwga_sort']?>" bwga_status="<?=${$file_name}[$i]['bwga_status']?>" bwga_content="<?=${$file_name}[$i]['bwga_content']?>" thumb_m="<?=${$file_name}[$i]['thumb_m_url']?>" title="개별 이미지 변경" src="<?=${$file_name}[$i]['thumb_url']?>">
								<img class="thumb_del" bwga_idx="<?=${$file_name}[$i]['bwga_idx']?>" width="24" height="24" title="개별 이미지 삭제" src="<?=G5_BPWIDGET_IMG_URL?>/close_bg_circle.png">
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th class="">이미지2</th>
			<td colspan="<?=$colspan7?>">
				<?php
				$file_name = 'bfile';
				${$file_name.'_maxlength'} = 5;
				${$file_name.'_accept'} = 'png|jpg|jpeg|gif|svg';
				${$file_name.'_data_maxfile'} = $g5['bpwidget']['bwgf_filesize'];
				${$file_name.'_uploaded'} = count(${$file_name});
				${$file_name.'_remain_cnt'} = ${$file_name.'_maxlength'} - ${$file_name.'_uploaded'};
				echo "<p style='color:#223390;'>총 ".${$file_name.'_maxlength'}."개 까지만 가능하고, 업로드 가능한 파일수는 ".${$file_name.'_remain_cnt'}."개 입니다.</p>".PHP_EOL;
				if(${$file_name.'_remain_cnt'}){
				?>
				<input type="file" name="<?=$file_name?>[]" id="<?=$file_name?>" multiple class="with-preview" maxlength="<?=${$file_name.'_remain_cnt'}?>" accept="<?=${$file_name.'_accept'}?>" data-maxfile="<?=${$file_name.'_data_maxfile'}?>">
				<?php } ?>
				<?php if(count(${$file_name})){?>
				<div class="uploaded" uploaded_cnt="<?=(count(${$file_name}))?>">
					<div class="total_del_box">
						<div class="total_del_tbl">
							<div class="total_del_td">
								<img class="row_files_del" bwga_idxs="<?=${$file_name.'_bwga_idxs'}?>" width="20" height="20" src="<?=G5_BPWIDGET_IMG_URL?>/close_b.png" title="전체삭제">
							</div>
						</div>
					</div>
					<ul>
						<?php for($i=0;$i<count(${$file_name});$i++){?>
							<li>
								<img class="thumb" width="<?=$thumb_wd?>" height="<?=$thumb_ht?>" bwga_idx="<?=${$file_name}[$i]['bwga_idx']?>" bwga_title="<?=${$file_name}[$i]['bwga_title']?>" bwga_rank="<?=${$file_name}[$i]['bwga_rank']?>" bwga_sort="<?=${$file_name}[$i]['bwga_sort']?>" bwga_status="<?=${$file_name}[$i]['bwga_status']?>" bwga_content="<?=${$file_name}[$i]['bwga_content']?>" thumb_m="<?=${$file_name}[$i]['thumb_m_url']?>" title="개별 이미지 변경" src="<?=${$file_name}[$i]['thumb_url']?>">
								<img class="thumb_del" bwga_idx="<?=${$file_name}[$i]['bwga_idx']?>" width="24" height="24" title="개별 이미지 삭제" src="<?=G5_BPWIDGET_IMG_URL?>/close_bg_circle.png">
							</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<script>
var w = '<?=$w?>';
var rgba = '';
var bwgs_idx = <?=(($bwgs_idx)?$bwgs_idx:0)?>;
$(function(){
	//이미지 업로드 플래그가 1이면 파일 관련 이벤트 정의
	<?php if($file_upload_flag){?>
		//파일업로드
		if($('input[type="file"]').length){
			$('input[type="file"]').each(function(){
				$(this).MultiFile();
			});
		}
		//첨부파일 개별삭제 처리
		$('.thumb_del').on('click',function(){
			var bwga_idx = $(this).attr('bwga_idx');
			if(bwga_idx) file_single_del(bwga_idx);
			else alert('해당파일의 식별idx코드가 없습니다.');
		});
		//한줄의 첨부파일 전부삭제 처리
		$('.row_files_del').on('click',function(){
			var bwga_idxs = $(this).attr('bwga_idxs');
			if(bwga_idxs) files_row_del(bwga_idxs);
			else alert('해당줄 파일들의 식별idx코드가 없습니다.');
		});
		//이미지섬네일 버튼에 의해 이미지 변경 모달을 표시한다.
		$('.thumb').on('click',function(){
			var thumb_m = $('<img src="'+$(this).attr('thumb_m')+'" width="<?=$thumb_m_wd?>" height="<?=$thumb_m_ht?>">');
			var cur_img = $('#img_change_modal #cur_img');
			$('#img_change_modal').show();
			cur_img.find('img').remove();
			$(thumb_m).prependTo(cur_img);
			$('#img_change_modal input[name="bwgs_idx"]').val(bwgs_idx);
			$('#img_change_modal input[name="bwga_idx"]').val($(this).attr('bwga_idx'));
			$('#img_change_modal input[name="bwga_title"]').val($(this).attr('bwga_title'));
			$('#img_change_modal input[name="bwga_rank"]').val($(this).attr('bwga_rank'));
			$('#img_change_modal input[name="bwga_sort"]').val($(this).attr('bwga_sort'));
			$('#img_change_modal select[name="bwga_status"] option[value="'+$(this).attr('bwga_status')+'"]').attr('selected',true).siblings('option').attr('selected',false);
			$('#img_change_modal textarea[name="bwga_content"]').val($(this).attr('bwga_content'));
		});
		
		//이미지 변경 모달 닫기
		$('#img_change_bg, #img_change_modal_close, .img_change_close').on('click',function(){
			if($('#img_change_modal .MultiFile-remove').length) $('#img_change_modal .MultiFile-remove').trigger('click');
			$('#img_change_modal input[name="bwgs_idx"]').val('');
			$('#img_change_modal input[name="bwga_idx"]').val('');
			$('#img_change_modal input[name="bwga_title"]').val('');
			$('#img_change_modal input[name="bwga_rank"]').val('');
			$('#img_change_modal input[name="bwga_sort"]').val('');
			$('#img_change_modal select[name="bwga_sort"] option[value="ok"]').attr('selected',true).siblings('option').attr('selected',false);
			$('#img_change_modal textarea[name="bwga_content"]').val('');
			$('#img_change_modal').hide();
		});
	<?php } ?>
});

//옵션설정 관련하여 validate가 필요하면 주석을 풀어서 사용해라
//function fbpwidgetoptionform_submit(){
//	if(true){
//		return false;
//	}
//	return true;
//}
</script>
</div><!--#bwg_skin_set-->