<?php
$sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '자재소요량산출';
include_once('./_top_menu_production.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$day_cnt = 7;
$next_day = get_dayAddDate(G5_TIME_YMD,1);
$days = array();
for($d=1;$d<=$day_cnt;$d++){
    $days[$d] = get_dayAddDate(G5_TIME_YMD,$d);
}
// print_r2($days);
$sql_common = " FROM {$g5['bom_table']} bom
                    LEFT JOIN {$g5['bom_category_table']} bct ON bom.bct_idx = bct.bct_idx
";

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " bom_status NOT IN ('trash','delete') ";
$where[] = " bom.com_idx = '".$_SESSION['ss_com_idx']."' ";
$where[] = " bom.bom_type != 'product' ";
$where[] = " bom.bom_type != 'half' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'bct.bct_name') :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'bom.bom_part_no' ) :
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

if (!$sst) {
    $sst = "bom_idx";
    $sod = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} ";
$sql_group = "";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];


$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT *
        {$sql_common} {$sql_search} {$sql_group} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);//exit;
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&prd_start_date='.$prd_start_date.'&prd_done_date='.$prd_done_date; // 추가로 확장해서 넘겨야 할 변수들

?>
<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="bct.bct_name"<?php echo get_selected($_GET['sfl'], "bct.bct_name"); ?>>차종</option>
        <option value="bom.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom.bom_part_no"); ?>>품목코드</option>
        <option value="bom.bom_name"<?php echo get_selected($_GET['sfl'], "bom.bom_name"); ?>>품명</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <label for="ord_start_date" class="sch_label" style="display:none;">
        <input type="text" name="ord_start_date" value="<?php echo $ord_start_date ?>" id="ord_start_date" readonly class="frm_input readonly" placeholder="시작일검색시작" style="width:120px;" autocomplete="off">
    </label>
    <label for="ord_done_date" class="sch_label" style="display:none;">
        <input type="text" name="ord_done_date" value="<?php echo $ord_done_date ?>" id="ord_done_date" readonly class="frm_input readonly" placeholder="시작일검색종료" style="width:120px;" autocomplete="off">
    </label>
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p style="display:none;">지시수량에 필요한 자재가 부족한 경우 <span class="color_red">빨간색</span>으로 표시됩니다. 자재 창고위치에 따라 현장 오차가 있을 수 있으므로 반드시 확인하시고 진행하세요.</p>
    <p>생산계획 등록후 목록페이지에서는 생산시작일/지시량/상태 외의 정보는 수정할 수 없습니다.</p>
    <p style="display:none;">'생산수량' 항목의 값은 생산이 진행중일 때 표시됩니다.</p>
</div>

<form name="form01" id="form01" action="./production_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="prd_start_date" value="<?php echo $prd_start_date ?>">
<input type="hidden" name="prd_done_date" value="<?php echo $prd_done_date ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="orp_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">ID</th>
        <th scope="col">차종</th>
        <th scope="col">품명</th>
        <th scope="col">유형</th>
        <th scope="col">SNP</th>
        <th scope="col">안전재고</th>
        <th scope="col">현재재고</th>
        <?php foreach($days as $dk => $dv){ ?>
        <th scope="col"><?=substr($dv,5,5).'('.get_yoil($dv).')'?></th>
        <?php } ?>
        <th scope="col">입고예정일</th>
    </tr>
    </thead>
    <tbody>
        <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $s_mod = '<a href="./production_form.php?'.$qstr.'&amp;w=u&amp;prd_idx='.$row['prd_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./order_practice_form.php?'.$qstr.'&w=c&orp_idx='.$row['orp_idx'].'" class="btn btn_03" style="margin-right:5px;">복제</a>';

        $bct_cmd = '';
        $bom_cmd = '';
        if($sfl == 'bct.bct_name'){
            $bct_cmd = ' AND json_value(bom_bct_json, "$[*]") = "'.$g5['cats_val_key'][strtoupper($stx)].'" ';
        }
        if($sfl == 'bom.bom_name'){
            $bom_cmd = ' AND bom.bom_name LIKE "%'.$stx.'%" ';
        }

        if($row['bom_type'] == 'product' || $row['bom_type'] == 'half' || $row['bom_type'] == 'material'){
            $itm_sql = " SELECT SUM(itm_value) AS total FROM {$g5['item_table']} itm
                        INNER JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
                        INNER JOIN {$g5['bom_category_table']} bct ON bom.bct_idx = bct.bct_idx
                    WHERE itm_status NOT IN('trash','delivery','error')
                        AND itm_part_no = '{$row['bom_part_no']}'
                        {$bct_cmd}
                        {$bom_cmd}
            ";
            // $itm = sql_fetch($itm_sql);
        }
        else{
            $itm_sql = " SELECT SUM(mtr_value) AS total FROM {$g5['material_table']} mtr
                        INNER JOIN {$g5['bom_table']} bom ON mtr.bom_idx = bom.bom_idx
                        INNER JOIN {$g5['bom_category_table']} bct ON bom.bct_idx = bct.bct_idx
                    WHERE mtr_status NOT IN('trash','delivery','error')
                        AND mtr_part_no = '{$row['bom_part_no']}'
                        {$bct_cmd}
                        {$bom_cmd}
            ";
            // $itm = sql_fetch($itm_sql);
        }
        // echo $itm_sql."<br>";
        $old_sql = " SELECT SUM(itm_value) AS sum, itm_date FROM {$g5['item_table']} itm
                            INNER JOIN {$g5['bom_table']} bom ON itm.itm_part_no = bom.bom_part_no
                            INNER JOIN {$g5['bom_category_table']} bct ON bom.bct_idx = bct.bct_idx
                    WHERE itm_status NOT IN('trash','delivery','error')
                        AND itm_part_no = '{$row['bom_part_no']}'
                        {$bct_cmd}
                        {$bom_cmd}
                    GROUP BY itm_date
                    HAVING itm_date < '".G5_TIME_YMD."'
                    ORDER BY itm_date DESC
                    LIMIT 3
        ";
        // echo $old_sql;
        $old_res = sql_query($old_sql,1);
        $old_sum = 0;
        $old_avg = 0;
        for($j=0;$orow=sql_fetch_array($old_res);$j++){
            $old_sum += $orow['sum'];
            $old_avg = floor($old_sum / ($j+1));
        }
        
        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['bom_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="bom_idx[<?php echo $row['bom_idx'] ?>]" value="<?php echo $row['bom_idx'] ?>" class="bom_idx_<?php echo $row['bom_idx'] ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bom_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['bom_idx'] ?>" id="chk_<?php echo $i ?>">
            <div class="chkdiv_btn" chk_no="<?=$i?>"></div>
        </td>
        <td class="td_bom_idx"><?=$row['bom_idx']?></td>
        <td class="td_bom_category">
            <?php
            $cat_arr = json_decode($row['bom_bct_json']);
            $cat_str = '';
            if(@count($cat_arr)){
                foreach($cat_arr as $v)
                    $cat_str .= (!$cat_str)?$g5['cats_key_val'][$v]:','.$g5['cats_key_val'][$v];
                echo $cat_str;
            }
            ?>
        </td><!-- 차종 -->
        <td class="td_bom_name">
            <input type="hidden" name="bom_idx[<?=$row['prd_idx']?>]" value="<?=$row['bom_idx']?>" id="bom_idx_<?=$i?>" class="bom_idx">
            <input type="hidden" name="pri_idx[<?=$row['prd_idx']?>]" value="<?=$row['pri_idx']?>" id="pri_idx_<?=$i?>" class="pri_idx">
            <span class="sp_cat"><?=$row['bom_part_no']?></span><br>
            <?=$row['bom_name']?>
        </td><!-- 품명 -->
        <td class="td_bom_type"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 유형 -->
        <td class="td_snp"><?=number_format($row['bom_ship_count'])?></td><!-- SNP -->
        <td class="td_safe_stock"><?=number_format($row['bom_safe_stock'])?></td><!-- 안전재고량 -->
        <td class="td_safe_stock"><?=(($row['bom_stock'])?number_format($row['bom_stock']):'-')?></td><!-- 현재재고량 -->
        <?php 
        $sum_cnt = 0;
        foreach($days as $dk=>$dv){ 
        ?>
        <td class="td_day_cnt">
            <?php
            // echo $old_avg;
            $pri = sql_fetch(" SELECT pri_value FROM {$g5['production_item_table']} pri
                            INNER JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
                        WHERE pri.bom_idx = '{$row['bom_idx']}'
                            AND prd.prd_start_date = '{$dv}'
                            AND pri_status = 'confirm'
            ");
            $prdict_cnt = ($pri['pri_value']) ? $pri['pri_value'] : $old_avg;
            $sum_cnt = $sum_cnt + $prdict_cnt;
            // echo $itm['total'].','.$sum_cnt.':'.($itm['total'] < $sum_cnt)."<br>";
            $warning = '';
            if($row['bom_stock']){
                $warning = ($row['bom_stock'] < $sum_cnt) ? 'sp_warning' : '';
            }
            $prdict_str = ($prdict_cnt) ? '<span class="'.$warning.'">'.number_format($prdict_cnt).'</span>' : '-';
            echo $prdict_str;
            ?>
        </td>
        <?php } ?>
        <td class="td_prid_day">
            
        </td><!-- 입고예정일 -->
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='16' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택발주" onclick="document.pressed=this.value" class="btn btn_05">
    <?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?schrows='.$schrows.'&'.$qstr.'&amp;page='); ?>

<script>
$(function(){
    
$("input[name=prd_start_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect: function(selectedDate){$("input[name=prd_done_date]").datepicker('option','minDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });

$("input[name=prd_done_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect:function(selectedDate){$("input[name=prd_start_date]").datepicker('option','maxDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}}});

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
        $('.pri_value').each(function(){
            if(!$(this).val()){
                alert('지시량을 설정해 주세요');
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
                if(!confirm("선택한 생산계획의 내용으로 각작업자들에게\n일괄적으로 문자를 보내시겠습니까?"))
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