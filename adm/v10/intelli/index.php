<?php
$sub_menu = "920110";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '실시간모니터링';
// include_once('./_top_menu_db.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];

// 검색 조건
$st_time_ahead = 3600*3;  // 5hour ahead.

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

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/intelli/style.css">', 2);
if(is_file(G5_USER_ADMIN_PATH.'/css/intelli/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/intelli/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
.dates {position:absolute;top:-36px;left:0px;padding-left:5px;font-size:0.8em;}
.xbuttons {position:absolute;top:-36px;right:0px;}
.xbuttons a{font-size:0.8em;}
#graph_wrapper {margin-top:40px;}
.graph_wrap {position:relative;}
</style>

<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/themes/high-contrast-dark.js"></script>
<!-- 다양한 시간 표현을 위한 플러그인 -->
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get" style="display:none;">
    <input type="text" name="st_date" value="<?=$st_date?>" id="st_date" class="frm_input" autocomplete="off" style="width:80px;" >
    <input type="text" name="st_time" value="<?=$st_time?>" id="st_time" class="frm_input" autocomplete="off" style="width:65px;">
    ~
    <input type="text" name="en_date" value="<?=$en_date?>" id="en_date" class="frm_input" autocomplete="off" style="width:80px;">
    <input type="text" name="en_time" value="<?=$en_time?>" id="en_time" class="frm_input" autocomplete="off" style="width:65px;">
    <button type="submit" class="btn btn_01 btn_search">확인</button>
</form>


<div id="graph_wrapper">
    <div class="graph_wrap">
        <div class="dates">
            <?=substr($st_date,5).' '.$st_time?> ~ <?=substr($en_date,5).' '.$en_time?>
        </div>
        <div class="xbuttons">
            <a href="./output_graph.php">더보기</a>
        </div>
        <!-- 차트 -->
        <div id="chart1">
            <span class="text01">[불러오기] 버튼을 클릭하여 그래프를 추가하세요.</span>
        </div>
    </div>
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
                },
                load: function() {
                    $('#xmin').val(this.xAxis[0].min);
                    $('#xmax').val(this.xAxis[0].max);
                    $('#ymin').val(this.yAxis[0].min);
                    $('#ymax').val(this.yAxis[0].max);
                    console.log('load');
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
                enabled: false
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
                dataGrouping: {
                    enabled: false, // dataGrouping 안 함 (range가 변경되면 평균으로 바뀌어서 헷갈림)
                },
                marker: {
                    enabled: true   // point dot display
                }
            }
        },

        navigator: {
            enabled: false,
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
                enabled: false, // contextButton (인쇄, 다운로드..) 설정 (기본옵션 사용자들에게는 안 보이게!!)
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

<?php
$sql = "SELECT * 
        FROM g5_1_xray_inspection AS xry
        LEFT JOIN g5_1_qr_cast_code AS qrc USING(qrcode)
        WHERE start_time >= '".$st_date.' '.$st_time."' AND start_time <= '".$en_date.' '.$en_time."' 
        ORDER BY xry_idx DESC
";
$result = sql_query($sql,1);
?>
<div class="tbl_head01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<thead>
	<tr>
		<th scope="col">Idx</th>
		<th scope="col">작업일</th>
		<th scope="col">종료시각</th>
		<th scope="col">설비번호</th>
		<th scope="col">QRCode</th>
		<th scope="col">결과</th>
		<th scope="col">주조코드</th>
		<th scope="col">주조기</th>
		<th scope="col">주조시각</th>
	</tr>
	</thead>
	<tbody class="tbl_body">
	<?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

		// 검사포인트
		for($j=0;$j<19;$j++) {
			$row['points_br'] = ($j%9==0 && $j>0) ? '<br>':'';
			$row['points'] .= '<a href="?'.$qstr.'&sfl=position_'.$j.'&stx='.$row['position_'.$j].'">'.$row['position_'.$j].'</a> '.$row['points_br'];
		}

		// 스타일
		// $row['tr_bgcolor'] = ($i==0) ? '#fff7ea' : '' ;
		// $row['tr_color'] = ($i==0) ? 'blue' : '' ;

        $s_mod_a = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&xry_idx='.$row['xry_idx'].'">';
        $s_mod = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=u&xry_idx='.$row['xry_idx'].'" class="btn btn_03">수정</a>';
        $s_copy = '<a href="./'.$fname.'_form.php?'.$qstr.'&w=c&xry_idx='.$row['xry_idx'].'" class="btn btn_03">복제</a>';

        echo '
			<tr tr_id="'.$i.'" style="background-color:'.$row['tr_bgcolor'].';color:'.$row['tr_color'].'">
				<td>'.$row['xry_idx'].'</td>
				<td>'.$row['work_date'].'</td>
				<td>'.substr($row['end_time'],0,19).'</td>
				<td>'.$row['machine_no'].'</td>
				<td>'.$row['qrcode'].'</td>
				<td>'.$row['result'].'</td>
				<td>'.$row['cast_code'].'</td>
				<td>'.$g5['mms'][$row['mms_idx']]['mms_name'].'</td>
				<td>'.$row['event_time'].'</td>
			</tr>
		';
	}
	if ($i == 0)
		echo '<tr class="no-data"><td colspan="15" class="text-center">등록(검색)된 자료가 없습니다.</td></tr>';
	?>
    </tbody>
    </table>
</div>
<!-- //리스트 테이블 -->

<?php
include_once ('./_tail.php');
?>
