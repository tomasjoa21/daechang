<?php
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//adm/ajax/bpwidget_call_skin_config.php
//아작스로 이 페이지가 호출될때는 add_stylesheet() OR add_javascript()함수가 반영되지 않는다.
//add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_form_style.css">', 1);
//이미지 업로드를 사용하려면 1로 변경하세요.
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
/*
//링크 체크
$logo_url = ($logo_url) ? bwg_g5_url_check($logo_url) : '';
//라디오버튼
$nav_align_left = ($nav_align == 'left') ? 'checked="checked"' : '';
$nav_align_center = ($nav_align == 'center') ? 'checked="checked"' : '';
$nav_align_right = ($nav_align == '' || $nav_align == 'right') ? 'checked="checked"' : '';
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
//라디오버튼2
$radio_wu_disable_flag = ($w == 'u') ? 1 : 0; //수정모드에서 비활성화할 라디오버튼박스에 사용하는 변수
<?php echo bwgf_radio_checked($bwgf_device, 'bwgs_device', $bwgs_device, $radio_wu_disable_flag);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
//컬러/투명도설정
<td colspan="<?=$colspan3?>" class="bwg_help">
	<?php echo bwg_help("첫번째 메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
	<?php echo bpwg_input_color('bwo[menu1_first_bg_color]',$menu1_first_bg_color,$w,1); ?>
</td>
//입력박스
<td class="bwg_help">
	<?php echo bwg_help("제일 상단 타이틀의 작은 문자의 내용을 입력하세요.",1,'#555555','#eeeeee'); ?>
	<input type="text" name="bwo[ttl_small]" class="bp_wdp100" value="<?=$ttl_small?>">
</td>
//숫자범위 100%
<td class="bwg_help">
	<?php echo bwg_help("1차메뉴의 너비(폭)를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
	<?php
	$menu1_wd = (isset($menu1_wd)) ? $menu1_wd : 100;
	echo bpwg_input_range('bwo[menu1_wd]',$menu1_wd,$w,80,400,1,'100%',38,'px');
	?>
</td>
//숫자범위 147px
<td class="bwg_help">
	<?php echo bwg_help("1차메뉴의  높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
	<?php
	$menu1_ht = (isset($menu1_ht)) ? $menu1_ht : 40;
	echo bpwg_input_range('bwo[menu1_ht]',$menu1_ht,$w,20,200,1,'147',38,'px');
	?>
</td>
//선택박스
<td>
	<?php
	$txt2_ani_disabled = 0;//($w == 'u') ? 1 : 0;
	?>
	<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_text_animation_type'], 'bwo[text2_ani_type]', $text2_ani_type, 1, 0,$txt2_ani_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
</td>
*/
$img_th_bg_color = '#d9faea';//로그인관련
$svg_th_bg_color = '#fbe3fa';//연한핑크배경색
$nav1_th_bg_color = '#FFEAB5';//로그인관련
$nav2_th_bg_color = '#C8FDC5';//로그인관련
$nav3_th_bg_color = '#D5CCEC';//로그인관련

$colspan12=12;
$colspan11=11;
$colspan10=10;
$colspan9=9;
$colspan8=8;
$colspan7=7;
$colspan6=6;
$colspan5=5;
$colspan4=4;
$colspan3=3;
$colspan2=2;
?>
<link rel="stylesheet" href="<?=$bwg_skin_set_url?>/bpwg_form_style.css">
<div id="bwg_skin_set">
<h2 class="h2_frm">옵션설정</h2>
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
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">&lt;!-- Generator: Adobe Illustrator 22.0.1, SVG Export Plug-In . SVG Version: 6.00 Build 0)  --&gt;</span><br><br>

<table class="tbl_frm" id="bwg_skin_opt">
	<colgroup>
		<col span="1" width="70">
		<col span="1" width="110">
		<col span="1" width="70">
		<col span="1" width="110">
		<col span="1" width="70">
		<col span="1" width="110">
		<col span="1" width="70">
		<col span="1" width="110">
		<col span="1" width="70">
		<col span="1" width="110">
		<col span="1" width="70">
		<col span="1" width="110">
	</colgroup>
	<tbody>
		<tr>
			<th>헤더<br>배경색상</th>
			<td colspan="<?=$colspan5?>" class="bwg_help">
				<?php echo bwg_help("헤더의 기본 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[head_bg_color]',$head_bg_color,$w,1); ?>
			</td>
			<th>헤더 하단<br>라인색</th>
			<td colspan="<?=$colspan5?>" class="bwg_help">
				<?php echo bwg_help("헤더하단 라인의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[head_line_color]',$head_line_color,$w,1); ?>
			</td>
		</tr>
		<tr>
			<th>헤더높이</th>
			<td class="bwg_help">
				<?php echo bwg_help("헤더 높이(px)를 설정해 주세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$head_height = (isset($head_height)) ? $head_height : 60;
				echo bpwg_input_range('bwo[head_height]',$head_height,$w,40,80,1,'100%',38,'px');
				?>
			</td>
			<th>메뉴열기<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("헤더 메뉴열기 아이콘(3선메뉴)의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu_icon_color]',$menu_icon_color,$w,0); ?>
			</td>
			<th>메뉴열기<br>아이콘크기</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("헤더 메뉴열기 아이콘(3선메뉴) 가로세로(같은비율) 사이즈(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$menu_icon_size = (isset($menu_icon_size)) ? $menu_icon_size : 30;
				echo bpwg_input_range('bwo[menu_icon_size]',$menu_icon_size,$w,10,50,1,'100%',38,'px');
				?>
			</td>
			<th>메뉴열기<br>아이콘정렬</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("메뉴열기 아이콘 버튼의 좌우 정렬을 선택하세요.",1,'#555555','#eeeeee');
				$menu_icon_align_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_horizontal_align2'], 'bwo[menu_icon_align]', $menu_icon_align, 0, 0,$menu_icon_align_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th>메뉴열기<br>아이콘X값</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("메뉴열기 아이콘의 X위치 값(px)을 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$menu_icon_x = (isset($menu_icon_x)) ? $menu_icon_x : 20;
				echo bpwg_input_range('bwo[menu_icon_x]',$menu_icon_x,$w,0,40,1,'100',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>검색사용<br>여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("검색사용 여부를 선택하세요.",1,'#555555','#eeeeee');
				$sch_use_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_yes_no'], 'bwo[sch_use]', $sch_use, 0, 0,$sch_use_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th>검색창열기<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("헤더 검색창열기 아이콘(돋보기 모양 아이콘)의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_open_icon_color]',$sch_open_icon_color,$w,0); ?>
			</td>
			<th>검색창열기<br>아이콘크기</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("헤더 검색창열기 아이콘(돋보기 모양 아이콘)의 가로세로(같은비율) 사이즈(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$sch_open_icon_size = (isset($sch_open_icon_size)) ? $sch_open_icon_size : 30;
				echo bpwg_input_range('bwo[sch_open_icon_size]',$sch_open_icon_size,$w,10,50,1,'100%',38,'px');
				?>
			</td>
			<th>검색창열기<br>아이콘정렬</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("검색창 열기 아이콘 버튼의 좌우 정렬을 선택하세요.",1,'#555555','#eeeeee');
				$sch_open_icon_align_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_horizontal_align2'], 'bwo[sch_open_icon_align]', $sch_open_icon_align, 0, 0,$sch_open_icon_align_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th>검색창열기<br>아이콘X값</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("메뉴열기 아이콘의 X위치 값(px)을 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$sch_open_icon_x = (isset($sch_open_icon_x)) ? $sch_open_icon_x : 20;
				echo bpwg_input_range('bwo[sch_open_icon_x]',$sch_open_icon_x,$w,0,40,1,'100',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>검색버튼<br>아이콘정렬</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("검색버튼 아이콘의 좌우 정렬을 선택하세요.",1,'#555555','#eeeeee');
				$sch_icon_align_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_horizontal_align2'], 'bwo[sch_icon_align]', $sch_icon_align, 0, 0,$sch_icon_align_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th>검색유형</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("검색유형(상품검색,일반검색)을 선택하세요.",1,'#555555','#eeeeee');
				$sch_shop_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_sch_shop'], 'bwo[sch_shop]', $sch_shop, 0, 0,$sch_shop_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th>검색버튼<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색 버튼 아이콘의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_icon_color]',$sch_icon_color,$w,0); ?>
			</td>
			<th>검색버튼<br>아이콘크기</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("헤더 검색버튼 아이콘(돋보기 모양 아이콘)의 가로세로(같은비율) 사이즈(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$sch_icon_size = (isset($sch_icon_size)) ? $sch_icon_size : 30;
				echo bpwg_input_range('bwo[sch_icon_size]',$sch_icon_size,$w,10,50,1,'100%',38,'px');
				?>
			</td>
			<th>검색입력란<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색 입력란의 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_bg_color]',$sch_bg_color,$w,0); ?>
			</td>
			<th>검색입력란<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색 입력란의 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_line_color]',$sch_line_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>검색입력란<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색 입력란의 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_font_color]',$sch_font_color,$w,0); ?>
			</td>
			<th>검색입력란<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색 입력란의 폰트 크기(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$sch_font_size = (isset($sch_font_size)) ? $sch_font_size : 20;
				echo bpwg_input_range('bwo[sch_font_size]',$sch_font_size,$w,15,30,1,'100',38,'px');
				?>
			</td>
			<th>검색닫기<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("검색닫기 버튼 아이콘의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[sch_close_icon_color]',$sch_close_icon_color,$w,0); ?>
			</td>
			<th>검색닫기<br>아이콘크기</th>
			<td colspan="<?=$colspan5?>" class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("검색닫기 버튼 아이콘(돋보기 모양 아이콘)의 가로세로(같은비율) 사이즈(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$sch_close_icon_size = (isset($sch_close_icon_size)) ? $sch_close_icon_size : 30;
				echo bpwg_input_range('bwo[sch_close_icon_size]',$sch_close_icon_size,$w,10,50,1,'100',38,'px');
				?>
			</td>
		</tr>
		<?php if($file_upload_flag){ ?>
		<tr>
			<th style="background:<?=$img_th_bg_color?>;">로고이미지<br>너비</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("로고의 가로 크기(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php
				$logo_width = (isset($logo_width)) ? $logo_width : 40;
				echo bpwg_input_range('bwo[logo_width]',$logo_width,$w,20,180,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$img_th_bg_color?>;">로고이미지<br>높이</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("로고의 세로 크기(px)를 설정해 주세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$logo_height = (isset($logo_height)) ? $logo_height : 40;
				echo bpwg_input_range('bwo[logo_height]',$logo_height,$w,20,150,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$img_th_bg_color?>;">로고URL</th>
			<td colspan="<?=$colspan5?>" class="bwg_help">
				<?php echo bwg_help("로고URL의 입력값이 없으면 자동으로 기본URL값으로 설정이 됩니다.",1,'#555555','#eeeeee'); ?>
				<input type="text" name="bwo[logo_url]" class="bp_wdp100" value="<?=$logo_url?>">
			</td>
			<th style="background:<?=$img_th_bg_color?>;">로고URL<br>새창여부</th>
			<td class="bwg_help">
				<?php 
				echo bwg_help("로고링크를 새창/현재창으로 열지의 여부를 설정하세요.",1,'#555555','#eeeeee');
				$logo_new_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_target'], 'bwo[logo_new]', $logo_new, 0, 0,$logo_new_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$svg_th_bg_color?>;">패스(선)<br>애니사용</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("패스(선)의 애니메이션사용 여부를 설정하세요.",1,'#555555','#eeeeee');
				$path_anim_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_yes_no'], 'bwo[path_anim]', $path_anim, 0, 0,$path_anim_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th style="background:<?=$svg_th_bg_color?>;">채우기<br>애니사용</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("패스채우기 사용여부 여부를 설정하세요.",1,'#555555','#eeeeee');
				$path_fill_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_yes_no'], 'bwo[path_fill]', $path_fill, 0, 0,$path_fill_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) 
				?>
			</td>
			<th style="background:<?=$svg_th_bg_color?>;">패스(선)<br>애니총시간</th>
			<td class="bwg_help">
				<?php echo bwg_help("패스 애니메이션 총시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
				<?php
				$path_time = (isset($path_time)) ? $path_time : 2.0;
				echo bpwg_input_range('bwo[path_time]',$path_time,$w,0,5.0,0.1,'100%',38,'초');
				?>
			</td>
			<th style="background:<?=$svg_th_bg_color?>;">패스(선)별<br>시간차</th>
			<td class="bwg_help">
				<?php echo bwg_help("각 패스별 애니메이션 시간차를 의미합니다.",1,'#555555','#eeeeee'); ?>
				<?php
				$time_diff = (isset($time_diff)) ? $time_diff : 0.3;
				echo bpwg_input_range('bwo[time_diff]',$time_diff,$w,0,5,0.1,'100%',38,'초');
				?>
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
				<?php
				$fill_speed = (isset($fill_speed)) ? $fill_speed : 0.5;
				echo bpwg_input_range('bwo[fill_speed]',$fill_speed,$w,0,5,0.1,'100%',38,'초');
				?>
			</td>
			<th style="background:<?=$svg_th_bg_color?>;">채우기<br>지연시간</th>
			<td class="bwg_help">
				<?php echo bwg_help("채우기 시작하기까지의 지연시간을 의미합니다.",1,'#555555','#eeeeee'); ?>
				<?php
				$fill_delay = (isset($fill_delay)) ? $fill_delay : 4;
				echo bpwg_input_range('bwo[fill_delay]',$fill_delay,$w,0,10,0.1,'100%',38,'초');
				?>
			</td>
			<th style="background:<?=$svg_th_bg_color?>;">기본<br>채우기색상</th>
			<td colspan="<?=$colspan7?>" class="bwg_help">
				<?php echo bwg_help("기본 채우기색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[fill_color]',$fill_color,$w,1); ?>
			</td>
		</tr>
		<tr>
			<td colspan="<?=$colspan12?>">
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
			<td colspan="<?=$colspan9?>" class="bwg_help">
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
			<td colspan="<?=$colspan9?>" class="bwg_help">
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
			<td colspan="<?=$colspan9?>" class="bwg_help">
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
			<td colspan="<?=$colspan9?>" class="bwg_help">
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
			<td colspan="<?=$colspan9?>" class="bwg_help">
				<?php echo bwg_help("클래스5의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[class5_color]',$class5_color,$w,1); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$svg_th_bg_color?>;">로고<br>이미지</th>
			<td colspan="<?=$colspan11?>">
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
		<tr>
			<td colspan="<?=$colspan12?>">
			<br>
			이하의 내용은 메뉴버튼을 클릭했을때 나타나는 슬라이드 판넬 관련 설정입니다.<br>
			[<a href="<?=G5_BPWIDGET_ADMIN_ADM_URL?>/menu_list.php" target="_blank" style="color:blue;">메뉴추가/삭제</a>]
			</td>
		</tr>
		<tr>
			<th>메뉴판넬<br>너비</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("메뉴판넬의 너비(px)를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<input type="text" name="bwo[panel_width]" class="bp_wdx50" value="<?=$panel_width?>" style="text-align:right;">
				<?php 
				$panel_width_unit_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_unit'], 'bwo[panel_width_unit]', $panel_width_unit, 0, 0,$panel_width_unit_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1))
				?>
			</td>
			<th>메뉴판넬<br>정렬위치</th>
			<td class="bwg_help opt_td_logo_size">
				<?php echo bwg_help("메뉴판넬의 정렬위치를 설정해 주세요.</strong>",1,'#555555','#eeeeee'); ?>
				<?php 
				$panel_align_disabled = 0;//($w == 'u') ? 1 : 0;
				echo bwgf_select_selected($g5['bpwidget']['bwgf_horizontal_align2'], 'bwo[panel_align]', $panel_align, 0, 0,$panel_align_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1))
				?>
			</td>
			<th>메뉴판넬<br>기본배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 기본배경의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_basic_bg_color]',$panel_basic_bg_color,$w,0); ?>
			</td>
			<th>판넬닫기<br>블라인드색</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("판넬닫기버튼 배경의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_close_blind_color]',$panel_close_blind_color,$w,1); ?>
			</td>
			<th>판넬기본<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬영역의 기본 테두리 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_basic_line_color]',$panel_basic_line_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>판넬탑영역<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬닫기버튼 배경의 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_top_bg_color]',$panel_top_bg_color,$w,0); ?>
			</td>
			<th>판넬탑영역<br>높이</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬탑 영역의 높이을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$panel_top_height = (isset($panel_top_height)) ? $panel_top_height : 60;
				echo bpwg_input_range('bwo[panel_top_height]',$panel_top_height,$w,40,80,1,'100%',38,'px');
				?>
			</td>
			<th>판넬탑영역<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬탑 영역의 폰트(& 꺽쇠아이콘) 크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$panel_top_font_size = (isset($panel_top_font_size)) ? $panel_top_font_size : 20;
				echo bpwg_input_range('bwo[panel_top_font_size]',$panel_top_font_size,$w,10,30,1,'100%',38,'px');
				?>
			</td>
			<th>판넬탑영역<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬탑영역의 폰트(& 꺽쇠아이콘) 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_top_font_color]',$panel_top_font_color,$w,0); ?>
			</td>
			<th>판넬닫기<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬탑영역의 아이콘(닫기(X)아이콘)색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_top_icon_color]',$panel_top_icon_color,$w,0); ?>
			</td>
			<th>판넬닫기<br>아이콘크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬탑 영역의 아이콘크기(가로/세로 동일)를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$panel_top_icon_size = (isset($panel_top_icon_size)) ? $panel_top_icon_size : 25;
				echo bpwg_input_range('bwo[panel_top_icon_size]',$panel_top_icon_size,$w,15,30,1,'100',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>그리드<br>아이콘크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드 아이콘 영역의 아이콘크기(가로/세로 동일)를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$panel_grid_icon_size = (isset($panel_grid_icon_size)) ? $panel_grid_icon_size : 25;
				echo bpwg_input_range('bwo[panel_grid_icon_size]',$panel_grid_icon_size,$w,10,40,1,'100',38,'px');
				?>
			</td>
			<th>그리드<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드영역의 아이콘색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_grid_icon_color]',$panel_grid_icon_color,$w,0); ?>
			</td>
			<th>그리드<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드영역의 폰트크기를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				$panel_grid_font_size = (isset($panel_grid_font_size)) ? $panel_grid_font_size : 20;
				echo bpwg_input_range('bwo[panel_grid_font_size]',$panel_grid_font_size,$w,10,30,1,'100%',38,'px');
				?>
			</td>
			<th>그리드<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드영역의 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_grid_font_color]',$panel_grid_font_color,$w,0); ?>
			</td>
			<th>그리드<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드영역의 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_grid_line_color]',$panel_grid_line_color,$w,0); ?>
			</td>
			<th>그리드<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 그리드영역의 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[panel_grid_bg_color]',$panel_grid_bg_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>높이</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의  높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu1_ht = (isset($menu1_ht)) ? $menu1_ht : 40;
				echo bpwg_input_range('bwo[menu1_ht]',$menu1_ht,$w,20,50,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu1_font_size = (isset($menu1_font_size)) ? $menu1_font_size : 14;
				echo bpwg_input_range('bwo[menu1_font_size]',$menu1_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>폰트두께</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu1_font_weight = (isset($menu1_font_weight)) ? $menu1_font_weight : 600;
				echo bpwg_input_range('bwo[menu1_font_weight]',$menu1_font_weight,$w,100,900,100,'100%',30);
				?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu1_font_color]',$menu1_font_color,$w); ?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu1_bg_color]',$menu1_bg_color,$w,0); ?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>아이콘크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 드롭다운 아이콘 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu1_icon_size = (isset($menu1_icon_size)) ? $menu1_icon_size : 16;
				echo bpwg_input_range('bwo[menu1_icon_size]',$menu1_icon_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴의 드롭다운 아이콘 색상을 설정하세요.(이미지일경우 색상은 반영 안됩니다.)",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu1_icon_color]',$menu1_icon_color,$w); ?>
			</td>
			<th style="background:<?=$nav1_th_bg_color?>;">1차메뉴<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("1차메뉴간 구분 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu1_line_color]',$menu1_line_color,$w,0); ?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>높이</th>
			<td class="bwg_help">
				<?php 
				echo bwg_help("2차메뉴의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee');
				$menu2_ht = (isset($menu2_ht)) ? $menu2_ht : 40;
				echo bpwg_input_range('bwo[menu2_ht]',$menu2_ht,$w,20,200,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>폰트크기</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("2차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee');
				$menu2_font_size = (isset($menu2_font_size)) ? $menu2_font_size : 12;
				echo bpwg_input_range('bwo[menu2_font_size]',$menu2_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>폰트두께</th>
			<td class="bwg_help">
				<?php 
				echo bwg_help("2차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee');
				$menu2_font_weight = (isset($menu2_font_weight)) ? $menu2_font_weight : 400;
				echo bpwg_input_range('bwo[menu2_font_weight]',$menu2_font_weight,$w,100,900,100,'100%',30);
				?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>들여쓰기</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("2차메뉴의 왼쪽에서의 들여쓰기 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee');
				$menu2_indent = (isset($menu2_indent)) ? $menu2_indent : 15;
				echo bpwg_input_range('bwo[menu2_indent]',$menu2_indent,$w,0,50,1,'100%',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("2차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu2_font_color]',$menu2_font_color,$w); ?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("2차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu2_bg_color]',$menu2_bg_color,$w,0); ?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>아이콘크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("2차메뉴의 아이콘 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu2_icon_size = (isset($menu2_icon_size)) ? $menu2_icon_size : 12;
				echo bpwg_input_range('bwo[menu2_icon_size]',$menu2_icon_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("2차메뉴의 아이콘 색상을 설정하세요.(이미지일경우 색상은 반영 안됩니다.)",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu2_icon_color]',$menu2_icon_color,$w); ?>
			</td>
			<th style="background:<?=$nav2_th_bg_color?>;">2차메뉴<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("2차메뉴간 구분 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu2_line_color]',$menu2_line_color,$w,0); ?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>높이</th>
			<td class="bwg_help">
				<?php 
				echo bwg_help("3차메뉴의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee');
				$menu3_ht = (isset($menu3_ht)) ? $menu3_ht : 40;
				echo bpwg_input_range('bwo[menu3_ht]',$menu3_ht,$w,20,200,1,'100%',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu3_font_size = (isset($menu3_font_size)) ? $menu3_font_size : 12;
				echo bpwg_input_range('bwo[menu3_font_size]',$menu3_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>폰트두께</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu3_font_weight = (isset($menu3_font_weight)) ? $menu3_font_weight : 400;
				echo bpwg_input_range('bwo[menu3_font_weight]',$menu3_font_weight,$w,100,900,100,'100%',30);
				?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu3_font_color]',$menu3_font_color,$w); ?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴의 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu3_bg_color]',$menu3_bg_color,$w,0); ?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>들여쓰기</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴의 왼쪽에서의 들여쓰기 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$menu3_indent = (isset($menu3_indent)) ? $menu3_indent : 30;
				echo bpwg_input_range('bwo[menu3_indent]',$menu3_indent,$w,0,60,1,'100',38,'px');
				?>
			</td>
			<th style="background:<?=$nav3_th_bg_color?>;">3차메뉴<br>라인색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("3차메뉴간 구분 라인색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[menu3_line_color]',$menu3_line_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>푸터정보<br>상단간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 푸터정보 상단간격 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$ft_top_interval = (isset($ft_top_interval)) ? $ft_top_interval : 30;
				echo bpwg_input_range('bwo[ft_top_interval]',$ft_top_interval,$w,10,100,1,'100%',38,'px');
				?>
			</td>
			<th>푸터정보<br>하단간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 푸터정보 하단간격 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$ft_bottom_interval = (isset($ft_bottom_interval)) ? $ft_bottom_interval : 30;
				echo bpwg_input_range('bwo[ft_bottom_interval]',$ft_bottom_interval,$w,10,100,1,'100%',38,'px');
				?>
			</td>
			<th>푸터정보<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 푸터정보의 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[ft_font_color]',$ft_font_color,$w); ?>
			</td>
			<th>푸터정보<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("판넬 푸터정보 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$ft_font_size = (isset($ft_font_size)) ? $ft_font_size : 12;
				echo bpwg_input_range('bwo[ft_font_size]',$ft_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th>푸터정보<br>문장줄간격</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("판넬 푸터정보 문장 줄간격 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$ft_line_interval = (isset($ft_line_interval)) ? $ft_line_interval : 5;
				echo bpwg_input_range('bwo[ft_line_interval]',$ft_line_interval,$w,1,20,1,'100',38,'px');
				?>
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