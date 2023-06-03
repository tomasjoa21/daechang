<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$bwgs_skin_url.'/bpwg_style.css">', 3);
//print_r2($bwg_arr);
//if(!$is_member) bwg_add_css_file('form',3);
if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		${$key} = $val;
	}
}
$option_flag = (count($bwg_arr['option'])) ? true : false;
if($option_flag){
//if(true){
	foreach($bwg_arr['option'] as $key=>$val){
		${$key} = $val;
		if($key == 'file'){
			foreach($file as $k=>$v){
				${$k} = $v;
			}
		}
	}
	
	//위젯객체 id 할당
	//$wid = 'qk_'.$bid.bpwg_uniqid();
	
	bwg_add_css_file('nanoscroller',3);
	bwg_add_js_file('nanoscroller_min',3);


    if (array_key_exists('mb_nick', $member)) {
        $nick  = get_text(cut_str($member['mb_nick'], $config['cf_cut_name']));
    }
    if (array_key_exists('mb_point', $member)) {
        $point = number_format($member['mb_point']);
    }
	// 읽지 않은 쪽지가 있다면
    if ($is_member) {
        if( isset($member['mb_memo_cnt']) ){
            $memo_not_read = $member['mb_memo_cnt'];
        } else {
            $memo_not_read = get_memo_not_read($member['mb_id']);
        }
        
        $mb_scrap_cnt = isset($member['mb_scrap_cnt']) ? (int) $member['mb_scrap_cnt'] : '';
    }

    $outlogin_url        = login_url($urlencode);
    $outlogin_action_url = G5_HTTPS_BBS_URL.'/login_check.php';
	
	include($bwgs_skin_path.'/bpwg_style.php');
?>
<div id="<?=$bid?>_bg"></div>
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
	<div class="open_toggle"><?=bpwg_icon('ani_arrow_left',$toggle_icon,20,24)?></div>
	<div class="sd_ttl">
		<p><span><?=$ttl_small?></span></p>
		<h3><?=$ttl_big?></h3>
	</div>
	<?php if(!$is_member){ //######################################## 로그아웃상태 ?>
	<div id="ol_before" class="ol">
		<div id="ol_be_cate">
			<h2 class="sound_only"><span>회원</span>로그인</h2>
		</div>
		<form name="foutlogin" action="<?php echo $outlogin_action_url ?>" onsubmit="return fhead_submit(this);" method="post" autocomplete="off">
			<fieldset>
				<div class="ol_wr">
					<input type="hidden" name="url" value="<?php echo $outlogin_url ?>">
					<label for="ol_id" id="ol_idlabel" class="sound_only">회원아이디<strong>필수</strong></label>
					<input type="text" id="ol_id" name="mb_id" required maxlength="20" placeholder="아이디">
					<label for="ol_pw" id="ol_pwlabel" class="sound_only">비밀번호<strong>필수</strong></label>
					<input type="password" name="mb_password" id="ol_pw" required maxlength="20" placeholder="비밀번호">
				</div>
				<div class="ol_auto_wr"> 
					<div id="ol_svc">
						<input type="submit" id="ol_submit" value="로그인" class="ol_btn_login">
						<a href="<?php echo G5_BBS_URL ?>/register.php" class="ol_btn join joinfind">회원가입</a>
						<a href="<?php echo G5_BBS_URL ?>/password_lost.php" id="ol_password_lost" class="ol_btn joinfind">정보찾기</a>
					</div>
					<div id="ol_auto" class="chk_box">
						<label for="auto_login" class="label_checkbox" style="margin-left:0;">
							<input type="checkbox" id="auto_login" name="auto_login" value="1">
							<strong></strong>
							<span>자동로그인</span>
						</label>
					</div>
				</div>
				<?php
				// 소셜로그인 사용시 소셜로그인 버튼
				@include_once(get_social_skin_path().'/social_login.skin.php');
				?>
			</fieldset>
		</form>
	</div>
	<script>
	$omi = $('#ol_id');
	$omp = $('#ol_pw');
	$omi_label = $('#ol_idlabel');
	$omi_label.addClass('ol_idlabel');
	$omp_label = $('#ol_pwlabel');
	$omp_label.addClass('ol_pwlabel');

	$(function() {

		$("#auto_login").click(function(){
			if ($(this).is(":checked")) {
				if(!confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?"))
					return false;
			}
		});
	});

	function fhead_submit(f)
	{
		return true;
	}
	</script>
	<?php }else{ //############################################# 로그인상태 
	// 쿠폰
	$cp_count = 0;
	$sql = " select cp_id
				from {$g5['g5_shop_coupon_table']}
				where mb_id IN ( '{$member['mb_id']}', '전체회원' )
				  and cp_start <= '".G5_TIME_YMD."'
				  and cp_end >= '".G5_TIME_YMD."' ";
	$res = sql_query($sql);
	
	for($k=0; $cp=sql_fetch_array($res); $k++) {
	    if(!is_used_coupon($member['mb_id'], $cp['cp_id']))
			$cp_count++;
	}
	?>
    <div id="ol_after" class="ol">
		<header id="ol_after_hd">
			<h2 class="sound_only">나의 회원정보</h2>
			<table>
				<tbody>
					<tr>
						<td class="td_first">
							<span class="profile_img">
								<?php echo get_member_profile_img($member['mb_id']); ?>
								<?php if ($is_admin == 'super' || $is_auth) {  ?><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>" class="btn_admin"><i class="fa fa-cog fa-fw"></i><span class="sound_only">관리자</span></a><?php }  ?>
							</span>
						</td>
						<td>
							<?php if ($is_admin == 'super' || $is_auth) {  ?>
							<a href="<?=G5_ADMIN_URL?>" target="_blank"><strong><?php echo $nick ?></strong></a> <span>님</span><br><span style="margin-left:5px;">환영합니다.</span><br>
							<?php }else { ?>
							<strong><?php echo $nick ?></strong> <span>님</span><br><span style="margin-left:5px;">환영합니다.</span><br>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		</header>
		<?php
		//$info_modify_url = ($is_admin == 'super' || $is_auth) ? G5_ADMIN_URL.'/member_form.php?w=u&mb_id='.$member['mb_id'] : G5_BBS_URL.'/member_confirm.php?url=register_form.php';
		$info_modify_url = G5_BBS_URL.'/member_confirm.php?url=register_form.php';
		?>
		<div class="mb_btn_box">
			<a href="<?php echo G5_BBS_URL ?>/logout.php" id="ol_after_logout" class="ol_btn btn_logout">로그아웃</a>
			<a href="<?=$info_modify_url?>" id="s_ol_after_info" class="ol_btn btn_infomodify">정보수정</a>
		</div>
		<ul id="ol_after_private">
			<li>
				<a href="<?php echo G5_SHOP_URL ?>/coupon.php" target="_blank" class="win_point">
					<span class="sp_ttl">쿠폰</span>
					<strong><?php echo number_format($cp_count); ?></strong>
				</a>
			</li>
			<li>
				<a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" id="ol_after_pt" class="win_point">
					<span class="sp_ttl">포인트</span>
					<strong><?php echo $point ?></strong>
				</a>
			</li>
			<li>
				<a href="<?php echo G5_BBS_URL ?>/memo.php" target="_blank" id="ol_after_memo" class="win_memo">
					<span class="sound_only">안 읽은 </span><span class="sp_ttl">쪽지</span>
					<strong><?php echo $memo_not_read ?></strong>
				</a>
			</li>
			<li>
				<a href="<?php echo G5_SHOP_URL; ?>/mypage.php" target="_blank">
					<span class="sp_ttl">마이페이지</span>
				</a>
			</li>
		</ul>
	</div>

	<script>
	// 탈퇴의 경우 아래 코드를 연동하시면 됩니다.
	function member_leave()
	{
		if (confirm("정말 회원에서 탈퇴 하시겠습니까?"))
			location.href = "<?php echo G5_BBS_URL ?>/member_confirm.php?url=member_leave.php";
	}
	</script>	
	<?php } ############################################################## 로그인끝 ?>
	<!------- 로그인아웃 끝 ------->
	<?php
	//$prev_ic = "<img src='".G5_BPWIDGET_SVG_URL."/icon_arrow_left.svg' width='15' height='15' title='이전'>";
	//$next_ic = bpwg_icon('arrow_right','#777',15,15);
	?>
	<ul id="ul_qck">
		<li class="li_qck li_today">
			<button type="button" class="bt_qck">오늘 본 상품<span><?php echo get_view_today_items_count(); ?></span><?=bpwg_icon('ani_plus',$accordion_font,15,15)?></button>
			<div class="qck_pg nano"><?php include($bwgs_skin_path.'/bpwg_today.skin.php'); // 오늘 본 상품 ?></div>
		</li>
		<li class="li_qck li_cart">
			<button type="button" class="bt_qck bt_crt">장바구니<span><?php echo get_boxcart_datas_count(); ?></span><?=bpwg_icon('ani_plus',$accordion_font,15,15)?></button>
			<div class="qck_pg nano"><?php include($bwgs_skin_path.'/bpwg_cart.skin.php'); // 장바구니 ?></div>
		</li>
		<li class="li_qck li_wish">
			<button type="button" class="bt_qck">위시리스트<span><?php echo get_wishlist_datas_count(); ?></span><?=bpwg_icon('ani_plus',$accordion_font,15,15)?></button>
			<div class="qck_pg nano"><?php include($bwgs_skin_path.'/bpwg_wish.skin.php'); // 위시리스트 ?></div>
		</li>
	</ul>
	<script>
		$('#<?=$bid?> .bt_qck').click(function(){
			$('#<?=$bid?> .qck_pg').nanoScroller({destroy:true});
			$(this).find('svg').toggleClass('show').end().siblings('.qck_pg').toggleClass('show').parent().siblings('.li_qck').find('.bt_qck').find('svg').removeClass('show').end().siblings('.qck_pg').removeClass('show');
			$(this).siblings('.qck_pg').nanoScroller();
		});
	</script>
</div>
<script>
//바로 아래 소스는 수정후 주석처리해라
//$('#<?=$bid?> .open_toggle').addClass('show').find('svg').addClass('show').end().parent().addClass('show').siblings('#<?=$bid?>_bg').addClass('show');	

var <?=$bid?>_tog_wd = $('#<?=$bid?> .open_toggle').width();
var <?=$bid?>_tog_ht = $('#<?=$bid?> .open_toggle').height();
var <?=$bid?>_togsgv_wd = $('#<?=$bid?> .open_toggle svg').width();
var <?=$bid?>_togsgv_ht = $('#<?=$bid?> .open_toggle svg').height();
$('#<?=$bid?> .open_toggle svg').css({'left':(<?=$bid?>_tog_wd - <?=$bid?>_togsgv_wd) / 2,'top':(<?=$bid?>_tog_ht - <?=$bid?>_togsgv_ht) / 2});
$('#<?=$bid?> .open_toggle,#<?=$bid?>_bg').on('click',function(){
	if($(this).hasClass('show')){
		if($(this).hasClass('open_toggle')) $(this).removeClass('show').find('svg').removeClass('show').end().parent().removeClass('show').siblings('#<?=$bid?>_bg').removeClass('show');
		else $(this).removeClass('show').siblings('#<?=$bid?>').removeClass('show').find('.open_toggle').removeClass('show').find('svg').removeClass('show');
	}else{
		if($(this).hasClass('open_toggle')) $(this).addClass('show').find('svg').addClass('show').end().parent().addClass('show').siblings('#<?=$bid?>_bg').addClass('show');		
		else $(this).addClass('show').siblings('#<?=$bid?>').addClass('show').find('.open_toggle').addClass('show').find('svg').addClass('show');
	}
});
</script>	
<?php } ?>
