<?php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

/*
$sql_common = " FROM {$g5['production_item_table']} pri
                    LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
                    LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
                    INNER JOIN {$g5['bom_item_table']} boi ON bom.bom_idx = boi.bom_idx
                    INNER JOIN {$g5['bom_table']} itm ON boi.bom_idx_child = itm.bom_idx
";
*/
$sql_common = " FROM {$g5['item_table']} itm
                    LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
                    LEFT JOIN {$g5['production_item_table']} pri ON itm.pri_idx = pri.pri_idx
                    LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
";

$where = array();
$where[] = " itm.itm_status NOT IN ('delete','del','trash') ";
$where[] = " itm.com_idx = '".$_SESSION['ss_com_idx']."' ";


// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'pri.pri_idx' || $sfl == 'bom.bom_part_no' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

$sql_group = " GROUP BY pri.pri_idx,itm.bom_idx ";

if (!$sst) {
    $sst = "prd.prd_start_date";
    $sod = "desc";
}

if (!$sst2) {
    $sst2 = ", pri.pri_idx";
    $sod2 = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} {$sst2} {$sod2} ";

// $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_group} ";
$sql = " SELECT COUNT(c.bom_idx) AS cnt FROM (
    SELECT itm.bom_idx {$sql_common} {$sql_search} {$sql_group}
) c ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
// $rows = 50;//10
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함
//mms_idx,bom_idx_parent,mtr_weight,mtr_heat,mtr_lot,mtr_bundle
$sql = "SELECT pri.pri_idx
            ,prd.prd_idx
            ,prd.prd_start_date
            ,pri.mms_idx
            ,pri.bom_idx
            ,pri.pri_value
            ,bom.bom_part_no
            ,bom.bom_name
            ,MAX(itm_date) AS itm_date
            ,MAX(itm_delivery_dt) AS itm_delivery_dt
            ,MAX(itm_reg_dt) AS itm_reg_dt
            ,MAX(itm_update_dt) AS itm_update_dt
        {$sql_common} {$sql_search} {$sql_group} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r2($sql);

$result = sql_query($sql,1); //$result->num_rows
$row = array();
for($i=0;$arr=sql_fetch_array($result);$arr++){   
    array_push($row,$arr);
}
// echo count($row);
$qstr .= '&sca='.$sca.'&fname='.$fname; // 추가로 확장해서 넘겨야 할 변수들

$g5['title'] = '생산계획리스트 ('.number_format($total_count).')';
include_once('./_head.sub.php');
?>
<style>
.scp_frame {padding:10px;}
.sp_cat{color:orange;font-size:0.85em;}
.sp_pno{color:skyblue;font-size:0.85em;}
.sp_std{color:#e87eee;font-size:0.85em;}
.new_frame_con {margin-top:10px;height:564px;overflow-y:auto;padding-bottom:25px;}
.tbl_head01 thead tr th{position:sticky;top:-1px;z-index:100;}
.td_bom_name
,.td_bom_part_no
,.td_com_name
 {text-align:left !important;}
.td_bom_price {text-align:right !important;}
</style>

<div id="sch_target_frm" class="new_win scp_frame">

    <form name="ftarget" method="get">
    <input type="hidden" name="frm" value="<?php echo $_GET['frm']; ?>">
    <input type="hidden" name="fname" value="<?php echo $_REQUEST['fname']; ?>">
    <input type="hidden" name="com_idx" value="<?php echo $_REQUEST['com_idx']; ?>">

    <div id="div_search" style="display:no ne;">
        <select name="sfl" id="sfl">
            <option value="pri.pri_idx"<?php echo get_selected($_GET['sfl'], "pri.pri_idx"); ?>>생산계획ID</option>
            <option value="bom.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom.bom_part_no"); ?>>완제품품번</option>
            <option value="bom.bom_name"<?php echo get_selected($_GET['sfl'], "bom.bom_name"); ?>>완제품명</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input" style="width:160px;">
        <input type="submit" value="검색" class="btn_frmline">
        <a href="<?php echo $_SERVER['SCRIPT_NAME']?>?fname=<?=$fname?>" class="btn btn_b10">취소</a>
    </div>
    
    <div class="tbl_head01 tbl_wrap new_frame_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th scope="col"><?php echo subject_sort_link('oop_idx') ?>생산계획ID</a></th>
            <th scope="col">완제품정보</th>
            <th scope="col">생산시작일</th>
            <th scope="col">선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $i<count($row); $i++) {
            $bg = 'bg'.($i%2);
           
        ?>
        <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['bom_idx'] ?>">
            <td class="td_pri_idx"><?=$row[$i]['pri_idx']?></td>
            <td class="td_bom_name">
                <b><?=$row[$i]['bom_name']?></b>
                <?php if($row[$i]['bom_part_no']){ ?><br><span class="sp_pno">[ <?=$row[$i]['bom_part_no']?> ]</span><?php } ?>
            </td><!-- 완제품정보 -->
            <td class="td_prd_start_date">
                <?=substr($row[$i]['prd_start_date'],2,8)?>
                <?php if($row[$i]['pri_value']){ ?>
                    <br>(<?=number_format($row[$i]['pri_value'])?>)
                <?php } ?>
            </td>
            <td class="td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    prd_idx="<?=$row[$i]['prd_idx']?>"
                    pri_idx="<?=$row[$i]['pri_idx']?>"
                    mms_idx="<?=$row[$i]['mms_idx']?>"
                    bom_idx="<?=$row[$i]['bom_idx']?>"
                    bom_name="<?=$row[$i]['bom_name']?>"
                    bom_part_no="<?=$row[$i]['bom_part_no']?>"
                    itm_delivery_dt="<?=$row[$i]['itm_delivery_dt']?>"
                    itm_date="<?=$row[$i]['itm_date']?>"
                    itm_reg_dt="<?=$row[$i]['itm_reg_dt']?>"
                    itm_update_dt="<?=$row[$i]['itm_update_dt']?>"
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

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;fname='.$fname.'&amp;page='); ?>

</div>

<script>
var fname = '<?=$fname?>';
var i = <?=$i?>;
// if(i == 0){
//     alert('해당 제품의 출하데이터가 없습니다.\n출하데이터를 먼저 등록해 주세요.');
//     window.close();
// }
$('.btn_select').click(function(e){
    e.preventDefault();
    var prd_idx = $(this).attr('prd_idx');
    var pri_idx = $(this).attr('pri_idx');
    var mms_idx = $(this).attr('mms_idx');
    var bom_idx = $(this).attr('bom_idx');
    var bom_name = $(this).attr('bom_name');
    var bom_part_no = $(this).attr('bom_part_no');
    var itm_delivery_dt = $(this).attr('itm_delivery_dt');
    var itm_date = $(this).attr('itm_date');
    var itm_reg_dt = $(this).attr('itm_reg_dt');
    var itm_update_dt = $(this).attr('itm_update_dt');
    // alert(fname);return false;
    if(fname == 'item_status_list'){
        // alert(oop_idx);return false;
        $("input[name=prd_idx]", opener.document).val( prd_idx );
        $("input[name=pri_idx]", opener.document).val( pri_idx );
        $("input[name=mms_idx]", opener.document).val( mms_idx );
        $("input[name=bom_part_no]", opener.document).val( bom_part_no );
        $("input[name=bom_idx]", opener.document).val( bom_idx );
        $("input[name=bom_name]", opener.document).val( bom_name );
        $("input[name=itm_delivery_dt]", opener.document).val( itm_delivery_dt );
        $("input[name=itm_date]", opener.document).val( itm_date );
        $("input[name=itm_reg_dt]", opener.document).val( itm_reg_dt );
        $("input[name=itm_update_dt]", opener.document).val( itm_update_dt );
    }
    

    window.close();
});
</script>

<?php
include_once('./_tail.sub.php');
?>