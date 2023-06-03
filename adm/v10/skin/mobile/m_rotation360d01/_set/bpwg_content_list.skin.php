<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

//G5_BPWIDGET_ADMIN_PATH.'/bpwidget_form.php에서 아래 파일을 호출
//G5_BPWIDGET_ADMIN_PATH.'/bpwidget_form_content_list.php 에서 현재 파일을 호출
//$bpwg_content_action_url은 위의 파일에서 정의
//$bpwg_content_skin_add_url,$bpwg_content_skin_add_path도 위위에 파일에서 정의
add_stylesheet('<link rel="stylesheet" href="'.$bwg_skin_set_url.'/bpwg_content_list_style.css">', 1);
//$add_content_url = $bwg_skin_set_url.'/_ajax/bpwg_add_content.php';//내용추가시 아작스처리 파일 url
//$del_content_url = $bwg_skin_set_url.'/_ajax/bpwg_del_content.php';//내용삭제시 아작스처리 파일 url
$rows = 10;//한 페이지에 보여줄 목록갯수
$page_cnt = 10;//페이징버튼의 최대갯수

if(!$page) $page = 1; //페이지가 없음녀 첫 페이지(1페이지)
	
$from_record = ($page - 1) * $rows; //시작 열을 구한다.

$sql = " SELECT SQL_CALC_FOUND_ROWS * FROM {$g5['bpwidget_content_table']} WHERE bwgs_idx = '{$bwgs_idx}' ORDER BY bwgc_order, bwgc_idx ";
$result = sql_query($sql);
$count = sql_fetch_array(sql_query(" SELECT FOUND_ROWS() AS total "));
$total_count = $count['total'];
$total_page = ceil($total_count / $rows); //전체 페이지 계산
//이미지 수정팝에창에서의 중간 섬네일 사이즈
$thumb_m_wd = 200;
$thumb_m_ht = 120;

?>
<p style="margin-top:10px;font-size:1.1em;line-height:1.5em;">
<h3>주의 사항</h3>
(1)순서순으로 가장 작은 숫자의 <strong style="color:blue">컨텐츠 1개만 노출</strong>됩니다.<br>
(2)노출을 원하시는 컨텐츠를 <strong style="color:blue">가장 위</strong>로 올리고 <strong style="color:blue">"표시"상태</strong>로 설정한 후 <strong style="color:blue">"적용"</strong>을 해 주세요.<br>
(3)동영상/이미지 표시 갯수 그리고 각각의 용량이 크면 클수록 페이지 로딩속도에 큰 영향을 미치게 됩니다.<br>
(4)<strong style="color:blue">서버환경의 조건에 따라 동영상은 표시가 원할하지 않을 수 있습니다.</strong><br>
(5)<strong style="color:red;">이미지 용량은 최대 300KB</strong>이하로 조정해 주시길<strong style="color:green;">강력히 추천</strong> 드립니다.
</p>
<form id="bwg_content_list_box" onsubmit="return fbwgcontentlist_submit(this)" action="<?=$bpwg_content_list_action_url?>" method="post">
	<input type="hidden" name="bpwg_content_list_action_skin_path" value="<?=$bpwg_content_list_action_skin_path?>">
	<input type="hidden" name="w" value="<?=$w?>">
	<input type="hidden" name="bwgs_idx" value="<?=$bwgs_idx?>">
	<input type="hidden" name="token" value="">
	<!--############################## 내용목록 : 시작 ############################-->
	<div id="bwg_content_list">
		<div style="text-align:right;padding:5px;">
			<button type="button" class="all_chk"><img src="<?=G5_BPWIDGET_IMG_URL?>/chk_0.png"><span>전체선택</span></button>
		</div>
	<?php
	//데이터가 존재하지 않으면
	if(!$total_count){ echo '<div id="bwg_content_empty">'.strtoupper($bwgs_cd).' 위젯의 내용 데이터가 없습니다.</div>'.PHP_EOL;}
	//데이터가 존재하면 : 시작
	else{
		echo '<ul id="ul_con">'.PHP_EOL;
		$thm_wd = 100;
		$thm_ht = 60;
		for($i=0;$row=sql_fetch_array($result);$i++){//테이터목록루프 : 시작
			$atcsql = " SELECT SQL_CALC_FOUND_ROWS * FROM {$g5['bpwidget_attachment_table']}  WHERE bwga_type = 'content' AND bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$row['bwgc_idx']}' ORDER BY bwga_sort, bwga_idx ";
			$atcresult = sql_query($atcsql,1);
			$tcnt = sql_fetch_array(sql_query(" SELECT FOUND_ROWS() as total "));
			$total_tcnt = $tcnt['total'];
			$ytb_split_arr = explode('/',$row['bwgc_ytb_url']);
			
			$row['bwgc_url'] = bwg_g5_url_check($row['bwgc_url']);
			$row['bwgc_link0'] = bwg_g5_url_check($row['bwgc_link0']);
			$row['bwgc_link1'] = bwg_g5_url_check($row['bwgc_link1']);
			$row['bwgc_link2'] = bwg_g5_url_check($row['bwgc_link2']);
			$row['bwgc_link3'] = bwg_g5_url_check($row['bwgc_link3']);
			$row['bwgc_link4'] = bwg_g5_url_check($row['bwgc_link4']);
			$row['bwgc_link5'] = bwg_g5_url_check($row['bwgc_link5']);
			$row['bwgc_link6'] = bwg_g5_url_check($row['bwgc_link6']);
			$row['bwgc_link7'] = bwg_g5_url_check($row['bwgc_link7']);
			$row['bwgc_link8'] = bwg_g5_url_check($row['bwgc_link8']);
			$row['bwgc_link9'] = bwg_g5_url_check($row['bwgc_link9']);
			$row['bwgc_link10'] = bwg_g5_url_check($row['bwgc_link10']);
			$row['bwgc_link11'] = bwg_g5_url_check($row['bwgc_link11']);
			
			$row['ytb_cd'] = $ytb_split_arr[count($ytb_split_arr) - 1];
			$row['ytb_iframe'] = ($row['ytb_cd']) ? '<iframe class="ytb_iframe" width="560" height="315" src="https://www.youtube.com/embed/'.$row['ytb_cd'].'?start=1&autoplay=1&controls=0&loop=1&disblekb=1&mute=1&rel=0&playlist='.$row['ytb_cd'].'" frameborder="0"></iframe>' : '';
			$row['thumb_arr'] = array();
			$row['thumb_idxs'] = '';
			if($total_tcnt){
				for($j=0;$trow=sql_fetch_array($atcresult);$j++){
					$ar = array();
					//print_r2($trow);
					$ar['idx'] = $trow['bwga_idx'];
					$row['thumb_idxs'] .= ($j == 0) ? $ar['idx'] : ','.$ar['idx'];
					$ar['path'] = $trow['bwga_path'];
					$ar['title'] = $trow['bwga_title'];
					$ar['name'] = $trow['bwga_name'];
					$ar['width'] = $trow['bwga_width'];
					$ar['height'] = $trow['bwga_height'];
					$ar['filesize'] = $trow['bwga_filesize'];
					$ar['rank'] = $trow['bwga_rank'];
					$ar['sort'] = $trow['bwga_sort'];
					$ar['content'] = $trow['bwga_content'];
					$ar['status'] = $trow['bwga_status'];
					$ar['thumb'] = thumbnail($trow['bwga_name'],G5_PATH.$trow['bwga_path'],G5_PATH.$trow['bwga_path'],$thm_wd,$thm_ht,false,true,'center');
					$ar['thumb_url'] = G5_URL.$trow['bwga_path'].'/'.$ar['thumb'];
					$ar['thumb_m'] = thumbnail($trow['bwga_name'],G5_PATH.$trow['bwga_path'],G5_PATH.$trow['bwga_path'],$thumb_m_wd,$thumb_m_ht,false,true,'center');
					$ar['thumb_m_url'] = G5_URL.$trow['bwga_path'].'/'.$ar['thumb_m'];
					array_push($row['thumb_arr'],$ar);
				}
			}
			//print_r2($row);
		?>
			<li class="li_con">
				<div class="con">
					<div class="move_tbl"><div class="move_td"><img src="<?=G5_BPWIDGET_IMG_URL?>/move.png"></div></div>
					<div class="upcon">
						<div class="ytb">
							<div class="ytb_box">
							<?php 
							//if(false){ 
							if($row['ytb_iframe']){ 
								echo ''.$row['ytb_iframe'].'<div class="ytb_blind"></div>'.PHP_EOL;
							} else {
								echo '<div class="ytb_iframe" width="560" height="315"><div class="ytb_tbl"><div class="ytb_td">No<br>YouTube</div></div></div>'.PHP_EOL;
							}
							?>
							</div>
						</div>
						<table class="info">
							<colgroup>
								<col span="1" width="70">
								<col span="1" width="240">
								<col span="1" width="60">
								<col span="1" width="240">
								<col span="1" width="30">
								<col span="1" width="70">
							</colgroup>
							<tbody>
								<tr>
									<th>설정</th>
									<td colspan="5" style="position:relative;">
										상태 :
										<select name="bwgc_status[<?=$row['bwgc_idx']?>]" style="margin-right:10px;">
											<option value="ok" <?=(($row['bwgc_status'] == 'ok') ? 'selected="selected"':'')?>>표시</option>
											<option value="pending" <?=(($row['bwgc_status'] == 'pending') ? 'selected="selected"':'')?>>대기</option>
										</select>
										순서 : <?=$row['bwgc_order']?>
										<input type="hidden" class="text_order" name="bwgc_order[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_order']?>">
										<div class="one_btn">
											<input type="hidden" name="chk[<?=$row['bwgc_idx']?>]" value="">
											<button type="button" class="one_chk"><img src="<?=G5_BPWIDGET_IMG_URL?>/chk_0.png"><span>선택</span></button>
											<button type="button" bwgc_idx="<?=$row['bwgc_idx']?>" class="one_del" value="삭제" onclick="one_del_func(document.getElementById('bwg_content_list_box'),<?=$row['bwgc_idx']?>);"><img src="<?=G5_BPWIDGET_IMG_URL?>/delete.png"><span>삭제</span></button>
										</div>
									</td>
								</tr>
								<tr>
									<th>유튜브URL</th>
									<td>
										<input type="text" name="bwgc_ytb_url[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_ytb_url']?>">
									</td>
									<th>전체링크</th>
									<td>
										<input type="text" name="bwgc_link0[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_link0']?>">
									</td>
									<th>타겟</th>
									<td class="td_center">
										<select name="bwgc_link0_target[<?=$row['bwgc_idx']?>]">
											<option value="_self" <?=(($row['bwgc_link0_target'] == '_self') ? 'selected="selected"':'')?>>현재창</option>
											<option value="_blank" <?=(($row['bwgc_link0_target'] == '_blank') ? 'selected="selected"':'')?>>새창</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>텍스트#1</th>
									<td>
										<input type="text" name="bwgc_text1[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_text1']?>">
									</td>
									<th>링크#1</th>
									<td>
										<input type="text" name="bwgc_link1[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_link1']?>">
									</td>
									<th>타겟</th>
									<td class="td_center">
										<select name="bwgc_link1_target[<?=$row['bwgc_idx']?>]">
											<option value="_self" <?=(($row['bwgc_link1_target'] == '_self') ? 'selected="selected"':'')?>>현재창</option>
											<option value="_blank" <?=(($row['bwgc_link1_target'] == '_blank') ? 'selected="selected"':'')?>>새창</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>텍스트#2</th>
									<td>
										<input type="text" name="bwgc_text2[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_text2']?>">
									</td>
									<th>링크#2</th>
									<td>
										<input type="text" name="bwgc_link2[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_link2']?>">
									</td>
									<th>타겟</th>
									<td class="td_center">
										<select name="bwgc_link2_target[<?=$row['bwgc_idx']?>]">
											<option value="_self" <?=(($row['bwgc_link2_target'] == '_self') ? 'selected="selected"':'')?>>현재창</option>
											<option value="_blank" <?=(($row['bwgc_link2_target'] == '_blank') ? 'selected="selected"':'')?>>새창</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>텍스트#3</th>
									<td>
										<input type="text" name="bwgc_text3[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_text3']?>">
									</td>
									<th>링크#3</th>
									<td>
										<input type="text" name="bwgc_link3[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_link3']?>">
									</td>
									<th>타겟</th>
									<td class="td_center">
										<select name="bwgc_link3_target[<?=$row['bwgc_idx']?>]">
											<option value="_self" <?=(($row['bwgc_link3_target'] == '_self') ? 'selected="selected"':'')?>>현재창</option>
											<option value="_blank" <?=(($row['bwgc_link3_target'] == '_blank') ? 'selected="selected"':'')?>>새창</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>텍스트#4</th>
									<td>
										<input type="text" name="bwgc_text4[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_text4']?>">
									</td>
									<th>링크#4</th>
									<td>
										<input type="text" name="bwgc_link4[<?=$row['bwgc_idx']?>]" value="<?=$row['bwgc_link4']?>">
									</td>
									<th>타겟</th>
									<td class="td_center">
										<select name="bwgc_link4_target[<?=$row['bwgc_idx']?>]">
											<option value="_self" <?=(($row['bwgc_link4_target'] == '_self') ? 'selected="selected"':'')?>>현재창</option>
											<option value="_blank" <?=(($row['bwgc_link4_target'] == '_blank') ? 'selected="selected"':'')?>>새창</option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php if($row['bwgc_file_maxlength']){?>
					<div class="downcon">
						<div class="total_del_box">
							<div class="total_del_tbl">
								<div class="total_del_td">
									<img class="row_files_del" bwga_idxs="<?=$row['thumb_idxs']?>" width="20" height="20" src="<?=G5_BPWIDGET_IMG_URL?>/close_b.png" title="전체삭제">
								</div>
							</div>
						</div>
						<ul>
						<?php for($k=0;$k<$row['bwgc_file_maxlength'];$k++){ ?>
							<?php if($row['thumb_arr'][$k]['thumb_url']){?>
							<li>
								<img class="img_modify" bwgc_idx="<?=$row['bwgc_idx']?>" bwga_idx="<?=$row['thumb_arr'][$k]['idx']?>" thumb_m="<?=$row['thumb_arr'][$k]['thumb_m_url']?>" bwga_rank="<?=$row['thumb_arr'][$k]['rank']?>" bwga_sort="<?=$row['thumb_arr'][$k]['sort']?>" bwga_title="<?=$row['thumb_arr'][$k]['title']?>" src="<?=$row['thumb_arr'][$k]['thumb_url']?>" bwga_status="<?=$row['thumb_arr'][$k]['status']?>" bwga_content="<?=$row['thumb_arr'][$k]['content']?>" title="이미지 수정">
								<img class="img_delete" bwgc_idx="<?=$row['bwgc_idx']?>" bwga_idx="<?=$row['thumb_arr'][$k]['idx']?>" name="<?=$row['thumb_arr'][$k]['name']?>" src="<?=G5_BPWIDGET_IMG_URL?>/delete.png" title="이미지 삭제">
							</li>
							<?php }else{ ?>
							<li>
								<div class="img_add" bwgc_idx="<?=$row['bwgc_idx']?>" title="이미지 추가" width="<?=$thm_wd?>" height="<?=$thm_ht?>" style="width:<?=$thm_wd?>px;height:<?=$thm_ht?>px;line-height:<?=$thm_ht?>px;"><img src="<?=G5_BPWIDGET_IMG_URL?>/add_img.png"><span class="sound_only">이미지 추가</span></div>
							</li>
							<?php } ?>
						<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</div><!--//.con-->
			</li>
		<?php
		}//테이터목록루프 : 종료
		echo '</ul>'.PHP_EOL;
	}//데이터가 존재하면 : 종료
	?>
	<script>
	$('iframe').load(function(){
		$('.ytp-title-text').css('font-size','0');
	});
	</script>
	</div><!--//#bwg_content_list-->
	<!--############################# 내용목록 : 종료 #############################-->
	<div class="btn_fixed_top btn_confirm">
		<input type="submit" value="적용" onclick="document.pressed=this.value" class="btn bp_btn_success" accesskey="s">
		<input type="button" id="bwg_con_add" value="내용추가" class="btn bp_btn_primary">
		<a class="btn btn_02" href="<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_list.php?<?php echo $qstr ?>">목록</a>
		<?php if($total_count){ ?>
		<input type="submit" id="bwg_con_del" value="선택삭제" onclick="document.pressed=this.value" idx="" class="btn bp_btn_danger">
		<?php } ?>
	</div>
	<?php //echo get_paging($page_cnt, $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page=$page&amp;bwg_con=1"); ?>
</form><!--//#bwg_content_list_box-->
<?php 
if(is_file($bwg_skin_set_path.'/bpwg_add_content.skin.php')){
	//#bcontform_modal 객체는 아래 파일안에 작성되어 있다.(스킨마다 다른형식의 모달일것이므로 공통 모달스킨에 넣지 않았다.)
	include_once($bwg_skin_set_path.'/bpwg_add_content.skin.php');
?>
<script>
var add_content_url = '<?=$add_content_url?>';
var del_content_url = '<?=$del_content_url?>';
var bwgs_idx = <?=(($bwgs_idx)?$bwgs_idx:0)?>;
$(function(){	
	//파일업로드
	if($('input[type="file"]').length){
		$('input[type="file"]').each(function(){
			$(this).MultiFile({
				onFileSelect : function(element,value,master_element){
					//console.log('onFileSelect');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','block');
				},
				onFileAppend : function(element,value,master_element){
					//console.log('onFileAppend');
				},
				afterFileAppend : function(element,value,master_element){
					//console.log('afterFileAppend');
				},
				afterFileSelect : function(element,value,master_element){
					//console.log('afterFileSelect');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','none');
				},
				onFileRemove : function(element,value,master_element){
					//console.log('onFileRemove');
				},
				afterFileRemove : function(element,value,master_element){
					//console.log('afterFileRemove');
				},
				onFileInvalid : function(element,value,master_element){
					//console.log('onFileInvalid');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','none');
				},
				onFileDuplicate : function(element,value,master_element){
					//console.log('onFileDuplicate');
				},
				onFileTooMany : function(element,value,master_element){
					//console.log('onFileTooMany');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','none');
				},
				onFileTooBig : function(element,value,master_element){
					//console.log('onFileTooBig');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','none');
				},
				onFileTooMuch : function(element,value,master_element){
					//console.log('onFileTooMuch');
					$('#bcontform_modal #bcontform_box #bcontf_loading').css('display','none');
				},
			});
		});
	}
	bwg_cont_event_on();

	//목록순서 정렬 이벤트
	$('#bwg_content_list_box #ul_con').sortable({
		update:function(event,ui){
			var n = 0;
			$('input[name^="bwgc_order["]').each(function(){
				n++;
				$(this).val(n);
			});
		}
	});
});
//이벤트 활성화 함수
function bwg_cont_event_on(){
	
	$('#bwg_con_add').on('click',function(){
		$('#bcontform_modal').css('display','table');
	});
	
	$('#bcont_form_bg,#add_modal_close').on('click',function(){
		bwg_addcont_modal_reset();
		$('#bcontform_modal').css('display','none');
	});
	
	$('#bwg_content_list_box .all_chk').on('click',function(){
		if($(this).find('img[src*="chk_0.png"]').length){ //체크하기
			$(this).find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_1.png');
			$('#bwg_content_list_box .one_chk').find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_1.png');
			$('#bwg_content_list_box input[name^="chk["]').val('1');
		}else{ //해제하기
			$(this).find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_0.png');
			$('#bwg_content_list_box .one_chk').find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_0.png');
			$('#bwg_content_list_box input[name^="chk["]').val('');
		}
	});
	
	$('#bwg_content_list_box .one_chk').on('click',function(){
		if($(this).find('img[src*="chk_0.png"]').length){ //체크하기
			$(this).find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_1.png').parent().siblings('input[name^="chk["]').val('1');
		}else{ //해제하기
			$(this).find('img').attr('src','<?=G5_BPWIDGET_IMG_URL?>/chk_0.png').parent().siblings('input[name^="chk["]').val('');
		}
	});
	
	//첨부파일 개별삭제 처리
	$('.img_delete').on('click',function(){
		var bwga_idx = $(this).attr('bwga_idx');
		if(bwga_idx) file_single_del(bwga_idx);
		else alert('해당파일의 식별idx코드가 없습니다.');
	});
	//한줄의 첨부파일 전부삭제 처리
	$('.row_files_del').on('click',function(){
		var bwga_idxs = $(this).attr('bwga_idxs');
		if(bwga_idxs) files_row_del(bwga_idxs);
		else alert('해당줄 파일들의 식별idx코드가 없습니다.');
	});
	
	//이미지 수정 버튼 
	$('.img_modify').on('click',function(){
		var thumb_m = $('<img src="'+$(this).attr('thumb_m')+'" width="<?=$thumb_m_wd?>" height="<?=$thumb_m_ht?>">');
		var cur_img = $('#img_change_modal #cur_img');
		$('#img_change_modal').show();
		cur_img.find('img').remove();
		$(thumb_m).prependTo(cur_img);
		//alert($('#img_change_modal input[name="bwgs_idx"]').length);return;
		$('#img_change_modal input[name="bwgs_idx"]').val(bwgs_idx);
		$('#img_change_modal input[name="bwgc_idx"]').val($(this).attr('bwgc_idx'));
		$('#img_change_modal input[name="bwga_idx"]').val($(this).attr('bwga_idx'));
		$('#img_change_modal input[name="bwga_title"]').val($(this).attr('bwga_title'));
		$('#img_change_modal input[name="bwga_rank"]').val($(this).attr('bwga_rank'));
		$('#img_change_modal input[name="bwga_sort"]').val($(this).attr('bwga_sort'));
		$('#img_change_modal select[name="bwga_status"] option[value="'+$(this).attr('bwga_status')+'"]').attr('selected',true).siblings('option').attr('selected',false);
		$('#img_change_modal textarea[name="bwga_content"]').val($(this).attr('bwga_content'));
	});
	
	//이미지 변경 모달 닫기
	$('#img_change_bg, #img_change_modal_close, .img_change_close').on('click',function(){
		if($('#img_change_modal .MultiFile-remove').length) $('#img_change_modal .MultiFile-remove').trigger('click');
		$('#img_change_modal input[name="bwgs_idx"]').val('');
		$('#img_change_modal input[name="bwgc_idx"]').val('');
		$('#img_change_modal input[name="bwga_idx"]').val('');
		$('#img_change_modal input[name="bwga_title"]').val('');
		$('#img_change_modal input[name="bwga_rank"]').val('');
		$('#img_change_modal input[name="bwga_sort"]').val('');
		$('#img_change_modal select[name="bwga_sort"] option[value="ok"]').attr('selected',true).siblings('option').attr('selected',false);
		$('#img_change_modal textarea[name="bwga_content"]').val('');
		$('#img_change_modal').hide();
	});
	
	//컨텐츠 이미지 추가 버튼 
	$('.img_add').on('click',function(){
		$('#confile_reg_modal').show();
		$('#confile_reg_modal input[name="bwgs_idx"]').val(bwgs_idx);
		$('#confile_reg_modal input[name="bwgc_idx"]').val($(this).attr('bwgc_idx'));
	});
	
	//컨텐츠 이미지 추가 모달 닫기
	$('#confile_reg_bg, #confile_reg_modal_close, .confile_reg_close').on('click',function(){
		if($('#confile_reg_modal .MultiFile-remove').length) $('#confile_reg_modal .MultiFile-remove').trigger('click');
		$('#confile_reg_modal input[name="bwgs_idx"]').val('');
		$('#confile_reg_modal input[name="bwgc_idx"]').val('');
		$('#confile_reg_modal input[name="bwga_title"]').val('');
		$('#confile_reg_modal input[name="bwga_rank"]').val('');
		$('#confile_reg_modal input[name="bwga_sort"]').val('');
		$('#confile_reg_modal select[name="bwga_sort"] option[value="ok"]').attr('selected',true).siblings('option').attr('selected',false);
		$('#confile_reg_modal textarea[name="bwga_content"]').val('');
		$('#confile_reg_modal').hide();
	});
	
}

function bwg_addcont_modal_reset(){
	$('#bcontform_modal').find('input[name="bwgc_order"]').val('');
	$('#bcontform_modal').find('input[name="bwgc_ytb_url"]').val('');
	$('#bcontform_modal').find('input[name^="bwgc_text"]').val('');
	$('#bcontform_modal').find('input[name^="bwgc_link"]').val('');
	$('#bcontform_modal').find('select[name="bwgc_status"]').find('option[value="ok"]').attr('selected',true);
	$('#bcontform_modal').find('select[name$="_target"]').find('option[value="_self"]').attr('selected',true);
	$('#bcontform_modal').find('input[type="file"]').siblings('.MultiFile-list').empty();
}

//이벤트 비활성화 함수
function bwg_cont_event_off(){
	$('#bwg_con_add').off('click');
	$('#bcont_form_bg,#add_modal_close').off('click');
	$('#bwg_content_list_box .all_chk').off('click');
	$('#bwg_content_list_box .one_chk').off('click');
	$('.img_modify').off('click');
	$('.img_delete').off('click');
	$('.img_add').off('click');
	$('#img_change_bg, #img_change_modal_close, .img_change_close').off('click');
}

function fbwgcontentlist_submit(f)
{
	
	//alert(document.pressed);
	//return false;
	
	var apply_flag = true;
    //f.action = "<?=G5_BPWIDGET_ADMIN_URL?>/bpwidget_form_update.php";
    if(document.pressed == "선택삭제") {
		if (!is_checked_bwg("chk")) {
			alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
			apply_flag = false;
			return false;
		}

        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			apply_flag = false;
            return false;
        }
		f.w.value = 'd';
    }else if(document.pressed == "적용"){
		var youtube_format = /^https\:\/\/youtu\.be\/[0-9a-zA-Z\_\-]{11}$/;
		var format_url = /^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i;
		var format_tel_sms = /^(tel|sms)\:[0-9]{2,3}\-[0-9]{3,4}\-[0-9]{4}/gm;
		var format_email = /^(mailto)\:[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
		var format_inner = /^(\#)[0-9a-zA-Z\_\-]*$/i;
		
		$('#bwg_content_list .li_con').each(function(){
			var file_length = $(this).find('.img_modify').length;
			var youtube_link = $(this).find('input[name^="bwgc_ytb_url["]').val();
			var link0 = $(this).find('input[name^="bwgc_link0["]').val();
			var link1 = $(this).find('input[name^="bwgc_link1["]').val();
			var link2 = $(this).find('input[name^="bwgc_link2["]').val();
			var link3 = $(this).find('input[name^="bwgc_link3["]').val();
			var link4 = $(this).find('input[name^="bwgc_link4["]').val();
			
			//https://youtu.be/VpNah-3SARM
			//유튜브,파일찾기1,파일찾기2 중에 한 개는 반드시 입력해 주세요.
			if(!youtube_link && !file_length){
				$(this).find('input[name^="bwgc_ytb_url["]').focus();
				alert('각 컨텐츠 마다 [유튜브URL] 또는 [이미지추가] 둘 중에 적어도 하나는 반드시 등록해야 합니다.');
				apply_flag = false;
				return false;
			}
			
			//https://youtu.be/Wop6B-HgTEg
			if(youtube_link && !youtube_link.match(youtube_format)){
				$(this).find('input[name^="bwgc_ytb_url["]').val('').focus();
				alert('올바른 유튜브URL형식이 아닙니다. 다시 확인해서 입력해 주세요.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_ytb_url["]').val($.trim(youtube_link));
			}
			
			if(link0 && !link0.match(format_url) && !link0.match(format_tel_sms) && !link0.match(format_email) && !link0.match(format_inner)){
				$(this).find('input[name^="bwgc_link0["]').val('').focus();
				alert('전체링크의 데이터가 올바른 링크형식이 아닙니다.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_link0["]').val($.trim(link0));
			}
			
			if(link1 && !link1.match(format_url) && !link1.match(format_tel_sms) && !link1.match(format_email) && !link1.match(format_inner)){
				$(this).find('input[name^="bwgc_link1["]').val('').focus();
				alert('링크#1의 데이터가 올바른 링크형식이 아닙니다.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_link1["]').val($.trim(link1));
			}
			
			if(link2 && !link2.match(format_url) && !link2.match(format_tel_sms) && !link2.match(format_email) && !link2.match(format_inner)){
				$(this).find('input[name^="bwgc_link2["]').val('').focus();
				alert('링크#2의 데이터가 올바른 링크형식이 아닙니다.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_link2["]').val($.trim(link2));
			}
			
			if(link3 && !link3.match(format_url) && !link3.match(format_tel_sms) && !link3.match(format_email) && !link3.match(format_inner)){
				$(this).find('input[name^="bwgc_link3["]').val('').focus();
				alert('링크#3의 데이터가 올바른 링크형식이 아닙니다.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_link3["]').val($.trim(link3));
			}
			
			if(link4 && !link4.match(format_url) && !link4.match(format_tel_sms) && !link4.match(format_email) && !link4.match(format_inner)){
				$(this).find('input[name^="bwgc_link4["]').val('').focus();
				alert('링크#4의 데이터가 올바른 링크형식이 아닙니다.');
				apply_flag = false;
				return false;
			}else{
				$(this).find('input[name^="bwgc_link4["]').val($.trim(link4));
			}
		});
		
		f.w.value = 'u';
	}
	
    return apply_flag;
}

function one_del_func(f,idx){
	
	if(!confirm("해당 내용을 정말 삭제하시겠습니까?")) {
		return false;
	}
	
	$('#bwg_content_list_box input[name="chk['+idx+']"]').val('1');
	f.w.value = 'd';
	f.submit();
	//return false;
}

function confilereg_check(f){
	if(!$('#confile_reg_modal .MultiFile-list .MultiFile-label').length){
		alert('등록하실 이미지를 선택해 주세요.');
		$('#confile_reg_modal input[type="file"]').focus();
		return false;
	}
	return true;
}

/*
//내용스킨 추가 함수
function bwg_conskin_add(){
	$.ajax({
		type : "POST",
		url : add_content_url,
		dataType : 'html',
		success:function(res){
			
		},
		error : function(xmlReq){
			alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
		}
	});
}
*/
</script>
<?php
}//if(is_file($bwg_skin_set_path.'/bpwg_add_content.skin.php'))
?>