<?php
$sub_menu = "922130";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'material';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_list/","",$g5['file_name']); // _list을 제외한 파일명
//print_r3($_REQUEST);
// 추가 변수 생성
foreach($_REQUEST as $key => $value ) {
    if(substr($key,0,4)=='ser_') {
    //    print_r3($key.'='.$value);
        if(is_array($value)) {
            foreach($value as $k2 => $v2 ) {
//                print_r3($key.$k2.'='.$v2);
                $qstr .= '&'.$key.'[]='.$v2;
                $form_input .= '<input type="hidden" name="'.$key.'[]" value="'.$v2.'" class="frm_input">'.PHP_EOL;
            }
        }
        else {
            $qstr .= '&'.$key.'='.$value;
            $form_input .= '<input type="hidden" name="'.$key.'" value="'.$value.'" class="frm_input">'.PHP_EOL;
        }
    }
}
// print_r3($qstr);

$g5['title'] = '자재현황';
@include_once('./_top_menu_material.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

//print_r2($g5['set_mb_gender_value']);
//echo array_search('남자',$g5['set_mb_gender_value']);

$sql_common = " FROM {$g5_table_name} AS ".$pre."
                LEFT JOIN {$g5['bom_table']} AS bom USING(bom_idx)
";

$where = array();
//$where[] = " (1) ";   // 디폴트 검색조건
$where[] = " ".$pre."_status NOT IN ('delete', 'trash') AND mtr_type IN ('".implode("','",$g5['set_mtr_type_key'])."') ";

// 해당 업체만
$where[] = " mtr.com_idx = '".$_SESSION['ss_com_idx']."' ";

if ($stx) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(mtr_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}


// 기간 검색
if ($ser_st_date)	// 시작일 있는 경우
    $where[] .= " mtr_reg_dt >= '{$ser_st_date} 00:00:00' ";
if ($ser_en_date)	// 종료일 있는 경우
    $where[] .= " mtr_reg_dt <= '{$ser_en_date} 23:59:59' ";

// 고객사
if ($ser_cst_idx_customer) {
    $where[] = " mtr.cst_idx_customer = '".$ser_cst_idx_customer."' ";
    $cst_customer = get_table('customer','cst_idx',$ser_cst_idx_customer);
}
// 공급사
if ($ser_cst_idx_provider) {
    $where[] = " mtr.cst_idx_provider = '".$ser_cst_idx_provider."' ";
    $cst_provider = get_table('customer','cst_idx',$ser_cst_idx_provider);
}

// 작업자
if ($ser_mb_id) {
    $where[] = " mb_id = '".$ser_mb_id."' ";
    $mb1 = get_table('member','mb_id',$ser_mb_id,'mb_name');
}

// 단가
if ($ser_st_price) {
    $where[] = " mtr_price >= '".preg_replace("/,/","",$ser_st_price)."' ";
}
if ($ser_en_price) {
    $where[] = " mtr_price <= '".preg_replace("/,/","",$ser_en_price)."' ";
}

// 상태
if($ser_mtr_status) {
    $where[] = " mtr_status = '".$ser_mtr_status."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
	$sst = "mtr_idx";
    //$sst = "mtr_sort, ".$pre."_reg_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";

$rows = $g5['setting']['set_'.$fname.'_page_rows'] ? $g5['setting']['set_'.$fname.'_page_rows'] : $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT *
            , mtr.cst_idx_customer AS cst_idx_customer
            , mtr.cst_idx_provider AS cst_idx_provider
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$from_record}, {$rows}
";
// echo $sql.BR;
$result = sql_query($sql,1);

// 전체 게시물 수 (쿼리 속도 문제가 갈수록 커질 듯)
$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

// status count (쿼리 속도 문제가 갈수록 커질 듯)
$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} AND mtr_status = 'pending' ";
$row = sql_fetch($sql);
$pending_count = $row['cnt'];

// status count (쿼리 속도 문제가 갈수록 커질 듯)
$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} AND mtr_status = 'ok' ";
$row = sql_fetch($sql);
$ok_count = $row['cnt'];


?>
<style>
.td_mng {width:90px;max-width:90px;}
.td_mtr_subject a, .td_mb_name a {text-decoration: underline;}
.td_mtr_price {width:80px;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건</span></span>
    <a href="?sfl=mtr_status&stx=pending<?=$qstr?>" class="btn_ov01"> <span class="ov_txt">대기 </span><span class="ov_num"><?=number_format($pending_count)?>건</span></a>
    <a href="?sfl=mtr_status&stx=ok<?=$qstr?>" class="btn_ov01"> <span class="ov_txt">입고완료 </span><span class="ov_num"><?=number_format($ok_count)?>건</span></a>
</div>

<div class="local_desc01 local_desc" style="display:none;">
    <p>각 랜딩 페이지별로 신청한 모든 사람들을 목록으로 보여줍니다.</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" style="width:100%;">
<label for="sfl" class="sound_only">검색대상</label>
기간: 
<input type="text" name="ser_st_date" value="<?=$ser_st_date ?>" id="ser_st_date" class="frm_input" style="width:80px;"> ~
<input type="text" name="ser_en_date" value="<?=$ser_en_date ?>" id="ser_en_date" class="frm_input" style="width:80px;">
<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <option value="bom_part_no" <?=get_selected($sfl, 'bom_part_no')?>>품번</option>
    <option value="mtr_name" <?=get_selected($sfl, 'mtr_name')?>>품명</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit btn_submit2" value="검색">
<div class="detaile_search <?=($_SESSION['ss_'.$fname.'_open'])?'on':'';?>">
	<?=($_SESSION['ss_'.$fname.'_open'])?'닫기':'상세';?>
</div>
<div class="detaile_box <?=($_SESSION['ss_'.$fname.'_open'])?'open':'';?>">
	<div class="tbl_frm01 tbl_wrap">
		<table>
			<caption><?php echo $g5['title']; ?></caption>
			<colgroup>
				<col class="grid_4" style="width:8%;">
				<col style="width:38%;">
				<col class="grid_4" style="width:8%;">
				<col style="width:45%;">
			</colgroup>
			<tbody>
				<tr>
                    <th>고객사선택</th>
                    <td>
                        <?php
                        if($ser_cst_idx_customer) {
                            $cst_customer = get_table_meta('customer','cst_idx',$ser_cst_idx_customer);
                        }
                        ?>
                        <input type="hidden" name="ser_cst_idx_customer" value="<?=$ser_cst_idx_customer?>" class="frm_input">
                        <input type="text" name="cst_name_customer" value="<?=$cst_customer['cst_name']?>" class="frm_input" style="width:300px;" readonly>
                        <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=customer" class="btn btn_02 btn_customer">찾기</a>
                    </td>
                    <th>공급사선택</th>
                    <td>
                        <?php
                        if($ser_cst_idx_provider) {
                            $cst_provider = get_table_meta('customer','cst_idx',$ser_cst_idx_provider);
                        }
                        ?>
                        <input type="hidden" name="ser_cst_idx_provider" value="<?=$ser_cst_idx_provider?>" class="frm_input">
                        <input type="text" name="cst_name_provider" value="<?=$cst_provider['cst_name']?>" class="frm_input" style="width:300px;" readonly>
                        <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=provider" class="btn btn_02 btn_customer">찾기</a>
                    </td>
				</tr>
				<tr>
					<th>작업자선택</th>
					<td>
                        <?php
                        if($ser_mb_id) {
                            $mb1 = get_table_meta('member','mb_id',$ser_mb_id);
                        }
                        ?>
                        <input type="hidden" name="ser_mb_id" value="<?=$ser_mb_id?>" class="frm_input" style="width:100px">
                        <input type="text" name="ser_mb_name" value="<?=$mb1['mb_name']?>" id="mb_name" class="frm_input" style="width:100px;" readonly>
                        <a href="./member_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_member">찾기</a>
                    </td>
					<th>단가</th>
					<td>
						<input type="text" name="ser_st_price" value="<?=$ser_st_price?>" class="frm_input" style="width:70px">원
						~
						<input type="text" name="ser_en_price" value="<?=$ser_en_price?>" class="frm_input" style="width:70px">원
					</td>
				</tr>
				<tr>
                    <th>상태</th>
					<td colspan="3">
						<input type="radio" name="ser_mtr_status" id="ser_mtr_status_all" value="" checked=""><label for="ser_mtr_status_all">관계없음</label>
						<?php
                        if(is_array($g5['set_mtr_status_value'])) {
                            foreach ($g5['set_mtr_status_value'] as $k1=>$v1) {
                                if(in_array($k1,array('trash'))) {continue;}
                                echo '<input type="radio" name="ser_mtr_status" id="ser_mtr_status_'.$k1.'" value="'.$k1.'">
                                      <label for="ser_mtr_status_'.$k1.'">'.$v1.'</label>'.PHP_EOL;
                            }
                        }
						?>
						<script>$('#ser_mtr_status_<?=$ser_mtr_status?>').attr('checked','checked');</script>
                    </td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="search_btn">
		<input type="submit" value="검색" class="search_btns search_btn01" accesskey="">
		<span class="search_btns search_btn02">닫기</span>
	</div>
</div>
<script>
// 업체 찾기
$(document).on('click','.btn_customer',function(e){
    e.preventDefault();
    var href = $(this).attr('href');
    winLanding = window.open(href, "winLanding", "left=100,top=100,width=520,height=600,scrollbars=1");
    winLanding.focus();
    return false;

});

// 회원 찾기
$(document).on('click','.btn_member',function(e){
    e.preventDefault();
    var href = $(this).attr('href');
    winMember = window.open(href, "winMember", "left=100,top=100,width=520,height=600,scrollbars=1");
    winMember.focus();
    return false;

});

// 검색 부분 상세, 닫기 버튼 클릭
$(".detaile_search").click(function(){	
	if($(".detaile_box").hasClass("open") === true) {
		$(".detaile_box").removeClass("open");
		$(this).removeClass("on");
		$(this).html('상세');
		search_detail('close');
	} else {
		$(".detaile_box").addClass("open");
		$(this).addClass("on");
		$(this).html('닫기');
		search_detail('open');
	};
});
// 취소 버튼 클릭
$(".search_btn .search_btn02").click(function(){
	$(".detaile_box").removeClass("open");
	$(this).removeClass("on");
	$(".detaile_search").html('상세');
	search_detail('close');
});
function search_detail(flag) {
	$.getJSON(g5_user_admin_url+'/ajax/session_set.php',{"fname":"<?=$fname?>","flag":flag},function(res) {
		if(res.result == true) {
			// console.log(res.flag);
			// console.log(res.msg);
		}
	});
}
</script>
</form>












<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">
<?=$form_input?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="mtr_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" style="width:100px;">품번</th>
        <th scope="col">품명</th>
        <th scope="col">구분</th>
        <th scope="col">설비</th>
        <th scope="col">작업자</th>
        <th scope="col" style="width:50px;">수량</th>
        <th scope="col" style="width:60px;">교대</th>
        <th scope="col">품질</th>
        <th scope="col">통계일</th>
        <th scope="col">날짜</th>
        <th scope="col" style="width:55px;">상태</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);
        $row['cst_customer'] = get_table('customer','cst_idx',$row['cst_idx_customer'],'cst_name');
        $row['cst_provider'] = get_table('customer','cst_idx',$row['cst_idx_provider'],'cst_name');
        $row['mms'] = get_table('mms','mms_idx',$row['mms_idx'],'mms_name');
        $row['mb1'] = get_table('member','mb_id',$row['mb_id'],'mb_name');
        $row['shf'] = get_table('shift','shf_idx',$row['shf_idx'],'shf_name');
        // print_r2($row['mb1']);

		$fle_width = '30';
		$fle_height = '30';
		// 관련 파일 추출
		$sql = "SELECT * FROM {$g5['file_table']}
				WHERE fle_db_table = 'item' AND fle_db_id = '".$row['mtr_idx']."'
                ORDER BY fle_sort, fle_reg_dt DESC
        ";
		$rs = sql_query($sql,1);
        // echo $sql;
		for($j=0;$row1=sql_fetch_array($rs);$j++) {
			$row[$row1['fle_type']][$row1['fle_sort']]['file'] = (is_file(G5_PATH.$row1['fle_path'].'/'.$row1['fle_name'])) ?
								'&nbsp;&nbsp;'.$row1['fle_name_orig'].'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_URL.'/lib/download.php?file_fullpath='.urlencode(G5_PATH.$row1['fle_path'].'/'.$row1['fle_name']).'&file_name_orig='.$row1['fle_name_orig'].'">파일다운로드</a>'
								.'&nbsp;&nbsp;<input type="checkbox" name="'.$row1['fle_type'].'_del['.$row1['fle_sort'].']" value="1"> 삭제'
								:'';
			$row[$row1['fle_type']][$row1['fle_sort']]['fle_name'] = (is_file(G5_PATH.$row1['fle_path'].'/'.$row1['fle_name'])) ?
								$row1['fle_name'] : '' ;
			$row[$row1['fle_type']][$row1['fle_sort']]['fle_path'] = (is_file(G5_PATH.$row1['fle_path'].'/'.$row1['fle_name'])) ?
								$row1['fle_path'] : '' ;
			$row[$row1['fle_type']][$row1['fle_sort']]['exists'] = (is_file(G5_PATH.$row1['fle_path'].'/'.$row1['fle_name'])) ?
								1 : 0 ;
		}
		// 대표이미지
		$fle_type3 = "item_list";
		if($row[$fle_type3][0]['fle_name']) {
			$row[$fle_type3][0]['thumbnail'] = thumbnail($row[$fle_type3][0]['fle_name'],
							G5_PATH.$row[$fle_type3][0]['fle_path'], G5_PATH.$row[$fle_type3][0]['fle_path'],
							200, 200,
							false, true, 'center', true, $um_value='85/3.4/15'
			);	// is_create, is_crop, crop_mode
			$row[$fle_type3][0]['thumbnail_img'] = '<img src="'.G5_URL.$row[$fle_type3][0]['fle_path'].'/'.$row[$fle_type3][0]['thumbnail'].'" width="'.$fle_width.'" height="'.$fle_height.'">';
		}
		else {
			$row[$fle_type3][0]['thumbnail'] = 'no_image.gif';
			$row[$fle_type3][0]['fle_path'] = '/theme/v10/img';
			$row[$fle_type3][0]['thumbnail_img'] = '<img src="'.G5_URL.$row[$fle_type3][0]['fle_path'].'/'.$row[$fle_type3][0]['thumbnail'].'" width="'.$fle_width.'" height="'.$fle_height.'">';
		}
        // print_r2($row);

        // 레벨 bom_item 
		$sql = "SELECT * FROM {$g5['bom_item_table']} WHERE bom_idx_child = '".$row['bom_idx']."' ORDER BY bit_reg_dt DESC ";
		$rs = sql_query($sql,1);
        // echo $sql.BR;
		for($j=0;$row1=sql_fetch_array($rs);$j++) {
        }

        // 처리일자 (히스토리가 있으면 처리날짜)
        $row['mtr_dt'] = $row['mtr_reg_dt'];
        // echo $row['mtr_history'].BR;
        $row['mtr_histories'] = explode("\n",$row['mtr_history']);
        // print_r2($row['mtr_histories']);
        if(isset($row['mtr_histories'][0])) {
            for($j=0;$j<sizeof($row['mtr_histories']);$j++) {
                if($row['mtr_histories'][$j]) {
                    $row['mtr_dt'] = substr($row['mtr_histories'][$j],-19);
                }
            }
        }

        // 품질 (mtr_defect_type이 없으면 양품)
        $row['mtr_quality'] = $row['mtr_defect_type'] ? $g5['set_mtr_defect_type_value'][$row['mtr_defect_type']]:'양품';


        // 버튼들
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&amp;w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?=$bg?>" tr_id="<?=$row[$pre.'_idx']?>">
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_mtr_part_no font_size_7"><?=$row['mtr_part_no']?></td><!-- 품번 -->
        <td class="td_mtr_name font_size_7"><?=$row['mtr_name']?></td><!-- 품명 -->
        <td class="td_mtr_type font_size_7"><?=$g5['set_mtr_type_value'][$row['mtr_type']]?></td><!-- 구분 -->
        <td class="td_mms_name"><?=$row['mms']['mms_name']?></td><!-- 설비 -->
        <td class="td_mb_name"><?=$row['mb1']['mb_name']?></td><!-- 작업자 -->
        <td class="td_mb_name"><?=$row['mtr_value']?></td><!-- 수량 -->
        <td class="td_shf_name font_size_7"><?=$row['shf']['shf_name']?></td><!-- 교대 -->
        <td class="td_mtr_quality font_size_7"><?=$row['mtr_quality']?></td><!-- 품질 -->
        <td class="td_mtr_date font_size_7"><?=($row['mtr_date']!='0000-00-00')?$row['mtr_date']:''?></td><!-- 통계일 -->
        <td class="td_mtr_dt font_size_7"><?=$row['mtr_dt']?></td><!-- 날짜 -->
        <td class="td_mtr_status"><!-- 상태 -->
			<select name="mtr_status[<?php echo $i; ?>]" id="mtr_status_<?=$row['mtr_idx']?>" style="width:100px;">
				<option value="">상태선택</option>
				<?=$g5['set_mtr_status_value_options']?>
			</select>
			<script>$("#mtr_status_<?=$row['mtr_idx']?>").val("<?=$row['mtr_status']?>").attr("selected","selected");</script>
        </td>
        <td class="td_admin">
			<?=$s_mod?>
		</td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="20" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택복제" onclick="document.pressed=this.value" class="btn btn_02" style="display:none;">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if ($is_admin == 'super') { ?>
    <a href="./<?=$fname?>_form.php" id="member_add" class="btn btn_01">추가하기</a>
    <?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
var posY;
$(function(e) {
    $("input[name$=_date]").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        //maxDate: "+0d"
    });	 
});


function form01_submit(f)
{

    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

	if(document.pressed == "선택수정") {
		$('input[name="w"]').val('u');
	}
	else if(document.pressed == "선택삭제") {
		if (!confirm("선택한 항목(들)을 정말 삭제 하시겠습니까?\n복구가 어려우니 신중하게 결정 하십시오.")) {
			return false;
		}
		// else {
		// 	$('input[name="w"]').val('d');
		// }
	}
	else if(document.pressed == "선택복제") {
		if (!confirm("선택한 항목(들)을 정말 복제 하시겠습니까?")) {
			return false;
		}
	}

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
