<?php
$sub_menu = "918110";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '수주관리';
include_once('./_top_menu_order.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'order_item';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_list/","",$g5['file_name']); // _list 을 제외한 파일명
$qstr1 = '&ser_cst_idx='.$ser_cst_idx.'&st_date='.$st_date.'&en_date='.$en_date; // 추가로 확장해서 넘겨야 할 변수들
$qstr .= $qstr1; // $qstr 확장

$sql_common = " FROM {$g5_table_name} AS ori
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = ori.bom_idx 
";

$where = array();
$where[] = " ".$pre."_status NOT IN ('trash','delete') ";   // 디폴트 검색조건
$where[] = " ".$pre."_type = 'normal' ";   // 디폴트 검색조건

// 해당 업체만
$where[] = " ori.com_idx = '".$_SESSION['ss_com_idx']."' ";


// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'ori_idx' || $sfl == 'cst_idx' || $sfl == 'bom_idx' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'ori_hp' ) :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 고객사
if ($ser_cst_idx) {
    $sql1 = "   SELECT GROUP_CONCAT(ori_idx) AS ori_idxs FROM {$g5['order_item_table']}
                WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND cst_idx = '".$ser_cst_idx."'
    ";
    // echo $sql1.'<br>';
    $one = sql_fetch($sql1,1);
    $where[] = $one['ori_idxs'] ? " ori_idx IN (".$one['ori_idxs'].") " : " (0)";
}

// 날자 검색
if ($st_date) {
    $where[] = " ori_date >= '".$st_date." 00:00:00' ";
}
if ($en_date) {
    $where[] = " ori_date <= '".$en_date." 23:59:59' ";
}

if($cat){
    $where[] = " bom.bct_idx = '".$cat."' ";
    $qstr .= "&cat=".$cat;
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = "ori_idx";
    $sod = "DESC";
}

$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];
// echo $sql.BR;

$rows = 50;
// $rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$sql = " SELECT * {$sql_common} {$sql_search} {$sql_order} LIMIT {$from_record}, {$rows} ";
// echo $sql.BR;
$result = sql_query($sql);

// pending
$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} AND ori_status = 'pending' ";
// echo $sql.'<br>';
$row = sql_fetch($sql);
$pending_count = $row['cnt'];

$colspan = 16;
?>
<style>
    .td_prd_total a {text-decoration:underline;color:dodgerblue;}
    .shp_detail {line-height:1.3em;}
    .shp_detail.pending {color:#22384d;}
    .td_shp_count.lacking a{color:darkorange;}
    .td_shp_detail a.btn {height: 25px;line-height: 25px;}
    .td_shp_detail a {text-decoration:underline;color:dodgerblue;line-height:1.3em;font-size:1.3em;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    <a href="?<?=$qstr?>&sfl=ori_status&stx=pending" class="btn_ov01"> <span class="ov_txt">대기 </span><span class="ov_num"><?php echo number_format($pending_count) ?>건</span></a>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="ser_cst_idx" id="ser_cst_idx">
    <option value="">고객사전체</option>
    <?php
    $sql = "SELECT cst_idx, cst_name FROM {$g5['customer_table']} WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND cst_type = 'customer' ORDER BY cst_idx ";
    // echo $sql.'<br>';
    $rs = sql_query($sql,1);
    for ($i=0; $row=sql_fetch_array($rs); $i++) {
        // print_r2($row);
        echo '<option value="'.$row['cst_idx'].'" '.get_selected($ser_cst_idx, $row['cst_idx']).'>'.$row['cst_name'].'</option>';
    }
    ?>
</select>
<script>$('select[name=ser_cst_idx]').val("<?=$ser_cst_idx?>").attr('selected','selected');</script>
<select name="cat" id="cat">
    <option value="">차종선택</option>
    <?php foreach($g5['cats_key_val'] as $k => $v) { ?>
    <option value="<?=$k?>" <?=get_selected($_GET['cat'], $k)?>><?=$v?></option>
    <?php } ?>
</select>

<label for="st_date" class="sound_only">시작일</label>
<input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" class="frm_input" style="width:90px;">
~
<label for="en_date" class="sound_only">종료일</label>
<input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" class="frm_input" style="width:90px;">

<select name="sfl" id="sfl">
    <option value="bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
    <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
    <option value="ori_id"<?php echo get_selected($_GET['sfl'], "ori_id"); ?>>수주ID</option>
    <option value="ori.bom_idx"<?php echo get_selected($_GET['sfl'], "ori.bom_idx"); ?>>BOMidx</option>
    <option value="ori_idx"<?php echo get_selected($_GET['sfl'], "ori_idx"); ?>>수주고유번호</option>
    <option value="ori_status"<?php echo get_selected($_GET['sfl'], "ori_status"); ?>>상태</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>수주수량과 출하수량이 다른 경우 <span style="color:darkorange;">빨간색</span>으로 표시됩니다.</p>
    <p>출하 처리가 안 된 경우는 '출하'항목 색상이 비활성으로 흐리게 보입니다.</p>
    <p>활용 메뉴얼은 Youtube 동영상을 참고하세요.: <a href="https://youtu.be/J4kBtA3ondQ" target="_blank">동영상링크 바로가기</a></p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./<?=$fname?>_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="st_date" value="<?php echo $st_date ?>">
<input type="hidden" name="en_date" value="<?php echo $en_date ?>">
<input type="hidden" name="cat" value="<?php echo $cat ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="w" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="ori_list_chk">
            <label for="chkall" class="sound_only">항목 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">수주ID</th>
        <th scope="col">고객사</th>
        <th scope="col">날짜</th>
        <th scope="col">차종</th>
        <th scope="col">품번</th>
        <th scope="col">품명</th>
        <th scope="col">수주수량</th>
        <th scope="col">예상출하</th>
        <th scope="col">출하</th>
        <th scope="col">생산계획</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $row['cst'] = get_table('customer','cst_idx',$row['cst_idx']);
        $row['bom'] = get_table('bom','bom_idx',$row['bom_idx']);
        $row['bct'] = get_table('bom_category','bct_idx',$row['bom']['bct_idx']);

        // 출하
        $sql1 = " SELECT * FROM {$g5['shipment_table']} WHERE ori_idx = '".$row['ori_idx']."' AND shp_status IN ('pending','ok') ";
        // print_r3($sql1);
        $rs1 = sql_query($sql1,1);
        for($j=0;$row1=sql_fetch_array($rs1);$j++) {
            // print_r3($row1);
            $row1['cst'] = get_table('customer','cst_idx',$row1['cst_idx']); // 출하처
            $row1['mb'] = get_table('member','mb_id',$row1['mb_id'],'mb_name'); // 기사
            $row1['mb_shipper'] = $row1['mb']['mb_name'] ? ' '.$row1['mb']['mb_name'].', ' : '';
            $row['shp_count'] += $row1['shp_count'];
            $row['shp_detail'] .= '<div class="shp_detail '.$row1['shp_status'].'">'.$row1['cst']['cst_name'].'('.$row1['shp_count'].', '.$row1['mb_shipper'].substr($row1['shp_dt'],5,11).')</div>';
        }
        if($j==0) {
            $row['shp_detail'] = '<a href="./shipment_form.php?ori_idx='.$row['ori_idx'].'" class="btn btn_03" target="_blank">출하등록</a>';
        }
        //출하계획
        $s_sql = " SELECT COUNT(shp_idx) AS shp_total FROM {$g5['shipment_table']}
                    WHERE ori_idx = '{$row['ori_idx']}'
                        AND shp_status NOT IN ('trash','delete') ";
        $s_res = sql_fetch($s_sql,1);
        $row['shp_total'] = $s_res['shp_total'];
        // 생산계획
        $sql2 = " SELECT COUNT(prd_idx) AS prd_total FROM {$g5['production_table']} WHERE ori_idx = '".$row['ori_idx']."' AND prd_status NOT IN ('delete','trash') ";
        $one2 = sql_fetch($sql2,1);
        // echo $sql2.'<br>';
        $row['prd_total'] = $one2['prd_total'];

        // if ord_count != shp_count
        $row['shp_count_class'] = ($row['ori_count']!=$row['shp_count']) ? 'lacking':'';

        // Btn modify
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);

        $shp_dt = $row['ori_date']; //get_dayAddDate($row['ori_date'],3).' 09:01:01';
        $prd_date = $row['ori_date']; //get_dayAddDate($row['ori_date'],1);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['ori_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="hidden" name="cst_idx[<?=$i?>]" value="<?=$row['cst_idx']?>" id="cst_idx_<?=$i?>">
            <input type="hidden" name="bom_idx[<?=$i?>]" value="<?=$row['bom_idx']?>" id="bom_idx_<?=$i?>">
            <input type="hidden" name="ori_count[<?=$i?>]" value="<?=$row['ori_count']?>" id="ori_count_<?=$i?>">
            <input type="hidden" name="shp_dt[<?=$i?>]" value="<?=$shp_dt?>" id="shp_dt_<?=$i?>">
            <input type="hidden" name="prd_date[<?=$i?>]" value="<?=$prd_date?>" id="prd_date_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_ori_id font_size_8"><?=$row['ori_id']?></td>
        <td class="td_cst_name"><a href="?ser_cst_idx=<?=$row['cst_idx']?>"><?=get_text($row['cst']['cst_name'])?></a></td>
        <td class="td_ori_date font_size_8"><a href="?<?=$qstr1?>&sfl=ori_date&stx=<?=$row['ori_date']?>"><?=$row['ori_date']?></a></td>
        <td class="td_ori_count"><?=$row['bct']['bct_name']?></td><!-- 차종 -->
        <td class="td_bom_part_no"><?=$row['bom']['bom_part_no']?></td>
        <td class="td_bom_name font_size_7"><?=$row['bom']['bom_name']?></td>
        <td class="td_ori_count"><?=$row['ori_count']?></td>
        <td class="td_shp_count <?=$row['shp_count_class']?>"><a href="./shipment_list.php?sfl=shp.ori_idx&stx=<?=$row['ori_idx']?>" target="_blank"><?=number_format($row['shp_count'])?></a></td>
        <td class="td_shp_detail font_size_8">
            <?php ;//$row['shp_detail']?>
            <a href="./shipment_list.php?sfl=shp.ori_idx&stx=<?=$row['ori_idx']?>" target="_blank"><?=number_format($row['shp_total'])?></a>
        </td>
        <td class="td_prd_total"><a href="./production_list.php?sfl=ori_idx&stx=<?=$row['ori_idx']?>" target="_blank"><?=number_format($row['prd_total'])?></a></td>
        <td class="td_ori_status"><?=$g5['set_ori_status_value'][$row['ori_status']]?></td>
        <td class="td_mng td_mng_s">
			<?php echo $s_mod ?><!-- 수정 -->
		</td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택출하" onclick="document.pressed=this.value" class="btn btn_03" style="display:no ne;">
    <input type="submit" name="act_button" value="선택생산계획" onclick="document.pressed=this.value" class="btn btn_04" style="display:no ne;">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if (!auth_check($auth[$sub_menu],'w',1)) { ?>
        <a href="./<?=$fname?>_form.php" class="btn btn_01">추가하기</a>
    <?php } ?>

</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
$(function(e) {
    $("#st_date, #en_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

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
	if(document.pressed == "선택삭제") {
		if (!confirm("선택한 항목(들)을 정말 삭제 하시겠습니까?\n복구가 어려우니 신중하게 결정 하십시오.")) {
			return false;
		}
		else {
			$('input[name="w"]').val('d');
		} 
	}
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>