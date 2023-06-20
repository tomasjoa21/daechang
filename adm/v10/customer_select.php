<?php
// 호출페이지들
// /adm/v10/order_form.php
// /adm/v10/bom_form.php
// /adm/v10/shipment_form.php
// /adm/v10/item_stock_list.php
// /adm/v10/item_today_list.php
// /adm/v10/item_form.php
// /adm/v10/material_stock_list.php
// /adm/v10/material_list.php
// /adm/v10/material_form.php
// /adm/v10/production_item_count_list.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$stx = isset($_REQUEST['stx']) ? clean_xss_tags($_REQUEST['stx'], 1, 1) : '';

$html_title = '거래처검색';

$g5['title'] = $html_title;
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['customer_table']} ";
$sql_where = " WHERE cst_status NOT IN ('delete','trash') ";

if($item=="customer") {
    $sql_where .= " AND cst_type = 'customer' ";
}
else if($item=="provider") {
    $sql_where .= " AND cst_type = 'provider' ";
}

if($stx){
    $stx = preg_replace('/\!\?\*$#<>()\[\]\{\}/i', '', strip_tags($stx));
    $sql_where .= " AND (cst_name LIKE '%".sql_real_escape_string($stx)."%' OR cst_names LIKE '%".sql_real_escape_string($stx)."%') ";
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
        ORDER BY cst_reg_dt DESC
        LIMIT $from_record, $rows
";
// echo $sql.'<br>';
$result = sql_query($sql);

$qstr1 = 'stx='.urlencode($stx).'&file_name='.$file_name.'&item='.$item;
?>

<div id="sch_member_frm" class="new_win scp_new_win">
    <h1><?=$html_title?></h1>

    <form name="fmember" method="get">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" class="frm_input">
    <input type="hidden" name="item" value="<?php echo $item; ?>" class="frm_input">
    <div id="scp_list_find">
        <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="frm_input required" required placeholder="검색어">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>업체명</th>
            <th>대표자</th>
            <th>구분</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
        ?>
        <tr>
            <td class="td_cst_name"><?php echo get_text($row['cst_name']); ?></td>
            <td class="td_cst_president"><?php echo $row['cst_president']; ?></td>
            <td class="td_cst_type"><?=$g5['set_cst_type_value'][$row['cst_type']]; ?></td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    cst_idx="<?=$row['cst_idx']?>"
                    cst_name="<?=$row['cst_name']?>"
                    cst_president="<?=$row['cst_president']?>"
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
    var cst_idx = $(this).attr('cst_idx');
    var cst_name = $(this).attr('cst_name');
    var cst_president = $(this).attr('cst_president');

    <?php
    if($file_name=='order_form'||$file_name=='shipment_form'||$file_name=='material_order_form') {
    ?>
        $("input[name=cst_idx]", opener.document).val( cst_idx );
        $("input[name=cst_name]", opener.document).val( cst_name );
    <?php
    }
	// 계약관리 수정
	else if($file_name=='bom_form') {
		if($item=='customer') {
    ?>
			$("input[name=cst_idx_customer]", opener.document).val( cst_idx );
			$("input[name=cst_name_customer]", opener.document).val( cst_name );
		<?php
		}
		else if ($item=='provider') {
		?>
			$("input[name=cst_idx_provider]", opener.document).val( cst_idx );
			$("input[name=cst_name_provider]", opener.document).val( cst_name );
    <?php
		}
    }
	else if($file_name=='item_stock_list'||$file_name=='item_today_list'||$file_name=='material_stock_list'||$file_name=='production_item_count_list'
            ||$file_name=='material_list') {
		if($item=='customer') {
    ?>
			$("input[name=ser_cst_idx_customer]", opener.document).val( cst_idx );
			$("input[name=cst_name_customer]", opener.document).val( cst_name );
		<?php
		}
		else if ($item=='provider') {
		?>
			$("input[name=ser_cst_idx_provider]", opener.document).val( cst_idx );
			$("input[name=cst_name_provider]", opener.document).val( cst_name );
    <?php
		}
    }
	else if($file_name=='item_form'||$file_name=='material_form') {
		if($item=='customer') {
    ?>
			$("input[name=cst_idx_customer]", opener.document).val( cst_idx );
			$("input[name=cst_name_customer]", opener.document).val( cst_name );
		<?php
		}
		else if ($item=='provider') {
		?>
			$("input[name=cst_idx_provider]", opener.document).val( cst_idx );
			$("input[name=cst_name_provider]", opener.document).val( cst_name );
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