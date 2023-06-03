<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
var chart_item_names = <?=json_encode($chart_item_names)?>;

// 천단위 표현
Highcharts.setOptions({
    lang: {
      decimalPoint: '.',
      thousandsSep: ','
    }
});

//######################## 구분타입별 알람(#chart_type) [Column with rotated labels]#############################
var chart_month_values = <?=json_encode($month_values)?>;
Highcharts.chart('chart_monthly', {
    chart: {
        type: 'column',
        style: {
            fontSize: '13px',
            fontFamily: 'Verdana, sans-serif',
            color: '#ffffff'
        }
    },
    title: {
        text: '',
        style: {
            fontSize: '13px',
            fontFamily: 'Verdana, sans-serif',
            color: '#ffffff'
        }
    },
    xAxis: {
        // categories: ["2021-12","2022-01","2022-01","2022-01","2022-01","2022-01","2022-02","2022-03"],
        categories: chart_item_names,
        crosshair: true,
        labels: {
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif',
                color: '#ffffff'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: '발생수',
            style: {
                color:'#ffffff'
            }
        },
        labels: {
            style: {
                color: '#ffffff'
            }
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}:</td>' +
            '<td style="padding-left:5px;"><b>{point.y:.0f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
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
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif',
                color: '#ffffff'
            }
        },
        labels: {
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif',
                color: '#ffffff'
            }
        },
        tickInterval: null
    },
    xAxis: {
        // categories: ["2021-12","2022-01","2022-01","2022-01","2022-01","2022-01","2022-02"]
        categories: chart_item_names,
        labels: {
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif',
                color: '#ffffff'
            }
        }
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