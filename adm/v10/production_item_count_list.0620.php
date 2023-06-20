<?php
$sub_menu = "922120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '생산제품현황';
@include_once('./_top_menu_item_status.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 검색 조건
$ser_st_date = $ser_st_date ?: G5_TIME_YMD;
$ser_st_time = $ser_st_time ?: '00:00:00';
$ser_en_date = $ser_en_date ?: G5_TIME_YMD;
$ser_en_time = $ser_en_time ?: '23:59:59';

// 통계일 default
$sfl = $sfl ?: 'pic_date';
$stx = $stx ?: G5_TIME_YMD;

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'production_item_count';
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


$sql_common = " FROM {$g5_table_name} AS ".$pre."
                LEFT JOIN {$g5['production_item_table']} AS pri USING(pri_idx)
                LEFT JOIN {$g5['bom_table']} AS bom USING(bom_idx)
";

$where = array();
//$where[] = " (1) ";   // 디폴트 검색조건
// $where[] = " pic_date = '".statics_date(G5_TIME_YMDHIS)."' ";    // 오늘 것만

// 해당 업체만
$where[] = " pri.com_idx = '".$_SESSION['ss_com_idx']."' ";

if ($stx && $sfl) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' ) :
            $where[] = " ({$sfl} = '{$stx}') ";
            break;
		case ($sfl == $pre.'_hp') :
            $where[] = " REGEXP_REPLACE(pic_hp,'-','') LIKE '".preg_replace("/-/","",$stx)."' ";
            break;
        default :
            $where[] = " ({$sfl} LIKE '%{$stx}%') ";
            break;
    }
}


// 기간 검색
if ($ser_st_date) {
    if ($ser_st_time) {
        $where[] = " pic_reg_dt >= '".$ser_st_date.' '.$ser_st_time."' ";
    }
    else {
        $where[] = " pic_reg_dt >= '".$ser_st_date.' 00:00:00'."' ";
    }
}
if ($ser_en_date) {
    if ($ser_en_time) {
        $where[] = " pic_reg_dt <= '".$ser_en_date.' '.$ser_en_time."' ";
    }
    else {
        $where[] = " pic_reg_dt <= '".$ser_en_date.' 23:59:59'."' ";
    }
}


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
    $where[] = " pic.mb_id = '".$ser_mb_id."' ";
    $mb1 = get_table('member','mb_id',$ser_mb_id,'mb_name');
}

// 설비
if ($ser_mms_idx) {
    $where[] = " mms_idx = '".$ser_mms_idx."' ";
    $mms = get_table('mms','mms_idx',$ser_mms_idx,'mms_name');
}

// 제품구분
if ($ser_bom_type) {
    $where[] = " bom_type = '".$ser_bom_type."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
	$sst = "pic_idx";
    //$sst = "pic_sort, ".$pre."_reg_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";

$rows = $g5['setting']['set_'.$fname.'_page_rows'] ? $g5['setting']['set_'.$fname.'_page_rows'] : $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT *
		{$sql_common}
		{$sql_search}
        {$sql_order}
		LIMIT {$from_record}, {$rows}
";
echo $sql.BR;
$result = sql_query($sql,1);

// 전체 게시물 수
$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.td_mng {width:90px;max-width:90px;}
.td_pic_subject a, .td_mb_name a {text-decoration: underline;}
.td_pic_price {width:80px;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건</span></span>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p><?=statics_date(G5_TIME_YMDHIS)?> 생산 제품 리스트입니다.</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" style="width:100%;">
<label for="sfl" class="sound_only">검색대상</label>
등록일:
<input type="text" name="ser_st_date" value="<?=$ser_st_date?>" id="ser_st_date" class="frm_input" autocomplete="off" style="width:90px;">
<input type="text" name="ser_st_time" value="<?=$ser_st_time?>" id="ser_st_time" class="frm_input" autocomplete="off" style="width:70px;" placeholder="00:00:00">
~
<input type="text" name="ser_en_date" value="<?=$ser_en_date?>" id="ser_en_date" class="frm_input" autocomplete="off" style="width:90px;">
<input type="text" name="ser_en_time" value="<?=$ser_en_time?>" id="ser_en_time" class="frm_input" autocomplete="off" style="width:70px;" placeholder="00:00:00">
<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <option value="bom_part_no" <?=get_selected($sfl, 'bom_part_no')?>>품번</option>
    <option value="bom_name" <?=get_selected($sfl, 'bom_name')?>>품명</option>
    <option value="pic_date" <?=get_selected($sfl, 'pic_date')?>>통계일</option>
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
					<th>설비선택</th>
					<td>
                        <input type="hidden" name="ser_mms_idx" value="<?=$ser_mms_idx?>" class="frm_input" style="width:100px">
                        <input type="text" name="mms_name" value="<?=$mms['mms_name']?>" id="mms_name" class="frm_input" style="width:100px;" readonly>
                        <a href="./mms_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_mms">찾기</a>
                    </td>
				</tr>
				<tr>
                    <th>제품구분</th>
					<td colspan="3">
						<input type="radio" name="ser_bom_type" id="ser_bom_type_all" value="" checked=""><label for="ser_bom_type_all">관계없음</label>
                        <input type="radio" name="ser_bom_type" id="ser_bom_type_product" value="product"><label for="ser_bom_type_product">완제품</label>
                        <input type="radio" name="ser_bom_type" id="ser_bom_type_half" value="half"><label for="ser_bom_type_half">서브제품</label>
						<script>$('#ser_bom_type_<?=$ser_bom_type?>').attr('checked','checked');</script>
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
// timepicker 설정
$("input[name$=_time]").timepicker({
    'timeFormat': 'H:i:s',
    'step': 10
});

// 설비 찾기
$(document).on('click','.btn_mms',function(e){
    e.preventDefault();
    var href = $(this).attr('href');
    winMMS = window.open(href, "winMMS", "left=100,top=100,width=520,height=600,scrollbars=1");
    winMMS.focus();
    return false;
});

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
        <th scope="col" id="pic_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" style="width:100px;">품번</th>
        <th scope="col">품명</th>
        <th scope="col">구분</th>
        <th scope="col">차종</th>
        <th scope="col">납품처</th>
        <th scope="col">작업자</th>
        <th scope="col">설비</th>
        <th scope="col">목표</th>
        <th scope="col">수량</th>
        <th scope="col">등록일시</th>
        <th scope="col">통계일</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);
        $row['cst_customer'] = get_table('customer','cst_idx',$row['cst_idx_customer'],'cst_name');
        $row['bct'] = get_table('bom_category','bct_idx',$row['bct_idx'],'bct_name');
        $row['mb1'] = get_table('member','mb_id',$row['mb_id'],'mb_name');
        // print_r2($row['cst_customer']);

        // 버튼들
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&amp;w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?=$bg?>" tr_id="<?=$row[$pre.'_idx']?>">
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_bom_part_no font_size_7"><?=$row['bom_part_no']?></td><!-- 품번 -->
        <td class="td_pic_name font_size_7"><?=$row['bom_name']?></td><!-- 품명 -->
        <td class="td_pic_type font_size_7"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 구분 -->
        <td class="td_bct_idx font_size_7"><?=$row['bct']['bct_name']?></td><!-- 차종 -->
        <td class="td_cst_name_customer font_size_7"><?=$row['cst_customer']['cst_name']?></td><!-- 납품처 -->
        <td class="td_mb_name"><a href="?ser_mms_idx=<?=$row['mms_idx']?>&ser_mb_id=<?=$row['mb_id']?>"><?=$row['mb1']['mb_name']?></a></td><!-- 작업자 -->
        <td class="td_mms_name"><a href="?ser_mms_idx=<?=$row['mms_idx']?>&ser_mb_id=<?=$row['mb_id']?>"><?=$g5['mms'][$row['mms_idx']]['mms_name']?></a></td><!-- 설비 -->
        <td class="td_pri_value font_size_7"><?=$row['pri_value']?></td><!-- 목표 -->
        <td class="td_pic_value font_size_7 color_red"><?=$row['pic_value']?></td><!-- 수량 -->
        <td class="td_pic_reg_dt font_size_7"><?=substr($row['pic_reg_dt'],5)?></td><!-- 등록일 -->
        <td class="td_pic_date font_size_7"><?=substr($row['pic_date'],5)?></td><!-- 통계일 -->
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
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02" style="display:none;">
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
