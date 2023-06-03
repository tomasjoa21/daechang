<?php
$sub_menu = "920130";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '생산현황그래프';
include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

// 검색 조건
$st_time_ahead = 3600*12;  // 5hour ahead.

// 초기 디폴트 로딩
if(!$mbd_idx) {
    // Set the search period reset according to the last data input.
    $sql = " SELECT * FROM g5_1_xray_inspection ORDER BY xry_idx DESC LIMIT 1 ";
    $one = sql_fetch($sql,1);
    // print_r3($one);
    $mbd['sried']['en_date'] = substr($one['end_time'],0,10);
    $mbd['sried']['en_time'] = substr($one['end_time'],11);
    $mbd['sried']['st_date'] = date("Y-m-d",strtotime($en_date.' '.$en_time)-$st_time_ahead);
    $mbd['sried']['st_time'] = date("H:i:s",strtotime($en_date.' '.$en_time)-$st_time_ahead);

    // 등급합계
    $mbd['data'][0]['name'] = '등급합계';
    $mbd['data'][0]['type'] = 'spline';
    $mbd['data'][0]['dashStyle'] = 'solid';
    $mbd['data'][0]['id']['dta_data_url_host'] = 'hanjoo.epcs.co.kr';
    $mbd['data'][0]['id']['dta_data_url_path'] = '/user/json';
    $mbd['data'][0]['id']['dta_data_url_file'] = 'output.php';
    $mbd['data'][0]['id']['mms_idx'] = '';
    $mbd['data'][0]['id']['dta_type'] = '';
    $mbd['data'][0]['id']['dta_no'] = '';
    $mbd['data'][0]['id']['type1'] = '';
    $mbd['data'][0]['id']['graph_name'] = urlencode($mbd['data'][0]['name']);
    $mbd['data'][0]['id']['graph_id'] = '1';
    // 양불 판정
    $mbd['data'][1]['name'] = '불량판정';
    $mbd['data'][1]['type'] = 'column';
    $mbd['data'][1]['dashStyle'] = 'solid';
    $mbd['data'][1]['id']['dta_data_url_host'] = 'hanjoo.epcs.co.kr';
    $mbd['data'][1]['id']['dta_data_url_path'] = '/user/json';
    $mbd['data'][1]['id']['dta_data_url_file'] = 'output.php';
    $mbd['data'][1]['id']['mms_idx'] = '';
    $mbd['data'][1]['id']['dta_type'] = '';
    $mbd['data'][1]['id']['dta_no'] = '';
    $mbd['data'][1]['id']['type1'] = 'ng';   // ng=양불데이터
    $mbd['data'][1]['id']['graph_name'] = urlencode($mbd['data'][1]['name']);
    $mbd['data'][1]['id']['graph_id'] = '2';
    // print_r2($mbd);
}


// mbd_idx 가 존재하면 저장된 값에서 그래프 추출
if($mbd_idx) {
    $mbd = get_table_meta('member_dash','mbd_idx',$mbd_idx);
    // print_r2($mbd);
    $mbd['sried'] = get_serialized($mbd['mbd_setting']);
    $mbd['data'] = json_decode($mbd['sried']['data_series'],true);
    unset($mbd['mbd_setting']);
    unset($mbd['sried']['data_series']);
    // print_r2($mbd);
    // for($j=0;$j<sizeof($mbd['data']);$j++) {
    //     // print_r2($mbd['data'][$j]);
    // }
    $ar['type'] = 'current'; // 현재 시점으로 바꿔달라는 요청에 따라 변경됨
    $ar['st_date'] = $mbd['sried']['st_date'];
    $ar['st_time'] = $mbd['sried']['st_time'];
    $ar['en_date'] = $mbd['sried']['en_date'];
    $ar['en_time'] = $mbd['sried']['en_time'];
    $ar['mms_idx'] = $mbd['data'][0]['id']['mms_idx'];  // 그래프는 serialized된 배열중에서 첫번째 항목을 참조함
    $ar['dta_type'] = $mbd['data'][0]['id']['dta_type'];
    $ar['dta_no'] = $mbd['data'][0]['id']['dta_no'];
    $ar['type1'] = $mbd['data'][0]['id']['type1'];
    // print_r2($ar);
    $start_end_dt = get_start_end_dt($ar);  // 데이터가 없을 때를 고려한 시간 범위로 재설정
    // print_r2($start_end_dt);
    unset($ar);

    $st_date = $start_end_dt['st_date'];
    $st_time = $start_end_dt['st_time'];
    $en_date = $start_end_dt['en_date'];
    $en_time = $start_end_dt['en_time'];
}


// Set the search period reset according to the last data input.
$sql = " SELECT * FROM g5_1_xray_inspection ORDER BY xry_idx DESC LIMIT 1 ";
$one = sql_fetch($sql,1);
// print_r3($one);
$en_date = ($en_date) ? $en_date : substr($one['end_time'],0,10);
$en_time = ($en_time) ? $en_time : substr($one['end_time'],11);
$st_date = ($st_date) ? $st_date : date("Y-m-d",strtotime($en_date.' '.$en_time)-$st_time_ahead);
$st_time = ($st_time) ? $st_time : date("H:i:s",strtotime($en_date.' '.$en_time)-$st_time_ahead);
// echo $en_date.' '.$en_time.' en_date<br>';
// echo $st_date.' '.$st_time.' st_date<br>';
// exit;

// query string
$qs = 'token=1099de5drf09&st_date='.$st_date.'&st_time='.$st_time.'&en_date='.$en_date.'&en_time='.$en_time;
add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/js/timepicker/jquery.timepicker.css">', 0);
?>
<script type="text/javascript" src="<?=G5_USER_ADMIN_URL?>/js/timepicker/jquery.timepicker.js"></script>
<style>
#chart1 {background:#0c172c;position:relative;width:100%; height:500px;line-height:300px;text-align:center;border:solid 1px #333;overflow:hidden;}
.graph_detail ul:after{display:block;visibility:hidden;clear:both;content:'';}
.graph_detail ul li {float:left;width:32%;margin-right:10px;margin-bottom:10px;}
.graph_detail ul li > div{border:solid 1px #ddd;height:300px;}
.div_btn_add {float:right;}
#fsearch {display:block;}
#fsearch .div_btn_add {margin:0 !important;}
#fchart {margin:0 0;}
#fchart .chr_name {font-weight:bold;font-size:1.5em;margin-right:6px;}
.table01 {width:auto;margin:0 auto;}
.table01 td {padding:7px 9px;}
.table01 td input {width:84px;-moz-outline: none;outline: none;ie-dummy: expression(this.hideFocus=true);}
.ui-slider-handle {-moz-outline: none;outline: none;ie-dummy: expression(this.hideFocus=true);}
.chr_control {display:none;}
#chr_type, #chr_line {height:30px;-moz-outline: none;outline: none;ie-dummy: expression(this.hideFocus=true);}
.ui-timepicker-list li {color: #909090;background-color: #0c172c;}
.ui-timepicker-list:hover .ui-timepicker-selected {color: #909090;background-color: #1c335d;}
.ui-timepicker-wrapper {border: 1px solid #555;}
#chr_amp_value, #chr_move_value {border:0 !important; color:#f6931f !important; font-weight:bold;background-color: unset !important;}
.xbuttons {position:absolute;top:-36px;right:0px;}
.xbuttons a {margin-left:2px;border:solid 1px #ddd;padding:2px 6px;}
.graph_wrap {position:relative;}
#report {display:none;position:absolute;top:-42px;right:100px;}
#report:after {display:block;visibility:hidden;clear:both;content:'';}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>


<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
    <input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;" >
    <input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;">
    ~
    <input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
    <input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;">
    <button type="submit" class="btn btn_01 btn_search">확인</button>
</form>

<div id="graph_wrapper">
    <div class="graph_wrap">
        <div id="report">
            <input type="text" id="xmin" placeholder="xmin">
            <input type="text" id="xmax" placeholder="xmax">
            <input type="text" id="ymin" placeholder="ymin">
            <input type="text" id="ymax" placeholder="ymax">
        </div>
        <div class="xbuttons">
            <a href="javascript:" class="btn_orig" title="초기화"><i class="fa fa-bars"></i></a>
            <a href="javascript:" class="btn_smaller" title="작게"><i class="fa fa-compress"></i></a>
            <a href="javascript:" class="btn_bigger" title="크게"><i class="fa fa-expand"></i></a>
        </div>
        <!-- 차트 -->
        <div id="chart1">
            <span class="text01">[불러오기] 버튼을 클릭하여 그래프를 추가하세요.</span>
        </div>
    </div><!-- .graph_wrap -->

    <!-- 컨트롤 -->
    <div class="chr_control" style="text-align:center;">
        <form name="fchart" id="fchart" method="post">
        <input type="hidden" name="chr_idx" style="width:20px"><!-- 차트고유번호 (클릭할 때마다 바뀜) -->
            <table class="table01">
            <tbody>
                <tr>
                    <td class="td_" style="vertical-align:bottom;">
                        <span class="chr_name">항목명</span>
                    </td>
                    <td class="td_" style="display:no ne;">
                        <label for="chr_amp_value">증폭</label>
                        <input type="text" id="chr_amp_value" autocomplete="off">
                        <div id="chr_amp" style="width:150px;"></div>
                    </td>
                    <td class="td_" style="display:no ne;">
                        <label for="chr_move_value">이동</label>
                        <input type="text" id="chr_move_value" autocomplete="off">
                        <div id="chr_move" style="width:150px;"></div>
                    </td>
                    <td class="td_" style="padding-top:14px;">
                        <select name="chr_type" id="chr_type" autocomplete="off">
                            <option value="">그래프타입</option>
                            <option value="spline">꺽은선1</option>
                            <option value="line">꺽은선2</option>
                            <option value="column">막대</option>
                        </select>
                    </td>
                    <td class="td_" style="padding-top:14px;">
                        <select name="chr_line" id="chr_line" autocomplete="off">
                            <option value="">선종류</option>
                            <option value="solid">실선</option>
                            <option value="shortdot">점선</option>
                        </select>
                    </td>
                    <td class="td_" style="vertical-align:bottom;">
                        <button type="submit" class="btn btn_03"><i class="fa fa-check"></i> 적용</button>
                        <a href="javascript:" class="btn btn_02 btn_chr_del" title="제거">제거 <i class="fa fa-trash-o"></i></a>
                        <a href="javascript:" class="btn btn_02 btn_chr_close" title="닫기">닫기 <i class="fa fa-times"></i></a>
                    </td>
                </tr>
            </tbody>
            </table>
        </form>
    </div>
    <script>
    // 컨트롤 부분 변경 (증푹 & 이동)
    $("#fchart").on('submit', function(e){
        e.preventDefault();
        var frm = $('#fchart');
        // console.log( frm.find('#chr_amp_value').val() );
        // console.log( frm.find('#chr_move_value').val() );

        var chr_idx_chg = parseInt(frm.find('input[name=chr_idx]').val());
        var chr_amp = parseFloat(frm.find('#chr_amp_value').val());
        var chr_move = parseInt(frm.find('#chr_move_value').val());
        var chr_type = frm.find('#chr_type').val();
        var chr_line = frm.find('#chr_line').val();
        
        // console.log( $('#chr_amp').slider('option','min') );
        // console.log( chr_amp_slider.slider("option","max") );
        // 증폭값 설정
        if(chr_amp<chr_amp_slider.slider("option","min")) {
           alert('증폭 최소값은 '+chr_amp_slider.slider("option","min")+'입니다.');
           return false;
        }
        if(chr_amp>chr_amp_slider.slider("option","max")) {
           chr_amp_slider.slider("option", "max", chr_amp);
           chr_amp_slider.slider("option", "value", chr_amp);
        }
        // 이동값 설정
        console.log( chr_move+'<'+chr_move_slider.slider("option","min") );
        if(chr_move<chr_move_slider.slider("option","min")) {
           alert('이동 최소값은 '+chr_move_slider.slider("option","min")+'입니다.');
           return false;
        }
        if(chr_move>chr_move_slider.slider("option","max")) {
            alert('이동 최대값은 '+chr_move_slider.slider("option","max")+'입니다.');
           return false;
        }

        // chr_move_slider.slider("option", "value", 0);
        if(isNaN(chr_idx_chg) == false) {
            // console.log(seriesOptions[chr_idx_chg]);
            old_type = seriesOptions[chr_idx_chg].type;
            old_line = seriesOptions[chr_idx_chg].dashStyle;
            old_yamp = seriesOptions[chr_idx_chg].data[0].yamp;
            old_ymove = seriesOptions[chr_idx_chg].data[0].ymove;
            // console.log( chr_idx_chg );
            // console.log( old_yamp +'/'+ chr_amp +'//'+ old_ymove +'/'+ chr_move +'//'+ old_type +'/'+ chr_type );
            // console.log( '----' );
            // 증폭, 이동값, 그래프 종류가 바뀐 경우만 수정 그래프 변형
            if(old_yamp!=chr_amp || old_ymove!=chr_move || old_type!=chr_type || old_line!=chr_line) {
                for(i=0;i<seriesOptions[chr_idx_chg].data.length;i++) {
                    // console.log(seriesOptions[chr_idx_chg].data[i]);
                    raw_y = seriesOptions[chr_idx_chg].data[i].yraw;    // original Y value
                    amp_y = raw_y*chr_amp;  // amplified value
                    new_y = amp_y+chr_move;  // amplified+moved value
                    // console.log( raw_y +'/'+ chr_amp );
                    seriesOptions[chr_idx_chg].type = chr_type;
                    seriesOptions[chr_idx_chg].dashStyle = chr_line;
                    seriesOptions[chr_idx_chg].data[i].yamp = chr_amp;
                    seriesOptions[chr_idx_chg].data[i].ymove = chr_move;
                    seriesOptions[chr_idx_chg].data[i].y = new_y;
                    // console.log(seriesOptions[chr_idx_chg].data[i].y);
                }
                createChart();
            }
        }
    });
    </script>
</div><!-- #graph_wrapper -->

<div class="div_btn_add" style="float:right;display:no ne;">
</div>

<div class="btn_fixed_top" style="display:none;">
    <a href="javascript:alert('설정된 그래프를 대시보드로 내보냅니다.');" class="btn btn_03 btn_add_dash" style="display:no ne;"><i class="fa fa-line-chart"></i> 내보내기</a>
    <a href="./data_graph_add.php?file_name=<?php echo $g5['file_name']?>" class="btn btn_03 btn_add_chart"><i class="fa fa-bar-chart"></i>불러오기</a>
</div>


<script>
var graphs2 = [], seriesOptions = [], data_series = [], graph_type = 'spline', graph_line = 'solid',
    seriesCounter = 0, chart, options;

function createChart() {
    // var chart = new Highcharts.stockChart({
    options = {
        chart: {
            renderTo: 'chart1',
            // type: 'spline',   // line, spline, area, areaspline, column, bar, pie, scatter, gauge, arearange, areasplinerange, columnrange
            // 챠트를 불러올 때 이동범위 초기값 입력
            events: {
                redraw: function(event) {
                    $('#xmin').val(this.xAxis[0].min);
                    $('#xmax').val(this.xAxis[0].max);
                    $('#ymin').val(this.yAxis[0].min);
                    $('#ymax').val(this.yAxis[0].max);
                    console.log('redraw');
                    // console.log(this.yAxis[0].max);
                    // console.log(this.yAxis[0].min);
                    // // bottom slider min, max change
                    // chr_move_slider.slider("option", "min", parseInt(this.yAxis[0].min));
                    // chr_move_slider.slider("option", "max", parseInt(this.yAxis[0].max));
                },
                load: function() {
                    $('#xmin').val(this.xAxis[0].min);
                    $('#xmax').val(this.xAxis[0].max);
                    $('#ymin').val(this.yAxis[0].min);
                    $('#ymax').val(this.yAxis[0].max);
                    console.log('load');
                    // console.log(this);
                    // console.log(this.series[0].dataMax);
                    // console.log(this.series[1].dataMax);
                    // console.log(event);
                    // // console.log(this.yAxis[0].max);
                    // // console.log(this.yAxis[0].min);
                    // // bottom slider min, max change
                    // chr_move_slider.slider("option", "min", parseInt(this.yAxis[0].min));
                    // chr_move_slider.slider("option", "max", parseInt(this.yAxis[0].max));
                }
            },
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
            events: {
                setExtremes: function (e) {
                    $('#xmin').val(e.min);
                    $('#xmax').val(e.max);
                }
            }
        },

        yAxis: {
            // max: 1800,   // 크게 확대해서 보려면 20
            // min: -100,  // 크게 확대해서 보려면 -10, 없애버리면 자동 스케일
            showLastLabel: true,    // 위 아래 마지막 label 보임 (이게 없으면 끝label이 안 보임)
            scrollbar: {
                enabled: true
            },
            opposite: false,
            tickInterval: null,
            // minorTickInterval: 5,
            // minorTickLength: 0,
        },

        plotOptions: {
            series: {
                showInNavigator: true,
                turboThreshold: 0,
                events: {
                    // 그래프 하단 챠트이름 클릭
                    legendItemClick: function (e) {
                        e.preventDefault();

                        // 챠트 정보 추출
                        var chr_idx = this.userOptions._colorIndex; // _symbolIndex가 undefined일 때가 있어서 _colorIndex로 대체함
                        var chr_name = this.userOptions.name;   // 챠트 이름
                        var old_type = this.userOptions.type;   // 챠트 타입 (spline..)
                        var old_dashStyle = this.userOptions.dashStyle;
                        var old_amp = this.userOptions.data[0].yamp;    // 증폭값
                        var old_move = this.userOptions.data[0].ymove;  // 이동값

                        // 폼에 값 변경
                        $('#fchart input[name=chr_idx]').val(chr_idx);  // 차트 고유번호
                        $('#fchart .chr_name').text(chr_name);          // 차트이름 변경

                        // 증푹값, 이동값 Reset
                        chr_amp_slider.slider("option", "value", old_amp);
                        chr_move_slider.slider("option", "value", old_move);
                        $('#chr_amp_value').val(old_amp);
                        $('#chr_move_value').val(old_move);
                        $('#chr_type option[value="'+old_type+'"]').attr('checked','checked');
                        $('#chr_type').val(old_type);
                        $('#chr_line').val(old_dashStyle);

                        // 각 차트의 최대 최소값 추출
                        // console.log(seriesOptions.length);
                        // console.log(this.yAxis.max);
                        // console.log(this.yAxis.min);
                        var chart_max = this.yAxis.max;
                        var chart_min = this.yAxis.min;
                        
                        // console.log(chr_idx + '번 그래프 항목 클릭');
                        // 이동범위 계산, 
                        // for(i=0;i<seriesOptions.length;i++) {
                        //     // console.log(seriesOptions[i].data);
                        //     console.log(i);
                        //     console.log(getMinMax2DArr(seriesOptions[i].data, 'y'));
                        // }
                        // console.log(seriesOptions);
                        // console.log(this);
                        // console.log(this.userOptions);
                        // console.log(this.userOptions.data[0]); 
                        var my_range_arr = getMinMax2DArr(seriesOptions[chr_idx].data, 'y');
                        // console.log(my_range_arr);

                        // 이동 하한값 = 나의 max - 그래프 min
                        // 이동 상한값 = 그래프 max - 나의 min
                        var move_value_min = my_range_arr[0] - chart_min;
                        var move_value_max = chart_max - my_range_arr[1];
                        console.log( -parseInt(move_value_min) + '~' + parseInt(move_value_max));
                        chr_move_slider.slider("option", "min", -parseInt(move_value_min));
                        chr_move_slider.slider("option", "max", parseInt(move_value_max));

                        // 챠트수정 폼 보이기
                        $('.chr_control').show();
                    }
                },
                dataGrouping: {
                    enabled: false, // dataGrouping 안 함 (range가 변경되면 평균으로 바뀌어서 헷갈림)
                },
                marker: {
                    enabled: true   // point dot display
                }
            }
        },

        navigator: {
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    second: '%H:%M:%S',
                    minute: '%H:%M',
                    hour: '%H:%M',
                    day: '%m-%d',
                    week: '%m-%d',
                    month: '%Y-%m',
                    year: '%Y-%m'
                }
            },
        },

        navigation: {
            buttonOptions: {
                enabled: true, // contextButton (인쇄, 다운로드..) 설정 (기본옵션 사용자들에게는 안 보이게!!)
                align: 'right',
                x: -20,
                y: 15
            }
        },

        legend: {
            enabled: true,
        },

        rangeSelector: {
            enabled: false,
        },

        tooltip: {
            formatter: function(e) {
                // var tooltip1 =  moment(this.x).format("YYYY-MM-DD HH:mm:ss");
                if($('#dta_item').val()=='daily'||$('#dta_item').val()=='weekly') {
                    var tooltip1 =  moment(this.x).format("MM/DD");
                }
                else if($('#dta_item').val()=='monthly') {
                    var tooltip1 =  moment(this.x).format("YYYY-MM");
                }
                else if($('#dta_item').val()=='yearly') {
                    var tooltip1 =  moment(this.x).format("YYYY");
                }
                else {
                    var tooltip1 =  moment(this.x).format("MM/DD HH:mm:ss");
                }
                // console.log(this);
                var tooltip2 = [];
                $.each(this.points, function () {
                    // console.log(this);
                    tooltip1 += '<br/><span style="color:' + this.color + '">\u25CF '+this.series.name+'</span>: <b>' + this.point.yraw + '</b>';
                    if(this.point.y!=this.point.yraw) {
                        if(this.point.yamp!=1)
                            tooltip2[0] = '×' + this.point.yamp;
                        if(this.point.ymove!=0) {
                            var tooltip2_unit = (this.point.ymove>0) ? '+':'';  // -기호는 자동으로 붙음
                            tooltip2[1] = tooltip2_unit + this.point.ymove;
                        }
                        // console.log(tooltip2);
          
                        if(tooltip2.length>=1) {
                            tooltip1 += '<span style="font-size:0.8em;"> (' + tooltip2.join(" ") + ')</span>';
                        }
                    }
                });
                return tooltip1;
            },
            split: false,
            shared: true
        },
        series: seriesOptions
        
    };

    chart = new Highcharts.stockChart(options);
    removeLogo();
    // console.log( chart.series );
    
}

// 2차 배열값에서 min, max 추출
function getMinMax2DArr(arr, idx) {
  const min = Math.min.apply(null, arr.map((el) => el[idx]));
  const max = Math.max.apply(null, arr.map((el) => el[idx]));
  return [max,min];
}

// As we're loading the data asynchronously, we don't know what order it will arrive.
// So we keep a counter and create the chart when all the data is loaded.
function drawChart(data) {
    // find which graph
    var para = urlParaToJSON2(this.url); // get values from Json Url
    // console.log(this.url);
    var graph_id = para.graph_id;
    // console.log('기준: '+graph_id);
    
    // idx 찾기 (비동기라 어떤 idx가 왔는지 모르기 때문!!)
    if( $("#chart1").attr("graphs") != undefined ) {
        graphs =  JSON.parse( $("#chart1").attr("graphs") );
        for(i=0;i<graphs.length;i++) {
            // console.log( graph_id +'=='+ graphs[i].graph_id );
            if( graph_id == graphs[i].graph_id ) {
                var chr_idx = i;
            }
        }
        console.log(chr_idx + ' arrived.');
            
        // console.log(graphs[chr_idx]);
        // 해당하는 graphs 배열에서 값을 뽑아서 graph_id 를 생성
        var graph_id1 = getGraphId(graphs[chr_idx].mms_idx, graphs[chr_idx].dta_type, graphs[chr_idx].dta_no, graphs[chr_idx].type1);
        var chr_id = {
            dta_data_url_host: graphs[chr_idx].dta_data_url_host,
            dta_data_url_path: graphs[chr_idx].dta_data_url_path,
            dta_data_url_file: graphs[chr_idx].dta_data_url_file,
            mms_idx: graphs[chr_idx].mms_idx,
            dta_type: graphs[chr_idx].dta_type,
            dta_no: graphs[chr_idx].dta_no,
            type1: graphs[chr_idx].type1,
            graph_name: graphs[chr_idx].graph_name,
            graph_id: graph_id1
        };

        // data variable definition <<<<==============================================
        seriesOptions[chr_idx] = {
            name: decodeURIComponent(graphs[chr_idx].graph_name),
            id:chr_id,
            type: graphs[chr_idx].graph_type,
            dashStyle: graphs[chr_idx].graph_line,
            data: data
        };

        // Create chart when all data loaded.
        seriesCounter += 1;
        // console.log(graphs.length + ' graphs.length == '+ seriesCounter + ' seriesCounter.');
        if (seriesCounter == graphs.length) {
            console.log('graph drawing .................................');
            console.log('seriesOptions length: ' + seriesOptions.length);
            createChart();
        }
    }
}

// 그래프 확인클릭(Submit) ======================================================================
$(document).on('click','#fsearch button[type=submit]',function(e){
    e.preventDefault();
    var frm = $('#fsearch');
    var st_date = frm.find('#st_date').val() || '';
    var st_time = frm.find('#st_time').val() || '';
    var en_date = frm.find('#en_date').val() || '';
    var en_time = frm.find('#en_time').val() || '';
    if(st_date==''||en_date==''||st_time==''||en_time=='') {
        alert('검색 날짜, 시간을 입력하세요.');
        return false;
    }

    dta_loading('show');
    seriesCounter = 0;
 
    // get the graphs attribute form target object div
    var graphs =  JSON.parse( $("#chart1").attr("graphs") );
    // console.log(graphs);
    // console.log(graphs.length);

    // 다중 그래프 표현
    for(i=0;i<graphs.length;i++) {
        // console.log(i+' --------------- ');
        var dta_data_url_host = graphs[i].dta_data_url_host;
        var dta_data_url_path = graphs[i].dta_data_url_path;
        var dta_data_url_file = graphs[i].dta_data_url_file;
        var mms_idx = graphs[i].mms_idx;
        var mms_name = graphs[i].mms_name;
        var dta_type = graphs[i].dta_type;
        var dta_no = graphs[i].dta_no;
        var type1 = graphs[i].type1;
        var graph_name = graphs[i].graph_name;
        var graph_id1 = getGraphId(mms_idx, dta_type, dta_no, type1);
        // console.log(i+'. '+graph_id1);

        // 그래프 호출 URL
        var dta_url = '//'+dta_data_url_host+dta_data_url_path+'/'+dta_data_url_file+'?token=1099de5drf09'
                        +'&mms_idx='+mms_idx+'&dta_type='+dta_type+'&dta_no='+dta_no+'&type1='+type1
                        +'&st_date='+st_date+'&st_time='+st_time+'&en_date='+en_date+'&en_time='+en_time
                        +'&graph_id='+graph_id1;
        //..........................................
        console.log(dta_url);

        Highcharts.getJSON(
            dta_url,
            drawChart
        );

    }
    // dta_loading('hide');
    $('.div_btn_add').show();

});

// 그래프 내보내기
$(document).on('click','.btn_add_dash',function(e){
    e.preventDefault();
    if(seriesOptions.length == 0) {
        alert('내보내기할 그래프가 없습니다.');
        return false;
    }
    if(confirm('설정된 그래프를 대시보드로 내보시겠습니까?')) {
        // 폼 설정값 
        var frm_serialized = $('#fsearch').serialize();

        // 그래프 설정값
        // console.log(seriesOptions);
        for(i=0;i<seriesOptions.length;i++) {
            // console.log(seriesOptions[i]);
            data_series[i] = {
                name: seriesOptions[i].name,
                id: seriesOptions[i].id,
                type:seriesOptions[i].type,
                dashStyle:seriesOptions[i].dashStyle
            };
        }
        // console.log(frm_serialized);
        // console.log(data_series);
        //-- 디버깅 Ajax --//
        $.ajax({
            url:g5_user_admin_ajax_url+'/dash.php',
            data:{"aj":"put","com_idx":"<?=$com['com_idx']?>","mms_idx":"<?=$mms_idx?>","mbd_idx":"<?=$mbd_idx?>","frm_data":frm_serialized,"data_series":data_series},
            dataType:'json', timeout:10000, beforeSend:function(){}, success:function(res){
                // console.log(res);
                //var prop1; for(prop1 in res.rows) { console.log( prop1 +': '+ res.rows[prop1] ); }
                if(res.result == true) {
                    self.location.href = '../index.php';
                }
                else {
                    alert(res.msg);
                }
            },
            error:function(xmlRequest) {
                alert('Status: ' + xmlRequest.status + ' \n\rstatusText: ' + xmlRequest.statusText 
                    + ' \n\rresponseText: ' + xmlRequest.responseText);
            } 
        });
    }
});


// Y축 스케일 조정 (크게, 작게, 제쟈리로)
$('.btn_bigger, .btn_orig, .btn_smaller').click(function(e) {
    var act = $(this).attr('class');    // btn_bigger, btn_orig, btn_smaller
    // $("#chart1").empty();
    y1 = parseInt($('#ymin').val());
    y2 = parseInt($('#ymax').val());
    ydiff = parseInt($('#ymax').val()) - parseInt($('#ymin').val());
    yhalf = ydiff/2;    // 작게 할 때는 1/2 단위 기준으로 양쪽 한 단위값 추가해서 작게 보이게..
    yquar = ydiff/4;    // 크게 할 때 1/4 단위 기준으로 양쪽 한 단위값 제거해서 크게 보이게
    xmin = parseInt($('#xmin').val());   // 크게 작게 하더라도 x좌표 현재값은 유지되어야 함
    xmax = parseInt($('#xmax').val());   // 크게 작게 하더라도 x좌표 현재값은 유지되어야 함
    if(act=='btn_bigger') {
        ymin = y1 + yquar;
        ymax = y2 - yquar;
        ytick = parseInt((ymax-ymin)/8);     // tickInterval
    }
    else if(act=='btn_smaller') {
        ymin = y1 - yhalf;
        ymax = y2 + yhalf;
        ytick = parseInt((ymax-ymin)/8);     // tickInterval
    }
    else {
        // xmin = null,   // 초기화 x좌표 초기화
        // xmax = null,   // 초기화 x좌표 초기화
        ymin = null;
        ymax = null;
        ytick = null;     // tickInterval
    }

    options.xAxis = {
        min: xmin,
        max: xmax,
        labels: {
            formatter: function() {
                return moment(this.value).format("MM/DD HH:mm");
            }
        },
        events: {
            setExtremes: function (e) {
                $('#xmin').val(e.min);
                $('#xmax').val(e.max);
            }
        }
    };
    options.yAxis = {
        min: ymin,
        max: ymax,
        showLastLabel: true,    // 위 아래 마지막 label 보임 (이게 없으면 끝label이 안 보임)
        scrollbar: {
            enabled: true
        },
        opposite: false,
        tickInterval: ytick,    // 눈금 크기(로그, 대수 형태로 계산한다는 데.. 모르겠다.)
    };
    chart = new Highcharts.stockChart(options);
    removeLogo();

});


// 차트 제거하기
$(document).on('click','.btn_chr_del',function(e){
    e.preventDefault();
    // 차트가 한개뿐이면 제거 안 함
    if(seriesOptions.length<=1) {
        alert('기본 차트는 제거할 수 없습니다.');
        return false;
    }
    var frm = $('#fchart');
    var chr_id_del = frm.find('input[name=chr_id]').val();
    var chr_idx_del = frm.find('input[name=chr_idx]').val();
    if(chr_id_del=='') {
        alert('제거할 차트를 선택하세요.');
        return false;
    }
    else {
        seriesOptions.splice(chr_idx_del,1);
        $('.div_dta_type a[id='+chr_id_del+']').removeAttr('chr_id');
        frm.find('input[name=chr_id]').val('');    // 폼초기화
        createChart();
        $('.chr_control').hide();
    }

});

// 차트 설정 닫기
$(document).on('click','.btn_chr_close',function(e){
    e.preventDefault();
    $('.chr_control').hide();
});


// 로딩 spinner 이미지 표시/비표시
function dta_loading(flag) {
    var img_loading = $('<i class="fa fa-spin fa-circle-o-notch" id="spinner" style="position:absolute;top:80px;left:46%;font-size:4em;color:gray;"></i>');
    if(flag=='show') {
        // console.log('show');
        $('#chart1').append(img_loading);
    }
    else if(flag=='hide') {
        // console.log('hide');
        $('#spinner').remove();
    }
}

// 증폭값 설정
var chr_amp_slider = $( "#chr_amp" ).slider({
    range: "max",
    step: 0.5,
    min: 0.5,
    max: 5,
    value: 1, // Default
    slide: function( event, ui ) {
        $( "#chr_amp_value" ).val( ui.value );
    },
    stop: function( event, ui ) {
        // 증폭 값이 바뀌면 적용
        // console.log(ui.value);
        $('#fchart button[type=submit]').trigger('click');
    }
});
// 증폭값 디폴트값 입력
$( "#chr_amp_value" ).val( $( "#chr_amp" ).slider( "value" ) );

// 이동값 설정
var chr_move_slider = $( "#chr_move" ).slider({
    range: "max",
    min: -200,
    max: 200,
    value: 0,   // Default
    slide: function( event, ui ) {
        $( "#chr_move_value" ).val( ui.value );
    },
    stop: function( event, ui ) {
        // 이동 값이 바뀌면 적용
        // console.log(ui.value);
        $('#fchart button[type=submit]').trigger('click');
    }
});
// 이동값 디폴트값 입력
$( "#chr_move_value" ).val( $( "#chr_move" ).slider( "value" ) );
// setTimeout(function(e){
//     chr_move_slider.slider("option", "min", -88);
//     chr_move_slider.slider("option", "max", 400);
// },2000);


// 대시보드에서 넘어온 페이지 그래프 Default 추가 ====================================================
<?php
// mbd_idx 가 존재하면 저장된 값에서 그래프 추출
// if($mbd_idx) {
    for($j=0;$j<sizeof($mbd['data']);$j++) {
        // print_r2($mbd['data'][$j]);
        ?>
            var graph_id1 = getGraphId('<?=$mbd['data'][$j]['id']['mms_idx']?>','<?=$mbd['data'][$j]['id']['dta_type']?>','<?=$mbd['data'][$j]['id']['dta_no']?>','<?=$mbd['data'][$j]['id']['type1']?>');
            // console.log(graph_id1);
            graphs2[<?=$j?>] = {
                dta_data_url_host: '<?=$mbd['data'][$j]['id']['dta_data_url_host']?>',
                dta_data_url_path: '<?=$mbd['data'][$j]['id']['dta_data_url_path']?>',
                dta_data_url_file: '<?=$mbd['data'][$j]['id']['dta_data_url_file']?>',
                mms_idx: '<?=$mbd['data'][$j]['id']['mms_idx']?>',
                mms_name: "<?=$mbd['data'][$j]['id']['mms_name']?>",
                dta_type: "<?=$mbd['data'][$j]['id']['dta_type']?>",
                dta_no: "<?=$mbd['data'][$j]['id']['dta_no']?>",
                type1: "<?=$mbd['data'][$j]['id']['type1']?>",
                graph_type: "<?=$mbd['data'][$j]['type']?>",
                graph_line: "<?=$mbd['data'][$j]['dashStyle']?>",
                graph_name: "<?=$mbd['data'][$j]['id']['graph_name']?>",
                graph_id: graph_id1
            };
        <?php
    }
    echo '$("#chart1").attr("graphs",JSON.stringify(graphs2));';
    // echo 'console.log( $("#chart1").attr("graphs") );';
    // [확인] 버튼 클릭
    echo "$('#fsearch button[type=submit]').trigger('click');";
// }
?>


// 그래프 불러오기 (팝업모달)
$(document).on('click','.btn_add_chart',function(e){
    e.preventDefault();
    var frm = $('#fsearch');
    var com_idx = '<?=$com['com_idx']?>';
    var mms_idx = '<?=$mms['mms_idx']?>';
    var st_date = frm.find('#st_date').val() || '';
    var st_time = frm.find('#st_time').val() || '';
    var en_date = frm.find('#en_date').val() || '';
    var en_time = frm.find('#en_time').val() || '';
    if(st_date==''||en_date=='') {
        alert('검색 날짜를 입력하세요.');
    }
    else {
        var href = $(this).attr('href');
        winAddChart = window.open(href+'&com_idx='+com_idx+'&sch_field=mms_idx&sch_word='+mms_idx+'&st_date='+st_date+'&st_time='+st_time+'&en_date='+en_date+'&en_time='+en_time,"winAddChart","left=100,top=100,width=520,height=600,scrollbars=1");
        winAddChart.focus();
    }
});
</script>

<script>
$(function(e) {
    // timepicker 설정
    $("input[name$=_time]").timepicker({
        'timeFormat': 'H:i:s',
        'step': 10
    });

    $("input[name$=_date]").datepicker({
        closeText: "닫기",
        currentText: "오늘",
        monthNames: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNamesMin:['일','월','화','수','목','금','토'],
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
        yearRange: "c-99:c+99",
        //maxDate: "+0d"
    });

});    
</script>


<?php
include_once ('./_tail.php');
?>
