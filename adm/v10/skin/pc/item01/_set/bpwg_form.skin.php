<?php
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//adm/ajax/bpwidget_call_skin_config.php
//아작스로 이 페이지가 호출될때는 add_stylesheet() OR add_javascript()함수가 반영되지 않는다.
//add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_form_style.css">', 1);
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

$casql = " SELECT ca_id,ca_name FROM {$g5['g5_shop_category_table']} WHERE ca_use = '1' ORDER BY ca_id ASC ";
$cresult = sql_query($casql);

$ca_select = '';
if($cresult->num_rows){
	$ca_select .= '<select name="bwo[ca_id]" id="select_ca_id" class="required" required value="'.$ca_id.'" style="margin-left:5px;">';
	for($i=0;$row=sql_fetch_array($cresult);$i++){
		if(strlen($row['ca_id']) == 2) $row['ca_indent'] = '';
		else if(strlen($row['ca_id']) == 4) $row['ca_indent'] = '--';
		else if(strlen($row['ca_id']) == 6) $row['ca_indent'] = '----';
		else if(strlen($row['ca_id']) == 8) $row['ca_indent'] = '------';
		else $row['ca_indent'] = '--------';
		
		$selected = ($row['ca_id'] == $ca_id) ? 'selected' : '';
		$ca_select .= '<option value="'.$row['ca_id'].'" '.$selected.'>'.$row['ca_indent'].$row['ca_name'].'</option>';
	}
	$ca_select .= '</select>';
}

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
<!--p>
<strong style="color:red;">핑크영역</strong>은 <strong style="color:blue;">텍스트SVG 패스(선) 애니메이션</strong>을 위한 설정내용입니다.<br>
일반 이미지는 SVG관련설정이 반영되지 않습니다.<br>
<strong><span style="color:red;">주의 : </span><span>SVG제작시 사용하는 폰트(서체)는 반드시 이미지로 변환(서체 깨트리기:Create Outline)해서 저장해 주세요.</span></strong><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(폰트(서체) 그대로 저장하시면 애니메이션 효과를 줄 수 없을 뿐만 아니라, 원하는 서체로 표시 되지 않을 수 있습니다.<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;반드시 폰트(서체)를 테두리(선)와 면으로 변환해 주세요.)
</p><br-->
<table class="tbl_frm" id="bwg_skin_opt">
	<colgroup>
		<col span="1" width="70"><!--1-->
		<col span="1" width="110">
		<col span="1" width="70"><!--2-->
		<col span="1" width="110">
		<col span="1" width="70"><!--3-->
		<col span="1" width="110">
		<col span="1" width="70"><!--4-->
		<col span="1" width="110">
		<col span="1" width="70"><!--5-->
		<col span="1" width="110">
		<col span="1" width="70"><!--6-->
		<col span="1" width="110">
	</colgroup>
	<tbody>
		<tr>
			<th>상품노출<br>유형</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("노출하고자 하는 상품 유형을 선택하세요.('분류'를 선택했을때는 옆에 나타나는 분류목록도 선택하셔야 합니다.)",1,'#555555','#eeeeee'); ?>
				<?php
				$item_show_type_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_item_show_type'], 'bwo[item_show_type]', $item_show_type, 0, 1,$item_show_type_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
				
				<script>
				var ca_id = <?=(($ca_id) ? $ca_id : 0)?>;
				var item_show_type = <?=(($item_show_type) ? $item_show_type : 0)?>;
				var ca_select = '<?=$ca_select?>';
				if(item_show_type == 6) $(ca_select).insertAfter('select[name="bwo[item_show_type]"]');
				
				$('select[name="bwo[item_show_type]"]').on('change',function(){
					if($(this).val() == 6) ca_insert();
					else ca_remove();
				});
				
				function ca_insert(){
					$(ca_select).insertAfter('select[name="bwo[item_show_type]"]');
				}
				function ca_remove(){
					if($('#select_ca_id').length > 0) $('#select_ca_id').remove();
				}
				</script>
			</td>
			<th>상품노출<br>스킨선택</th>
			<td colspan="<?=$colspan2?>" class="bwg_help td_opt_skin">
				<?php echo bwg_help("상품노출 스킨을 선택하세요.",1,'#555555','#eeeeee'); ?>
				<?php
				echo bpwg_get_file_select($bwg_skin_path, 'skin', 'bwo_item_skin', 'bwo[item_skin]', $item_skin, 'required');
				?>
			</td>
			<th>1줄당 상품<br>노출갯수</th>
			<td class="bwg_help">
				<?php echo bwg_help("1줄당 상품노출 갯수를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$item_mod_cnt = (isset($item_mod_cnt)) ? $item_mod_cnt : 3;
				echo bpwg_input_range('bwo[item_mod_cnt]',$item_mod_cnt,$w,1,40,1,'100%',32,'개');
				?>
			</td>
			<th>목록출력<br>줄 수</th>
			<td class="bwg_help">
				<?php echo bwg_help("출력할 상품 줄 수를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$item_row_cnt = (isset($item_row_cnt)) ? $item_row_cnt : 2;
				echo bpwg_input_range('bwo[item_row_cnt]',$item_row_cnt,$w,1,10,1,'100%',32,'줄');
				?>
			</td>
			<th>기본배경<br>색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("기본 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[basic_bg_color]',$basic_bg_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>기본너비(폭)</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("기본너비(폭)를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$bsic_width_size = (isset($bsic_width_size)) ? $bsic_width_size : 1080;
				echo bpwg_input_range('bwo[bsic_width_size]',$bsic_width_size,$w,900,1300,10,'147',45,'px');
				?>
			</td>
			<th>섬네일<br>가로너비</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("섬네일의 가로너비를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$thumb_width_size = (isset($thumb_width_size)) ? $thumb_width_size : 320;
				echo bpwg_input_range('bwo[thumb_width_size]',$thumb_width_size,$w,300,400,2,'147',38,'px');
				?>
			</td>
			<th>섬네일<br>세로높이</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("섬네일의 세로높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$thumb_height_size = (isset($thumb_height_size)) ? $thumb_height_size : 250;
				echo bpwg_input_range('bwo[thumb_height_size]',$thumb_height_size,$w,200,400,2,'147',38,'px');
				?>
			</td>
			<th>타이틀</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("타이틀입력값이 없으면 [상품노출유형] 데이터가 타이틀로 노출됩니다.",1,'#555555','#eeeeee'); ?>
				<input type="text" name="bwo[item_title]" class="bp_wdx170" value="<?=$item_title?>">
			</td>
		</tr>
		<tr>
			<th>타이틀<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("타이틀 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[title_font_color]',$title_font_color,$w,0); ?>
			</td>
			<th class="sthd_bg1">타이틀<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("타이틀의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_font_size = (isset($title_font_size)) ? $title_font_size : 14;
				echo bpwg_input_range('bwo[title_font_size]',$title_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">타이틀<br>폰트두께</th>
			<td class="bwg_help">
				<?php echo bwg_help("타이틀의 폰트 두께를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_font_weight = (isset($title_font_weight)) ? $title_font_weight : 400;
				echo bpwg_input_range('bwo[title_font_weight]',$title_font_weight,$w,100,700,100,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">타이틀<br>상단간격</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("타이틀 상단 간격을 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_top_interval = (isset($title_top_interval)) ? $title_top_interval : 100;
				echo bpwg_input_range('bwo[title_top_interval]',$title_top_interval,$w,5,200,5,'147',38,'px');
				?>
			</td>
			<th class="sthd_bg1">타이틀<br>하단간격</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("타이틀 하단 간격을 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_bottom_interval = (isset($title_bottom_interval)) ? $title_bottom_interval : 50;
				echo bpwg_input_range('bwo[title_bottom_interval]',$title_bottom_interval,$w,5,150,5,'147',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>블라인드<br>아이콘색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("블라인드 아이콘색상을 설정하세요.(grid,default_grid스킨은 블라인드 적용이 안됩니다.)",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[blind_icon_color]',$blind_icon_color,$w,0); ?>
			</td>
			<th>블라인드<br>색상투명도</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("블라인드의 색상/투명도를 설정하세요.(grid,default_grid스킨은 블라인드 적용이 안됩니다.)",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[blind_color]',$blind_color,$w,1); ?>
			</td>
			<th>정보영역<br>높이</th>
			<td colspan="<?=$colspan5?>" class="bwg_help">
				<?php echo bwg_help("정보(텍스트)영역의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$info_height_size = (isset($info_height_size)) ? $info_height_size : 120;
				echo bpwg_input_range('bwo[info_height_size]',$info_height_size,$w,50,250,2,'147',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>상품ID<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("상품ID 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$id_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[id_show]', $id_show, 0, 1,$id_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>상품명<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("상품명 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$name_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[name_show]', $name_show, 0, 1,$name_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>상품설명<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("상품설명 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$basic_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[basic_show]', $basic_show, 0, 1,$basic_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>시중가격<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("시중가격 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$custprice_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[custprice_show]', $custprice_show, 0, 1,$custprice_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>판매가격<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("판매가격 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$price_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[price_show]', $price_show, 0, 1,$price_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>유형아이콘<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("상품유형아이콘 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$icon_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[icon_show]', $icon_show, 0, 1,$icon_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
		</tr>
		<tr>
			<th>상품ID<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품ID 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[id_font_color]',$id_font_color,$w,0); ?>
			</td>
			<th>상품명<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품명 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[name_font_color]',$name_font_color,$w,0); ?>
			</td>
			<th>상품설명<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품설명 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[basic_font_color]',$basic_font_color,$w,0); ?>
			</td>
			<th>시중가격<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("시중가격 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[custprice_font_color]',$custprice_font_color,$w,0); ?>
			</td>
			<th>판매가격<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("판매가격 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[price_font_color]',$price_font_color,$w,0); ?>
			</td>
			<th>SNS<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("SNS아이콘 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$sns_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[sns_show]', $sns_show, 0, 1,$sns_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
		</tr>
		<tr>
			<th class="sthd_bg1">상품ID<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품ID의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$id_font_size = (isset($id_font_size)) ? $id_font_size : 14;
				echo bpwg_input_range('bwo[id_font_size]',$id_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">상품명<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품명의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$name_font_size = (isset($name_font_size)) ? $name_font_size : 14;
				echo bpwg_input_range('bwo[name_font_size]',$name_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">상품설명<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("상품설명의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$basic_font_size = (isset($basic_font_size)) ? $basic_font_size : 14;
				echo bpwg_input_range('bwo[basic_font_size]',$basic_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">시중가격<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("시중가격의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$custprice_font_size = (isset($custprice_font_size)) ? $custprice_font_size : 14;
				echo bpwg_input_range('bwo[custprice_font_size]',$custprice_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">판매가격<br>폰트크기</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("판매가격의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$price_font_size = (isset($price_font_size)) ? $price_font_size : 14;
				echo bpwg_input_range('bwo[price_font_size]',$price_font_size,$w,8,30,1,'104',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>목록버튼<br>표시여부</th>
			<td class="bwg_help">
				<?php
				echo bwg_help("목록페이지로 이동하는 버튼의 표시여부를 설정하세요.",1,'#555555','#eeeeee');
				$listlink_show_disabled = 0;//($w == 'u') ? 1 : 0;
				?>
				<?php echo bwgf_select_selected($g5['bpwidget']['bwgf_show_hide'], 'bwo[listlink_show]', $listlink_show, 0, 1,$listlink_show_disabled);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','bwo[text2_ani_type](name속성)','ok(값)',0(값없음활성화=1),1(필수여부=1),1(비활성화=1)) ?>
			</td>
			<th>목록버튼<br>배경색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록페이지로 이동버튼 배경색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[listlink_bg_color]',$listlink_bg_color,$w,0); ?>
			</td>
			<th>목록버튼<br>폰트색상</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록페이지로 이동버튼 폰트색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[listlink_font_color]',$listlink_font_color,$w,0); ?>
			</td>
			<th>목록버튼<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록버튼의 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$listlink_font_size = (isset($listlink_font_size)) ? $listlink_font_size : 14;
				echo bpwg_input_range('bwo[listlink_font_size]',$listlink_font_size,$w,8,30,1,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">목록버튼<br>가로너비</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록버튼의 가로너비를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$listlink_width_size = (isset($listlink_width_size)) ? $listlink_width_size : 320;
				echo bpwg_input_range('bwo[listlink_width_size]',$listlink_width_size,$w,100,400,10,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">목록버튼<br>세로높이</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록버튼의 세로높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$listlink_height_size = (isset($listlink_height_size)) ? $listlink_height_size : 60;
				echo bpwg_input_range('bwo[listlink_height_size]',$listlink_height_size,$w,20,100,5,'100%',38,'px');
				?>
			</td>
		</tr>
		<tr>
			<th class="sthd_bg1">목록버튼<br>상단간격</th>
			<td class="bwg_help">
				<?php echo bwg_help("목록버튼의 상단간격을 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$listlink_top_interval = (isset($listlink_top_interval)) ? $listlink_top_interval : 70;
				echo bpwg_input_range('bwo[listlink_top_interval]',$listlink_top_interval,$w,5,100,5,'100%',38,'px');
				?>
			</td>
			<th class="sthd_bg1">목록버튼<br>하단간격</th>
			<td colspan="<?=$colspan9?>" class="bwg_help">
				<?php echo bwg_help("목록버튼의 하단간격을 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$listlink_bottom_interval = (isset($listlink_bottom_interval)) ? $listlink_bottom_interval : 20;
				echo bpwg_input_range('bwo[listlink_bottom_interval]',$listlink_bottom_interval,$w,5,150,5,'100',38,'px');
				?>
			</td>
		</tr>
		<?php if($file_upload_flag){ ?>
		<tr>
			<th class="">이미지</th>
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
				echo '<p style="color:blue;font-weight:700;">SVG이미지는 단색표시 또는 단색 애니메이션 용도로만 사용할 수 있습니다.</p>'.PHP_EOL;
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
	/*
	var format_url = /^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i;
	var format_tel_sms = /^(tel|sms)\:[0-9]{2,3}\-[0-9]{3,4}\-[0-9]{4}/gm;
	var format_email = /^(mailto)\:[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
	var format_inner = /^(\#)[0-9a-zA-Z\_\-]*$/i;
	
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