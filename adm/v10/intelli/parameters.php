<?php
$sub_menu = "920115";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '최적파라메타';
// include_once('./_top_menu_db.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/intelli/style.css">', 2);
if(is_file(G5_USER_ADMIN_PATH.'/css/intelli/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/intelli/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
</style>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highcharts-more.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>

<div class="local_desc01 local_desc" style="display:none;">
    <p>작업중!!</p>
</div>

<div class="div_recommend">
    <div class="title01">
        최적파라미타 수집 시점
        <span class="btn_more"><a href="./best_list.php">더보기</a></span>
    </div>
    <div class="cont01">
        <?php
        // 각 설비별로 최적값 추출
        if(is_array($g5['set_dicast_mms_idxs_array'])) {
            foreach($g5['set_dicast_mms_idxs_array'] as $k1=>$v1) {
                // echo $k1.'=>'.$v1.'<br>';
                $sql = "SELECT *
                        FROM {$g5['data_measure_best_table']}
                        WHERE mms_idx = '".$v1."'
                        ORDER BY dmb_reg_dt DESC
                        LIMIT 1
                ";
                // echo $sql.'<br>';
                $one = sql_fetch($sql,1);
                $mms = get_table('mms','mms_idx',$one['mms_idx']);
                $one['mms_name'] = $g5['mms'][$one['mms_idx']]['mms_name'];
                $one['machine_id'] = $mms['mms_idx2'];
                // print_r2($one);
                $best[$k1] = $one;

                echo '<div class="rec_item">
                        <p>'.$g5['mms'][$one['mms_idx']]['mms_name'].'</p>
                        <strong>'.$one['dmb_dt'].'</strong>
                        <span>'.$one['dmb_min'].'~'.$one['dmb_max'].' ('.$one['dmb_group_count'].')</span>
                    </div>
                ';
            }
        }
        // print_r2($best);
        ?>
    </div>
</div>


<script>
// 로딩 spinner 이미지 표시/비표시
function dta_loading(flag,chart_id) {
    var img_loading = $('<i class="fa fa-spin fa-spinner" id="spinner_'+chart_id+'" style="position:absolute;top:80px;left:46%;font-size:4em;"></i>');
    if(flag=='show') {
        // console.log('show');
        $('#'+chart_id).append(img_loading);
    }
    else if(flag=='hide') {
        // console.log('hide');
        $('#spinner_'+chart_id).remove();
    }
}

function createChart(chart_id,seriesOptions,shot_ids) {
    // console.log(chart_id);
    // console.log(seriesOptions);
    chart = new Highcharts.stockChart({
        chart: {
            renderTo: chart_id
        },
        scrollbar: {
            enabled: false
        },
        subtitle: {
            text: 'Shot id: '+shot_ids
        },        

        animation: false,

        xAxis: {
            // min: 1587635789000,
            // max: 1587643939000,
            type: 'datetime',
            labels: {
                formatter: function() {
                    return moment(this.value).format("MM/DD HH:mm");
                }
            },
            // 샷 시작 & 종료표시
            // plotLines: [
            //     {
            //         color: '#FF0000',
            //         width: 2,
            //         value: 1667226311000
            //     },
            //     {
            //         color: '#FF0000',
            //         width: 1,
            //         dashStyle: 'LongDash',
            //         value: 1667226348000
            //     }
            // ]
        },

        yAxis: {
            // max: 1800,   // 크게 확대해서 보려면 20
            // min: -100,  // 크게 확대해서 보려면 -10, 없애버리면 자동 스케일
            showLastLabel: true,    // 위 아래 마지막 label 보임 (이게 없으면 끝label이 안 보임)
            opposite: false,
            tickInterval: null,
            // minorTickInterval: 5,
            // minorTickLength: 0,
        },

        plotOptions: {
            series: {
                showInNavigator: true,
                turboThreshold: 0,
                dataGrouping: {
                    enabled: false, // dataGrouping 안 함 (range가 변경되면 평균으로 바뀌어서 헷갈림)
                },
                marker: {
                    enabled: true   // point dot display
                }
            }
        },

        navigator: {
            enabled: false
        },

        navigation: {
            buttonOptions: {
                enabled: false, // contextButton (인쇄, 다운로드..) 설정 (기본옵션 사용자들에게는 안 보이게!!)
                align: 'right',
                x: -20,
                y: 15
            }
        },

        legend: {
            enabled: true,
            useHTML: true,
            // labelFormatter: function () { // 하단 항목명(legend) 수정
            //     // console.log(this._i);
            //     // console.log(mmses);
            //     var mms_name = (mms_count>1 && mmses[this._i]) ? '<br>'+decodeURIComponent(mmses[this._i]) : '';
            //     return '<span>'+this.name+mms_name+'</span>';
            // }
        },

        rangeSelector: {
            enabled: false,
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
        series: seriesOptions
    });

    dta_loading('hide',chart_id);
    removeLogo();
}
</script>

<?php
// Rotate each machines.
for($i=0;$i<sizeof($best);$i++) {
    $shot_ids1 = $shot_ids2 = array();
    $mms = get_table_meta('mms', 'mms_idx', $best[$i]['mms_idx']);  // mms meta 값으로 태그명들이 쭉 들어가 있음
    // print_r2($best[$i]);

    // 해당 시간 근처의 해당 설비의 주조공정(cast_shot) 추출, 중요한 변수들은 shot_id, start_time, end_time, machine_cycletime, product_cycletime
    // 기준 시간 위쪽으로 10개 
    $sql = "SELECT * FROM g5_1_cast_shot
            WHERE machine_id = '".$best[$i]['machine_id']."' AND end_time >= '".$best[$i]['dmb_dt']."'
            ORDER BY csh_idx LIMIT 10 OFFSET 0
    ";
    // echo $sql.'<br>';
    $rs = sql_query_pg($sql,1);
    for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
        // print_r2($row);
        $shot_ids1[$j] = $row['shot_id'];
        // 금형번호 추출
        if($j==0) {
            $best_mold[$i] = $row['mold_no'];
        }
        // 샷 시작 & 종료시각
        $shot_start1[$j] = strtotime($row['start_time']);
        $shot_end1[$j] = strtotime($row['end_time']);
    }
    // print_r2($shot_ids1);
    // print_r2($shot_start1);
    // print_r2($shot_end1);

    // 기준 시간 아래쪽으로 10개 (향후 활용을 위해서 일단 추출해 둠)
    $sql = "SELECT * FROM g5_1_cast_shot
            WHERE machine_id = '".$best[$i]['machine_id']."' AND end_time < '".$best[$i]['dmb_dt']."'
            ORDER BY csh_idx DESC LIMIT 10 OFFSET 0
    ";
    // echo $sql.'<br>';
    $rs = sql_query_pg($sql,1);
    for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
        // print_r2($row);
        $shot_ids2[$j] = $row['shot_id'];
        // 샷 시작 & 종료시각
        $shot_start2[$j] = strtotime($row['start_time']);
        $shot_end2[$j] = strtotime($row['end_time']);
    }
    // print_r2($shot_ids2);

    // 기준점 중심 3개 값
    $shot_ids = array($shot_ids2[0],$shot_ids1[0],$shot_ids1[1]);
    // print_r2($shot_ids);

    // 샷시작, 종료 3개값
    $shot_starts = array($shot_start2[0],$shot_start1[0],$shot_start1[1]);
    $shot_ends = array($shot_end2[0],$shot_end1[0],$shot_end1[1]);
    // print_r2($shot_starts);
    // print_r2($shot_ends);
    ?>
    <script>
        var seriesOptions1 = []; // 압력배열 초기화
        var seriesOptions2 = []; // 압력배열 초기화
    </script>
    <div class="div_detail">
        <div class="title01">
            <span class="title_name"><?=$best[$i]['mms_name']?></span>
            <span class="title_date"><?=$best[$i]['dmb_dt']?></span>
            <span class="title_mold">금형: <?=$best_mold[$i]?></span>
            <a href="../system/graph.php?mode=detail&mms_idx=<?=$best[$i]['mms_idx']?>&machine_id=<?=$best[$i]['machine_id']?>" class="btn_graph">Detail</a>
        </div>
        <div id="tabs<?=$i?>">
        <ul>
            <li><a href="#tabs-<?=$i?>1" tag="pressure" st_dt="" en_dt="">압력</a></li>
            <li><a href="#tabs-<?=$i?>2" tag="temperature" st_dt="" en_dt="">온도</a></li>
            <li><a href="#tabs-<?=$i?>3">설정값</a></li>
        </ul>
        <div id="tabs-<?=$i?>1">
            <?php
            // 압력 ===========================================================================================
            // 온도 (3개 포인트 - 기준점 아래, 기준점, 기준점 위)
            // $sql = "SELECT *
            //         FROM g5_1_cast_shot_sub
            //         WHERE machine_id = '".$best[$i]['machine_id']."' AND shot_id IN (".$shot_ids2[0].",".$shot_ids1[0].",".$shot_ids1[1].")
            //         ORDER BY event_time
            // ";
            $sql = "SELECT *
                    FROM g5_1_cast_shot_pressure
                    WHERE shot_id IN (".$shot_ids2[0].",".$shot_ids1[0].",".$shot_ids1[1].")
                    ORDER BY event_time
            ";
            // echo $sql.'<br>';
            $rs = sql_query_pg($sql,1);
            $list = array();
            $dta = array();
            for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
                // print_r2($row);
                $row['no'] = $i;
                $row['timestamp'] = strtotime($row['event_time']);
                // echo $row['timestamp'].'<br>';
                // 시작시점, 종료시점
                if($j==0) {$st_dt_pressure[$i] = substr($row['event_time'],0,19);}
                $en_dt_pressure[$i] = substr($row['event_time'],0,19);

                // 각 태그별로 데이터 설정
                // hold_temp=보온로온도, upper_heat=상형히트, lower_heat=하형히트, upper_1_temp=상금형1, upper_2_temp=상금형2, upper_3_temp=상금형3, upper_4_temp=상금형4, upper_5_temp=상금형5, upper_6_temp=상금형6, lower_1_temp=하금형1, lower_2_temp=하금형2, lower_3_temp=하금형3, detect_pressure=검출압력, target_pressure=목표압력, control_pressure=조작압력, deviation_pressure=편차, temp_avg=평균온도, temp_max=온도최대, temp_min=온도최소, hum_avg=평균습도, hum_max=습도최대, hum_min=습도최소
                foreach($g5['set_data_name_value'] as $k1=>$v1) {
                    // echo $k1.'=>'.$v1.'<br>';
                    if($row[$k1]) {
                        $dta[$mms['mms_idx']][$k1][$j]['x'] = $row['timestamp']*1000;
                        $dta[$mms['mms_idx']][$k1][$j]['y'] = (float)$row[$k1];
                    }
                }
            }
            // print_r2($dta[$mms['mms_idx']]);
            // echo $st_dt_pressure[$i].'~'.$en_dt_pressure[$i].'<br>'; // 시작시점~종료시점
            ?>
            <script>
                // 그래프 시작시점, 종료시점 입력
                $('#tabs<?=$i?>').find('a[tag=pressure]').attr('st_dt','<?=$st_dt_pressure[$i]?>');
                $('#tabs<?=$i?>').find('a[tag=pressure]').attr('en_dt','<?=$en_dt_pressure[$i]?>');
            </script>
            <!-- // 챠트 표시 ====================================================== -->
            <div id="pressure_<?=$i?>" class="chart_cont">
                <i class="fa fa-spin fa-circle-o-notch" id="spinner" style="position:absolute;top:80px;left:46%;font-size:4em;color:#38425b;"></i>
            </div>
            <?php
            if(is_array($dta[$mms['mms_idx']])) {
                $j=0;
                foreach($dta[$mms['mms_idx']] as $k1=>$v1) {
                    // echo $k1.'('.$g5['set_data_name_value'][$k1].')=>'.$v1.'<br>';
                    ?>
                    <script>
                        // for 돌면서 변수를 생성해 두고..
                        var data = <?=json_encode($v1)?>;
                        seriesOptions1[<?=$j?>] = {
                            name: '<?=$g5['set_data_name_value'][$k1]?>',
                            type: 'spline',
                            dashStyle: 'solid',
                            data: data
                        };
                    </script>
                    <?php
                    $j++;
                }
            }
            ?>
            <script>
                // 만들어진 배열 변수 실행
                createChart('pressure_<?=$i?>',seriesOptions1,'<?=implode(" ",$shot_ids)?>');
                <?php
                // 샷 시작 종료 시점 표시
                for ($j=0; $j<sizeof($shot_starts); $j++) {
                    // echo 'console.log('.$shot_starts[$j].');';
                    // echo 'console.log('.$shot_ends[$j].');';
                    if($shot_starts[$j]) {
                        echo "chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'Solid', color: '#FF0000', value: ".$shot_starts[$j]."000, zIndex:5});".PHP_EOL;
                    }
                    if($shot_ends[$j]) {
                        echo "chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'LongDash', color: '#FF0000', value: ".$shot_ends[$j]."000, zIndex:5});".PHP_EOL;
                    }
                }
                ?>
                // chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'Solid', color: '#FF0000', value: 1667232760000, zIndex:5});
                // chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'LongDash', color: '#FF0000', value: 1667232980000, zIndex:5});
            </script>
        </div>
        <div id="tabs-<?=$i?>2">
            <?php
            // 온도 ===========================================================================================
            $sql = "SELECT *
                    FROM g5_1_cast_shot_sub
                    WHERE machine_id = '".$best[$i]['machine_id']."' AND shot_id IN (".$shot_ids2[0].",".$shot_ids1[0].",".$shot_ids1[1].")
                    ORDER BY event_time
            ";
            // echo $sql.'<br>';
            $rs = sql_query_pg($sql,1);
            $list = array();
            $dta = array();
            for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
                // print_r2($row);
                $row['no'] = $i;
                $row['timestamp'] = strtotime($row['event_time']);
                // 시작시점, 종료시점
                if($j==0) {$st_dt_temperature[$i] = substr($row['event_time'],0,19);}
                $en_dt_temperature[$i] = substr($row['event_time'],0,19);

                // 각 태그별로 데이터 설정
                // hold_temp=보온로온도, upper_heat=상형히트, lower_heat=하형히트, upper_1_temp=상금형1, upper_2_temp=상금형2, upper_3_temp=상금형3, upper_4_temp=상금형4, upper_5_temp=상금형5, upper_6_temp=상금형6, lower_1_temp=하금형1, lower_2_temp=하금형2, lower_3_temp=하금형3, detect_pressure=검출압력, target_pressure=목표압력, control_pressure=조작압력, deviation_pressure=편차, temp_avg=평균온도, temp_max=온도최대, temp_min=온도최소, hum_avg=평균습도, hum_max=습도최대, hum_min=습도최소
                foreach($g5['set_data_name_value'] as $k1=>$v1) {
                    // echo $k1.'=>'.$v1.'<br>';
                    if($row[$k1]) {
                        $dta[$mms['mms_idx']][$k1][$j]['x'] = $row['timestamp']*1000;
                        $dta[$mms['mms_idx']][$k1][$j]['y'] = (float)$row[$k1];
                    }
                }
            }
            // print_r2($dta[$mms['mms_idx']]);
            ?>
            <script>
                // 그래프 시작시점, 종료시점 입력
                $('#tabs<?=$i?>').find('a[tag=temperature]').attr('st_dt','<?=$st_dt_temperature[$i]?>');
                $('#tabs<?=$i?>').find('a[tag=temperature]').attr('en_dt','<?=$en_dt_temperature[$i]?>');
            </script>
            <!-- // 챠트 표시 ====================================================== -->
            <div id="temperature_<?=$i?>" class="chart_cont">
                <i class="fa fa-spin fa-circle-o-notch" id="spinner" style="position:absolute;top:80px;left:46%;font-size:4em;color:#38425b;"></i>
            </div>
            <?php
            if(is_array($dta[$mms['mms_idx']])) {
                $j=0;
                foreach($dta[$mms['mms_idx']] as $k1=>$v1) {
                    // echo $k1.'('.$g5['set_data_name_value'][$k1].')=>'.$v1.'<br>';
                    ?>
                    <script>
                        // for 돌면서 변수를 생성해 두고..
                        var data = <?=json_encode($v1)?>;
                        seriesOptions2[<?=$j?>] = {
                            name: '<?=$g5['set_data_name_value'][$k1]?>',
                            type: 'spline',
                            dashStyle: 'solid',
                            data: data
                        };
                    </script>
                    <?php
                    $j++;
                }
            }
            ?>
            <script>
                // 만들어진 배열 변수 실행
                createChart('temperature_<?=$i?>',seriesOptions2,'<?=implode(" ",$shot_ids)?>');
                <?php
                // 샷 시작 종료 시점 표시
                for ($j=0; $j<sizeof($shot_starts); $j++) {
                    // echo 'console.log('.$shot_starts[$j].');';
                    // echo 'console.log('.$shot_ends[$j].');';
                    if($shot_starts[$j]) {
                        echo "chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'Solid', color: '#FF0000', value: ".$shot_starts[$j]."000, zIndex:5});".PHP_EOL;
                    }
                    if($shot_ends[$j]) {
                        echo "chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'LongDash', color: '#FF0000', value: ".$shot_ends[$j]."000, zIndex:5});".PHP_EOL;
                    }
                }
                ?>
                // chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'Solid', color: '#FF0000', value: 1667232760000, zIndex:1});
                // chart.xAxis[0].addPlotLine({width: 1, dashStyle: 'LongDash', color: '#FF0000', value: 1667232980000, zIndex:1});
            </script>

        </div>
        <div id="tabs-<?=$i?>3">
            <div class="div_set_value">
                <ul>
                    <?php
                    // get the setting values round the found spot.
                    $sql = "SELECT * FROM g5_1_data_measure_".$best[$i]['mms_idx']."
                            WHERE dta_dt >= '".$best[$i]['dmb_dt']."' AND dta_type = 13
                            ORDER BY dta_idx LIMIT 1
                    ";
                    // echo $sql.'<br>';
                    $one = sql_fetch_pg($sql,1);
                    // // print_r2($one);
                    $sql = "SELECT * FROM g5_1_data_measure_".$best[$i]['mms_idx']."
                            WHERE dta_dt = '".$one['dta_dt']."' AND dta_type = 13
                    ";
                    // echo $sql.'<br>';
                    $rs = sql_query_pg($sql,1);
                    for($j=0;$row=sql_fetch_array_pg($rs);$j++) {
                        // print_r2($row);
                        // 각 태그별 명칭
                        $row['dta_type_no_name'] = $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']] ? 
                                                        $mms['dta_type_label-'.$row['dta_type'].'-'.$row['dta_no']]
                                                            : $g5['set_data_type_value'][$row['dta_type']].'-'.$row['dta_no'];
                        // echo $row['dta_type_no_name'].'<br>';
                        echo '<li>
                                <div class="set_title">'.$row['dta_type_no_name'].'</div>
                                <strong>'.$row['dta_value'].'</strong>
                            </li>
                        ';
                    }

                    // for($j=0;$j<31;$j++) {
                    //     echo '<li>
                    //             <strong>제목</strong>
                    //             <div>내용</div>
                    //         </li>
                    //     ';
                    // }
                    ?>
                </ul>
                
            </div>
        </div>
        </div>
    </div>
    <script>
    var $tabs = $("#tabs<?=$i?>");
    $("#tabs<?=$i?>").tabs({
        create: function(event, ui) {
            // Adjust hashes to not affect URL when clicked.
            var widget = $tabs.data("uiTabs");
            widget.panels.each(function(i){
            this.id = "uiTab_" + this.id; // Prepend a custom string to tab id.
            widget.anchors[i].hash = "#" + this.id;
            $(widget.tabs[i]).attr("aria-controls", this.id);
            });
        },
        activate: function(event, ui) {
            // Add the original "clean" tab id to the URL hash.
            window.location.hash = ui.newPanel.attr("id").replace("uiTab_", "");
        },
    });
    </script>

    <?php
}
?>
<script>
// Detail 클릭시 이동
$(document).on('click','.btn_graph',function(e){
    e.preventDefault();
    var $div_par = $(this).closest('div.div_detail')
    var href = $(this).attr('href');
    // console.log( $div_par.find('li.ui-state-active').text() );
    var tag = 'pressure';
    tag = ($div_par.find('li.ui-state-active').text() == '온도') ? 'temperature' : tag;
    // console.log(tag);
    var st_dt = $div_par.find('li.ui-state-active').find('a').attr('st_dt');
    var en_dt = $div_par.find('li.ui-state-active').find('a').attr('en_dt');
    if($div_par.find('li.ui-state-active').text() == '설정값') {
        return false;
    }
    location.href = href+'&tag='+tag+'&st_dt='+st_dt+'&en_dt='+en_dt;
});
</script>


<?php
include_once ('./_tail.php');
?>
