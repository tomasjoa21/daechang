<?php
$maxlength = 1;
$accept = 'png|jpg|jpeg|gif|svg';
$data_maxfile = $g5['bpwidget']['bwgf_filesize'];
?>
<div id="bcontform_modal">
	<div id="bcontform_box">
		<div id="bcont_form_bg"></div>
		<div id="bcontf_loading"><div id="bcontf_loading_in" style="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/loading.gif);"></div></div>
		<form id="bcont_form" action="<?=$bwg_skin_set_url?>/bpwg_add_content_update.skin.php" onsubmit="return fbcontform_submit(this)" method="post" enctype="multipart/form-data">
			<input type="hidden" name="bwgs_idx" value="<?=$bwgs_idx?>">
			<input type="hidden" name="bwgc_idx" value="">
			<input type="hidden" name="bwgc_file_maxlength" value="<?=$maxlength?>">
			<input type="hidden" name="w" value="">
			<img id="add_modal_close" width="30" height="30" src="<?=G5_BPWIDGET_IMG_URL?>/close.png">
			<h4><?=strtoupper($bwgs_cd)?> 위젯내용<span></span></h4>
			<div id="bcont_info">
				<?php if(false){?>
				<div id="bc_media_box" class="bc_sec">
					<div id="bc_ytb" class="bc_med" style="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_ytb.png);" default_bg="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_ytb.png);"><span>유튜브</span><div id="bc_ytb_in"></div></div>
					<div id="bc_img1" class="bc_med" style="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_thumb.png);" default_bg="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_thumb.png);"><span>이미지#1</span></div>
					<div id="bc_img2" class="bc_med" style="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_thumb.png);" default_bg="background-image:url(<?=G5_BPWIDGET_IMG_URL?>/no_thumb.png);"><span>이미지#2</span></div>
				</div>
				<?php } ?>
				<table id="bc_data" class="bc_sec">
					<tbody>
						<tr>
							<th>유튜브URL</th>
							<td class="bwg_help">
								<?php echo bwg_help("유튜브동영상페이지에서 [공유] > 팝업창에 표시된 [URL]을 복사해서 붙여넣기 하세요. 절대 [소스퍼가기]의 iframe소스를 넣지 마세요.<br><span style='color:blue;'>[OK] https://youtu.be/Wop6B-HgTEg</span><br><span style='color:red;'>[NO]&lt;iframe ~~~&gt;&lt;/iframe&gt;</span>",1,'#555555','#eeeeee'); ?>
								<input type="text" name="bwgc_ytb_url" class="bp_wdp100" value="">
							</td>
							<th>전체링크</th>
							<td class="bwg_help">
								<?php echo bwg_help("전체링크에 값이 있으면 개별링크는 반영되지 않습니다.",1,'#555555','#eeeeee'); ?>
								<input type="text" name="bwgc_link0" class="bp_wdp80" value="">
								<select name="bwgc_link0_target">
									<option value="_self">현재창</option>
									<option value="_blank">새창</option>
								</select>
							</td>
						</tr>
						<?php for($i=1;$i<=4;$i++){?>
						<tr>
							<th>텍스트#<?=$i?></th>
							<td><input type="text" name="bwgc_text<?=$i?>" class="bp_wdp100" value=""></td>
							<th>링크#<?=$i?></th>
							<td>
								<input type="text" name="bwgc_link<?=$i?>" class="bp_wdp80" value="">
								<select name="bwgc_link<?=$i?>_target">
									<option value="_self">현재창</option>
									<option value="_blank">새창</option>
								</select>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<th><span class="img_upload bp_bgsecondary">파일찾기</span></th>
							<td class="td_file" colspan="3"><input type="file" name="bwcfile[]" multiple class="with-preview" maxlength="<?=$maxlength?>" accept="<?=$accept?>" data-maxfile="<?=$data_maxfile?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="btn_box">
				순서 : <input type="text" name="bwgc_order" size="3" style="text-align:right;height:30px;line-height:30px;">
				<select id="status_select" name="bwgc_status">
					<option value="ok">표시</option>
					<option value="pending">비표시</option>
				</select>
				<input type="submit" value="적용" class="btn bp_btn_primary">
			</div>
		</form>
	</div>
</div>
<script>
function fbcontform_submit(f){
	var bwgc_order = f.bwgc_order.value;
	var youtube_link = f.bwgc_ytb_url.value;
	var link0 = f.bwgc_link0.value;
	var link1 = f.bwgc_link1.value;
	var link2 = f.bwgc_link2.value;
	var link3 = f.bwgc_link3.value;
	var link4 = f.bwgc_link4.value;

	//var format = /^((http(s?))\:\/\/)([0-9a-zA-Z\-]+\.)+[a-zA-Z]{2,6}(\:[0-9]+)?(\/\S*)?$/;
	var format_order = /[0-9]{1,2}/;
	var youtube_format = /^https\:\/\/youtu\.be\/[0-9a-zA-Z\_\-]{11}$/;
	var format_url = /^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i;
	var format_tel_sms = /^(tel|sms)\:[0-9]{2,3}\-[0-9]{3,4}\-[0-9]{4}/gm;
	var format_email = /^(mailto)\:[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
	var format_inner = /^(\#)[0-9a-zA-Z\_\-]*$/i;
	var file_length = $('#bcontform_modal input[type="file"]').siblings('.MultiFile-list').find('.MultiFile-label').length;
	
	//순서에 입력값이 없으면
	if(!bwgc_order){
		$('input[name="bwgc_order"]').val('0');
	}else{ //순서에 입력값이 있으면
		if(!bwgc_order.match(format_order) || bwgc_order.length > 2){
			alert('순서 입력란에는 반드시 2자리 이하의 숫자만 입력 가능합니다.');
			$('input[name="bwgc_order"]').val('').focus();
			return false;
		}
	}
	
	//유튜브,파일찾기1,파일찾기2 중에 한 개는 반드시 입력해 주세요.
	if(!f.bwgc_ytb_url.value && !file_length){
		alert('[유튜브URL] 또는 [파일찾기] 둘 중에 하나는 반드시 입력해 주세요.');
		$('input[name="bwgc_ytb_url"]').focus();
		return false;
	}
	//https://youtu.be/Wop6B-HgTEg
	if(f.bwgc_ytb_url.value && !youtube_link.match(youtube_format)){
		alert('올바른 유튜브URL형식이 아닙니다. 다시 확인해서 입력해 주세요.');
		$('input[name="bwgc_ytb_url"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_ytb_url"]').val($.trim(youtube_link));
	}
	
	if(f.bwgc_link0.value && !link0.match(format_url) && !link0.match(format_tel_sms) && !link0.match(format_email) && !link0.match(format_inner)){
		alert('전체링크의 데이터가 올바른 링크형식이 아닙니다.');
		$('input[name="bwgc_link0"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_link0"]').val($.trim(link0));
	}
	
	if(f.bwgc_link1.value && !link1.match(format_url) && !link1.match(format_tel_sms) && !link1.match(format_email) && !link1.match(format_inner)){
		alert('링크#1의 데이터가 올바른 링크형식이 아닙니다.');
		$('input[name="bwgc_link1"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_link1"]').val($.trim(link1));
	}
	
	if(f.bwgc_link2.value && !link2.match(format_url) && !link2.match(format_tel_sms) && !link2.match(format_email) && !link2.match(format_inner)){
		alert('링크#2의 데이터가 올바른 링크형식이 아닙니다.');
		$('input[name="bwgc_link2"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_link2"]').val($.trim(link2));
	}
	
	if(f.bwgc_link3.value && !link3.match(format_url) && !link3.match(format_tel_sms) && !link3.match(format_email) && !link3.match(format_inner)){
		alert('링크#3의 데이터가 올바른 링크형식이 아닙니다.');
		$('input[name="bwgc_link3"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_link3"]').val($.trim(link3));
	}
	
	if(f.bwgc_link4.value && !link4.match(format_url) && !link4.match(format_tel_sms) && !link4.match(format_email) && !link4.match(format_inner)){
		alert('링크#4의 데이터가 올바른 링크형식이 아닙니다.');
		$('input[name="bwgc_link4"]').val('').focus();
		return false;
	}else{
		$('input[name="bwgc_link4"]').val($.trim(link4));
	}
	
	return true;
}
</script>