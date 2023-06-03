<?php
$sub_menu = "922130";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '발주제품별입고';
@include_once('./_top_menu_material.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql_common = " 
                FROM {$g5['material_order_item_table']} moi
                LEFT JOIN {$g5['material_order_table']} mto ON moi.mto_idx = mto.mto_idx
                LEFT JOIN {$g5['bom_table']} bom ON moi.bom_idx = bom.bom_idx
                LEFT JOIN {$g5['material_table']} mtr ON moi.bom_idx = mtr.bom_idx
                        AND mtr.mtr_status NOT IN('trash','used','reject','delivery','scrap')
";
$where = array();
//디폴트 검색조건
$where[] = " moi_status NOT IN ('trash','delete') ";
// $where[] = " mtr.moi_idx != '0' ";
$where[] = " mto.com_idx = '{$_SESSION['ss_com_idx']}' ";

//검색어 설정
if($stx != '') {
    switch($sfl){
        case ($sfl == 'bom_part_no' || $sfl == 'bom_idx'):
            $where[] = " bom.{$sfl} = '".trim($stx)."' ";
            break;
        default:
            $where[] = " bom.{$sfl} LIKE '%".trim($stx)."%' ";
            break;
    }
}

if($cst_idx_provider){
    $where[] = " bom.cst_idx_provider = '{$cst_idx_provider}' ";
}

if($bct_idx){
    $where[] = " bom.bct_idx = '{$bct_idx}' ";
}

//최종 WHERE 분리정리
if($where)
    $sql_search = " WHERE ".implode(' AND',$where);

if(!$sst) {
    $sst = "moi.moi_idx";
    $sod = "DESC";
}

$sql_group = " GROUP BY moi.moi_idx ";

$sql_order = " ORDER BY {$sst} {$sod} ";

$basic_sql = " SELECT count(*) AS cnt {$sql_common} {$sql_search} {$sql_group} {$sql_order}
";

$sql = " SELECT COUNT(*) AS cnt FROM ({$basic_sql}) q  ";
$row = sql_fetch($sql,1);
$total_count = $row['cnt'];
//print_r3($sql).'<br>';

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$sql = " SELECT moi.moi_idx
            ,moi.mto_idx
            ,bom.cst_idx_provider
            ,bom.cst_idx_customer
            ,bct_idx
            ,bom.bom_idx
            ,bom.bom_name
            ,bom.bom_part_no
            ,bom.bom_price
            ,moi.moi_count
            ,moi.moi_input_date
            ,SUM(mtr_value) AS mtr_sum
        {$sql_common} {$sql_search} {$sql_group} {$sql_order}
        limit {$from_record}, {$rows}
";
// print_r3($sql);
$result = sql_query($sql);

$colspan = 11;
?>
<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="bom.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품목코드</option>
        <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
        <option value="bom_idx"<?php echo get_selected($_GET['sfl'], "bom_idx"); ?>>품번</option>
    </select>
    <select name="cst_idx_provider" id="cst_idx_provider">
        <option value="">::공급업체::</option>
        <?php foreach($g5['provider_key_val'] as $pk => $pv){ ?>
        <option value="<?=$pk?>"><?=$pv?></option>
        <?php } ?>
    </select>
    <script>
        $('#cst_idx_provider').val('<?=$cst_idx_provider?>');
    </script>
    <select name="bct_idx" id="bct_idx">
        <option value="">::차종::</option>
        <?php foreach($g5['cats_key_val'] as $ck => $cv){ ?>
        <option value="<?=$ck?>"><?=$cv?></option>
        <?php } ?>
    </select>
    <script>
        $('#bct_idx').val('<?=$bct_idx?>');
    </script>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>
        각 발주별 제품의 재고현황과 재고를 등록하는 페이지 입니다.
    </p>
</div>
<form name="form01" id="form01" action="./material_order_input_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="qstr" value="<?php echo $qstr ?>">

<div class="tbl_head01 tbl_wrap">
<table>
<caption><?php echo $g5['title']; ?> 목록</caption>
<thead>
<tr>
    <th scope="col" id="moi_list_chk">
        <label for="chkall" class="sound_only">전체</label>
        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
    </th>
    <th scope="col">발주제품ID</th>
    <th scope="col">발주ID</th>
    <th scope="col">공급업체</th>
    <th scope="col">차종</th>
    <th scope="col">제품ID</th>
    <th scope="col">품명</th>
    <th scope="col">품번</th>
    <th scope="col">입고수량</th>
    <th scope="col">발주량</th>
    <th scope="col">현재고량</th>
    <th scope="col">입고예정일</th>
</tr>
</thead><!--//thead-->
<tbody>
<?php
for($i=0;$row=sql_fetch_array($result);$i++){
    /*
    $mtr_sql = sql_fetch(" SELECT SUM(mtr_value) AS mtr_sum FROM {$g5['material_table']}
        WHERE moi_idx = '{$row['moi_idx']}' AND mtr_status NOT IN ('trash','delete')
    ");
    */
    /*
    $mtr_sql = sql_fetch(" SELECT SUM(mtr_value) AS mtr_sum FROM {$g5['material_table']}
    WHERE moi_idx = '{$row['moi_idx']}' AND mtr_status != 'trash'
    GROUP BY moi_idx
    ");
    $row['mtr_sum'] = $mtr_sql['mtr_sum'];
    */
    $bg = 'bg'.($i%2);
?>
<tr class="<?=$bg?>">
    <td class="td_chk">
        <label for="chk_<?=$i?>" class="sound_only"><?php echo get_text($row['moi_idx']); ?></label>
        <input type="checkbox" name="chk[]" value="<?=$row['moi_idx']?>" id="chk_<?=$i?>">
        <div class="chkdiv_btn" chk_no="<?=$i?>"></div>

        <input type="hidden" name="bom_idx[<?=$row['moi_idx']?>]" value="<?=$row['bom_idx']?>">
        <input type="hidden" name="bom_name[<?=$row['moi_idx']?>]" value="<?=$row['bom_name']?>">
        <input type="hidden" name="bom_part_no[<?=$row['moi_idx']?>]" value="<?=$row['bom_part_no']?>">
        <input type="hidden" name="cst_idx_provider[<?=$row['moi_idx']?>]" value="<?=$row['cst_idx_provider']?>">
        <input type="hidden" name="cst_idx_customer[<?=$row['moi_idx']?>]" value="<?=$row['cst_idx_customer']?>">
        <input type="hidden" name="bom_price[<?=$row['moi_idx']?>]" value="<?=$row['bom_price']?>">
    </td><!--체크박스-->
    <td class="td_moi_idx"><?=$row['moi_idx']?></td>
    <td class="td_mto_idx"><?=$row['mto_idx']?></td>
    <td class="td_cst_name"><?=$g5['provider_key_val'][$row['cst_idx_provider']]?></td>
    <td class="td_bct_idx"><?=$g5['cats_key_val'][$row['bct_idx']]?></td>
    <td class="td_bom_idx"><?=$row['bom_idx']?></td>
    <td class="td_bom_name"><?=$row['bom_name']?></td>
    <td class="td_bom_part_no"><?=$row['bom_part_no']?></td>
    <td class="td_input_cnt">
        <input type="text" name="input_cnt[<?=$row['moi_idx']?>]" onclick="javascript:numtoprice(this)" class="frm_input input_cnt wg_wdx60 wg_right">
    </td>
    <td class="td_moi_count"><?=$row['moi_count']?></td>
    <td class="td_mtr_sum"><?=$row['mtr_sum']?></td>
    <td class="td_moi_input_date"><?=$row['moi_input_date']?></td>
</tr>
<?php }
if ($i == 0)
    echo "<tr><td colspan='".$colspan."' class=\"empty_table\">자료가 없습니다.</td></tr>";
?>
</tbody><!--//tbody-->
</table>
</div><!--//.tbl_wrap-->

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="선택발주제품입고" onclick="document.pressed=this.value" class="btn wg_btn_success">
    <?php } ?>
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?schrows='.$schrows.'&'.$qstr.'&amp;page='); ?>

<script>

function form01_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(!is_exist_input_count()){
        alert("선택된 항목의 입고량을 반드시 입력하셔야 합니다.");
        return false;
    }

    return true;
}

//선택된 품목중에 입고수량을 입력하지 않은 항목이 있는지 확인하는 함수
function is_exist_input_count(){
    var blank_exist = true;
    var chk = $('input[name="chk[]"]:checked');
    chk.each(function(){
        if(!$('input[name="input_cnt['+$(this).val()+']"]').val()){
            blank_exist = false;
        }
    });
    
    return blank_exist;
}
</script>
<?php
include_once('./_tail.php');