<?php
// 호출페이지들
// /adm/v10/material_form.php
// /adm/v10/production_item_count_form.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$stx = isset($_REQUEST['stx']) ? clean_xss_tags($_REQUEST['stx'], 1, 1) : '';


$g5['title'] = '생산실행(아이템)검색';
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['production_item_table']} AS pri
                LEFT JOIN {$g5['production_table']} AS prd USING(prd_idx)
                LEFT JOIN {$g5['bom_table']} AS bom ON pri.bom_idx = bom.bom_idx
";
$sql_where = " WHERE pri_status NOT IN ('delete','trash') ";

if($ser_date) {
    $sql_where .= " AND prd_start_date = '".$ser_date."' ";
}

if($item=="product") {
    $sql_where .= " AND pri_type = 'customer' ";
}
else if($item=="provider") {
    $sql_where .= " AND pri_type = 'provider' ";
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
        ORDER BY pri_reg_dt DESC
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
    <div id="scp_list_find">
        <input type="text" name="ser_date" value="<?=$ser_date?>" class="frm_input" style="width:90px;" placeholder="날짜">
        <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="frm_input required" required placeholder="품번 or 품명">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>품번/품명</th>
            <th>생산일</th>
            <th>지시량</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            // $row['bom'] = get_table('bom','bom_idx',$row['bom_idx']);
            $row['mms'] = get_table('mms','mms_idx',$row['mms_idx']);
            // print_r2($row['mms']);
        ?>
        <tr>
            <td class="td_bom_part_name td_left">
                <?=get_text($row['bom_part_no'])?>
                <p class="font_size_8"><?=$row['bom_name']?></p>
            </td>
            <td class="td_prd_start_ate td_left">
                <?=get_text($row['prd_start_date'])?>
                <div class="font_size_7"><?=$row['mms']['mms_name']?></div>
            </td>
            <td class="td_pri_value">
                <?=number_format($row['pri_value'])?>
            </td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    prd_idx="<?=$row['prd_idx']?>"
                    pri_idx="<?=$row['pri_idx']?>"
                    pri_value="<?=$row['pri_value']?>"
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
$("input[name$=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99",closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });

$('.btn_select').click(function(e){
    e.preventDefault();
    var prd_idx = $(this).attr('prd_idx');
    var pri_idx = $(this).attr('pri_idx');
    var pri_value = $(this).attr('pri_value');

    <?php
    if($file_name=='material_form'||$file_name=='production_item_count_form') {
    ?>
        $("input[name=prd_idx]", opener.document).val( prd_idx );
        $("input[name=pri_idx]", opener.document).val( pri_idx );
    <?php
    }
    else if($file_name=='item_form') {
    ?>
        $("input[name=pri_idx]", opener.document).val( pri_idx );
    <?php
    }
    ?>

    // 창닫기
    window.close();
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');