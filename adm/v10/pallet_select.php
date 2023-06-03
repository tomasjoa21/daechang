<?php
// 호출페이지들
// /adm/v10/item_form.php
// /adm/v10/material_form.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

$sql_common = " FROM {$g5['pallet_table']} AS plt ";

$where = array();
// 디폴트 검색조건
$where[] = " plt_status NOT IN ('trash','delete') ";

// 업체조건 (관리 권한이 있는 경우)
$where[] = " com_idx IN (".$_SESSION['ss_com_idx'].") ";

// 검색어 설정
if ($sch_word != "") {
    switch ($sch_field) {
		case ( $sch_field == 'plt_type' ) :
			$where[] = " plt_keys REGEXP 'plt_type=[가-힝]*(".trim($sch_word).")+[가-힝]*:' ";
            break;
		case ( $sch_field == 'com_idx' ) :
			$where[] = " mms.com_idx = '".trim($sch_word)."' ";
            break;
        default :
			$where[] = " $sch_field LIKE '%".trim($sch_word)."%' ";
            break;
    }
}
else
    $sch_field = 'plt_idx';

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


// 정렬기준
$sql_order = " ORDER BY plt_idx DESC ";


// 테이블의 전체 레코드수
$sql = " SELECT COUNT(*) AS cnt " . $sql_common . $sql_search;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$config['cf_write_pages'] = $config['cf_mobile_pages'] = 5;

// 리스트 쿼리
$sql = "SELECT *
        " . $sql_common . $sql_search . $sql_order . "
        LIMIT $from_record, $rows
";
// echo $sql.BR;
$result = sql_query($sql,1);

$qstr = 'frm='.$frm.'&file_name='.$file_name.'&com_idx='.$com_idx;
$qstr1 = $qstr.'&sch_field='.$sch_field.'&sch_word='.urlencode($sch_word);

$g5['title'] = '파레트 ('.number_format($total_count).')';
include_once('./_head.sub.php');
?>

<div id="sch_target_frm" class="new_win scp_new_win">
    <h1><?php echo $g5['title'];?></h1>

    <form name="ftarget" method="get">
    <input type="hidden" name="file_name" value="<?php echo $_REQUEST['file_name']; ?>">

    <div id="scp_list_find">
        <select name="sch_field" id="sch_field">
            <option value="plt_idx">파레트번호</option>
            <option value="plt_barcode">파레트바코드</option>
        </select>
        <script>$('select[name=sch_field]').val('<?php echo $sch_field?>').attr('selected','selected')</script>
        <input type="text" name="sch_word" id="sch_word" value="<?php echo get_text($sch_word); ?>" class="frm_input required" required size="20">
        <input type="submit" value="검색" class="btn_frmline">
        <a href="<?php echo $_SERVER['SCRIPT_NAME']?>?<?php echo $qstr?>" class="btn btn_b10">검색취소</a>
    </div>
    
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th scope="col">파레트번호</th>
            <th scope="col">바코드</th>
            <th scope="col">갯수</th>
            <th scope="col">상태</th>
            <th scope="col">선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            // print_r2($row);
        ?>
        <tr>
            <td class="td_plt_idx"><?php echo $row['plt_idx']; ?></td>
            <td class="td_plt_barcode"><?php echo $row['plt_barcode']; ?></td>
            <td class="td_plt_count"><?php echo $row['plt_count']; ?></td>
            <td class="td_plt_status"><?=$g5['set_plt_status_value'][$row['plt_status']]?></td>
            <td class="td_mng td_mng_s" plt_idx="<?php echo $row['plt_idx']; ?>"
                                        plt_name="<?php echo $row['plt_name']; ?>">
                <button type="button" class="btn btn_03 btn_select">선택</button>
            </td>
        </tr>
        <?php
        }
        if($i ==0)
            echo '<tr><td colspan="6" class="empty_table">검색된 자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
    </form>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr1.'&amp;page='); ?>

    <div class="win_btn ">
        <button type="button" onclick="window.close();" class="btn btn_close">창닫기</button>
    </div>

</div>

<script>
$('.btn_select').click(function(e){
    e.preventDefault();
    var plt_idx = $(this).closest('td').attr('plt_idx');
    var plt_count = $(this).closest('td').attr('plt_count');  // 

    <?php
    if($file_name=='item_form'
        ||$file_name=='material_form'
        ||$file_name=='plt_item_form'
    ) {
    ?>
        $("input[name=plt_idx]", opener.document).val( plt_idx );
    <?php
    }
    ?>

    window.close();
});
</script>

<?php
include_once('./_tail.sub.php');
?>