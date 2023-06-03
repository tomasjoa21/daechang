<?php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$stx = isset($_REQUEST['stx']) ? clean_xss_tags($_REQUEST['stx'], 1, 1) : '';

$html_title = '발주검색';

$g5['title'] = $html_title;
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['material_order_table']} mto
                LEFT JOIN {$g5['customer_table']} cst ON mto.cst_idx = cst.cst_idx ";
$sql_where = " WHERE mto_status NOT IN ('delete','trash') ";


if($stx){
    $stx = preg_replace('/\!\?\*$#<>()\[\]\{\}/i', '', strip_tags($stx));
    $sql_where .= " AND (cst.cst_name LIKE '%".sql_real_escape_string($stx)."%' OR cst.cst_names LIKE '%".sql_real_escape_string($stx)."%') ";
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
        ORDER BY mto_reg_dt DESC
        LIMIT $from_record, $rows
";
// echo $sql.'<br>';
$result = sql_query($sql);

$qstr1 = 'stx='.urlencode($stx).'&file_name='.$file_name;
?>
<div id="sch_member_frm" class="new_win scp_new_win">
    <h1><?=$html_title?></h1>

    <form name="fmember" method="get">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" class="frm_input">
    <div id="scp_list_find">
        <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="frm_input required" required placeholder="검색어">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>ID</th>
            <th>업체명</th>
            <th>입고일</th>
            <th>등록일시</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
        ?>
        <tr>
            <td class="td_mto_idx"><?=$row['mto_idx']?></td>
            <td class="td_cst_name"><?=$row['cst_name']?></td>
            <td class="td_mto_input_date"><?=$row['mto_input_date']?></td>
            <td class="td_mto_reg_dt"><?=$row['mto_reg_dt']?></td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    mto_idx="<?=$row['mto_idx']?>"
                    cst_idx="<?=$row['cst_idx']?>"
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
    var mto_idx = $(this).attr('mto_idx');
    var cst_idx = $(this).attr('cst_idx');

    <?php
    if($file_name=='material_order_form') {
    ?>
        $("input[name=mto_idx]", opener.document).val( mto_idx );
        $("input[name=cst_idx]", opener.document).val( cst_idx );
        var bom_href = $(".btn_bom", opener.document).attr('link');
        bom_href = bom_href + '&cst_idx=' + cst_idx;
        $(".btn_bom", opener.document).attr('href','').attr('href', bom_href);
        $("#bom_idx", opener.document).val('');
        $("#bom_name", opener.document).val('');

    <?php
    }
    ?>
    // 창닫기
    window.close();
});
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');