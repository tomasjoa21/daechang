<?php
$sub_menu = "930120";
include_once('./_common.php');

auth_check($auth[$sub_menu],'w');

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'maintain';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_form/","",$g5['file_name']); // _form을 제외한 파일명
$qstr = "&st_date=$st_date&st_time=$st_time&en_date=$en_date&en_time=$en_time&ser_mms_idx=$ser_mms_idx";

if ($w == '') {
    $sound_only = '<strong class="sound_only">필수</strong>';
    $w_display_none = ';display:none';  // 쓰기에서 숨김
    
    ${$pre}['trm_idx_maintain'] = 0;
    ${$pre}['mnt_date'] = G5_TIME_YMD;
    ${$pre}['mnt_people'] = 1;
    ${$pre}['mnt_start_dt'] = G5_TIME_YMDHIS;
    ${$pre}['mnt_end_dt'] = date("Y-m-d H:i:s",G5_SERVER_TIME+3600);
    ${$pre}['mnt_time_hh'] = 0;
    ${$pre}['mnt_time_mm'] = 0;
    ${$pre}['mb_id'] = $member['mb_id'];
    ${$pre}[$pre.'_status'] = 'ok';
    // 앞에서 넘어온 값이 있는 경우 표현이 필요함
    ${$pre}['mms_idx'] = $_REQUEST['mms_idx'];
    $sql = "SELECT cod_idx, cod_name FROM {$g5['code_table']}
            WHERE com_idx = '".$_SESSION['ss_com_idx']."'
                AND mms_idx = '".$_REQUEST['mms_idx']."'
                AND cod_code = '".$_REQUEST['arm_code']."'
    ";
    $one = sql_fetch($sql,1);
    // print_r3($one);
    ${$pre}['mnt_db_idx'] = $one['cod_idx'];
    ${$pre}['mnt_subject'] = $one['cod_name'];

}
else if ($w == 'u') {
    $u_display_none = ';display:none;';  // 수정에서 숨김

	${$pre} = get_table_meta($table_name, $pre.'_idx', ${$pre."_idx"});
    if (!${$pre}[$pre.'_idx'])
		alert('존재하지 않는 자료입니다.');
	$com = get_table_meta('company','com_idx',${$pre}['com_idx']);
    $imp = get_table_meta('imp','imp_idx',${$pre}['imp_idx']);
    $mms = get_table_meta('mms','mms_idx',${$pre}['mms_idx']);
    // print_r3(${$pre});
    // print_r3($imp);
    // 시간 분리
    ${$pre}['mnt_time_hhmm'] = second_to_hhmm(${$pre}['mnt_minute']);
    ${$pre}['mnt_time_hhmm_array'] = explode(":",${$pre}['mnt_time_hhmm']);
    ${$pre}['mnt_time_hh'] = (int)${$pre}['mnt_time_hhmm_array'][0];
    ${$pre}['mnt_time_mm'] = (int)${$pre}['mnt_time_hhmm_array'][1];
}
else
    alert('제대로 된 값이 넘어오지 않았습니다.');

// 라디오&체크박스 선택상태 자동 설정 (필드명 배열 선언!)
$check_array=array('mb_field');
for ($i=0;$i<sizeof($check_array);$i++) {
	${$check_array[$i].'_'.${$pre}[$check_array[$i]]} = ' checked';
}

$html_title = ($w=='')?'추가':'수정'; 
$g5['title'] = '정비조치 '.$html_title;
//include_once('./_top_menu_data.php');
include_once ('./_head.php');
//echo $g5['container_sub_title'];
?>
<style>
    .towhom_wrapper {
        border: solid 1px #ddd;
        background: #f5f5f5;
        padding: 10px;
    }
    .towhom_form {
        margin-top: 1px;
    }
    .towhom_info {
        min-height: 80px;
        border: solid 1px #ddd;
        background: #fff;
        padding: 10px;
        margin-top: 5px;
    }
    .set_send_type {
        margin-right: 5px;
    }
    .set_send_type input {
        margin-right: 4px;
    }
    label[disabled] {
        color: #ddd;
    }
    .td_range_interval > div {
        float: left;
    }
    .td_range_interval .range_graph input {
        position: absolute;
        right: -52px;
        width: 50px;
    }
    .td_range_interval .range_intraval > div {
        position: absolute;
        left: 0;
        display: inline-block;
        width: 260px;
    }
</style>

<form
    name="form01"
    id="form01"
    action="./<?=$g5['file_name']?>_update.php"
    onsubmit="return form01_submit(this);"
    method="post"
    enctype="multipart/form-data">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="token" value="">
    <input type="hidden" name="<?=$pre?>_idx" value="<?php echo ${$pre."_idx"} ?>">
    <input type="hidden" name="com_idx" value="<?php echo $_SESSION['ss_com_idx'] ?>">
    <input type="hidden" name="ser_mms_idx" value="<?php echo $ser_mms_idx ?>">
    <input type="hidden" name="st_date" value="<?php echo $st_date ?>">
    <input type="hidden" name="en_date" value="<?php echo $en_date ?>">

    <div class="local_desc01 local_desc" style="display:no ne;">
        <p>장비 고장을 예측하고 예방하기 위한 소중한 정보입니다. 자세히 내용을 확인하고 입력해 주시기 바랍니다.</p>
        <p>설비를 선택하면 설비의 비가동에 영향을 주는 알람 목록이 발생 빈도 순으로 선택박스에 나타납니다. <b>관련 알람을 선택</b>하시고 정보를 입력해 주시기 바랍니다.</p>
        <p>[전체][한달][1주일][오늘] 버튼을 클릭하여 특정 알람 발생 기간을 선택하여 조회할 수 있습니다. (디폴트는 전체기간입니다.)</p>
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
                    <th scope="row">설비선택</th>
                    <td colspan="3">
                        <select name="mms_idx" id="mms_idx">
                            <option value="">설비선택</option>
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
                        <script>
                            $('select[name=mms_idx]')
                                .val("<?=${$pre}['mms_idx']?>")
                                .attr('selected', 'selected');
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">알람선택</th>
                    <td colspan="3">
                        <i class="fa fa-spinner fa-spin fa-fw" style="display:none;" id="mnt_db_idx_spinner"></i>
                        <!-- 설비선택에 따른 알람select change -->
                        <input type="hidden" name="<?=$pre?>_db_table" value="code">
                        <!-- 일단은 코드별에서만..(태그별도 해야 됨) -->
                        <select name="mnt_db_idx" id="mnt_db_idx" mnt_db_idx="<?=${$pre}['mnt_db_idx']?>" style="margin-bottom:5px;">
                            <option value="">관련 알람을 선택하세요.</option>
                        </select>
                        <br>
                        <a href="javascript:" class="btn btn_02 btn_period" period="">전체</a>
                        <a href="javascript:" class="btn btn_02 btn_period" period="month">한달</a>
                        <a href="javascript:" class="btn btn_02 btn_period" period="week">1주일</a>
                        <a href="javascript:" class="btn btn_02 btn_period" period="today">오늘</a>

                    </td>
                    <script>
                        // 설비 선택 시 관련알람 추출
                        $(document).on('change', '#mms_idx', function (e) {
                            mmt_db_idx_select($(this).val(),'');
                        });
                        // 기간 선택 시 관련알람 추출
                        $(document).on('click', '.btn_period', function (e) {
                            var mms_idx = $('#mms_idx').val();
                            var ser_period = $(this).attr('period');
                            mmt_db_idx_select(mms_idx,ser_period);
                        });
                        // 설비 선택 시 관련알람 추출 함수
                        function mmt_db_idx_select(mms_idx, ser_period) {
                            if (mms_idx) {

                                // 진행중 표시 아이콘
                                $('#mnt_db_idx_spinner').show();
                                $('#mnt_db_idx').hide();

                                var mnt_db_idx = $('#mnt_db_idx').attr('mnt_db_idx');
                                $.ajax({
                                    url:'<?=G5_USER_ADMIN_AJAX_URL?>/mms_alarm_select.php',
                                    data:{'mms_idx': mms_idx, 'ser_period': ser_period},
                                    dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
                                        // console.log(res);
                                        //var prop1; for(prop1 in res.rows) { console.log( prop1 +': '+ res.rows[prop1] ); }
                                        $('#mnt_db_idx').children('option:not(:first)').remove(); // 항목 초기화
                                        $.each(res.rows, function (i, v) {
                                            // console.log(i+':'+v);
                                            $('#mnt_db_idx').append(
                                                "<option value='" + v['cod_idx'] + "' cod_name='"+v['cod_name']+"' cod_trm_name='"+v['cod_trm_name']+"' cod_memo='"+v['cod_memo']+"'>" + v['cod_name'] +" (발생횟수 "+ v['cnt'] + ")</option>"
                                                // "<option value='" + v['cod_idx'] + "' cod_name='"+v['cod_name']+"' cod_memo='"+v['cod_memo']+"'>" + v['cod_name'] +' - '+ v['cod_trm_name'] +" (발생횟수 "+ v['cnt'] + ")</option>"
                                                // "<option value='" + v['cod_idx'] + "' cod_name='"+v['cod_name']+"'>" + v['cod_name'] +")</option>"
                                            );
                                        });
                                        $('#mnt_db_idx').append(
                                            "<option value='0'>기타</option>"
                                        );
                                        // 기존값이 있었다면 선택상태로 설정
                                        if( $('#mnt_db_idx').attr('mnt_db_idx') !='' ) {
                                            $('#mnt_db_idx').val( $('#mnt_db_idx').attr('mnt_db_idx') );
                                            $('#code_trm_name').text( $('#mnt_db_idx option:selected').attr('cod_trm_name') );
                                        }

                                        // 진행중 표시 아이콘 숨김
                                        $('#mnt_db_idx_spinner').hide();
                                        $('#mnt_db_idx').show();

                                    },
                                    error:function(xmlRequest) {
                                        alert('Status: ' + xmlRequest.status + ' \n\rstatusText: ' + xmlRequest.statusText 
                                            + ' \n\rresponseText: ' + xmlRequest.responseText);
                                    } 
                                }); 
                            }
                            else {
                                $('#mnt_db_idx').children('option:not(:first)').remove(); // 항목 초기화
                            }
                        };
                        <?php
                        // 설비선택이 있는 경우 함수 호출
                        if($mnt['mms_idx']) {
                            echo "mmt_db_idx_select(".$mnt['mms_idx'].",'');";
                        }
                        ?>

                        // 알람 선택 시 제목수정
                        $(document).on('change', '#mnt_db_idx', function (e) {
                            // console.log( $('#mnt_db_idx option:selected').attr('cod_name') );
                            if( $('#mnt_db_idx option:selected').attr('cod_name') != 'null' ) {
                                $('#mnt_subject').val( $('#mnt_db_idx option:selected').attr('cod_name') );
                            }
                            if( $('#mnt_db_idx option:selected').attr('cod_trm_name') != '' ) {
                                $('#code_trm_name').text( $('#mnt_db_idx option:selected').attr('cod_trm_name') );
                            }
                            if( $('input[name=w]').val() == '' ) {
                                $('#mnt_content').text( decodeURIComponent($('#mnt_db_idx option:selected').attr('cod_memo').replace(/\+/g, '%20')) );
                            }
                        });
                    </script>
                </tr>
                <tr>
                    <th scope="row">알람분류</th>
                    <td colspan="3">
                        <span id="code_trm_name"></span>
                    </td>
                </tr>
                <tr>
                    <th scope="row">조치분류</th>
                    <td colspan="3">
                        <select name="trm_idx_maintain" id="trm_idx_maintain">
                            <option value="0">분류를 선택하세요.</option>
                            <?=$maintain_select_options?>
                        </select>
                        <script>
                            $('select[name="trm_idx_maintain"]').val('<?=${$pre}['trm_idx_maintain']?>');
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">제목</th>
                    <td colspan="3">
                        <input
                            type="text"
                            name="mnt_subject"
                            value="<?php echo ${$pre}['mnt_subject'] ?>"
                            id="mnt_subject"
                            class="frm_input required"
                            required="required"
                            style="width:80%;">
                    </td>
                </tr>
                <tr>
                    <th scope="row">날짜/담당자</th>
                    <td colspan="3">
                        <input type="text" name="mnt_date" value="<?=${$pre}['mnt_date']?>" id="mnt_date" required="required" class="frm_input required" style="width:90px;" placeholder="정비일">
                        &nbsp;&nbsp;&nbsp; 담당자:
                        <input type="hidden" name="mb_id" value="<?=${$pre}['mb_id']?>" id="mb_id" class="frm_input">
                        <input type="text" name="mnt_name" value="<?=${$pre}['mnt_name']?>" id="mnt_name" class="frm_input" style="width:100px;">
                        <a href="./member_select.php?file_name=<?=$g5['file_name']?>&item=mb_id_employee" class="btn btn_02" id="btn_member">검색</a>
                        &nbsp;&nbsp;&nbsp; 정비인원:
                        <select name="mnt_people" id="mnt_people" class="required" required>
                            <?php
                            for ($i=1; $i<11; $i++) {
                                echo '<option value="'.$i.'" '.get_selected(${$pre}['mnt_people'], $i).'>'.$i.' 명</option>';
                            }
                            ?>
                        </select>
                        <script>
                            $('select[name=mnt_people]')
                                .val("<?=${$pre}['mnt_people']?>")
                                .attr('selected', 'selected');
                        </script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">정비시간</th>
                    <td colspan="3">
                        <input type="text" name="mnt_start_dt" value="<?=(check_date(${$pre}['mnt_start_dt']))?${$pre}['mnt_start_dt']:''?>" id="mnt_start_dt" class="frm_input" style="width:140px;">
                        ~
                        <input type="text" name="mnt_end_dt" value="<?=(check_date(${$pre}['mnt_end_dt']))?${$pre}['mnt_end_dt']:''?>" id="mnt_end_dt" class="frm_input" style="width:140px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mnt_content">조치내용</label>
                    </th>
                    <td colspan="3">
                        <textarea name="mnt_content" id="mnt_content" style="height:100px;"><?php echo ${$pre}['mnt_content'] ?></textarea>
                    </td>
                </tr>
                <tr style="display:<?=(!$member['mb_manager_yn'])?'none':''?>;">
                    <th scope="row">
                        <label for="mnt_status">상태</label>
                    </th>
                    <td colspan="3">
                        <?php echo help("상태값은 관리자만 수정할 수 있습니다."); ?>
                        <select
                            name="<?=$pre?>_status"
                            id="<?=$pre?>_status"
                            <?php if (auth_check($auth[$sub_menu],"d",1)) { ?>
                            onchange='this.selectedIndex=this.initialSelect;'
                            <?php } ?>onFocus='this.initialSelect=this.selectedIndex;' >
                            <?=$g5['set_status_options']?>
                        </select>
                        <script>
                            $('select[name="<?=$pre?>_status"]').val('<?=${$pre}[$pre.'_status']?>');
                        </script>
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
$(function () {
    // 회원검색
    $(document).on('click','#btn_member',function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        memberfindwin = window.open(href,"memberfindwin","left=100,top=100,width=520,height=600,scrollbars=1");
        memberfindwin.focus();
    });

    $("input[name$=_date]").datepicker(
        {changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99"}
    );
});

function form01_submit(f) {
    
    if(f.mnt_db_idx.value=='') {
        alert('관련 알람을 선택해 주세요.');
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>