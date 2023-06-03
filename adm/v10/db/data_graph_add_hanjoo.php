<?php
// 호출페이지들
// /adm/v10/data_measure_real_chart.php: 차트추가하기
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

// 권한이 없는 경우는 자기것만 리스트

// com_idx가 있는 경우
$com_idx = $com_idx ?: $_SESSION['ss_com_idx'];
$com = get_table_meta('company','com_idx',$com_idx);

// 시작, 종료를 timestamp 값으로!!
$st_time = $st_time ?: '00:00:00';
$en_time = $en_time ?: '23:59:59';
$start_dt = $st_date.' '.$st_time;
$end_dt = $en_date.' '.$en_time;
$start = strtotime($start_dt);
$end = strtotime($en_dt);


// 설비 정보 호출
$sql_common = " FROM {$g5['mms_table']} AS mms
                    LEFT JOIN {$g5['company_table']} AS com ON com.com_idx = mms.com_idx
";
$where = array();
// 디폴트 검색조건
$where[] = " mms_status NOT IN ('trash','delete') ";
$where[] = " imp_idx != 31 ";

// 업체조건
$where[] = " mms.com_idx = '".$com_idx."' ";


// 검색어 설정
if ($sch_word != "") {
    switch ($sch_field) {
		case ( $sch_field == 'com_idx' || $sch_field == 'imp_idx' || $sch_field == 'mms_idx' ) :
			$where[] = " $sch_field = '".trim($sch_word)."' ";
            break;
        default :
			$where[] = " $sch_field LIKE '%".trim($sch_word)."%' ";
            break;
    }
}
else
    $sch_field = 'mms_name';

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);


// 정렬기준
$sql_order = " ORDER BY mms_idx ";


// 테이블의 전체 레코드수
$sql = " SELECT COUNT(*) AS cnt " . $sql_common . $sql_search;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$config['cf_write_pages'] = $config['cf_mobile_pages'] = 5;

// 리스트 쿼리
$sql = "SELECT *
        " . $sql_common . $sql_search . $sql_order . "
        LIMIT $from_record, $rows
";
// echo $sql;
$result = sql_query($sql,1);

$qstr0 = 'frm='.$frm.'&file_name='.$file_name.'&com_idx='.$com_idx;
$qstr1 = '&st_date='.$st_date.'&st_time='.$st_time.'&en_date='.$en_date.'&en_time='.$en_time;
$qstr = $qstr0.$qstr1.'&sch_field='.$sch_field.'&sch_word='.urlencode($sch_word);

$g5['title'] = $com['com_name'].' 그래프 추가';
include_once('./_head.sub.php');
?>
<style>
.div_title {position:relative;height:30px;line-height:30px;background:#2b2b2b;color:white;padding-left:5px;}
.div_title span b {font-weight:normal;font-size:0.9em;color:#818181;}
.div_title span b:after {content:':';margin-right:3px;}
.div_title .spsn_mms_name {font-size:1.2em;}
.div_title .spsn_mms_name:before {content:'\2713';margin-right:5px;}
.div_title .spsn_mms_group {position:absolute;top:1px;right:10px;}
.btn_mesaure, .btn_product {
    cursor:pointer;margin:2px 2px 2px 0;
    border: solid 1px #ddd;
    border-radius: 15px;
    padding: 0 10px;
    background: #fff;
}
.div_measure, .div_product {padding:2px 0;}
.div_product {margin-top:7px;}
.div_measure {margin:10px 5px 30px;}
.btn_mesaure:hover, .btn_product:hover {color:yellow;background: #000;}
#spinner {display:none;}
.tbl_head01 tbody td {
    text-align:left;
    border-left:none;
    border-right:none;
}
.div_measure span {display:inline-block;}
#scp_list_find {border: none;text-align:center;}
.new_win_con {padding: 0px;}
.tbl_head01 tbody td {padding:0;}
.btn_fixed_top {top: 10px;}
</style>

<div id="sch_target_frm" class="new_win scp_new_win">
    <h1 id="g5_title"><?php echo $g5['title'];?></h1>

    <form name="ftarget" method="get">
    <input type="hidden" name="frm" value="<?php echo $_GET['frm']; ?>">
    <input type="hidden" name="file_name" value="<?php echo $_REQUEST['file_name']; ?>">
    <input type="hidden" name="com_idx" value="<?php echo $_REQUEST['com_idx']; ?>">
    <input type="hidden" name="st_date" value="<?php echo $_REQUEST['st_date']; ?>">
    <input type="hidden" name="st_time" value="<?php echo $_REQUEST['st_time']; ?>">
    <input type="hidden" name="en_date" value="<?php echo $_REQUEST['en_date']; ?>">
    <input type="hidden" name="en_time" value="<?php echo $_REQUEST['en_time']; ?>">
    <input type="hidden" name="sch_field" value="mms_idx">

    <div id="scp_list_find">
        <select name="sch_word" id="sch_word" style="width:380px;">
            <option value="">전체설비</option>
            <?php
            // 해당 범위 안의 모든 설비를 select option으로 만들어서 선택할 수 있도록 한다.
            // Get all the mms_idx values to make them optionf for selection.
            $sql2 = "   SELECT mms_idx, mms_name
                        FROM {$g5['mms_table']}
                        WHERE com_idx = '".$com_idx."' AND imp_idx != 31
                        ORDER BY mms_idx
            ";
            // echo $sql2.'<br>';
            $result2 = sql_query($sql2,1);
            for ($i=0; $row2=sql_fetch_array($result2); $i++) {
                // print_r2($row2);
                echo '<option value="'.$row2['mms_idx'].'" '.get_selected($ser_mms_idx, $row2['mms_idx']).'>'.$row2['mms_name'].'</option>';
            }
            ?>
        </select>
        <script>$('select[name=sch_word]').val('<?php echo $sch_word?>').attr('selected','selected')</script>
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    
    <div class="local_desc01 local_desc" style="display:no ne;">
        <p>항목을 클릭하면 그래프가 추가됩니다.</p>
        <p>기간 선택 범위 및 좌표갯수에 <span class="color_red">그래프 로딩 시간이 꽤 많이</span> 걸릴 수 있습니다.</p>
    </div>

    <div class="tbl_head01 tbl_wrap new_win_con">
        <i class="fa fa-spin fa-circle-o-notch" id="spinner" style="position:absolute;top:280px;left:46%;font-size:4em;z-index:100;"></i>
        <table>
        <caption>검색결과</caption>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
            $row['mta'] = get_meta('mms',$row['mms_idx']);  // 측정값 레이블형식: [dta_type_label-1-1] => 좌측온도
            $row['group'] = get_table_meta('mms_group','mmg_idx',$row['mmg_idx']);
            // print_r2($row);

            // 해당 기간 온도값 추출
            $sql1 = "   SELECT machine_id, MAX(hold_temp) AS hold_temp_max, MAX(upper_heat) AS upper_heat_max, MAX(lower_heat) AS lower_heat_max
                            , MAX(upper_1_temp) AS upper_1_temp_max, MAX(upper_2_temp) AS upper_2_temp_max, MAX(upper_3_temp) AS upper_3_temp_max, MAX(upper_4_temp) AS upper_4_temp_max, MAX(upper_5_temp) AS upper_5_temp_max, MAX(upper_6_temp) AS upper_6_temp_max
                            , MAX(lower_1_temp) AS lower_1_temp_max, MAX(lower_2_temp) AS lower_2_temp_max, MAX(lower_3_temp) AS lower_3_temp_max
                        FROM g5_1_cast_shot_sub
                        WHERE event_time >= '".$start_dt."' AND event_time <= '".$end_dt."'
                            AND machine_id = '".$row['mms_idx2']."'
                        GROUP BY machine_id
            ";
            // echo $sql1.'<br>';
            $stmt = sql_query_pg($sql1,1);
            $one[$i] = $stmt->fetch(PDO::FETCH_ASSOC);
            // $one[$i] = sql_fetch($sql1,1);
            // print_r2($one[$i]);
            if(is_array($one[$i])) {
                foreach($one[$i] as $k1=>$v1) {
                    if($k1=='machine_id') {continue;} // 건너뛰는 필드
                    if(!$v1) {continue;} // 값이 없으면(제로) 건너뜀
                    // echo $k1.'/'.$v1.'<br>';
                    // if( $g5['set_data_name'][preg_replace("/_max/","",$k1)] ) {
                    //     echo $g5['set_data_name_value'][preg_replace("/_max/","",$k1)].'<br>';
                    // }
                    $one[$i]['data_name_code'] = preg_replace("/_max/","",$k1);
                    $one[$i]['data_name_text'] = $g5['set_data_name_value'][$one[$i]['data_name_code']];
                    // 레이블값
                    $one[$i]['dta_label'] = $one[$i]['data_name_text'] ? $one[$i]['data_name_text'] : $k1;
                    // echo $one[$i]['dta_label'].'<br>';
                    // 차트(그래프) 항목 배열
                    $row['charts'][] = '<span class="btn_mesaure" mms_idx="'.$row['mms_idx'].'" mms_name="'.$row['mms_name'].'" '
                                        .'mms_data_url_host="'.$row['mms_data_url_host'].'" '
                                        .'mms_data_url_path="/device/rdb" '
                                        .'mms_data_url_file="shot_sub.php" '
                                        .'dta_type="'.$one[$i]['data_name_code'].'" dta_no="'.$row1['dta_no'].'" '
                                        .'graph_name="'.$row['mms_name'].' '.$one[$i]['data_name_text'].'" '
                                        .'>'.$one[$i]['dta_label'].'</span>';
                }
            }
    
            // 압력
            $sql1 = "   SELECT machine_id, MAX(detect_pressure) AS detect_pressure_max, MAX(target_pressure) AS target_pressure_max
                            , MAX(control_pressure) AS control_pressure_max
                            , MAX(deviation_pressure) AS deviation_pressure_max
                        FROM g5_1_cast_shot_pressure
                        WHERE event_time >= '".$start_dt."' AND event_time <= '".$end_dt."'
                            AND machine_id = '".$row['mms_idx2']."'
                        GROUP BY machine_id
            ";
            // echo $sql1.'<br>';
            $stmt = sql_query_pg($sql1,1);
            $one[$i] = $stmt->fetch(PDO::FETCH_ASSOC);

            // $one[$i] = sql_fetch($sql1,1);
            // print_r2($one[$i]);
            if(is_array($one[$i])) {
                foreach($one[$i] as $k1=>$v1) {
                    if($k1=='machine_id') {continue;} // 건너뛰는 필드
                    if(!$v1) {continue;} // 값이 없으면(제로) 건너뜀
                    // echo $k1.'/'.$v1.'<br>';
                    $one[$i]['data_name_code'] = preg_replace("/_max/","",$k1);
                    $one[$i]['data_name_text'] = $g5['set_data_name_value'][$one[$i]['data_name_code']];
                    // 레이블값
                    $one[$i]['dta_label'] = $one[$i]['data_name_text'] ? $one[$i]['data_name_text'] : $k1;
                    // echo $one[$i]['dta_label'].'<br>';
                    // 차트(그래프) 항목 배열
                    $row['charts'][] = '<span class="btn_mesaure" mms_idx="'.$row['mms_idx'].'" mms_name="'.$row['mms_name'].'" '
                                        .'mms_data_url_host="'.$row['mms_data_url_host'].'" '
                                        .'mms_data_url_path="/device/rdb" '
                                        .'mms_data_url_file="shot_pressure.php" '
                                        .'dta_type="'.$one[$i]['data_name_code'].'" dta_no="'.$row1['dta_no'].'" '
                                        .'graph_name="'.$row['mms_name'].' '.$one[$i]['data_name_text'].'" '
                                        .'>'.$one[$i]['dta_label'].'</span>';
                }
            }
    
        ?>
        <tr>
            <td>
                <div class="div_title">
                    <span class="spsn_mms_name"><?php echo $row['mms_name']; ?></span>
                    <span class="spsn_mms_model">(<?php echo $row['mms_model']; ?>)</span>
                    <span class="spsn_mms_group"><b>위치</b><?php echo $row['group']['mmg_name']; ?></span>
                </div>
                <div class="div_measure">
                    <?=(is_array($row['charts']))?implode(" ",$row['charts']):'그래프 없음'?>
                </div>
            </td>
        </tr>
        <?php
        }
        if($i ==0)
            echo '<tr><td colspan="6" class="empty_table">검색된 자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
    </form>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

    <div class="win_btn ">
        <button type="button" onclick="window.close();" class="btn btn_close">창닫기</button>
        <a href="javascript:" class="btn btn_test" style="display:<?=($member['mb_level']<8)?'none':''?>;">테스트</a>
    </div>

</div>

<script>
var graphs2 =[];
// // get graphs attribute for graph info from opener chart div
// if( $("#chart1", opener.document).attr("graphs") != undefined ) {
//     graphs =  JSON.parse( $("#chart1", opener.document).attr("graphs") );
//     for(i=0;i<graphs.length;i++) {
//         graphs2[i] = graphs[i];
//     }
// }
// console.log(graphs2);
// idx = graphs2.length; // graph count

// 아래 전역 변수들를 만들어 두고 바꿔가면서 applyGraphs()함수를 실행하여 graphs 배열변수를 생성(추가)한다.
var dta_data_url_host;
var dta_data_url_path;
var dta_data_url_file;
var dta_group;  // mea, product, run, error
var mms_idx;
var mms_name;
var dta_type;  // 
var dta_no;    // 
var shf_no = 0;
var dta_mmi_no = 0;
var dta_defect = "0,1";
var dta_defect_type = 0;
var dta_code = "";
var graph_type = "spline";
var graph_line = "solid";
var graph_name = "Graph";
// console.log(window.opener.graphs);

$(function() {
    // for testing.
    $(".btn_test").click(function() {
        var tests =[];
        tests[0] = {
            dta_data_url_host: "icmms.co.kr/device/json",
            dta_no: 0,
        };
        tests[1] = {
            dta_data_url_host: "icmms.co.kr/device/json",
            dta_no: 1,
        };
        $("#chart1", opener.document).attr("tests",JSON.stringify(tests) );
        window.close();
    });

    // btn company click.
    $("#btn_company").click(function() {
        var href = $(this).attr("href");
        winCompany = window.open(href, "winCompany", "left=70,top=70,width=520,height=600,scrollbars=1");
        winCompany.focus();
        return false;
    });

    // 측정그래프 추가
    $(document).on('click','.btn_mesaure',function(e){
        e.preventDefault();
        dta_data_url_host = $(this).attr('mms_data_url_host') || "<?=$g5['set_data_url_host']?>";
        dta_data_url_path = $(this).attr('mms_data_url_path');
        dta_data_url_file = $(this).attr('mms_data_url_file');
        mms_idx = $(this).attr('mms_idx');
        mms_name = encodeURIComponent($(this).attr('mms_name'));
        dta_type = $(this).attr('dta_type');  // 
        dta_no = $(this).attr('dta_no');    // 
        graph_name = encodeURIComponent($(this).attr('graph_name'));
        $('#spinner').show();

        // get graphs attribute for graph info from opener chart div
        if( $("#chart1", opener.document).attr("graphs") != undefined ) {
            graphs =  JSON.parse( $("#chart1", opener.document).attr("graphs") );
            for(i=0;i<graphs.length;i++) {
                graphs2[i] = graphs[i];
            }
        }
        var chr_idx = graphs2.length; // graph count
        var graph_id1 = getGraphId(mms_idx,dta_type,dta_no);
        for(i=0;i<graphs2.length;i++) {
            // console.log(i);
            // console.log(graphs[i].dta_data_url_host);
            if( graph_id1 == graphs2[i].graph_id) {
                chr_idx = i;
            }
        }
        // console.log(chr_idx);
        applyGraphs(chr_idx);

        $("#fsearch button[type=submit]", opener.document).trigger('click');
        setTimeout(function(e){
            console.log( $("#chart1", opener.document).attr("graphs") );
            window.close();
        }, 400);

    });

});

// graphs 배열 변수 업데이트
function applyGraphs(idx) {
    var graph_id1 = getGraphId(mms_idx,dta_type,dta_no);
    graphs2[idx] = {
        dta_data_url_host: dta_data_url_host,
        dta_data_url_path: dta_data_url_path,
        dta_data_url_file: dta_data_url_file,
        mms_idx: mms_idx,
        mms_name: mms_name,
        dta_type: dta_type,
        dta_no: dta_no,
        graph_type: graph_type,
        graph_line: graph_line,
        graph_name: graph_name,
        graph_id: graph_id1
    };
    $("#chart1", opener.document).attr("graphs",JSON.stringify(graphs2));
}

</script>

<br><br>
<?php
include_once('./_tail.sub.php');
?>