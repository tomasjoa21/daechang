<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){ $adid = 'ad_'.$bid.bpwg_uniqid();?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<div class="tail1">
		<?php if($cs_title){ ?>
		<h3><?=$cs_title?></h3>
		<?php } ?>
		<?php if($cs_title && $default['de_admin_company_tel']){ ?>
		<div class="cs_tel"><a href="tel:<?=$default['de_admin_company_tel']?>"><?=$default['de_admin_company_tel']?></a></div>
		<?php } ?>
		<?php if($cs_title && $etc_info){ ?>
		<div class="p_group p_info"><?=$etc_info?></div>
		<?php } ?>
		
		<h3><?=$companyinfo_title?></h3>
		<div class="p_group">
			<?php if($default['de_admin_company_name']){ ?><p><strong class="strong_first">회사명 : </strong><span><?=$default['de_admin_company_name']?></span></p><?php } ?>
			<?php if($default['de_admin_company_owner']){ ?><p><strong>대표 : </strong><span><?=$default['de_admin_company_owner']?></span></p><?php } ?>
			<?php if($default['de_admin_company_saupja_no']){ ?><p><strong>사업자등록번호 : </strong><span><?=$default['de_admin_company_saupja_no']?></span><?php } ?>
			<?php if($default['de_admin_company_tel']){ ?><p><strong class="strong_first">대표전화번호 : </strong><span><a href="tel:<?=$default['de_admin_company_tel']?>"><?=$default['de_admin_company_tel']?></a></span><?php } ?>
			<?php if($default['de_admin_company_fax']){ ?><p><strong>팩스번호 : </strong><span><?=$default['de_admin_company_fax']?></span><?php } ?>
			<?php if($default['de_admin_tongsin_no']){ ?><p><strong class="strong_first">통신판매신고번호 : </strong><span><?=$default['de_admin_tongsin_no']?></span><?php } ?>
			<?php if($default['de_admin_buga_no']){ ?><p><strong>부가통신사업번호 : </strong><span><?=$default['de_admin_buga_no']?></span><?php } ?>
			<?php if($default['de_admin_info_name']){ ?><p><strong class="strong_first">정보관리자명 : </strong><span><?=$default['de_admin_info_name']?></span><?php } ?>
			<?php if($default['de_admin_info_email']){ ?><p><strong>이메일 : </strong><span><?=$default['de_admin_info_email']?></span><?php } ?>
			<?php if($default['de_admin_company_addr']){ ?>
				<p>
					<strong>주소 : </strong>
					<span>
						<?php if($default['de_admin_company_zip']){ ?>
							㉾<?=$default['de_admin_company_zip']?><br>
						<?php } ?>
						<?=$default['de_admin_company_addr']?>
					</span>
				</p>
			<?php } ?>
		</div>
		
		<?php if($bankinfo_title && $default['de_bank_account']){ ?>
		<h3><?=$bankinfo_title?></h3>
		<div class="p_group p_info"><?=$default['de_bank_account']?></div>
		<?php } ?>
	</div>
	<?php if($escro_kcp == 'yes' || $escro_lg == 'yes' || $escro_kg == 'yes' || $escro_kb == 'yes' || $escro_ibk == 'yes' || $escro_nh == 'yes' || $escro_hn == 'yes' || $escro_er == 'yes' || $inicis_kg == 'yes'){
		$kcp_path = G5_PATH.$bwg_arr['option']['file']['file_kcp'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kcp'][0]['bwga_name'];
		$kcp_src = G5_URL.$bwg_arr['option']['file']['file_kcp'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kcp'][0]['bwga_name'];
		
		$lg_path = G5_PATH.$bwg_arr['option']['file']['file_lg'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_lg'][0]['bwga_name'];
		$lg_src = G5_URL.$bwg_arr['option']['file']['file_lg'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_lg'][0]['bwga_name'];
		
		$kg_path = G5_PATH.$bwg_arr['option']['file']['file_kg'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kg'][0]['bwga_name'];
		$kg_src = G5_URL.$bwg_arr['option']['file']['file_kg'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kg'][0]['bwga_name'];
		
		$kgini_path = G5_PATH.$bwg_arr['option']['file']['file_kgini'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kgini'][0]['bwga_name'];
		$kgini_src = G5_URL.$bwg_arr['option']['file']['file_kgini'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kgini'][0]['bwga_name'];
		
		$kb_path = G5_PATH.$bwg_arr['option']['file']['file_kb'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kb'][0]['bwga_name'];
		$kb_src = G5_URL.$bwg_arr['option']['file']['file_kb'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_kb'][0]['bwga_name'];
		
		$ibk_path = G5_PATH.$bwg_arr['option']['file']['file_ibk'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_ibk'][0]['bwga_name'];
		$ibk_src = G5_URL.$bwg_arr['option']['file']['file_ibk'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_ibk'][0]['bwga_name'];
		
		$nh_path = G5_PATH.$bwg_arr['option']['file']['file_nh'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_nh'][0]['bwga_name'];
		$nh_src = G5_URL.$bwg_arr['option']['file']['file_nh'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_nh'][0]['bwga_name'];
		
		//$hn_path = G5_PATH.$bwg_arr['option']['file']['file_hn'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_hn'][0]['bwga_name'];
		//$hn_src = G5_URL.$bwg_arr['option']['file']['file_hn'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_hn'][0]['bwga_name'];
		
		//$er_path = G5_PATH.$bwg_arr['option']['file']['file_er'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_er'][0]['bwga_name'];
		//$er_src = G5_URL.$bwg_arr['option']['file']['file_er'][0]['bwga_path'].'/'.$bwg_arr['option']['file']['file_er'][0]['bwga_name'];
	?>
	<div class="tail2">
		<?php if($escro_kcp_code && $escro_kcp == 'yes' && is_file($kcp_path)){ //KCP 에스크로 ?>
		<form name="shop_check" method="post" action="http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp">
			<input type="hidden" name="site_cd" value="<?=$escro_kcp_code?>">
		</form>
		<a href="javascript:go_kcp_check();"><img src="<?=$kcp_src?>" alt="KCP에스크로"></a>
		<?php } ?>
		<?php if($escro_kg_code && $escro_kg == 'yes' && is_file($kg_path)){ //KG 에스크로 526*403 ?>
		<a href="https://mark.inicis.com/mark/popup_v1.php?mid=<?=$escro_kg_code?>" onclick="window.open(this.href,'kg_pop','width=526,height=403');return false;"><img src="<?=$kg_src?>" alt="KG에스크로"></a>
		<?php } ?>
		<?php if($escro_lg_code && $escro_lg == 'yes' && is_file($lg_path)){ //LGU+ 에스크로 ?>
		<a onclick="goValidEscrow('<?=$escro_lg_code?>');"><img src="<?=$lg_src?>" alt="LGU+에스크로" style="border:1px solid #ddd;"></a>
		<?php } ?>
		<?php //if(false){ //국민(KB) 에스크로 ?>
		<?php if($escro_kb_code && $escro_kb == 'yes' && is_file($kb_path)){ //국민(KB) 에스크로 ?>
		<form name="KB_AUTHMARK_FORM" method="get">
			<input type="hidden" name="page" value="C021590"/>
			<input type="hidden" name="cc" value="b034066:b035526"/>
			<input type="hidden" name="mHValue" value='<?=$escro_kb_code?>'/>
		</form>
		<a href="javascript:onPopKBAuthMark();"><img src="<?=$kb_src?>" alt="국민(KB)에스크로"></a>
		<?php } ?>
		<?php //if(false){ //기업(ibk) 에스크로 ?>
		<?php if($escro_ibk_code && $escro_ibk == 'yes' && is_file($ibk_path)){ //기업(ibk) 에스크로 ?>
		<form name='AUTHMARK_FORM' method='post'><input type="hidden" name="authmarkinfo"></form>
		<a href="javascript:onPopAuthMark('<?=$escro_ibk_code?>')"><img src='<?=$ibk_src?>' alt='기업은행 안심이체 인증마크'></a>
		<?php } ?>
		<?php //if(false){ //농협(nh) 에스크로 ?>
		<?php if($escro_nh_code && $escro_nh == 'yes' && is_file($nh_path)){ //농협(nh) 에스크로 ?>
		<form name='CERTMARK_FORM' method='post'><input type="hidden" name="certMarkURLKey"></form> 
		<a href="javascript:onPopCertMar('<?=$escro_nh_code?>')"><img src="<?=$nh_src?>" alt="농협에스크로"></a>
		<?php } ?>
		<?php //if($escro_hn_code && $escro_hn == 'yes' && is_file($hn_path)){ //하나(hn) 에스크로  } ?>
		<?php //if($escro_er_code && $escro_er == 'yes' && is_file($er_path)){ //EveryRich(er) 에스크로  } ?>
		<?php //if($inicis_kg_code && $inicis_kg == 'yes' && is_file($kgini_path)){ //KG 이니시스인증  } ?>
	</div>
	<script>
	<?php if($escro_kcp_code && $escro_kcp == 'yes'){ //KCP 에스크로 ?>
		function go_kcp_check(){
			var status="width=500,height=450,menubar=no,scrollbars=no,resizable=no,status=no";
			var obj = window.open('', 'kcp_pop', status);
			document.shop_check.method = "post";
			document.shop_check.target = "kcp_pop";
			document.shop_check.action = "http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp";
			document.shop_check.submit();
		}
	<?php } ?>
	<?php if($escro_lg_code && $escro_lg == 'yes'){ //LGU+ 에스크로 ?>
		$('head').append("<script language='javascript' src='https:\/\/pgweb.dacom.net\/WEB_SERVER\/js\/escrowValid.js'><\/script>");
	<?php } ?>
	<?php //if($escro_kg_code && $escro_kg == 'yes'){ //KG 에스크로 } ?>
	<?php if($escro_kb_code && $escro_kb == 'yes'){ //국민(KB) 에스크로 ?>
		function onPopKBAuthMark(){
			window.open('','KB_AUTHMARK','height=604, width=648, status=yes, toolbar=no, menubar=no,location=no');
			document.KB_AUTHMARK_FORM.action='https://okbfex.kbstar.com/quics';
			document.KB_AUTHMARK_FORM.target='KB_AUTHMARK';
			document.KB_AUTHMARK_FORM.submit();
		}
	<?php } ?>
	<?php //if(false){ //기업(ibk) 에스크로 ?>
	<?php if($escro_ibk_code && $escro_ibk == 'yes'){ //기업(ibk) 에스크로 ?>
		function onPopAuthMark(key){
		   window.open('','AUTHMARK_POPUP','height=615, width=630, status=yes, toolbar=no, menubar=no, location=no');
		   document.AUTHMARK_FORM.authmarkinfo.value = key;
		   document.AUTHMARK_FORM.action='http://mybank.ibk.co.kr/ibs/jsp/guest/esc/esc1030/esc103020/CESC302020_i.jsp';
		   document.AUTHMARK_FORM.target='AUTHMARK_POPUP';
		   document.AUTHMARK_FORM.submit();
		}
	<?php } ?>
	<?php //if(false){ //농협(nh) 에스크로 ?>
	<?php if($escro_nh_code && $escro_nh == 'yes'){ //농협(nh) 에스크로 ?>
		function onPopCertMar(key){
		   window.open('','self','height=700, width=650, status=yes, toolbar=no, menubar=no, location=no');
		   document.CERTMARK_FORM.certMarkURLKey.value = key;
		   document.CERTMARK_FORM.action='https://escrow.nonghyup.com/?certMarkURLKey=' + key; 
		   document.CERTMARK_FORM.target='self';
		   document.CERTMARK_FORM.submit();
		}
	<?php } ?>
	<?php //if($escro_hn_code && $escro_hn == 'yes'){ //하나(hn) 에스크로 } ?>
	<?php //if($escro_er_code && $escro_er == 'yes'){ //EveryRich(er) 에스크로 } ?>
	<?php //if($inicis_kg_code && $inicis_kg == 'yes'){ //KG 이니시스인증 } ?>
	</script>
	<?php } ?>
	<div class="tail3">
		<div class="com_link">
			<a href="<?=G5_BBS_URL?>/content.php?co_id=company">회사소개</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?=G5_BBS_URL?>/content.php?co_id=privacy">개인정보 처리방침</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?=G5_BBS_URL?>/content.php?co_id=provision">이용약관</a>
		</div>
		<div class="copyright">
			<?=$copyright?>
		</div>
		<!----------------푸터추가 태그및 스크립트(예:SSL보안인증관련 버튼이미지 및 스크립트)------------------>
		<?php echo $config['cf_bpwg_add_mobile_ft_tag_script']; ?>
		<!---------------//푸터추가 태그및 스크립트(예:SSL보안인증관련 버튼이미지 및 스크립트)----------------->
	</div>
	<ul class="ic_move">
		<li id="ic_top" class="ic_mv" title="최상단으로 이동"><?=bpwg_icon('arrow_up',$tail_icon_color,$icon_size,$icon_size)?></li>
		<li id="ic_mid" class="ic_mv" title="중간지점으로 이동"><?=bpwg_icon('circle',$tail_icon_color,$icon_size,$icon_size)?></li>
		<li id="ic_bot" class="ic_mv" title="최하단으로 이동"><?=bpwg_icon('arrow_down',$tail_icon_color,$icon_size,$icon_size)?></li>
	</ul>
	<?php //if($is_admin == 'super'){
	$device_mod_url = ((bpwg_is_https()) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	if(preg_match('/\?/i',$device_mod_url)){
		if(preg_match('/\?device\=mobile/i',$device_mod_url)){
			$device_mod_url = str_replace('?device=mobile','?device=pc',$device_mod_url);
		}else if(preg_match('/\&device\=mobile/i',$device_mod_url)){
			$device_mod_url = str_replace('&device=mobile','&device=pc',$device_mod_url);
		}else{
			$device_mod_url .= '&device=pc';
		}
	}else{
		$device_mod_url .= '?device=pc';
	}	
	?>
	<a href="<?=$device_mod_url?>" class="pc_mode2" title="PC모드전환"><?=bpwg_icon('pc',$tail_icon_color,$icon_size,$icon_size)?></a>
	<?php //} ?>
	<script>
	var <?=$fid?>_icon_size = <?=($icon_size+5)?>;
	var <?=$fid?>_hosizontal_offset = 10;
	var <?=$fid?>_right_offset = 5;
	var <?=$fid?>_left_pos = 0;
	var <?=$fid?>_right_pos = 0;
	var <?=$fid?>_scroll_pos = 0;
	var <?=$fid?>_pc_pos = 0;
	var <?=$fid?>_top_y = 0;
	var <?=$fid?>_mid_y = $(document).height()/2 - ($(window).height()/2 + 70);
	var <?=$fid?>_bot_y = $(document).height();
	$('#<?=$bid?> #ic_top').attr('y',<?=$fid?>_top_y);
	$('#<?=$bid?> #ic_mid').attr('y',<?=$fid?>_mid_y);
	$('#<?=$bid?> #ic_bot').attr('y',<?=$fid?>_bot_y);
	$('#<?=$bid?> .ic_mv').on('click',function(){
		var <?=$fid?>_mv_y = Number($(this).attr('y'));
		$('html,body').stop().animate({scrollTop:<?=$fid?>_mv_y},300);
	});
	
	//이상하게도 모바일에서 body,html요소의 폭이 100%가 아니라서 $(document).width()에 맞췄다.
	$(function(){
		<?=$fid?>_left_pos = $('body').offset().left + <?=$fid?>_hosizontal_offset;
		<?=$fid?>_right_pos = $('body').offset().left + $('body').width() - (<?=$fid?>_hosizontal_offset + <?=$fid?>_right_offset) - <?=$fid?>_icon_size;
		<?=$fid?>_scroll_pos = ('<?=$scroll_align?>' == 'left') ? <?=$fid?>_left_pos : <?=$fid?>_right_pos;
		<?=$fid?>_pc_pos = ('<?=$pc_align?>' == 'left') ? <?=$fid?>_left_pos : <?=$fid?>_right_pos;
		$('#<?=$bid?> .ic_move').css({'left':<?=$fid?>_scroll_pos,'bottom':<?=$fid?>_hosizontal_offset});
		<?php //if($is_admin == 'super'){ ?>
		$('#<?=$bid?> .pc_mode2').css({'left':<?=$fid?>_pc_pos,'bottom':<?=$fid?>_hosizontal_offset});
		<?php //} ?>
	});
	
	$(window).resize(function(){
		<?=$fid?>_left_pos = $('body').offset().left + <?=$fid?>_hosizontal_offset;
		<?=$fid?>_right_pos = $('body').offset().left + $('body').width() - (<?=$fid?>_hosizontal_offset + <?=$fid?>_right_offset) - <?=$fid?>_icon_size;
		<?=$fid?>_scroll_pos = ('<?=$scroll_align?>' == 'left') ? <?=$fid?>_left_pos : <?=$fid?>_right_pos;
		<?=$fid?>_pc_pos = ('<?=$pc_align?>' == 'left') ? <?=$fid?>_left_pos : <?=$fid?>_right_pos;
		$('#<?=$bid?> .ic_move').css({'left':<?=$fid?>_scroll_pos,'bottom':<?=$fid?>_hosizontal_offset});
		<?php //if($is_admin == 'super'){ ?>
		$('#<?=$bid?> .pc_mode2').css({'left':<?=$fid?>_pc_pos,'bottom':<?=$fid?>_hosizontal_offset});
		<?php //} ?>
	});
	
	</script>
</div>