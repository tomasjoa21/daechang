<div id="<?=$pnl?>" class="">
	<?php include($bwgs_skin_path.'/bpwg_style.panel.php'); ?>
	<div class="pnl_bg"></div>
	<div class="pnl_con">
		<div class="pnl_top">
			<a href="javascript:" class="pnl_close">
				<?php bpwg_icon('close',$panel_top_icon_color,$panel_top_icon_size,$panel_top_icon_size); ?>
				<span class="sound_only">판넬닫기</span>
			</a>
			<div class="pnl_top_con">
				<table class="pnl_top_tbl">
					<tr>
					<?php if($is_member){ ?>
					<td class="pnl_top_td">
						<span><?php echo get_member_profile_img($member['mb_id']); ?></span>
						<?php if($is_admin == 'super' || $is_auth){ ?>
						<a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>"><nobr><?=$member['mb_nick']?></nobr></a>
						<?php }else{ ?>
						<a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php"><nobr><?=$member['mb_nick']?></nobr></a>
						<?php } ?>
					</td>
					<?php }else{ ?>
					<td class="pnl_top_td">
						<a href="<?=G5_BBS_URL?>/login.php?url=<?=$urlencode?>">로그인<?php bpwg_icon('arrow_right',$panel_top_font_color,$panel_top_font_size-3,$panel_top_font_size-3); ?></a>
					</td>
				<?php } ?>
					</tr>
				</table>
			</div><!--//.pnl_top_con-->
		</div>
		<table class="ic_tbl">
			<tbody>
				<tr>
					<?php if($is_member){ ?>
					<td>
						<a href="<?=G5_BBS_URL?>/logout.php?url=<?=$urlencode?>">
							<?php bpwg_icon('logout',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>로그아웃</span>
						</a>
					</td>
					<?php }else{ ?>
					<td>
						<a href="<?php echo G5_BBS_URL ?>/register.php">
							<?php bpwg_icon('pen',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>회원가입</span>
						</a>
					</td>
					<?php } ?>
					<td>
						<a href="<?php echo G5_SHOP_URL; ?>/mypage.php">
							<?php bpwg_icon('person',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>마이페이지</span>
						</a>
					</td>
					<td>
						<a href="<?php echo G5_BBS_URL ?>/qalist.php">
							<?php bpwg_icon('qna',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>1:1문의</span>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="<?php echo G5_SHOP_URL; ?>/cart.php">
							<?php bpwg_icon('cart',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>장바구니</span>
						</a>
					</td>
					<td>
						<a href="<?php echo G5_SHOP_URL; ?>/wishlist.php">
							<?php bpwg_icon('hart',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>찜한상품</span>
						</a>
					</td>
					<td>
						<a href="<?php echo G5_BBS_URL ?>/faq.php">
							<?php bpwg_icon('question',$panel_grid_icon_color,$panel_grid_icon_size,$panel_grid_icon_size); ?><br>
							<span>FAQ</span>
						</a>
					</td>
				</tr>
			</tbody>
		</table><!--//.ic_tbl-->
		<?php if(count($nlist)){?>
		<ul class="n1_ul">
			<?php for($i=0;$i<count($nlist);$i++){ 
			
			?>
			<li class="n1_li">
				<div class="n1_dv">
					<a class="n1_a" href="<?=$nlist[$i]['me_link']?>" target="_<?=$nlist[$i]['me_target']?>">
						<?=$nlist[$i]['me_name']?>
					</a>
					<?php if(count($nlist[$i]['me_2'])){ ?>
					<button type="button" class="n_svg n1_btn">
						<?php bpwg_icon('ani_plus',$menu1_font_color,$menu1_icon_size,$menu1_icon_size); ?>
					</button>
					<?php } ?>
				</div>
				<?php if(count($nlist[$i]['me_2'])){?>
				<ul class="n_ul n2_ul">
					<?php for($j=0;$j<count($nlist[$i]['me_2']);$j++){ 
				
					?>
					<li class="n2_li">
						<div class="n2_dv">
							<a class="n2_a" href="<?=$nlist[$i]['me_2'][$j]['me_link']?>" target="_<?=$nlist[$i]['me_2'][$j]['me_target']?>">
								<?=$nlist[$i]['me_2'][$j]['me_name']?>
							</a>
							<?php if(count($nlist[$i]['me_2'][$j]['me_3'])){ ?>
								<button type="button" class="n_svg n2_btn">
								<?php bpwg_icon('ani_plus',$menu2_font_color,$menu2_icon_size,$menu2_icon_size); ?>
								</button>
							<?php } ?>
						</div>
						<?php if(count($nlist[$i]['me_2'][$j]['me_3'])){ ?>
						<ul class="n_ul n3_ul">
							<?php for($k=0;$k<count($nlist[$i]['me_2'][$j]['me_3']);$k++){ 
						
							?>
							<li class="n3_li">
								<div class="n3_dv">
									<a class="n3_a" href="<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_link']?>" target="_<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_target']?>">
										<?=$nlist[$i]['me_2'][$j]['me_3'][$k]['me_name']?>
									</a>
								</div>
							</li><!--//.n3_li-->
							<?php } ?>
						</ul><!--//.n3_ul-->
						<?php } ?>
					</li><!--//.n2_li-->
					<?php } ?>
				</ul><!--//.n2_ul-->
				<?php } ?>
			</li><!--//.n1_li-->
			<?php } ?>
		</ul><!--//.n1_ul-->
		<?php } //if(count($nlist)) ?>
		<div class="pnl_ft_info">
			<div class="p_btn_group"><a href="<?=G5_SHOP_URL?>/personalpay.php" class="btn_pv">개인결제</a></div>
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
		</div><!--//.pnl_ft_info-->
	</div><!--//.pnl_con-->
</div>
<script>
var <?=$pnl?>_open = $('#<?=$bid?> .nav_btn'); 
var <?=$pnl?> = $('#<?=$pnl?>');
var <?=$pnl?>_bg = $('#<?=$pnl?> .pnl_bg');
var <?=$pnl?>_con = $('#<?=$pnl?> .pnl_con');
var <?=$pnl?>_nav = $('#<?=$pnl?> .pnl_con .nav_area');
var <?=$pnl?>_unit = '<?=$panel_width_unit?>';
var <?=$pnl?>_event_time = 200;
var <?=$pnl?>_con_wd = (<?=$pnl?>_unit == 'px') ? <?=$panel_width?> : 0;
var <?=$pnl?>_con_x = 0;
var <?=$pnl?>_offset_x = 0;
var <?=$pnl?>_body_wd = 0;

let <?=$pnl?>_pos_y = 0;
let <?=$pnl?>_pnl_f = false;
$(function(){
	<?=$pnl?>_offset_x = $('body').offset().left;
	<?=$pnl?>_body_wd = $('body').width() + 10;//스크롤 폭때문에 블라인드가 약 10px모자르게 표시되므로 +10을 했다.
	if(<?=$pnl?>_unit != 'px') <?=$pnl?>_con_wd = (<?=$panel_width?> * <?=$pnl?>_body_wd) / 100;
	<?=$pnl?>_con_x = -<?=$pnl?>_con_wd;
	<?=$pnl?>.css({'left':<?=$pnl?>_offset_x,'width':<?=$pnl?>_body_wd});
	<?=$pnl?>_con.css({'<?=$panel_align?>':<?=$pnl?>_con_x});
});

$(window).resize(function(){
	<?=$pnl?>_offset_x = $('body').offset().left;
	<?=$pnl?>_body_wd = $('body').width();
	if(<?=$pnl?>_unit != 'px') <?=$pnl?>_con_wd = (<?=$panel_width?> * <?=$pnl?>_body_wd) / 100;
	<?=$pnl?>_con_x = -<?=$pnl?>_con_wd;
	<?=$pnl?>.css({'left':<?=$pnl?>_offset_x,'width':<?=$pnl?>_body_wd});
	if(!<?=$pnl?>.hasClass('show')) <?=$pnl?>_con.css({'<?=$panel_align?>':<?=$pnl?>_con_x});
	else <?=$pnl?>_con.css({'<?=$panel_align?>':0});
});	

<?=$pnl?>_open.on('click',function(){
	<?=$pnl?>_pos_y = $(window).scrollTop();
	<?=$pnl?>.addClass('show');
	<?=$pnl?>_con.animate({'<?=$panel_align?>':0},<?=$pnl?>_event_time);
	
	if(browser_name.indexOf('ie') != -1 && browser_name.indexOf('edge') != -1) <?=$pnl?>_scroll_no();
	else $('html,body').addClass('is_hidden');
});

$('#<?=$pnl?> .pnl_bg,#<?=$pnl?> .pnl_close').on('click',function(){
	<?=$pnl?>_con.animate({'<?=$panel_align?>':<?=$pnl?>_con_x},<?=$pnl?>_event_time,function(){
		<?=$pnl?>.removeClass('show');
		
		if(browser_name.indexOf('ie') != -1 && browser_name.indexOf('edge') != -1) <?=$pnl?>_scroll_ok();
		else $('html,body').removeClass('is_hidden');
	});
});

$('#<?=$pnl?> .n_svg').on('click',function(){
	$(this).find('svg').toggleClass('show').end().parent().siblings('.n_ul').toggleClass('show');
});

function <?=$pnl?>_scroll_no(){
    $('html,body').addClass('is_hidden').on('touchmove mousewheel', function(e){
        e.preventDefault();
		e.stopPropagation();
		return false;
    });
}
function <?=$pnl?>_scroll_ok(){
    $('html,body').removeClass('is_hidden').off('touchmove mousewheel');
}
</script>