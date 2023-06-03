<?php
$sub_menu = "940135";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'shift';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr .= '&ser_mms_idx='.$ser_mms_idx; // 추가로 확장해서 넘겨야 할 변수들

// print_r3($member);
// print_r3($_SESSION);

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
    
    ${$pre}['com_idx'] = $_SESSION['ss_com_idx'];
    ${$pre}['mms_idx'] = 0; // 모둔설비
    ${$pre}[$pre.'_period_type'] = 0;
    ${$pre}[$pre.'_start_time'] = '09:00:00';
    ${$pre}[$pre.'_end_time'] = '23:59:59';
    ${$pre}[$pre.'_target_1'] = 100;
    ${$pre}[$pre.'_start_dt'] = date("Y-m-d H:i:00");
    ${$pre}[$pre.'_end_dt'] = date("Y-m-d H:i:00",time()+86400*3);
    ${$pre}[$pre.'_status'] = 'ok';
}
else if ($w == 'u' || $w == 'c') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

    ${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    // print_r3(${$pre});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
	$com = get_table_meta('company','com_idx',${$pre}['com_idx']);
    $mms = get_table_meta('mms','mms_idx',${$pre}['mms_idx']);

	// 관련 파일 추출
	$sql = "SELECT * FROM {$g5['file_table']} 
			WHERE fle_db_table = '".$pre."' AND fle_db_id = '".${$pre}[$pre.'_idx']."' ORDER BY fle_sort, fle_reg_dt DESC ";
	$rs = sql_query($sql,1);
//	echo $sql;
	for($i=0;$row=sql_fetch_array($rs);$i++) {
		${$pre}[$row['fle_type']][$row['fle_sort']]['file'] = (is_file(G5_PATH.$row['fle_path'].'/'.$row['fle_name'])) ? 
							'&nbsp;&nbsp;'.$row['fle_name_orig'].'&nbsp;&nbsp;<a href="'.G5_USER_ADMIN_URL.'/lib/download.php?file_fullpath='.urlencode(G5_PATH.$row['fle_path'].'/'.$row['fle_name']).'&file_name_orig='.$row['fle_name_orig'].'">파일다운로드</a>'
							.'&nbsp;&nbsp;<input type="checkbox" name="'.$row['fle_type'].'_del['.$row['fle_sort'].']" value="1"> 삭제'
							:'';
		${$pre}[$row['fle_type']][$row['fle_sort']]['fle_name'] = (is_file(G5_PATH.$row['fle_path'].'/'.$row['fle_name'])) ? 
							$row['fle_name'] : '' ;
		${$pre}[$row['fle_type']][$row['fle_sort']]['fle_path'] = (is_file(G5_PATH.$row['fle_path'].'/'.$row['fle_name'])) ? 
							$row['fle_path'] : '' ;
		${$pre}[$row['fle_type']][$row['fle_sort']]['exists'] = (is_file(G5_PATH.$row['fle_path'].'/'.$row['fle_name'])) ? 
							1 : 0 ;
	}
	
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

// 적용기간 선택
${$pre.'_period_type_'.${$pre}[$pre.'_period_type']} = ' checked';

// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_sex');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'추가':'수정'; 
$html_title = ($w=='c')?'복제':$html_title; 
$g5['title'] = '작업교대 '.$html_title;
//include_once('./_top_menu_shift.php');
include_once ('./_head.php');
//echo $g5['container_sub_title'];
?>
<style>
.div_shf_period {margin-top:5px;}
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<input type="hidden" name="ser_mms_idx" value="<?php echo $ser_mms_idx ?>">
<input type="hidden" name="com_idx" value="<?=$_SESSION['ss_com_idx']?>">

<div class="local_desc01 local_desc" style="display:none;">
    <p>각종 고유번호(설비번호, IMP번호..)들은 내부적으로 다른 데이타베이스 연동을 통해서 정보를 가지고 오게 됩니다.</p>
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
        <?php
        $ar['id'] = 'shf_name';
        $ar['name'] = '교대(구간)명';
        $ar['type'] = 'input';
        $ar['width'] = '100%';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = 'ex)1교대, 야간교대';
        echo create_td_input($ar);
        unset($ar);
        ?>
		<th scope="row">설비선택</th>
		<td>
            <select name="mms_idx" id="mms_idx">
                <option value="0">모든설비</option>
                <?php
                // 해당 범위 안의 모든 설비를 select option으로 만들어서 선택할 수 있도록 한다.
                // Get all the mms_idx values to make them optionf for selection.
                $sql2 = "SELECT mms_idx, mms_name
                        FROM {$g5['mms_table']}
                        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
                        ORDER BY mms_idx       
                ";
                // echo $sql2.'<br>';
                $result2 = sql_query($sql2,1);
                for ($i=0; $row2=sql_fetch_array($result2); $i++) {
                    // print_r2($row2);
                    echo '<option value="'.$row2['mms_idx'].'" '.get_selected(${$pre}['mms_idx'], $row2['mms_idx']).'>'.$row2['mms_name'].'</option>';
                }
                ?>
            </select>
		</td>
    </tr>
	<tr>
        <?php
        $ar['id'] = 'shf_start_time';
        $ar['name'] = '교대시작시간';
        $ar['type'] = 'input';
        $ar['width'] = '70px';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = 'HH:MM:SS';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <th scope="row">교대종료시간</th>
        <td>
            <input type="text" name="shf_end_time" value="<?php echo ${$pre}['shf_end_time'] ?>" required class="frm_input required" style="width:70px;">
            &nbsp;
            <label><input type="checkbox" name="shf_end_prevday" <?=(${$pre}['shf_end_prevday'])?'checked':''?> value="<?=(${$pre}['shf_end_prevday'])?'1':''?>" id="shf_end_prevday"> 마지막시간은 작일(昨日) 여부</label>
            <script>
            $(document).on('click','#shf_end_prevday',function(e){
                if($(this).is(':checked')) {$(this).val(1);}
                else {$(this).val(0);}
            });
            </script>
        </td>
    </tr>
	<tr>
        <th scope="row">적용기간</th>
        <td colspan="3">
            <?=help('<span style="color:red;">기간선택</span>: 설정 기간동안만 교대 시간 적용<br><span style="color:red;">전체기간</span>: 해당 설비에 대하여 전체 기간 동안 매일 적용됩니다.')?>
            <label for="shf_period_type_0"><input type="radio" name="shf_period_type" id="shf_period_type_0" value="0" <?=$shf_period_type_0?>> 기간선택</label>
            <label for="shf_period_type_1"><input type="radio" name="shf_period_type" id="shf_period_type_1" value="1" <?=$shf_period_type_1?>> 전체기간</label>
            <div class="div_shf_period" style="display:<?=(${$pre}['shf_period_type'])?'none':''?>">
                <input type="text" name="shf_start_dt" value="<?=${$pre}['shf_start_dt']?>"
                    class="frm_input" style="width:133px;">
                <span class="span_wave">~</span>
                <input type="text" name="shf_end_dt" value="<?=${$pre}['shf_end_dt']?>"
                    class="frm_input" style="width:133px;">
            </div>
            <script>
            // 기간선택, 전체기간
            $(document).on('click','input[name=shf_period_type]',function(e){
                // 기간선택
                if( $(this).val() == '0' ) {
                    $('.div_shf_period').show();
                }
                // 전체기간
                else {
                    $('.div_shf_period').hide();
                }
            });
            </script>
        </td>
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
	<tr style="display:<?=(!$member['mb_manager_yn'])?'none':''?>;">
		<th scope="row"><label for="com_status">상태</label></th>
		<td colspan="3">
			<?php echo help("상태값은 관리자만 수정할 수 있습니다."); ?>
			<select name="<?=$pre?>_status" id="<?=$pre?>_status"
				<?php if (auth_check($auth[$sub_menu],"d",1)) { ?>onFocus='this.initialSelect=this.selectedIndex;' onChange='this.selectedIndex=this.initialSelect;'<?php } ?>>
				<?=$g5['set_status_options']?>
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
$(function() {

    $(document).on('click','.btn_item_target',function(e){
        var shf_idx = $(this).attr('shf_idx');
        var shf_no = $(this).attr('shf_no');
        // alert( shf_idx +'/'+ shf_no );
		var url = "./shift_item_goal_list.popup.php?file_name=<?=$g5['file_name']?>&shf_idx="+shf_idx+"&shf_no="+shf_no;
		win_item_goal = window.open(url, "win_item_goal", "left=300,top=150,width=550,height=600,scrollbars=1");
        win_item_goal.focus();
    });

    $("input[name$=_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99" });

    // 가격 입력 쉼표 처리
	$(document).on( 'keyup','input[name$=_price]',function(e) {
//        console.log( $(this).val() )
//		console.log( $(this).val().replace(/,/g,'') );
        if(!isNaN($(this).val().replace(/,/g,'')))
            $(this).val( thousand_comma( $(this).val().replace(/,/g,'') ) );
	});

});

function form01_submit(f) {
    // 교대시간 체크

    return true;
}

</script>

<?php
include_once ('./_tail.php');
?>
