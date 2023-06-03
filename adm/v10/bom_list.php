<?php
$sub_menu = "940120";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = 'BOM(제품관리)';
include_once('./_top_menu_bom.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


$sql_common = " FROM {$g5['bom_table']} AS bom
                    LEFT JOIN {$g5['bom_category_table']} AS bct ON bct.bct_idx = bom.bct_idx
                        AND bct.com_idx = '".$_SESSION['ss_com_idx']."'
                    LEFT JOIN {$g5['customer_table']} AS cst ON cst.cst_idx = bom.cst_idx_provider
"; 

$where = array();
$where[] = " bom_status NOT IN ('delete','trash') AND bom.com_idx = '".$_SESSION['ss_com_idx']."' ";   // 디폴트 검색조건

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'bct_idx' ) :
			$where[] = " {$sfl} LIKE '".trim($stx)."%' ";
            break;
		case ( $sfl == 'bom_part_no' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        
		case ( $sfl == 'bom_idx' ) :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

if($ser_cst_idx) {
    $where[] = " cst_idx_customer = '".trim($ser_cst_idx)."' ";
}

// 차종
if($ser_bct_idx) {
    $where[] = " bom.bct_idx = '".trim($ser_bct_idx)."' ";
}

// 타입
$ser_bom_type = $ser_bom_type ?: 'all';
if($ser_bom_type!='all') {  // all 인 경우는 조건이 필요없음
    $where[] = " bom_type = '".trim($ser_bom_type)."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "bom_idx";
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

$sql = "SELECT *
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);
$result = sql_query($sql,1);

// 완제품
$sql = " SELECT COUNT(*) as cnt FROM {$g5['bom_table']} WHERE bom_status NOT IN ('delete','trash') AND bom_type = 'product' ";
$row = sql_fetch($sql);
$product_count = $row['cnt'];

// 반제품
$sql = " SELECT COUNT(*) as cnt FROM {$g5['bom_table']} WHERE bom_status NOT IN ('delete','trash') AND bom_type = 'half' ";
$row = sql_fetch($sql);
$half_count = $row['cnt'];

// 자재
$sql = " SELECT COUNT(*) as cnt FROM {$g5['bom_table']} WHERE bom_status NOT IN ('delete','trash') AND bom_type = 'material' ";
$row = sql_fetch($sql);
$material_count = $row['cnt'];

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&ser_bct_idx='.$ser_bct_idx.'&ser_bom_type='.$ser_bom_type; // 추가로 확장해서 넘겨야 할 변수들
?>
<style>
.td_bom_name {text-align:left !important;}
.td_bom_part_no, .td_com_name, .td_bom_maker
,.td_bom_items, .td_bom_items_title {text-align:left !important;}
.span_bom_price {margin-left:20px;}
.span_cst_name{color:rgb(161, 143, 110);}
.span_bit_count:before {content:'×';}
.td_bom_items {color:#818181 !important;}
.span_bom_part_no {margin-left:10px;}
.span_com_name {margin-left:20px;}
.span_com_name:before {content:'거래처:';font-size:0.8em;}
.span_bom_edit {margin-left:30px;}
.span_bom_edit a:link,.span_bom_edit a:visited {color:#3a3a3a !important;}
.span_bom_price b, .span_bit_count b {color:#737132;font-weight:normal;}
#modal01 table ol {padding-right: 20px;text-indent: -12px;padding-left: 12px;}
#modal01 form {overflow:hidden;}
.ui-dialog .ui-dialog-titlebar-close span {
    display: unset;
    margin: -8px 0 0 -8px;
}
.btn_number {padding:1px 5px 2px;margin-right:10px;font-size:0.7em;color:white;background-color:#354667;cursor:pointer;}
.div_part span {margin-right:10px;}
.div_bom_part_no {position:relative;display:inline-block;}
.div_bom_part_no_count {position:absolute;top:0px;right:-16px;background-color:red;color:white;border-radius:7px;padding:0px 5px;font-size:10px;height:15px;line-height:13px;}
.div_part_no_detail {position:absolute;top:7px;left:9px;padding:5px 10px;background-color:#354667;width:max-content;font-size:1.3em;line-height:1.3em;display:none;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">검색 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    <a href="?<?='ser_bom_type=product'?>" class="btn_ov01" style="margin-left:20px;">
        <span class="ov_txt">완제품 </span><span class="ov_num"><?php echo number_format($product_count) ?></span>
    </a>
    <a href="?<?='ser_bom_type=half'?>" class="btn_ov01">
        <span class="ov_txt">반제품 </span><span class="ov_num"><?php echo number_format($half_count) ?></span>
    </a>
    <a href="?<?='ser_bom_type=material'?>" class="btn_ov01">
        <span class="ov_txt">자재 </span><span class="ov_num"><?php echo number_format($material_count) ?></span>
    </a>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<select name="ser_cst_idx" id="ser_cst_idx">
    <option value="">고객사전체</option>
    <?php
    $sql = "SELECT cst_idx, cst_name FROM {$g5['customer_table']} WHERE com_idx = '".$_SESSION['ss_com_idx']."' AND cst_type = 'customer' ORDER BY cst_idx ";
    // echo $sql.'<br>';
    $rs = sql_query($sql,1);
    for ($i=0; $row=sql_fetch_array($rs); $i++) {
        // print_r2($row);
        echo '<option value="'.$row['cst_idx'].'" '.get_selected($ser_cst_idx, $row['cst_idx']).'>'.$row['cst_name'].'</option>';
    }
    ?>
</select>
<script>$('select[name=ser_cst_idx]').val("<?=$ser_cst_idx?>").attr('selected','selected');</script>

<select name="ser_bct_idx" id="ser_bct_idx">
    <option value="">차종선택</option>
    <?php foreach($g5['cats_key_val'] as $k => $v) { ?>
    <option value="<?=$k?>" <?=get_selected($_GET['ser_bct_idx'], $k)?>><?=$v?></option>
    <?php } ?>
</select>

<select name="ser_bom_type" id="ser_bom_type">
    <option value="all">제품구분</option>
    <?=$g5['set_bom_type_options']?>
</select>
<script>$('select[name="ser_bom_type"]').val('<?=$ser_bom_type?>');</script>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품번</option>
    <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
    <option value="bom_idx"<?php echo get_selected($_GET['sfl'], "bom_idx"); ?>>BOMidx</option>
    <option value="cst_idx_customer"<?php echo get_selected($_GET['sfl'], "cst_idx_customer"); ?>>거래처번호</option>
    <option value="bom_stock_check_yn"<?php echo get_selected($_GET['sfl'], "bom_stock_check_yn"); ?>>입고검사여부</option>
    <option value="bom_delivery_check_yn"<?php echo get_selected($_GET['sfl'], "bom_delivery_check_yn"); ?>>출하검사여부</option>
    <option value="bom_pallet_check_yn"<?php echo get_selected($_GET['sfl'], "bom_pallet_check_yn"); ?>>완성품검사여부</option>
    <option value="bom_maker"<?php echo get_selected($_GET['sfl'], "bom_maker"); ?>>메이커</option>
    <option value="bom_idx"<?php echo get_selected($_GET['sfl'], "bom_idx"); ?>>고유번호</option>
    <option value="bom_memo"<?php echo get_selected($_GET['sfl'], "bom_memo"); ?>>메모</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" class="btn_submit" value="검색">

</form>

<div class="local_desc01 local_desc" style="display:none;">
    <p>리스트 디폴트는 완성품입니다. 전체제품 목록을 확인하시려면 제품타입을 [전체]로 설정하시고 검색하세요.</p>
    <p>가격이 변경될 미래 날짜를 지정해 두면 해당 날짜부터 변경될 가격이 적용됩니다.</p>
</div>

<form name="form01" id="form01" action="./bom_list_update.php" onsubmit="return form01_submit(this);" method="post">
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
        <th scope="col" id="bom_list_chk">
            <label for="chkall" class="sound_only">전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">품번</th>
        <th scope="col"><?php echo subject_sort_link('bom_name') ?>품명</a></th>
        <th scope="col">업체명</th>
        <th scope="col">차종</th>
        <th scope="col">타입</th>
        <th scope="col">관리</th>
    </tr>
    <tr>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        //print_r2($row);
        // if($row['bct_name']){
        //     $cat_tree = category_tree_array($row['bct_idx']);
        //     $row['bct_name_tree'] = '';
        //     for($k=0;$k<count($cat_tree);$k++){
        //         $cat_str = sql_fetch(" SELECT bct_name FROM {$g5['bom_category_table']} WHERE bct_idx = '{$cat_tree[$k]}' ");
        //         $row['bct_name_tree'] .= ($k == 0) ? $cat_str['bct_name'] : ' > '.$cat_str['bct_name'];
        //     }
        // }
        $com_p = get_table_meta('company','com_idx',$row['cst_idx_provider']);
        $com_c = get_table_meta('company','com_idx',$row['cst_idx_customer']);
        // bom_item 에서 뽑아야 하는 제품만 (완제품, 반제품)
        if(@in_array($row['bom_type'], $g5['set_bom_type_displays'])) {
            $sql1 = "SELECT bom.bom_idx, cst_idx_provider, bom.bom_name, bom_part_no, bom_type, bom_price, bom_status, cst_name
                        , bit.bit_idx, bit.bom_idx, bit.bit_main_yn, bit.bom_idx_child, bit.bit_reply, bit.bit_count
                    FROM {$g5['bom_item_table']} AS bit
                        LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                        LEFT JOIN {$g5['customer_table']} AS cst ON cst.cst_idx = bom.cst_idx_provider
                    WHERE bit.bom_idx = '".$row['bom_idx']."'
                    ORDER BY bit.bit_reply
            ";
            // echo $sql1.BR;
            $rs1 = sql_query($sql1,1);
            $row['rows'] = sql_num_rows($rs1);
            $row['rows_text'] = $row['rows'] ? '<span class="font_size_8 ml_10">(구성품수: '.$row['rows'].')</span>' : '';
            // echo $rowspan.'<br>';
            for ($j=0; $row1=sql_fetch_array($rs1); $j++) {
                // print_r2($row1);
                $row1['bit_main_class'] = $row1['bit_main_yn'] ? 'bit_main' : ''; // 대표제품 색상
                $len = strlen($row1['bit_reply'])/2+1;
                $row1['len'] = '<span class="btn_number">'.$len.'</span>';
                for ($k=2; $k<$len; $k++) { $row1['nbsp'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; } // 들여쓰기공백
                $row['parts_list'][] = '<div class="div_part" bom_idx="'.$row1['bom_idx'].'" bit_idx="'.$row1['bit_idx'].'">
                                            <span class="span_bom_part_no '.$row1['bit_main_class'].' font_size_7 font_color_white">'.$row1['nbsp'].$row1['len'].$row1['bom_part_no'].'</span>
                                            <span class="span_bom_name">'.$row1['bom_name'].'</span>
                                            <span class="span_bom_type font_size_7">'.$g5['set_bom_type_value'][$row1['bom_type']].'</span>
                                            <span class="span_cst_name font_size_8">'.$row1['cst_name'].'</span>
                                            <span class="span_bom_price font_size_8">'.number_format($row1['bom_price']).'원</span>
                                            <span class="span_bit_count font_size_8">'.$row1['bit_count'].'개</span>
                                            <span class="span_bom_edit"><a href="./bom_form.php?w=u&bom_idx='.$row1['bom_idx_child'].'" target="_blank"><i class="fa fa-external-link"></i></a></span>
                                        </div>';
                // 재료비합계
                $row['bom_price_material'] += $row1['bom_price']*$row1['bit_count'];
            }
            // 재료비합계표시
            $row['bom_price_material_text'] = number_format($row['bom_price_material']);
            // 재료비율
            $row['bom_profit_ratio'] = ($row['bom_price']) ? number_format(($row['bom_price_material']/$row['bom_price']*100),1).'%' : '-';
        }
        // 자재인 경우
        else {
            $row['bom_price_material_text'] = '';
        }

        // 품번이 여러개인 경우
        if($row['bom_part_nos']) {
            $row['bom_part_nos_array'] = explode("|",$row['bom_part_nos']);
            for ($j=0;$j<sizeof($row['bom_part_nos_array'])-1; $j++) {
                $row['bom_part_nos_items'][] = $row['bom_part_nos_array'][$j];
            }
            // print_r2($row['bom_part_nos_items']);
            $row['bom_part_nos_items_content'] = ($row['bom_part_nos_items'][0]) ? '<div class="div_part_no_detail">'.implode("<br>",$row['bom_part_nos_items']).'</div>':'';
            $row['bom_part_nos_text'] = '<div class="div_bom_part_no_count">'.sizeof($row['bom_part_nos_items']).$row['bom_part_nos_items_content'].'</div>';
        }

        // buttons for admin.
        $s_bom = '<a href="./bom_structure_form.php?'.$qstr.'&amp;w=u&amp;bom_idx='.$row['bom_idx'].'" class="btn btn_03">BOM</a>';
        $s_mod = '<a href="./bom_form.php?'.$qstr.'&amp;w=u&amp;bom_idx='.$row['bom_idx'].'" class="btn btn_03">수정</a>';

        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['bom_idx'] ?>">
        <td class="td_chk">
            <input type="hidden" name="bom_idx[<?php echo $i ?>]" value="<?php echo $row['bom_idx'] ?>" id="bom_idx_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bom_name']); ?> <?php echo get_text($row['bom_nick']); ?>님</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td class="td_bom_part_no"><!-- 품번 -->
            <input type="hidden" name="bom_part_no[<?php echo $i ?>]" value="<?php echo $row['bom_part_no'] ?>">
            <div class="div_bom_part_no"><?=$row['bom_part_no']?><?=$row['bom_part_nos_text']?></div>
        </td>
        <td class="td_bom_name"><!-- 품명(구성품수) -->
            <label for="name_<?php echo $i; ?>" class="sound_only">품명</label>
            <input type="text" name="bom_name[<?php echo $i; ?>]" value="<?php echo htmlspecialchars2(cut_str($row['bom_name'],250, "")); ?>" required class="tbl_input required" style="width:250px;display:none;">
            <?=$row['bom_name']?><?=$row['rows_text']?>
        </td>
        <td class="td_cst_name"><?=$row['cst_name']?></td><!-- 업체명 -->
        <td class="td_bct_name"><?=$row['bct_name']?></td><!-- 차종 -->
        <td class="td_bom_type"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 타입 -->
        <td class="td_mng">
            <?=(($row['bom_type']!='material')?$s_bom:'')?><!-- 자재가 아닌 경우만 BOM 버튼 -->
			<?=$s_mod?>
		</td>
    </tr>
    <tr class="<?php echo $bg; ?>" tr_id="<?=$row['bom_idx']?>" style="display:<?=(!in_array($row['bom_type'],$g5['set_bom_type_displays']))?'none':''?>">
        <td>
        </td>
        <td class="td_bom_items" colspan="10">
            <?php
            if(is_array($row['parts_list'])) {
                echo implode(" ",$row['parts_list']);
            }
            else {
                echo '구성품 없음';
            }
            ?>
        </td>
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
    <?php if (false){ //(!auth_check($auth[$sub_menu],'d')) { ?>
       <a href="javascript:" id="btn_excel_upload" class="btn btn_02" style="margin-right:50px;">엑셀등록</a>
    <?php } ?>
    <?php if (!auth_check($auth[$sub_menu],'w',1)) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <a href="./bom_form.php" id="member_add" class="btn btn_01">추가하기</a>
    <?php } ?>

</div>


</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div id="modal01" title="엑셀 파일 업로드" style="display:none;">
    <form name="form02" id="form02" action="./bom_excel_upload.php" onsubmit="return form02_submit(this);" method="post" enctype="multipart/form-data">
        <table>
        <tbody>
        <tr>
            <td style="line-height:130%;padding:10px 0;">
                <ol>
                    <li>엑셀은 97-2003통합문서만 등록가능합니다. (*.xls파일로 저장)</li>
                    <li>엑셀은 하단에 탭으로 여러개 있으면 등록 안 됩니다. (한개의 독립 문서이어야 합니다.)</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <input type="file" name="file_excel" onfocus="this.blur()">
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <button type="submit" class="btn btn_01">확인</button>
            </td>
        </tr>
        </tbody>
        </table>
    </form>
</div>


<script>
// 대표제품 
$(document).on('click','.btn_number',function(e){
    if(confirm('선택하신 항목을 대표제품으로 변경하시겠습니까?')) {
        var bom_idx = $(this).closest('.div_part').attr('bom_idx');
        var bit_idx = $(this).closest('.div_part').attr('bit_idx');
        console.log(bom_idx+'/'+bit_idx);
        //-- 디버깅 Ajax --//
        $.ajax({
            url:g5_user_admin_url+'/ajax/bom_item.json.php',
            data:{"aj":"c1","bom_idx":bom_idx,"bit_idx":bit_idx},
            dataType:'json', 
            timeout:10000, 
            success:function(res){
                // alert(res);
                self.location.reload();
            },
            error:function(req) {
                alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText 
                    + ' \n\rresponseText: ' + req.responseText);
            }
        });
    }
});

// 품번 마우스 hover 
$(".div_bom_part_no").on({
    mouseenter: function () {
        // console.log('mouseenter');
        $(this).find('.div_part_no_detail').show();
    },
    mouseleave: function () {
        // console.log('mouseleave');
        $('.div_part_no_detail').hide();
    }    
});

// 엑셀등록 버튼
$( "#btn_excel_upload" ).on( "click", function() {
    $( "#modal01" ).dialog( "open" );
});
$( "#modal01" ).dialog({
    autoOpen: false
    , position: { my: "right-10 top-10", of: "#btn_excel_upload"}
});

// 가격 입력 쉼표 처리
$(document).on( 'keyup','input[name^=bom_price], input[name^=bom_count], input[name^=bom_lead_time], input[name^=bom_min_cnt]',function(e) {
    if(!isNaN($(this).val().replace(/,/g,'')))
        $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
});

// 숫자만 입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}
    

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
    if (!f.file_excel.value) {
        alert('엑셀 파일(.xls)을 입력하세요.');
        return false;
    }
    else if (!f.file_excel.value.match(/\.xls$|\.xlsx$/i) && f.file_excel.value) {
        alert('엑셀 파일만 업로드 가능합니다.');
        return false;
    }

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
