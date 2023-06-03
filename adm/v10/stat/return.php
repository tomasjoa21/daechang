<?php
$sub_menu = "935110";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

// 변수 설정, 필드 구조 및 prefix 추출
$table_name = 'return';
$g5_table_name = $g5[$table_name.'_table'];
$fields = sql_field_names($g5_table_name);
$pre = substr($fields[0],0,strpos($fields[0],'_'));
$fname = preg_replace("/_list/","",$g5['file_name']); // _list을 제외한 파일명
$qstr .= "&st_date=$st_date&st_time=$st_time&en_date=$en_date&en_time=$en_time";

$g5['title'] = '반품율보고서';
include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


//-- 기본 검색값 할당
$st_date = $st_date ?: date("Y-01-01",G5_SERVER_TIME);
$en_date = $en_date ?: date("Y-m-31",G5_SERVER_TIME);


$where = array();
$where[] = " (1) ";   // 디폴트 검색조건

// com_idx 조건
// $where[] = " ".$pre.".com_idx IN (".$_SESSION['ss_com_idx'].") ";

// 기간 검색
if ($st_date) {
    $where[] = " ret_ym >= '".$st_date."' ";
}
if ($en_date) {
    $where[] = " ret_ym <= '".$en_date."' ";
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


$sql = "SELECT ret_type, mo01, mo02, mo03, mo04, mo05, mo06, mo07, mo08, mo09, mo10, mo11, mo12, flag
        FROM (
        SELECT ret_type
            , SUM(IF(SUBSTR(ret_ym,6,2)='01', ret_count, 0)) AS mo01
            , SUM(IF(SUBSTR(ret_ym,6,2)='02', ret_count, 0)) AS mo02
            , SUM(IF(SUBSTR(ret_ym,6,2)='03', ret_count, 0)) AS mo03
            , SUM(IF(SUBSTR(ret_ym,6,2)='04', ret_count, 0)) AS mo04
            , SUM(IF(SUBSTR(ret_ym,6,2)='05', ret_count, 0)) AS mo05
            , SUM(IF(SUBSTR(ret_ym,6,2)='06', ret_count, 0)) AS mo06
            , SUM(IF(SUBSTR(ret_ym,6,2)='07', ret_count, 0)) AS mo07
            , SUM(IF(SUBSTR(ret_ym,6,2)='08', ret_count, 0)) AS mo08
            , SUM(IF(SUBSTR(ret_ym,6,2)='09', ret_count, 0)) AS mo09
            , SUM(IF(SUBSTR(ret_ym,6,2)='10', ret_count, 0)) AS mo10
            , SUM(IF(SUBSTR(ret_ym,6,2)='11', ret_count, 0)) AS mo11
            , SUM(IF(SUBSTR(ret_ym,6,2)='12', ret_count, 0)) AS mo12
            , (CASE ret_type
                WHEN '투입' THEN 1
                WHEN 'OK' THEN 2
                WHEN 'N02' THEN 3
                WHEN 'L/ARM' THEN 4
                WHEN 'SKID' THEN 5
                WHEN 'A/ARM' THEN 6
                WHEN 'B/MTG' THEN 7
                WHEN '기타' THEN 8
                WHEN '미가공' THEN 9
            ELSE 1000
            END) AS flag
        FROM {$g5['return_table']}
            {$sql_search}
        GROUP BY ret_type
        ) AS db1
        ORDER BY flag
";
// echo $sql.'<br>';
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>
<style>
.tr_status_투입 td {background-color:#211d04;}
.tr_status_ng_rate td {background-color:#442805;}
.tbl_head01 tbody tr.tr_status_ng_count td {background-color:#211d04;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
<label for="sfl" class="sound_only">검색대상</label>
<input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;">
~
<input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">

<input type="submit" class="btn_submit" value="검색">
</form>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>납품 제품에 대해서 고객사 가공 후 확인된 결함 정보를 월별로 표시합니다.</p>
    <p>엑셀을 등록하실 때는 표준 형식에 맞추어야 합니다. 엑셀 형식이 바뀌면 등록 시 에러가 발생할 수 있습니다. <a href="https://docs.google.com/spreadsheets/d/1CjUaoQWKcJQmwhCcu1WJB8MYhCMWo9MGT_VjakBnX0U/edit?usp=sharing" target="_blank">[엑셀샘플보기]</a></p>
</div>

<!-- 리스트 테이블 -->
<div class="tbl_head01 tbl_wrap">
	<table>
	<thead>
	<tr>
		<th scope="col">항목명</th>
        <?php
        for ($j=1; $j<13; $j++) {
            echo '<th scope="col" style="width:80px;">'.$j.'월</th>';
        }
        ?>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {
		//print_r2($row);

		// 월별 항목 td
        for ($j=1; $j<13; $j++) {
            $row['months'] .= '<td style="text-align:right;">'.number_format($row['mo'.sprintf("%02d",$j)]).'</td>';
            // 불량수 불량율 계산
            if(!in_array($row['ret_type'],array("투입","OK"))) {
                ${'ng_count'.$j} += $row['mo'.sprintf("%02d",$j)];
            }
            // 월별 투입수 (첫줄에서 합계 변수 할당)
            if($row['ret_type']=='투입') {
                ${'output_total'.$j} = $row['mo'.sprintf("%02d",$j)];
            }
        }
        
        // 항목명
		$row['item_name'] = ($g5['set_return_item_value2'][$row['ret_type']]) ?
                            $g5['set_return_item_value2'][$row['ret_type']]
                            : $row['ret_type'];
		
		echo '
			<tr tr_id="'.$i.'" class="tr_status_'.$row['ret_type'].'">
				<td style="text-align:left;">'.$row['item_name'].'</td>
                '.$row['months'].'
			</tr>
			';
	}
    // 불량수, 불량율 표시
    for ($j=1; $j<13; $j++) {
        // echo ${'ng_count'.$j}.'<br>';
        // echo ${'output_total'.$j}.'<br>';
        ${'ng_rate'.$j} = ${'output_total'.$j} ? 
                            round(${'ng_count'.$j}/${'output_total'.$j}*100,1)
                            : 0;
        $td_ng_count .= '<td style="text-align:right;">'.number_format(${'ng_count'.$j}).'</td>';
        $td_ng_rate .= '<td style="text-align:right;">'.${'ng_rate'.$j}.'<span style="font-size:0.6em;">%</span></td>';
    }
    echo '
        <tr tr_id="'.($i+1).'" class="tr_status_ng_count">
            <td style="text-align:left;">보은 불량수</td>
            '.$td_ng_count.'
        </tr>
    ';
    echo '
        <tr tr_id="'.($i+2).'" class="tr_status_ng_rate">
            <td style="text-align:left;">보은 불량율</td>
            '.$td_ng_rate.'
        </tr>
    ';
	if ($i == 0)
		echo '<tr class="no-data"><td colspan="8" class="text-center">등록(검색)된 자료가 없습니다.</td></tr>';
	?>
    </tbody>
    </table>
</div>
<!-- //리스트 테이블 -->

<div class="btn_fixed_top">
    <a href="javascript:alert('작업중입니다.')" id="btn_excel_upload" class="btn btn_01" style="margin-right:0px;">엑셀등록</a>
    <?php if(!auth_check($auth[$sub_menu],"d",1)) { ?>
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn" style="display:none;">
    <a href="./<?=$fname?>_form.php" id="btn_add" class="btn btn_01" style="display:none;">추가하기</a>
    <?php } ?>
</div>

<div id="modal01" title="엑셀 파일 업로드" style="display:none;">
    <form name="form02" id="form02" action="./<?=$fname?>_excel_upload.php" onsubmit="return form02_submit(this);" method="post" enctype="multipart/form-data">
        <table>
        <tbody>
        <tr>
            <td style="line-height:130%;padding:10px 0;">
                <ol>
                    <li>엑셀은 97-2003통합문서만 등록가능합니다. (*.xls파일로 저장)</li>
                    <li>엑셀은 하단에 탭으로 여러개 있으면 등록 안 됩니다. (한개의 독립 문서이어야 합니다.)</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <input type="file" name="file_excel" onfocus="this.blur()">
            </td>
        </tr>
        <tr>
            <td style="padding:15px 0;">
                <button type="submit" class="btn btn_01">확인</button>
            </td>
        </tr>
        </tbody>
        </table>
    </form>
</div>

<script>
$(function(e) {
    // st_date chage
    $(document).on('focusin', 'input[name=st_date]', function(){
        // console.log("Saving value: " + $(this).val());
        $(this).data('val', $(this).val());
    }).on('change','input[name=st_date]', function(){
        var prev = $(this).data('val');
        var current = $(this).val();
        // console.log("Prev value: " + prev);
        // console.log("New value: " + current);
        if(prev=='') {
            $('input[name=st_time]').val('00:00:00');
        }
    });
    // en_date chage
    $(document).on('focusin', 'input[name=en_date]', function(){
        $(this).data('val', $(this).val());
    }).on('change','input[name=en_date]', function(){
        var prev = $(this).data('val');
        if(prev=='') {
            $('input[name=en_time]').val('23:59:59');
        }
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

    // 엑셀등록 버튼
    $( "#btn_excel_upload" ).on( "click", function(e) {
        e.preventDefault();
        $( "#modal01" ).dialog( "open" );
    });
    $( "#modal01" ).dialog({
        autoOpen: false
        , position: { my: "right-10 top-10", of: "#btn_excel_upload"}
    });


});

function form02_submit(f) {
    if (!f.file_excel.value) {
        alert('엑셀 파일(.xls)을 입력하세요.');
        return false;
    }
    else if (!f.file_excel.value.match(/\.xls$|\.xlsx$/i) && f.file_excel.value) {
        alert('엑셀 파일만 업로드 가능합니다.');
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
