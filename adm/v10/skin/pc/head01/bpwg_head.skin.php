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
	<div class="tlnk">
		<div class="tlnk_in">
			<?php if($sch_use == 'yes'){ ?>
			<ul class="tlnk_ul tlnk_left">
				<li>
					<?php if($sch_shop == 'yes'){ //쇼핑몰 검색############### ?>
					<fieldset class="sch">
						<legend>쇼핑몰 전체검색</legend>
						<form name="frmsearch1" action="<?php echo G5_SHOP_URL; ?>/search.php" onsubmit="return search_submit(this);">
						<label for="sch_str" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
						<input type="text" name="q" value="<?php echo stripslashes(get_text(get_search_string($q))); ?>" id="sch_str" required placeholder="상품 검색어를 입력해주세요">
						<button type="submit" id="sch_submit" value="검색">
							<?php bpwg_icon('search',$schfont_color,18,18); ?>
							<span class="sound_only">검색</span>
						</button>
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
							<?php bpwg_icon('search',$schfont_color,18,18); ?>
							<span class="sound_only">검색</span>
						</button>
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
				</li>
			</ul>
			<?php } ?>
			<ul class="tlnk_ul tlnk_right">
				<li><a href="<?php echo G5_BBS_URL ?>/faq.php">FAQ</a></li>
	            <li><a href="<?php echo G5_BBS_URL ?>/qalist.php">1:1문의</a></li>
	            <li><a href="<?php echo G5_SHOP_URL ?>/personalpay.php">개인결제</a></li>
	            <li><a href="<?php echo G5_SHOP_URL ?>/itemuselist.php">사용후기</a></li> 
	            <li><a href="<?php echo G5_SHOP_URL ?>/itemqalist.php">상품문의</a></li>
				<li class="bd"><a href="<?php echo G5_SHOP_URL; ?>/couponzone.php">쿠폰존</a></li>
	        </ul>
		</div>
	</div>
	<div class="logonav">
		<div class="logonav_in">
			<table>
				<tbody>
					<tr>
						<?php if($logo_show == 'yes'){ ?>
						<td class="td_logo">
							<div class="logo">
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
							</div>
						</td>
						<?php } ?>
						<td class="td_nav">
							<div class="nav">
								<?php if(!count($nlist)){ ?>
								<div class="nav_empty">
									메뉴데이터가 없습니다.<br><a href="<?=G5_BPWIDGET_ADMIN_ADM_URL?>/menu_list.php" target="_blank">[메뉴설정]</a>해 주시기 바랍니다.
								</div>
								<?php }else{ ?>
								<ul class="ul_nav1">
									<?php for($i=0;$i<count($nlist);$i++){ 
									$first_nav1 = ($menu1_first && $i == 0) ? ' first_nav1' : '';
									?>
									<li class="li_nav1<?=$first_nav1?>">
										<a href="<?=$nlist[$i]['me_link']?>" target="_<?=$nlist[$i]['me_target']?>">
											<?=$nlist[$i]['me_name']?>
											<?php if($i != 0 && $menu1_gubun == 'yes'){ ?><line></line><?php } ?>
											<?php
											if($menu1_icon == 'yes' && count($nlist[$i]['me_2'])){
												include(G5_BPWIDGET_SVG_PATH.'/icon_arrow_down.svg');
											}
											?>
										</a>
										<?php if($menu2_sub == 'yes' && count($nlist[$i]['me_2'])){ ?>
										<ul class="ul_nav2">
											<?php for($j=0;$j<count($nlist[$i]['me_2']);$j++){ 
												//$last_nav3 = ($i != 0 && $i == (count($nlist) - 1) && count($nlist[$i]['me_2'][$j]['me_3'])) ? ' last_nav3' : '';
												$last_nav3 = ($i == (count($nlist) - 1) && count($nlist[$i]['me_2'][$j]['me_3'])) ? ' last_nav3' : '';
												$last_li2 = ($i == (count($nlist) - 1) && count($nlist[$i]['me_2'][$j]['me_3'])) ? ' last_li2' : '';
											?>
											<li class="li_nav2<?=$last_li2?>">
												<a href="<?=$nlist[$i]['me_2'][$j]['me_link']?>" target="_<?=$nlist[$i]['me_2'][$j]['me_target']?>">
												<?=$nlist[$i]['me_2'][$j]['me_name']?>
												<?php
												if($i != (count($nlist) - 1) && $menu2_icon == 'yes' && count($nlist[$i]['me_2'][$j]['me_3'])){
													include(G5_BPWIDGET_SVG_PATH.'/icon_arrow_right.svg');
												}else if($i == (count($nlist) - 1) && $menu2_icon == 'yes' && count($nlist[$i]['me_2'][$j]['me_3'])){
													include(G5_BPWIDGET_SVG_PATH.'/icon_arrow_left.svg');
												}
												?>
												</a>
												<?php if(count($nlist[$i]['me_2'][$j]['me_3'])){ ?>
												<ul class="ul_nav3<?=$last_nav3?>">
													<?php for($k=0;$k<count($nlist[$i]['me_2'][$j]['me_3']);$k++){ ?>
													<li class="li_nav3">
														<a href="<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_link']?>" target="_<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_target']?>">
														<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_name']?>
														</a>
													</li>
													<?php } ?>
												</ul>
												<?php } ?>
											</li>
											<?php } ?>
										</ul>
										<?php } ?>
									</li>
									<?php } ?>
								</ul>
								<?php } ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>