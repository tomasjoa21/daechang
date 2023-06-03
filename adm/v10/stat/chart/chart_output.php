<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
Highcharts.chart('chart_day', {
    chart: {
        type: 'column'
    },
    title: {
        text: '',
    },
    xAxis: {
        // categories: ['2020-10-01', '2020-10-02', '2020-10-03', '2020-10-04']
        categories: ['<?=implode("','",$categories)?>']
    },
    yAxis: {
        min: 0,
        title: {
            text: ''
        },
        stackLabels: {
            enabled: true,
            style: {
                fontWeight: 'bold',
                color: ( // theme
                    Highcharts.defaultOptions.title.style &&
                    Highcharts.defaultOptions.title.style.color
                ) || 'gray',
                textOutline: 'none'
            }
        }
    },
    legend:{ enabled:false },
    tooltip: {
        headerFormat: '<b>{point.x}</b><br/>',
        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: true
            }
        }
    },
    series: [
        {
            name: 'OK',
            // data: [30, 50, 10, 130]
            data: [<?=implode(",",$series_ok)?>]
        }, {
            name: 'NG',
            data: [<?=implode(",",$series_ng)?>]
        }
    ]
});
</script>