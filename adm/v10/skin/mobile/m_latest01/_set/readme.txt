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