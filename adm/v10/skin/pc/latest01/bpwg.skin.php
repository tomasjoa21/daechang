<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$bwgs_skin_url.'/bpwg_style.css">', 3);
//print_r2($bwg_arr);
//$bwg_arr['config']) -> bwgs
//$bwg_arr['option']) -> bwgo
//$bwg_arr['content']) -> bwgc(콘텐트 목록을 담는다.)
//$boc -> bwgc(한 개 콘텐트만 담는다.)
//$bocf -> bwgc(한 개 콘텐트의 첨부파일만 담는다.) $bocf[bwcfile][0]['bwgc_idx']
if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		if($key == 'mb_id') ${'bwgs_mb_id'} = $val; //변수 충돌을 피하기 위해서
		else ${$key} = $val;
	}
}
$option_flag = (count($bwg_arr['option'])) ? true : false;
if($option_flag){
	foreach($bwg_arr['option'] as $key=>$val){
		${$key} = $val;
		if($key == 'file'){
			foreach($file as $k=>$v){
				${$k} = $v;
			}
		}
	}
	//관리자버튼에 할당할 id값
	$adid = 'ad_'.$bid.bpwg_uniqid();
	//위젯객체 id 할당(접두어:로고=lg,헤더=hd...등등)
	$wid = 'qk_'.$bid.bpwg_uniqid();
	/*
	include($bwgs_skin_path.'/bpwg_style.php');
	include_once($bwgs_skin_path.'/bpwg_style.head.php');
	include_once($bwgs_skin_path.'/bpwg_head.skin.php');
	include_once($bwgs_skin_path.'/bpwg_style.tail.php');
	*/
}

$lst_title_href = 'javascript:';
if($lst_type == 'board_'){	
	$bod_table = $g5['write_prefix'].$lst_table;
	$ltsql = " SELECT wr_id,mb_id,ca_name,wr_email,wr_homepage,wr_hit,wr_seo_title,wr_comment,wr_option,wr_reply,wr_link1,wr_link2,wr_subject,wr_name,wr_datetime,wr_file,wr_last FROM {$bod_table} WHERE wr_is_comment != '1' ORDER BY wr_datetime DESC LIMIT {$lst_cnt} ";
	$lst_title_href = G5_BBS_URL.'/board.php?bo_table='.$lst_table;
}else if($lst_type == 'itemuse_'){
	$ltsql = " SELECT it_id,mb_id,is_subject,is_score,is_content,is_name,is_time FROM {$g5['g5_shop_item_use_table']} WHERE is_confirm = '1' ORDER BY is_time DESC LIMIT {$lst_cnt} ";
	$lst_title_href = G5_SHOP_URL.'/itemuselist.php';
}
$ltresult = sql_query($ltsql,1);
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg bpwg_latest">
<?php include($bwgs_skin_path.'/bpwg_style.php'); ?>
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<div class="lt_box">
		<h3><a href="<?=$lst_title_href?>"><?=$lst_name?><span><?=bpwg_icon('plus',$title_icon_color,$title_font_size,$title_font_size)?></span></a></h3>
		<div class="lt_con">
			<?php include($bwgs_skin_skin_path.'/'.$lst_skin); ?>
		</div>
	</div>
</div>
