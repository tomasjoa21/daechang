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


    if($mtyp == 'mto'){
        $sql_it = " SELECT *
                    FROM {$g5['material_order_item_table']} AS moi
                        LEFT JOIN {$g5['bom_table']} AS bom ON moi.bom_idx = bom.bom_idx 
                    WHERE mto_idx = '{$mto_idx}' AND moi_status NOT IN('trash','delete','del','cancel') ORDER BY moi_idx ";
        $res = sql_query($sql_it,1);
        $total_count = sql_num_rows($res);
    }
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

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_JS_URL.'/nestable/jquery.nestable.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_CSS_URL.'/nestable.css">', 0);
?>
<style>
#mto_idx{width:100px;text-align:center;}
#mto_input_date{width:90px;}

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
        <td id="mto_price"><?=number_format($row['mto_price'])?></td>
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

<?php if($mtyp == 'mto'){ ?>
    <div class="local_desc01 local_desc" style="display:no ne;">
    <p>오른편에서 품명을 검색하고 입력한 다음 발주상품목록을 구성하세요.</p>
    <p>구성이 끝났으면 상단 [확인] 버튼을 클릭하여 저장하세요.</p>
</div>
<div class="tbl_frm01">
    <div class="div_wrapper div_left">
        <div class="div_title">
            <?php if($row['com_name']){ ?>
            <span class="ord_title"><?=$row['com_name']?></span>의 수주 설정
            <?php }else{ ?>
            수주설정
            <?php } ?>
            <a href="javascript:" id="del-item" class="btn_03 btn float_right"> 초기화</a>
        </div>

        <div class="dd" id="nestable3">
        <ol class="dd-list">
        <?php
        $depth = 0;
        for ($i=0; $row=sql_fetch_array($res); $i++) {
            $row['idx'] = $i+1;
            // $row['com_provider'] = get_table('company','com_idx',$row['cst_idx_provider']);
            $row['bit_count'] = $row['bit_count'] ?: 1;
            $bno = sql_fetch(" SELECT bom_part_no,bom_name FROM {$g5['bom_table']} WHERE bom_idx = '{$row['bom_idx']}' ");
            $row['bom_part_no'] = $bno['bom_part_no'];
            $row['bom_name'] = $bno['bom_name'];
			
			$otq_sql = " SELECT SUM(oro_count) AS ous FROM {$g5['order_out_table']} WHERE ord_idx = '{$row['ord_idx']}' AND ori_idx = '{$row['ori_idx']}' AND oro_status NOT IN('trash','delete','del','cancel') ";
			//echo $otq_sql;
            $otq = sql_fetch($otq_sql);
			$out_cnt = ($otq['ous']) ? $otq['ous'] : 0;
			//echo $out_cnt;
			$cnt_blick = '';//($out_cnt != $row['ori_count']) ? ' txt_redblink' : '';
            echo '
            <li class="dd-item dd3-item" data-id="'.$row['idx'].'">
                <div class="dd-handle dd3-handle">Drag</div>
                <div class="dd3-content" bom_idx_child="'.$row['bom_idx'].'">
                    <span class="bom_name">'.cut_str($row['bom_name'],20).'('.$row['moi_idx'].')</span>
                    <span class="bom_prvd">'.$g5['provider_key_val'][$row['cst_idx_provider']].'</span>
                    <div class="add_items">
                    <span class="bom_part_no">'.$row['bom_part_no'].'</span>
                        <span class="bom_price" price="'.$row['ori_price'].'"><b>'.number_format($row['moi_price']).'</b>원</span>
                        <span class="span_count"><span class="bit_count'.$cnt_blick.'">'.$row['moi_count'].'</span>&nbsp;&nbsp;개</span>
                        <img src="https://icongr.am/clarity/times.svg?size=30&color=bbbbbb" class="btn_remove" title="삭제">
                    </div>
                </div>
            </li>'.PHP_EOL;
        }
        if( $i == 0 ) {
            echo '<li class="empty_table">구성품이 없습니다.</li>';
        }
        ?>
        </ol>
        </div>
        
        <div style="clear:both;"></div>
        <div class="btn_control">
            <!-- ========================================= -->
            <div class="div_serialize" style="display:none;">
            <p><strong>Serialised Output (per list)</strong></p>
            <textarea class="navi_result" id="nestable3-output" name="serialized"></textarea>
            </div>
            <!-- ========================================= -->
        </div>
    </div>
    <div class="div_wrapper div_right float_right">
        <div class="div_title">
            <span class="bom_title2">제품 리스트</span>
        </div>
        <div class="div_bom_list">
            <iframe id="frame_bom_list" src="./material_order_item_list.php?file_name=<?=$g5['file_name']?>" frameborder="0" scrolling="no"></iframe>
        </div>

    </div>
</div>
<?php } ?>

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


//###################################### 상품목록 #########################
if(mtyp == 'mto'){ 
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
    function add_item(bom_idx, bom_name, bom_part_no, bom_prvd, com_name, bom_price, bom_price2) {
        if($('#nestable3 li .dd3-content .add_items .bom_part_no:contains('+bom_part_no+')').length > 0){
            alert('같은 상품을 올릴수 없습니다. 올라간 상품의 개수(개)로 조정하세요.');
            return;
        }

        var li_dom ='<div class="dd3-content" bom_idx_child="'+bom_idx+'">'
                    +'  <span class="bom_name">'+bom_name+'</span>'
                    +'  <span class="bom_prvd">'+bom_prvd+'</span>'
                    +'  <div class="add_items">'
                    +'      <span class="bom_part_no">'+bom_part_no+'</span>'
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
        $('#mto_price').val(thousand_comma(totalprice));
        //console.log(thousand_comma(totalprice));
    }   
    
    // 숫자만 입력
    function chk_Number(object){
        $(object).keyup(function(){
            $(this).val($(this).val().replace(/[^0-9|-]/g,""));
        });
    }
} // if(mtyp == 'mto')
//################### //상품목록 종료 ##########################



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