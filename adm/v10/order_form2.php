<?php
$sub_menu = "918110";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'order_item';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr1 = '&ser_cst_idx='.$ser_cst_idx.'&st_date='.$st_date.'&en_date='.$en_date; // 추가로 확장해서 넘겨야 할 변수들
$qstr .= $qstr1; // $qstr 확장


$total_count = 0;
if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
	${$pre}['ori_status'] = 'ok';
    
}
else if ($w == 'u') {
    $sql = " SELECT * FROM {$g5['order_table']} WHERE ori_idx = '{$ori_idx}' ";
    $ord = sql_fetch($sql);
    $csql = sql_fetch(" SELECT cst_name FROM {$g5['company_table']} WHERE com_idx = '{${$pre}['cst_idx']}' ");
    ${$pre}['cst_name_customer'] = $csql['cst_name'];

    $sql_it = " SELECT *
                        ,( SELECT bct_desc FROM {$g5['bom_category_table']} WHERE bct_id = LEFT(bom.bct_id,2) ) AS cat1
                        ,( SELECT bct_desc FROM {$g5['bom_category_table']} WHERE bct_id = LEFT(bom.bct_id,4) ) AS cat2
                FROM {$g5['order_item_table']} AS ori
                    LEFT JOIN {$g5['bom_table']} AS bom ON ori.bom_idx = bom.bom_idx 
                    LEFT JOIN {$g5['bom_category_table']} AS bct ON bom.bct_id = bct.bct_id 
                WHERE ori_idx = '{$ori_idx}' AND ori_status NOT IN('trash','delete','del','cancel') ORDER BY ori_idx ";
    $result = sql_query($sql_it,1);
    $total_count = sql_num_rows($result);
}


$html_title = ($w=='')?'추가':'수정'; 
$g5['title'] = '수주 '.$html_title;
include_once ('./_head.php');

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/nestable/jquery.nestable.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/nestable.css">', 0);
//print_r3($ord);
?>
<style>
.tbl_frm01:after {display:block;visibility:hidden;clear:both;content:'';}
.div_wrapper {display:inline-block;background:#1e2531;width:49.5%;}
.dd {min-width: 100%;}
.div_title {background:#000204;padding:15px;}
.div_title .bom_title {color:#00ffe7;font-weight:bold;}
.bom_detail:before {content:"(";margin-left:10px;}
.bom_detail:after {content:")";}
#del-item {margin-top:-6px;}
#nestable3 {padding:10px 20px;min-height:616px;}
.div_bom_list {min-height:600px;padding:10px 20px;}
#frame_bom_list {width:100%;min-height:600px;}
.empty_table {background:#1e2531;}
#sp_notice,#sp_ex_notice{color:yellow;margin-left:10px;}
#sp_notice.sp_error,#sp_ex_notice.sp_error{color:red;}
</style>

<form name="form01" id="form01" action="./<?=$fname?>_form_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="com_idx" value="<?=$_SESSION['ss_com_idx']?>">
<input type="hidden" name="ori_idx" value="<?=$ori_idx?>">
<input type="hidden" name="sca" value="<?php echo $sca ?>">
<input type="hidden" name="ser_cst_idx" value="<?=$ser_cst_idx?>">
<input type="hidden" name="st_date" value="<?=$st_date?>">
<input type="hidden" name="en_date" value="<?=$en_date?>">

<div class="local_desc01 local_desc" style="display:none;">
    <p>형식에 맞는 엑셀 파일로 수주데이터를 일괄 등록 할 수 있습니다.</p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4" style="width:15%;">
		<col style="width:35%;">
		<col class="grid_4" style="width:15%;">
		<col style="width:35%;">
    </colgroup>
	<tbody>
	<tr>
        <th scope="row">고객사</th>
		<td>
            <input type="hidden" name="cst_idx" value="<?=${$pre}['cst_idx']?>">
			<input type="text" name="cst_name" value="<?=${$pre}['cst_name']?>" class="frm_input required readonly" required readonly>
            <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=customer" class="btn btn_02" id="btn_customer">찾기</a>
        </td>
        <th scope="row">수주금액</th>
		<td>
			<input type="text" name="ori_price" id="ori_price" value="<?=number_format(${$pre}['ori_price'])?>" readonly class="frm_input readonly" style="width:130px;text-align:right;">&nbsp;원
		</td>
    </tr>
    <tr>
        <th scope="row"><label for="ori_date">수주일</label></th>
        <td>
            <input type="text" name="ori_date" id="ori_date" value="<?=${$pre}['ori_date']?>" readonly required class="date frm_input readonly required" style="width:130px;">
            <span id="sp_notice" class="<?=((${$pre}['ori_date'])?'':'sp_error')?>"><?=((${$pre}['ori_date'])?'':'수주일을 입력해 주세요.')?></span>
        </td>
        <th scope="row">수주상태</th>
        <td>
            <select name="ori_status" id="ori_status">
                <?=$g5['set_ori_status_options']?>
            </select>
            <script>$('select[name="ori_status"]').val('<?=${$pre}['ori_status']?>');</script>
        </td>
    </tr>
	</tbody>
	</table>
</div>
<div class="btn_fixed_top">
    <a href="./order_list.php?<?php echo $qstr ?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script src="<?=G5_USER_ADMIN_JS_URL?>/nestable/jquery.nestable.js"></script>
<script>
$(function() {
    <?php if($w == ''){ ?>
    $("input[name=ori_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99",
    onSelect: function(date,datepicker){
        chk_Code(date);
    }
    });
    <?php } ?>
    // 가격 입력 쉼표 처리
	$(document).on( 'keyup','input[name$=_price]',function(e) {
        if(!isNaN($(this).val().replace(/,/g,'')))
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
	});
    // 가격정보 보임 숨김
	$(".btn_price_add").click(function(e) {
        if( $('.tr_price').is(':hidden') ) {
            $('.tr_price').show();
        }
        else
           $('.tr_price').hide();
	});

    // 거래처찾기 버튼 클릭
	$("#btn_customer").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winCustomerSelect = window.open(href, "winCustomerSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winCustomerSelect.focus();
	});

    // 가격 입력 쉼표 처리
	$(document).on( 'keyup','input[name$=_price]',function(e) {
//        console.log( $(this).val() )
//		console.log( $(this).val().replace(/,/g,'') );
        if(!isNaN($(this).val().replace(/,/g,'')))
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
	});

});

//###################################### 상품목록 #########################
var listNodeName = 'ol';
var itemNodeName = 'li';
var listClass = 'dd-list';
var contentClass = 'dd3-content';
var includeContent = false;
var naviLastId = <?=$total_count?>;    // 항목수

$(document).ready(function() {
    // activate Nestable for navi
    $('#nestable3').nestable({
        group: 10,
        contentCallback: function(item) {
            var content = item.content || '' ? item.content : item.id;
            content += '';

            return content;
        },
        maxDepth: 0,
        itemClass:'dd-item dd3-item',
        handleClass:'dd-handle dd3-handle',
        contentNodeName: 'div',
        contentClass: 'dd3-content',
        callback: function(l, e, p) {
            printOutput();
        },
        itemRenderer: function(item_attrs, content, children, options, item) {
            var item_attrs_string = $.map(item_attrs, function(value, key) {
                return ' ' + key + '="' + value + '"';
            }).join(' ');

            var html = '<' + options.itemNodeName + item_attrs_string + '>';
            html += '<' + options.handleNodeName + ' class="' + options.handleClass + '">';
            html += '</' + options.handleNodeName + '>';
            html += content;
            html += '</' + options.contentNodeName + '>';
            html += children;
            html += '</' + options.itemNodeName + '>';

            return html;
        }
    });

    // 초기값 입력
    printOutput();

});


// 변경 내용 출력
var printOutput = function() {
    // output initial serialised data
    var result_array = list_update( $("#nestable3").find(listNodeName).first() );
    resultOutput(result_array,'nestable3-output')
};
// 변경 내용 출력 함수
var resultOutput = function(arr, obj) {
    result_text = ((window.JSON)) ? window.JSON.stringify(arr) : 'JSON browser support required for this demo.';
    $('#'+obj).val( result_text );
};

// Serialized output 내용 업데이트 함수
function list_update(obj) {
    var array = [],
        items = obj.children(itemNodeName);
        
    items.each(function() {
        var li = $(this),
            item = $.extend({}, li.data()),
            sub = li.children(listNodeName);
            
        // depth 속성 추가
        var li_depth = li.parents('.dd-list').length - 1;
        item.depth = li_depth;
        item.bom_name = li.find('.'+contentClass).first().find('span.bom_name').text();
        item.bom_idx_child = li.find('.'+contentClass).first().attr('bom_idx_child');
        item.bit_count = li.find('.'+contentClass).first().find('span.bit_count').text();
        item.ori_price = li.find('.'+contentClass).first().find('span.bom_price').attr('price');
        // item.bit_2 = li.find('.'+contentClass).first().attr('bit_2');
    
        if (includeContent) {
            var content = li.find('.' + contentClass).html();
    
            if (content) {
                item.content = content;
            }
        }
    
        if (sub.length) {
            item.children = list_update(sub);
        }
        array.push(item);
    });
    return array;
}


// 내용 수정
$(document).on('click','.dd3-content',function(e){
    e.stopPropagation();
    // 안에 input 박스가 존재하면 input 벗겨내고
    if( $(this).find('input').length ) {
        //console.log('있다.');
        var this_value = $(this).find('input').val();
        $(this).find('span').html( this_value );
        
        printOutput();
    }
    // 아니면 input 박스 추가해서 내용 변경할 수 있도록 한다.
    else {
        //console.log('없다.');
        var this_value = $(this).find('span.bit_count').text();
        $(this).find('span.bit_count').html('<input type="" name="" value="'+this_value+'" class="dd3-content-input" style="width:50px;">');
        $(this).find('span input').select().focus();
    }
    
});

// 내용수정 input 클릭하면 div 에 영향을 주지 않게 stopPropagation
$(document).on('click','.dd3-content input',function(e){
    e.stopPropagation();
});

// 내용수정 input 키보드를 누르면 1이상의 숫자만 입력하도록
$(document).on('keyup','.dd3-content input',function(e){
    e.stopPropagation();
    var ask = e.keyCode;
    if((ask < 48 || ask > 57) && (ask < 96 || ask > 105)){ //숫자,백스페이,좌우방향이 아닌 키를 입력했다면 무조건 1 입력
        if(ask != 8 && ask != 37 && ask != 39) $(this).val('1');
    }
});

// input박스 Blur or keyup 되면 현재값 입력
$(document).on('blur','.dd3-content input',function(e){
    e.stopPropagation();
    if($(this).val() == '' || $(this).val() == null || $(this).val() == undefined || $(this).val() == '0') $(this).val('1');
    var this_value = $(this).val();
    $(this).closest('span').html( this_value );
    totalCalculatePrice();
    printOutput();
});

// 항목 삭제 클릭
$(document).on('click','.dd3-content .add_items img',function(e){
    e.preventDefault();
    e.stopPropagation();
    var this_id = $(this).closest('.dd-item').attr('data-id');
//    var this_bit_idx = $(this).closest('.dd-item').attr('data-bit_idx');
    var this_subject = $(this).closest('.dd3-content').find('span:first').text();

    if( $(this).hasClass('btn_remove') ) {
        // if(confirm('해당 항목을 삭제하시겠습니까?\n수정하신 후 [확인] 버튼을 클릭해 주셔야 최종 적용됩니다.')) {
            $('#nestable3').nestable('remove', this_id);
        // }
        totalCalculatePrice();
        if($('#nestable3 .dd-list').children().length == 0){
            $('#nestable3 .dd-list').html('<li class="empty_table">구성품이 없습니다.</li>');
        }
        printOutput();
    }
    
});


// 초기화 클릭
$(document).on('click','#del-item',function(e){
    e.preventDefault();
    if(confirm('전체 항목을 삭제하시겠습니까?\n수정하신 후 [확인] 버튼을 클릭해 주셔야 최종 적용됩니다.')) {
        $('.dd-item').each(function(i,v){
            $('#nestable3').nestable('remove', $(this).attr('data-id'));
        });
        $('#nestable3 .dd-list').html('<li class="empty_table">구성품이 없습니다.</li>');
        naviLastId = 0; // id 초기화
        totalCalculatePrice();
        printOutput();
    }    
});

// 항목추가 함수
function add_item(bom_idx, bom_name, bom_part_no, bom_std, cst_name, bom_price, bom_price2) {
    if($('#nestable3 li .dd3-content .add_items .bom_part_no:contains('+bom_part_no+')').length > 0){
        alert('같은 상품을 올릴수 없습니다. 올라간 상품의 개수(개)로 조정하세요.');
        return;
    }

    var li_dom ='<div class="dd3-content" bom_idx_child="'+bom_idx+'">'
                +'  <span class="bom_name">'+bom_name+'</span>'
                +'  <div class="add_items">'
                +'      <span class="bom_part_no">'+bom_part_no+'</span>'
                +'      <span class="bom_std">'+bom_std+'</span>'
                +'      <span class="bom_price" price="'+bom_price2+'">'+bom_price+'원</span>'
                +'      <span class="span_count"><span class="bit_count">1</span>&nbsp;&nbsp;개</span>'
                +'      <img src="https://icongr.am/clarity/times.svg?size=30&color=bbbbbb" class="btn_remove" title="삭제">'
                +'  </div>'
                +'</div>';

    var newItem = {
        "id": ++naviLastId,
        "content": li_dom
    };
    $('#nestable3').nestable('add', newItem);
    totalCalculatePrice();
    printOutput();

    // 항목이 한개 이상이면 empty_table 제거
    if( $('.dd-item').length > 0 ) {
        $('.empty_table').remove();
    }
}

//thousand_comma()
function totalCalculatePrice(){
    var item_list = $('#nestable3 .dd-item .dd3-content .add_items');
    var totalprice = 0;
    if(item_list.length > 0){
        item_list.each(function(){
            var soge = Number($(this).find('.bom_price').attr('price')) * Number($(this).find('.span_count').find('.bit_count').text());
            totalprice += soge;
        });
    }
    $('#ori_price').val(thousand_comma(totalprice));
    //console.log(thousand_comma(totalprice));
}
//################################### //상품목록 종료 ######################################


// 숫자만 입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}


function chk_Code(date){
    var date_chk_url = './ajax/ori_date_overlap_chk.php';
    $.ajax({
        type : 'POST',
        url : date_chk_url,
        dataType : 'text',
        data : {'ori_date' : date},
        success : function(res){
            //console.log(res);
            if(res == 'ok'){
                document.getElementById('sp_notice').textContent = '등록 가능한 날짜입니다.';
                $('#sp_notice').removeClass('sp_error');
            }
            else if(res == 'overlap'){
                document.getElementById('sp_notice').textContent = '이미 등록된 날짜입니다.';
                $('#sp_notice').removeClass('sp_error');
                $('#sp_notice').addClass('sp_error');
            }
        },
        error : function(xmlReq){
            alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
        }
    });
}


function form01_submit(f) {
    // 폼에 input 박스가 한개라도 있으면 안 된다.
    // input 처리를 하고 return false
    if( $('.dd3-content input').length ) {
        //alert('수정하시던 작업이 있습니다.\n작업을 마무리해 주세요.');
        var this_value = $('.dd3-content input').val();
        $('.dd3-content input').closest('span').html( this_value );
        printOutput();
        return false;
    }

    if($('#sp_notice').hasClass('sp_error')){
        alert('등록가능한 수주일를 입력해 주세요.');
        $('input[name="ori_date"]').focus();
        return false;
    }

    if(!$('#nestable3 .dd-list .dd-item').length){
        alert('적어도 상품 한 개 이상은 등록해야 합니다.');
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
