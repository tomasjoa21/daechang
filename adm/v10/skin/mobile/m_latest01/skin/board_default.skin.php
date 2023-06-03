<?php if($ltresult->num_rows){
$lst_ht = (($lst_height-1) / $lst_cnt) - 2;
//wr_id,wr_subject,wr_name,wr_datetime
$lst_left_offset = ($lst_add == 'no') ? 0 : 80;

$board = get_board_db($lst_table, true);

$thumb_width = 0;
$thumb_height = 0;

?>
<style>
#<?=$bid?> .lt_box .lt_con .lt_list{}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul{}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li{position:relative;padding-right:<?=$lst_left_offset?>px;font-size:<?=$lst_font_size?>px;border:1px solid <?=$lst_bg_color?>;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .subj{height:<?=$lst_ht?>px;line-height:<?=$lst_ht?>px;padding:0 5px;color:<?=$lst_font_color?>;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .nm,
#<?=$bid?> .lt_box .lt_con .lt_list .lt_list_ul .lt_list_li .dt{position:absolute;top:0;right:0;height:<?=$lst_ht?>px;line-height:<?=$lst_ht?>px;padding:0 5px;width:<?=$lst_left_offset?>px;overflow-x:hidden;text-align:right;color:<?=$lst_add_font_color?>;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;}

#<?=$bid?> .title_icon {margin-right:2px}
#<?=$bid?> .fa-heart {color:#ff0000}
#<?=$bid?> .fa-lock {display:inline-block;line-height:14px;width:16px;font-size:0.833em;color:#4f818c;background:#cbe3e8;text-align:center;border-radius:2px;font-size:12px;border:1px solid #cbe3e8;vertical-align:middle}
#<?=$bid?> .new_icon {display:inline-block;width:16px;line-height:16px;font-size:0.833em;color:#23db79;background:#b9ffda;text-align:center;border-radius:2px;margin-left:2px;font-weight:bold;vertical-align:middle}
#<?=$bid?> .hot_icon {display:inline-block;width:16px;line-height:16px;font-size:0.833em;color:#ff0000;background:#ffb9b9;text-align:center;border-radius:2px;vertical-align:middle}
#<?=$bid?> .fa-caret-right {color:#bbb}
#<?=$bid?> .fa-download {display:inline-block;width:16px;line-height:16px;font-size:0.833em;color:#daae37;background:#ffefb9;text-align:center;border-radius:2px;margin-left:5px;vertical-align:middle}
#<?=$bid?> .fa-link {display:inline-block;width:16px;line-height:16px;font-size:0.833em;color:#b451fd;background:#edd3fd;text-align:center;border-radius:2px;margin-left:5px;vertical-align:middle}

</style>
<div class="lt_list">
	<ul class="lt_list_ul">
	<?php for($i=0;$row=sql_fetch_array($ltresult);$i++){ 
		$board_notice = array_map('trim', explode(',', $board['bo_notice']));
    	$row['is_notice'] = in_array($row['wr_id'], $board_notice);
		
		if( ! (isset($row['wr_seo_title']) && $row['wr_seo_title']) && $row['wr_id'] ){
			seo_title_update(get_write_table_name($board['bo_table']), $row['wr_id'], 'bbs');
		}

		// 목록에서 내용 미리보기 사용한 게시판만 내용을 변환함 (속도 향상) : kkal3(커피)님께서 알려주셨습니다.
		if ($board['bo_use_list_content'])
		{
			$html = 0;
			if (strstr($row['wr_option'], 'html1'))
				$html = 1;
			else if (strstr($row['wr_option'], 'html2'))
				$html = 2;

			$row['content'] = conv_content($row['wr_content'], $html);
		}

		$row['comment_cnt'] = '';
		if ($row['wr_comment'])
			$row['comment_cnt'] = "<span class=\"cnt_cmt\">".$row['wr_comment']."</span>";

		$row['datetime'] = substr($row['wr_datetime'],0,10);
		$row['datetime2'] = $row['wr_datetime'];
		if ($row['datetime'] == G5_TIME_YMD)
			$row['datetime2'] = substr($row['datetime2'],11,5);
		else
			$row['datetime2'] = substr($row['datetime2'],5,5);
		
		// 4.1
		$row['last'] = substr($row['wr_last'],0,10);
		$row['last2'] = $row['wr_last'];
		if ($row['last'] == G5_TIME_YMD)
			$row['last2'] = substr($row['last2'],11,5);
		else
			$row['last2'] = substr($row['last2'],5,5);
		
		$row['wr_homepage'] = get_text($row['wr_homepage']);

		$row['wr_name'] = ($row['wr_name']) ? mb_substr($row['wr_name'],'0',2)."**" : '';
		if ($board['bo_use_sideview'])
			$row['name'] = get_sideview($row['mb_id'], $row['wr_name'], $row['wr_email'], $row['wr_homepage']);
		else
			$row['name'] = '<span class="'.($row['mb_id']?'sv_member':'sv_guest').'">'.$row['wr_name'].'</span>';

		$reply = $row['wr_reply'];
		$row['reply'] = strlen($reply)*20;

		//$bwgs_skin_skin_url
		$row['icon_reply'] = '';
		if ($row['reply'])
			$row['icon_reply'] = '<img src="'.$bwgs_skin_skin_url.'/img/icon_reply.gif" class="icon_reply" alt="답변글">';

		$row['icon_link'] = '';
		if ($row['wr_link1'] || $row['wr_link2'])
			$row['icon_link'] = '<i class="fa fa-link" aria-hidden="true"></i> ';

		// 분류명 링크
    	$row['ca_name_href'] = get_pretty_url($board['bo_table'], '', 'sca='.urlencode($row['ca_name']));
		
		$row['href'] = get_pretty_url($board['bo_table'], $row['wr_id'], $qstr);
    	$row['comment_href'] = $row['href'];

		$row['icon_new'] = '';
		if ($board['bo_new'] && $row['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
			$row['icon_new'] = '<img src="'.$bwgs_skin_skin_url.'/img/icon_new.gif" class="title_icon" alt="새글"> ';
		
		$row['icon_hot'] = '';
		if ($board['bo_hot'] && $row['wr_hit'] >= $board['bo_hot'])
			$row['icon_hot'] = '<i class="fa fa-heart" aria-hidden="true"></i> ';

		$row['icon_secret'] = '';
		if (strstr($row['wr_option'], 'secret'))
			$row['icon_secret'] = '<i class="fa fa-lock" aria-hidden="true"></i> ';
		
		// 링크
		for ($j=1; $j<=G5_LINK_COUNT; $j++) {
			$row['link'][$j] = set_http(get_text($row["wr_link{$j}"]));
			$row['link_href'][$j] = G5_BBS_URL.'/link.php?bo_table='.$board['bo_table'].'&amp;wr_id='.$row['wr_id'].'&amp;no='.$j.$qstr;
			$row['link_hit'][$j] = (int)$row["wr_link{$j}_hit"];
		}

		// 가변 파일
		if ($board['bo_use_list_file'] || $row['wr_file']) {
			$row['file'] = get_file($board['bo_table'], $row['wr_id']);
		} else {
			$row['file']['count'] = $row['wr_file'];
		}

		if ($row['file']['count'])
        	$row['icon_file'] = '<i class="fa fa-download" aria-hidden="true"></i> ';

		$row['first_file_thumb'] = (isset($row['wr_file']) && $row['wr_file']) ? get_board_file_db($lst_table, $row['wr_id'], 'bf_file, bf_content', "and bf_type between '1' and '3'", true) : array('bf_file'=>'', 'bf_content'=>'');
		$row['bo_table'] = $lst_table;
		// 썸네일 추가
		if($thumb_width && $thumb_height) {
			$thumb = get_list_thumbnail($bo_table, $row['wr_id'], $thumb_width, $thumb_height, false, true, 'center', true, '85/3.4/15');
			// 이미지 썸네일
			if($thumb['src']) {
				$img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'">';
				$row['img_thumbnail'] = '<a href="'.$row['href'].'" class="lt_img">'.$img_content.'</a>';
			// } else {
			//     $img_content = '<img src="'. G5_IMG_URL.'/no_img.png'.'" alt="'.$thumb['alt'].'" width="'.$thumb_width.'" height="'.$thumb_height.'" class="no_img">';
			}
		}
	?>
		<li class="lt_list_li">
			<a href="<?=$lst_title_href?>&wr_id=<?=$row['wr_id']?>">
				<?php if($row['wr_subject']){ ?>
					<div class="subj">
						<?php
						if ($row['icon_secret']) echo "<i class=\"fa fa-lock\" aria-hidden=\"true\"></i><span class=\"sound_only\">비밀글</span> ";
						//if ($row['icon_hot']) echo "<span class=\"hot_icon\"><i class=\"fa fa-heart\" aria-hidden=\"true\"></i><span class=\"sound_only\">인기글</span></span>";
						if ($row['icon_new']) echo "<span class=\"new_icon\">N<span class=\"sound_only\">새글</span></span>";
						// if ($row['link']['count']) { echo "[{$row['link']['count']}]"; }
						// if ($row['file']['count']) { echo "<{$row['file']['count']}>"; }

						//echo $row['icon_reply']." ";
						//if ($row['icon_file']) echo " <i class=\"fa fa-download\" aria-hidden=\"true\"></i>" ;
						//if ($row['icon_link']) echo " <i class=\"fa fa-link\" aria-hidden=\"true\"></i>" ;
						//if ($row['comment_cnt'])  echo "<span class=\"lt_cmt\"><span class=\"sound_only\">댓글</span>".$row['comment_cnt']."</span>";
						$sub_margin = ($row['icon_secret'] || $row['icon_new']) ? " style='margin-left:5px;'" : "";
						if ($row['is_notice'])
							echo "<strong".$sub_margin.">".$row['wr_subject']."</strong>";
						else
							echo "<span".$sub_margin.">".$row['wr_subject']."</span>";
						?>
					</div>
				<?php } ?>
				<?php if($lst_add == 'id'){ ?><div class="nm"><?=$row['wr_name']?></div><?php } ?>
				<?php if($lst_add == 'date'){ ?><div class="dt"><?=$row['datetime2']?></div><?php } ?>
			</a>
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