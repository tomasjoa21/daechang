<?php
// 호출페이지들
// /adm/v10/shipment_form.php
// /adm/v10/item_form.php
// /adm/v10/material_form.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$stx = isset($_REQUEST['stx']) ? clean_xss_tags($_REQUEST['stx'], 1, 1) : '';


$g5['title'] = '수주제품검색';
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['order_item_table']} AS ori
                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = ori.bom_idx
";
$sql_where = " WHERE ori_status NOT IN ('delete','trash') ";

if($ser_cst_idx) {
    $sql_where .= " AND cst_idx = '".$ser_cst_idx."' ";
}

if($item=="product") {
    $sql_where .= " AND ori_type = 'customer' ";
}
else if($item=="provider") {
    $sql_where .= " AND ori_type = 'provider' ";
}

if($stx){
    $stx = preg_replace('/\!\?\*$#<>()\[\]\{\}/i', '', strip_tags($stx));
    $sql_where .= " AND (bom_part_no = '".sql_real_escape_string($stx)."' OR bom_name LIKE '%".sql_real_escape_string($stx)."%') ";
}

// 테이블의 전체 레코드수만 얻음
$sql = " SELECT COUNT(*) AS cnt " . $sql_common . $sql_where;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
            $sql_common
            $sql_where
        ORDER BY ori_reg_dt DESC
        LIMIT $from_record, $rows
";
// echo $sql.'<br>';
$result = sql_query($sql);

$qstr1 = 'stx='.urlencode($stx).'&file_name='.$file_name.'&item='.$item;
?>

<div id="sch_member_frm" class="new_win scp_new_win">
    <h1><?=$g5['title']?></h1>

    <form name="fmember" method="get">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" class="frm_input">
    <input type="hidden" name="item" value="<?php echo $item; ?>" class="frm_input">
    <script>$('select[name=ser_cst_idx]').val("<?=$ser_cst_idx?>").attr('selected','selected');</script>
    <div id="scp_list_find">
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
        <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="frm_input required" required placeholder="품번 or 품명">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>고객사</th>
            <th>품번/품명</th>
            <th>수량</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            $row['cst'] = get_table('customer','cst_idx',$row['cst_idx']);
        ?>
        <tr>
            <td class="td_cst_name td_left">
                <?=get_text($row['cst']['cst_name'])?>
            </td>
            <td class="td_bom_part_name td_left">
                <?=get_text($row['bom_part_no'])?>
                <p class="font_size_8"><?=$row['bom_name']?></p>
            </td>
            <td class="td_ori_count">
                <?=number_format($row['ori_count'])?>
            </td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    cst_idx="<?=$row['cst_idx']?>"
                    cst_name="<?=$row['cst']['cst_name']?>"
                    ori_idx="<?=$row['ori_idx']?>"
                    bom_part_no="<?=$row['bom_part_no']?>"
                    bom_name="<?=$row['bom_name']?>"
                    ori_count="<?=$row['ori_count']?>"
                >선택</button>
            </td>
        </tr>
        <?php
        }

        if($i ==0)
            echo '<tr><td colspan="4" class="empty_table">검색된 자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
    </form>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr1.'&amp;page='); ?>

</div>

<script>
$('.btn_select').click(function(e){
    e.preventDefault();
    var ori_idx = $(this).attr('ori_idx');
    var bom_part_no = $(this).attr('bom_part_no');
    var bom_name = $(this).attr('bom_name');
    var cst_idx = $(this).attr('cst_idx');
    var cst_name = $(this).attr('cst_name');
    var ori_count = $(this).attr('ori_count');

    <?php
    if($file_name=='shipment_form') {
    ?>
        $("input[name=ori_idx]", opener.document).val( ori_idx );
        $("input[name=cst_idx]", opener.document).val( cst_idx );
        $("input[name=cst_name]", opener.document).val( cst_name );
        var ori_detail = cst_name+', <b>'+bom_part_no+'</b> (품명:'+bom_name+'), 수량:<b>'+ori_count+'</b>';
        $(".span_ori_detail", opener.document).html( ori_detail );
    <?php
    }
    else if($file_name=='item_form'||$file_name=='material_form') {
    ?>
        $("input[name=ori_idx]", opener.document).val( ori_idx );
    <?php
    }
	// 계약관리 수정
	else if($file_name=='ori_form') {
		if($item=='customer') {
    ?>
			$("input[name=ori_idx_customer]", opener.document).val( ori_idx );
			$("input[name=bom_name_customer]", opener.document).val( bom_name );
		<?php
		}
		else if ($item=='provider') {
		?>
			$("input[name=ori_idx_provider]", opener.document).val( ori_idx );
			$("input[name=bom_name_provider]", opener.document).val( bom_name );
    <?php
		}
    }
    ?>

    // 창닫기
    window.close();
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');