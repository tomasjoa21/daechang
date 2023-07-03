<?php
$sub_menu = "922150";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '발주관리';
include_once('./_top_menu_material_order.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$mtyp = ($mtyp) ? $mtyp:'moi'; //moi OR mto

$ctm_sql = " SELECT cst_idx, ctm_title FROM {$g5['customer_member_table']}
                WHERE mb_id = '{$member['mb_id']}'
                    AND ctm_title != '13'
";    
$cst = sql_fetch($ctm_sql);
//협력업체idx != 대창공업idx && 업체직함 != '기사' => '공급업체담당자'
$provider_member_yn = ($member['mb_8'] && $member['mb_8'] != $_SESSION['ss_com_idx'] && $cst['ctm_title'] != '13') ? true : false;

if($mtyp == 'mto'){
    $sql_common = " FROM {$g5['material_order_table']} mto
                        LEFT JOIN {$g5['customer_table']} cst ON mto.cst_idx = cst.cst_idx
    ";

    $where = array();
    //디폴트 검색조건
    $where[] = " mto_status NOT IN ('trash','delete') ";
    $where[] = " mto.com_idx = '{$_SESSION['ss_com_idx']}' ";
    
    $sst2 = '';
    $sod2 = '';
}
else{
    $sql_common = " FROM {$g5['material_order_item_table']} moi
                        LEFT JOIN {$g5['material_order_table']} mto ON moi.mto_idx = mto.mto_idx
                        LEFT JOIN {$g5['bom_table']} bom ON moi.bom_idx = bom.bom_idx
                        LEFT JOIN {$g5['customer_table']} cst ON mto.cst_idx = cst.cst_idx
    ";
    //디폴트 검색조건
    if($provider_member_yn){
        $where[] = " moi_status NOT IN ('trash','delete','pending','cancel','reject') ";
    }else{
        $where[] = " moi_status NOT IN ('trash','delete') ";
    }
    $where[] = " mto.com_idx = '{$_SESSION['ss_com_idx']}' ";

    
    if(!$sst2){
        $sst2 = ", moi_idx";
        $sod2 = "DESC";
    }
}
//협력업체 회원이면 해당업체 목록만 보여주자
if($provider_member_yn){
    $where[] = " mto.cst_idx = '{$cst['cst_idx']}' ";
}

//검색어 설정
if($stx != '') {
    switch($sfl){
        case ($sfl == 'bom_part_no'):
            $where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        default:
            $where[] = " {$sfl} LIKE '%".trim($stx)."%' ";
            break;
    }
}

if($sch_from_date && !$sch_to_date){
    $where[] = " {$mtyp}_input_date >= '".$sch_from_date."' ";
}
else if(!$sch_from_date && $sch_to_date){
    $where[] = " {$mtyp}_input_date <= '".$sch_to_date."' ";
}
else if($sch_from_date && $sch_to_date){
    $where[] = " {$mtyp}_input_date >= '".$sch_from_date."' ";
    $where[] = " {$mtyp}_input_date <= '".$sch_to_date."' ";
}

//최종 WHERE 생성
if($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if(!$sst){
    $sst = "mto.mto_idx";
    $sod = "DESC";
}

$sql_order = " ORDER BY {$sst} {$sod} {$sst2} {$sod2} ";
$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page = ceil($total_count / $rows); //전체 페이지 계산
if($page < 1) $page = 1; //페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; //시작 열을 구함

if($mtyp == 'moi'){
    $column_list = " moi_idx
                    , moi.mto_idx
                    , moi.bom_idx
                    , bom.bct_idx
                    , bom.bom_part_no
                    , bom.bom_name
                    , mto.cst_idx
                    , cst.cst_name
                    , moi_count
                    , moi_price
                    , moi_input_date
                    , moi_memo
                    , moi_status
                    , moi_reg_dt
                    , moi_update_dt

    ";
}
else if($mtyp == 'mto'){
    $column_list = " mto.mto_idx
                    , mto.cst_idx
                    , cst.cst_name
                    , mto.mb_id
                    , mto_price
                    , mto_input_date
                    , mto_location
                    , mto_memo
                    , mto_type
                    , mto_status
                    , mto_reg_dt
                    , mto_update_dt
    ";
}

$sql = " SELECT {$column_list}
            {$sql_common} {$sql_search} {$sql_order}
            LIMIT {$from_record}, {$rows} 
";
// echo $sql;
$result = sql_query($sql, 1);
$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].(($mtyp)?'?mtyp='.$mtyp:'').'" class="ov_listall">전체목록</a>';

// 추가로 확장해서 넘겨야 할 변수들
if($mtyp){
    $qstr .= '&mtyp='.$mtyp; 
}
if($sch_from_date){
    $qstr .= '&sch_from_date='.$sch_from_date; 
}
if($sch_to_date){
    $qstr .= '&sch_to_date='.$sch_to_date; 
}
$colspan = ($mtyp == 'moi') ? 15 : 12;
$colspan = ($provider_member_yn) ? $colspan - 1 : $colspan;
?>
<style>
.td_orange_bold{color:orange !important;font-weight:700;}
.td_skyblue_bold{color:skyblue !important;font-weight:700;}
.td_qr i{font-size:2em;cursor:pointer;}
</style>
<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <?php if($member['mb_4'] == $_SESSION['ss_com_idx'] && $member['mb_8'] == ''){ ?>
            <option value="cst_name"<?php echo get_selected($_GET['sfl'], "cst_name"); ?>>협력사명</option>
        <?php } ?>
        <?php if($mtyp == 'moi'){ ?>
            <option value="bom.bom_part_no"<?php echo get_selected($_GET['sfl'], "bom_part_no"); ?>>품목코드</option>
            <option value="bom_name"<?php echo get_selected($_GET['sfl'], "bom_name"); ?>>품명</option>
            <option value="moi_status"<?php echo get_selected($_GET['sfl'], "moi_status"); ?>>상태</option>
        <?php } else { ?>
            <option value="mto_status"<?php echo get_selected($_GET['sfl'], "mto_status"); ?>>상태</option>
        <?php } ?>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <label for="sch_from_date" class="sch_label" style="display:no ne;">
        <input type="text" name="sch_from_date" value="<?php echo $sch_from_date ?>" id="sch_from_date" readonly class="frm_input readonly" placeholder="납기일시작" style="width:120px;" autocomplete="off">
    </label>
    <label for="sch_to_date" class="sch_label" style="display:no ne;">
        <input type="text" name="sch_to_date" value="<?php echo $sch_to_date ?>" id="sch_to_date" readonly class="frm_input readonly" placeholder="납기일종료" style="width:120px;" autocomplete="off">
    </label>
    <input type="submit" class="btn_submit" value="검색">

    <ul class="view_type">
        <li><a href="<?=(($mtyp == 'moi')?'javascript:':G5_USER_ADMIN_URL.'/material_order_list.php')?>" class="<?=(($mtyp == 'moi')?'focus':'')?>">개별관리</a></li>
        <?php if($member['mb_4'] == $_SESSION['ss_com_idx'] && !$member['mb_8']){ ?>
        <li><a href="<?=(($mtyp == 'mto')?'javascript:':G5_USER_ADMIN_URL.'/material_order_list.php?mtyp=mto')?>" class="<?=(($mtyp == 'mto')?'focus':'')?>">묶음관리</a></li>
        <?php } ?>
    </ul>
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>
        <?php if($mtyp == 'moi'){ ?>
        각 제품별 발주내용을 확인하는 페이지 입니다.
        <?php } else { ?>
        각 발주단위별 내용을 확인하는 페이지 입니다.
        <?php } ?>
    </p>
</div>

<form name="form01" id="form01" action="./material_order_list_update.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="mtyp" value="<?php echo $mtyp ?>">
<input type="hidden" name="sch_from_date" value="<?php echo $sch_from_date ?>">
<input type="hidden" name="sch_to_date" value="<?php echo $sch_to_date ?>">
<input type="hidden" name="qstr" value="<?php echo $qstr ?>">

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
        <th scope="col">공급업체</th>
        <?php if($mtyp == 'moi'){ ?>
        <th scope="col">차종</th>
        <th scope="col">품번/품명</th>
        <th scope="col">QR코드</th>
        <th scope="col">메모</th>
        <th scope="col">발주ID</th>
        <th scope="col">발주량</th>
        <th scope="col">입고</th>
        <th scope="col">미입고</th>
        <th scope="col">가격</th>
        <?php } ?>
        <?php if($mtyp == 'mto'){ ?>
        <th scope="col">메모</th>
        <th scope="col">발주건수</th>
        <th scope="col">매입형태</th>
        <th scope="col">납품장소</th>
        <th scope="col">발주담당자</th>
        <th scope="col">가격</th>
        <?php } ?>
        <th scope="col">납기일</th>
        <th scope="col">상태</th>
        <?php if(!$provider_member_yn) { ?><th scope="col">관리</th><?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    // print_r2($g5);
    $status_arr = $g5['set_'.$mtyp.'_status_value'];
    $status_skips = array('pending','input','cancel','reject');
    for($i=0;$row=sql_fetch_array($result);$i++){
        // print_r2($row);
        if($mtyp == 'moi'){
            $mtr = sql_fetch(" SELECT SUM(mtr_value) AS input_sum 
                        FROM {$g5['material_table']} mtr 
                        WHERE bom_idx = '{$row['bom_idx']}'
                            AND moi_idx = '{$row['moi_idx']}'
                            AND moi_status IN('ok','used','delivery','scrap')
                        GROUP BY moi_idx
            ");
            $row['no_input_cnt'] = $row['moi_count'] - $mtr['input_sum'];
            $row['input_cnt'] = $mtr['input_sum'];
            $row['sum_price'] = $row['moi_count'] * $row['moi_price'];
        }
        else{
            $moi_sql = "SELECT COUNT(moi_idx) AS cnt
                            , SUM(moi_price * moi_count) AS mto_price
                        FROM {$g5['material_order_item_table']} moi
                        WHERE mto_idx = '{$row['mto_idx']}'
                            AND moi_status IN('pending','ok','used','delivery','scrap')
                        GROUP BY mto_idx
            ";
            // echo $moi_sql."<br>";
            $moi = sql_fetch($moi_sql);
            $row['moi_cnt'] = $moi['cnt'];
            $row['mto_price'] = ($row['mto_price'])?$row['mto_price']:$moi['mto_price'];

            $mem = sql_fetch(" SELECT mb_name FROM {$g5['member_table']} WHERE mb_id = '{$row['mb_id']}'
            ");
            $row['mb_name'] = $mem['mb_name'];
        }
        $s_mod = '<a href="./material_order_form.php?'.$qstr.'&amp;w=u&amp;'.$mtyp.'_idx='.$row[$mtyp.'_idx'].'">수정</a>';
    ?>
    <tr class="<?=$bg?>" tr_id="<?=$row[$mtyp.'_idx']?>">
        <td class="td_chk">
            <label for="chk_<?=$i?>" class="sound_only"><?php echo get_text($row[$mtyp.'_idx']); ?></label>
            <input type="checkbox" name="chk[]" value="<?=$row[$mtyp.'_idx']?>" id="chk_<?=$i?>">
            <div class="chkdiv_btn" chk_no="<?=$i?>"></div>
        </td><!--체크박스-->
        <td class="td_<?=$mtyp?>_idx"><?=$row[$mtyp.'_idx']?></td><!--ID-->
        <td class="td_com_name"><?=$row['cst_name']?></td><!--공급업체-->
        <?php if($mtyp == 'moi'){ ?>
        <td class="td_bct_idx"><?=$g5['cats_key_val'][$row['bct_idx']]?></td>
        <td class="td_bom_name"><span style="color:orange;"><?=$row['bom_part_no']?></span><br><?=$row['bom_name']?></td><!--제품명-->
        <td class="td_qr">
            <?php if($row['moi_status'] == 'ready'){ //(true){ //($row['moi_status'] == 'ready') { ?>
                <i class="fa fa-qrcode moi_qr" aria-hidden="true" moi="<?=$row['moi_idx']?>" cnt="<?=$row['moi_count']?>" no="<?=$row['bom_part_no']?>"></i>
            <?php } else { ?>
                -
            <?php } ?>
        </td>
        <td class="td_<?=$mtyp?>_memo">
            <?php
            if($row[$mtyp.'_memo']){
                $memo_no_tag = strip_tags($row[$mtyp.'_memo']);
                $memo_trim = trim($memo_no_tag);
                $memo_replace = str_replace('&nbsp;','',$memo_trim);
                echo cut_str($memo_replace,10,'...');
                echo '<pre>'.$memo_trim.'</pre>';
            }
            ?>
        </td><!--메모-->
        <td class="td_mto_idx">
            <input type="hidden" name="mto_idx[<?=$row[$mtyp.'_idx']?>]" value="<?=$row['mto_idx']?>">
            <?=$row['mto_idx']?>
        </td><!--발주ID-->
        <td class="td_moi_count<?=(($provider_member_yn)?' td_orange_bold':'')?>">
            <?php if($provider_member_yn){ ?>
                <input type="hidden" name="moi_count[<?=$row[$mtyp.'_idx']?>]" value="<?=number_format($row['moi_count'])?>">
                <?=number_format($row['moi_count'])?>
            <?php } else { ?>
                <input type="text" name="moi_count[<?=$row[$mtyp.'_idx']?>]" value="<?=number_format($row['moi_count'])?>" class="frm_input moi_count" onclick="javascript:numtoprice(this)">
            <?php } ?>
        </td><!--발주량-->
        <td class="td_input_cnt"><?=number_format($row['input_cnt'])?></td><!--입고-->
        <td class="td_no_input_cnt"><?=number_format($row['no_input_cnt'])?></td><!--미입고-->
        <td class="td_sum_price"><?=number_format($row['sum_price'])?></td><!--소계가격-->
        <?php } ?>
        <?php if($mtyp == 'mto'){ ?>
        <td class="td_<?=$mtyp?>_memo">
            <?php
            if($row[$mtyp.'_memo']){
                $memo_no_tag = strip_tags($row[$mtyp.'_memo']);
                $memo_trim = trim($memo_no_tag);
                $memo_replace = str_replace('&nbsp;','',$memo_trim);
                echo cut_str($memo_replace,10,'...');
                echo '<pre>'.$memo_trim.'</pre>';
            }
            ?>
        </td><!--메모-->
        <td class="td_moi_cnt"><?=number_format($row['moi_cnt'])?></td><!--발주건수-->
        <td class="td_mto_type">
            <select name="mto_type[<?=$row['mto_idx']?>]" id="mto_type_<?=$i?>">
            <?=$g5['set_mto_type_value_options']?>
            </select>
            <?php if($row['mto_type']){ ?>
            <script>
            $('#mto_type_<?=$i?>').val('<?=$row['mto_type']?>');
            </script>
            <?php } ?>
        </td><!--발주형태-->
        <td class="td_mto_location">
            <select name="mto_location[<?=$row['mto_idx']?>]" id="mto_location_<?=$i?>">
            <?=$g5['mng_input_location_value_options']?>
            </select>
            <?php if($row['mto_location']){ ?>
            <script>
            $('#mto_location_<?=$i?>').val('<?=$row['mto_location']?>');
            </script>
            <?php } ?>
        </td><!--입고장소-->
        <td class="td_mb_id" mb_id="<?=$row['mb_id']?>"><?=$row['mb_name']?></td><!--발주자ID-->
        <td class="td_mto_price"><?=number_format($row['mto_price'])?></td><!--발주총가격-->
        <?php } ?>
        <td class="td_<?=$mtyp?>_input_date<?=(($provider_member_yn)?' td_skyblue_bold':'')?>">
            <?php if($provider_member_yn){ ?>
                <input type="hidden" name="<?=$mtyp?>_input_date[<?=$row[$mtyp.'_idx']?>]" value="<?=$row[$mtyp.'_input_date']?>">
                <?=$row[$mtyp.'_input_date']?>
            <?php } else { ?>
                <input type="text" name="<?=$mtyp?>_input_date[<?=$row[$mtyp.'_idx']?>]" value="<?=$row[$mtyp.'_input_date']?>" readonly class="frm_input <?=$mtyp?>_input_date">
            <?php } ?>
        </td><!--입고예정일-->
        <td class="td_<?=$mtyp?>_status">
            <select name="<?=$mtyp?>_status[<?=$row[$mtyp.'_idx']?>]" id="<?=$mtyp?>_status_<?=$i?>">
                <?php 
                if($provider_member_yn){
                    foreach($status_arr as $sk => $sv){ 
                        if(in_array($sk, $status_skips)) continue;
                ?>
                <option value="<?=$sk?>"><?=$sv?></option>
                <?php 
                    }
                } else {
                    echo $g5['set_'.$mtyp.'_status_value_options'];
                }
                ?>
            </select>
            <?php if($row[$mtyp.'_status']){ ?>
            <script>
            $('#<?=$mtyp?>_status_<?=$i?>').val('<?=$row[$mtyp.'_status']?>');
            </script>
            <?php } ?>
        </td><!--상태-->
        <?php if(!$provider_member_yn) { ?>
        <td class="td_mng"><?=$s_mod?></td><!--관리-->
        <?php } ?>
    </tr>
    <?php
    }
    if($i == 0)
        echo "<tr><td colspan='".$colspan."' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <?php if (!auth_check($auth[$sub_menu],'w') && !$member['mb_8']) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
        <?php if($mtyp == 'moi'){ ?>
        <a href="./material_order_form.php?mtyp=<?=$mtyp?>" id="order_add" class="btn btn_01">추가하기</a>
        <?php } ?>
    <?php } else if($provider_member_yn) { ?>
        <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
$("input[name=sch_from_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect: function(selectedDate){$("input[name=sch_to_date]").datepicker('option','minDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });

$("input[name=sch_to_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect:function(selectedDate){$("input[name=sch_from_date]").datepicker('option','maxDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}}});

$(".moi_input_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
$(".mto_input_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});

$('.td_moi_memo,.td_mto_memo').on('mouseenter',function(){
    $(this).find('pre').addClass('focus');
});

$('.td_moi_memo,.td_mto_memo').on('mouseleave',function(){
    $(this).find('pre').removeClass('focus');
});

//모달관련
let down_url = '';
let down_name = '';
$('.moi_qr').on('click',function(){
    var moi_idx = $(this).attr('moi');
    var moi_cnt = $(this).attr('cnt');
    var chk_url = "<?=G5_USER_ADMIN_MOBILE_URL?>/input_check.php";
    var qr_url = "https://chart.googleapis.com/chart?chs=140x140&cht=qr&chl="+chk_url+"?moi_cnt="+moi_idx+"_"+moi_cnt;
    var bom_part_no = $(this).attr('no');
    var img_tag = '<img src="'+qr_url+'">';

    down_url = qr_url;
    down_name = bom_part_no+'_moi_'+moi_idx+'_cnt_'+moi_cnt+'.png';
    
    $('.mdl_st_ttl').text(bom_part_no);
    $('.mdl_moi_idx').text(moi_idx);
    $('.mdl_bom_part_no').text(bom_part_no);
    $('.mdl_moi_count').text(moi_cnt);
    $('.mdl_qr_img_box').html(img_tag);

    $('.modal').removeClass('mdl_hide');

    mdl_evt_on();
});

function mdl_evt_on(){
    $('.mdl_bg, .mdl_close').on('click',function(){
        $('.mdl_st_ttl').text('');
        $('.mdl_moi_idx').text('');
        $('.mdl_bom_part_no').text('');
        $('.mdl_moi_count').text('');
        $('.mdl_qr_img_box').empty();
        $('.modal').addClass('mdl_hide');

        mdl_evt_off();
    });
    $('.mdl_qr_download').on('click',function(){
        $.ajax({
            url: down_url,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob){
                let url = window.URL.createObjectURL(blob);
                let link = document.createElement("a");
                link.href = url;
                link.download = down_name;

                document.body.appendChild(link);
                link.click();

                $(link).remove();
            },
            error: function(){
                console.log('error');
            }
        });
    });
}

function mdl_evt_off(){
    $('.mdl_bg, .mdl_close').off('click');
    $('.mdl_qr_download').off('click');
    down_url = '';
    down_name = '';
}

function form01_submit(f){
    if(!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }
    <?php if($mtyp == 'moi'){ ?>
    if(!is_exist_order_count()){
        alert("선택된 항목의 발주량을 반드시 입력하셔야 합니다.");
        return false;
    }
    <?php } ?>

    return true;
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
