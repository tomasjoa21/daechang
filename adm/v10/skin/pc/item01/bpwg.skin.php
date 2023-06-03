<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

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
	$wid = 'it_'.$bid.bpwg_uniqid();

}

$itcommon = " WHERE it_use = '1' AND ca_id != '' ";
$it_ttl = '베스트 상품';
switch($item_show_type){
	case 1:
		$itcommon .= "AND it_type1 = 1";
		$it_ttl = '히트 상품';
		break;
	case 2:
		$itcommon .= "AND it_type2 = 1";
		$it_ttl = '추천 상품';
		break;
	case 3:
		$itcommon .= "AND it_type3 = 1";
		$it_ttl = '최신 상품';
		break;
	case 4:
		$itcommon .= "AND it_type4 = 1";
		$it_ttl = '인기 상품';
		break;
	case 5:
		$itcommon .= "AND it_type5 = 1";
		$it_ttl = '할인 상품';
		break;
	case 6:
		$itcommon .= "AND (ca_id = '{$ca_id}' OR ca_id2 = '{$ca_id}' OR ca_id3 = '{$ca_id}') ";
		$itttl = sql_fetch(" SELECT ca_name FROM {$g5['g5_shop_category_table']} WHERE ca_id = '{$ca_id}' ");
		$it_ttl = $itttl['ca_name'];
		break;
	default:	
}

if($item_title) $it_ttl = $item_title;
$it_list_cnt = $item_mod_cnt * $item_row_cnt;
$it_sql = " SELECT it_id,it_name,ca_id,ca_id2,ca_id3,it_type1,it_type2,it_type3,it_type4,it_type5,it_basic,it_cust_price,it_price,it_tel_inq,it_img1,it_img2,it_img3,it_img4,it_img5,it_img6,it_img7,it_img8,it_img9,it_img10 FROM {$g5['g5_shop_item_table']}{$itcommon} ORDER BY it_order,it_id LIMIT {$it_list_cnt} ";
$itresult = sql_query($it_sql,1);

if($itresult->num_rows){
include($bwgs_skin_path.'/bpwg_style.php');
?>
<div id="<?=$bid?>" class="<?=$bid?> bpwg">
	<?php if($is_admin == 'super' || $is_bwg_auth){ ?>
	<a id="<?=$adid?>" class="bpwg_btn_admin" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form.php?w=u&bwgs_idx=<?=$bwgs_idx?>" target="_blank" title="<?=$bwgs_cd?> 위젯"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only"><?=$bwgs_cd?> 위젯관리자</span></a>
	<script>
	$('#<?=$adid?>').hover(function(e){$(this).parent().css('border','1px solid red');},function(e){$(this).parent().css('border','0');});
	</script>
	<?php } ?>
	<div class="it_box">
		<?php
		$ttl_a1 = '';
		$ttl_a2 = '';
		if($item_show_type != 7){
			$item_list_url = ($item_show_type < 6) ? G5_SHOP_URL.'/listtype.php?type='.$item_show_type : G5_SHOP_URL.'/list.php?ca_id='.$ca_id;
			$ttl_a1 = '<a href="'.$item_list_url.'">';
			$ttl_a2 = '</a>';
		}
		?>
		<h3><?=$ttl_a1?><?=$it_ttl?><?=$ttl_a2?></h3>
		<?php include($bwgs_skin_skin_path.'/'.$item_skin); ?>
	</div>
</div>
<?php } ?>