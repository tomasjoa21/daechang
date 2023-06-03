<?php
$sub_menu = "920120";
include_once('./_common.php');

$g5['title'] = '파라메터 분포';
include_once('./_top_menu_db.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


// 검색 조건
$st_time_ahead = 3600*1;  // 5hour ahead.

// Set the search period reset according to the last data input.
$sql = " SELECT * FROM g5_1_cast_shot_sub ORDER BY css_idx DESC LIMIT 1 ";
$one = sql_fetch($sql,1);
// print_r3($one);
$en_date = ($en_date) ? $en_date : substr($one['event_time'],0,10);
$en_time = ($en_time) ? $en_time : substr($one['event_time'],11);
$st_date = ($st_date) ? $st_date : date("Y-m-d",strtotime($en_date.' '.$en_time)-$st_time_ahead);
$st_time = ($st_time) ? $st_time : date("H:i:s",strtotime($en_date.' '.$en_time)-$st_time_ahead);
// 시작 ~ 종료 기간
$start_end = $en_date.' '.$en_time.' ~ '.$st_date.' '.$st_time;
// echo $en_date.' '.$en_time.'<br>';
// echo $st_date.' '.$st_time.'<br>';
// exit;

// Get mms_infos
// print_r2($g5['set_dicast_mms_idxs_array']);
$sql = "SELECT mms_idx, mms_name, mms_model
        FROM {$g5['mms_table']}
        WHERE com_idx = '".$_SESSION['ss_com_idx']."'
            AND mms_idx IN (".implode(",",$g5['set_dicast_mms_idxs_array']).")
        ORDER BY mms_idx
";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    $mms_name[$row['mms_idx']] = $row['mms_name'];
    $mms_model[$row['mms_idx']] = $row['mms_model'];
}

// 태그검색
if($ser_dta_type) {
    $sql_dta_type = " dta_type = '".$ser_dta_type."' ";
}
else {
    $sql_dta_type = " dta_type IN (1,8) ";
}

if($ser_mms_idx) {
    $g5['mms_idxs_array'] = array($ser_mms_idx);
}
else {
    $g5['mms_idxs_array'] = $g5['set_dicast_mms_idxs_array'];
}
// print_r2($g5['mms_idxs_array']);

// 주조기 설비 분포 범위 (max, min)
for($i=0;$i<sizeof($g5['mms_idxs_array']);$i++) {
    // echo $g5['mms_idxs_array'][$i].'<br>';
    $mms_idx[$i] = $g5['mms_idxs_array'][$i];
    // echo $mms_name[$mms_idx[$i]].' 설비명 ---------- <br>';
    $mms = get_table_meta('mms', 'mms_idx', $mms_idx[$i]);  // mms meta 값으로 태그명들이 쭉 들어가 있음
    // print_r2($mms);
    $sql = "SELECT dta_type, dta_no, MAX(dta_value), MIN(dta_value)
            FROM g5_1_data_measure_".$g5['mms_idxs_array'][$i]."
            WHERE {$sql_dta_type}
            AND dta_dt >= '".$st_date." ".$st_time."' AND dta_dt <= '".$en_date." ".$en_time."'
            GROUP BY dta_type, dta_no
            ORDER BY dta_type, dta_no ASC
    ";
    // echo $sql.'<br>';
    $rs = sql_query_pg($sql,1);
    for($j=0;$row=sql_fetch_array_pg($rs);$j++) {
        // print_r2($row);
        // 태그명
        $row['dta_type_no_name'] = $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']] ? 
                                        $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]
                                            : $g5['set_data_type_value'][$row['dta_type']].'-'.$row['dta_no'];
        // echo $row['dta_type_no_name'].'<br>';
        $row['dta_name'] = $row['dta_type_no_name'];

        // 자바스크랩트에 표현할 태그명
        $tags[$mms_idx[$i]][] = $row['dta_name'];

        // 자바스크랩트에 표현할 배열 생성
        $ranges[$mms_idx[$i]][$j][] = $row['dta_name'];
        $ranges[$mms_idx[$i]][$j][] = round($row['max'],2);
        $ranges[$mms_idx[$i]][$j][] = round($row['min'],2);

        // 하단 for 문장내부 최적값 추출을 위한 배열 생성
        $best_type_no[$mms_idx[$i]][$j]['dta_name'] = $row['dta_name'];
        $best_type_no[$mms_idx[$i]][$j]['dta_type'] = $row['dta_type'];
        $best_type_no[$mms_idx[$i]][$j]['dta_no'] = $row['dta_no'];
    }
}
// print_r2($tags);
// print_r2($ranges);
// echo json_encode($ranges[58],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
// print_r2($best_type_no);

// 최적값 추출
if(is_array($best_type_no)) {
    foreach($best_type_no as $k1=>$v1) {
        // echo $k1.$v1.'<br>';
        // echo $mms_name[$k1].' 설비 최적값들 ------------------------------------------ <br>';
        $mms = get_table_meta('mms', 'mms_idx', $k1);  // mms meta 값으로 태그명들이 쭉 들어가 있음
        for($i=0;$i<sizeof($v1);$i++) {
            // print_r2($v1[$i]);
            // echo $v1[$i]['dta_name'].'='.$v1[$i]['dta_type'].','.$v1[$i]['dta_no'].'<br>';
            $sql = "SELECT *
                    FROM {$g5['data_measure_best_table']}
                    WHERE mms_idx = '".$k1."'
                    ORDER BY dmb_reg_dt DESC
                    LIMIT 1
            ";
            // echo $sql.'<br>';
            $one = sql_fetch($sql,1);
            // print_r2($one);
            // 태그명
            $one['dta_type_no_name'] = $mms['dta_type_label-'.$one['dta_type'].'-'.$one['dta_no']] ? 
                                            $mms['dta_type_label-'.$one['dta_type'].'-'.$one['dta_no']]
                                                : $g5['set_data_type_value'][$one['dta_type']].'-'.$one['dta_no'];
            // echo $one['dta_type_no_name'].'<br>';
            $one['dta_name'] = $one['dta_type_no_name'];

            // 최적값 배열 생성
            $averages[$k1][$i][] = $one['dta_name'];
            $averages[$k1][$i][] = round($one['dta_value'],2);
        }
    }
}
// print_r2($averages);
// echo json_encode($averages[58],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);



add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
.graph_wrap > div {margin-bottom:20px;}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highcharts-more.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <select name="ser_mms_idx" id="ser_mms_idx">
        <option value="">설비전체</option>
        <?php
        if(is_array($g5['mms'])) {
            foreach ($g5['mms'] as $k1=>$v1 ) {
                // print_r2($g5['mms'][$k1]);
                if( $g5['mms'][$k1]['com_idx']==$_SESSION['ss_com_idx'] && in_array($k1,$g5['set_dicast_mms_idxs_array']) ) {
                    echo '<option value="'.$k1.'" '.get_selected($ser_mms_idx, $k1).'>'.$g5['mms'][$k1]['mms_name'].'</option>';
                }
            }
        }
        ?>
    </select>
    <script>$('select[name=ser_mms_idx]').val("<?=$ser_mms_idx?>").attr('selected','selected');</script>

    <select name="ser_dta_type" id="ser_dta_type">
        <option value="">태그전체</option>
        <option value="1" <?=get_selected($ser_dta_type, 1)?>>온도</option>
        <option value="8" <?=get_selected($ser_dta_type, 1)?>>압력</option>
    </select>
    <script>$('select[name=ser_dta_type]').val("<?=$ser_dta_type?>").attr('selected','selected');</script>

    <input type="text" name="st_date" value="<?=$st_date?>" id="st_date" required class="required frm_input" autocomplete="off" style="width:80px;" >
    <input type="text" name="st_time" value="<?=$st_time?>" id="st_time" required class="required frm_input" autocomplete="off" style="width:65px;">
    ~
    <input type="text" name="en_date" value="<?=$en_date?>" id="en_date" required class="required frm_input" autocomplete="off" style="width:80px;">
    <input type="text" name="en_time" value="<?=$en_time?>" id="en_time" required class="required frm_input" autocomplete="off" style="width:65px;">
    <button type="submit" class="btn btn_01 btn_search">확인</button>
</form>

<div class="local_desc01 local_desc" style="display:none;">
    <p>가운데 빨간색으로 표시된 그래프가 최적기준선입니다. 기간내 추출된 태그 항목들만 표시됩니다.</p>
</div>

<div id="graph_wrapper">
<div class="graph_wrap">

    <!-- 차트 -->
    <?php
    for($i=0;$i<sizeof($g5['mms_idxs_array']);$i++) {
        $mms_idx[$i] = $g5['mms_idxs_array'][$i];
        // echo $mms_name[$mms_idx[$i]].' 설비명 ---------- <br>';
        // if(!$tags[$mms_idx[$i]][0]) {continue;}
    ?>
    <div id="chart<?=$mms_idx[$i]?>" style="position:relative;width:100%; height:400px;">
        <div class="chart_empty">그래프가 존재하지 않습니다.</div>
    </div>
    <?php
    }
    ?>

</div><!-- .graph_wrap -->
</div><!-- #graph_wrapper -->

<div class="btn_fixed_top" style="display:none;">
    <a href="./parameters.php" class="btn_04 btn">개별그래프</a>
    <a href="./parameters.php" class="btn_04 btn">전체그래프</a>
</div>

<script>
<?php
// 각 설비별 루틴
for($i=0;$i<sizeof($g5['mms_idxs_array']);$i++) {
    $mms_idx[$i] = $g5['mms_idxs_array'][$i];
    // echo $mms_name[$mms_idx[$i]].' 설비명 ---------- <br>';
    // if(!$tags[$mms_idx[$i]][0]) {continue;}
    $tags[$i] = json_encode($tags[$mms_idx[$i]],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
    $ranges[$i] = json_encode($ranges[$mms_idx[$i]],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
    $averages[$i] = json_encode($averages[$mms_idx[$i]],JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK);
?>
var ranges<?=$mms_idx[$i]?> = <?=$ranges[$i]?>,
    averages<?=$mms_idx[$i]?> = <?=$averages[$i]?>;

Highcharts.chart('chart<?=$mms_idx[$i]?>', {
    title: {
        text: '<?=$mms_name[$mms_idx[$i]]?> <?=$mms_model[$mms_idx[$i]]?>'
    },
    subtitle: {
        text: '<?=$start_end?>'
    },
    xAxis: {
        categories: <?=$tags[$i]?>
    },
    yAxis: {
        title: {
            text: null
        }
    },
    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: ''   // °C
    },
    series: [{
        name: '범위',
        data: ranges<?=$mms_idx[$i]?>,
        type: 'areasplinerange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.5,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
    // 최적 부분은 주기 형태로 표현되면서 의미가 없음
    // series: [{
    //     name: '최적',
    //     data: averages<?=$mms_idx[$i]?>,
    //     type: 'spline',
    //     zIndex: 1,
    //     color: '#FF0000'
    // }, {
    //     name: '범위',
    //     data: ranges<?=$mms_idx[$i]?>,
    //     type: 'areasplinerange',
    //     lineWidth: 0,
    //     linkedTo: ':previous',
    //     color: Highcharts.getOptions().colors[0],
    //     fillOpacity: 0.5,
    //     zIndex: 0,
    //     marker: {
    //         enabled: false
    //     }
    // }]
});
<?php
}
?>
</script>
<script>
    // timepicker 설정
    $("input[name$=_time]").timepicker({
        'timeFormat': 'H:i:s',
        'step': 10
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
</script>

<?php
include_once ('./_tail.php');
?>
