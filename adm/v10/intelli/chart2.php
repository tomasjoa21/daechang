<?php
$sub_menu = "920120";
include_once('./_common.php');

$g5['title'] = '그래프(주조공정(SUB))';
include_once('./_top_menu_db.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

?>
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

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>특정 기간 안에 최대, 최소값을 보여줍니다.</p>
    <p>17호기, 18호기, 19호기, 20호기 선택</p>
</div>

<div id="graph_wrapper">

<div class="graph_wrap">
    <!-- 차트 -->
    <div id="chart1" style="position:relative;width:100%; height:400px;">
        <div class="chart_empty">그래프가 존재하지 않습니다.</div>
    </div>
    <div id="chart2" style="position:relative;width:100%; height:400px;">
        <div class="chart_empty">그래프가 존재하지 않습니다.</div>
    </div>
    <div id="chart3" style="position:relative;width:100%; height:400px;">
        <div class="chart_empty">그래프가 존재하지 않습니다.</div>
    </div>
    <div id="chart4" style="position:relative;width:100%; height:400px;">
        <div class="chart_empty">그래프가 존재하지 않습니다.</div>
    </div>
</div><!-- .graph_wrap -->
</div><!-- #graph_wrapper -->

<div class="btn_fixed_top">
    <a href="./chart2.php" class="btn_04 btn">분포도</a>
    <a href="./chart1.php" class="btn_04 btn">모니터링</a>
</div>


<script>
var ranges = [
        ['보온로온도', 650, 720],
        ['상형히트', 350, 362],
        ['하형히트', 391, 408],
        ['상금형1', null, null],
        ['상금형2', null, null],
        ['상금형3', 490, 520.225],
        ['상금형4', null, null],
        ['상금형5', 435.225, 475.225],
        ['상금형6', 394.5, 429.1],
        ['하금형1', 392.8, 427.7],
        ['하금형2', null, null],
        ['하금형3', null, null],
    ],
    averages = [
        ['보온로온도', 695],
        ['상형히트', 360],
        ['하형히트', 402],
        ['상금형1', null],
        ['상금형2', null],
        ['상금형3', 498],
        ['상금형4', null],
        ['상금형5', 470],
        ['상금형6', 410],
        ['하금형1', 400],
        ['하금형2', null],
        ['하금형3', null],
    ];

// 17호기 ----------------------------
Highcharts.chart('chart1', {

    title: {
        text: '17호기'
    },
    subtitle: {
        text: '최적 기준선이 가운데 나타나고 최대(위) 최소값(아래)이 표현됩니다.'
    },

    xAxis: {
        categories: ['보온로온도', '상형히트', '하형히트', '상금형1', '상금형2','상금형3', '상금형4','상금형5', '상금형6', '하금형1', '하금형2','하금형3']
    },
    yAxis: {
        title: {
            text: null
        }
    },

    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: '°C'
    },

    series: [{
        name: '최적',
        data: averages,
        type: 'spline',
        zIndex: 1,
        color: '#FF0000'
    }, {
        name: '범위',
        data: ranges,
        type: 'areasplinerange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.6,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});

// 18호기 ----------------------------
Highcharts.chart('chart2', {

    title: {
        text: '18호기'
    },
    subtitle: {
        text: '최적 기준선이 가운데 나타나고 최대(위) 최소값(아래)이 표현됩니다.'
    },

    xAxis: {
        categories: ['보온로온도', '상형히트', '하형히트', '상금형1', '상금형2','상금형3', '상금형4','상금형5', '상금형6', '하금형1', '하금형2','하금형3']
    },
    yAxis: {
        title: {
            text: null
        }
    },

    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: '°C'
    },

    series: [{
        name: '최적',
        data: averages,
        type: 'spline',
        zIndex: 1,
        color: '#FF0000'
    }, {
        name: '범위',
        data: ranges,
        type: 'areasplinerange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.6,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});

// 19호기 ----------------------------
Highcharts.chart('chart3', {

    title: {
        text: '19호기'
    },
    subtitle: {
        text: '최적 기준선이 가운데 나타나고 최대(위) 최소값(아래)이 표현됩니다.'
    },

    xAxis: {
        categories: ['보온로온도', '상형히트', '하형히트', '상금형1', '상금형2','상금형3', '상금형4','상금형5', '상금형6', '하금형1', '하금형2','하금형3']
    },
    yAxis: {
        title: {
            text: null
        }
    },

    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: '°C'
    },

    series: [{
        name: '최적',
        data: averages,
        type: 'spline',
        zIndex: 1,
        color: '#FF0000'
    }, {
        name: '범위',
        data: ranges,
        type: 'areasplinerange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.6,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});

// 20호기 ----------------------------
Highcharts.chart('chart4', {

    title: {
        text: '20호기'
    },
    subtitle: {
        text: '최적 기준선이 가운데 나타나고 최대(위) 최소값(아래)이 표현됩니다.'
    },

    xAxis: {
        categories: ['보온로온도', '상형히트', '하형히트', '상금형1', '상금형2','상금형3', '상금형4','상금형5', '상금형6', '하금형1', '하금형2','하금형3']
    },
    yAxis: {
        title: {
            text: null
        }
    },

    tooltip: {
        crosshairs: true,
        shared: true,
        valueSuffix: '°C'
    },

    series: [{
        name: '최적',
        data: averages,
        type: 'spline',
        zIndex: 1,
        color: '#FF0000'
    }, {
        name: '범위',
        data: ranges,
        type: 'areasplinerange',
        lineWidth: 0,
        linkedTo: ':previous',
        color: Highcharts.getOptions().colors[0],
        fillOpacity: 0.6,
        zIndex: 0,
        marker: {
            enabled: false
        }
    }]
});

</script>

<?php
include_once ('./_tail.php');
?>
