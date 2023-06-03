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
                    // console.log(this.series[0]);
                    setInterval(function () {
                        $.getJSON(g5_user_url+'/json/robot.php',{"token":"1099de5drf09","robot":"1","type":"tq2"},function(res) {
                            // console.log(res);
                            $.each(res, function(i, v) {
                                // console.log(i+':'+v);
                                // console.log(i+':'+v['x']+','+v['y']);
                                var setTime = i*1000;
                                console.log(setTime+':'+v['x']+','+v['y']);
                                setTimeout(function(e){
                                    series0.addPoint([v['x'], v['y']], true, true);
                                },setTime);
                            });
                        });

                        // dt1 = new Date();
                        // dt1.setSeconds(dt1.getSeconds());
                        // var x0_1 = dt1.getTime(), y0_1 = 0.1;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_2 = dt1.getTime(), y0_2 = 0.2;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_3 = dt1.getTime(), y0_3 = 0.3;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_4 = dt1.getTime(), y0_4 = 0.4;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_5 = dt1.getTime(), y0_5 = 0.5;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_6 = dt1.getTime(), y0_6 = 0.6;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_7 = dt1.getTime(), y0_7 = 0.7;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_8 = dt1.getTime(), y0_8 = 0.8;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_9 = dt1.getTime(), y0_9 = 0.9;
                        // dt1.setSeconds(dt1.getSeconds() - 1);
                        // var x0_10 = dt1.getTime(), y0_10 = 1.0;

                        // setTimeout(function(e){
                        //     series0.addPoint([x0_1, y0_1], true, true);
                        // },9000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_2, y0_2], true, true);
                        // },8000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_3, y0_3], true, true);
                        // },7000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_4, y0_4], true, true);
                        // },6000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_5, y0_5], true, true);
                        // },5000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_6, y0_6], true, true);
                        // },4000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_7, y0_7], true, true);
                        // },3000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_8, y0_8], true, true);
                        // },2000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_9, y0_9], true, true);
                        // },1000);
                        // setTimeout(function(e){
                        //     series0.addPoint([x0_10, y0_10], true, true);
                        // },0);
    
                    }, 10000);
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
                value: 24,  // 경고 기준값
                color: 'yellow',
                dashStyle: 'solid',
                width: 3
            },
            {
                value: 45,  // 정지 기준값
                color: 'red',
                dashStyle: 'solid',
                width: 3
            }]
    },
    plotOptions: {
        series: {
            marker: {
                enabled: true   // point dot display
            }
        }
    },
    legend: {
        enabled: false
    },
    exporting: {
        enabled: false
    },
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
    series: [{
        name: '토크1',
        data: (function () {
            // generate an array of random data
            var data = [],
                time = (new Date()).getTime(),
                i;
            for (i = -29; i <= 0; i += 1) {
                data.push({
                    x: time + i * 1000,
                    y: Math.random()*20
                });
            }
            return data;
        }())
    }]
});


// =================================================================================================================
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
