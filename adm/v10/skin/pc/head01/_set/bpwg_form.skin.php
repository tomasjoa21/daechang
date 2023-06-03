<?php
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//adm/ajax/bpwidget_call_skin_config.php
//아작스로 이 페이지가 호출될때는 add_stylesheet() OR add_javascript()함수가 반영되지 않는다.
//add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_form_style.css">', 1);
//이미지 업로드를 사용하려면 1로 변경하세요.
$file_upload_flag = 1;


if($w == 'u'){
	//이미지 수정모드에서의 작음 섬네일 크기
	$thumb_wd = 80;
	$thumb_ht = 50;
	//이미지 수정팝에창에서의 중간 섬네일 크기
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

//스크롤 하단 이동시 헤더영역의 그림자 표시의 여부를 설정
$head_shadow_1 = ($head_shadow == 'yes') ? 'checked="checked"' : '';
$head_shadow_0 = ($head_shadow == '' || $head_shadow == 'no') ? 'checked="checked"' : '';

//검색 사용여부 설정
$sch_use_1 = ($sch_use == '' || $sch_use == 'yes') ? 'checked="checked"' : '';
$sch_use_0 = ($sch_use == 'no') ? 'checked="checked"' : '';

//검색유형 설정
$sch_shop_1 = ($sch_shop == '' || $sch_shop == 'yes') ? 'checked="checked"' : '';
$sch_shop_0 = ($sch_shop == 'no') ? 'checked="checked"' : '';

//1차메뉴의 정렬 설정
$nav_align_left = ($nav_align == 'left') ? 'checked="checked"' : '';
$nav_align_center = ($nav_align == 'center') ? 'checked="checked"' : '';
$nav_align_right = ($nav_align == '' || $nav_align == 'right') ? 'checked="checked"' : '';

//1차메뉴중에 첫번째 메뉴의 스타일을 별도로 설정
$menu1_first_1 = ($menu1_first == 'yes') ? 'checked="checked"' : '';
$menu1_first_0 = ($menu1_first == '' || $menu1_first == 'no') ? 'checked="checked"' : '';

//1차메뉴들 사이에 구분선 표시의 여부를 설정
$menu1_gubun_1 = ($menu1_gubun == 'yes') ? 'checked="checked"' : '';
$menu1_gubun_0 = ($menu1_gubun == '' || $menu1_gubun == 'no') ? 'checked="checked"' : '';

//1차메뉴 드롭다운 아이콘 표시 여부 설정
$menu1_icon_1 = ($menu1_icon == 'yes') ? 'checked="checked"' : '';
$menu1_icon_0 = ($menu1_icon == '' || $menu1_icon == 'no') ? 'checked="checked"' : '';

//2차,3차(서브)메뉴의 표시 여부를 설정
$menu2_sub_1 = ($menu2_sub == '' || $menu2_sub == 'yes') ? 'checked="checked"' : '';
$menu2_sub_0 = ($menu2_sub == 'no') ? 'checked="checked"' : '';

//2차메뉴그룹의 그림자 표시의 여부를 설정
$menu2_shadow_1 = ($menu2_shadow == 'yes') ? 'checked="checked"' : '';
$menu2_shadow_0 = ($menu2_shadow == '' || $menu2_shadow == 'no') ? 'checked="checked"' : '';

//2차메뉴의 정렬 설정
$menu2_align_left = ($menu2_align == '' || $menu2_align == 'left') ? 'checked="checked"' : '';
$menu2_align_center = ($menu2_align == 'center') ? 'checked="checked"' : '';

//2차메뉴 드롭다운 아이콘 표시 여부 설정
$menu2_icon_1 = ($menu2_icon == 'yes') ? 'checked="checked"' : '';
$menu2_icon_0 = ($menu2_icon == '' || $menu2_icon == 'no') ? 'checked="checked"' : '';


//3차메뉴그룹의 그림자 표시의 여부를 설정
$menu3_shadow_1 = ($menu3_shadow == 'yes') ? 'checked="checked"' : '';
$menu3_shadow_0 = ($menu3_shadow == '' || $menu3_shadow == 'no') ? 'checked="checked"' : '';

//2차메뉴의 텍스트 정렬 설정
$menu3_align_left = ($menu3_align == '' || $menu3_align == 'left') ? 'checked="checked"' : '';
$menu3_align_center = ($menu3_align == 'center') ? 'checked="checked"' : '';


//링크 체크
$logo_url = ($logo_url) ? bwg_g5_url_check($logo_url) : '';

//로고 표시/비표시의 여부를 설정
$logo_show_1 = ($logo_show == '' || $logo_show == 'yes') ? 'checked="checked"' : '';
$logo_show_0 = ($logo_show == 'no') ? 'checked="checked"' : '';

//로고URL을 새창/현재창으로 열지의 여부를 설정
$logo_new_1 = ($logo_new == '_blank') ? 'checked="checked"' : '';
$logo_new_0 = ($logo_new == '' || $logo_new == '_self') ? 'checked="checked"' : '';


//패스 애니메이션 사용여부 설정
$path_anim_1 = ($path_anim == 'yes') ? 'checked="checked"' : '';
$path_anim_0 = ($path_anim == '' || $path_anim == 'no') ? 'checked="checked"' : '';

//패스 채우기 사용여부 설정
$path_fill_1 = ($path_fill == 'yes') ? 'checked="checked"' : '';
$path_fill_0 = ($path_fill == '' || $path_fill == 'no') ? 'checked="checked"' : '';

$nom_th_bg_color = '#f1f1f1';//연한핑크배경색
$svg_th_bg_color = '#fbe3fa';//연한핑크배경색

$colspan7=7;
$colspan5=5;
$colspan3=3;
?>
<link rel="stylesheet" href="<?=$bwg_skin_set_url?>/bpwg_form_style.css">
<div id="bwg_skin_set">
	<h2 class="h2_frm">헤더영역 공통설정</h2>
	<?php
	//echo bpwg_input_color('','rgba(3, 3, 3, 1)',$w,1);
	//echo bpwg_input_range('',0.5,$w,0,1,0.05,130,48,'초');
	?>
	<table class="tbl_frm" id="bwg_skin_opt1">
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
				<th>기본설정</th>
				<td colspan="<?=$colspan7?>">
					<table class="stbl">
						<!--th90,td157-->
						<colgroup>
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
						</colgroup>
						<tbody>
							<tr>
								<th>헤더영역<br>기본너비(폭)</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("헤더영역의 기본너비를 설정.<br>(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$head_default_wd = (isset($head_default_wd)) ? $head_default_wd : 1080;
									echo bpwg_input_range('bwo[head_default_wd]',$head_default_wd,$w,900,1300,10,'147',45,'px');
									?>
								</td>
								<th>헤더영역<br>그림자표시</th>
								<td class="bwg_help">
									<?php echo bwg_help("하단으로 스크롤 이동시 헤더영역의 그림자를 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_head_shadow_1" class="label_radio first_child bwo_head_shadow_1">
											<input type="radio" id="bwo_head_shadow_1" name="bwo[head_shadow]" value="yes" <?=$head_shadow_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_head_shadow_0" class="label_radio bwo_head_shadow_0">
											<input type="radio" id="bwo_head_shadow_0" name="bwo[head_shadow]" value="no" <?=$head_shadow_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
								<th>헤더그림자<br>크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("헤더그림자의 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$head_shadow_size = (isset($head_shadow_size)) ? $head_shadow_size : 5;
									echo bpwg_input_range('bwo[head_shadow_size]',$head_shadow_size,$w,0,20,1,'100%',38,'px');
									?>
								</td>
							</tr>
							<tr>
								<th>헤더그림자<br>색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("스크롤 하단이동시 헤더 그림자의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[head_shadow_color]',$head_shadow_color,$w,1); ?>
								</td>
								<th>헤더그림자<br>X위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("헤더그림자의 X위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$head_shadow_x = (isset($head_shadow_x)) ? $head_shadow_x : 1;
									echo bpwg_input_range('bwo[head_shadow_x]',$head_shadow_x,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th>헤더그림자<br>Y위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("헤더그림자의 Y위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$head_shadow_y = (isset($head_shadow_y)) ? $head_shadow_y : 1;
									echo bpwg_input_range('bwo[head_shadow_y]',$head_shadow_y,$w,0,20,1,'100%',38,'px');
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th>전체배경</th>
				<td colspan="<?=$colspan7?>">
					<table class="stbl">
						<!--th90,td157-->
						<colgroup>
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
						</colgroup>
						<tbody>
							<tr>
								<th>헤더배경<br>스크롤탑</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("스크롤이 최상단일때 헤더의<br>배경색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toppos_color]',$toppos_color,$w,1); ?>
								</td>
								<th>헤더배경<br>스크롤하단</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("스크롤이 하단으로 내려올때 헤더의<br>배경색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[sclpos_color]',$sclpos_color,$w,1); ?>
								</td>
							</tr>
							<tr>
								<th>헤더하단라인<br>스크롤탑</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("스크롤이 최상단일때 헤더하단의<br>라인색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toppos_line_color]',$toppos_line_color,$w,1); ?>
								</td>
								<th>헤더하단라인<br>스크롤하단</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("스크롤이 하단으로 내려올때 헤더하단의<br>라인색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[sclpos_line_color]',$sclpos_line_color,$w,1); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th>탑링크배경</th>
				<td colspan="<?=$colspan7?>">
					<table class="stbl">
						<!--th90,td157-->
						<colgroup>
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
						</colgroup>
						<tbody>
							<tr>
								<th>스크롤상단<br>색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("헤더의 탑링크 영역의 스크롤 최상단일때<br>배경색상과 투명도를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toplink_top_color]',$toplink_top_color,$w,1); ?>
								</td>
								<th>폰트색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("헤더의 탑링크 영역의<br>폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toplinkfont_color]',$toplinkfont_color,$w,0); ?>
								</td>
							</tr>
							<tr>
								<th>스크롤하단<br>색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("헤더의 탑링크 영역의 스크롤 하단이동일때 <br>배경색상과 투명도를 설정하세요.(본 헤더 스킨에서는 방영이 안됩니다.)",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toplink_low_color]',$toplink_low_color,$w,1); ?>
								</td>
								<th>하단 라인색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("헤더의 탑링크 영역의<br>하단 라인색상을 설정하세요.(본 헤더 스킨에서는 방영이 안됩니다.)",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[toplink_line_color]',$toplink_line_color,$w,1); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th>검색설정</th>
				<td colspan="<?=$colspan7?>">
					<table class="stbl">
						<!--th90,td157-->
						<colgroup>
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
						</colgroup>
						<tbody>
							<tr>
								<th>검색사용여부</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("검색 사용여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_sch_use_1" class="label_radio first_child bwo_sch_use_1">
											<input type="radio" id="bwo_sch_use_1" name="bwo[sch_use]" value="yes" <?=$sch_use_1?>>
											<strong></strong>
											<span>사용</span>
										</label>
										<label for="bwo_sch_use_0" class="label_radio bwo_sch_use_0">
											<input type="radio" id="bwo_sch_use_0" name="bwo[sch_use]" value="no" <?=$sch_use_0?>>
											<strong></strong>
											<span>사용안함</span>
										</label>
									</div>
								</td>
								<th>검색유형</th>
								<td class="bwg_help">
									<?php echo bwg_help("검색 유형(상품검색/일반검색)을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_sch_shop_1" class="label_radio first_child bwo_sch_shop_1">
											<input type="radio" id="bwo_sch_shop_1" name="bwo[sch_shop]" value="yes" <?=$sch_shop_1?>>
											<strong></strong>
											<span>상품</span>
										</label>
										<label for="bwo_sch_shop_0" class="label_radio bwo_sch_shop_0">
											<input type="radio" id="bwo_sch_shop_0" name="bwo[sch_shop]" value="no" <?=$sch_shop_0?>>
											<strong></strong>
											<span>일반</span>
										</label>
									</div>
								</td>
								<th>폰트/아이콘</th>
								<td class="bwg_help">
									<?php echo bwg_help("검색의 폰트/아이콘 색상을 선택하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[schfont_color]',$schfont_color,$w,0); ?>
								</td>
							</tr>
							<tr>
								<th>검색입력란<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("검색의 배경색상",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[schbg_color]',$schbg_color,$w,1); ?>
								</td>
								<th>테두리</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("검색(테두리)색상을 선택하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[sch_color]',$sch_color,$w,1); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<th>메뉴스킨설정<br>[<a href="<?=G5_BPWIDGET_ADMIN_ADM_URL?>/menu_list.php" target="_blank" style="color:blue;">메뉴추가/삭제</a>]</th>
				<td colspan="<?=$colspan7?>">
					<table class="stbl">
						<!--th90,td157-->
						<colgroup>
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
							<col span="1" width="90">
							<col span="1" width="157">
						</colgroup>
						<tbody>
							<tr style="border-bottom:0;">
								<th class="sthd_bg1">전체기본<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("네비게이션 기본배경색상",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[nav_bg_color]',$nav_bg_color,$w,1); ?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>전체정렬</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴 전체 정렬을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_nav_align_left" class="label_radio first_child bwo_nav_align_left">
											<input type="radio" id="bwo_nav_align_left" name="bwo[nav_align]" value="left" <?=$nav_align_left?>>
											<strong></strong>
											<span>왼쪽</span>
										</label>
										<label for="bwo_nav_align_center" class="label_radio bwo_nav_align_center">
											<input type="radio" id="bwo_nav_align_center" name="bwo[nav_align]" value="center" <?=$nav_align_center?>>
											<strong></strong>
											<span>중앙</span>
										</label>
										<label for="bwo_nav_align_right" class="label_radio bwo_nav_align_right">
											<input type="radio" id="bwo_nav_align_right" name="bwo[nav_align]" value="right" <?=$nav_align_right?>>
											<strong></strong>
											<span>오른쪽</span>
										</label>
									</div>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">첫번째메뉴<br>스타일별도<?=($menu1_first == '')?></th>
								<td class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 스타일을 별도로 설정할지의 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu1_first_1" class="label_radio first_child bwo_menu1_first_1">
											<input type="radio" id="bwo_menu1_first_1" name="bwo[menu1_first]" value="yes" <?=$menu1_first_1?>>
											<strong></strong>
											<span>네</span>
										</label>
										<label for="bwo_menu1_first_0" class="label_radio bwo_menu1_first_0">
											<input type="radio" id="bwo_menu1_first_0" name="bwo[menu1_first]" value="no" <?=$menu1_first_0?>>
											<strong></strong>
											<span>아니오</span>
										</label>
									</div>
								</td>
								<th class="sthd_bg1">첫번째메뉴<br>폰트색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_first_font_color]',$menu1_first_font_color,$w); ?>
								</td>
								<th class="sthd_bg1">첫번째메뉴<br>롤오버폰트</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 롤오버시 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_first_hover_font_color]',$menu1_first_hover_font_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">첫번째메뉴<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_first_bg_color]',$menu1_first_bg_color,$w,1); ?>
								</td>
								<th class="sthd_bg1">첫번째메뉴<br>롤오버배경</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 롤오버시 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_first_hover_bg_color]',$menu1_first_hover_bg_color,$w,1); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">1차메뉴 너비</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의 너비(폭)를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_wd = (isset($menu1_wd)) ? $menu1_wd : 100;
									echo bpwg_input_range('bwo[menu1_wd]',$menu1_wd,$w,80,400,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg1">1차메뉴 높이</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의  높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_ht = (isset($menu1_ht)) ? $menu1_ht : 40;
									echo bpwg_input_range('bwo[menu1_ht]',$menu1_ht,$w,20,200,1,'147',38,'px');
									?>
								</td>
								<th class="sthd_bg1">1차메뉴간<br>구분선표시</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴들 사이에 구분선을 표시 할지의 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu1_gubun_1" class="label_radio first_child bwo_menu1_gubun_1">
											<input type="radio" id="bwo_menu1_gubun_1" name="bwo[menu1_gubun]" value="yes" <?=$menu1_gubun_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu1_gubun_0" class="label_radio bwo_menu1_gubun_0">
											<input type="radio" id="bwo_menu1_gubun_0" name="bwo[menu1_gubun]" value="no" <?=$menu1_gubun_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">1차메뉴간<br>구분선높이</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의 구분선 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_gubun_ht = (isset($menu1_gubun_ht)) ? $menu1_gubun_ht : 12;
									echo bpwg_input_range('bwo[menu1_gubun_ht]',$menu1_gubun_ht,$w,5,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg1">1차메뉴간<br>구분선색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("첫번째 메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_gubun_color]',$menu1_gubun_color,$w); ?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>폰트크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_font_size = (isset($menu1_font_size)) ? $menu1_font_size : 14;
									echo bpwg_input_range('bwo[menu1_font_size]',$menu1_font_size,$w,8,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>폰트두께</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_font_weight = (isset($menu1_font_weight)) ? $menu1_font_weight : 600;
									echo bpwg_input_range('bwo[menu1_font_weight]',$menu1_font_weight,$w,100,900,100,'100%',30);
									?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">1차메뉴<br>폰트색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_font_color]',$menu1_font_color,$w); ?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>롤오버폰트</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴의 롤오버시 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_font_hover_color]',$menu1_font_hover_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">1차메뉴<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_bg_color]',$menu1_bg_color,$w,1); ?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>롤오버배경</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php //echo $menu1_bg_hover_color; ?>
									<?php echo bwg_help("1차메뉴의 롤오버시 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_bg_hover_color]',$menu1_bg_hover_color,$w,1); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg1">1차메뉴<br>아이콘표시</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴 드롭다운 아이콘 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu1_icon_1" class="label_radio first_child bwo_menu1_icon_1">
											<input type="radio" id="bwo_menu1_icon_1" name="bwo[menu1_icon]" value="yes" <?=$menu1_icon_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu1_icon_0" class="label_radio bwo_menu1_icon_0">
											<input type="radio" id="bwo_menu1_icon_0" name="bwo[menu1_icon]" value="no" <?=$menu1_icon_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>							
								<th class="sthd_bg1">1차메뉴<br>아이콘크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("1차메뉴의 드롭다운 아이콘 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu1_icon_size = (isset($menu1_icon_size)) ? $menu1_icon_size : 16;
									echo bpwg_input_range('bwo[menu1_icon_size]',$menu1_icon_size,$w,8,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg1">1차메뉴<br>아이콘색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("1차메뉴의 드롭다운 아이콘 색상을 설정하세요.(이미지일경우 색상은 반영 안됩니다.)",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu1_icon_color]',$menu1_icon_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">서브메뉴<br>표시여부</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차,3차(서브)메뉴의 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu2_sub_1" class="label_radio first_child bwo_menu2_sub_1">
											<input type="radio" id="bwo_menu2_sub_1" name="bwo[menu2_sub]" value="yes" <?=$menu2_sub_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu2_sub_0" class="label_radio bwo_menu2_sub_0">
											<input type="radio" id="bwo_menu2_sub_0" name="bwo[menu2_sub]" value="no" <?=$menu2_sub_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
								<th class="sthd_bg2">2차메뉴 너비</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 너비(폭)를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_wd = (isset($menu2_wd)) ? $menu2_wd : 100;
									echo bpwg_input_range('bwo[menu2_wd]',$menu2_wd,$w,80,300,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴 높이</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_ht = (isset($menu2_ht)) ? $menu2_ht : 40;
									echo bpwg_input_range('bwo[menu2_ht]',$menu2_ht,$w,20,200,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>그림자표시</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴그룹의 그림자를 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu2_shadow_1" class="label_radio first_child bwo_menu2_shadow_1">
											<input type="radio" id="bwo_menu2_shadow_1" name="bwo[menu2_shadow]" value="yes" <?=$menu2_shadow_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu2_shadow_0" class="label_radio bwo_menu2_shadow_0">
											<input type="radio" id="bwo_menu2_shadow_0" name="bwo[menu2_shadow]" value="no" <?=$menu2_shadow_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">2차메뉴<br>그림자X위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴 그림자의 X위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_shadow_x = (isset($menu2_shadow_x)) ? $menu2_shadow_x : 1;
									echo bpwg_input_range('bwo[menu2_shadow_x]',$menu2_shadow_x,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>그림자Y위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴 그림자의 Y위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_shadow_y = (isset($menu2_shadow_y)) ? $menu2_shadow_y : 1;
									echo bpwg_input_range('bwo[menu2_shadow_y]',$menu2_shadow_y,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>그림자크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴 그림자의 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_shadow_size = (isset($menu2_shadow_size)) ? $menu2_shadow_size : 5;
									echo bpwg_input_range('bwo[menu2_shadow_size]',$menu2_shadow_size,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>그림자색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 그림자 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_shadow_color]',$menu2_shadow_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">2차메뉴<br>폰트크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_font_size = (isset($menu2_font_size)) ? $menu2_font_size : 12;
									echo bpwg_input_range('bwo[menu2_font_size]',$menu2_font_size,$w,8,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>폰트두께</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_font_weight = (isset($menu2_font_weight)) ? $menu2_font_weight : 400;
									echo bpwg_input_range('bwo[menu2_font_weight]',$menu2_font_weight,$w,100,900,100,'100%',30);
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>폰트정렬</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 폰트 정렬을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu2_align_left" class="label_radio first_child bwo_menu2_align_left">
											<input type="radio" id="bwo_menu2_align_left" name="bwo[menu2_align]" value="left" <?=$menu2_align_left?>>
											<strong></strong>
											<span>왼쪽</span>
										</label>
										<label for="bwo_menu2_align_center" class="label_radio bwo_menu2_align_center">
											<input type="radio" id="bwo_menu2_align_center" name="bwo[menu2_align]" value="center" <?=$menu2_align_center?>>
											<strong></strong>
											<span>중앙</span>
										</label>
									</div>
								</td>
								<th class="sthd_bg2">2차메뉴<br>들여쓰기</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 왼쪽에서의 들여쓰기 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_indent = (isset($menu2_indent)) ? $menu2_indent : 10;
									echo bpwg_input_range('bwo[menu2_indent]',$menu2_indent,$w,0,30,1,'100%',38,'px');
									?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">2차메뉴<br>라인색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("2차메뉴의 라인 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_line_color]',$menu2_line_color,$w,1); ?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>폰트색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_font_color]',$menu2_font_color,$w); ?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>롤오버폰트</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_font_hover_color]',$menu2_font_hover_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">2차메뉴<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("2차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_bg_color]',$menu2_bg_color,$w,1); ?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>롤오버배경</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("2차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_bg_hover_color]',$menu2_bg_hover_color,$w,1); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg2">2차메뉴<br>아이콘표시</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴 드롭다운 아이콘 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu2_icon_1" class="label_radio first_child bwo_menu2_icon_1">
											<input type="radio" id="bwo_menu2_icon_1" name="bwo[menu2_icon]" value="yes" <?=$menu2_icon_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu2_icon_0" class="label_radio bwo_menu2_icon_0">
											<input type="radio" id="bwo_menu2_icon_0" name="bwo[menu2_icon]" value="no" <?=$menu2_icon_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
								<th class="sthd_bg2">2차메뉴<br>아이콘크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("2차메뉴의 아이콘 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu2_icon_size = (isset($menu2_icon_size)) ? $menu2_icon_size : 12;
									echo bpwg_input_range('bwo[menu2_icon_size]',$menu2_icon_size,$w,8,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg2">2차메뉴<br>아이콘색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("2차메뉴의 아이콘 색상을 설정하세요.(이미지일경우 색상은 반영 안됩니다.)",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu2_icon_color]',$menu2_icon_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg3">3차메뉴 너비</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 너비(폭)를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_wd = (isset($menu3_wd)) ? $menu3_wd : 100;
									echo bpwg_input_range('bwo[menu3_wd]',$menu3_wd,$w,80,300,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴 높이</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_ht = (isset($menu3_ht)) ? $menu3_ht : 40;
									echo bpwg_input_range('bwo[menu3_ht]',$menu3_ht,$w,20,200,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>그림자표시</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("3차메뉴그룹의 그림자를 표시 여부를 선택하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu3_shadow_1" class="label_radio first_child bwo_menu3_shadow_1">
											<input type="radio" id="bwo_menu3_shadow_1" name="bwo[menu3_shadow]" value="yes" <?=$menu3_shadow_1?>>
											<strong></strong>
											<span>표시</span>
										</label>
										<label for="bwo_menu3_shadow_0" class="label_radio bwo_menu3_shadow_0">
											<input type="radio" id="bwo_menu3_shadow_0" name="bwo[menu3_shadow]" value="no" <?=$menu3_shadow_0?>>
											<strong></strong>
											<span>비표시</span>
										</label>
									</div>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg3">3차메뉴<br>그림자X위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴 그림자의 X위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_shadow_x = (isset($menu3_shadow_x)) ? $menu3_shadow_x : 1;
									echo bpwg_input_range('bwo[menu3_shadow_x]',$menu3_shadow_x,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>그림자Y위치</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴 그림자의 Y위치 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_shadow_y = (isset($menu3_shadow_y)) ? $menu3_shadow_y : 1;
									echo bpwg_input_range('bwo[menu3_shadow_y]',$menu3_shadow_y,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>그림자크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴 그림자의 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_shadow_size = (isset($menu3_shadow_size)) ? $menu3_shadow_size : 5;
									echo bpwg_input_range('bwo[menu3_shadow_size]',$menu3_shadow_size,$w,0,20,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>그림자색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 그림자 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_shadow_color]',$menu3_shadow_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg3">3차메뉴<br>폰트크기</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_font_size = (isset($menu3_font_size)) ? $menu3_font_size : 12;
									echo bpwg_input_range('bwo[menu3_font_size]',$menu3_font_size,$w,8,30,1,'100%',38,'px');
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>폰트두께</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_font_weight = (isset($menu3_font_weight)) ? $menu3_font_weight : 400;
									echo bpwg_input_range('bwo[menu3_font_weight]',$menu3_font_weight,$w,100,900,100,'100%',30);
									?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>폰트정렬</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 폰트 정렬을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<div>
										<label for="bwo_menu3_align_left" class="label_radio first_child bwo_menu3_align_left">
											<input type="radio" id="bwo_menu3_align_left" name="bwo[menu3_align]" value="left" <?=$menu3_align_left?>>
											<strong></strong>
											<span>왼쪽</span>
										</label>
										<label for="bwo_menu3_align_center" class="label_radio bwo_menu3_align_center">
											<input type="radio" id="bwo_menu3_align_center" name="bwo[menu3_align]" value="center" <?=$menu3_align_center?>>
											<strong></strong>
											<span>중앙</span>
										</label>
									</div>
								</td>
								<th class="sthd_bg3">3차메뉴<br>들여쓰기</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 왼쪽에서의 들여쓰기 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
									<?php
									$menu3_indent = (isset($menu3_indent)) ? $menu3_indent : 10;
									echo bpwg_input_range('bwo[menu3_indent]',$menu3_indent,$w,0,30,1,'100%',38,'px');
									?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg3">3차메뉴<br>라인색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("3차메뉴의 라인 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_line_color]',$menu3_line_color,$w,1); ?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>폰트색상</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_font_color]',$menu3_font_color,$w); ?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>롤오버폰트</th>
								<td class="bwg_help">
									<?php echo bwg_help("3차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_font_hover_color]',$menu3_font_hover_color,$w); ?>
								</td>
							</tr>
							<tr>
								<th class="sthd_bg3">3차메뉴<br>배경색상</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("3차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_bg_color]',$menu3_bg_color,$w,1); ?>
								</td>
								<th class="sthd_bg3">3차메뉴<br>롤오버배경</th>
								<td colspan="<?=$colspan3?>" class="bwg_help">
									<?php echo bwg_help("3차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
									<?php echo bpwg_input_color('bwo[menu3_bg_hover_color]',$menu3_bg_hover_color,$w,1); ?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h2 class="h2_frm">로고 설정</h2>
	<p>
	아래 타이틀 배경색이<strong style="color:red;">핑크영역</strong>은 <strong style="color:blue;">텍스트SVG 패스(선) 애니메이션</strong>을 위한 설정내용입니다.<br>
	일반 이미지는 SVG관련설정이 반영되지 않습니다.<br>
	<strong><span style="color:red;">SVG파일 제작시 중요한 주의사항 : </span></strong><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><span>SVG제작시 사용하는 폰트(서체)는 반드시 이미지로 변환(서체 깨트리기:Create Outline)해서 저장해 주세요.</span></strong><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><span>즉,폰트를 선과면의 구조로 변환해 주세요.(그렇지 않으면 애니메이션 효과를 줄 수 없습니다.)</span></strong><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:blue;">SVG안에 구성하는 모든 요소는 path요소로만 구성해야하며 g요소등의 그룹/계층 구조로 되어 있으면 안됩니다.</span><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:blue;">(line요소,polygon요소는 표현이 안됩니다.)</span><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:green;">SVG 요소 외의 태그는 전부 삭제해 주세요. 그렇지 않으면 서버환경에 따라 오류가 발생할 수 있습니다.</span><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:green;">(예를 들어 svg태그 위에 생성되는 아래와 같은 태그는 삭제하고 저장해 주세요.)</span><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">&lt;?xml version="1.0" encoding="utf-8"?&gt;</span><br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">&lt;!-- Generator: Adobe Illustrator 22.0.1, SVG Export Plug-In . SVG Version: 6.00 Build 0)  --&gt;</span><br>
	
	</p><br>
	<table class="tbl_frm" id="bwg_skin_opt2">
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
				<th>이미지 너비</th>
				<td class="bwg_help opt_td_logo_size">
					<?php echo bwg_help("로고의 가로 크기(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
					<?php
					$logo_width = (isset($logo_width)) ? $logo_width : 40;
					echo bpwg_input_range('bwo[logo_width]',$logo_width,$w,20,180,1,'147',38,'px');
					?>
				</td>
				<th>이미지 높이</th>
				<td class="bwg_help opt_td_logo_size">
					<?php echo bwg_help("로고의 세로 크기(px)를 설정해 주세요.",1,'#555555','#eeeeee'); ?>
					<?php
					$logo_height = (isset($logo_height)) ? $logo_height : 40;
					echo bpwg_input_range('bwo[logo_height]',$logo_height,$w,20,150,1,'147',38,'px');
					?>
				</td>
				<th>로고/네비영역<br>전체높이</th>
				<td colspan="<?=$colspan3?>" class="bwg_help">
					<?php echo bwg_help("로고/네비게이션에 해당하는 영역의 전체 높이를 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php
					$logo_area_height = (isset($logo_area_height)) ? $logo_area_height : 110;
					echo bpwg_input_range('bwo[logo_area_height]',$logo_area_height,$w,20,150,1,'147',38,'px');
					?>
				</td>
			</tr>
			<tr>
				<th>로고URL</th>
				<td colspan="<?=$colspan3?>" class="bwg_help">
					<?php echo bwg_help("로고URL의 입력값이 없으면 자동으로 기본URL값으로 설정이 됩니다.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[logo_url]" class="bp_wdp100" value="<?=$logo_url?>">
				</td>
				<th>로고<br>표시여부</th>
				<td class="bwg_help">
					<?php echo bwg_help("로고를 표시/비표시의 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
					<div>
						<label for="bwo_logo_show_1" class="label_radio first_child bwo_logo_show_1">
							<input type="radio" id="bwo_logo_show_1" name="bwo[logo_show]" value="yes" <?=$logo_show_1?>>
							<strong></strong>
							<span>표시</span>
						</label>
						<label for="bwo_logo_show_0" class="label_radio bwo_logo_show_0">
							<input type="radio" id="bwo_logo_show_0" name="bwo[logo_show]" value="no" <?=$logo_show_0?>>
							<strong></strong>
							<span>비표시</span>
						</label>
					</div>
				</td>
				<th>로고URL<br>새창여부</th>
				<td class="bwg_help">
					<?php echo bwg_help("로고링크를 새창/현재창으로 열지의 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
					<div>
						<label for="bwo_logo_new_1" class="label_radio first_child bwo_logo_new_1">
							<input type="radio" id="bwo_logo_new_1" name="bwo[logo_new]" value="_blank" <?=$logo_new_1?>>
							<strong></strong>
							<span>새창</span>
						</label>
						<label for="bwo_logo_new_0" class="label_radio bwo_logo_new_0">
							<input type="radio" id="bwo_logo_new_0" name="bwo[logo_new]" value="_self" <?=$logo_new_0?>>
							<strong></strong>
							<span>현재창</span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">패스(선)애니<br>사용</th>
				<td class="bwg_help">
					<?php echo bwg_help("패스애니메이션사용 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
					<div>
						<label for="bwo_path_anim_1" class="label_radio first_child bwo_path_anim_1">
							<input type="radio" id="bwo_path_anim_1" name="bwo[path_anim]" value="yes" <?=$path_anim_1?>>
							<strong></strong>
							<span>네</span>
						</label>
						<label for="bwo_path_anim_0" class="label_radio bwo_path_anim_0">
							<input type="radio" id="bwo_path_anim_0" name="bwo[path_anim]" value="no" <?=$path_anim_0?>>
							<strong></strong>
							<span>아니오</span>
						</label>
					</div>
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">채우기애니<br>사용</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("패스채우기 사용여부 여부를 설정하세요.",1,'#555555','#eeeeee'); ?>
					<div>
						<label for="bwo_path_fill_1" class="label_radio first_child bwo_path_fill_1">
							<input type="radio" id="bwo_path_fill_1" name="bwo[path_fill]" value="yes" <?=$path_fill_1?>>
							<strong></strong>
							<span>네</span>
						</label>
						<label for="bwo_path_fill_0" class="label_radio bwo_path_fill_0">
							<input type="radio" id="bwo_path_fill_0" name="bwo[path_fill]" value="no" <?=$path_fill_0?>>
							<strong></strong>
							<span>아니오</span>
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">각패스(선)별<br>애니시간</th>
				<td class="bwg_help">
					<?php echo bwg_help("개별 패스 애니메이션 시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
					<div>
						<?php
						$path_time = (isset($path_time)) ? $path_time : 2.0;
						echo bpwg_input_range('bwo[path_time]',$path_time,$w,0,5.0,0.1,'100%',38,'초');
						?>
					</div>
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">각패스(선)별<br>시간차</th>
				<td class="bwg_help">
					<?php echo bwg_help("각 패스별 애니메이션 시간차를 의미합니다.",1,'#555555','#eeeeee'); ?>
					<div>
						<?php
						$time_diff = (isset($time_diff)) ? $time_diff : 0.3;
						echo bpwg_input_range('bwo[time_diff]',$time_diff,$w,0,5,0.1,'100%',38,'초');
						?>
					</div>
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">기본<br>패스색상</th>
				<td colspan="<?=$colspan3?>" class="bwg_help">
					<?php echo bwg_help("기본 패스색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[path_color]',$path_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">채우기<br>속도</th>
				<td class="bwg_help">
					<?php echo bwg_help("채우기 속도 시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
					<div>
						<?php
						$fill_speed = (isset($fill_speed)) ? $fill_speed : 0.5;
						echo bpwg_input_range('bwo[fill_speed]',$fill_speed,$w,0,5,0.1,'100%',38,'초');
						?>
					</div>
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">채우기<br>지연시간</th>
				<td class="bwg_help">
					<?php echo bwg_help("채우기 시작하기까지의 지연시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
					<div>
						<?php
						$fill_delay = (isset($fill_delay)) ? $fill_delay : 4;
						echo bpwg_input_range('bwo[fill_delay]',$fill_delay,$w,0,10,0.1,'100%',38,'초');
						?>
					</div>
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">기본<br>채우기색상</th>
				<td colspan="<?=$colspan3?>" class="bwg_help">
					<?php echo bwg_help("기본 채우기색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[fill_color]',$fill_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<td colspan="8">
				<br>
				아래의 클래스와 색상 설정은 svg로 제작된 로고의 path에 별도의 class명을 지정하여<br>
				class별로 별도의 색상을 설정하기 위한 폼입니다.<br>
				class를 2개이상 입력할 경우 중간에 공백란 없이 순서대로 입력해 주세요.<br>
				공백 이후의 class값과 색상은 반영되지 않습니다.<br>
				path요소에 인라인으로 지정되어 있는 색상 설정은 삭제해 주세요.
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">클래스1</th>
				<td class="bwg_help">
					<?php echo bwg_help("색상구분을 위해 클래스1의 CLASS값을 입력하세요.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[class1]" class="bp_wdp100" value="<?=$class1?>">
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">클래스1<br>색상</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("클래스1의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[class1_color]',$class1_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">클래스2</th>
				<td class="bwg_help">
					<?php echo bwg_help("색상구분을 위해 클래스2의 CLASS값을 입력하세요.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[class2]" class="bp_wdp100" value="<?=$class2?>">
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">클래스2<br>색상</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("클래스2의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[class2_color]',$class2_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">클래스3</th>
				<td class="bwg_help">
					<?php echo bwg_help("색상구분을 위해 클래스3의 CLASS값을 입력하세요.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[class3]" class="bp_wdp100" value="<?=$class3?>">
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">클래스3<br>색상</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("클래스3의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[class3_color]',$class3_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">클래스4</th>
				<td class="bwg_help">
					<?php echo bwg_help("색상구분을 위해 클래스4의 CLASS값을 입력하세요.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[class4]" class="bp_wdp100" value="<?=$class4?>">
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">클래스4<br>색상</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("클래스4의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[class4_color]',$class4_color,$w,1); ?>
				</td>
			</tr>
			<tr>
				<th style="background:<?=$svg_th_bg_color?>;">클래스5</th>
				<td class="bwg_help">
					<?php echo bwg_help("색상구분을 위해 클래스5의 CLASS값을 입력하세요.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="bwo[class5]" class="bp_wdp100" value="<?=$class5?>">
				</td>
				<th style="background:<?=$svg_th_bg_color?>;">클래스5<br>색상</th>
				<td colspan="<?=$colspan5?>" class="bwg_help">
					<?php echo bwg_help("클래스5의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<?php echo bpwg_input_color('bwo[class5_color]',$class5_color,$w,1); ?>
				</td>
			</tr>
			<?php if($file_upload_flag){ ?>
			<tr>
				<th class="">로고<br>이미지</th>
				<td colspan="<?=$colspan7?>">
					<?php
					$file_name = 'afile';
					${$file_name.'_maxlength'} = 1;
					${$file_name.'_accept'} = 'png|jpg|jpeg|gif|svg';
					//${$file_name.'_data_maxsize'} = 0; //업로드되는 파일들 전체용량
					${$file_name.'_data_maxfile'} = $g5['bpwidget']['bwgf_filesize']; //각각의 파일용량
					${$file_name.'_uploaded'} = count(${$file_name});
					${$file_name.'_remain_cnt'} = ${$file_name.'_maxlength'} - ${$file_name.'_uploaded'};
					echo "<p style='color:#223390;'>총 ".${$file_name.'_maxlength'}."개 까지만 가능하고, 업로드 가능한 파일수는 ".${$file_name.'_remain_cnt'}."개 입니다.<br>각각의 파일 용량은 ".${$file_name.'_data_maxfile'}."KB이하로 업로드 해 주세요.</p>".PHP_EOL;
					echo '<p style="color:red;font-weight:700;">SVG애니메이션은 PC모드의 explorer브라우저는 동작하지 않고, safari브라우저에서는 표시와 동작 둘 다 원활하지 않습니다.</p>'.PHP_EOL;
					echo '<p style="color:green;font-weight:700;">모든 브라우저에서의 원활한 표시를 원하시면 로고이미지를 일반이미지파일(gif,jpg,png)로 등록해 주세요.</p>'.PHP_EOL;
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
									<img class="thumb" width="<?=$thumb_wd?>" height="<?=$thumb_ht?>" bwga_idx="<?=${$file_name}[$i]['bwga_idx']?>" bwga_title="<?=${$file_name}[$i]['bwga_title']?>" bwga_rank="<?=${$file_name}[$i]['bwga_rank']?>" bwga_sort="<?=${$file_name}[$i]['bwga_sort']?>" bwga_status="<?=${$file_name}[$i]['bwga_status']?>" bwga_content="<?=${$file_name}[$i]['bwga_content']?>" thumb_m="<?=${$file_name}[$i]['thumb_m_url']?>" title="개별 이미지 변경(가로:<?=${$file_name}[$i]['bwga_width']?>,가로:<?=${$file_name}[$i]['bwga_height']?>)" src="<?=${$file_name}[$i]['thumb_url']?>">
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
	$('#bwgs_tab li a:contains(내용목록)').attr('href','javascript:');
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

	//반드시 존재해야 하는 함수
	function fbpwidgetoptionform_submit(){
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
		
		return true;
	}
	</script>
</div><!--#bwg_skin_set-->