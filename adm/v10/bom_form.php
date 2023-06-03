<?php
$sub_menu = "940120";
include_once('./_common.php');
include_once(G5_USER_ADMIN_LIB_PATH.'/category.lib.php');
auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'bom';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr .= '&ser_bct_idx='.$ser_bct_idx.'&ser_bom_type='.$ser_bom_type; // 추가로 확장해서 넘겨야 할 변수들


if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김

    ${$pre}[$pre.'_count'] = 1;
    ${$pre}[$pre.'_moq'] = 1;
    ${$pre}[$pre.'_start_date'] = G5_TIME_YMD;
    ${$pre}[$pre.'_status'] = 'ok';
}
else if ($w == 'u') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
    // print_r3(${$pre});
    $com = get_table_meta('customer','cst_idx',$bom['cst_idx_customer']);
    $com2 = get_table_meta('customer','cst_idx',$bom['cst_idx_provider']);
    // print_r3($com2);

    // 가격 (오늘날짜 기준가격)
    ${$pre}['bom_price'] = get_bom_price(${$pre."_idx"});

    //완성품만 이미지를 등록한다.
    // if(${$pre}['bom_type'] == 'product'){
        //관련파일 추출
        $flesql = " SELECT * FROM {$g5['file_table']}
        WHERE fle_db_table = 'bom'
        AND fle_type IN ('bomf1','bomf2','bomf3','bomf4','bomf5','bomf6')
        AND fle_db_id = '".${$pre."_idx"}."' ORDER BY fle_reg_dt,fle_idx ";
        //print_r3($flesql);
        $fle_rs = sql_query($flesql,1);

        $rowb['bom_bomf1'] = array();//1번째 파일그룹
        $rowb['bom_bomf1_idxs'] = array();//(fle_idx) 목록이 담긴 배열
        $rowb['bom_bomf2'] = array();//2번째 파일그룹
        $rowb['bom_bomf2_idxs'] = array();//(fle_idx) 목록이 담긴 배열
        $rowb['bom_bomf3'] = array();//3번째 파일그룹
        $rowb['bom_bomf3_idxs'] = array();//(fle_idx) 목록이 담긴 배열
        $rowb['bom_bomf4'] = array();//4번째 파일그룹
        $rowb['bom_bomf4_idxs'] = array();//(fle_idx) 목록이 담긴 배열
        $rowb['bom_bomf5'] = array();//5번째 파일그룹
        $rowb['bom_bomf5_idxs'] = array();//(fle_idx) 목록이 담긴 배열
        $rowb['bom_bomf6'] = array();//6번째 파일그룹
        $rowb['bom_bomf6_idxs'] = array();//(fle_idx) 목록이 담긴 배열

        for($i=0;$flerow=sql_fetch_array($fle_rs);$i++){
            //print_r3($flerow);
            $file_del = (is_file(G5_PATH.$flerow['fle_path'].'/'.$flerow['fle_name'])) ? $flerow['fle_name_orig'].'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_URL.'/lib/download.php?file_fullpath='.urlencode(G5_PATH.$flerow['fle_path'].'/'.$flerow['fle_name']).'&file_name_orig='.$flerow['fle_name_orig'].'" file_path="'.$flerow['fle_path'].'">[파일다운로드]</a>&nbsp;&nbsp;'.$flerow['fle_reg_dt'].'&nbsp;&nbsp;<label for="del_'.$flerow['fle_idx'].'" style="position:relative;top:-3px;cursor:pointer;"><input type="checkbox" name="'.$flerow['fle_type'].'_del['.$flerow['fle_idx'].']" id="del_'.$flerow['fle_idx'].'" value="1"> 삭제</label><br><img src="'.G5_URL.$flerow['fle_path'].'/'.$flerow['fle_name'].'" style="width:200px;height:auto;">':''.PHP_EOL;
            @array_push($rowb['bom_'.$flerow['fle_type']],array('file'=>$file_del));
            @array_push($rowb['bom_'.$flerow['fle_type'].'_idxs'],$flerow['fle_idx']);
        }
        //print_r3($rowb['bom_bomf1']);
    // }
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');


// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_field');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'추가':'수정';
$g5['title'] = '제품(BOM) '.$html_title;
// print_r2($g5['line_reverse']['1라인']);
// exit;
include_once ('./_head.php');
?>
<script src="<?=G5_USER_ADMIN_JS_URL?>/multifile/jquery.MultiFile.min.js" type="text/javascript" language="javascript"></script>
<style>
.bop_price {font-size:0.8em;color:#a9a9a9;margin-left:10px;}
.bop_price:before {content:'(';}
.bop_price:after {content:')';margin-left:3px;}
.bom_part_nos {font-size:0.9em;color:#a9a9a9;margin-top:5px;line-height:1.1em;}
.btn_bop_delete {cursor:pointer;margin-left:5px;}
.btn_part_no_delete {cursor:pointer;margin-left:5px;}
a.btn_price_add {color:#3a88d8 !important;cursor:pointer;}
/*멀티파일관련*/
input[type="file"]{position:relative;width:250px;height:80px;border-radius:10px;overflow:hidden;cursor:pointer;border:1px solid #333;}
input[type="file"]::before{display:block;content:'';position:absolute;left:0;top:0;width:100%;height:100%;background:#000;opacity:1;z-index:3;}
input[type="file"]::after{display:block;content:'파일선택\A(드래그앤드롭 가능)';position:absolute;z-index:4;left:50%;top:50%;transform:translate(-50%,-50%);text-align:center;}
.MultiFile-wrap ~ ul{margin-top:10px;}
.MultiFile-wrap ~ ul > li{margin-top:10px;}
.MultiFile-wrap .MultiFile-list{}
.MultiFile-wrap .MultiFile-list > .MultiFile-label{position:relative;padding-left:25px;margin-top:10px;}
.MultiFile-wrap .MultiFile-list > .MultiFile-label .MultiFile-remove{position:absolute;top:0;left:0;font-size:0;}
.MultiFile-wrap .MultiFile-list > .MultiFile-label .MultiFile-remove::after{content:'×';display:block;position:absolute;left:0;top:0;width:20px;height:20px;border:1px solid #ccc;border-radius:50%;font-size:14px;line-height:20px;text-align:center;}
.MultiFile-wrap .MultiFile-list > .MultiFile-label > span{}
.MultiFile-wrap .MultiFile-list > .MultiFile-label > span span.MultiFile-label{display:inline-block;font-size:14px;border:1px solid #444;background:#333;padding:2px 5px;border-radius:3px;line-height:1.2em;margin-top:5px;}
.div_bop {line-height:1em;}
.div_add_price {margin-top:7px;display:none;}
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
<input type="hidden" name="ser_bct_idx" value="<?php echo $ser_bct_idx ?>">
<input type="hidden" name="ser_bom_type" value="<?php echo $ser_bom_type ?>">

<div class="local_desc01 local_desc" style="display:none;">
    <p>가격 변경 이력을 관리합니다. (가격 변동 날짜 및 가격을 지속적으로 기록하고 관리합니다.)</p>
    <p>가격이 변경될 미래 날짜를 지정해 두면 해당 날짜부터 변경될 가격이 적용됩니다.</p>
</div>
<?php //echo $rowb['bom_bomf1'][0]['file'];//print_r3($rowb['bom_bomf1']); ?>
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
        <?php
        $ar['id'] = 'bom_name';
        $ar['name'] = '품명';
        $ar['type'] = 'input';
        $ar['width'] = '100%';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = '제품명 or 자재명';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <th scope="row">타입</th>
		<td>
            <select name="bom_type" id="bom_type">
                <option value="">선택하세요</option>
                <?=$g5['set_bom_type_options']?>
            </select>
            <script>
                $('select[name="<?=$pre?>_type"]').val('<?=${$pre}[$pre.'_type']?>');
            </script>
		</td>
    </tr>
	<tr>
        <th scope="row">품번 (P/NO)</th>
        <td>
            <input type="text" name="bom_part_no" value="<?php echo ${$pre}['bom_part_no'] ?>" id="bom_part_no" required class="frm_input required" style="width:150px;">
            <div class="bom_part_nos">
                <?php
                // 품번히스토리
                if(${$pre}['bom_part_nos']) {
                    ${$pre}['bom_part_nos_array'] = explode("|",${$pre}['bom_part_nos']);
                    for ($j=0;$j<sizeof(${$pre}['bom_part_nos_array'])-1; $j++) {
                        if(${$pre}['bom_part_nos_array'][$j]) {
                            echo '<div idx="'.$j.'">'.${$pre}['bom_part_nos_array'][$j].' <span class="btn_part_no_delete" idx="'.$j.'"><i class="fa fa-times"></i></span></div>';
                        }
                    }
                }
                ?>
            </div>
        </td>
		<th scope="row">차종</th>
		<td>
            <?php
            // $csql = " SELECT bct_idx,bct_name FROM {$g5['bom_category_table']} WHERE com_idx = '{$_SESSION['ss_com_idx']}' AND LENGTH(bct_idx) = 2 ORDER BY bct_order, bct_idx ";
            $csql = " SELECT bct_idx,bct_name FROM {$g5['bom_category_table']} WHERE com_idx = '{$_SESSION['ss_com_idx']}' ORDER BY bct_order, bct_idx ";
            // echo $csql;
            $cresult = sql_query($csql,1);
            if($cresult->num_rows){
                echo '<select name="bct_idx" id="bct_idx" class="frm_input">'.PHP_EOL;
                    echo '<option value="">카테고리 선택</option>'.PHP_EOL;
                    for($i=0;$row=sql_fetch_array($cresult);$i++){
                    ?>
                    <option value="<?=$row['bct_idx']?>"><?=$row['bct_name']?></option>
                    <?php
                    }
                echo '</select>'.PHP_EOL;
                if($w == 'u'){
                ?>
                <script>
                $('#bct_idx').val('<?=${$pre}['bct_idx']?>');
                </script>
                <?php
                }
            }
            ?>
		</td>
    </tr>
	<tr>
        <th scope="row">고객사</th>
		<td>
            <input type="hidden" name="cst_idx_customer" value="<?=${$pre}['cst_idx_customer']?>">
			<input type="text" name="cst_name_customer" value="<?=$com['cst_name']?>" class="frm_input readonly" readonly>
            <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=customer" class="btn btn_02 btn_customer">찾기</a>
        </td>
        <th scope="row">협력사</th>
		<td>
            <input type="hidden" name="cst_idx_provider" value="<?=${$pre}['cst_idx_provider']?>">
			<input type="text" name="cst_name_provider" value="<?=$com2['cst_name']?>" class="frm_input readonly" readonly>
            <a href="./customer_select.php?file_name=<?=$g5['file_name']?>&item=provider" class="btn btn_02 btn_customer">찾기</a>
        </td>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'bom_spec';
        $ar['name'] = '규격';
        $ar['type'] = 'input';
        $ar['width'] = '80%';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['placeholder'] = '규격';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <th scope="row">단가 <a href="" class="btn btn_03 btn_add_price" style="margin-left:10px;">추가</a></th>
		<td>
            <?php
            $sql = " SELECT * FROM {$g5['bom_price_table']} WHERE bom_idx = '".${$pre}['bom_idx']."' ORDER BY bop_start_date ";
            // echo $sql.'<br>';
            $rs = sql_query($sql,1);
            for($i=0;$row=sql_fetch_array($rs);$i++) {
                // print_r2($row);
                echo '  <div class="div_bop font_size_9">'
                            .number_format($row['bop_price']).' 원 <span class="bop_price"> '.$row['bop_start_date'].' ~</span>
                            <span class="btn_bop_delete" bop_idx="'.$row['bop_idx'].'"><i class="fa fa-times"></i></span>
                        </div>';
            }
            ?>
            <div class="div_add_price">
                <input type="text" name="bom_price" value="<?=${$pre}['bom_price']?>" class="frm_input" style="width:80px;"> 원
                (적용일: <input type="text" name="bom_start_date" value="<?=${$pre}['bom_start_date']?>" class="frm_input" style="width:90px;"> 일부터)
            </div>
        </td>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'bom_usage';
        $ar['name'] = 'U/S';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'bom_moq';
        $ar['name'] = '최소구매수량';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'bom_min_cnt';
        $ar['name'] = '재고경고알림수량';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'bom_safe_stock';
        $ar['name'] = '안전재고수량';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'bom_lead_time';
        $ar['name'] = '리드타임';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['unit'] = '초';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'bom_draw';
        $ar['name'] = '도면번호';
        $ar['type'] = 'input';
        $ar['width'] = '200px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <th scope="row">포장용기</th>
        <td>
            <select name="<?=$pre?>_packing" id="<?=$pre?>_packing">
                <option value="">포장용기선택</option>
                <?=$g5['set_bom_packing_options']?>
            </select>
            <script>$('select[name="<?=$pre?>_packing"]').val('<?=${$pre}[$pre.'_packing']?>');</script>
        </td>
        <?php
        $ar['id'] = 'bom_ship_count';
        $ar['name'] = '출하포장갯수';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <?php
        $ar['id'] = 'bom_maker';
        $ar['name'] = '메이커';
        $ar['type'] = 'input';
        $ar['width'] = '100px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = 'bom_sort';
        $ar['name'] = '순서';
        $ar['type'] = 'input';
        $ar['width'] = '50px';
        $ar['value'] = ${$pre}[$ar['id']];
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
    <tr>
        <th scope="row">입고검사여부</th>
        <td>
            <input type="hidden" name="bom_stock_check_yn" value="<?=(${$pre}['bom_stock_check_yn'])?'1':''?>">
            <label><input type="checkbox" <?=(${$pre}['bom_stock_check_yn'])?'checked':''?> id="bom_stock_check_yn"> 입고검사</label>
            <script>
            $(document).on('click','#bom_stock_check_yn',function(e){
                if($(this).is(':checked')) {$('input[name=bom_stock_check_yn]').val(1);}
                else {$('input[name=bom_stock_check_yn]').val(0);}
            });
            </script>
        </td>
        <th scope="row">출하검사여부</th>
        <td>
            <input type="hidden" name="bom_delivery_check_yn" value="<?=(${$pre}['bom_delivery_check_yn'])?'1':''?>">
            <label><input type="checkbox" <?=(${$pre}['bom_delivery_check_yn'])?'checked':''?> id="bom_delivery_check_yn"> 출하검사</label>
            <script>
            $(document).on('click','#bom_delivery_check_yn',function(e){
                if($(this).is(':checked')) {$('input[name=bom_delivery_check_yn]').val(1);}
                else {$('input[name=bom_delivery_check_yn]').val(0);}
            });
            </script>
        </td>
    </tr>
        <th scope="row">완성품검사여부</th>
        <td>
            <input type="hidden" name="bom_pallet_check_yn" value="<?=(${$pre}['bom_pallet_check_yn'])?'1':''?>">
            <label><input type="checkbox" <?=(${$pre}['bom_pallet_check_yn'])?'checked':''?> id="bom_pallet_check_yn"> 완성품검사</label>
            <script>
            $(document).on('click','#bom_pallet_check_yn',function(e){
                if($(this).is(':checked')) {$('input[name=bom_pallet_check_yn]').val(1);}
                else {$('input[name=bom_pallet_check_yn]').val(0);}
            });
            </script>
        </td>
        <th scope="row">대표제품 초기화</th>
        <td>
            <label><input type="checkbox" name="bom_main_delete_yn" value="1"> 대표제품 초기화</label>
        </td>
    <tr>
    </tr>
    <?php
    $ar['id'] = 'bom_memo';
    $ar['name'] = '메모';
    $ar['type'] = 'textarea';
    $ar['value'] = ${$pre}[$ar['id']];
    $ar['colspan'] = 3;
    echo create_tr_input($ar);
    unset($ar);
    ?>
    <?php
    $ar['id'] = 'bom_bct_json';
    $ar['name'] = '분류번호배열';
    $ar['type'] = 'textarea';
    $ar['value'] = ${$pre}[$ar['id']];
    $ar['colspan'] = 3;
    echo create_tr_input($ar);
    unset($ar);
    ?>
    <tr>
        <th scope="row">상태</th>
        <td colspan="3">
            <select name="<?=$pre?>_status" id="<?=$pre?>_status"
                <?php if (auth_check($auth[$sub_menu],"d",1)) { ?>onFocus='this.initialSelect=this.selectedIndex;' onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
                <?=$g5['set_bom_status_options']?>
            </select>
            <script>$('select[name="<?=$pre?>_status"]').val('<?=${$pre}[$pre.'_status']?>');</script>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="multi_file1">대표이미지파일</label></th>
        <td colspan="3">
            <?php echo help("대표이미지파일을 등록하고 관리해 주시면 됩니다.<br>이미지파일 확장자는 gif | jpg | png 중에 하나의 확장자파일로 등록해 주세요."); ?>
            <input type="file" id="multi_file1" name="bom_f1[]" multiple class="bom_file">
            <?php
            //print_r3($row);
            if(@count($rowb['bom_bomf1'])){
                echo '<ul>'.PHP_EOL;
                for($i=0;$i<count($rowb['bom_bomf1']);$i++) {
                    echo "<li>[".($i+1).']'.$rowb['bom_bomf1'][$i]['file']."</li>".PHP_EOL;
                }
                echo '</ul>'.PHP_EOL;
            }
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="multi_file2">검사항목상세이미지</label></th>
        <td colspan="3">
            <?php echo help("검사항목상세이미지를 등록하고 관리해 주시면 됩니다.<br>이미지파일 확장자는 gif | jpg | png 중에 하나의 확장자파일로 등록해 주세요."); ?>
            <input type="file" id="multi_file2" name="bom_f2[]" multiple class="bom_file">
            <?php
            //print_r3($row);
            if(@count($rowb['bom_bomf2'])){
                echo '<ul>'.PHP_EOL;
                for($i=0;$i<count($rowb['bom_bomf2']);$i++) {
                    echo "<li>[".($i+1).']'.$rowb['bom_bomf2'][$i]['file']."</li>".PHP_EOL;
                }
                echo '</ul>'.PHP_EOL;
            }
            ?>
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
$(function() {
    $("#bom_start_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    
    var bom_file_cnt = $('.bom_file').length;
    for(var i=1; i<=bom_file_cnt; i++){
        $('#multi_file'+i).MultiFile({
            max: 1,
            accept: 'gif|jpg|jpeg|png'
        });
    }

    // 거래처찾기 버튼 클릭
	$(".btn_customer").click(function(e) {
		e.preventDefault();
        var href = $(this).attr('href');
		winCustomerSelect = window.open(href, "winCustomerSelect", "left=300,top=150,width=550,height=600,scrollbars=1");
        winCustomerSelect.focus();
	});

    $("input[name$=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

    // 가격 입력 쉼표 처리
	$(document).on( 'keyup','input[name$=_price], #bom_moq, #bom_lead_time',function(e) {
        if(!isNaN($(this).val().replace(/,/g,'')))
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
	});

    // 단가 보임 숨김
	$(document).on("click",".btn_add_price",function(e) {
        e.preventDefault();
        if( $('.div_add_price').is(':hidden') ) {
            $('.div_add_price').show();
            $('.btn_add_price').text('닫기');
        }
        else {
            $('.div_add_price').hide();
            $('.btn_add_price').text('추가');
        }
	});

	// 가격삭제
	$(document).on('click','.btn_bop_delete',function(e) {
		e.preventDefault();
		var bop_idx = $(this).attr('bop_idx');

		if(confirm('가격 정보를 삭제하시겠습니까?')) {

			//-- 디버깅 Ajax --//
			$.ajax({
				url:g5_user_admin_url+'/ajax/bom_price.php',
				data:{"aj":"del","bop_idx":bop_idx},
				dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
			//$.getJSON(g5_user_admin_url+'/ajax/com_item.json.php',{"aj":"del","bop_idx":bop_idx},function(res) {
				//alert(res.sql);
				if(res.result == true) {
                    // self.location.reload();
                    $('span[bop_idx='+bop_idx+']').closest('div.div_bop').remove();
				}
				else {
					alert(res.msg);
				}

				}, error:this_ajax_error	//<-- 디버깅 Ajax --//
			});
		}
	});
	// 품번 삭제
	$(document).on('click','.btn_part_no_delete',function(e) {
		e.preventDefault();
		var bom_idx = '<?=$bom_idx?>';
		var idx = $(this).attr('idx');
		if(confirm('품번 정보를 삭제하시겠습니까?')) {

			//-- 디버깅 Ajax --//
			$.ajax({
				url:g5_user_admin_url+'/ajax/bom.json.php',
				data:{"aj":"d2","bom_idx":bom_idx,"idx":idx},
				dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
			//$.getJSON(g5_user_admin_url+'/ajax/com_item.json.php',{"aj":"del","bop_idx":bop_idx},function(res) {
				//alert(res.sql);
				if(res.result == true) {
                    $('div[idx='+idx+']').remove();
				}
				else {
					alert(res.msg);
				}

				}, error:this_ajax_error	//<-- 디버깅 Ajax --//
			});
		}
	});

});

// 숫자만 입력
function chk_Number(object){
    $(object).keyup(function(){
        $(this).val($(this).val().replace(/[^0-9|-]/g,""));
    });
}

function form01_submit(f) {

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
