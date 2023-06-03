<?php
$sub_menu = "925140";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '로봇실시간제어';
include_once('./_top_menu_robot.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// Temp data
$t = G5_SERVER_TIME;
for($i=1;$i<7;$i++) {
    for($j=0;$j<10;$j++) {
        ${'tq'.$i}[$j]['x'] = $t+1; // +1초씩
        ${'tq'.$i}[$j]['y'] = rand(0,40);
    }
}
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
var seriesOptions1 = []; // 토크배열 초기화
var seriesOptions2 = []; // 온도배열 초기화

var time = (new Date()).getTime();
<?php
// 데이터 배열 생성
for($i=0;$i<6;$i++) {
?>
var data<?=$i?> = [], i;
for (i = -39; i <= 0; i += 1) {
    data<?=$i?>.push({
        x: time + i * 1000,
        y: Math.random()
    });
}
seriesOptions1[<?=$i?>] = {
    name: '토크<?=$i?>',
    type: 'spline',
    dashStyle: 'solid',
    data: data<?=$i?>
};
<?php
}
?>


Highcharts.chart('chart1', {
    chart: {
        type: 'spline',
        animation: Highcharts.svg, // don't animate in old IE
        marginRight: 10,
        events: {
            load: function () {

                // for(i=0;i<6;i++) {
                    // set up the updating of the chart each second
                    // 1초에 하나 데이터를 추가하고 오래된 건 delete off from the start point.
                    var series0 = this.series[0];
                    var series1 = this.series[1];
                    var series2 = this.series[2];
                    var series3 = this.series[3];
                    var series4 = this.series[4];
                    var series5 = this.series[5];
                    // console.log(this.series[0]);
                    setInterval(function () {
                        dt1 = new Date();
    
                        dt1.setSeconds(dt1.getSeconds());
                        var x0_1 = dt1.getTime(), y0_1 = Math.random();
                        var x1_1 = dt1.getTime(), y1_1 = Math.random();
                        var x2_1 = dt1.getTime(), y2_1 = Math.random();
                        var x3_1 = dt1.getTime(), y3_1 = Math.random();
                        var x4_1 = dt1.getTime(), y4_1 = Math.random();
                        var x5_1 = dt1.getTime(), y5_1 = Math.random();
                        dt1.setSeconds(dt1.getSeconds() - 1);
                        var x0_2 = dt1.getTime(), y0_2 = Math.random();
                        var x1_2 = dt1.getTime(), y1_2 = Math.random();
                        var x2_2 = dt1.getTime(), y2_2 = Math.random();
                        var x3_2 = dt1.getTime(), y3_2 = Math.random();
                        var x4_2 = dt1.getTime(), y4_2 = Math.random();
                        var x5_2 = dt1.getTime(), y5_2 = Math.random();
                        dt1.setSeconds(dt1.getSeconds() - 1);
                        var x0_3 = dt1.getTime(), y0_3 = Math.random();
                        var x1_3 = dt1.getTime(), y1_3 = Math.random();
                        var x2_3 = dt1.getTime(), y2_3 = Math.random();
                        var x3_3 = dt1.getTime(), y3_3 = Math.random();
                        var x4_3 = dt1.getTime(), y4_3 = Math.random();
                        var x5_3 = dt1.getTime(), y5_3 = Math.random();
    
                        setTimeout(function(e){
                            series0.addPoint([x0_1, y0_1], true, true);
                            series1.addPoint([x1_1, y1_1], true, true);
                            series2.addPoint([x2_1, y2_1], true, true);
                            series3.addPoint([x3_1, y3_1], true, true);
                            series4.addPoint([x4_1, y4_1], true, true);
                            series5.addPoint([x5_1, y5_1], true, true);
                        },2000);
                        setTimeout(function(e){
                            series0.addPoint([x0_2, y0_2], true, true);
                            series1.addPoint([x1_2, y1_2], true, true);
                            series2.addPoint([x2_2, y2_2], true, true);
                            series3.addPoint([x3_2, y3_2], true, true);
                            series4.addPoint([x4_2, y4_2], true, true);
                            series5.addPoint([x5_2, y5_2], true, true);
                        },1000);
                        setTimeout(function(e){
                            series0.addPoint([x0_3, y0_3], true, true);
                            series1.addPoint([x1_3, y1_3], true, true);
                            series2.addPoint([x2_2, y2_2], true, true);
                            series3.addPoint([x3_2, y3_2], true, true);
                            series4.addPoint([x4_2, y4_2], true, true);
                            series5.addPoint([x5_2, y5_2], true, true);
                        },0);
    
                    }, 3000);
                // }
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

    legend: {
        enabled: false
    },
    
    exporting: {
        enabled: false
    },
    
    // tooltip: {
    //     headerFormat: '<b>{series.name}</b><br/>',
    //     pointFormat: '{point.x:%Y-%m-%d %H:%M:%S}<br/>{point.y:.2f}'
    // },
    tooltip: {
        formatter: function(e) {
            var tooltip1 =  moment(this.x).format("YYYY-MM-DD HH:mm:ss");
            // console.log(this);
            var tooltip2 = [];
            $.each(this.points, function () {
                var this_name = this.series.name;
                tooltip1 += '<br/><span style="color:' + this.color + '">\u25CF '+this_name+'</span>: <b>' + this.point.y + '</b>';
            });
            return tooltip1;
        },
        split: false,
        shared: true
    },


    // 초기 데이터 19개 series
    series: seriesOptions1
});


// ===========================================================================================
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
