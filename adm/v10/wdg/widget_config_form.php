<?php
include_once('./_wdg_top.php');
$sub_menu = $wdg_sub_menu2;//"910200";
include_once('./_common.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
// print_r2($auth);exit;
auth_check($auth[$sub_menu], 'r');

//환경설정 wgf_name값을 일반변수로 변경 예)$g5['wdg']['wgf_country'] => $wgf_country
foreach($g5['wdg'] as $key => $val) $$key = $val;

// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
//$check_array=array('wgf_community_use','wgf_shop_use','wgf_only_mobile','wgf_device','wgf_site_type','wgf_community_use','wgf_use_mobile');
$check_array=array();
//print_r2($g5['wdg']);
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.$g5['wdg'][$check_array[$i]]} = ' checked';
}

//########### 환경설정 관련 파일 : 시작{ ####################
$thumb_wd = 200;
$thumb_ht = 150;
$grpsql = " SELECT wga_array FROM {$g5['wdg_file_table']}  WHERE wga_type = 'config' GROUP BY wga_array ";
$grp_result = sql_query($grpsql,1);
$conffile_group = array();
//어떤종류의 파일배열이 있는지 총 종류를 뽑아내는 루프
for($i=0;$grow=sql_fetch_array($grp_result);$i++){
	array_push($conffile_group,$grow['wga_array']);
	${$grow['wga_array']} = array();
	${$grow['wga_array'].'_idx'} = 0;
}
//해당 위젯idx(bwgs_idx)의 option에 해당하는 파일 레코드를 전부 추출
$confsql = " SELECT * FROM {$g5['wdg_file_table']} WHERE wga_type = 'config' ";
$conf_result = sql_query($confsql,1);
for($i=0;$frow=sql_fetch_array($conf_result);$i++){
	$type_arr = explode('/',$frow['wga_mime_type']);
	$frow['type'] = $type_arr[0];//image or text
	$frow['file_path'] = G5_PATH.$frow['wga_path'].'/'.$frow['wga_name'];
	$frow['file_url'] = G5_URL.$frow['wga_path'].'/'.$frow['wga_name'];
	$frow['thumb_url'] = '';
	//등록 이미지 섬네일 생성
	if($frow['type'] == 'image'){
		$thumbf = thumbnail($frow['wga_name'],G5_PATH.$frow['wga_path'],G5_PATH.$frow['wga_path'],$thumb_wd,$thumb_ht,false,false,'center');
		$thumbf_url = G5_URL.$frow['wga_path'].'/'.$thumbf;
		$frow['thumb_url'] = $thumbf_url;
	}
	//상단에 파일배열 종류에 해당하는 배열에 분류되어 파일레코드 요소를 담는다.
	//array_push(${$frow['wga_array']},$frow);
	foreach($frow as $k=>$v)
		${$frow['wga_array']}[$k] = $v;
	${$frow['wga_array'].'_idx'} = $frow['wga_idx'];
}
//########### 환경설정 관련 파일 : 종료} ####################

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_wdg_info">사이트기본정보</a></li>
    <li><a href="#anc_wdg_basic">기본 설정</a></li>
    <li><a href="#anc_wdg_opengraph">오픈그래프</a></li>
    <li><a href="#anc_wdg_webmaster">웹마스터</a></li>
</ul>';
    //<li><a href="#anc_wdg_dataset">데이터 설정</a></li>

// 확인 및 메인으로 버튼 정의">
$frm_submit = '<div class="btn_fixed_top btn_confirm">
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
    <a href="'.G5_URL.'/" class="btn btn_02">메인으로</a>
</div>';

$g5['title'] = '위젯 환경설정';
include_once('../_head.php');

$colspan11=11;
$colspan10=10;
$colspan9=9;
$colspan8=8;
$colspan7=7;
$colspan6=6;
$colspan5=5;
$colspan4=4;
$colspan3=3;
$colspan2=2;

$conf_edit_flag = 1; //편집수정 필요시 값을 1로 변경
$readonly = (!$conf_edit_flag) ? " readonly" : "";
?>
<style>
.img_tr td{background:#1e2531;}
</style>
<div id="wdg_frm" class="wdg_frm">
<form name="fbwgf" id="fbwgf" method="post" onsubmit="return fwgf_submit(this);" enctype="multipart/form-data">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">
<section id="anc_wdg_info">
	<h2 class="h2_frm">사이트기본정보</h2>
    <?php echo $pg_anchor; ?>
	<table class="tbl_frm">
		<colgroup>
			<col span="1" width="70">
			<col span="1" width="110">
			<col span="1" width="70">
			<col span="1" width="110">
			<col span="1" width="70">
			<col span="1" width="110">
			<col span="1" width="70">
			<col span="1" width="110">
			<col span="1" width="70">
			<col span="1" width="110">
			<col span="1" width="70">
			<col span="1" width="110">
		</colgroup>
		<tbody>
			<tr>
				<th>회사명</th>
				<td class="wdg_help">
					<input type="text" name="wgf_company_name" class="wg_wdp100" value="<?=$wgf_company_name?>">
				</td>
				<th>대표전화</th>
				<td class="wdg_help">
					<input type="text" name="wgf_main_tel" class="wg_wdp100" value="<?=$wgf_main_tel?>">
				</td>
				<th>CS전화</th>
				<td class="wdg_help">
					<input type="text" name="wgf_cs_tel" class="wg_wdp100" value="<?=$wgf_cs_tel?>">
				</td>
				<th>평일업무</th>
				<td class="wdg_help">
					<input type="text" name="wgf_weekday_time" class="wg_wdp100" value="<?=$wgf_weekday_time?>">
				</td>
				<th>점심시간</th>
				<td class="wdg_help">
					<input type="text" name="wgf_lunch_time" class="wg_wdp100" value="<?=$wgf_lunch_time?>">
				</td>
				<th>주말업무</th>
				<td class="wdg_help">
					<input type="text" name="wgf_weekend_time" class="wg_wdp100" value="<?=$wgf_weekend_time?>">
				</td>
			</tr>
			<tr>
				<th>대표자명</th>
				<td class="wdg_help">
					<input type="text" name="wgf_ceo_name" class="wg_wdp100" value="<?=$wgf_ceo_name?>">
				</td>
				<th>사업자번호</th>
				<td class="wdg_help">
					<input type="text" name="wgf_business_no" class="wg_wdp100" value="<?=$wgf_business_no?>">
				</td>
				<th>전화번호</th>
				<td class="wdg_help">
					<input type="text" name="wgf_company_tel" class="wg_wdp100" value="<?=$wgf_company_tel?>">
				</td>
				<th>팩스번호</th>
				<td class="wdg_help">
					<input type="text" name="wgf_company_fax" class="wg_wdp100" value="<?=$wgf_company_fax?>">
				</td>
				<th>사업장주소</th>
				<td colspan="<?=$colspan3?>" class="wdg_help">
					<input type="text" name="wgf_company_addr" class="wg_wdp100" value="<?=$wgf_company_addr?>">
				</td>
			</tr>
			<tr>
				<th>저작권문장</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="text" name="wgf_company_copyright" class="wg_wdp100" value="<?=$wgf_company_copyright?>">
				</td>
				<th>추가문장</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="text" name="wgf_company_addtext" class="wg_wdp100" value="<?=$wgf_company_addtext?>">
				</td>
			</tr>
			<tr class="img_tr">
				<th scope="row">로고1</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo1]" value="<?=$logo1_idx?>">
					<input type="file" name="logo1" class="">
					<label for="del_idx[logo1]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo1]" name="del_idx[logo1]" value="<?=$logo1_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo1['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo1['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
				<th scope="row">로고2</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo2]" value="<?=$logo2_idx?>">
					<input type="file" name="logo2" class="">
					<label for="del_idx[logo2]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo2]" name="del_idx[logo2]" value="<?=$logo2_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo2['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo2['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
			</tr>
			<tr class="img_tr">
				<th scope="row">로고3</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo3]" value="<?=$logo3_idx?>">
					<input type="file" name="logo3" class="">
					<label for="del_idx[logo3]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo3]" name="del_idx[logo3]" value="<?=$logo3_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo3['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo3['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
				<th scope="row">로고4</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo4]" value="<?=$logo4_idx?>">
					<input type="file" name="logo4" class="">
					<label for="del_idx[logo4]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo4]" name="del_idx[logo4]" value="<?=$logo4_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo4['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo4['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
			</tr>
			<tr class="img_tr">
				<th scope="row">로고5</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo5]" value="<?=$logo5_idx?>">
					<input type="file" name="logo5" class="">
					<label for="del_idx[logo5]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo5]" name="del_idx[logo5]" value="<?=$logo5_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo5['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo5['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
				<th scope="row">로고6</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo6]" value="<?=$logo6_idx?>">
					<input type="file" name="logo6" class="">
					<label for="del_idx[logo6]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo6]" name="del_idx[logo6]" value="<?=$logo6_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo6['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo6['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
			</tr>
			<tr class="img_tr">
				<th scope="row">로고7</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo7]" value="<?=$logo7_idx?>">
					<input type="file" name="logo7" class="">
					<label for="del_idx[logo7]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo7]" name="del_idx[logo7]" value="<?=$logo7_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo7['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo7['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
				<th scope="row">로고8</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<input type="hidden" name="fle_idx[logo8]" value="<?=$logo8_idx?>">
					<input type="file" name="logo8" class="">
					<label for="del_idx[logo8]" class="label_checkbox">
						<input type="checkbox" id="del_idx[logo8]" name="del_idx[logo8]" value="<?=$logo8_idx?>">
						<strong></strong>
						<span>삭제</span>
					</label>
					<?php if($logo8['thumb_url']){ ?>
					<div style="margin-top:5px;">
					<img src="<?=$logo8['thumb_url']?>">
					</div>
					<?php } ?>
				</td>
			<tr>
		</tbody>
	</table>
</section><!--#anc_wdg_info-->
<section id="anc_wdg_basic">
	<h2 class="h2_frm">위젯 환경설정</h2>
    <?php echo $pg_anchor; ?>
	<table class="tbl_frm">
		<colgroup>
			<col span="1" width="110">
			<col span="1" width="250">
			<col span="1" width="110">
			<col span="1" width="250">
			<col span="1" width="110">
			<col span="1" width="250">
		</colgroup>
		<tbody>
			<tr>
				<th>위젯목록<br>메뉴번호</th>
				<td class="wdg_help">
					<?php echo wdg_help("여섯자리 숫자 입력. 값이 없으면 기본값은 '910100'으로 입력됩니다.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="wgf_sub_menu"<?=$readonly?> class="wg_wdp100<?=$readonly?>" value="<?=$wdg_sub_menu?>">
				</td>
				<th>위젯환경설정<br>메뉴번호</th>
				<td class="wdg_help">
					<?php echo wdg_help("여섯자리 숫자 입력. 값이 없으면 기본값 '910200'으로 입력됩니다.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="wgf_sub_menu2"<?=$readonly?> class="wg_wdp100<?=$readonly?>" value="<?=$wdg_sub_menu2?>">
				</td>
				<th>위젯캐시<br>저장시간</th>
				<td class="wdg_help">
					<!--
					$cache_time은 시간단위 
					1시간=1, 5초=0.00139, 10초=0.0028, 20초=0.0056, 30초=0.0084, 40초=0.012, 50초=0.0139, 60초=0.0167, 3600초=1시간
					-->
					<?php echo wdg_help("캐시 저장시간의 값이 작을수록 위젯 수정후 반영되는 시간이 짧아집니다.",1,'#555555','#eeeeee'); ?>
					<?php echo wdg_select_selected($wgf_cachetime, 'wgf_cache_time', $wgf_cache_time, 0,0,0);//인수('pending=대기,ok=정상,hide=숨김,trash=삭제','wgf_status(name속성)','ok(값)',0(값없음활성화),1(필수여부)) ?>
				</td>
			</tr>
			<tr>
				<th>위젯스킨목록<br>메뉴번호</th>
				<td colspan="<?=$colspan5?>" class="wdg_help">
					<?php echo wdg_help("여섯자리 숫자 입력. 값이 없으면 기본값은 '910100'으로 입력됩니다.",1,'#555555','#eeeeee'); ?>
					<input type="text" name="wgf_sub_menu3"<?=$readonly?> class="wg_wdx315<?=$readonly?>" value="<?=$wdg_sub_menu3?>">
				</td>
			</tr>
			<tr>
				<th>개별업로드<br>파일용량</th>
				<td class="wdg_help">
					<?php echo wdg_help("(예 : 300)개별업로드 용량이 크면 페이지로딩에 영향을 줍니다.",1,'#555555','#eeeeee'); ?>
					최대 <input type="text" name="wgf_filesize" class="wg_wdp40" value="<?=$wgf_filesize?>" style="text-align:right;">&nbsp;KB 까지
				</td>
				<th>업로드하는<br>멀티파일 총용량</th>
				<td class="wdg_help">
					<?php echo wdg_help("(예 : 3000)멀티파일 총용량이 크면 페이지로딩에 영향을 줍니다.",1,'#555555','#eeeeee'); ?>
					최대 <input type="text" name="wgf_total_filesize" class="wg_wdp40" value="<?=$wgf_total_filesize?>" style="text-align:right;">&nbsp;KB 까지
				</td>
				<th>PC기본색상</th>
				<td class="wdg_help">
					<?php echo wdg_help("PC버전에서 사이트 전체 기본 배경/폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<ul class="ul_pc_basic_color">
						<li>
						배경<br>
						<?php echo wdg_input_color('wgf_default_bg',$wgf_default_bg,$w); ?>
						</li>
						<li>
						폰트<br>
						<?php echo wdg_input_color('wgf_default_font',$wgf_default_font,$w); ?>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<th>모바일 기본색상</th>
				<td class="wdg_help">
					<?php echo wdg_help("모바일 버전에서 사이트 전체 기본 배경/폰트 색상을 설정하세요.",1,'#555555','#eeeeee'); ?>
					<ul class="ul_pc_basic_color">
						<li>
						배경<br>
						<?php echo wdg_input_color('wgf_mo_default_bg',$wgf_mo_default_bg,$w); ?>
						</li>
						<li>
						폰트<br>
						<?php echo wdg_input_color('wgf_mo_default_font',$wgf_mo_default_font,$w); ?>
						</li>
					</ul>
				</td>
				<th>기본선형<br>그라데이션색상</th>
				<td colspan="<?=$colspan3?>" class="wdg_help">
					<?php echo wdg_help("사이트 전체 기본 선형 그라데이션 색상을 설정하세요.(주로 로그인,비번확인,비번찾기 페이지에서 사용됨.)",1,'#555555','#eeeeee'); ?>
					<ul class="ul_pc_basic_color">
						<li>
						From 색상<br>
						<?php echo wdg_input_color('wgf_gradient_from',$wgf_gradient_from,$w); ?>
						</li>
						<li>
						To 색상<br>
						<?php echo wdg_input_color('wgf_gradient_to',$wgf_gradient_to,$w); ?>
						</li>
					</ul>
				</td>
			</tr>
			<tr>
				<th>캐시시간</th>
				<td class="wdg_help">
					<?php echo wdg_help("예제 : 0=0초,0.00139=5초,0.0028=10초,0.0056=20초,0.0084=30초,0.012=40초,0.0139=50초,0.0167=60초,1=1시간",1,'#555555','#eeeeee'); ?>
					<!--
					$cache_time은 시간단위 
					1시간=1, 5초=0.00139, 10초=0.0028, 20초=0.0056, 30초=0.0084, 40초=0.012, 50초=0.0139, 60초=0.0167, 3600초=1시간
					-->
					<input type="text" name="wgf_cachetime"<?=$readonly?> class="wg_wdp100<?=$readonly?>" value="<?=$wgf_cachetime?>">
				</td>
				<th>언어설정</th>
				<td class="wdg_help">
					<?php echo wdg_help("예제 : ko_KR=한국,en_US=영어,zh_CN=중국,ja_JP=일본",1,'#555555','#eeeeee'); ?>
					<input type="text" name="wgf_language"<?=$readonly?> class="wg_wdp100<?=$readonly?>" value="<?=$wgf_language?>">
				</td>
				<th>공통 상태</th>
				<td class="wdg_help">
					<?php echo wdg_help("예제 : pending=대기,ok=정상,hide=숨김,trash=삭제",1,'#555555','#eeeeee'); ?>
					<input type="text" name="wgf_common_status"<?=$readonly?> class="wg_wdp100<?=$readonly?>" value="<?=$wgf_common_status?>">
				</td>
			</tr>
		</tbody>
	</table>
</section><!--#anc_wdg_basic-->

<?php echo $frm_submit; ?>
</form><!--#fbwgf-->
</div><!--#wdg_frm-->
<script>
function fwgf_submit(f){
	<?php //echo get_editor_js("wgf_xxxxx1"); ?>	
	<?php //echo get_editor_js("wgf_xxxxx2"); ?>	
	<?php //echo get_editor_js("wgf_xxxxx3"); ?>	

	f.action = "./wdg_config_form_update.php";
	return true;
}
</script>
<?php
include_once('../_tail.php');