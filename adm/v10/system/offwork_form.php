<?php
$sub_menu = "925900";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'offwork';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr .= '&ser_mms_idx='.$ser_mms_idx; // 추가로 확장해서 넘겨야 할 변수들

$html_title = ($w=='')?'추가':'수정'; 
$html_title = ($w=='c')?'복제':$html_title; 
$g5['title'] = '공제시간 '.$html_title;
include_once('./_top_menu_setting.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// print_r3($member);
// print_r3($_SESSION);

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
    
    ${$pre}['com_idx'] = $_SESSION['ss_com_idx'];
    ${$pre}['mms_idx'] = 0;
    ${$pre}[$pre.'_period_type'] = 0;
    // ${$pre}['mms_idx'] = rand(1,4);
    ${$pre}[$pre.'_start_time'] = '09:00:00';
    ${$pre}[$pre.'_end_time'] = '10:00:00';
    ${$pre}[$pre.'_start_dt'] = date("Y-m-d H:00:00");
    ${$pre}[$pre.'_end_dt'] = '9999-12-31 23:59:59';
    ${$pre}[$pre.'_status'] = 'ok';
}
else if ($w == 'u' || $w == 'c') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

	${$pre} = get_table($table_name, $pre.'_idx', ${$pre."_idx"});
    // print_r2(${$pre});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
	$com = get_table_meta('company','com_idx',${$pre}['com_idx']);
    $mms = get_table_meta('mms','mms_idx',${$pre}['mms_idx']);

}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

// 적용기간 선택
${$pre.'_period_type_'.${$pre}[$pre.'_period_type']} = ' checked';


// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_gender');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}
?>
<style>
.frm_date {width:75px;}
</style>

<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="com_idx" value="<?=$_SESSION['ss_com_idx']?>">
<input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
<input type="hidden" name="ser_mms_idx" value="<?php echo $ser_mms_idx ?>">

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>설비별로 따로 공제시간을 설정할 경우 설비를 선택하세요. 모든설비를 선택하면 모든 설비에 대해 공통 적용됩니다.</p>
    <p>밤 12시(자정) 경계를 넘어서는 시간대는 자정을 기준으로 분리해서 등록해 주세요. 24시전(~23:59:59)과 00시후(00:00:00~)로 분리하시면 됩니다.</p>
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
        $ar['id'] = 'off_name';
        $ar['name'] = '공제시간명칭';
        $ar['type'] = 'input';
        $ar['width'] = '100%';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = 'ex)점심시간, 휴식시간';
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
        $ar['id'] = $pre.'_start_time';
        $ar['name'] = '시작시간';
        $ar['type'] = 'input';
        $ar['width'] = '70px';
        $ar['unit'] = '24시간제로 입력 ex) 11:30:00';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = 'HH:MM:SS';
        echo create_td_input($ar);
        unset($ar);
        ?>
        <?php
        $ar['id'] = $pre.'_end_time';
        $ar['name'] = '종료시간';
        $ar['type'] = 'input';
        $ar['width'] = '70px';
        $ar['unit'] = 'ex) 19:30:00';
        $ar['value'] = ${$pre}[$ar['id']];
        $ar['required'] = 'required';
        $ar['placeholder'] = 'HH:MM:SS';
        echo create_td_input($ar);
        unset($ar);
        ?>
    </tr>
	<tr>
        <th scope="row">적용기간</th>
        <td colspan="3">
            <input type="text" name="off_start_dt" value="<?=${$pre}['off_start_dt']?>" class="frm_input" style="width:146px;">
            <span class="span_wave">~</span>
            <input type="text" name="off_end_dt" value="<?=${$pre}['off_end_dt']?>" class="frm_input" style="width:146px;">
        </td>
    </tr>
    <?php
    $ar['id'] = 'off_memo';
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
    <a href="./<?=$fname?>_list.php?<?php echo $qstr ?>" class="btn btn_b01">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>

<script>
$(function() {
    // 설비선택, 전체설비
    $(document).on('click','input[name=mms_idx_radio]',function(e){
        if( $(this).val() == '0' ) {
            $('input[name=mms_idx]').attr('old_value',$('input[name=mms_idx]').val()).val('0').attr('type','hidden');
        }
        else {
            $('input[name=mms_idx]').val($('input[name=mms_idx]').attr('old_value')).attr('type','text').select().focus();
        }
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
