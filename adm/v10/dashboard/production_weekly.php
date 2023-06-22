<?php
// http://daechang.epcs.co.kr/adm/v10/dashboard/production_weekly.php?w=1&h=1
include_once('./_common.php');

$g5['title'] = '주간생산그래프';
include_once('./_head.sub.php');

// st_date, en_date
// 한달 전
$sql = " SELECT DATE_ADD(now(), INTERVAL -1 MONTH) AS month_ago ";
$one = sql_fetch($sql,1);
$st_date = $st_date ?: substr($one['month_ago'],0,10);
$en_date = $en_date ?: G5_TIME_YMD;
// echo $st_date.'~'.$en_date.BR;

$sql = "SELECT (CASE WHEN n='1' THEN ymd_date ELSE 'total' END) AS item_name
            , SUM(output_total) AS output_total
            , MAX(output_total) AS output_max
            , SUM(output_good) AS output_good
            , SUM(output_defect) AS output_defect
        FROM
        (

            SELECT 
                ymd_date
                , SUM(output_total) AS output_total
                , SUM(output_good) AS output_good
                , SUM(output_defect) AS output_defect
            FROM
            (
                (
                SELECT 
                    CAST(ymd_date AS CHAR) AS ymd_date
                    , 0 AS output_total
                    , 0 AS output_good
                    , 0 AS output_defect
                FROM {$g5['ymd_table']} AS ymd
                WHERE ymd_date BETWEEN '".$st_date."' AND '".$en_date."'
                ORDER BY ymd_date
                )
                UNION ALL
                (
                SELECT 
                    itm_date AS ymd_date
                    , COUNT(itm_idx) AS output_total
                    , SUM( CASE WHEN itm_status IN ('finish','delivery','check') THEN 1 ELSE 0 END ) AS output_good
                    , SUM( CASE WHEN itm_defect_type = 'defect' THEN 1 ELSE 0 END ) AS output_defect
                FROM {$g5['item_table']}
                WHERE itm_date >= '".$st_date."' AND itm_date <= '".$en_date."'
                GROUP BY ymd_date
                ORDER BY ymd_date
                )
            ) AS db_table
            GROUP BY ymd_date

        ) AS db2, g5_5_tally AS db_no
        WHERE n <= 2
        GROUP BY item_name
        ORDER BY n DESC, item_name
";
// echo $sql.BR;
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    //print_r2($row);
    // 합계인 경우
    if($row['item_name'] == 'total') {
        $row['item_name'] = '합계';
        $row['tr_class'] = 'tr_stat_total';
        $output_total = $row['output_total'];
        $output_max = $row['output_max']; // 값들 중에서 최대값
    }
    else {
        $row['tr_class'] = 'tr_stat_normal';
        $categories[] = $row['item_name'];
        $series_ok[] = $row['output_good'];
        $series_ng[] = $row['output_defect'];
    }
    // echo $output_total.'<br>';
    // echo $output_max.'<br>';
}


if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/style.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/style.css">', 2);
}
if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
</style>

<div class="box_header">
    <div class="top_left">
        <p class="title_main"><?=$st_date?> ~ <?=$en_date?></p>
    </div>
    <div class="top_right">
        <p>
            <a href="../stat/output.php" class="btn_detail" style="margin-right:10px;"><i class="fa fa-list-alt"></i></a>
            <a href="javascript:" class="btn_reload"><i class="fa fa-repeat"></i></a>
        </p>
    </div>
</div>
<div class="box_body">
    <div id="chart_day" style="height:100%;"></div>
</div>



<script>
var dom_height = $('.frame_03', parent.document).height() - 20;
$('#chart_day').css('height',dom_height+'px');

$(document).on('click','.btn_detail',function(e){
    e.preventDefault();
    parent.location.href = $(this).attr('href');
});
$(document).on('click','.btn_reload',function(){
    self.location.reload();
});

Highcharts.chart('chart_day', {
    chart: {
        type: 'column'
    },
    exporting: {
        enabled: false
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
setTimeout(function(e){
    $('.highcharts-credits').remove();
},10);
// 10분에 한번 재로딩
setTimeout(function(e){
    self.location.reload();
},1000*600);
</script>

<?php
include_once ('./_tail.sub.php');
?>
