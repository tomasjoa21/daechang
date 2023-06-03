<?php
$sub_menu = "940160";
include_once('./_common.php');

$g5['title'] = '용해온도';
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
$sql = " SELECT * FROM g5_1_melting_temp ORDER BY mlt_idx DESC LIMIT 1 ";
$one = sql_fetch($sql,1);
// print_r3($one);
$en_date = ($en_date) ? $en_date : substr($one['event_time'],0,10);
$en_time = ($en_time) ? $en_time : substr($one['event_time'],11);
$st_date = ($st_date) ? $st_date : date("Y-m-d",strtotime($en_date.' '.$en_time)-$st_time_ahead);
$st_time = ($st_time) ? $st_time : date("H:i:s",strtotime($en_date.' '.$en_time)-$st_time_ahead);
// echo $en_date.' '.$en_time.'<br>';
// echo $st_date.' '.$st_time.'<br>';
// exit;

// item_type
$item_type = ($item_type) ? $item_type : 'temp_avg';
// query string
$qs = 'token=1099de5drf09&st_date='.$st_date.'&st_time='.$st_time.'&en_date='.$en_date.'&en_time='.$en_time.'&item_type='.$item_type;
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
    <select name="item_type">
        <option value="temp_avg">평균온도</option>
        <option value="temp_min">최소온도</option>
        <option value="temp_max">최대온도</option>
        <option value="alarm_min">최소알람기준</option>
        <option value="alarm_max">최대알람기준</option>
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

<div class="btn_fixed_top" style="display:none;">
    <a href="./chart2.php" class="btn_04 btn">분포도</a>
    <a href="./chart1.php" class="btn_04 btn">모니터링</a>
</div>


<script>
// Detail graph
Highcharts.getJSON(g5_url+'/device/rdb/melting_temp.php?<?=$qs?>', function(data) {

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
