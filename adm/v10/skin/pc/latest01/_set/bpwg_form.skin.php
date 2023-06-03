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
$lst_add_no = ($lst_add == '' || $lst_add == 'no') ? 'checked="checked"' : '';
$lst_add_id = ($lst_add == 'id') ? 'checked="checked"' : '';
$lst_add_date = ($lst_add == 'date') ? 'checked="checked"' : '';

$bsql = " SELECT bo_table,bo_subject FROM {$g5['board_table']} ORDER BY bo_order ";
$bresult = sql_query($bsql,1);
$bo_lst = array(); //G5_TABLE_PREFIX, G5_SHOP_TABLE_PREFIX
if($bresult->num_rows){
	$bo_select_list = '<div id="dmo_tbl"><div id="dmo_td"><div id="dmo_bg"></div><div id="dmo_con"><img id="dmo_close" src="'.G5_BPWIDGET_IMG_URL.'/close.png"><h3>자료선택</h3><ul id="dmo_ul">'.PHP_EOL;
	$bo_select_list .= '<li class="dmo_li dmo_li_no" title="" subject="">선택안함<br><span>No Select</span></li>'.PHP_EOL;
	for($i=0;$brow=sql_fetch_array($bresult);$i++){
		$bo_select_list .= '<li class="dmo_li" title="'.$brow['bo_table'].'" subject="'.$brow['bo_subject'].'">'.$brow['bo_subject'].'<br><span>'.$brow['bo_table'].'</span></li>'.PHP_EOL;
	}
	//$iu = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['g5_shop_item_use_table']} WHERE is_confirm = '1' ");
	//if($iu['cnt'])
	$bo_select_list .= '<li class="dmo_li dmo_li_use" title="item_use" subject="상품후기">상품후기<br><span>item_use</span></li>'.PHP_EOL;
	$bo_select_list .= '</ul></div></div></div>'.PHP_EOL;
}

$bo_skin_select = bpwg_get_file_match_select($bwg_skin_path, 'skin', 'bwo_lst_skin', 'bwo[lst_skin]', $lst_skin, 'required', 'board_');
$iu_skin_select = bpwg_get_file_match_select($bwg_skin_path, 'skin', 'bwo_lst_skin', 'bwo[lst_skin]', $lst_skin, 'required', 'itemuse_');


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
			<th>최신글<br>자료선택</th>
			<td colspan="<?=$colspan3?>" class="bwg_help">
				<?php echo bwg_help("제일 상단 타이틀의 작은 문자의 내용을 입력하세요.",1,'#555555','#eeeeee'); ?>
				<input type="hidden" name="bwo[lst_type]" class="lst_type" value="<?=$lst_type?>">
				<input type="hidden" name="bwo[lst_table]" class="lst_table" value="<?=$lst_table?>">
				<input type="text" name="bwo[lst_name]" placeholder="자료선택" readonly required class="lst_name bp_wdx150 required readonly" value="<?=$lst_name?>" style="margin-right:5px;">
				<?php
				if($lst_type == 'board_'){
					echo $bo_skin_select;
				}else if($lst_type == 'itemuse_'){
					echo $iu_skin_select;
				}
				?>
			</td>
			<th>최신글<br>목록갯수</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록갯수를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$lst_cnt = (isset($lst_cnt)) ? $lst_cnt : 4;
				echo bpwg_input_range('bwo[lst_cnt]',$lst_cnt,$w,1,10,1,'100',29,'개');
				?>
			</td>
			<th>최신글<br>첨부표시</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("목록에 첨부표시할 데이터를 설정하세요.",1,'#555555','#eeeeee'); ?>
				<div>
					<label for="bwo_lst_add_no" class="label_radio first_child bwo_lst_add_no">
						<input type="radio" id="bwo_lst_add_no" name="bwo[lst_add]" value="no" <?=$lst_add_no?>>
						<strong></strong>
						<span>없음</span>
					</label>
					<label for="bwo_lst_add_id" class="label_radio bwo_lst_add_id">
						<input type="radio" id="bwo_lst_add_id" name="bwo[lst_add]" value="id" <?=$lst_add_id?>>
						<strong></strong>
						<span>ID</span>
					</label>
					<label for="bwo_lst_add_date" class="label_radio bwo_lst_add_date">
						<input type="radio" id="bwo_lst_add_date" name="bwo[lst_add]" value="date" <?=$lst_add_date?>>
						<strong></strong>
						<span>날짜</span>
					</label>
				</div>
			</td>
			<th>최신글<br>높이</th>
			<td colspan="<?=$colspan2?>" class="bwg_help">
				<?php echo bwg_help("최신글 목록영역의 높이를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$lst_height = (isset($lst_height)) ? $lst_height : 300;
				echo bpwg_input_range('bwo[lst_height]',$lst_height,$w,20,500,5,'100%',40,'px');
				?>
			</td>
		</tr>
		<tr>
			<th>타이틀<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[title_bg_color]',$title_bg_color,$w,0); ?>
			</td>
			<th>타이틀<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[title_font_color]',$title_font_color,$w,0); ?>
			</td>
			<th>타이틀<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_font_size = (isset($title_font_size)) ? $title_font_size : 20;
				echo bpwg_input_range('bwo[title_font_size]',$title_font_size,$w,8,30,1,'100%',32,'px');
				?>
			</td>
			<th>타이틀<br>폰트두께</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀의 폰트 두께 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$title_font_weight = (isset($title_font_weight)) ? $title_font_weight : 500;
				echo bpwg_input_range('bwo[title_font_weight]',$title_font_weight,$w,100,900,100,'100%',30);
				?>
			</td>
			<th>타이틀<br>아이콘색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 아이콘 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[title_icon_color]',$title_icon_color,$w,0); ?>
			</td>
			<th>타이틀<br>라인색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 라인 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[title_line_color]',$title_line_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>목록영역<br>배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록영역 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_bg_color]',$lst_bg_color,$w,0); ?>
			</td>
			<th>목록<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_font_color]',$lst_font_color,$w,0); ?>
			</td>
			<th>목록<br>폰트크기</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 폰트 크기를 설정하세요.(미세조정은 키보드의 방향키로 하세요.)",1,'#555555','#eeeeee'); ?>
				<?php
				$lst_font_size = (isset($lst_font_size)) ? $lst_font_size : 17;
				echo bpwg_input_range('bwo[lst_font_size]',$lst_font_size,$w,8,30,1,'100%',32,'px');
				?>
			</td>
			<th>목록첨부<br>폰트색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록첨부표시 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_add_font_color]',$lst_add_font_color,$w,0); ?>
			</td>
			<th>목록영역<br>라인색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록영역 라인 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_line_color]',$lst_line_color,$w,0); ?>
			</td>
			<th>목록영역<br>아이콘색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 타이틀 아이콘 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_icon_color]',$lst_icon_color,$w,0); ?>
			</td>
		</tr>
		<tr>
			<th>목록영역<br>오버배경색</th>
			<td class="bwg_help">
				<?php echo bwg_help("최신글 목록영역 마우스오버시 배경 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_hover_bg_color]',$lst_hover_bg_color,$w,0); ?>
			</td>
			<th>목록영역<br>오버폰트색</th>
			<td colspan="<?=$colspan9?>" class="bwg_help">
				<?php echo bwg_help("최신글 목록영역 마우스오버시 타이틀 폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
				<?php echo bpwg_input_color('bwo[lst_hover_font_color]',$lst_hover_font_color,$w,0); ?>
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
var bo_skin_select = $('<?=$bo_skin_select?>');
var iu_skin_select = $('<?=$iu_skin_select?>');
$(function(){
	
	//자료선택 모달창 열기
	$('input.lst_name').on('click',function(){
		$('#dmo_tbl').css('display','table');
	});
	
	//자료선택 모달창 닫기
	$('#dmo_bg,#dmo_close').on('click',function(){
		$('#dmo_tbl').css('display','none');
	});
	
	//자료선택시
	$('.dmo_li').on('click',function(){
		if($(this).hasClass('dmo_li_no')){
			$('input.lst_name').siblings('select').remove();
			$('input[name="bwo[lst_type]"]').val('');
			$('input[name="bwo[lst_table]"]').val('');
			$('input[name="bwo[lst_name]"]').val('');
		}else if($(this).hasClass('dmo_li_use')){
			if($(this).attr('title') != $('input.lst_table').val()){
				$('input.lst_name').siblings('select').remove();
				$('input[name="bwo[lst_type]"]').val('itemuse_');
				$('input[name="bwo[lst_table]"]').val($(this).attr('title'));
				$('input[name="bwo[lst_name]"]').val($(this).attr('subject'));
				iu_skin_select.insertAfter('input.lst_name');	
			}
		}else{
			if($(this).attr('title') != $('input.lst_table').val()){
				$('input.lst_name').siblings('select').remove();
				$('input[name="bwo[lst_type]"]').val('board_');
				$('input[name="bwo[lst_table]"]').val($(this).attr('title'));
				$('input[name="bwo[lst_name]"]').val($(this).attr('subject'));
				bo_skin_select.insertAfter('input.lst_name');
			}
		}
		$('#dmo_tbl').css('display','none');
	});
	
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
<?php echo $bo_select_list; ?>