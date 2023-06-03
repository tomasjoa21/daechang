<?php
$sub_menu = "940160";
include_once('./_common.php');

$g5['title'] = '온도(주조공정(SUB)) 그래프';
include_once('./_top_menu_tsdb.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 검색 조건
$st_time_ahead = 3600*24;  // 5hour ahead.
// $st_date = ($st_date) ? $st_date : date("Y-m-d",G5_SERVER_TIME-$st_time_ahead);
// $st_time = ($st_time) ? $st_time : date("H:i:s",G5_SERVER_TIME-$st_time_ahead);
// $en_date = ($en_date) ? $en_date : G5_TIME_YMD;
// $en_time = ($en_time) ? $en_time : date("H:i:s",G5_SERVER_TIME);

// Set the search period reset according to the last data input.
$sql = " SELECT * FROM g5_1_cast_shot_sub ORDER BY css_idx DESC LIMIT 1 ";
$one = sql_fetch($sql,1);
// print_r3($one);
$en_date = ($en_date) ? $en_date : substr($one['event_time'],0,10);
$en_time = ($en_time) ? $en_time : substr($one['event_time'],11);
$st_date = ($st_date) ? $st_date : date("Y-m-d",strtotime($en_date.' '.$en_time)-$st_time_ahead);
$st_time = ($st_time) ? $st_time : date("H:i:s",strtotime($en_date.' '.$en_time)-$st_time_ahead);
// echo $en_date.' '.$en_time.'<br>';
// echo $st_date.' '.$st_time.'<br>';
// exit;



// mms_idx
$mms_idx = ($mms_idx) ? $mms_idx : 45;
// item_type
$item_type = ($item_type) ? $item_type : 'hold_temp';
// query string
$qs = 'token=1099de5drf09&mms_idx='.$mms_idx.'&st_date='.$st_date.'&st_time='.$st_time.'&en_date='.$en_date.'&en_time='.$en_time.'&item_type='.$item_type;
?>
<style>
.graph_detail ul:after{display:block;visibility:hidden;clear:both;content:'';}
.graph_detail ul li {float:left;width:32%;margin-right:10px;margin-bottom:10px;}
.graph_detail ul li > div{border:solid 1px #ddd;height:300px;}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>


<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <select name="mms_idx">
        <option value="45">LPM05(17)</option>
        <option value="44">LPM04(18)</option>
        <option value="58">LPM03(19)</option>
        <option value="59">LPM02(20)</option>
    </select>
    <script>
        $('select[name=mms_idx]').val('<?=$mms_idx?>');
        $(document).on('change','select[name=mms_idx]',function(e){
            $('.btn_search').trigger('click');
        });
    </script>
    <select name="item_type">
        <option value="hold_temp">보온로온도</option>
        <option value="upper_heat">상형온도</option>
        <option value="lower_heat">하형온도</option>
        <option value="upper1_temp">상금형1</option>
        <option value="upper2_temp">상금형2</option>
        <option value="upper3_temp">상금형3</option>
        <option value="upper4_temp">상금형4</option>
        <option value="upper5_temp">상금형5</option>
        <option value="upper6_temp">상금형6</option>
        <option value="lower1_temp">하금형1</option>
        <option value="lower2_temp">하금형2</option>
        <option value="lower3_temp">하금형3</option>
    </select>
    <script>
        $('select[name=item_type]').val('<?=$item_type?>');
        $(document).on('change','select[name=item_type]',function(e){
            $('.btn_search').trigger('click');
        });
    </script>
    <input type="hidden" name="dta_minsec" value="<?=$dta_minsec?>" id="dta_minsec" class="frm_input" style="width:20px;">
    <input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;" >
    <input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;">
    ~
    <input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
    <input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;">
    <button type="submit" class="btn btn_01 btn_search">확인</button>
</form>

<div id="graph_wrapper">

    <div class="graph_wrap">
        <!-- 차트 -->
        <div id="chart1" style="position:relative;width:100%; height:500px;">
            <div class="chart_empty">그래프 불러오는 중..</div>
        </div>
    </div><!-- .graph_wrap -->

</div><!-- #graph_wrapper -->

<div class="btn_fixed_top" style="display:no ne;">
    <a href="./tsdb_shot_sub_multi.php" class="btn_04 btn">겹쳐보기</a>
    <a href="./chart1.php" class="btn_04 btn" style="display:none;">모니터링</a>
</div>


<script>
// Detail graph
// Highcharts.getJSON('http://hanjoo.epcs.co.kr/php/hanjoo/device/json/usdeur.json?st_date=2022-06-02&st_time=13:33:14&en_date=2022-06-02&en_time=14:33:14', function(data) {
Highcharts.getJSON(g5_url+'/device/rdb/shot_sub.php?<?=$qs?>', function(data) {

    var startDate = new Date(data[data.length - 1][0]), // Get year of last data point
        minRate = 1,
        maxRate = 0,
        startPeriod,
        date,
        rate,
        index;

    startDate.setMonth(startDate.getMonth() - 3); // a quarter of a year before last data point
    startPeriod = Date.UTC(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());

    for (index = data.length - 1; index >= 0; index = index - 1) {
        date = data[index][0]; // data[i][0] is date
        rate = data[index][1]; // data[i][1] is exchange rate
        if (date < startPeriod) {
            break; // stop measuring highs and lows
        }
        if (rate > maxRate) {
            maxRate = rate;
        }
        if (rate < minRate) {
            minRate = rate;
        }
    }

    // Create the chart
    Highcharts.stockChart('chart1', {

        rangeSelector: {
            selected: 1
        },
        title: {
            text: '온도(주조공정SUB)'
        },

        yAxis: {
            plotLines: [{
                value: 690,
                color: 'red',
                dashStyle: 'LongDash',
                width: 2,
                label: {
                    text: 'Last quarter minimum'
                }
            }]
        },

        series: [{
            name: '값',
            data: data,
            tooltip: {
                valueDecimals: 4
            }
        }]
    });
});
</script>

<script>
$(function(e) {
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
</script>


<?php
include_once ('./_tail.php');
?>
