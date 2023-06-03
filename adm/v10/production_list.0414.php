<?php
$sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '생산계획';
include_once('./_top_menu_production.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " FROM {$g5['production_table']} AS prd
                    LEFT JOIN {$g5['bom_table']} AS bom USING(bom_idx)
";

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " prd_status NOT IN ('trash','delete') ";
$where[] = " prd.com_idx = '".$_SESSION['ss_com_idx']."' ";
// $where[] = " bom.bom_type = 'product' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'prd.prd_idx' || $sfl == 'ori_idx') :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 날자 검색
if ($st_date) {
    $where[] = " prd_start_date >= '".$st_date."' ";
}
if ($en_date) {
    $where[] = " prd_done_date <= '".$en_date."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "prd_idx";
    $sod = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];


$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT *
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);//exit;
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&st_date='.$st_date.'&en_date='.$en_date; // 추가로 확장해서 넘겨야 할 변수들

?>
<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <label for="st_date" class="sch_label">
        <input type="text" name="st_date" value="<?php echo $st_date ?>" id="st_date" readonly class="frm_input readonly" placeholder="시작일" style="width:90px;" autocomplete="off">
    </label>
    <label for="en_date" class="sch_label">
        <input type="text" name="en_date" value="<?php echo $en_date ?>" id="en_date" readonly class="frm_input readonly" placeholder="종료일" style="width:90px;" autocomplete="off">
    </label>
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
        <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
        <option value="prd_order_no"<?php echo get_selected($_GET['sfl'], "prd_order_no"); ?>>지시번호</option>
        <option value="ori_idx"<?php echo get_selected($_GET['sfl'], "ori_idx"); ?>>수주번호</option>
        <option value="prd.prd_idx"<?php echo get_selected($_GET['sfl'], "prd_idx"); ?>>생산계획고유번호</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p style="display:none;">지시수량에 필요한 자재가 부족한 경우 <span class="color_red">빨간색</span>으로 표시됩니다. 자재 창고위치에 따라 현장 오차가 있을 수 있으므로 반드시 확인하시고 진행하세요.</p>
    <p>생산계획 등록후 목록페이지에서는 생산시작일 & 상태 정보만 수정할 수 있습니다.</p>
    <p>완제품의 지시량 및 하위 제품의 생산지시량은 [수정] 페이지에서 변경할 수 있습니다.</p>
    <p style="display:none;">'생산수량' 항목의 값은 생산이 진행중일 때 표시됩니다.</p>
</div>

<form name="form01" id="form01" action="./production_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="st_date" value="<?php echo $st_date ?>">
<input type="hidden" name="en_date" value="<?php echo $en_date ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="orp_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" style="min-width:60px;">ID</th>
        <th scope="col">품번</th>
        <th scope="col">품명</th>
        <th scope="col">수주량</th>
        <th scope="col">지시번호</th>
        <th scope="col">생산시작일</th>
        <th scope="col">지시량</th>
        <th scope="col">상태</th>
        <th scope="col">관리</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
        <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $row['oir'] = get_table('order_item','ori_idx',$row['ori_idx']);    // 수주
        // print_r2($row);
        $s_mod = '<a href="./production_form.php?'.$qstr.'&amp;w=u&amp;prd_idx='.$row['prd_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./order_practice_form.php?'.$qstr.'&w=c&orp_idx='.$row['orp_idx'].'" class="btn btn_03" style="margin-right:5px;">복제</a>';

        $bg = 'bg'.($i%2);

        // 완제품에 대한 지시수량만 가지고 옴
        $sql2 = "SELECT SUM(prm_value) AS prm_total, COUNT(prm_idx) AS prm_count
                FROM {$g5['production_member_table']} AS prm
                    LEFT JOIN {$g5['production_item_table']} AS pri USING(pri_idx)
                WHERE prd_idx = '".$row['prd_idx']."' AND pri.bom_idx = '".$row['bom_idx']."' AND prm_status NOT IN ('trash','delete')
                GROUP BY pri_idx
        ";
        // print_r3($sql2);
        $prm = sql_fetch($sql2,1);
        // print_r3($prm);
        $row['prm_count'] = $prm['prm_count'];
        $row['pri_value'] = $prm['prm_total'];
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['prd_idx'] ?>">
        <td class="td_chk">
            <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?php echo $i ?>">
            <input type="hidden" name="prd_idx[<?=$i?>]" value="<?=$row['prd_idx']?>">
        </td>
        <td class="td_prd_id"><?=$row['prd_idx']?></td>
        <td class="td_prd_bom_part_no"><?=$row['bom_part_no']?></td><!-- 품번 -->
        <td class="td_prd_bom_name font_size_7">
            <?=$row['bom_name']?>
        </td><!-- 품명 -->
        <td class="td_prd_order_no font_size_7"><?=number_format($row['oir']['ori_count'])?></td><!-- 수주량 -->
        <td class="td_prd_order_no font_size_7"><?=$row['prd_order_no']?></td><!-- 지시번호 -->
        <td class="td_prd_start_date"><!-- 시작일 -->
            <input type="text" name="prd_start_date[<?=$i?>]" id="prd_start_date_<?=$row['prd_idx']?>" bom_idx="<?=$row['bom_idx']?>" value="<?=(($row['prd_start_date'] == '0000-00-00')?'':$row['prd_start_date'])?>" old="<?=(($row['prd_start_date'] == '0000-00-00')?'':$row['prd_start_date'])?>" readonly class="tbl_input readonly prd_start_date" style="width:95px;text-align:center;">
        </td>
        <td class="td_pri_count"><!-- 지시량 -->
            <?=number_format($row['pri_value'])?>
        </td>
        <td class="td_prd_status td_prd_status_<?=$row['prd_idx']?>">
            <select name="prd_status[<?=$i?>]" id="prd_status_<?=$row['prd_idx']?>" class="prd_status">
                <?=$g5['set_prd_status_value_options']?>
            </select>
            <?php if($row['prd_status']){ ?>
            <script>$('#prd_status_<?=$row['prd_idx']?>').val('<?=$row['prd_status']?>');</script>
            <?php } ?>
        </td><!-- 상태 -->
        <td class="td_mng">
            <?=$s_mod?>
        </td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='20' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택문자전송" onclick="document.pressed=this.value" class="btn btn_05">
    <?php } ?>
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
    <?php if(!auth_check($auth[$sub_menu],'w')) { //($is_admin){ ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
    <a href="./production_form.php" id="member_add" class="btn btn_01">생산계획추가</a>
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
$(function(){
$("input[name=st_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect: function(selectedDate){$("input[name=en_date]").datepicker('option','minDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });
$("input[name=en_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect:function(selectedDate){$("input[name=st_date]").datepicker('option','maxDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}}});

$(".prd_start_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect: function(date_str,obj){
    var tmp_oldd = $('#'+obj.id).attr('old');
    var tmp_date = date_str;
    var tmp_bom = $('#'+obj.id).attr('bom_idx');
    var chk_flag = true;
    $(".prd_start_date").each(function(){
        if($(this).attr('id') != obj.id){
            if($(this).val() == tmp_date && $(this).attr('bom_idx') == tmp_bom){
                chk_flag = false;
            }
        }
    });
    if(!chk_flag){
        alert('동일한 제품에 동일한 생산시작일의 항목이 존재하므로 변경할 수 없습니다.');
        $('#'+obj.id).val(tmp_oldd);
    }
} });

});//$(function(){

    
function form01_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?"))
            return false;
    }
    else if(document.pressed == "선택수정" || document.pressed == "선택문자전송") {
        $('.prd_start_date').each(function(){
            if(!$(this).val()){
                alert('생산시작일을 설정해 주세요');
                $(this).focus();
                return false;
            }
        });
        $('.prd_status').each(function(){
            if(!$(this).val()){
                alert('상태값을 선택해 주세요');
                $(this).focus();
                return false;
            }
        });

        if(document.pressed == "선택문자전송") {
            var status_confirm = true;
            $('.prd_status').each(function(){
                if($(this).val() != 'confirm'){
                    status_confirm = false;
                    alert('상태값이 [확정]일때만 문자를 전송할 수 있습니다.');
                    $(this).focus();
                    return false;
                }
            });
            if(status_confirm == true){
                if(!confirm("선택한 생산계획의 내용으로 각 작업자들에게\n일괄적으로 문자를 보내시겠습니까?"))
                    return false;
            }
            else{
                return status_confirm;
            }
        }
    }

    return true;
}
</script>
<?php
include_once ('./_tail.php');