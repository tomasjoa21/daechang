<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){
		$adid = 'ad_'.$bid.bpwg_uniqid();
	?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(
		function(e){$(this).parent().css('border','1px solid red');},
		function(e){$(this).parent().css('border','0');}
	);
	</script>
	<?php } ?>
	<div class="hd_area">
		<a href="javascript:" class="nav_btn"><?php bpwg_icon('3line_menu',$menu_icon_color,$menu_icon_size,$menu_icon_size); ?></a>
		<?php
		//(수정)$lgid변수는 bpwg.skin.php에 정의함
		//$lgid = 'lg_'.$bid.bpwg_uniqid();
		
		$logo_width_data = 'auto';
		$logo_height_data = 'auto';
		if($logo_width != '' && $logo_width != 'auto'){
			$logo_width_data = $logo_width.'px';
		}
		if($logo_height != '' && $logo_height != 'auto'){
			$logo_height_data = $logo_height.'px';
		}
		$logo_path = G5_PATH.$file['afile'][0]['bwga_path'].'/'.$file['afile'][0]['bwga_name'];
		$logo_file = $file['afile'][0]['bwga_name'];
		
		if(is_file($logo_path)){
			$sv_wd = $logo_width_data;
			$sv_ht = $logo_height_data;
			$logo_src = G5_URL.$file['afile'][0]['bwga_path'].'/'.$file['afile'][0]['bwga_name'];
			$logo_link = ($logo_url) ? bwg_g5_url_check($logo_url) : G5_URL;
			//일반이미지
			if(!preg_match("/\.svg/i",$logo_file)){
				echo '<div id="'.$lgid.'" class="bpwg top_logo01" style="width:'.$sv_wd.';height:'.$sv_ht.';">'.PHP_EOL;
				echo '<a href="'.$logo_link.'" target="'.$logo_new.'">'.PHP_EOL;
				echo '<img style="width:'.$sv_wd.';height:'.$sv_ht.';" src="'.$logo_src.'" alt="'.$config['cf_title'].'">'.PHP_EOL;
				echo '</a>'.PHP_EOL;
				echo '</div>'.PHP_EOL;
			}
			//벡터이미지
			else{
				$path_anim_ok = $path_anim;
				$path_fill_ok = $path_fill;
				$path_time = $path_time;
				$time_diff = $time_diff;
				$path_color = $path_color;
				$fill_speed = $fill_speed;
				$fill_delay = $fill_delay;
				$fill_color = $fill_color;
				
				$fillcolor = substr(substr($fill_color,5),0,-1);
				$fillcolor_arr = explode(',',$fillcolor);
				$fillcolor_ini = 'rgba('.trim($fillcolor_arr[0]).','.trim($fillcolor_arr[1]).','.trim($fillcolor_arr[2]).',0)';
				//추가색상관련 루프
				$addcolor_cnt = 20;
				$class_cnt = 0;
				for($i=1;$i<=$addcolor_cnt;$i++){
					if(${'class'.$i} != '' && ${'class'.$i.'_color'} != ''){
						${'class'.$i.'_fill'} = substr(substr(${'class'.$i.'_color'},5),0,-1);
						${'class'.$i.'_fill_arr'} = explode(',',${'class'.$i.'_fill'});
						${'class'.$i.'_fill_ini'} = 'rgba('.trim(${'class'.$i.'_fill_arr'}[0]).','.trim(${'class'.$i.'_fill_arr'}[1]).','.trim(${'class'.$i.'_fill_arr'}[2]).',0)';
						$class_cnt = $i;
					}else{
						break;
					}
				}
				
				echo '<div id="'.$lgid.'" class="bpwg '.$lgid.'" style="width:'.$sv_wd.';height:'.$sv_ht.';">'.PHP_EOL;
				echo '<a href="'.$logo_link.'" target="'.$logo_new.'">'.PHP_EOL;
				include($logo_path);
				echo '</a>'.PHP_EOL;
				echo '</div>'.PHP_EOL;
				if(!($path_anim_ok == 'no' && $path_fill_ok == 'no')){
					echo '<script>'.PHP_EOL;
					echo 'var id_'.$lgid.' = "'.$lgid.'";'.PHP_EOL;
					echo 'var ob_'.$lgid.' = $("#'.$lgid.'");'.PHP_EOL;
					echo 'var sv_'.$lgid.' = $("#'.$lgid.' svg");'.PHP_EOL;
					echo 'const pt_'.$lgid.' = document.querySelectorAll("#'.$lgid.' svg path");'.PHP_EOL;
					echo 'var '.$lgid.'_anim_ok = "'.$path_anim_ok.'";'.PHP_EOL;
					echo 'var '.$lgid.'_fill_ok = "'.$path_fill_ok.'";'.PHP_EOL;
					echo 'var '.$lgid.'_path_time = "'.$path_time.'";'.PHP_EOL;
					echo 'var '.$lgid.'_time_diff = "'.$time_diff.'";'.PHP_EOL;
					echo 'var '.$lgid.'_path_color = "'.$path_color.'";'.PHP_EOL;
					echo 'var '.$lgid.'_fill_speed = "'.$fill_speed.'";'.PHP_EOL;
					echo 'var '.$lgid.'_fill_delay = "'.$fill_delay.'";'.PHP_EOL;
					echo 'var '.$lgid.'_fill_color = "'.$fill_color.'";'.PHP_EOL;
					echo 'var '.$lgid.'_fillcolor_ini = "'.$fillcolor_ini.'";'.PHP_EOL;
					
					echo 'var '.$lgid.'_addarr = new Array();'.PHP_EOL;
					echo 'var '.$lgid.'_class_cnt = '.$class_cnt.';'.PHP_EOL;
					for($i=1;$i<=$class_cnt;$i++){
						echo $lgid.'_addarr["'.${'class'.$i}.'"] = {"path":"'.${'class'.$i.'_color'}.'","fill_ini":"'.${'class'.$i.'_fill_ini'}.'","fill":"'.${'class'.$i.'_color'}.'"};'.PHP_EOL;
					}
					
					echo 'var '.$lgid.'_svgcss = "#'.$lgid.' svg";'.PHP_EOL;
					echo 'var '.$lgid.'_pthcss = "#'.$lgid.' svg path";'.PHP_EOL;
					echo 'var '.$lgid.'_kfr_lineani = "'.$lgid.'_kfr_lineani";'.PHP_EOL;
					echo 'var '.$lgid.'_kfr_fill = "'.$lgid.'_kfr_fill";'.PHP_EOL;
					echo '</script>'.PHP_EOL;
					
					include($bwgs_skin_path.'/bpwg_style.logo.php');
				}
			}
		}
		?>
		<?php if($sch_use == 'yes'){ ?>
			<a href="javascript:" class="sch_btn"><?php bpwg_icon('search',$sch_open_icon_color,$sch_open_icon_size,$sch_open_icon_size); ?></a>
			<?php if($sch_shop == 'yes'){ //쇼핑몰 검색############### ?>
			<fieldset class="sch">
				<legend>쇼핑몰 전체검색</legend>
				<form name="frmsearch1" action="<?php echo G5_SHOP_URL; ?>/search.php" onsubmit="return search_submit(this);">
				<label for="sch_str" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="q" value="<?php echo stripslashes(get_text(get_search_string($q))); ?>" id="sch_str" required placeholder="상품 검색어를 입력해주세요">
				<button type="submit" id="sch_submit" value="검색">
					<?php bpwg_icon('search',$sch_icon_color,$sch_icon_size,$sch_icon_size); ?>
					<span class="sound_only">검색</span>
				</button>
				<a href="javascript:" class="sch_close">
					<?php bpwg_icon('close',$sch_close_icon_color,$sch_close_icon_size,$sch_close_icon_size); ?>
					<span class="sound_only">검색닫기</span>
				</a>
				</form>
				<script>
				function search_submit(f) {
					if (f.q.value.length < 2) {
						alert("검색어는 두글자 이상 입력하십시오.");
						f.q.select();
						f.q.focus();
						return false;
					}
					return true;
				}
				</script>
			</fieldset>
			<?php }else{ //일반 검색##################### ?>
			<fieldset class="sch">
				<legend>사이트 내 전체검색</legend>
				<form name="fsearchbox" method="get" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
				<input type="hidden" name="sfl" value="wr_subject||wr_content">
				<input type="hidden" name="sop" value="and">
				<label for="sch_stx" class="sound_only">검색어 필수</label>
				<input type="text" name="stx" id="sch_stx" maxlength="20" placeholder="검색어를 입력해주세요">
				<button type="submit" id="sch_submit" value="검색">
					<?php bpwg_icon('search',$sch_icon_color,$sch_icon_size,$sch_icon_size); ?>
					<span class="sound_only">검색</span>
				</button>
				<a href="javascript:" class="sch_close">
					<?php bpwg_icon('close',$sch_close_icon_color,$sch_close_icon_size,$sch_close_icon_size); ?>
					<span class="sound_only">검색닫기</span>
				</a>
				</form>

				<script>
				function fsearchbox_submit(f)
				{
					if (f.stx.value.length < 2) {
						alert("검색어는 두글자 이상 입력하십시오.");
						f.stx.select();
						f.stx.focus();
						return false;
					}

					// 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
					var cnt = 0;
					for (var i=0; i<f.stx.value.length; i++) {
						if (f.stx.value.charAt(i) == ' ')
							cnt++;
					}

					if (cnt > 1) {
						alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
						f.stx.select();
						f.stx.focus();
						return false;
					}

					return true;
				}
				</script>

			</fieldset>
			<?php } ?>
			<script>
			$('#<?=$bid?> .sch_btn').on('click',function(){$('#<?=$bid?> .sch').addClass('show');});
			$('#<?=$bid?> .sch_close').on('click',function(){
				$('#<?=$bid?> .sch').removeClass('show');
				if($('#<?=$bid?> #sch_str').length > 0) $('#<?=$bid?> #sch_str').val('');
				if($('#<?=$bid?> #sch_stx').length > 0) $('#<?=$bid?> #sch_stx').val('');
			});
			</script>
		<?php } ?>
	</div>
</div>
<script>
var <?=$bid?> = $('#<?=$bid?>');
<?=$bid?>.width($('body').width());
$(window).resize(function(){
	<?=$bid?>.width($('body').width());
});
</script>