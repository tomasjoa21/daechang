<?php
$sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

$sql = " SELECT prd.prd_idx
                , prd.bom_idx
                , cst.cst_name
                , bom.bom_name
                , bom.bom_part_no
                , ori.ori_date
                , prd.ori_idx
                , prd_order_no
                , prd_start_date
                , prd_memo
                , prd_status
                , pri.pri_idx
                , pri.mms_idx
                , pri.trm_idx_operation
                , pri.trm_idx_line
                , pri.pri_value
                , pri.pri_memo
                , pri.pri_status
                , pri.pri_reg_dt
                , pri.pri_update_dt
        FROM {$g5['production_table']} prd
            LEFT JOIN {$g5['order_item_table']} ori ON prd.ori_idx = ori.ori_idx
            LEFT JOIN {$g5['production_item_table']} pri ON prd.bom_idx = pri.bom_idx AND prd.prd_idx = pri.prd_idx
            LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
            LEFT JOIN {$g5['customer_table']} cst ON cst.cst_idx = bom.cst_idx_provider
        WHERE prd.prd_idx = '{$prd_idx}'
";
$row = sql_fetch($sql,1);

// print_r2($row);exit;
$str_arr = array();
if($w == 'u') {
    //완제품 정보는 미리 셋팅해 둔다
    array_push($str_arr,array(
        'bom_idx'=>$row['bom_idx']
        , 'cst_name'=>$row['cst_name']
        , 'bom_name'=>$row['bom_name']
        , 'bom_part_no'=>$row['bom_part_no']
        , 'bit_count'=>'1'
        , 'bit_reply'=>'0'
        , 'pri_idx'=>$row['pri_idx']
        , 'mms_idx'=>$row['mms_idx']
        , 'trm_idx_operation'=>$row['trm_idx_operation']
        , 'trm_idx_line'=>$row['trm_idx_line']
        , 'pri_value'=>$row['pri_value']
        , 'pri_memo'=>$row['pri_memo']
        , 'pri_status'=>$row['pri_status']
        , 'pri_reg_dt'=>$row['pri_reg_dt']
        , 'pri_update_dt'=>$row['pri_update_dt']
    ));
    
    //서브 제품군을 셋팅한다.
    $sql1 = " SELECT bom.bom_idx
                    , cst.cst_name
                    , bom.bom_name
                    , bom_part_no
                    , boi.bit_count
                    , boi.bit_reply
                    , pri.pri_idx
                    , pri.mms_idx
                    , pri.trm_idx_operation
                    , pri.trm_idx_line
                    , pri.pri_value
                    , pri.pri_memo
                    , pri.pri_status
                    , pri.pri_reg_dt
                    , pri.pri_update_dt
                FROM {$g5['bom_item_table']} boi
                    LEFT JOIN {$g5['bom_table']} bom ON boi.bom_idx_child = bom.bom_idx
                    LEFT JOIN {$g5['customer_table']} cst ON cst.cst_idx = bom.cst_idx_provider
                    LEFT JOIN {$g5['production_item_table']} pri ON bom.bom_idx = pri.bom_idx
                WHERE boi.bom_idx = '{$row['bom_idx']}'
                    AND pri.prd_idx = '{$row['prd_idx']}'
                    AND pri.pri_status != 'trash'
                ORDER BY boi.bit_reply
    ";
    // print_r3($sql1);
    $res = sql_query($sql1,1);
    if($res->num_rows){
        for($i=0;$srow=sql_fetch_array($res);$i++){
            // if($srow['bit_reply'] == '') $srow['bit_reply'] = '1';
            array_push($str_arr,$srow);
        }
    }
}
// print_r2($str_arr);exit;

$html_title = ($w=='')?'추가':'수정';
$html_title = ($w=='c')?'복제':$html_title;
$g5['title'] = '생산계획 '.$html_title;
// $g5['title'] = '(제품별)출하생산계획 '.$html_title;
$g5['title'] .= ($w != '') ? ' - '.$row['bom_name'].'['.$row['bom_part_no'].']' : '';

$qstr .= ($calendar)?'&start_date='.$first_date.'&end_date='.$last_date:'';

$readonly = ' readonly';
$required = ' required';

include_once('./_head.php');
?>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" >
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="prd_idx" value="<?php echo $row["prd_idx"] ?>">
<input type="hidden" name="sms_pri_idx" value="">
<input type="hidden" name="sms_pri_mmw" value="">
<input type="hidden" name="sms_pri_memo" value="">
<?php if($calendar){ ?>
<input type="hidden" name="calendar" value="1">
<input type="hidden" name="start_date" value="<?=$start_date?>">
<input type="hidden" name="end_date" value="<?=$end_date?>">
<?php } ?>

<div class="local_desc01 local_desc" style="display:none;">
    <p>각종 고유번호(설비번호, IMP번호..)들은 내부적으로 다른 데이타베이스 연동을 통해서 정보를 가지고 오게 됩니다.</p>
	<p class="txt_redblink" style="display:no ne;">설비idx=0 인 경우는 전체설비(설비 비선택 추가해라!!!)<br>설비idx 가 있으면 특정설비의 작업구간</p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
	<colgroup>
		<col class="grid_4" style="width:10%;">
		<col style="width:40%;">
		<col class="grid_4" style="width:13%;">
		<col style="width:37%;">
	</colgroup>
	<tbody>
        <tr>
            <th scope="row">품명/품번</th>
            <td>
                <input type="hidden" name="bom_idx" id="bom_idx" value="<?=${$pre}['bom_idx']?>">
                <input type="text" name="bom_name" value="<?=$bom['bom_name']?>" class="frm_input required readonly" required readonly>
                <span class="span_bom_part_no font_size_8"><?=$bom['bom_part_no']?></span>
                <?php if($w == ''){ ?>
                    <a href="./bom_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02" id="btn_bom">찾기</a>
                    <button type="button" class="btn btn_04 bom_cancel">취소</button>
                <?php } ?>
            </td>
            <th scope="row">수주(수주일/ID)</th>
            <td>
                <input type="text" name="ori_date" id="ori_date" placeholder="수주날짜" value="<?=$row['ori_date']?>"<?=$readonly?> class="frm_input<?=$readonly?>" style="width:90px;"> /
                <input type="text" name="ori_idx" id="ori_idx" placeholder="수주ID" value="<?=$row['ori_idx']?>"<?=$readonly?> class="frm_input<?=$readonly?>" style="width:80px;">
                <a href="<?=G5_USER_ADMIN_WIN_URL?>/order_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02" id="btn_ori">찾기</a>
                <button type="button" class="btn btn_04 ori_cancel">취소</button>
            </td>
        </tr>
        <tr>
            <th scope="row">지시번호</th>
            <td>
                <?php
                if($w == '' || $w == 'c'){
                    $tdcode = preg_replace('/[ :-]*/','',G5_TIME_YMDHIS);
                    $prd_order_no = "PRD-".strtoupper(wdg_uniqid());
                    echo '<input type="text" name="prd_order_no" value="'.$prd_order_no.'"'.$readonly.' class="frm_input'.$readonly.'" style="width:200px;">';
                }
                else { 
                    echo $row['prd_order_no'];
                }
                ?>
                
            </td>
            <th scope="row">지시량</th>
            <td>
                <input type="text" name="prd_count" value="<?=number_format($row['pri_value'])?>" id="prd_count" onclick="javascript:<?php if($w == ''){ ?>numtoprice(this)<?php } else { ?>numtoprice_prd(this)<?php } ?>" class="frm_input" style="text-align:right;width:90px;">&nbsp;&nbsp;
                <span>완제품기준의 지시량</span>
            </td>
        </tr>
        <tr>
            <th scope="row">생산시작일</th>
            <td>
                <input type="text" name="prd_start_date" id="prd_start_date" value="<?=(($row['prd_start_date'] && $row['prd_start_date'] != '0000-00-00')?$row['prd_start_date']:'')?>" readonly class="readonly tbl_input" style="width:90px;background:#333 !important;text-align:center;">
            </td>
            <th scope="row">상태</th>
            <td>
                <select name="prd_status" id="prd_status"<?php if($w != ''){ ?> onchange="javascript:status_change(this);"<?php } ?>>
                    <?=$g5['set_prd_status_options']?>
                </select>
                <script>
                $('#prd_status').val('<?=(($w=='')?'confirm':$row['prd_status'])?>');   
                </script>
            </td>
        </tr>
        <tr>
            <th scope="row">메모</th>
            <td colspan="3">
                <input type="text" name="prd_memo" id="prd_memo" class="frm_input" value="<?=$row['prd_memo']?>" style="width:100%;">
            </td>
        </tr>
	</tbody>
	</table>
    <!--########### BOM 구조목록 : 시작 ###########-->
    <?php 
    if($w == 'u' && count($str_arr)){ 
    ?>
    <script>
    var mmw_arr = <?=json_encode($g5['mmw_arr'],JSON_PRETTY_PRINT)?>;
    // console.log(mms_arr);
    </script>
    <div class="tbl_head01" style="padding-top:20px;">
        <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th class="th_bom_name">품번/품명</th>
            <th class="th_mms_idx">설비</th>
            <th class="th_mms_idx">작업자</th>
            <th class="th_pri_value">지시량</th>
            <th class="th_pri_memo" style="width:100px;">메모</th>
            <th class="th_pri_status">상태</th>
            <th class="th_one_sms">문자발송</th>
        </tr>
        <tr>
        </tr>
        </thead>
        <tbody>
        <?php for($i=0;$i<count($str_arr);$i++){ 
            if($str_arr[$i]['bit_reply'] == '0'){
                $len = 0;
            }else{
                $len = strlen($str_arr[$i]['bit_reply']) / 2;
            }
            $padding_left = ($len)?$len*20:10;
            $tg_len = '<span class="btn_number">'.($len+1).'</span>';
        ?>
        <tr>
            <td class="td_bom_name" style="padding-left:<?=$padding_left?>px;">
                <?=$tg_len?>
                <?php if($row['bom_part_no'] === $str_arr[$i]['bom_part_no']){ ?>
                <input type="hidden" name="pri_idx" value="<?=$str_arr[$i]['pri_idx']?>">
                <?php } ?>
                <span class="font_size_7"><?=$str_arr[$i]['bom_part_no']?></span>
                <?=$str_arr[$i]['bom_name']?>
                <span class="span_cst_name font_size_8"><?=$str_arr[$i]['cst_name']?></span>
                <span class="span_bit_count font_size_8"><?=$str_arr[$i]['bit_count']?>개</span>
            </td>
            <td class="td_mms_idx" mms_idx="<?=$str_arr[$i]['mms_idx']?>">
                <select name="mms_idxs[<?=$str_arr[$i]['pri_idx']?>]" id="mms_idx_<?=$i?>" sync="mmw_idx_<?=$i?>" class="mms_idx">
                <?=$g5['mms_options']?>
                </select>
                <script>
                <?php if($w != ''){ ?>
                    $('#mms_idx_<?=$i?>').val('<?=$str_arr[$i]['mms_idx']?>');
                <?php } ?>
                </script>
            </td>
            <td class="td_mmw_idx">
                <select name="mmw_idxs[<?=$str_arr[$i]['pri_idx']?>]" id="mmw_idx_<?=$i?>" class="mmw_idx">
                <?php foreach($g5['mmw_arr'][$str_arr[$i]['mms_idx']] as $wk=>$wv){ ?>
                <option value="<?=$wk?>"><?=$wv?></option>
                <?php } ?>  
                </select>
            </td>
            <td class="td_pri_value" pri_value="<?=$str_arr[$i]['pri_value']?>">
                <input type="text" name="pri_values[<?=$str_arr[$i]['pri_idx']?>]" class="frm_input pri_value" value="<?=number_format($str_arr[$i]['pri_value'])?>" onclick="javascript:numtoprice(this)" bit_count="<?=$str_arr[$i]['bit_count']?>">
            </td>
            <td class="td_pri_memo">
                <input type="text" name="pri_memos[<?=$str_arr[$i]['pri_idx']?>]" class="frm_input pri_memo" id="pri_memo_<?=$i?>" value="<?=$str_arr[$i]['pri_memo']?>">
            </td>
            <td class="td_pri_status">
                <select name="pri_statuss[<?=$str_arr[$i]['pri_idx']?>]" id="pri_status_<?=$i?>" class="pri_status<?php if($row['prd_status']!='confirm'){ ?> deactivation<?php } ?>"<?php if($row['prd_status']!='confirm'){ ?> onFocus='this.initialSelect=this.selectedIndex;'
                    onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
                <?=$g5['set_prd_status_value_options']?>
                </select>
                <script>
                $('#pri_status_<?=$i?>').val('<?=(($w=='')?'confirm':$str_arr[$i]['pri_status'])?>');   
                </script>
            </td>
            <td class="td_one_sms">
                <input type="submit" name="act_button" value="개별문자" sync="mmw_idx_<?=$i?>" syncm="pri_memo_<?=$i?>" syns="pri_status_<?=$i?>" onclick="document.pressed=this.value;document.sms_pri_idx=<?=$str_arr[$i]['pri_idx']?>;document.sync=this.getAttribute('sync');document.syncm=this.getAttribute('syncm');document.syns=this.getAttribute('syns');" class="btn btn_05" accesskey='s'>
            </td>
        </tr>
        <?php } ?>
        </tbody>  
        </table>
    </div><!--//tbl_head01 tbl_wrap-->
    <?php }
    else { ?>
    <div class="empty_list">기본 정보를 등록한 후 설정할 수 있습니다.</div>
    <?php } ?>
    <!--########### BOM 구조목록 : 종료 ###########-->
</div>

<script>
$('.mms_idx').on('change',function(){
    var mms_idx = $(this).val();
    var mmw_obj = mmw_arr[mms_idx];
    var sync_obj = $('#'+$(this).attr('sync'));
    sync_obj.empty();
    for(idx in mmw_obj){
        $('<option value="'+idx+'">'+mmw_obj[idx]+'</option>').appendTo(sync_obj);
    }
});
</script>
<div class="btn_fixed_top">
    <?php
    $production_url = ($calendar) ? './order_out_practice_calendar_list.php?'.$qstr:'./production_list.php?'.$qstr;
    ?>
    <?php if($w == 'u'){ ?>
    <input type="submit" name="act_button" value="문자발송" onclick="document.pressed=this.value" class="btn btn_05" accesskey='s'>
    <?php } ?>
    <a href="<?=$production_url?>" class="btn btn_02">목록</a>
    <input type="submit" name="act_button" value="확인" onclick="document.pressed=this.value" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function(){
    //생산일선택을 하면 [생산계획ID] 선택을 해제해야 한다.
    $("#prd_start_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", closeText:'취소', onClose:function(){if($(window.event.srcElement).hasClass('ui-datepicker-close')){$(this).val('0000-00-00');}}});

    // 품명 버튼 클릭
	$("#btn_bom").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winBOM = window.open(href, "winBOM", "left=300,top=150,width=550,height=600,scrollbars=1");
        winBOM.focus();
	});

    // // 생산제품(상품)선택 버튼 클릭
    // $('#btn_bom').click(function(e){
    //     e.preventDefault();
    //     var href = $(this).attr('href');
    //     var winBomSelect = window.open(href, "winBomSelect", "left=300,top=150,width=650,height=700,scrollbars=1");
    //     winBomSelect.focus();
    //     return false;
    // });

    // 수주ID찾기 버튼 클릭
	$("#btn_ori").click(function(e) {
		e.preventDefault();
        if(!$('#bom_idx').val()){
            alert('상품을 먼저 선택해 주세요.');
            $('#ori_idx').val('');
            $('#ori_date').val('');
            $('#prd_start_date').val('');
            $('#prd_count').val('');
            return false;
        }
        var href = $(this).attr('href')+'&bom_idx='+$('#bom_idx').val();
		var winOrderSelect = window.open(href, "winOrderSelect", "left=300,top=150,width=650,height=700,scrollbars=1");
        winOrderSelect.focus();
        return false;
	});

    //제품ID 취소는 수주ID도 취소 & 출하ID도 취소한다.
    $('.bom_cancel').on('click',function(){
        $('#bom_idx').val('');
        $('input[name=bom_name]').val('');
        $('.span_bom_part_no').text('');

        $('#ori_idx').val('');
        $('#ori_date').val('');
        $('#prd_start_date').val('');
        $('#prd_count').val('');
    });
    //수주ID 취소는 출하ID도 취소한다.
    $('.ori_cancel').on('click',function(){
        $('#ori_idx').val('');
        $('#ori_date').val('');
        $('#prd_start_date').val('');
        $('#prd_count').val('');
    });

});

<?php if($w != ''){ ?>
function numtoprice_prd(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-|,]/g,""));
        if(!isNaN($(this).val().replace(/,/g,''))){
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
        }
        if($(this).val() == '0') {
            $(this).val('');
        }

        var itm_val = $(this).val().replace(/,/g,'');
        $('.pri_value').each(function(){
            var cnt = Number($(this).attr('bit_count'));
            var this_val = itm_val * cnt;
            $(this).val( thousand_comma( this_val ) );
            if($(this).val() == '0') {
                $(this).val('');
            }
        });
    });
}

function status_change(obj){
    // alert(obj.value);
    $('.pri_status').each(function(){
        $(this).val(obj.value);
        if(obj.value != 'confirm'){
            $(this).attr({'onFocus':'this.initialSelect=this.selectedIndex;','onChange':'selectedIndex=this.initialSelect;'}).addClass('deactivation');
        }
        else{
            $(this).removeAttr('onFocus').removeAttr('onChange').removeClass('deactivation');
        }
    });
}
<?php } ?>

function form01_submit(f){
    if(document.pressed == "개별문자"){
        var mmw_select = document.getElementById(document.sync);
        var pri_status = document.getElementById(document.syns).value;
        var memo = document.getElementById(document.syncm).value;
        var mmw_id = mmw_select.options[mmw_select.selectedIndex].value;
        var mmw_name = mmw_select.options[mmw_select.selectedIndex].textContent;
        memo = $.trim(memo);
        if(pri_status != 'confirm'){
            alert('상태값이 [확정]일때만 문자를 전송할 수 있습니다.');
            return false;
        }
        
        if(memo == ''){
            alert('전송할 내용을 메모란에 기입해 주세요.');
            return false;
        }
        
        if(!confirm("해당 제품의 메모내용으로 작업자인 "+mmw_name+"에게 문자를 보내시겠습니까?"));
            return false;
        // alert(document.sms_pri_idx);return false;
        f.sms_pri_idx.value = document.sms_pri_idx;
        f.sms_pri_mmw.value = mmw_id;
        f.sms_pri_memo.value = memo;
        f.w.value = 'o';
    }
    else if(document.pressed == "문자발송"){
        if($('#prd_status').val() != 'confirm'){
            alert('상태값이 [확정]일때만 문자전송이 가능합니다.');
            $('#prd_status').focus();
            return false;
        }
        if(!confirm("아래와 같은 내용으로 해당 BOM구조의 담당자들에게\n일괄적으로 문자를 보내시겠습니까?"))
            return false;

        f.w.value = 's';
    }
    
    //생산할 상품을 선택하세요
    if(!f.bom_idx.value){
        alert('생산할 상품을 선택해 주세요.');
        f.bom_name.focus();
        return false;
    }

    //생산시작일을 설정해 주세요
    if(f.prd_start_date.value == '' || f.prd_start_date.value == '0000-00-00'){
        alert('생산시작일을 선택해 주세요.');
        f.prd_start_date.focus();
        return false;
    }
    
    //지시수량을 설정하세요.
    if(!f.prd_count.value){
        alert('지시수량을 설정해 주세요.');
        f.prd_count.focus();
        return false;
    }
    //상태값을 설정해 주세요
    if(!f.prd_status.value){
        alert('상태값을 선택해 주세요.');
        f.prd_status.focus();
        return false;
    }

    $('.mms_idx').each(function(){
        if(!$(this).val()){
            alert('설비를 선택해 주세요.');
            $(this).focus();
            return false;
        }
    });

    $('.pri_value').each(function(){
        if(!$(this).val()){
            alert('지시량을 입력해 주세요.');
            $(this).focus();
            return false;
        }
    });

    $('.pri_status').each(function(){
        if(!$(this).val()){
            alert('상태값을 선택해 주세요.');
            $(this).focus();
            return false;
        }
    });

    return true;
}
</script>

<?php
include_once ('./_tail.php');