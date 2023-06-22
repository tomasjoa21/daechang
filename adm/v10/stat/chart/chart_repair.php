<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
var chart_item_names = <?=json_encode($chart_item_names)?>;

//######################## 구분타입별 알람(#chart_type) [Column with rotated labels]#############################
var chart_month_values = <?=json_encode($month_values)?>;
Highcharts.chart('chart_monthly', {
    chart: {
        type: 'column',
    },
    title: {
        text: '',
    },
    xAxis: {
        // categories: ["2021-12","2022-01","2022-01","2022-01","2022-01","2022-01","2022-02","2022-03"],
        categories: chart_item_names,
        crosshair: true,
    },
    yAxis: {
        min: 0,
        title: {
            text: '발생수',
        }
    },
    tooltip: {
        headerFormat: '<b>{point.key}</b><br/>',
        pointFormat: '{series.name}:{point.y:.0f}<br/>',
        shared: true
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: true
            }
        }
    },
    series: 
        // [{name: '발생수',data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6]}, {name: '정비수',data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0]}],
        chart_month_values,
    exporting: {
        enabled:false
    },
    credits:{
        enabled:false
    }

});
//######################## 구분타입별 알람(#chart_type) [Column with rotated labels]#############################
var chart_month_code_values = <?=json_encode($month_code_values)?>;
Highcharts.chart('chart_code', {
    title: {
        text: ''
    },
    yAxis: {
        title: {
            text: '알람 발생수',
        },
        tickInterval: null
    },
    xAxis: {
        // categories: ["2021-12","2022-01","2022-01","2022-01","2022-01","2022-01","2022-02"]
        categories: chart_item_names,
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: true
            }
        }
    },
    series: 
        // [{name: 'M1043',data: [34, 2, 4, 4, 3, 3, 3]}, {name: 'M1041',data: [2, 2, 3, 4, 5, 6, 7]}]
        chart_month_code_values,
    exporting: {
        enabled:false
    },
    credits:{
        enabled:false
    },
    legend: {
        enabled:false
    }
});
</script>