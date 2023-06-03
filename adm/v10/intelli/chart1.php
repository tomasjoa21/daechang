<?php
$sub_menu = "920110";
include_once('./_common.php');

$g5['title'] = '그래프(주조공정(SUB))';
@include_once('./_top_menu_db.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

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

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>1분에 한번씩 reloading. 제품 생산 카운터가 있으면 추가되고 아니면 같은 그래프 유지!</p>
    <p>몇 개의 shot을 보여줄 지 설정을 해 주면 되겠습니다. 한 페이지에 max 15개 정도가 될 거 같습니다.</p>
    <p>17호기, 18호기, 19호기, 20호기</p>
</div>

<div id="graph_wrapper">

    <div class="graph_wrap">
        <!-- 차트 -->
        <div id="chart1" style="position:relative;width:100%; height:400px;">
            <div class="chart_empty">그래프가 존재하지 않습니다.</div>
        </div>
    </div><!-- .graph_wrap -->
    <div class="graph_detail" style="margin-top:10px;">
        <ul>
            <li>
                <div id="chart11" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart12" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart13" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart21" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart22" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart23" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart31" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart32" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart33" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart41" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart42" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
            <li>
                <div id="chart43" style="position:relative;width:100%;">
                    <div class="chart_empty">그래프 불러오는 중!</div>
                </div>
            </li>
        </ul>
    </div><!-- .graph_detail -->

</div><!-- #graph_wrapper -->

<div class="btn_fixed_top">
    <a href="./chart2.php" class="btn_04 btn">분포도</a>
    <a href="./chart1.php" class="btn_04 btn">모니터링</a>
</div>


<script>
var colors = Highcharts.getOptions().colors;

Highcharts.chart('chart1', {
    chart: {
        type: 'spline'
    },

    title: {
        text: '주조 파라메타 모니터링'
    },
    subtitle: {
        text: '최적의 양품 조건 (빨간색) & 실시간 파라메타 분포'
    },
    legend: {
        layout: 'proximate',
        align: 'right'
    },
    xAxis: {
        categories: ['보온로온도', '상형히트', '하형히트', '상금형1', '상금형2','상금형3', '상금형4','상금형5', '상금형6', '하금형1', '하금형2','하금형3']
    },

    plotOptions: {
        series: {
            point: {
                events: {
                    click: function () {
                        if(this.series.options.website) {
                            window.location.href = this.series.options.website;
                        }
                    }
                }
            },
            cursor: 'pointer'
        }
    },

    series: [
        {
            name: 'Standard',
            data: [690.8, 360.0, 402.2, null, null, 498.4, null, 470, 410, 400, null, null],
            website: 'https://www.naver.com',
            color: '#FF0000',
            zIndex:1
        }, {
            name: '250 (17호기)',
            data: [685.8, 355.0, 400.2, null, null, 498, null, 469.4, 411, 390, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }, {
            name: '249 (17호기)',
            data: [685.8, 362.0, 402.2, null, null, 494.4, null, 470.4, 412, 399, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }, {
            name: '248 (17호기)',
            data: [686.8, 363.0, 402.2, null, null, 498.4, null, 470.4, 413, 401, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }, {
            name: '247 (17호기)',
            data: [687.8, 364.0, 402.2, null, null, 496.4, null, 470.4, 414, 400, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }, {
            name: '246 (17호기)',
            data: [688.8, 361.0, 402.2, null, null, 497.4, null, 470.4, 415, 402, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }, {
            name: '245 (17호기)',
            data: [693.8, 360.0, 410.2, null, null, 498.4, null, 470.4, 416, 403, null, null],
            dashStyle: 'ShortDot',
            color: '#B1B1B1',
            marker: {
                symbol: 'diamond'
            }
        }
    ]
});


// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
    Highcharts.stockChart('chart11', {

        rangeSelector: {
            selected: 1
        },
        title: {
            text: '보온로 온도'
        },

        yAxis: {
            plotLines: [{
                value: 0.83,
                color: 'yellow',
                dashStyle: 'solid',
                width: 3
            },
            {
                value: maxRate,
                color: 'red',
                dashStyle: 'solid',
                width: 3
            }]
        },

        series: [{
            name: 'USD to EUR',
            data: data,
            tooltip: {
                valueDecimals: 4
            }
        }]
    });
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
    Highcharts.stockChart('chart12', {

        rangeSelector: {
            selected: 1
        },
        title: {
            text: '상형히트'
        },

        yAxis: {
            plotLines: [{
                value: maxRate,
                color: 'red',
                dashStyle: 'solid',
                width: 1,
                label: {
                    text: 'Last quarter maximum'
                }
            }]
        },

        series: [{
            name: 'USD to EUR',
            data: data,
            tooltip: {
                valueDecimals: 4
            }
        }]
    });
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
    Highcharts.stockChart('chart13', {

        rangeSelector: {
            selected: 1
        },
        title: {
            text: '하형히트'
        },

        yAxis: {
            plotLines: [{
                value: maxRate,
                color: 'red',
                dashStyle: 'solid',
                width: 1,
                label: {
                    text: 'Last quarter maximum'
                }
            }]
        },

        series: [{
            name: 'USD to EUR',
            data: data,
            tooltip: {
                valueDecimals: 4
            }
        }]
    });
});

// 2nd line ----------------------------------------

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart21', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형1'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart22', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형2'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart23', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형3'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// 3rd line ----------------------------------------

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart31', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형4'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart32', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형5'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart33', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '상금형6'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// 4th line ----------------------------------------

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart41', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '하금형1'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart42', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '하금형2'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});

// Detail graph
Highcharts.getJSON('https://cdn.jsdelivr.net/gh/highcharts/highcharts@v7.0.0/samples/data/usdeur.json', function (data) {

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
Highcharts.stockChart('chart43', {

    rangeSelector: {
        selected: 1
    },
    title: {
        text: '하금형3'
    },

    yAxis: {
        plotLines: [{
            value: maxRate,
            color: 'red',
            dashStyle: 'solid',
            width: 1,
            label: {
                text: 'Last quarter maximum'
            }
        }]
    },

    series: [{
        name: 'USD to EUR',
        data: data,
        tooltip: {
            valueDecimals: 4
        }
    }]
});
});


</script>

<?php
include_once ('./_tail.php');
?>
