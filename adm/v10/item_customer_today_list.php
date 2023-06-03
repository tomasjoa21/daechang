<?php
$sub_menu = "922120";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '고객사별현황';
@include_once('./_top_menu_item_status.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


$sql_common = " FROM {$g5['production_item_table']} AS pri
                LEFT JOIN {$g5['production_table']} AS prd USING(prd_idx)
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = pri.bom_idx
";

$where = array();
//$where[] = " (1) ";   // 디폴트 검색조건
$where[] = " prd_start_date = '".statics_date(G5_TIME_YMDHIS)."' ";    // 오늘 것만

// 해당 업체만
$where[] = " pri.com_idx = '".$_SESSION['ss_com_idx']."' ";

if ($stx && $sfl) {
    switch ($sfl) {
		case ( $sfl == $pre.'_id' || $sfl == $pre.'_idx' || $sfl == 'mms_idx' ) :
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

// 고객사
if ($ser_cst_idx_customer) {
    $where[] = " cst_idx_customer = '".$ser_cst_idx_customer."' ";
}

// 작업자
if ($cst_idx_customer) {
    $where[] = " pri.mb_id = '".$cst_idx_customer."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

$rows = $g5['setting']['set_'.$g5['file_name'].'_page_rows'] ? $g5['setting']['set_'.$g5['file_name'].'_page_rows'] : $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT pri_idx, pri.bom_idx AS pri_bom_idx, prd_start_date, bom.bom_idx, bom.bom_part_no, bom.bom_name, bom_type, bct_idx,
            cst_idx_customer,
            SUM(pri_value) AS pri_value_sum,
            GROUP_CONCAT(pri_idx) AS pri_idxs,
            GROUP_CONCAT(pri_value) AS pri_values
		{$sql_common}
		{$sql_search}
        GROUP BY cst_idx_customer, pri_bom_idx
        ORDER BY cst_idx_customer ASC, pri_bom_idx
		LIMIT {$from_record}, {$rows}
";
// echo $sql.BR;
$result = sql_query($sql,1);

// 전체 게시물 수
$sql = " SELECT COUNT(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산


// 고객사 select list 추출
$sql1 = " SELECT cst_idx_customer {$sql_common} 
            WHERE prd_start_date = '".statics_date(G5_TIME_YMDHIS)."' AND pri.com_idx = '".$_SESSION['ss_com_idx']."'
            GROUP BY cst_idx_customer
";
// echo $sql1.BR;
$rs = sql_query($sql1,1);
for ($i=0; $row=sql_fetch_array($rs); $i++) {
    $row['cst_customer'] = get_table('customer','cst_idx',$row['cst_idx_customer'],'cst_name');
    $row['cst_name'] = $row['cst_customer']['cst_name'];
    // print_r2($row);
    $cst_selects[$i] = array('cst_idx_customer'=>$row['cst_idx_customer'],'cst_name'=>$row['cst_name']);
}
// print_r2($cst_selects);


$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

?>
<style>
.td_mng {width:90px;max-width:90px;}
.td_pri_subject a, .td_cst_name a {text-decoration: underline;}
.td_pri_price {width:80px;}
.td_pic_value a{color:#ff5e5e;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총건수 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건</span></span>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p><?=statics_date(G5_TIME_YMDHIS)?> 각 고객사별 생산 현황입니다.</p>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" style="width:100%;">
<label for="sfl" class="sound_only">검색대상</label>
<select name="cst_idx_customer" id="cst_idx_customer">
    <option value="">고객사전체</option>
    <?php
    for ($i=0; $i<sizeof($cst_selects); $i++) {
        echo '<option value="'.$cst_selects[$i]['cst_idx_customer'].'">'.$cst_selects[$i]['cst_name'].'</option>';
    }
    ?>
</select>
<script>$('#cst_idx_customer').val('<?=$cst_idx_customer?>');</script>
<select name="sfl" id="sfl">
    <option value="">검색항목</option>
    <option value="bom_part_no" <?=get_selected($sfl, 'bom_part_no')?>>품번</option>
    <option value="bom_name" <?=get_selected($sfl, 'bom_name')?>>품명</option>
    <option value="mms_idx" <?=get_selected($sfl, 'mms_idx')?>>설비번호</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit btn_submit2" value="검색">
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
        <th scope="col" id="pri_list_chk" style="display:none;">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">고객사</th>
        <th scope="col" style="min-width:200px;">품번/품명</th>
        <th scope="col">구분</th>
        <th scope="col">차종</th>
        <th scope="col">목표</th>
        <th scope="col">생산수량</th>
        <th scope="col" style="width:50px;">달성율</th>
        <th scope="col" style="width:200px;">그래프</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // print_r2($row);
        $row['cst_customer'] = get_table('customer','cst_idx',$row['cst_idx_customer'],'cst_name');
        $row['bct'] = get_table('bom_category','bct_idx',$row['bct_idx'],'bct_name');
        // print_r2($row['cst_customer']);

        // 현재 생산수량 합계
        $sql1 = " SELECT SUM(pic_value) AS pic_sum FROM {$g5['production_item_count_table']} 
                    WHERE pri_idx in (".$row['pri_idxs'].") AND pic_date = '".statics_date(G5_TIME_YMDHIS)."'
        ";
        // echo $sql1.BR;
        $row['pic'] = sql_fetch($sql1,1);

        // 비율
        $row['rate'] = ($row['pri_value_sum']) ? $row['pic']['pic_sum'] / $row['pri_value_sum'] * 100 : 0 ;
        $row['rate_color'] = '#d1c594';
        $row['rate_color'] = ($row['rate']>=80) ? '#72ddf5' : $row['rate_color'];
        $row['rate_color'] = ($row['rate']>=100) ? '#ff9f64' : $row['rate_color'];

        // 그래프
        if($row['pri_value_sum'] && $row['pic']['pic_sum']) {
            // $row['rate_percent'] = $row['pic']['pic_sum'] / max($item_max) * 100;
            $row['rate_percent'] = $row['pic']['pic_sum'] / $row['pri_value_sum'] * 100;
            $row['graph'] = '<img class="graph_output" src="../img/dot.gif" style="width:'.(($row['rate_percent']>100)?100:$row['rate_percent']).'%;background:'.$row['rate_color'].';" height="8px">';
        }

        // 버튼들
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&amp;w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?=$bg?>" tr_id="<?=$row[$pre.'_idx']?>">
        <td class="td_chk" style="display:none;">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_cst_name"><a href="?ser_cst_idx_customer=<?=$row['cst_idx_customer']?>"><?=$row['cst_customer']['cst_name']?></a></td><!-- 고객사 -->
        <td class="td_part_no_name td_left"><!-- 품번/품명 -->
            <?=$row['bom_part_no']?><br><?=$row['bom_name']?>
        </td>
        <td class="td_pri_type font_size_7"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 구분 -->
        <td class="td_bct_idx font_size_7"><?=$row['bct']['bct_name']?></td><!-- 차종 -->
        <td class="td_pri_value_sum"><?=$row['pri_value_sum']?></td><!-- 목표 -->
        <td class="td_pic_value color_red"><a href="./item_worker_today_list.php?sfl=bom_part_no&stx=<?=$row['bom_part_no']?>"><?=(int)$row['pic']['pic_sum']?></a></td><!-- 생산수량 -->
        <td class="td_pri_rate color_yellow font_size_7"><?=number_format($row['rate_percent'],1)?> %</td><!-- 달성율 -->
        <td class="td_graph td_left"><!-- 그래프 -->
            <?=$row['graph']?>
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

<div class="btn_fixed_top" style="display:none;">
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
