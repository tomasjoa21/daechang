<?php
$sub_menu = "925140";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '로봇실시간제어';
include_once('./_top_menu_robot.php');
include_once('./_head.php');
echo $g5['container_sub_title'];
?>
<style>
/* /adm/v10/css/robot_realtime.css 에서 기본설정 */
.highcharts-dynamic {width:100%;}
.highcharts-dynamic > div {width:49%;}
.highcharts-dynamic:after {display:block;visibility:hidden;clear:both;content:'';}
#chart1 {float:left;}
#chart2 {float:right;}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?=G5_URL?>/lib/highcharts/Highcharts/code/highcharts-more.js"></script>
<script src="<?=G5_URL?>/lib/highcharts/Highstock/code/modules/solid-gauge.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>


<div class="local_desc01 local_desc" style="display:none;">
    <p>작업중!!</p>
</div>

<div class="highcharts-dynamic">
    <div id="chart1"></div>
    <div id="chart2"></div>
</div>

<div class="div_button" style="margin-top:40px;">
    <a href="javascript:alert('정말 중지시키겠습니까?');" style="padding:20px 50px;background-color:#555;">로봇중지</a>
    <a href="javascript:alert('경고를 전달하시겠습니까?');" style="padding:20px 50px;background-color:#555;">경고</a>
    <a href="javascript:alert('로봇을 재시작 시키겠습니까?');" style="padding:20px 50px;background-color:#555;">로봇재시작</a>
</div>

<script>
Highcharts.chart('chart1', {
    chart: {
        type: 'spline',
        animation: Highcharts.svg, // don't animate in old IE
        marginRight: 10,
        events: {
            load: function () {
                // set up the updating of the chart each second
                // 1초에 하나 데이터를 추가하고 오래된 건 delete off from the start point.
                var series = this.series[0];
                // console.log(this.series[0]);
                setInterval(function () {
                    dt1 = new Date();

                    dt1.setSeconds(dt1.getSeconds());
                    var x1 = dt1.getTime(),
                        y1 = Math.random();
                    // series.addPoint([x, y], true, true);
                    setTimeout(function(e){
                        series.addPoint([x1, y1], true, true);
                    },2000);
                    // addPoint() adds only single point. To add more points use that function multiple times.

                    // console.log( dt1 );
                    dt1.setSeconds(dt1.getSeconds() - 1);
                    var x2 = dt1.getTime(),
                        y2 = Math.random();

                    setTimeout(function(e){
                        series.addPoint([x2, y2], true, true);
                    },1000);

                    dt1.setSeconds(dt1.getSeconds() - 1);
                    var x3 = dt1.getTime(),
                        y3 = Math.random();

                    setTimeout(function(e){
                        series.addPoint([x3, y3], true, true);
                    },0);

                }, 3000);
            }
        }
    },

    time: {
        useUTC: false
    },

    title: {
        text: '토크'
    },

    accessibility: {
        announceNewData: {
            enabled: true,
            minAnnounceInterval: 15000,
            announcementFormatter: function (allSeries, newSeries, newPoint) {
                if (newPoint) {
                    return 'New point added. Value: ' + newPoint.y;
                }
                return false;
            }
        }
    },

    xAxis: {
        type: 'datetime',
        tickPixelInterval: 150
    },

    yAxis: {
        title: {
            text: 'Value'
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }]
    },

    tooltip: {
        headerFormat: '<b>{series.name}</b><br/>',
        pointFormat: '{point.x:%Y-%m-%d %H:%M:%S}<br/>{point.y:.2f}'
    },

    legend: {
        enabled: false
    },

    exporting: {
        enabled: false
    },

    // 초기 데이터 19개 series
    series: [{
        name: '토크1',
        data: (function() {
            // generate an array of random data
            var data = [],
                time = (new Date()).getTime(),
                i;
            for (i = -39; i <= 0; i += 1) {
                data.push({
                    x: time + i * 1000,
                    y: Math.random()
                });
            }
            return data;
        }())
    }]
});

Highcharts.chart('chart2', {
    chart: {
        type: 'spline',
        animation: Highcharts.svg, // don't animate in old IE
        marginRight: 10,
        events: {
            load: function () {
                // set up the updating of the chart each second
                // 1초에 하나 데이터를 추가하고 오래된 건 delete off from the start point.
                var series = this.series[0];
                // console.log(this.series[0]);
                // setInterval(function () {
                //     var x = (new Date()).getTime(), // current time
                //         y = Math.random();
                //     series.addPoint([x, y], true, true);
                //     // console.log(x + '=' + y);
                //     // console.log(series.data[0].options);
                //     // console.log(series.data[1].options);
                // }, 3000);
                setInterval(function () {
                    dt1 = new Date();
                    dt1.setSeconds(dt1.getSeconds());
                    var x = dt1.getTime(),
                        y = Math.random();
                    series.addPoint([x, y], true, true);
                    // addPoint() adds only single point. To add more points use that function multiple times.

                    // console.log( dt1 );
                    dt1.setSeconds(dt1.getSeconds() - 1);
                    var x = dt1.getTime(),
                        y = Math.random();
                    series.addPoint([x, y], true, true);
                    // console.log( dt1 );

                    dt1.setSeconds(dt1.getSeconds() - 1);
                    // console.log( dt1 );
                    var x = dt1.getTime(),
                        y = Math.random();
                    series.addPoint([x, y], true, true);
                }, 3000);
            }
        }
    },

    time: {
        useUTC: false
    },

    title: {
        text: '온도'
    },

    accessibility: {
        announceNewData: {
            enabled: true,
            minAnnounceInterval: 15000,
            announcementFormatter: function (allSeries, newSeries, newPoint) {
                if (newPoint) {
                    return 'New point added. Value: ' + newPoint.y;
                }
                return false;
            }
        }
    },

    xAxis: {
        type: 'datetime',
        tickPixelInterval: 150
    },

    yAxis: {
        title: {
            text: 'Value'
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }]
    },

    tooltip: {
        headerFormat: '<b>{series.name}</b><br/>',
        pointFormat: '{point.x:%Y-%m-%d %H:%M:%S}<br/>{point.y:.2f}'
    },

    legend: {
        enabled: false
    },

    exporting: {
        enabled: false
    },

    // 초기 데이터 19개 series
    series: [{
        name: '토크1',
        data: (function () {
            // generate an array of random data
            var data = [],
                time = (new Date()).getTime(),
                i;
            for (i = -19; i <= 0; i += 1) {
                data.push({
                    x: time + i * 1000,
                    y: Math.random()
                });
            }
            return data;
        }())
    }]
});
</script>



<?php
include_once ('./_tail.php');
?>
