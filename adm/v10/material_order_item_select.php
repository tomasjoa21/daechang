<?php
// 호출페이지들
// /adm/v10/material_form.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$stx = isset($_REQUEST['stx']) ? clean_xss_tags($_REQUEST['stx'], 1, 1) : '';


$g5['title'] = '발주제품검색';
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['material_order_item_table']} AS moi
                LEFT JOIN {$g5['material_order_table']} AS mto USING(mto_idx)
                LEFT JOIN {$g5['bom_table']} AS bom ON moi.bom_idx = bom.bom_idx
";
$sql_where = " WHERE moi_status NOT IN ('delete','trash') ";

if($ser_cst_idx) {
    $sql_where .= " AND cst_idx = '".$ser_cst_idx."' ";
}

if($ser_date) {
    $sql_where .= " AND prd_start_date = '".$ser_date."' ";
}

if($item=="product") {
    $sql_where .= " AND moi_type = 'customer' ";
}
else if($item=="provider") {
    $sql_where .= " AND moi_type = 'provider' ";
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
        ORDER BY moi_reg_dt DESC
        LIMIT $from_record, $rows
";
// echo $sql.'<br>';
$result = sql_query($sql);

$qstr1 = 'stx='.urlencode($stx).'&file_name='.$file_name.'&item='.$item;
?>
<style>
.cst_name {margin-left:10px;}
</style>
<div id="sch_member_frm" class="new_win scp_new_win">
    <h1><?=$g5['title']?></h1>

    <form name="fmember" method="get">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" class="frm_input">
    <input type="hidden" name="item" value="<?php echo $item; ?>" class="frm_input">
    <div id="scp_list_find">
        <select name="ser_cst_idx" id="ser_cst_idx" style="width:150px;">
            <option value="">고객사전체</option>
            <?php
            $sql = "SELECT cst_idx, cst_name FROM {$g5['customer_table']} WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND cst_type = 'provider' ORDER BY cst_idx ";
            // echo $sql.'<br>';
            $rs = sql_query($sql,1);
            for ($i=0; $row=sql_fetch_array($rs); $i++) {
                // print_r2($row);
                echo '<option value="'.$row['cst_idx'].'" '.get_selected($ser_cst_idx, $row['cst_idx']).'>'.$row['cst_name'].'</option>';
            }
            ?>
        </select>
        <script>$('select[name=ser_cst_idx]').val("<?=$ser_cst_idx?>").attr('selected','selected');</script>
        <input type="text" name="ser_date" value="<?=$ser_date?>" class="frm_input" style="width:90px;" placeholder="날짜">
        <input type="text" name="stx" id="stx" value="<?php echo get_text($stx); ?>" class="frm_input" style="width:150px;" placeholder="품번 or 품명">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>품번/품명</th>
            <th>납기일</th>
            <th>발주수량</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            $row['cst_provider'] = get_table('customer','cst_idx',$row['cst_idx'],'cst_name');
            // print_r2($row['cst_provider']);
        ?>
        <tr>
            <td class="td_bom_part_name td_left">
                <?=get_text($row['bom_part_no'])?>
                <span class="cst_name font_size_7"><?=$row['cst_provider']['cst_name']?></span>
                <p class="font_size_8"><?=$row['bom_name']?></p>
            </td>
            <td class="td_moi_input_date font_size_7">
                <?=get_text($row['moi_input_date'])?>
            </td>
            <td class="td_moi_count">
                <?=number_format($row['moi_count'])?>
            </td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    mot_idx="<?=$row['mot_idx']?>"
                    moi_idx="<?=$row['moi_idx']?>"
                    moi_count="<?=$row['moi_count']?>"
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
    var moi_idx = $(this).attr('moi_idx');
    var moi_value = $(this).attr('moi_value');

    <?php
    if($file_name=='material_form') {
    ?>
        $("input[name=prd_idx]", opener.document).val( prd_idx );
        $("input[name=moi_idx]", opener.document).val( moi_idx );
    <?php
    }
    else if($file_name=='item_form') {
    ?>
        $("input[name=moi_idx]", opener.document).val( moi_idx );
    <?php
    }
    ?>

    // 창닫기
    window.close();
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');