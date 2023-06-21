<?php
$sub_menu = "922145";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '제품상태관리';
include_once('./_head.php');
// include_once('./_top_menu_item.php');


$sql_common = " FROM {$g5['item_table']} itm
                    LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
                    LEFT JOIN {$g5['production_item_table']} pri ON itm.pri_idx = pri.pri_idx
                    LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
";

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " itm.itm_status NOT IN ('delete','del','trash') ";
$where[] = " itm.com_idx = '".$_SESSION['ss_com_idx']."' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'bom_idx' || $sfl == 'itm_idx' || $sfl == 'itm_borcode' || $sfl == 'itm_lot' || $sfl == 'itm_defect_type' || $sfl == 'trm_idx_location' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'bct_idx' ) :
			$where[] = " {$sfl} LIKE '".trim($stx)."%' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

if($mtr_date){
    $where[] = " itm_date = '".$itm_date."' ";
    $qstr .= $qstr.'&itm_date='.$itm_date;
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

// $sql_group = " GROUP BY itm.bom_idx, itm_date ";
$sql_group = " GROUP BY pri.pri_idx,itm.bom_idx ";

if (!$sst) {
    $sst = "prd_start_date";
    $sod = "desc";
}

if (!$sst2) {
    $sst2 = ", pri.pri_idx";
    $sod2 = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} {$sst2} {$sod2} ";

// $sql = " SELECT COUNT(DISTINCT itm.bom_idx, itm_date) as cnt {$sql_common} {$sql_search} ";
$sql = " SELECT COUNT(c.bom_idx) AS cnt FROM (
            SELECT itm.bom_idx {$sql_common} {$sql_search} {$sql_group}
        ) c ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];
// echo $total_count.'<br>';

$rows = 20;//$config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$sql = "SELECT itm.itm_name
              ,itm.bom_idx
              ,pri.pri_idx
              ,pri.prd_idx
              ,bom.bom_part_no
              ,prd.prd_start_date
              ,ROW_NUMBER() OVER (ORDER BY prd_start_date, pri.pri_idx) AS itm_num
              ,COUNT(*) AS cnt
              ,COUNT( CASE WHEN itm_defect_type = 'press' THEN 1 END ) AS error_press
              ,COUNT( CASE WHEN itm_defect_type = 'cut' THEN 1 END ) AS error_cut
              ,COUNT( CASE WHEN itm_defect_type = 'welding' THEN 1 END ) AS error_welding
              ,COUNT( CASE WHEN itm_defect_type = 'bending' THEN 1 END ) AS error_bending
              ,COUNT( CASE WHEN itm_defect_type = 'compress' THEN 1 END ) AS error_compress
              ,COUNT( CASE WHEN itm_defect_type = 'assemble' THEN 1 END ) AS error_assemble
              ,COUNT( CASE WHEN itm_defect_type = 'injection' THEN 1 END ) AS error_injection
              ,COUNT( CASE WHEN itm_defect_type = 'supply' THEN 1 END ) AS error_supply
              ,COUNT( CASE WHEN itm_defect_type = 'etc' THEN 1 END ) AS error_etc
              ,COUNT( CASE WHEN itm_status = 'rtn' THEN 1 END ) AS rtn
              ,COUNT( CASE WHEN itm_status = 'rfd' THEN 1 END ) AS rfd
              ,COUNT( CASE WHEN itm_status = 'scrap' THEN 1 END ) AS scrap
              ,COUNT( CASE WHEN itm_status = 'finish' THEN 1 END ) AS finish
              ,COUNT( CASE WHEN itm_status = 'delivery' THEN 1 END ) AS delivery
        {$sql_common} {$sql_search} {$sql_group}  {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// echo $sql;
$result = sql_query($sql,1);

$defect_arr = array();
foreach($g5['set_defect_type_value'] as $ek => $ev){
    $defect_arr['error_'.$ek] = $ev;
}
// print_r2($defect_arr);
$status_names = array_merge($g5['set_defect_type_arr'],$g5['set_itm_status_arr']);
$status_values = array_merge($defect_arr,$g5['set_itm_status_value']);
$status_options = '';
foreach($status_values as $sk => $sv){
    $status_options .= '<option value="'.$sk.'">'.$sv.'</option>';
}

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
// print_r2($g5['set_half_status_value']);
// print_r2($g5['set_half_status_ng_array']);
?>
<style>
#itm_data{position:relative;padding-bottom:10px;}
/*
#half_data #form02{position:absolute;right:0;top:-47px;}
*/
.b_fromto,.b_cnt{position:relative;top:2px;margin-right:5px;}

.tbl_head01 thead tr th{position:sticky;top:100px;z-index:100;}
.td_chk{position:relative;}
.td_chk .chkdiv_btn{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,255,0,0);}
.td_itm_name {text-align:left !important;}
.sp_pno{color:skyblue;font-size:0.85em;}
.sp_std{color:#e87eee;font-size:0.85em;}
.td_com_name, .td_itm_maker
,.td_itm_items, .td_itm_items_title {text-align:left !important;}
.span_itm_price {margin-left:20px;}
.span_itm_price b, .span_bit_count b {color:#737132;font-weight:normal;}
#modal01 table ol {padding-right: 20px;text-indent: -12px;padding-left: 12px;}
#modal01 form {overflow:hidden;}
.ui-dialog .ui-dialog-titlebar-close span {
    display: unset;
    margin: -8px 0 0 -8px;
}
.td_itm_history {width:190px !important;}
label[for="itm_static_date"]{position:relative;}
label[for="itm_static_date"] i{position:absolute;top:-10px;right:0px;z-index:2;cursor:pointer;}
.slt_label{position:relative;display:inline-block;}
.slt_label i{position:absolute;top:-7px;right:0px;z-index:2;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>
<?php
echo $g5['container_sub_title'];
?>
<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="itm_name"<?php echo get_selected($_GET['sfl'], "itm_name"); ?>>품명</option>
    <option value="bom.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
    <option value="oop.oop_idx"<?php echo get_selected($_GET['sfl'], "oop_idx"); ?>>생산계획ID</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">

</label>
<script>
<?php
$sfl = ($sfl == '') ? 'itm_name' : $sfl;
?>
$('#sfl').val('<?=$sfl?>');
$('#shift').val('<?=$shift?>');
$('#itm2_status').val('<?=$itm2_status?>');
</script>
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>생산계획ID별 따른 완제품 상태값조정 및 재고를 추가하는 페이지입니다.</p>
</div>

<script>
$('.data_blank').on('click',function(e){
    e.preventDefault();
    //$(this).parent().siblings('input').val('');
    var obj = $(this).parent().next();
    if(obj.prop("tagName") == 'INPUT'){
        if(obj.attr('type') == 'hidden'){
            obj.val('');
            obj.siblings('input').val('');
        }else if(obj.attr('type') == 'text'){
            obj.val('');
        }
    }else if(obj.prop("tagName") == 'SELECT'){
        obj.val('');
    }
});
//mms_idx,bom_idx_parent,mtr_weight,mtr_heat,mtr_lot,mtr_bundle
</script>
<div id="itm_data">
    <form name="form02" id="form02" action="./item_status_update.php" onsubmit="return form02_submit(this);" method="post" autocomplete="off">
        <strong style="position:relative;top:3px;">완제품재고 추가/변경:</strong>
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="sst2" value="<?php echo $sst2 ?>">
        <input type="hidden" name="sod2" value="<?php echo $sod2 ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="token" value="">

        <input type="hidden" name="prd_idx" value="">
        <input type="hidden" name="itm_date" value="">
        <input type="hidden" name="itm_delivery_dt" value="">
        <input type="hidden" name="itm_reg_dt" value="">
        <input type="hidden" name="itm_update_dt" value="">
        
        <input type="hidden" name="mms_idx" value="">
        <input type="text" name="pri_idx" value="" class="frm_input pri_select" link="./pri_itm_select.php?fname=<?=$g5['file_name']?>" readonly placeholder="생산계획ID">
        <input type="text" name="bom_part_no" value="" class="frm_input pri_select" link="./pri_itm_select.php?fname=<?=$g5['file_name']?>" readonly placeholder="품목코드">
        <input type="hidden" name="bom_idx" value="">
        <input type="text" name="bom_name" value="" class="frm_input pri_select" link="./pri_itm_select.php?fname=<?=$g5['file_name']?>" readonly placeholder="품명" style="width:300px;">
        <select name="plus_modify" class="plus_modify">
            <option value="plus">추가하기</option>
            <option value="modify">변경하기</option>
        </select>
        <span class="sp_from">
            <select name="from_status" class="from_status">
                <option value="">::기존상태::</option>
                <?=$status_options?>
            </select><b class="b_fromto b_from">(을)를</b>
        </span>
        <span>
            <select name="to_status" class="to_status">
                <option value="">::목표상태::</option>
                <?=$status_options?>
                <!-- <option value="trash">삭제</option> -->
            </select><b class="b_fromto b_to">(으)로</b>
        </span>
        <input type="text" name="count" class="frm_input count" value="" style="width:60px;text-align:right;" placeholder="갯수"><b class="b_cnt">개</b>
        <input type="submit" value="적용" class="btn_submit btn">
        <a href="javascript:" class="btn btn_04 btn_no">취소</a>
    </form>
</div>
<script>
$('.sp_from').hide();
$('.plus_modify').on('change',function(){
    if($(this).val()=='plus'){
        $('.sp_from').hide();
    }
    else if($(this).val()=='modify'){
        $('.sp_from').show();
    }
});
//숫자만 입력
$('.count').on('keyup',function(){
    $(this).val($(this).val().replace(/[^0-9|-]/g,""));
});
//생산계획선택
$('.pri_select').on('click',function(e){
    e.preventDefault();
    var href = $(this).attr('link');
    var winOrpSelect = window.open(href, "winOrpSelect", "left=300,top=150,width=650,height=700,scrollbars=1");
    winOrpSelect.focus();
    return false;
});
//취소
$('.btn_no').on('click',function(){
    $('input[name="prd_idx"],input[name="pri_idx"],input[name="bom_part_no"],input[name="bom_idx"],input[name="bom_name"],input[name="itm_delivery_dt"],input[name="itm_date"],input[name="itm_reg_dt"],input[name="itm_update_dt"]').val('');
    $('.plus_modify').val('plus');
    $('.from_status,.to_status').val('');
    $('.sp_from').hide();
    $('.count').val('');
});
</script>

<form name="form01" id="form01" action="./item_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sst2" value="<?php echo $sst2 ?>">
<input type="hidden" name="sod2" value="<?php echo $sod2 ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">번호</th>
        <th scope="col">생산계획ID</th>
        <th scope="col"><?php echo subject_sort_link('itm_name') ?>품명/품번</a></th>
        <th scope="col">생산시작일</th>
        <?php foreach($g5['set_defect_type_arr'] as $ng_name){ ?>
        <th scope="col">
            <?=$g5['set_defect_type_value'][$ng_name]?>
        </th>
        <?php } ?>
        <?php foreach($g5['set_itm_status_arr'] as $st_name){ ?>
        <th scope="col">
            <?=$g5['set_itm_status_value'][$st_name]?>
        </th>
        <?php } ?>
        <th scope="col" style="color:skyblue;">총생산갯수</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php
    
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $s_mod = '<a href="./item_form.php?'.$qstr.'&amp;w=u&amp;mtr_idx='.$row['mtr_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['itm_idx'] ?>">
        <td class="td_itm_num"><?=$row['itm_num']?></td><!-- 번호 -->
        <td class="td_pri_idx">
            <?=$row['pri_idx']?>
        </td><!-- 생산계회ID -->
        <td class="td_itm_name">
            <b><?=$row['itm_name']?></b>
            <?php if($row['bom_part_no']){ ?>
            <br><span class="sp_pno">[ <?=$row['bom_part_no']?> ]</span>
            <?php } ?>
        </td><!-- 품명 -->
        <td class="td_prd_start_date"><?=substr($row['prd_start_date'],2,8)?></td><!-- 생산시작일 -->
        <?php foreach($g5['set_defect_type_arr'] as $ng_name){ ?>
        <td class="td_itm_cnt"><?=(($row['error_'.$ng_name])?$row['error_'.$ng_name]:'-')?></td><!-- 재고개수 -->
        <?php } ?>
        <?php foreach($g5['set_itm_status_arr'] as $st_name){ ?>
        <td class="td_itm_cnt"><?=(($row[$st_name])?$row[$st_name]:'-')?></td><!-- 재고개수 -->
        <?php } ?>
        <td class="td_itm_total" style="color:skyblue;"><?=(($row['cnt'])?$row['cnt']:'-')?></td><!-- 총생산갯수 -->
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='33' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if(false){//($is_admin){ ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <a href="./item_form.php" id="member_add" class="btn btn_01">추가하기</a>
    <?php } ?>
    <?php //} ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>


<script>
$("input[name*=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

$('label[for="itm_date"] i').on('click',function(){
    $(this).siblings('input').val('');
});


function form01_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

function form02_submit(f) {
    if (!f.pri_idx.value || !f.bom_part_no.value || !f.bom_name.value || !f.bom_idx.value) {
        alert('생산계획을 선택해 주세요.');
        return false;
    }

    if(f.plus_modify.value == 'plus'){
        if(!f.to_status.value){
            alert('목표상태를 선택해 주세요.')
            return false;
        }
    }
    else if(f.plus_modify.value == 'modify'){
        if(!f.from_status.value){
            alert('기존상태를 선택해 주세요.')
            return false;
        }
        if(!f.to_status.value){
            alert('목표상태를 선택해 주세요.')
            return false;
        }
        if(f.from_status.value == f.to_status.value){
            alert('기존상태값과 목표상태값이 동일합니다.');
            return false;
        }
    }

    if(!f.count.value){
        alert('갯수를 입력해 주세요.');
        return false;
    }

    return true;
}

</script>

<?php
include_once ('./_tail.php');
