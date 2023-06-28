<?php
$sub_menu = "922150";
include_once('./_common.php');
auth_check($auth[$sub_menu],'w');
if($w == ''){
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
}
else if($w == 'u'){
    $u_display_none = ';display:none;';  // 수정에서 숨김

    if($mtyp == 'mto'){
        $sql = " SELECT mto_idx
                        , mto.cst_idx
                        , cst.cst_name
                        , mto_type
                        , mto_location
                        , mto_input_date
                        , mto_memo
                        , mto_status
                        , mto_reg_dt
                        , mto_update_dt
             FROM {$g5['material_order_table']} mto
             LEFT JOIN {$g5['customer_table']} cst ON mto.cst_idx = cst.cst_idx
             WHERE mto_idx = '{$mto_idx}'
        ";
    }
    else if($mtyp == 'moi'){
        $sql = " SELECT moi_idx
                        , moi.mto_idx
                        , mto.cst_idx
                        , cst.cst_name
                        , moi.bom_idx
                        , bom.bom_part_no
                        , bom.bom_name
                        , moi_count
                        , moi_price
                        , mb_id_driver
                        , mb_id_check
                        , moi_input_date
                        , moi_input_dt
                        , moi_check_yn
                        , moi_check_text
                        , moi_memo
                        , moi_status
                        , moi_reg_dt
                        , moi_update_dt
             FROM {$g5['material_order_item_table']} moi
             LEFT JOIN {$g5['bom_table']} bom ON moi.bom_idx = bom.bom_idx
             LEFT JOIN {$g5['material_order_table']} mto ON mto.mto_idx = moi.mto_idx
             LEFT JOIN {$g5['customer_table']} cst ON mto.cst_idx = cst.cst_idx
             WHERE moi_idx = '{$moi_idx}'
        ";
    }
    // echo $sql;exit;
    $row = sql_fetch($sql,1);
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


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


$html_title = ($w=='')?'추가':'수정';
$g5['title'] = '발주 '.(($mtyp == 'moi')?'제품':'').$html_title;
include_once ('./_head.php');
?>
<style>
#mto_idx{width:100px;text-align:center;}
#mto_input_date{width:90px;}
</style>
<form name="form01" id="form01" action="./material_order_form_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$mtyp?>_idx" value="<?php echo ${$mtyp."_idx"} ?>">
<input type="hidden" name="mtyp" value="<?=$mtyp?>">
<input type="hidden" name="sch_from_date" value="<?=$sch_from_date?>">
<input type="hidden" name="sch_to_date" value="<?=$sch_to_date?>">

<div class="local_desc01 local_desc" style="display:none;">
    <p>발주관리 페이지입니다.</p>
</div>
<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4" style="width:10%;">
		<col style="width:40%;">
		<col class="grid_4" style="width:10%;">
		<col style="width:40%;">
    </colgroup>
    <tbody>
    <?php if($mtyp == 'mto'){ ?>
    <tr>
        <th scope="row">업체선택</th>
        <td>
            <input type="hidden" name="cst_idx" value="<?=$row['cst_idx']?>">
			<input type="text" name="cst_name" value="<?=$row['cst_name']?>" class="frm_input required readonly" required readonly>
            <?php if(false){ ?>
            <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=provider" class="btn btn_02 btn_customer">찾기</a>
            <?php } ?>
        </td>
        <th scope="row">매입형태</th>
        <td>
            <select name="mto_type" id="mto_type">
            <?=$g5['set_mto_type_value_options']?>
            </select>
            <?php if($w == 'u'){ ?>
            <script>
            $('#mto_type').val('<?=$row['mto_type']?>')
            </script>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th scope="row">납기예정일</th>
        <td <?=(($w == '')?'colspan="3"':'')?>>
            <input type="text" name="mto_input_date" value="<?=$row['mto_input_date']?>" readonly class="frm_input" id="mto_input_date">
        </td>
        <?php if($w == 'u'){ ?>
        <th scope="row">금액</th>
        <td><?=number_format($row['mto_price'])?></td>
        <?php } ?>
    </tr>
    <tr>
        <th scope="row">메모</th>
        <td colspan="3">
            <textarea name="mto_memo" rows="5"><?=$row['mto_memo']?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">상태</th>
        <td colspan="3">
            <select name="mto_status" id="mto_status">
            <?=$g5['set_mto_status_value_options']?>
            </select>
            <?php if($w == 'u'){ ?>
            <script>
            $('#mto_status').val('<?=$row['mto_status']?>')
            </script>
            <?php } ?>
        </td>
    </tr>
    <?php } else if ($mtyp == 'moi'){ ?>
    <tr>
        <th scope="row">발주ID선택</th>
        <td>
            <input type="hidden" name="mto_idx" id="mto_idx" value="<?=$row['mto_idx']?>">
            <input type="text" name="moi_idx" id="moi_idx" value="<?=$row['moi_idx']?>" class="frm_input required readonly" required readonly style="width:90px;">
            <input type="hidden" name="cst_idx" id="cst_idx" value="<?=$row['cst_idx']?>">
            <?php if($w == ''){ ?>
            <a href="./material_order_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02 btn_material_order">찾기</a>
            <?php } ?>
        </td>
        <th scope="row">제품선택</th>
        <td>
            <input type="hidden" name="bom_idx" id="bom_idx" value="<?=$row['bom_idx']?>">
			<input type="text" name="bom_name" id="bom_name" value="<?=$row['bom_name']?>" class="frm_input required readonly" required readonly>
            <span class="span_bom_part_no font_size_8"><?=$row['bom_part_no']?></span>
            <?php if($w == ''){ ?>
            <a href="./bom_select.php?file_name=<?=$g5['file_name']?>&item=provider" link="./bom_select.php?file_name=<?=$g5['file_name']?>&item=provider" class="btn btn_02 btn_bom">찾기</a>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th scope="row">발주량</th>
        <td>
            <input type="text" name="moi_count" id="moi_count" value="<?=number_format($row['moi_count'])?>" class="frm_input moi_count" onclick="javascript:numtoprice(this)">
        </td>
        <th scope="row">납기예정일</th>
        <td>
            <input type="text" name="moi_input_date" value="<?=$row['moi_input_date']?>" readonly class="frm_input" id="moi_input_date">
        </td>
    </tr>
    <tr>
        <th scope="row">메모</th>
        <td colspan="3">
            <textarea name="moi_memo" rows="5"><?=$row['moi_memo']?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">검사자</th>
        <td>
            <input type="hidden" name="mb_id_check" value="<?=$row['mb_id_check']?>">
            <?php
            $mbr = get_meta('member', $row['mb_id_check']);
            echo ($mbr['mb_name']) ? $mbr['mb_name'] : '-';
            ?>
        </td>
        <th scope="row">납기완료일시</th>
        <td>
            <input type="hidden" name="moi_input_dt" value="<?=$row['moi_input_dt']?>">
            <?=(($row['moi_input_dt'] != '0000-00-00 00:00:00')?$row['moi_input_dt']:'-')?>
        </td>
    </tr>
    <tr>
        <th scope="row">반려사유</th>
        <td colspan="3">
            <textarea name="moi_check_text" rows="5"><?=$row['moi_check_text']?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">입고검사완료여부</th>
        <td>
            <input type="hidden" name="moi_check_yn" value="<?=($row['moi_check_yn'])?'1':''?>">
            <label><input type="checkbox" <?=($row['moi_check_yn'])?'checked':''?> id="moi_check_yn"> 입고검사완료</label>
            <script>
            $(document).on('click','#moi_check_yn',function(e){
                if($(this).is(':checked')) {$('input[name=moi_check_yn]').val(1);}
                else {$('input[name=moi_check_yn]').val(0);}
            });
            </script>
        </td>
        <th scope="row">입고기사</th>
        <td>
            <input type="hidden" name="mb_id_driver" value="<?=$row['mb_id_driver']?>">
            <?php
            $mbr = get_meta('member', $row['mb_id_driver']);
            echo ($mbr['mb_name']) ? $mbr['mb_name'] : '-';
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row">상태</th>
        <td colspan="3">
            <select name="moi_status" id="moi_status">
            <?=$g5['set_moi_status_value_options']?>
            </select>
            <?php if($w == 'u'){ ?>
            <script>
            $('#moi_status').val('<?=$row['moi_status']?>')
            </script>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <a href="./material_order_list.php?<?=$qstr?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
var mtyp = '<?=$mtyp?>';
$(function(){
    if(mtyp == 'mto'){
        $("#mto_input_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
        
        
    }
    else if(mtyp == 'moi'){
        $("#moi_input_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"});
        // 발주찾기 버튼 클릭
        $(".btn_material_order").click(function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            winMaterialOrderSelect = window.open(href, "winMaterialOrderSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
            winMaterialOrderSelect.focus();
        });
        // 제품찾기 버튼 클릭
        $(".btn_bom").click(function(e) {
            e.preventDefault();
            if(!$('#mto_idx').val()){
                alert('먼저 발주ID를 선택해 주세요.');
                $('#mto_idx').focus();
                return false;
            }
            var href = $(this).attr('href');
            winMaterialOrderSelect = window.open(href, "winMaterialOrderSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
            winMaterialOrderSelect.focus();
        });
    }
});


function form01_submit(f) {
    // if(f.ori_count.value <= 0) {
    //     alert('수량을 입력하세요.');
    //     f.ori_count.focus();
    //     return false;
    // }
    if(mtyp == 'mto'){

    }
    else if(mtyp == 'moi'){

    }

    return true;
}
</script>

<?php
include_once ('./_tail.php');