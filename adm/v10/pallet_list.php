<?php
$sub_menu = "922160";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '파렛트조회';
// include_once('./_top_menu_orp.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];
$sql_common = " FROM {$g5['pallet_table']} "; 
$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " plt_status NOT IN ('trash','delete') ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'plt_idx') :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}


if($plt_status){
    $where[] = " plt_status = '".trim($plt_status)."' ";
    $qstr .= $qstr.'&plt_status='.$plt_status;
}

if($plt_reg_dt){
    $where[] = " plt_reg_dt LIKE '".trim($plt_reg_dt)."%' ";
    $qstr .= $qstr.'&plt_reg_dt='.$plt_reg_dt;
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "plt_idx";
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

$sql = " SELECT plt_idx
        , mb_id_delivery
        , plt_date
        , plt_reg_dt
        , plt_update_dt
        , plt_status
        , plt_check_yn
        , plt_reg_dt
        , plt_update_dt
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);//exit;
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
?>
<style>
.tbl_head01 thead tr th{position:sticky;top:100px;z-index:100;}
.td_plt_idx{}
.td_bom_info {text-align:left !important;}
.td_bom_info p b{color:skyblue;}
.td_bom_info span{color:orange;}
.td_bom_info strong{color:yellow;}
.sp_pno{color:skyblue;font-size:0.85em;}
.sp_std{color:#e87eee;font-size:0.85em;}
.td_mb_id_delivery {width:90px;text-align:center !important;}
.td_itm_total{width:80px;text-align:center !important;}
.td_plt_check_yn{width:80px;}
.td_plt_reg_dt{width:170px;}
.td_plt_update_dt{width:170px;}
.td_plt_status{width:90px;}

.sp_cat{color:orange;font-size:0.85em;}
.sp_pno{color:skyblue;}

.sch_label{position:relative;}
.sch_label span{position:absolute;top:-23px;left:0px;z-index:2;}
.sch_label .data_blank{position:absolute;top:3px;right:-18px;z-index:2;font-size:1.1em;cursor:pointer;}
.slt_label{position:relative;}
.slt_label span{position:absolute;top:-23px;left:0px;z-index:2;}
.slt_label .data_blank{position:absolute;top:3px;right:-18px;z-index:2;font-size:1.1em;cursor:pointer;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <input type="hidden" name="sfl" value="plt_idx">
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" placeholder="파레트ID" id="stx" class="frm_input">
    <label for="plt_status" class="sch_label">
        <select name="plt_status" id="plt_status">
            <option value="">::상태선택::</option>
            <?=$g5['set_plt_status_value_options']?>
        </select>
    </label>
    <script>
        <?php if($plt_status){ ?>
        $('#plt_status').val('<?=$plt_status?>');
        <?php } ?>
    </script>
    <input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>파렛트를 관리하는 페이지 입니다.</p>
</div>

<form name="form01" id="form01" action="./pallet_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">파렛트ID</th>
        <th scope="col">제품정보</th>
        <th scope="col">배송기사</th>
        <th scope="col">총적재수량</th>
        <th scope="col">검사완료</th>
        <th scope="col">등록일시</th>
        <th scope="col">출하일시</th>
        <th scope="col">상태</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
        <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $mb = sql_fetch(" SELECT mb_name FROM {$g5['member_table']} WHERE mb_id = '{$row['mb_id_delivery']}' ");
        $row['mb_name'] = $mb['mb_name'];
        $row['itm_total'] = 0;
        $chk_yn = false;
        $itm_sql = " SELECT itm.bom_idx
                        , itm_name
                        , itm_part_no
                        , bct_idx
                        , bom_delivery_check_yn
                        , SUM(itm_value) AS itm_sum
                    FROM {$g5['item_table']} itm
                    LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
                    WHERE plt_idx = '{$row['plt_idx']}'
                    GROUP BY itm.bom_idx
        ";
        $itm_res = sql_query($itm_sql,1);
        // print_r2($row);
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['orp_idx'] ?>">
        <td class="td_plt_idx"><?=$row['plt_idx']?></td>
        <td class="td_bom_info">
            <?php for($j=0;$itm_row=sql_fetch_array($itm_res);$j++){ 
                    $row['itm_total'] += $itm_row['itm_sum'];
                    if($itm_row['bom_delivery_check_yn'])
                        $chk_yn = true;
            ?>
            <p><b>( <?=$g5['cats_key_val'][$itm_row['bct_idx']]?> )</b> <?=$itm_row['itm_name']?></p>
            <span>[ <?=$itm_row['itm_part_no']?> ]</span>
            <strong>(<?=$itm_row['itm_sum']?> EA)</strong>
            <?php } ?>
        </td>
        <td class="td_mb_id_delivery"><?=$row['mb_name']?></td>
        <td class="td_itm_total"><?=number_format($row['itm_total'])?></td><!-- 적재수량 -->
        <td class="td_plt_check_yn">
                <label for="chk_<?php echo $i; ?>" class="sound_only"></label>
                <input type="hidden" name="chk[<?=$row['plt_idx']?>]" value="0">
                <input type="checkbox" name="plt_check_yn[<?=$row['plt_idx']?>]"<?php echo $row['plt_check_yn'] ? ' checked' : ''; ?> value="1" id="plt_check_yn_<?php echo $i ?>">
                <div class="chkdiv_btn" chk_no="<?=$i?>"></div>
        </td>
        <td class="td_plt_reg_dt"><?=$row['plt_reg_dt']?></td>
        <td class="td_plt_update_dt">
            <?=(($row['plt_status'] == 'delivery')?$row['plt_update_dt']:'-')?>
        </td>
        <td class="td_plt_status"><?=$g5['set_plt_status_value'][$row['plt_status']]?></td><!-- 상태 -->
    </tr>
    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='8' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w')) { ?>
    <input type="submit" name="act_button" value="수정" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
    <?php if(false){//($is_admin){ ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>


<script>
$("input[name*=_date],input[id*=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });


function form01_submit(f)
{

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
