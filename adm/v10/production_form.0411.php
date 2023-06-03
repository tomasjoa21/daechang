<?php
$sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

if($w == 'u') {

    $prd = get_table('production','prd_idx',$prd_idx);
    // print_r2($prd);exit;
    $bom = get_table('bom','bom_idx',$prd['bom_idx']);

    // 완제품에 대한 지시수량만 가지고 옴
    $sql = "  SELECT pri_idx, pri_value FROM {$g5['production_item_table']}
                        WHERE bom_idx = '{$prd['bom_idx']}' AND prd_idx = '{$prd['prd_idx']}' AND pri_status NOT IN ('trash','delete')
                        LIMIT 1
    ";
    $pri = sql_fetch($sql,1);
    // print_r3($sql);
    // print_r3($pri);
    $prd['pri_idx'] = $pri['pri_idx'];
    $prd['pri_value'] = $pri['pri_value'];

}
// print_r2($str_arr);exit;

$html_title = ($w=='')?'추가':'수정';
$html_title = ($w=='c')?'복제':$html_title;
$g5['title'] = '생산계획 '.$html_title;

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
<input type="hidden" name="prd_idx" value="<?php echo $prd["prd_idx"] ?>">
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
                <input type="hidden" name="bom_idx" id="bom_idx" value="<?=$prd['bom_idx']?>">
                <input type="text" name="bom_name" value="<?=$bom['bom_name']?>" class="frm_input required readonly" required readonly>
                <span class="span_bom_part_no font_size_8"><?=$bom['bom_part_no']?></span>
                <?php if($w == ''){ ?>
                    <a href="./bom_select.php?file_name=<?=$g5['file_name']?>" class="btn btn_02" id="btn_bom">찾기</a>
                    <button type="button" class="btn btn_04 bom_cancel">취소</button>
                <?php } ?>
            </td>
            <th scope="row">수주(수주일/ID)</th>
            <td>
                <input type="text" name="ori_date" id="ori_date" placeholder="수주날짜" value="<?=$prd['ori_date']?>"<?=$readonly?> class="frm_input<?=$readonly?>" style="width:90px;"> /
                <input type="text" name="ori_idx" id="ori_idx" placeholder="수주ID" value="<?=$prd['ori_idx']?>"<?=$readonly?> class="frm_input<?=$readonly?>" style="width:80px;">
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
                    echo $prd['prd_order_no'];
                }
                ?>
                
            </td>
            <th scope="row">지시량</th>
            <td>
                <input type="text" name="prd_count" value="<?=number_format($prd['pri_value'])?>" id="prd_count" onclick="javascript:<?php if($w == ''){ ?>numtoprice(this)<?php } else { ?>numtoprice_prd(this)<?php } ?>" class="frm_input" style="text-align:right;width:90px;">&nbsp;&nbsp;
                <span>(완제품기준)</span>
            </td>
        </tr>
        <tr>
            <th scope="row">생산시작일</th>
            <td>
                <input type="text" name="prd_start_date" id="prd_start_date" value="<?=(($prd['prd_start_date'] && $prd['prd_start_date'] != '0000-00-00')?$prd['prd_start_date']:'')?>" readonly class="readonly tbl_input" style="width:90px;background:#333 !important;text-align:center;">
            </td>
            <th scope="row">상태</th>
            <td>
                <select name="prd_status" id="prd_status"<?php if($w != ''){ ?> onchange="javascript:status_change(this);"<?php } ?>>
                    <?=$g5['set_prd_status_options']?>
                </select>
                <script>
                $('#prd_status').val('<?=(($w=='')?'confirm':$prd['prd_status'])?>');   
                </script>
            </td>
        </tr>
        <tr>
            <th scope="row">메모</th>
            <td colspan="3">
                <input type="text" name="prd_memo" id="prd_memo" class="frm_input" value="<?=$prd['prd_memo']?>" style="width:100%;">
            </td>
        </tr>
	</tbody>
	</table>
    <!--########### BOM 구조목록 : 시작 ###########-->
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
            <th style="width:80px;">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // 맨 처음 항목(완제품) 추출을 위해서 UNION ALL
        $sql1 = "   SELECT *
                    FROM (
                            (
                            SELECT bom.bom_idx, bom.bom_name, bom_part_no, bom_price, bom_status, 'MIP' AS cst_name
                                , 0 AS bit_idx, 0 AS bit_bom_idx, 0 AS bit_main_yn, 0 AS bom_idx_child, '' AS bit_reply, bom_usage AS bit_count
                            FROM g5_1_bom AS bom
                            WHERE bom_idx = '".$prd['bom_idx']."'
                            )
                        UNION ALL
                            (
                            SELECT bom.bom_idx, bom.bom_name, bom_part_no, bom_price, bom_status, cst_name
                                , bit.bit_idx, bit.bom_idx, bit.bit_main_yn, bit.bom_idx_child, bit.bit_reply, bit.bit_count
                            FROM {$g5['bom_item_table']} AS bit
                                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                                LEFT JOIN {$g5['customer_table']} AS cst ON cst.cst_idx = bom.cst_idx_provider
                            WHERE bit.bom_idx = '".$prd['bom_idx']."'
                            ORDER BY bit.bit_reply
                            )
                    ) AS db1
        ";
        // print_r3($sql1);
        $rs1 = sql_query($sql1,1);
        $row['rows'] = sql_num_rows($rs1);
        $row['rows_text'] = $row['rows'] ? '<span class="font_size_8 ml_10">(구성품수: '.$row['rows'].')</span>' : '';
        // echo $rowspan.'<br>';
        for ($j=0; $row1=sql_fetch_array($rs1); $j++) {
            // print_r2($row1);
            $row1['bit_main_class'] = $row1['bit_main_yn'] ? 'bit_main' : ''; // 대표제품 색상
            $len = strlen($row1['bit_reply'])/2+1;
            $row1['len'] = '<span class="btn_number">'.$len.'</span>';
            for ($k=1; $k<$len; $k++) { $row1['nbsp'] .= '&nbsp;&nbsp;&nbsp;'; } // 들여쓰기공백

            // 설비별 작업자 연결 정보 추출 ---------------------------------------------------------------
            $sql2 = "   SELECT bmw_idx, bmw.mms_idx AS mms_idx, mms_name, bmw.mb_id AS mb_id, mb_name, bmw_type
                        FROM {$g5['bom_mms_worker_table']} AS bmw
                            LEFT JOIN {$g5['mms_table']} AS mms ON mms.mms_idx = bmw.mms_idx
                            LEFT JOIN {$g5['member_table']} AS mb ON mb.mb_id = bmw.mb_id
                        WHERE bom_idx = '".$row1['bom_idx']."'
            ";
            // echo $sql2.BR;
            $rs2 = sql_query($sql2,1);
            $row1['bmw_rows'] = sql_num_rows($rs2); // 몇 개
            // echo $row1['bmw_rows'];
            for ($k=0; $row2=sql_fetch_array($rs2); $k++) {
                // print_r2($row2);
                $row1['mms_option_arr'][$k]['mms_idx'] = $row2['mms_idx'];
                $row1['mms_option_arr'][$k]['mms_name'] = $row2['mms_name'];
                $row1['mb_id_arr'][$k]['mb_id'] = $row2['mb_id'];
                $row1['mb_id_arr'][$k]['mb_name'] = $row2['mb_name'];
                $row1['mb_id_arr'][$k]['bmw_tpye'] = $row2['bmw_type'];
            }
            // print_r2($row1['mms_option_arr']);
            // 설비 중복 제거하고 하나씩만 표현
            $row1['mms_options_ar'] = @array_unique($row1['mms_option_arr'],SORT_REGULAR);
            $row1['mms_options_ar'] = @array_values(array_filter($row1['mms_options_ar']));
            // print_r2($row1['mms_options_ar']);
            for ($y=0; $y<@sizeof($row1['mms_options_ar']); $y++) {
                // print_r2($row1['mms_options_ar'][$y]);
                $row1['mms_options'] .= '<option value="'.$row1['mms_options_ar'][$y]['mms_idx'].'">'.$row1['mms_options_ar'][$y]['mms_name'].'</option>';
            }
            // 작업자 중복 제거하고 하나씩만 표현
            $row1['mb_ids_ar'] = @array_unique($row1['mb_id_arr'],SORT_REGULAR);
            $row1['mb_ids_ar'] = @array_values(array_filter($row1['mb_ids_ar']));
            // print_r2($row1['mb_ids_ar']);
            for ($y=0; $y<@sizeof($row1['mb_ids_ar']); $y++) {
                // print_r2($row1['mb_ids_ar'][$y]);
                $row1['mb_ids_ar'][$y]['mb_type'] = $g5['set_bmw_type_value'][$row1['mb_ids_ar'][$y]['bmw_tpye']];
                $row1['mb_id_options'] .= '<option value="'.$row1['mb_ids_ar'][$y]['mb_id'].'">'.$row1['mb_ids_ar'][$y]['mb_name'].'('.$row1['mb_ids_ar'][$y]['mb_type'].')</option>';
            }

            // 설비별 작업자 할당 정보 추출 ---------------------------------------------------------------
            // production_item
            $row1['pri'] = sql_fetch("  SELECT * FROM {$g5['production_item_table']}
                                WHERE bom_idx = '{$row1['bom_idx']}' AND prd_idx = '{$prd['prd_idx']}' AND pri_status NOT IN ('trash','delete')
                                LIMIT 1
            ");
            // print_r2($row1['pri']);
            // production_member (생산아이템:생산자=1:n)
            if($row1['pri']['pri_idx']) {
                $sql3 = "   SELECT * FROM {$g5['production_member_table']}
                            WHERE pri_idx = '".$row1['pri']['pri_idx']."'
                ";
                $rs3 = sql_query($sql3,1);
                for ($x=0; $row3=sql_fetch_array($rs3); $x++) {
                    print_r2($row3);
                }
            }
            ?>
            <tr>
                <td class="td_bom_name" style="padding-left:<?=$padding_left?>px;">
                    <input type="hidden" name="prm_idx" value="<?=$row['prm_idx']?>">
                    <?=$row1['nbsp']?><?=$row1['len']?>
                    <span class="font_size_7"><?=$row1['bom_part_no']?></span>
                    <?=$row1['bom_name']?>
                    <span class="span_cst_name font_size_8"><?=$row1['cst_name']?></span>
                    <span class="span_bit_count font_size_8"><?=$row1['bit_count']?>개</span>
                </td>
                <td class="td_mms_idx" mms_idx="<?=$str_arr[$i]['mms_idx']?>"><!-- 설비 -->
                    <?php if($row1['bmw_rows']) { ?>
                    <select name="mms_idxs[<?=$str_arr[$i]['pri_idx']?>]" id="mms_idx_<?=$i?>" sync="mmw_idx_<?=$i?>" class="mms_idx">
                        <?=$row1['mms_options']?>
                    </select>
                    <script>
                    <?php if($w != ''){ ?>
                        $('#mms_idx_<?=$i?>').val('<?=$str_arr[$i]['mms_idx']?>');
                    <?php } ?>
                    </script>
                    <?php } ?>
                </td>
                <td class="td_mmw_idx"><!-- 작업자 -->
                    <?php if($row1['bmw_rows']) { ?>
                    <select name="mb_ids[<?=$str_arr[$i]['pri_idx']?>]" id="mb_id_<?=$i?>" sync="mmw_idx_<?=$i?>" class="mb_id">
                        <?=$row1['mb_id_options']?>
                    </select>
                    <script>
                    <?php if($w != ''){ ?>
                        $('#mb_id_<?=$i?>').val('<?=$str_arr[$i]['mb_id']?>');
                    <?php } ?>
                    </script>
                    <?php } ?>
                </td>
                <td class="td_pri_value" pri_value="<?=$str_arr[$i]['pri_value']?>">
                    <?php if($row1['bmw_rows']) { ?>
                    <input type="text" name="pri_values[<?=$str_arr[$i]['pri_idx']?>]" class="frm_input pri_value" value="<?=number_format($str_arr[$i]['pri_value'])?>" onclick="javascript:numtoprice(this)" bit_count="<?=$str_arr[$i]['bit_count']?>">
                    <?php } ?>
                </td>
                <td class="td_pri_memo">
                    <?php if($row1['bmw_rows']) { ?>
                    <input type="text" name="pri_memos[<?=$str_arr[$i]['pri_idx']?>]" class="frm_input pri_memo" id="pri_memo_<?=$i?>" value="<?=$str_arr[$i]['pri_memo']?>">
                    <?php } ?>
                </td>
                <td class="td_pri_status"><!-- 상태 -->
                    <?php if($row1['bmw_rows']) { ?>
                    <select name="pri_statuss[<?=$str_arr[$i]['pri_idx']?>]" id="pri_status_<?=$i?>" class="pri_status<?php if($row['prd_status']!='confirm'){ ?> deactivation<?php } ?>"<?php if($row['prd_status']!='confirm'){ ?> onFocus='this.initialSelect=this.selectedIndex;'
                        onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
                    <?=$g5['set_prd_status_value_options']?>
                    </select>
                    <script>
                    $('#pri_status_<?=$i?>').val('<?=(($w=='')?'confirm':$str_arr[$i]['pri_status'])?>');   
                    </script>
                    <?php } ?>
                </td>
                <td class="td_btns">
                    <?php if($row1['bmw_rows']) { ?>
                    <input type="submit" name="act_button" value="문자" sync="mmw_idx_<?=$i?>" syncm="pri_memo_<?=$i?>" syns="pri_status_<?=$i?>" onclick="document.pressed=this.value;document.sms_pri_idx=<?=$str_arr[$i]['pri_idx']?>;document.sync=this.getAttribute('sync');document.syncm=this.getAttribute('syncm');document.syns=this.getAttribute('syns');" class="btn btn_05" accesskey='s'>
                    <a href="javascript:" class="btn btn_05 btn_copy">복제</a>
                    <?php } ?>
                </td>
            </tr>
            <?php
        }
        if($j<=0) { 
            echo '<tr><td colspan="9" class="empty_table">기본 정보를 등록한 후 설정할 수 있습니다.</td></tr>';
        }
        ?>
        </tbody>  
        </table>
    </div><!--//tbl_head01 tbl_wrap-->



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
    $(document).on('click','.btn_copy',function(e){
        e.preventDefault();
        

    });

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