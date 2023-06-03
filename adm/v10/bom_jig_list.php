<?php
$sub_menu = "940130";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " FROM {$g5['bom_jig_table']} AS boj
                LEFT JOIN {$g5['mms_table']} AS mms ON mms.mms_idx = boj.mms_idx
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = boj.bom_idx
";

$where = array();
$where[] = " boj_status NOT IN ('trash','delete') ";   // 디폴트 검색조건

// 해당 업체만
// $where[] = " boj.com_idx = '".$_SESSION['ss_com_idx']."' ";


// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'boj_idx' || $sfl == 'boj.mms_idx' || $sfl == 'boj.bom_idx' || $sfl == 'bom_part_no' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'boj_hp' ) :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}


// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


if (!$sst) {
    $sst = "boj_reg_dt";
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

$g5['title'] = '설비별지그관리';
include_once('./_top_menu_mms.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql = " SELECT * {$sql_common} {$sql_search} {$sql_order} LIMIT {$from_record}, {$rows} ";
// print_r3($sql);
$result = sql_query($sql);

$colspan = 16;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
    <option value="mms_name"<?php echo get_selected($_GET['sfl'], "mms_name"); ?>>설비명</option>
    <option value="boj.mms_idx"<?php echo get_selected($_GET['sfl'], "boj.mms_idx"); ?>>설비번호</option>
    <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>제품명</option>
    <option value="boj.bom_idx"<?php echo get_selected($_GET['sfl'], "boj.bom_idx"); ?>>BOMidx</option>
    <option value="boj_code"<?php echo get_selected($_GET['sfl'], "boj_code"); ?>>지그명</option>
    <option value="boj_plc_ip"<?php echo get_selected($_GET['sfl'], "boj_plc_ip"); ?>>아이피</option>
    <option value="boj_plc_port"<?php echo get_selected($_GET['sfl'], "boj_plc_ip"); ?>>포트</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>PLC NO는 엑셀의 번호와 차이가 있습니다. (엑셀은 1부터 시작, 배열번호는 0부터 시작)</p>
    <p>공유 엑셀을 참고하세요. <a href="https://docs.google.com/spreadsheets/d/1baQOZuue_rMJ2xiY1DhqHxFDAfeefK_94llKKmPBEO8/edit?usp=sharing" target="_blank">바로가기</a></p>
</div>


<form name="fmemberlist" id="fmemberlist" action="./bom_jig_list_update.php" onsubmit="return form01_submit(this);" method="post">
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
        <th scope="col" id="boj_list_chk">
            <label for="chkall" class="sound_only">항목 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">설비명</th>
        <th scope="col">지그명</th>
        <th scope="col">품번</th>
        <th scope="col">품명</th>
        <th scope="col"><?php echo subject_sort_link('boj_plc_ip') ?>PLC IP</a></th>
        <th scope="col"><?php echo subject_sort_link('boj_plc_port') ?>PLC Port</a></th>
        <th scope="col"><?php echo subject_sort_link('boj_plc_no') ?>PLC No</a></th>
        <th scope="col"><?php echo subject_sort_link('boj_status') ?>상태</a></th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $s_mod = '<a href="./bom_jig_form.php?'.$qstr.'&amp;w=u&amp;boj_idx='.$row['boj_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['boj_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="boj_idx[<?php echo $i ?>]" value="<?php echo $row['boj_idx'] ?>" id="boj_idx_<?php echo $i ?>">
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_mms_name"><a href="?sfl=boj.mms_idx&stx=<?=$row['mms_idx']?>"><?php echo get_text($row['mms_name']); ?></a></td>
        <td class="td_boj_code"><?php echo get_text($row['boj_code']); ?></td>
        <td class="td_bom_part_no"><a href="?sfl=bom_part_no&stx=<?=$row['bom_part_no']?>"><?=$row['bom_part_no']?></a></td>
        <td class="td_bom_name"><a href="?sfl=boj.bom_idx&stx=<?=$row['bom_idx']?>"><?php echo get_text($row['bom_name']); ?></a></td>
        <td class="td_boj_plc_ip"><a href="?sfl=boj_plc_ip&stx=<?=$row['boj_plc_ip']?>"><?=$row['boj_plc_ip']?></a></td>
        <td class="td_boj_plc_port"><a href="?sfl=boj_plc_port&stx=<?=$row['boj_plc_port']?>"><?=$row['boj_plc_port']?></a></td>
        <td class="td_boj_plc_no"><?=$row['boj_plc_no']?></td>
        <td class="td_boj_status"><?php echo $g5['set_boj_status_value'][$row['boj_status']] ?></td>
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
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if (!auth_check($auth[$sub_menu],'w',1)) { ?>
        <a href="./bom_jig_form.php" id="member_add" class="btn btn_01">추가하기</a>
    <?php } ?>

</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
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
