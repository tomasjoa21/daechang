<?php
if (!defined('_GNUBOARD_')) exit;

// print_r2($_REQUEST);
// exit;

// 보고서별 기간 설정
$mode = $mode ?: 'week';
$st_ymd_offset = 0;
if($g5['file_name']=='kpi_maintain') {
    if(!$st_date) {
        $st_ymd_offset = ($mode == 'week') ? 86400*30 : 86400*365/3;
    }
}

// 당월, 당일
// $st_ymd = date("Y-m-01",G5_SERVER_TIME);
$st_ymd = date("Y-m-d",G5_SERVER_TIME-$st_ymd_offset);
$st_ym_first = date("Y-m-01",G5_SERVER_TIME);
$ym_days = date("t",G5_SERVER_TIME);
$en_ymd = date("Y-m-".$ym_days,G5_SERVER_TIME);
$today = date("Y-m-d",G5_SERVER_TIME);
$yesterday = date("Y-m-d",G5_SERVER_TIME-86400);
$tomorrow = date("Y-m-d",G5_SERVER_TIME+86400);
//echo $today.'<br>';

// st_date, en_date
switch ($g5['file_name']) {
    case ( $g5['file_name'] == 'output' ) :
        $st_date = $st_date ?: $st_ym_first;
        break;
    default :
        $st_date = $st_date ?: date($st_ymd);
        break;
}
$en_date = $en_date ?: date("Y-m-d");
$en_date2 = ($st_date==$en_date) ? '' : ' ~ '.$en_date; // wave(~) mark before en_date.

$member['com_idx'] = $_SESSION['ss_com_idx'] ?: $member['com_idx'];
$com = get_table_meta('company','com_idx',$member['com_idx']);
$com_idx = $com['com_idx'];
// print_r2($com);



$st_timestamp = strtotime($st_date.' 00:00:00');
$en_timestamp = strtotime($en_date.' 23:59:59');

// echo $mmg_idx.' ---- mmg_idx <br>';
// echo $mms_idx.' ---- mms_idx <br>';
// exit;

// breadcrumb 표현을 위해서 up_names 추출
$sql = "SELECT mmg.mmg_idx
            , CONCAT( REPEAT('   ', COUNT(parent.mmg_idx) - 1), mmg.mmg_name) AS name
            , (COUNT(parent.mmg_idx) - 1) AS depth
            , GROUP_CONCAT(cast(parent.mmg_idx as char) ORDER BY parent.mmg_left) AS up_idxs
            , GROUP_CONCAT(parent.mmg_name ORDER BY parent.mmg_left SEPARATOR '|') AS up_names
            , (CASE WHEN mmg.mmg_right - mmg.mmg_left = 1 THEN 1 ELSE 0 END ) AS leaf_node_yn
            , mmg.mmg_left
        FROM g5_1_mms_group AS mmg,
            g5_1_mms_group AS parent
        WHERE mmg.mmg_left BETWEEN parent.mmg_left AND parent.mmg_right
            AND mmg.com_idx = '".$_SESSION['ss_com_idx']."'
            AND parent.com_idx = '".$_SESSION['ss_com_idx']."'
            AND mmg.mmg_status NOT IN ('trash','delete') AND parent.mmg_status NOT IN ('trash','delete')
        GROUP BY mmg.mmg_idx
        ORDER BY mmg.mmg_left
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    $up_names[$row['mmg_idx']] = explode("|",$row['up_names']);
    $mmg_select .= '<option value="'.$row['mmg_idx'].'">'.preg_replace("/\|/"," > ",$row['up_names']).'</option>';
}
// echo $mmg_idx.'<br>';
// print_r2($up_names);

// 그룹 숨김처리
switch ($g5['file_name']) {
    case ( $g5['file_name'] == 'output' ) :
        $mmg_display = 'none';
        break;
    default :
        $mmg_display = 'block';
        break;
}


add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/function.date.js"></script>', 0);
add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js"></script>', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/jquery-nice-select-1.1.0/css/nice-select.css">', 0);
?>
<div class="stat_wrapper">
    <div class="title01">
        <?=$com['com_name']?>
        <span class="title_breadcrumb">
            <?php
            if($mmg_idx && is_array($up_names[$mmg_idx])) {
                // print_r2($up_names[$mmg_idx]);
                for($i=0;$i<sizeof($up_names[$mmg_idx]);$i++) {
                    echo ' > '.$up_names[$mmg_idx][$i];
                }
                if($mms_idx) {
                    $mms2 = get_table('mms','mms_idx',$mms_idx);
                    echo ' > '.$mms2['mms_name'];
                }
            }
            ?>
        </span><!-- > 제1공장 > 1라인 -->
        <span class="text01 title_date"><?=$st_date?><?=$en_date2?></span>
    </div>
    <!-- selections -->
    <form id="form01" name="form01" class="form01" onsubmit="return sch_submit(this);" method="get">
        <input type="hidden" name="com_idx" value="<?=$com['com_idx']?>" class="frm_input">
        <input type="hidden" name="mode" value="<?=$mode?>" class="frm_input">
        <input type="text" name="st_date" id="st_date" value="<?=$st_date?>" class="frm_input">
        <span class="text01">~</span>
        <input type="text" name="en_date" id="en_date" value="<?=$en_date?>" class="frm_input">
        <div class="text02 prev_month"><i class="fa fa-chevron-left"></i></div>
        <div class="text02 this_month" s_ymd="<?=$st_ym_first?>" e_ymd="<?=$en_ymd?>">이번달</div>
        <div class="text02 next_month"><i class="fa fa-chevron-right"></i></div>
        <div class="text02 prev_day"><i class="fa fa-chevron-left"></i></div>
        <div class="text02 this_day" s_ymd="<?=$today?>" e_ymd="<?=$today?>">오늘</div>
        <div class="text02 next_day"><i class="fa fa-chevron-right"></i></div>
        <div>
            <div style="display:inline-block;">
            <select name="mmg_idx" id="mmg_idx" style="display:<?=$mmg_display?>">
                <option value="">전체</option>
                <?=$mmg_select?>
            </select>
            </div>
            <div style="display:none;"><!-- inline-block -->
            <select name="mms_idx" id="mms_idx" mms_idx="<?=$mms_idx?>">
                <option value="">전체</option>
                <?=$mms_select?>
            </select>
            </div>
        </div>
        <input type="submit" class="btn_submit" value="확인">
    </form>
    <script>
    // 공장선택
    $('#mmg_idx').val('<?=$mmg_idx?>').attr('selected','selected');
    // 설비선택
    $('#mms_idx').val('<?=$mms_idx?>').attr('selected','selected');

    $(function(e){
        $('#mmg_idx, #mms_idx').niceSelect();
    });
    function sch_submit(f){
        
        if(f.st_date.value && f.en_date.value){
            var st_d = new Date(f.st_date.value);
            var en_d = new Date(f.en_date.value);
            if(st_d.getTime() > en_d.getTime()){
                alert('검색날짜의 최종날짜를 시작날짜보다 과거날짜를 입력 할 수는 없습니다.');
                return false;
            }
        }
        
        return true;
    }
    </script>
</div><!-- .stat_wrapper -->

<script>
$(function(e){
	// group select change
	$(document).on('change','select[name^=mmg]',function(e) {
		// console.log( $(this).attr('id') );
		var mmg_depth = $(this).attr('id').replace('mmg','');
		var mmg_idx = $(this).val();
		// console.log( 'select tag count: '+$('select[name^=mmg]').length );
		var mmg_select_count = $('select[name^=mmg]').length;

		// 일단 제거한 후 mms_idx가 있으면 보임
		$('#mms_idx').closest('div').hide();

		// 선택항목이 있는 경우만
		if(mmg_idx) {
            group_loading(<?=$com_idx?>, mmg_idx);
		}

	});
	// default group loading.
    <?php if($mmg_idx) { ?>
	group_loading(<?=$com_idx?>, <?=$mmg_idx?>);
    <?php } ?>

	// prev Month
	$(document).on('click','.prev_month',function(e) {
		// console.log( $('#st_date').val() );
		this_day = $('#st_date').val();
		$('#st_date').val( getPrevMonthFirst( this_day ) );
		$('#en_date').val( getPrevMonthLast( this_day ) );
	});
	// next Month
	$(document).on('click','.next_month',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNextMonthFirst( this_day ) );
		$('#en_date').val( getNextMonthLast( this_day ) );
	});
	// prev Day
	$(document).on('click','.prev_day',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNthPrevDay( this_day, 1 ) );
		$('#en_date').val( getNthPrevDay( this_day, 1 ) );
	});
	// prev Day
	$(document).on('click','.next_day',function(e) {
		this_day = $('#st_date').val();
		$('#st_date').val( getNthNextDay( this_day, 1 ) );
		$('#en_date').val( getNthNextDay( this_day, 1 ) );
	});

	// this month, this day click
	$(document).on('click','div[s_ymd]',function(e) {
		$('#st_date').val( $(this).attr('s_ymd') );
		$('#en_date').val( $(this).attr('e_ymd') );
	});

    $("input[name$=_date]").datepicker({
        closeText: "닫기",
        currentText: "오늘",
        monthNames: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNamesMin:['일','월','화','수','목','금','토'],
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        //maxDate: "+0d"
    });

});

function group_loading(com_idx, mmg_idx) {

    // $('#mms_idx').val('').attr("selected","selected");
    $('#mms_idx option').each(function(i,v){
        if( $(v).val() != '' ) { 
            $(this).remove();
        }
    });

	// console.log(mmg_idx);
	//-- 디버깅 Ajax --//
	$.ajax({
		url:g5_user_admin_ajax_url+'/mms.list.php',
		data:{"aj":"grp","com_idx":com_idx,"mmg_idx":mmg_idx},
		dataType:'json', 
        timeout:15000, 
        beforeSend:function(){}, 
        success:function(res){
			// console.log(res);
			//var prop1; for(prop1 in res.rows) { console.log( prop1 +': '+ res.rows[prop1] ); }
			if(res.result == true) {
                if(res.list!=null) {
                    $.each(res.list, function(i,v){
                        $('<option value="'+ v['mms_idx'] +'">' + v['mms_name'] + '</option>').appendTo('#mms_idx');
                    });
                    // mms_idx 값이 있으면 선택상태로..
                    if($('#mms_idx').attr('mms_idx')!='') {
                        // console.log($('#mms_idx').attr('mms_idx'));
                        $('#mms_idx').val($('#mms_idx').attr('mms_idx')).attr('selected','selected');
                    }
                    $('#mms_idx').attr('mms_idx',''); // 두 번째 셀렉박스부터 초기화 상태로 보이게 설정
                    $('#mms_idx').closest('div').css("display","inline-block");
                    $('#mms_idx').niceSelect('update');
                }
			}
			else {
				console.log(res.msg);
			}
		},
		error:function(xmlRequest) {
			// console.log('<?=$row['mms_name']?>(<?=$row['mms_idx']?>): error');
			console.log('Status: ' + xmlRequest.status);
			console.log('statusText: ' + xmlRequest.statusText);
			console.log('responseText: ' + xmlRequest.responseText);
		}
	});

}
</script>