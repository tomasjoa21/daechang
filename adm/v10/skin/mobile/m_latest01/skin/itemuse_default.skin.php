<?php if($ltresult->num_rows){
$lst_ht = (($lst_height - 1) / $lst_cnt) - 2;
//it_id,is_name,is_subject,is_score,mb_id,is_time
$lst_score_width = 70;
$lst_add_width = 50;
$lst_left_offset = ($lst_add == 'no') ? $lst_score_width : $lst_add_width + $lst_score_width;
$thumb_wd = $lst_ht;
$thumb_ht = $lst_ht;
?>
<style>
#<?=$bid?> .lt_box .lt_con .lt_list{}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul{}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li{}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv{position:relative;padding-left:<?=$thumb_wd?>px;padding-right:<?=$lst_left_offset?>px;font-size:<?=$lst_font_size?>px;border:1px solid <?=$lst_bg_color?>;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv img{position:absolute;left:0;top:0;width:<?=$thumb_wd?>px;height:<?=$thumb_ht?>px;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .subj{position:relative;cursor:pointer;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .subj .is_subj{height:<?=$lst_ht?>px;line-height:<?=$lst_ht?>px;padding:0 5px;color:<?=$lst_font_color?>;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .nm,
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .dt{position:absolute;top:0;right:<?=$lst_score_width?>px;height:<?=$lst_ht?>px;line-height:<?=$lst_ht?>px;padding:0 5px;width:<?=$lst_add_width?>px;overflow-x:hidden;text-align:right;color:<?=$lst_add_font_color?>;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .score{position:absolute;top:0;right:0;height:<?=$lst_ht?>px;line-height:<?=$lst_ht?>px;padding:0 5px;width:<?=$lst_score_width?>px;overflow-x:hidden;text-align:center;background-repeat:no-repeat;background-position:center center;background-size:contain;}


#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .is_cont{position:relative;padding:5px;border:1px solid #ddd;border-bottom-left-radius:5px;border-bottom-right-radius:5px;background:#f1f1f1;font-size:0.8em;overflow:hidden;display:none;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .is_cont.focus{display:block;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .is_cont > p{height:15px;line-height:15px;margin-bottom:5px;font-weight:500;color:#777;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
</style>
<div class="lt_list">
	<ul class="lt_list_ul">
	<?php for($i=0;$row=sql_fetch_array($ltresult);$i++){ 
		$row['datetime'] = substr($row['is_time'],0,10);
		$row['datetime2'] = $row['is_time'];
		if ($row['datetime'] == G5_TIME_YMD)
			$row['datetime2'] = substr($row['datetime2'],11,5);
		else
			$row['datetime2'] = substr($row['datetime2'],5,5);

		$row['is_content'] = strip_tags($row['is_content']);
		$row['star'] = get_star($row['is_score']);
		$row['star_url'] = G5_URL.'/shop/img/s_star'.$row['star'].'.png';
		$row['is_name'] = ($row['is_name']) ? mb_substr($row['is_name'],'0',2)."**" : '';
		$it_name = sql_fetch(" SELECT it_name FROM {$g5['g5_shop_item_table']} WHERE it_id = '{$row['it_id']}' ");
		$row['it_name'] = $it_name['it_name'];
	?>
		<li class="lt_list_li">
			<div class="lt_list_dv">
				<a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$row['it_id']?>">
					<!--//$it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='', $is_crop=false, $is_url=false-->
					<?php echo bpwg_get_it_image($row['it_id'], $thumb_wd, $thumb_ht,false,'','',true,false); ?>
				</a>
				<?php if($row['is_subject']){ ?>
				<div class="subj">
					<p class="is_subj">
						<?=$row['is_subject']?>
					</p>
				</div>
				<?php } ?>
				<?php if($lst_add == 'id'){ ?><div class="nm"><?=$row['is_name']?></div><?php } ?>
				<?php if($lst_add == 'date'){ ?><div class="dt"><?=$row['datetime2']?></div><?php } ?>
				<div class="score" style="background-image:url(<?=$row['star_url']?>);"></div>
			</div>
			<?php if($row['is_subject']){ ?>
			<div class="is_cont">
				<p>
					<a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$row['it_id']?>"><?=$row['it_name']?></a>
				</p>
				<?=$row['is_content']?>
			</div>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>
</div><!--//.lt_list-->
<?php }else{ ?>
<div class="lt_empty">
	<div class="lt_empty_con">
		자료가 없습니다.
	</div><!--//.lt_empty_con-->
</div><!--//.lt_list-->
<?php } ?>
<script>
var <?=$bid?>_subj = $('#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .lt_list_dv .subj');
<?=$bid?>_subj.on('click',function(){
	if($(this).parent().siblings('.is_cont').hasClass('focus')){
		$(this).parent().siblings('.is_cont').removeClass('focus');
	}else{
		$('#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .is_cont').removeClass('focus');
		$(this).parent().siblings('.is_cont').addClass('focus');
	}
});
</script>