<?php
$sub_menu = "922140";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'item';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
//$qstr .= '&st_date='.$st_date.'&en_date='.$en_date; // 추가로 확장해서 넘겨야 할 변수들
// 추가 변수 생성
foreach($_REQUEST as $key => $value ) {
    if(substr($key,0,4)=='ser_') {
    //    print_r3($key.'='.$value);
        if(is_array($value)) {
            foreach($value as $k2 => $v2 ) {
//                print_r3($key.$k2.'='.$v2);
                $qstr .= '&'.$key.'[]='.$v2;
                $form_input .= '<input type="hidden" name="'.$key.'[]" value="'.$v2.'" class="frm_input">'.PHP_EOL;
            }
        }
        else {
            $qstr .= '&'.$key.'='.$value;
            $form_input .= '<input type="hidden" name="'.$key.'" value="'.$value.'" class="frm_input">'.PHP_EOL;
        }
    }
}

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김

    ${$pre}[$pre.'_defect'] = 0;
    ${$pre}[$pre.'_status'] = 'finish';
}
else if ($w == 'u') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

    ${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
    // print_r3(${$pre});
    $cst_customer = get_table_meta('customer', 'cst_idx', ${$pre}['cst_idx_customer']);
    $cst_provider = get_table_meta('customer', 'cst_idx', ${$pre}['cst_idx_provider']);
    $mb1 = get_table_meta('member', 'mb_id', ${$pre}['mb_id']);
    $mms = get_table_meta('mms', 'mms_idx', ${$pre}['mms_idx']);
    $ori = get_table_meta('order_item', 'ori_idx', ${$pre}['ori_idx']);
    $bom = get_table_meta('bom', 'bom_idx', ${$pre}['bom_idx']);
    $shf = get_table_meta('shift', 'shf_idx', ${$pre}['shf_idx']);
    $plt = get_table_meta('pallet', 'plt_idx', ${$pre}['plt_idx']);
    // print_r3($mms);
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_field');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'등록':'수정';
$g5['title'] = '재고 '.$html_title;
include_once ('./_head.php');
?>
<style>
.div_mip {color:#818181;}
.mip_price {font-size:0.8em;color:#a9a9a9;margin-left:10px;}
.btn_mip_delete {border:solid 1px #ddd;border-radius:3px;padding:1px 4px;font-size:0.7em;margin-left:10px;}
.div_empty {color:#818181;}
.btn_mip_delete {cursor:pointer;}
.change_alert{color: #e61212;}
.row_chk{float: right;font-weight: normal;}
label[for^=itm_defect_type] {margin-right:5px;}
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<?=$form_input?>


<div class="local_desc01 local_desc" style="display:no ne;">
    <p>모든 항목의 내용을 입력한 후 [확인]을 클릭하세요.</p>
</div>

<div id="anc_basic" class="tbl_frm01 tbl_wrap">
	<table>
		<colgroup>
            <col class="grid_4" style="width:10%;">
            <col style="width:40%;">
            <col class="grid_4" style="width:10%;">
            <col style="width:40%;">
		</colgroup>
		<tbody>
        <tr>
            <th scope="row">수주선택</th>
            <td>
                <input type="text" name="ori_idx" value="<?=${$pre}['ori_idx']?>" class="frm_input required readonly" style="width:90px" required readonly>
                <a href="./order_item_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_order_item">찾기</a>
                <span class="span_ori_detail font_size_8"><?=$ori['ori_detail']?></span>
            </td>
            <th scope="row">품명/품번</th>
            <td>
                <input type="hidden" name="bom_idx" value="<?=${$pre}['bom_idx']?>">
                <input type="text" name="bom_name" value="<?=$bom['bom_name']?>" class="frm_input required readonly" required readonly>
                <span class="span_bom_part_no font_size_8"><?=$bom['bom_part_no']?></span>
                <a href="./bom_select.php?file_name=<?=$g5['file_name']?>&item=customer" class="btn btn_02 btn_bom">찾기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">고객사선택</th>
            <td>
                <input type="hidden" name="cst_idx_customer" value="<?=${$pre}['cst_idx_customer']?>" class="frm_input">
                <input type="text" name="cst_name_customer" value="<?=$cst_customer['cst_name']?>" class="frm_input" style="width:300px;" readonly>
                <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=customer" class="btn btn_02 btn_customer">찾기</a>
            </td>
            <th scope="row">공급사선택</th>
            <td>
                <input type="hidden" name="cst_idx_provider" value="<?=${$pre}['cst_idx_provider']?>" class="frm_input">
                <input type="text" name="cst_name_provider" value="<?=$cst_provider['cst_name']?>" class="frm_input" style="width:300px;" readonly>
                <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=provider" class="btn btn_02 btn_customer">찾기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">작업자선택</th>
            <td>
                <input type="hidden" name="mb_id" value="<?=${$pre}['mb_id']?>" class="frm_input" style="width:100px">
                <input type="text" name="mb_name" value="<?=$mb1['mb_name']?>" id="mb_name" class="frm_input" style="width:100px;" readonly>
                <a href="./member_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_member">찾기</a>
            </td>
            <th scope="row">설비선택</th>
            <td>
                <input type="hidden" name="mms_idx" value="<?=${$pre}['mms_idx']?>" class="frm_input">
                <input type="text" name="mms_name" value="<?=$mms['mms_name']?>" class="frm_input" style="width:300px;" readonly>
                <a href="./mms_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_mms">찾기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">작업구간</th>
            <td>
                <input type="hidden" name="shf_idx" value="<?=${$pre}['shf_idx']?>">
                <input type="text" name="shf_name" value="<?=$shf['shf_name']?>" class="frm_input required readonly" style="width:90px" required readonly>
                <a href="./shift_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_shift">찾기</a>
            </td>
            <th scope="row">파레트번호</th>
            <td>
                <input type="text" name="plt_idx" value="<?=${$pre}['plt_idx']?>" class="frm_input required readonly" style="width:90px" required readonly>
                <a href="./pallet_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_pallet">찾기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">제품타입</th>
            <td>
                <select name="<?=$pre?>_type" id="<?=$pre?>_type">
                    <option value="">선택하세요.</option>
                    <?=$g5['set_itm_type_options']?>
                </select>
                <script>$('select[name="<?=$pre?>_type"]').val('<?=${$pre}[$pre.'_type']?>');</script>
            </td>
            <?php
            $ar['id'] = $pre.'_barcode';
            $ar['name'] = '바코드';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '200px';
            echo create_td_input($ar);
            unset($ar);
            ?>
        </tr>
        <tr>
            <?php
            $ar['id'] = $pre.'_value';
            $ar['name'] = '값(수량)';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '60px';
            echo create_td_input($ar);
            unset($ar);
            ?>
            <?php
            $ar['id'] = $pre.'_lot';
            $ar['name'] = 'LOT번호';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '60px';
            echo create_td_input($ar);
            unset($ar);
            ?>
        </tr>
        <tr>
            <?php
            $ar['id'] = $pre.'_price';
            $ar['name'] = '단가';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '80px';
            echo create_td_input($ar);
            unset($ar);
            ?>
            <?php
            $ar['id'] = 'trm_idx_location';
            $ar['name'] = '위치';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '80px';
            echo create_td_input($ar);
            unset($ar);
            ?>
        </tr>
        <tr>
            <?php
            $ar['id'] = $pre.'_auth_dt';
            $ar['name'] = '승인일시';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '150px';
            echo create_td_input($ar);
            unset($ar);
            ?>
            <?php
            $ar['id'] = $pre.'_delivery_dt';
            $ar['name'] = '출하일시';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '150px';
            echo create_td_input($ar);
            unset($ar);
            ?>
        </tr>
        <tr>
            <?php
            $ar['id'] = $pre.'_date';
            $ar['name'] = '통계일';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '88px';
            echo create_td_input($ar);
            unset($ar);
            ?>
            <?php
            $ar['id'] = $pre.'_reg_dt';
            $ar['name'] = '등록일시';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '150px';
            echo create_td_input($ar);
            unset($ar);
            ?>
        </tr>
        <?php
        $ar['id'] = $pre.'_memo';
        $ar['name'] = '메모';
        $ar['type'] = 'textarea';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['colspan'] = 3;
        echo create_tr_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = $pre.'_history';
        $ar['name'] = '히스토리';
        $ar['type'] = 'textarea';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['colspan'] = 3;
        echo create_tr_input($ar);
        unset($ar);
        ?>
        <tr class="tr_itm_defect_type" style="display:<?=(${$pre}[$pre.'_status']=='defect')?'':'none'?>;">
            <th scope="row">불량타입</th>
            <td colspan="3">
                <?php
                if(is_array($g5['set_defect_type_value'])) {
                    foreach ($g5['set_defect_type_value'] as $k1=>$v1) {
                        if(in_array($k1,array('trash'))) {continue;}
                        echo '<input type="radio" name="itm_defect_type" id="itm_defect_type_'.$k1.'" value="'.$k1.'">
                                <label for="itm_defect_type_'.$k1.'">'.$v1.'</label>'.PHP_EOL;
                    }
                }
                ?>
                <script>$('#itm_defect_type_<?=${$pre}['itm_defect_type']?>').attr('checked','checked');</script>
                <script>
                    $(document).on('change','select[name=itm_status]',function(e){
                        // 불량이 체크된 경우
                        if( $(this).val() == 'defect' ) {
                            $('.tr_itm_defect_type').show();
                        }
                        else {
                            $('.tr_itm_defect_type').hide();
                        }
                    });
                </script>
            </td>
        </tr>
        <tr>
            <th scope="row">상태</th>
            <td colspan="3">
                <select name="<?=$pre?>_status" id="<?=$pre?>_status">
                    <option value="">선택하세요.</option>
                    <?=$g5['set_'.$pre.'_status_options']?>
                </select>
                <script>$('select[name="<?=$pre?>_status"]').val('<?=${$pre}[$pre.'_status']?>');</script>
            </td>
        </tr>
    </tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="./<?=$fname?>_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function(e) {

    // 업체 찾기
    $(document).on('click','.btn_customer',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winLanding = window.open(href, "winLanding", "left=100,top=100,width=520,height=600,scrollbars=1");
        winLanding.focus();
        return false;

    });

    // 회원 찾기
    $(document).on('click','.btn_member',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winMember = window.open(href, "winMember", "left=100,top=100,width=520,height=600,scrollbars=1");
        winMember.focus();
        return false;

    });

    // 설비 찾기
    $(document).on('click','.btn_mms',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winMms = window.open(href, "winMms", "left=100,top=100,width=520,height=600,scrollbars=1");
        winMms.focus();
        return false;

    });

    // 수주 찾기
	$(".btn_order_item").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winOrderItem = window.open(href, "winOrderItem", "left=300,top=150,width=550,height=600,scrollbars=1");
        winOrderItem.focus();
	});

    // 품번 찾기
	$(".btn_bom").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winBom = window.open(href, "winBom", "left=300,top=150,width=550,height=600,scrollbars=1");
        winBom.focus();
	});

    // 작업구간 찾기
	$(".btn_shift").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winShift = window.open(href, "winShift", "left=300,top=150,width=550,height=600,scrollbars=1");
        winShift.focus();
	});

    // 파레트 찾기
	$(".btn_pallet").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winPallet = window.open(href, "winPallet", "left=300,top=150,width=550,height=600,scrollbars=1");
        winPallet.focus();
	});

    // 달력 datapicker
    $("input[name$=_date], #apc_birth").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });


});

function form01_submit(f) {
    // // 사진 파일
    // if (!$('input[name^=applicant_list_file]').val().match(/\.(gif|jpe?g|png)$/i) && $('input[name^=applicant_list_file]').val()) {
    //     alert('사진은 이미지 파일만 가능합니다.(gif, jpg, png)');
    //     return false;
    // }

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
