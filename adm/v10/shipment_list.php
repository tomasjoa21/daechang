<?php
$sub_menu = "918120";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '출하관리';
// include_once('./_top_menu_order.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'shipment';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_list/","",$g5['file_name']); // _list 을 제외한 파일명
$qstr1 = '&ser_cst_idx='.$ser_cst_idx.'&st_date='.$st_date.'&en_date='.$en_date; // 추가로 확장해서 넘겨야 할 변수들
$qstr .= $qstr1; // $qstr 확장

$sql_common = " FROM {$g5_table_name} AS shp
                LEFT JOIN {$g5['order_item_table']} AS ori ON ori.ori_idx = shp.ori_idx 
                LEFT JOIN {$g5['member_table']} AS mbr ON mbr.mb_id = shp.mb_id
";

$where = array();
$where[] = " ".$pre."_status NOT IN ('trash','delete') ";   // 디폴트 검색조건

// 해당 업체만
$where[] = " shp.com_idx = '".$_SESSION['ss_com_idx']."' ";


// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'shp_idx' || $sfl == 'cst_idx' || $sfl == 'bom_idx' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'bom_part_no') :
            $sql1 = "   SELECT GROUP_CONCAT(bom_idx) AS bom_idxs FROM {$g5['bom_table']}
                        WHERE bom_part_no = '".trim($stx)."'
            ";
            // echo $sql1.'<br>';
            $one = sql_fetch($sql1,1);
            $where[] = $one['bom_idxs'] ? " bom_idx IN (".$one['bom_idxs'].") " : " (0)";
            break;
		case ( $sfl == 'bom_name' ) :
            $sql1 = "   SELECT GROUP_CONCAT(bom_idx) AS bom_idxs FROM {$g5['bom_table']}
                        WHERE bom_name LIKE '%".trim($stx)."%'
            ";
            // echo $sql1.'<br>';
            $one = sql_fetch($sql1,1);
            $where[] = $one['bom_idxs'] ? " bom_idx IN (".$one['bom_idxs'].") " : " (0)";
            break;
		// case ( $sfl == 'mb_name' ) :
        //     $sql1 = "   SELECT GROUP_CONCAT(mb_id) AS mb_ids FROM {$g5['member_table']}
        //                 WHERE mb_name LIKE '%".trim($stx)."%'
        //     ";
        //     // echo $sql1.'<br>';
        //     $one = sql_fetch($sql1,1);
        //     $one['mb_ids_array'] = explode(",",$one['mb_ids']);
        //     $where[] = $one['bom_idxs'] ? " bom_idx IN ('".implode("','",$one['mb_ids_array'])."') " : " (0)";
        //     break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 고객사
if ($ser_cst_idx) {
    $sql1 = "   SELECT GROUP_CONCAT(shp_idx) AS shp_idxs FROM {$g5['shipment_table']}
                WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND cst_idx = '".$ser_cst_idx."'
    ";
    // echo $sql1.'<br>';
    $one = sql_fetch($sql1,1);
    $where[] = $one['shp_idxs'] ? " shp_idx IN (".$one['shp_idxs'].") " : " (0)";
}

// 날자 검색
if ($ser_st_date) {
    $where[] = " shp_dt >= '".$ser_st_date." 00:00:00' ";
}
if ($ser_en_date) {
    $where[] = " shp_dt <= '".$ser_en_date." 23:59:59' ";
}


// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = $pre."_reg_dt";
    $sod = "DESC";
}

$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];
//print_r3($sql).'<br>';

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$driver_sql = " SELECT cmm.mb_id, mb.mb_name FROM {$g5['company_member_table']} cmm
                    LEFT JOIN {$g5['member_table']} mb ON cmm.mb_id = mb.mb_id
                WHERE cmm_type = 'driver'
                    AND com_idx = '{$_SESSION['ss_com_idx']}'
                    AND mb_leave_date = ''
                    AND mb_intercept_date = ''
";
$driver_res = sql_query($driver_sql,1);
$driver_opions = '';
if($driver_res->num_rows){
for($d=0;$dr=sql_fetch_array($driver_res);$d++){
    $driver_options .= '<option value="'.$dr['mb_id'].'">'.$dr['mb_name'].'</option>';
}
}


$sql = "SELECT *
            , shp.cst_idx AS shp_cst_idx
            , ori.cst_idx AS ori_cst_idx
        {$sql_common} {$sql_search} {$sql_order} 
        LIMIT {$from_record}, {$rows}
";
// echo $sql.'<br>';
$result = sql_query($sql,1);

// pending
$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} AND shp_status = 'pending' ";
// echo $sql.'<br>';
$row = sql_fetch($sql);
$pending_count = $row['cnt'];

$colspan = 16;
?>
<style>
    .td_prd_total a {text-decoration:underline;color:dodgerblue;}
    .shp_detail {line-height:1.3em;}
    .shp_detail.pending {color:#22384d;}
    .td_shp_count.lacking {color:darkorange;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    <a href="?<?=$qstr?>&sfl=shp_status&stx=pending" class="btn_ov01"> <span class="ov_txt">대기 </span><span class="ov_num"><?php echo number_format($pending_count) ?>건</span></a>
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
        echo '<option value="'.$row['cst_idx'].'" '.get_selected($ser_cst_idx, $row['cst_idx']).'>'.$row['cst_name'].')</option>';
    }
    ?>
</select>
<script>$('select[name=ser_cst_idx]').val("<?=$ser_cst_idx?>").attr('selected','selected');</script>

<label for="ser_st_date" class="sound_only">시작일</label>
<input type="text" name="ser_st_date" value="<?php echo $ser_st_date ?>" id="ser_st_date" class="frm_input" style="width:90px;">
~
<label for="ser_en_date" class="sound_only">종료일</label>
<input type="text" name="ser_en_date" value="<?php echo $ser_en_date ?>" id="ser_en_date" class="frm_input" style="width:90px;">

<select name="sfl" id="sfl">
    <option value="bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
    <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
    <option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>기사이름</option>
    <option value="shp.mb_id"<?php echo get_selected($_GET['sfl'], "shp.mb_id"); ?>>기사아이디</option>
    <option value="shp.ori_idx"<?php echo get_selected($_GET['sfl'], "shp.ori_idx"); ?>>수주고유번호</option>
    <option value="shp_status"<?php echo get_selected($_GET['sfl'], "shp_status"); ?>>상태</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>수주받은 물량을 여러번으로 나누어서 출하하는 경우 [복제] 버튼을 클릭해서 출하를 여러 회차로 분산시키시면 됩니다. (출하수량을 확인하고 변경하세요.)</p>
    <p>출하처리가 완료된 건수에 대해서는 [복제]가 불가능합니다.</p>
    <p>활용 메뉴얼은 Youtube 동영상을 참고하세요.: <a href="https://youtu.be/KVMORsFp_5k" target="_blank">동영상링크 바로가기</a></p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./<?=$fname?>_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="w" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="shp_list_chk">
            <label for="chkall" class="sound_only">항목 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">출하처</th>
        <th scope="col">배송기사</th>
        <th scope="col">출하일시</th>
        <th scope="col">품번</th>
        <th scope="col">품명</th>
        <th scope="col">출하수량</th>
        <th scope="col">수주정보</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $row['cst1'] = get_table('customer','cst_idx',$row['shp_cst_idx']);
        $row['cst2'] = get_table('customer','cst_idx',$row['ori_cst_idx']);
        $row['bom'] = get_table('bom','bom_idx',$row['bom_idx']);

        // 수주정보
        $row['ori_detail'] = $row['cst2']['cst_name'].'('.$row['ori_count'].', '.substr($row['ori_date'],5,11).')';
        // 출하일
        $row['shp_date'] = substr($row['shp_dt'],0,10);


        // Btn modify
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">복제</a>';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.$row[$pre.'_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['shp_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="<?=$pre?>_idx[<?=$i?>]" value="<?=$row[$pre.'_idx']?>" id="<?=$pre?>_idx_<?=$i?>">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
        </td>
        <td class="td_cst_name"><a href="?ser_cst_idx=<?=$row['shp_cst_idx']?>"><?=get_text($row['cst1']['cst_name'])?></a></td>
        <td class="td_mb_name" style="width:110px;">
            <!-- <a href="?<?=$qstr1?>&sfl=shp.mb_id&stx=<?php ;//$row['mb_id']?>"><?php ;//$row['mb_name']?></a> -->
            <select name="mb_id[<?=$i?>]" id="mb_id_<?=$i?>">
                <option value="">::기사선택::</option>
                <?=$driver_options?>
            </select>
            <script>$('#mb_id_<?=$i?>').val('<?=$row['mb_id']?>');</script>
        </td>
        <td class="td_shp_dt font_size_8"><a href="?ser_st_date=<?=$row['shp_date']?>&ser_en_date=<?=$row['shp_date']?>"><?=$row['shp_dt']?></a></td>
        <td class="td_bom_part_no"><?=$row['bom']['bom_part_no']?></td>
        <td class="td_bom_name font_size_7"><?=$row['bom']['bom_name']?></td>
        <td class="td_shp_count">
            <input type="text" name="shp_count[<?=$i?>]" value="<?=number_format($row['shp_count'])?>" onclick="javascript:numtoprice(this)" class="frm_input moi_count wg_wdx60 wg_right">
        </td>
        <td class="td_shp_detail font_size_8"><a href="./order_list.php?sfl=ori_idx&stx=<?=$row['ori_idx']?>" target="_blank"><?=$row['ori_detail']?></a></td>
        <td class="td_shp_status" style="width:100px;">
            <select name="shp_status[<?=$i?>]" id="shp_status_<?=$i?>">
            <?=$g5['set_shp_status_value_options']?>
            </select>
            <script>$('#shp_status_<?=$i?>').val('<?=(($row['shp_status'])?$row['shp_status']:'pending')?>');</script>
        </td>
        <td class="td_mng">
			<?=(in_array($row['shp_status'],array('pending','confirm')))?$s_copy:''?><!-- 복제 -->
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
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02" style="display:no ne;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if (!auth_check($auth[$sub_menu],'w',1)) { ?>
        <a href="./<?=$fname?>_form.php" class="btn btn_01">추가하기</a>
    <?php } ?>

</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
$(function(e) {
    $("#ser_st_date, #ser_en_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

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