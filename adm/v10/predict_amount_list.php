<?php
$sub_menu = "922150";
// $sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '자재소요량산출';
include_once('./_top_menu_material_order.php');
// include_once('./_top_menu_production.php');
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
                    LEFT JOIN {$g5['customer_table']} cst ON bom.cst_idx_provider = cst.cst_idx
";

$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " bom_status NOT IN ('trash','delete') ";
$where[] = " bom.com_idx = '".$_SESSION['ss_com_idx']."' ";
// $where[] = " bom.bom_type != 'product' ";
// $where[] = " bom.bom_type != 'half' ";
$where[] = " bom.bom_type NOT IN ('half','product') ";

//공급처 검색
if($provider){
    $where[] = " cst.cst_idx = '".$provider."' ";
}

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
    $sst = "cst_idx_provider";
    $sod = "desc";
}
if (!$sst2) {
    $sst2 = ", bom_idx";
    $sod2 = "desc";
}

$sql_order = " ORDER BY {$sst} {$sod} {$sst2} {$sod2} ";
$sql_group = "";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];


$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT bom.bom_idx
            , cst_idx_provider
            , cst_name
            , cst_idx_customer
            , bom.bct_idx
            , bom_bct_json
            , bom_name
            , bom_part_no
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
    <select name="provider" id="provider">
        <option value="">::공급업체::</option>
        <?php foreach($g5['provider_key_val'] as $pk => $pv){ ?>
        <option value="<?=$pk?>"><?=$pv?></option>
        <?php } ?>
    </select>
    <?php if($provider){ ?>
    <script>$('#provider').val('<?=$provider?>');</script>
    <?php } ?>
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
    <p>여기서의 <span style="color:lightgreen;font-weight:700;">[선택임시발주]</span>등록시 <span style="color:orange;font-weight:700;">[입고예정일은 임의로 3일후]</span>의 날짜에,<br>발주유형은 <span style="color:orange;font-weight:700;">[일반발주]</span>이고,<br>입고장소는 <span style="color:orange;font-weight:700;">[본사]</span>로 임시발주등록이 됩니다.<br>정확한 입고예정일과 그 외의 정보는 <span style="color:skyblue;font-weight:700;">[발주관리]</span>페이지에서 설정해 주세요.</p>
    <p style="display:none;">'생산수량' 항목의 값은 생산이 진행중일 때 표시됩니다.</p>
</div>

<form name="form01" id="form01" action="./predict_amount_list_update.php" onsubmit="return form01_submit(this);" method="post">
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
        <th scope="col">제품ID</th>
        <th scope="col" style="min-width:250px;">품번/품명</th>
        <th scope="col">SNP</th>
        <th scope="col">안전재고</th>
        <th scope="col">현재고</th>
        <?php foreach($days as $dk => $dv){ ?>
        <th scope="col"><?=substr($dv,5,5).'('.get_yoil($dv).')'?></th>
        <?php } ?>
        <th scope="col">발주수량</th>
        <th scope="col">최근발주제품ID</th>
        <th scope="col">입고예정일</th>
    </tr>
    </thead>
    <tbody>
        <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bct_cmd = '';
        $bom_cmd = '';
        if($sfl == 'bct.bct_name' && $stx){
            $bct_cmd = ' AND json_value(bom_bct_json, "$[*]") = "'.$g5['cats_val_key'][strtoupper($stx)].'" ';
        }
        if($sfl == 'bom.bom_name' && $stx){
            $bom_cmd = ' AND bom.bom_name LIKE "%'.$stx.'%" ';
        }

        // 과거 3일치의 생산된 수량의 평균값을 산출하기 위한 코드
        $old_sql = " SELECT SUM(mtr_value) AS sum, mtr_date FROM {$g5['material_table']} mtr
                            INNER JOIN {$g5['bom_table']} bom ON mtr.mtr_part_no = bom.bom_part_no
                            INNER JOIN {$g5['bom_category_table']} bct ON bom.bct_idx = bct.bct_idx
                    WHERE mtr_status NOT IN('trash','delivery','defect','error','scrap')
                        AND mtr_part_no = '{$row['bom_part_no']}'
                        {$bct_cmd}
                        {$bom_cmd}
                    GROUP BY mtr_date
                    HAVING mtr_date < '".G5_TIME_YMD."'
                    ORDER BY mtr_date DESC
                    LIMIT 3
        ";
        // echo $old_sql."<br>";
        $old_res = sql_query($old_sql,1);
        $old_sum = 0;
        $old_avg = 0;
        for($j=0;$orow=sql_fetch_array($old_res);$j++){
            $old_sum += $orow['sum'];
            $old_avg = floor($old_sum / ($j+1));
        }

        //현재고를 추출
        $cur_sql = " SELECT SUM(mtr_value) AS mtr_stock FROM {$g5['material_table']}
            WHERE mtr_status IN ('ok','finish','defect')
                AND bom_idx = '{$row['bom_idx']}'
        ";
        // echo $cur_sql."<br>";
        $mtr = sql_fetch($cur_sql);
        $row['bom_stock'] = $mtr['mtr_stock'];
        
        $moi = sql_fetch(" SELECT moi_idx, mto_idx, moi_input_date FROM {$g5['material_order_item_table']}
                WHERE bom_idx = '{$row['bom_idx']}'
                    AND moi_status != 'trash'
                ORDER BY moi_input_date DESC
                LIMIT 1
        ");

        $diff_day = dt_diff(G5_TIME_YMD,$moi['moi_input_date'],'day');

        $past = ($diff_day < 0) ? 1 : 0;

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['bom_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="bom_idx[<?php echo $row['bom_idx'] ?>]" value="<?php echo $row['bom_idx'] ?>" class="bom_idx_<?php echo $row['bom_idx'] ?>">
            <input type="hidden" name="bom_price[<?=$row['bom_idx']?>]" value="<?=$row['bom_price']?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bom_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $row['bom_idx'] ?>" id="chk_<?php echo $i ?>">
            <div class="chkdiv_btn" chk_no="<?=$i?>"></div>
        </td>
        <td class="td_bom_idx font_size_7"><?=$row['bom_idx']?></td>
        <td class="td_bom_name font_size_8">
            <input type="hidden" name="cst_idx_provider[<?=$row['bom_idx']?>]" value="<?=$row['cst_idx_provider']?>">
            <input type="hidden" name="bom_idx[<?=$row['prd_idx']?>]" value="<?=$row['bom_idx']?>" id="bom_idx_<?=$i?>" class="bom_idx">
            <input type="hidden" name="pri_idx[<?=$row['prd_idx']?>]" value="<?=$row['pri_idx']?>" id="pri_idx_<?=$i?>" class="pri_idx">
            <span class="sp_cat"><?=$row['bom_part_no']?></span>
            <?php
            $cat_arr = json_decode($row['bom_bct_json']);
            $cat_str = '';
            if(@count($cat_arr)){
                foreach($cat_arr as $v)
                    $cat_str .= (!$cat_str)?$g5['cats_key_val'][$v]:','.$g5['cats_key_val'][$v];
                echo '<span class="ml_5 font_size_7" style="color:yellow;font-weight:700;">'.$cat_str.'</span>';
            }
            ?>
            <span class="font_size_7"><?=$g5['set_bom_type_value'][$row['bom_type']]?></span>
            <span style="color:skyblue;"><?=$row['cst_name']?></span>
            <br>
            <?=$row['bom_name']?>
        </td><!-- 품번/품명 -->
        <td class="td_snp"><?=number_format($row['bom_ship_count'])?></td><!-- SNP(패킹적재량) -->
        <td class="td_safe_stock font_size_7"><?=number_format($row['bom_safe_stock'])?></td><!-- 안전재고량 -->
        <td class="td_current_stock"><?=(($row['bom_stock'])?number_format($row['bom_stock']):'-')?></td><!-- 현재재고량 -->
        <?php 
        $sum_cnt = 0;
        foreach($days as $dk=>$dv){ 
        ?>
        <td class="td_day_cnt">
            <?php
            // echo $old_avg;
            // $pri_sql = " SELECT pri_value
            //                     , pri.prd_idx
            //                     , prd.bom_idx
            //                 FROM {$g5['production_item_table']} pri
            //                 INNER JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
            //             WHERE pri.bom_idx = '{$row['bom_idx']}'
            //                 AND prd.prd_start_date = '{$dv}'
            //                 AND pri_status = 'confirm'
            // ";
            
            // $pri = sql_fetch($pri_sql);
            $prdict_cnt = $old_avg; //($pri['pri_value']) ? $pri['pri_value'] : $old_avg;
            $sum_cnt = $sum_cnt + $prdict_cnt;
            // echo $itm['total'].','.$sum_cnt.':'.($itm['total'] < $sum_cnt)."<br>";
            $warning = '';
            if($row['bom_stock']){
                $warning = ($row['bom_stock'] < $sum_cnt) ? 'sp_warning' : '';
            }else{
                $warning = 'sp_warning';
            }
            $prdict_str = ($prdict_cnt) ? '<span class="'.$warning.'" t="'.$row['bom_stock'].'">'.number_format($prdict_cnt).'</span>' : '-';
            echo $prdict_str;
            ?>
        </td>
        <?php } ?>
        <td class="td_moi_count">
            <input type="text" name="moi_count[<?=$row['bom_idx']?>]" onclick="javascript:numtoprice(this)" class="frm_input moi_count wg_wdx60 wg_right">
        </td><!-- 발주수량 -->
        <td class="td_mto_idx">
            <span class="<?=(($past)?'sp_past':'')?>"><?=$moi['moi_idx']?></span>
        </td><!-- 발주ID -->
        <td class="td_prid_day wg_wdx100 font_size_8">
            <span class="<?=(($past)?'sp_past':'')?>"><?=$moi['moi_input_date']?></span>
        </td><!-- 입고예정일 -->
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='19' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택임시발주" onclick="document.pressed=this.value" class="btn wg_btn_success">
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


$('.select_order').on('click',function(){
    var f = document.getElementById('form01');
    
});


});//$(function(){
   
function form01_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(!is_checked("chk[]")) {
        alert("발주하실 항목을 하나 이상 선택하세요.");
        return false;
    }
    if(!is_same_provider()){
        alert('동일한 공급업체의 품목으로 선택해 주세요.');
        return false;
    }
    if(!is_exist_order_count()){
        alert("선택된 항목의 발주수량을 반드시 입력하셔야 합니다.");
        return false;
    }

    if(!confirm('선택한 품목을 발주등록 하시겠습니까?')){
        return false;
    }

    return true;
}

//동일한 공급업체 품목만 선택했는지 확인하는 함수
function is_same_provider(){
    var same_provider = true;
    var chk = $('input[name="chk[]"]:checked');
    var cst_idx = 0;
    var chk_num = 0;
    chk.each(function(){
        if(chk_num == 0){
            cst_idx = $('input[name="cst_idx_provider['+$(this).val()+']"]').val();
        }
        else{
            if(cst_idx != $('input[name="cst_idx_provider['+$(this).val()+']"]').val()){
                same_provider = false;
            }
        }
        chk_num++;
    });

    return same_provider;
}

//선택된 품목중에 발주수량을 입력하지 않은 항목이 있는지 확인하는 함수
function is_exist_order_count(){
    var blank_exist = true;
    var chk = $('input[name="chk[]"]:checked');
    chk.each(function(){
        if(!$('input[name="moi_count['+$(this).val()+']"]').val()){
            blank_exist = false;
        }
    });
    
    return blank_exist;
}
 
</script>
<?php
include_once ('./_tail.php');