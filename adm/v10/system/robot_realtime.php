<?php
$sub_menu = "925140";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '로봇실시간제어';
include_once('./_top_menu_robot.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$type_array = array('tq1'=>'토크1','tq2'=>'토크2','tq3'=>'토크3','tq4'=>'토크4','tq5'=>'토크5','tq6'=>'토크6'
                    ,'et1'=>'온도1','et2'=>'온도2','et3'=>'온도3','et4'=>'온도4','et5'=>'온도5','et6'=>'온도6');
// foreach($type_array as $k1=>$v1) {
//     echo $k1.'=>'.$v1.'<br>';
// }

// 로봇 설정값 추출
$fields = sql_field_names('g5_1_robot_setup');
$sql = " SELECT * FROM g5_1_robot_setup ORDER BY rst_robot_no, rst_type ";
// echo $sql.'<br>';
$result = sql_query($sql,1);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // print_r2($row);
    for ($j=0; $j<sizeof($fields); $j++) {
        // echo $fields[$j].'<br>';
        if(preg_match("/(_tq|_et)/",$fields[$j])) {
            // echo $fields[$j].'<br>';
            $row['rst_type_key'] = substr($fields[$j],4,2).substr($fields[$j],-1);
            $setups[$row['rst_robot_no']][$row['rst_type']][$row['rst_type_key']] = $row[$fields[$j]];
        }
    }
}
// print_r2($setups);
?>
<style>
/* /adm/v10/css/robot_realtime.css 에서 기본설정 */
.buttons a {font-size:0.8em;padding:2px 10px;    border: solid 1px #2b3c76;background: #222;}
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
    <p>노란색 라인은 알림경고, 빨간색 라인은 정지위치를 표시합니다. <a href="./config_form_robot.php">[설정하기]</a></p>
    <p>초기 로딩 시 10초 버퍼링 후 실시간 값을 표현합니다.</p>
    <p>빨간색(정지) 값을 초과하는 상태가 발생하면 <span style="color:darkorange;">즉시 작업을 중단</span>합니다. 설정된 안정화 시간이 지난 후 다시 작동됩니다. [설정하기] 페이지에서 수정하세요.</p>
</div>

<div class="chart_wrapper">
    <div class="chart_left">
        <div class="chart_title">
            <strong>로봇1</strong>
            <div class="buttons">
                <a href="javascript:robot_action('warn','1')">경고</a>
                <a href="javascript:robot_action('stop','1')">로봇정지</a>
                <a href="javascript:alert('로봇을 재시작 시키겠습니까?');" style="display:none">로봇재시작</a>
            </div>
        </div>
        <div id="chart1_tq1"></div><!-- 토크1 -->
        <div id="chart1_tq2"></div><!-- 토크2 -->
        <div id="chart1_tq3"></div><!-- 토크3 -->
        <div id="chart1_tq4"></div><!-- 토크4 -->
        <div id="chart1_tq5"></div><!-- 토크5 -->
        <div id="chart1_tq6"></div><!-- 토크6 -->
        <div id="chart1_et1"></div><!-- 온도1 -->
        <div id="chart1_et2"></div><!-- 온도2 -->
        <div id="chart1_et3"></div><!-- 온도3 -->
        <div id="chart1_et4"></div><!-- 온도4 -->
        <div id="chart1_et5"></div><!-- 온도5 -->
        <div id="chart1_et6"></div><!-- 온도6 -->
    </div>
    <div class="chart_right">
        <div class="chart_title">
            <strong>로봇2</strong>
            <div class="buttons">
                <a href="javascript:robot_action('warn','2')">경고</a>
                <a href="javascript:robot_action('stop','2')">로봇정지</a>
                <a href="javascript:alert('로봇을 재시작 시키겠습니까?');" style="display:none">로봇재시작</a>
            </div>
        </div>
        <div id="chart2_tq1"></div><!-- 토크1 -->
        <div id="chart2_tq2"></div><!-- 토크2 -->
        <div id="chart2_tq3"></div><!-- 토크3 -->
        <div id="chart2_tq4"></div><!-- 토크4 -->
        <div id="chart2_tq5"></div><!-- 토크5 -->
        <div id="chart2_tq6"></div><!-- 토크6 -->
        <div id="chart2_et1"></div><!-- 온도1 -->
        <div id="chart2_et2"></div><!-- 온도2 -->
        <div id="chart2_et3"></div><!-- 온도3 -->
        <div id="chart2_et4"></div><!-- 온도4 -->
        <div id="chart2_et5"></div><!-- 온도5 -->
        <div id="chart2_et6"></div><!-- 온도6 -->
    </div>
</div>


<script>
<?php
for($x=1;$x<3;$x++) {
    foreach($type_array as $k1=>$v1) {
        // set setup values for toqrue and temperature.
        $setups[$x]['A'][$k1] = $setups[$x]['A'][$k1] ?: 0;
        $setups[$x]['S'][$k1] = $setups[$x]['S'][$k1] ?: 0;
        // echo 'console.log("'.$k1.'");';
        // echo 'console.log("'.$setups[$x]['A'][$k1].' '.$setups[$x]['S'][$k1].'");';
?>
Highcharts.chart('chart<?=$x?>_<?=$k1?>', {
    chart: {
        type: 'spline',
        animation: Highcharts.svg, // don't animate in old IE
        marginRight: 10,
        events: {
            load: function () {
                var series0 = this.series[0];   // 그래프중에서 첫번째 (실제로 여러개의 그래프가 들어갈 수 있음)
                // console.log(this.series[0]);
                console.log('<?=G5_USER_URL?>/json/robot.php?token=1099de5drf09&robot=<?=$x?>&type=<?=$k1?>');
                setInterval(function () {
                    $.getJSON(g5_user_url+'/json/robot.php',{"token":"1099de5drf09","robot":"<?=$x?>","type":"<?=$k1?>"},function(res) {
                        // console.log(res);
                        $.each(res, function(i, v) {
                            // console.log(i+':'+v);
                            // console.log(i+':'+v['x']+','+v['y']);
                            var setTime = i*1000;
                            // console.log(setTime+':'+v['x']+','+v['y']);
                            setTimeout(function(e){
                                series0.addPoint([v['x'], v['y']], true, true);
                            },setTime);
                        });
                    });
                }, 10000);
            }
        }
    },
    time: {
        useUTC: false
    },
    title: {
        text: '<?=$v1?>'
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
                value: <?=$setups[$x]['A'][$k1]?>,  // 경고 기준값
                color: 'yellow',
                dashStyle: 'solid',
                width: 3
            },
            {
                value: <?=$setups[$x]['S'][$k1]?>,  // 정지 기준값
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
        name: '<?=$v1?>',
        data: (function () {
            // generate an array of random data
            var data = [],
                time = (new Date()).getTime(),
                i;
            for (i = -29; i <= 0; i += 1) {
                data.push({
                    x: time + i * 1000,
                    y: 0
                    // y: Math.random()*20
                });
            }
            return data;
        }())
    }]
});
<?php
    }
}
?>

// 로봇액션
function robot_action(act, no) {
    var act_text = (act=='stop') ? '정지':'경고';
    // alert(act + no);
    if(confirm(no+'번 로봇을 '+act_text+'처리하시겠습니까?')) {
        $.getJSON(g5_user_admin_ajax_url+'/robot.json.php',{"aj":"r1","act":act,"no":no},function(res) {
            // console.log(res);
            alert(res.msg);
        });
    }
}
</script>



<?php
include_once ('./_tail.php');
?>
