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
.highcharts-gauge > div {
    width: 300px;
    height: 200px;
    float:left;
    margin-bottom:10px;
}
.highcharts-gauge:after {display:block;visibility:hidden;clear:both;content:'';}
.highcharts-gauge {
    width: 600px;
    /* margin: 0 auto; */
}

/*******************/
.highcharts-dynamic,
.highcharts-data-table table {
    min-width: 320px;
    max-width: 700px;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}

.div_button:after {display:block;visibility:hidden;clear:both;content:'';}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?=G5_URL?>/lib/highcharts/Highcharts/code/highcharts-more.js"></script>
<script src="<?=G5_URL?>/lib/highcharts/Highstock/code/modules/solid-gauge.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>


<div class="local_desc01 local_desc" style="display:no ne;">
    <p>작업중!!</p>
</div>


<div class="highcharts-gauge">
    <div id="container-speed" class="chart-container"></div>
    <div id="container-rpm" class="chart-container"></div>
</div>

<div class="highcharts-dynamic">
    <div id="chart1"></div>
</div>

<div class="div_button" style="margin-top:40px;">
    <a href="javascript:alert('정말 중지시키겠습니까?');" style="padding:20px 50px;background-color:#555;">로봇중지</a>
    <a href="javascript:alert('경고를 전달하시겠습니까?');" style="padding:20px 50px;background-color:#555;">경고</a>
    <a href="javascript:alert('로봇을 재시작 시키겠습니까?');" style="padding:20px 50px;background-color:#555;">로봇재시작</a>
</div>

<script>
var gaugeOptions = {
    chart: {
        type: 'solidgauge'
    },

    title: null,

    pane: {
        center: ['50%', '85%'],
        size: '140%',
        startAngle: -90,
        endAngle: 90,
        background: {
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
            innerRadius: '60%',
            outerRadius: '100%',
            shape: 'arc'
        }
    },

    exporting: {
        enabled: false
    },

    tooltip: {
        enabled: false
    },

    // the value axis
    yAxis: {
        stops: [
            [0.1, '#55BF3B'], // green
            [0.5, '#DDDF0D'], // yellow
            [0.9, '#DF5353'] // red
        ],
        lineWidth: 0,
        tickWidth: 0,
        minorTickInterval: null,
        tickAmount: 2,
        title: {
            y: -70
        },
        labels: {
            y: 16
        }
    },

    plotOptions: {
        solidgauge: {
            dataLabels: {
                y: 5,
                borderWidth: 0,
                useHTML: true
            }
        }
    }
};

// The Temperature gauge
var chartSpeed = Highcharts.chart('container-speed', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: 200,
        title: {
            text: 'Temperature'
        }
    },

    credits: {
        enabled: false
    },

    series: [{
        name: 'Temperature',
        data: [80],
        dataLabels: {
            format:
                '<div style="text-align:center">' +
                '<span style="font-size:25px">{y}</span><br/>' +
                '<span style="font-size:12px;opacity:0.4">°C</span>' +
                '</div>'
        },
        tooltip: {
            valueSuffix: ' °C'
        }
    }]

}));

// The RPM gauge
var chartRpm = Highcharts.chart('container-rpm', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: 5,
        title: {
            text: 'Torque'
        }
    },

    series: [{
        name: 'Torque',
        data: [1],
        dataLabels: {
            format:
                '<div style="text-align:center">' +
                '<span style="font-size:25px">{y:.1f}</span><br/>' +
                '<span style="font-size:12px;opacity:0.4">hPa</span>' +
                '</div>'
        },
        tooltip: {
            valueSuffix: ' revolutions/min'
        }
    }]

}));

// Bring life to the dials
setInterval(function () {
    // Temperature
    var point,
        newVal,
        inc;

    if (chartSpeed) {
        point = chartSpeed.series[0].points[0];
        inc = Math.round((Math.random() - 0.5) * 100);
        newVal = point.y + inc;

        if (newVal < 0 || newVal > 200) {
            newVal = point.y - inc;
        }

        point.update(newVal);
    }

    // Torque
    if (chartRpm) {
        point = chartRpm.series[0].points[0];
        inc = Math.random() - 0.5;
        newVal = point.y + inc;

        if (newVal < 0 || newVal > 5) {
            newVal = point.y - inc;
        }

        point.update(newVal);
    }
}, 2000);


////////////////////////
data1 = {};
Highcharts.chart('chart1', {
    chart: {
        type: 'spline',
        animation: Highcharts.svg, // don't animate in old IE
        marginRight: 10,
        events: {
            load: function () {
                // set up the updating of the chart each second
                // 1초에 하나 데이터를 추가하고 오래된 건 delete off the start point.
                var series = this.series[0];
                console.log(this.series[0]);
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

                    console.log( dt1 );
                    dt1.setSeconds(dt1.getSeconds() - 1);
                    var x = dt1.getTime(),
                        y = Math.random();
                    series.addPoint([x, y], true, true);
                    console.log( dt1 );

                    dt1.setSeconds(dt1.getSeconds() - 1);
                    console.log( dt1 );
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
        text: 'Live random data'
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
        name: 'Random data',
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
