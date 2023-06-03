<?php
$sub_menu = "922110";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

if ($w == '') {

}
else if ($w == 'u') {

    $prd = get_table('production','prd_idx',$prd_idx);
    // print_r3($prd);//exit;
    $bom = get_table('bom','bom_idx',$prd['bom_idx']);

    // 완제품에 대한 지시수량만 가지고 옴
    $sql = "SELECT SUM(prm_value) AS prm_total, COUNT(prm_idx) AS prm_count
            FROM {$g5['production_member_table']} AS prm
                LEFT JOIN {$g5['production_item_table']} AS pri USING(pri_idx)
            WHERE prd_idx = '".$prd['prd_idx']."' AND pri.bom_idx = '".$prd['bom_idx']."' AND prm_status NOT IN ('trash','delete')
            GROUP BY pri_idx
    ";
    // print_r3($sql);
    $prm = sql_fetch($sql,1);
    // print_r3($prm);
    if($prm['prm_total']) {
        $prd['pri_count'] = $prm['prm_total'];
    }
    else {
        $ori = get_table('order_item','ori_idx',$prd['ori_idx']);
        // print_r3($ori);
        $prd['pri_count'] = $ori['ori_count'];
    }

}
// print_r2($str_arr);exit;

if(!$prd['prd_order_no']) {
    $tdcode = preg_replace('/[ :-]*/','',G5_TIME_YMDHIS);
    $prd_order_no = "PRD-".strtoupper(wdg_uniqid());
    $prd['prd_order_no'] = $prd_order_no;
}

$html_title = ($w=='')?'추가':'수정';
$html_title = ($w=='c')?'복제':$html_title;
$g5['title'] = '생산계획 '.$html_title;

$qstr .= ($calendar)?'&start_date='.$first_date.'&end_date='.$last_date:'';

$readonly = ' readonly';
$required = ' required';

include_once('./_head.php');
?>
<style>
.btn_del {background-color:#5e2902 !important;}
</style>

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

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>'지시량' 항목은 수정 불가능합니다.(최초 등록 시에만 가능함) 하단 리스트중에서 최상위 완제품의 지시수량을 변경해 주시면 자동으로 계산됩니다.</p>
    <p>상단의 [초기화] 버튼을 클릭하면 완제품 기준으로 모든 정보가 초기화됩니다. 초기 상태에서 새롭게 설정하시면 됩니다.</p>
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
                <input type="text" name="prd_order_no" value="<?=$prd['prd_order_no']?>" class="frm_input" <?=($w=='u')?'readonly':''?> style="width:200px;">
            </td>
            <th scope="row">지시량</th>
            <td>
                <input type="text" name="prd_count" value="<?=number_format($prd['pri_count'])?>" id="prd_count" onclick="javascript:numtoprice(this);" class="frm_input" style="text-align:right;width:90px;" <?=($w=='u')?'readonly':''?>>
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
            <th class="th_prm_value">지시량</th>
            <th class="th_prm_status">상태</th>
            <th style="width:80px;">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // BOM 구조를 따라서 계층구조 추출
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
        for ($i=0; $row1=sql_fetch_array($rs1); $i++) {
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
            // production_member + production_item (2개 이상인 경우는 복수개로 표현되어야 함)
            $sql3 = "   SELECT prm.*, bom.*
                        FROM {$g5['production_member_table']} AS prm
                            LEFT JOIN {$g5['production_item_table']} AS pri USING(pri_idx)
                            LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = pri.bom_idx
                        WHERE prd_idx = '".$prd['prd_idx']."' AND pri.bom_idx = '".$row1['bom_idx']."'
                        ORDER BY pri_idx, prm_idx
            ";
            // print_r3($sql3);
            $rs3 = sql_query($sql3,1);
            for ($x=0; $row3=sql_fetch_array($rs3); $x++) {
                // print_r2($row3);
                $ix = (int)$i.$x;
                // 복제, 삭제 버튼
                $s_copy = '<a href="javascript:" class="btn btn_05 btn_copy">복제</a>';
                $s_del = '<a href="javascript:" class="btn btn_05 btn_del">삭제</a>';

                // // bom_usage 임의 변경(테스트)
                // $row3['bom_usage'] = ($row3['bom_idx']==55)? 2:$row3['bom_usage'];               
                ?>
                <tr bom_idx="<?=$row3['bom_idx']?>" bom_usage="<?=$row3['bom_usage']?>" prm_idx="<?=$row3['prm_idx']?>" pri_idx="<?=$row3['pri_idx']?>">
                    <td class="td_bom_name" style="padding-left:<?=$padding_left?>px;">
                        <input type="hidden" name="chk[<?=$ix?>]" value="<?=$row3['prm_idx']?>">
                        <input type="hidden" name="pri_idxs[<?=$ix?>]" value="<?=$row3['pri_idx']?>">
                        <input type="hidden" name="prm_idxs[<?=$ix?>]" value="<?=$row3['prm_idx']?>">
                        <span style="visibility:<?=($bom_prev==$row3['bom_idx'])?'hidden':''?>;"><?=$row1['nbsp']?><?=$row1['len']?></span>
                        <?php if($bom_prev != $row3['bom_idx']) { // 이전 bom과 같으면 품번/품명 숨김 ?>
                            <span class="font_size_7 <?=$row1['bit_main_class']?>"><?=$row1['bom_part_no']?></span>
                            <?=$row1['bom_name']?>
                            <span class="span_cst_name font_size_8"><?=$row1['cst_name']?></span>
                            <span class="span_bit_count font_size_8"><?=$row1['bit_count']?>개</span>
                        <?php } else { ?>
                            <span class="font_size_7 <?=$row1['bit_main_class']?>">ㄴ <?=$row1['bom_part_no']?></span>
                            <span class="span_cst_name font_size_8">동일 제품 생산</span>
                        <?php } ?>
                    </td>
                    <td class="td_mms_idx"><!-- 설비 -->
                        <select name="mms_idxs[<?=$ix?>]" id="mms_idx_<?=$ix?>" sync="mmw_idx_<?=$ix?>" class="mms_idx">
                            <?=$row1['mms_options']?>
                        </select>
                        <script>$('#mms_idx_<?=$ix?>').val('<?=$row3['mms_idx']?>');</script>
                    </td>
                    <td class="td_mb_id"><!-- 작업자 -->
                        <select name="mb_ids[<?=$ix?>]" id="mb_id_<?=$ix?>" sync="mb_id_<?=$ix?>" class="mb_id">
                            <?=$row1['mb_id_options']?>
                        </select>
                        <script>$('#mb_id_<?=$ix?>').val('<?=$row3['mb_id']?>');</script>
                    </td>
                    <td class="td_prm_value"><!-- 지시량 -->
                        <input type="text" name="prm_values[<?=$ix?>]" class="frm_input prm_value" value="<?=number_format($row3['prm_value'])?>" onclick="javascript:numtoprice(this)" bit_count="<?=$row3['bit_count']?>">
                    </td>
                    <td class="td_prm_status"><!-- 상태 -->
                        <select name="prm_statuss[<?=$ix?>]" id="prm_status_<?=$ix?>" class="prm_status<?php if($row['prd_status']!='confirm'){ ?> deactivation<?php } ?>"<?php if($row['prd_status']!='confirm'){ ?> onFocus='this.initialSelect=this.selectedIndex;'
                            onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
                        <?=$g5['set_prd_status_value_options']?>
                        </select>
                        <script>
                        $('#prm_status_<?=$ix?>').val('<?=(($w=='')?'confirm':$row3['prm_status'])?>');   
                        </script>
                    </td>
                    <td class="td_btns">
                        <input type="submit" name="act_button" value="문자" class="btn btn_05">
                        <?=($bom_prev==$row3['bom_idx'])?$s_del:$s_copy?>
                    </td>
                </tr>
                <?php
                // 기존 bom 정보 저장
                $bom_prev = $row3['bom_idx'];
            }
            // 생산아이템(production_item)이 없는 경우는 단순 자재인 경우로 봐야 함 (설정값 없음)
            if($x<=0) {
                ?>
                <tr>
                    <td class="td_bom_name" style="padding-left:<?=$padding_left?>px;">
                        <?=$row1['nbsp']?><?=$row1['len']?>
                        <span class="font_size_7 <?=$row1['bit_main_class']?>"><?=$row1['bom_part_no']?></span>
                        <?=$row1['bom_name']?>
                        <span class="span_cst_name font_size_8"><?=$row1['cst_name']?></span>
                        <span class="span_bit_count font_size_8"><?=$row1['bit_count']?>개</span>
                    </td>
                    <td class="td_mms_idx"><!-- 설비 -->
                    </td>
                    <td class="td_mmw_idx"><!-- 작업자 -->
                    </td>
                    <td class="td_prm_value">
                    </td>
                    <td class="td_prm_status"><!-- 상태 -->
                    </td>
                    <td class="td_btns">
                    </td>
                </tr>
                <?php
            }
        }
        if($i<=0) { 
            echo '<tr><td colspan="9" class="empty_table">기본 정보를 등록한 후 설정하세요.</td></tr>';
        }
        ?>
        </tbody>  
        </table>
    </div><!--//tbl_head01 tbl_wrap-->

</div>

<div class="btn_fixed_top">
    <?php
    $production_url = ($calendar) ? './order_out_practice_calendar_list.php?'.$qstr:'./production_list.php?'.$qstr;
    ?>
    <?php if($w == 'u'){ ?>
    <input type="submit" name="act_button" value="문자발송" onclick="document.pressed=this.value" class="btn btn_05">
    <input type="submit" name="act_button" value="초기화" onclick="document.pressed=this.value" class="btn btn_05 mr_30">
    <?php } ?>
    <a href="<?=$production_url?>" class="btn btn_02">목록</a>
    <input type="submit" name="act_button" value="확인" onclick="document.pressed=this.value" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function(){
    // 복제, ajax로 복제 작업 후 새로고침
    $(document).on('click','.btn_copy',function(e){
        e.preventDefault();
        if(confirm('해당 항목을 복제하고 작업을 분산처리 하시겠습니까?\n복제 후 지시수량 반드시 확인하세요.')) {
            //-- 디버깅 Ajax --//
            var prm_idx = $(this).closest('tr').attr('prm_idx');
            var mb_id = $(this).closest('tr').find('select[name^=mb_id]').val();
            var mms_idx = $(this).closest('tr').find('select[name^=mms_idx]').val();
            var prm_value = $(this).closest('tr').find('input[name^=prm_value]').val();
            // console.log(prm_idx+'/'+mb_id+'/'+prm_value);

            $.ajax({
                url:g5_user_admin_url+'/ajax/production.json.php',
                data:{"aj":"c1","prm_idx":prm_idx,"mms_idx":mms_idx,"mb_id":mb_id,"prm_value":prm_value},
                dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
            //$.getJSON(g5_user_admin_url+'/ajax/company.json.php',{"aj":"c1","com_idx":com_idx},function(res) {
                //alert(res.sql);
                    if(res.result == true) {
                        self.location.reload();
                    }
                    else {
                        alert(res.msg);
                    }
                },
                error:function(xmlRequest) {
                    alert('Status: ' + xmlRequest.status + ' \n\rstatusText: ' + xmlRequest.statusText 
                        + ' \n\rresponseText: ' + xmlRequest.responseText);
                }
            });
        }
    });

    // 삭제, ajax로 작업 후 새로고침
    $(document).on('click','.btn_del',function(e){
        e.preventDefault();
        if(confirm('해당 항목을 삭제하시겠습니까?\n삭제 후 지시수량 확인하세요.')) {
            //-- 디버깅 Ajax --//
            var prm_idx = $(this).closest('tr').attr('prm_idx');

            $.ajax({
                url:g5_user_admin_url+'/ajax/production.json.php',
                data:{"aj":"d1","prm_idx":prm_idx},
                dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
            //$.getJSON(g5_user_admin_url+'/ajax/company.json.php',{"aj":"c1","com_idx":com_idx},function(res) {
                //alert(res.sql);
                    if(res.result == true) {
                        self.location.reload();
                    }
                    else {
                        alert(res.msg);
                    }
                },
                error:function(xmlRequest) {
                    alert('Status: ' + xmlRequest.status + ' \n\rstatusText: ' + xmlRequest.statusText 
                        + ' \n\rresponseText: ' + xmlRequest.responseText);
                }
            });
        }
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
function status_change(obj){
    // alert(obj.value);
    $('.prm_status').each(function(){
        $(this).val(obj.value);
        // production_item 상태가 confirm이면 하위 상태값은 수정 불가!
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
    if(document.pressed == "문자"){
        var mmw_select = document.getElementById(document.sync);
        var prm_status = document.getElementById(document.syns).value;
        var memo = document.getElementById(document.syncm).value;
        var mmw_id = mmw_select.options[mmw_select.selectedIndex].value;
        var mmw_name = mmw_select.options[mmw_select.selectedIndex].textContent;
        memo = $.trim(memo);
        if(prm_status != 'confirm'){
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
    else if(document.pressed == "초기화"){
        if(confirm('생산계획을 초기화하시겠습니까?')){
            return true;
        }
        return false;
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

    var mms_idx_error = 0;
    $('.mms_idx').each(function(){
        if(!$(this).val()){
            mms_idx_error++;
        }
    });
    if(mms_idx_error>0) {
        alert('설비선택을 확인해 주세요.');
        return false;
    }

    var mb_id_error = 0;
    $('.mb_id').each(function(){
        if(!$(this).val()){
            mb_id_error++;
        }
    });
    if(mb_id_error>0) {
        alert('작업자 선택을 확인해 주세요.');
        return false;
    }



    var prm_value_empty_error = 0;
    var prm_value_arr = [];
    $('.prm_value').each(function(){
        if(!$(this).val()){
            prm_value_empty_error++;
        }
        // console.log( $('#prd_count').val() );
        // console.log( $(this).closest('tr').attr('pri_idx') );
        // console.log( $(this).val() );
        var bom_idx = parseInt($(this).closest('tr').attr('bom_idx'));
        var bom_usage = parseInt($(this).closest('tr').attr('bom_usage'));
        var prd_idx = parseInt($(this).closest('tr').attr('pri_idx'));
        var prd_val = parseInt($(this).val().replace(/[^0-9|-]/g,""));
        prm_value_arr.push({bom_idx:bom_idx,bom_usage:bom_usage,prd_idx:prd_idx,prd_val:prd_val});
    });
    if(prm_value_empty_error>0) {
        alert('지시량이 없는 항목이 있습니다.');
        return false;
    }
    // console.log(prm_value_arr);
    // 각 요소별 항목을 개별 합계 추출 reduce 함수 사용
    const prm_values = Object.values(prm_value_arr.reduce((acc, cur) => {
        const {bom_idx, bom_usage, prd_idx} = cur;
        const key = `${bom_idx}_${bom_usage}_${prd_idx}`;
        if (acc[key]) {
            acc[key].prd_val += parseInt(cur.prd_val);
        } else {
            acc[key] = {...cur};
        }
        return acc;
    }, {}));
    // console.log(prm_values);
    // const result = prm_values.find((item)=>item.bom_idx=== 114).prd_val;
    // console.log(result); // 130
    // 지시량과 맞지 않는 항목이 있으면 수정 불가!!
    var prm_value_count_error = 0;
    var prd_value = prm_values.find((item)=>item.bom_idx===parseInt($('#bom_idx').val())).prd_val;
    // console.log(prd_value);
    for (const i in prm_values) {
    //    console.log(`prm_values[${i}] = ${prm_values[i]}`);
        // console.log(prm_values[i]);
        // console.log(prm_values[i]['bom_usage']);
        // console.log(prm_values[i]['prd_val']);
        var prd_value2 = prm_values[i]['bom_usage']*prd_value;
        // console.log(prd_value2);
        if(prm_values[i]['prd_val'] != prd_value2 ) {
            prm_value_count_error++;
        }
    }
    // console.log(prm_value_count_error);
    if(prm_value_count_error>0) {
        alert('완제품의 지시수량과 각 하위제품의 지시수량 합계가 일치하지 않습니다.\n제품의 지시 수량을 확인하세요.');
        return false;
    }



    var prm_status_error = 0;
    $('.mms_idx').each(function(){
        if(!$(this).val()){
            prm_status_error++;
        }
    });
    if(prm_status_error>0) {
        alert('상태값 설정을 확인해 주세요.');
        return false;
    }

    return true;
    // return false;
}
</script>

<?php
include_once ('./_tail.php');