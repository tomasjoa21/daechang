<?php
$sub_menu = "922120";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'production_item_count';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명

$qstr .= "&st_date=$st_date&en_date=$en_date"; // 추가로 확장해서 넘겨야 할 변수들
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
    $mb1 = get_table_meta('member', 'mb_id', ${$pre}['mb_id']);
    $pri = get_table_meta('production_item', 'pri_idx', ${$pre}['pri_idx']);
    $bom = get_table_meta('bom', 'bom_idx', $pri['bom_idx']);
    $cst_customer = get_table_meta('customer', 'cst_idx', $bom['cst_idx_customer']);
    $bct = get_table('bom_category','bct_idx',$bom['bct_idx'],'bct_name');
    $mms = get_table_meta('mms', 'mms_idx', $pri['mms_idx']);
    $prd = get_table_meta('production', 'prd_idx', $pri['prd_idx']);
    $ori = get_table_meta('order_item', 'ori_idx', $prd['ori_idx']);
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
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="st_date" value="<?=$st_date?>">
<input type="hidden" name="en_date" value="<?=$en_date?>">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<?=$form_input?>


<div class="local_desc01 local_desc" style="display:no ne;">
    <p>모든 항목의 내용을 입력한 후 [확인]을 클릭하세요.</p>
</div>

<div id="anc_basic" class="tbl_frm01 tbl_wrap">
	<table>
		<colgroup>
            <col class="grid_4" style="width:15%;">
            <col style="width:35%;">
            <col class="grid_4" style="width:10%;">
            <col style="width:40%;">
		</colgroup>
		<tbody>
        <tr style="display:<?=($w!='u')?'none':''?>;">
            <th scope="row">생산정보</th>
            <td colspan="3">
                <div>납품처: <?=$cst_customer['cst_name']?></div>
                <div>품번/품명: <?=$bom['bom_part_no']?> &nbsp;&nbsp;&nbsp; <?=$bom['bom_name']?></div>
                <div>차종: <?=$bct['bct_name']?></div>
            </td>
        </tr>
        <tr>
            <th scope="row">생산(아이템)선택</th>
            <td>
                <input type="hidden" name="prd_idx" value="<?=${$pre}['prd_idx']?>" class="frm_input">
                <input type="text" name="pri_idx" value="<?=${$pre}['pri_idx']?>" class="frm_input" style="width:70px;" readonly>
                <a href="./production_item_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_production_item">찾기</a>
            </td>
            <th scope="row">작업자선택</th>
            <td>
                <input type="hidden" name="mb_id" value="<?=${$pre}['mb_id']?>" class="frm_input" style="width:100px">
                <input type="text" name="mb_name" value="<?=$mb1['mb_name']?>" id="mb_name" class="frm_input" style="width:100px;" readonly>
                <a href="./member_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_member">찾기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">작업상태</th>
            <td>
                <select name="<?=$pre?>_ing" id="<?=$pre?>_ing">
                    <option value="">선택하세요.</option>
                    <?=$g5['set_pri_ing_options']?>
                </select>
                <script>$('select[name="<?=$pre?>_ing"]').val('<?=${$pre}[$pre.'_ing']?>');</script>
            </td>
            <?php
            $ar['id'] = $pre.'_value';
            $ar['name'] = '값(수량)';
            $ar['type'] = 'input';
            $ar['value'] = ${$pre}[$ar['id']];
            $ar['width'] = '60px';
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

    // 생산(아이템) 찾기
    $(document).on('click','.btn_production_item',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        winProductionItem = window.open(href, "winProductionItem", "left=100,top=100,width=520,height=600,scrollbars=1");
        winProductionItem.focus();
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
