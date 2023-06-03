<?php
$sub_menu = "935150";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '조치보고서';
include_once('./_top_menu_stat.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];
$file_name_css_path = G5_USER_ADMIN_STAT_PATH.'/css/'.$g5['file_name'].'.css';
$file_name_css_url = G5_USER_ADMIN_STAT_URL.'/css/'.$g5['file_name'].'.css';

include_once('./_top.stat.php');

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_STAT_URL.'/css/stat.css">', 0);
if(is_file($file_name_css_path)){
    @add_stylesheet('<link rel="stylesheet" href="'.$file_name_css_url.'">', 0);
}
add_javascript('<script src="'.G5_USER_ADMIN_URL.'/js/function.date.js"></script>', 0);
?>
<div id="report_wrapper">
    <div class="div_wrapper">
        <div class="div_one" style="">
            <!-- ========================================================================================= -->
            <div class="div_title_02f"><i class="fa fa-check" aria-hidden="true"> <?=($mode=='week')?'주간별':'월별'?>알람발생량</i></div>
            <div id="chart_monthly" style="height:300px;"></div>
            <table class="table01">
                <thead class="tbl_head">
                <tr>
                    <th scope="col">구분</th>
                    <th scope="col" style="width:15%">발생수</th>
                    <th scope="col" style="width:12%;">비율</th>
                    <th scope="col" style="width:140px;">그래프</th>
                    <th scope="col" style="width:10%">정비수</th>
                </tr>
                </thead>
                <tbody class="tbl_body">
                <?php
                // 주간, 월간 구분 query
                $sql_ymd_unit = ($mode=='week') ? " YEARWEEK(ymd_date,4) AS ymd_unit " : " substring( CAST(ymd_date AS CHAR),1,7) AS ymd_unit " ;
                $sql_arm_unit = ($mode=='week') ? " YEARWEEK(arm_reg_dt,4) AS ymd_unit " : " substring( CAST(arm_reg_dt AS CHAR),1,7) AS ymd_unit " ;
                // 챠트 이름 배열
                $month_values[0]['name'] = '발생수';
                $month_values[1]['name'] = '정비수';
                $sql = "SELECT 
                            ymd_unit
                            , SUM(arm_count_sum) AS arm_count_sum
                        FROM
                        (
                            (
                            SELECT 
                                {$sql_ymd_unit}
                                , 0 AS arm_count_sum
                            FROM g5_5_ymd AS ymd
                            WHERE ymd_date BETWEEN '".$st_date."' AND '".$en_date."'
                            GROUP BY ymd_unit
                            ORDER BY ymd_date
                            )
                            UNION ALL
                            (
                            SELECT {$sql_arm_unit}
                                , COUNT(arm_idx) AS arm_count_sum
                            FROM g5_1_alarm AS arm
                            WHERE arm_reg_dt >= '".$st_date." 00:00:00' AND arm_reg_dt <= '".$en_date." 23:59:59'
                                AND arm.com_idx='".$com_idx."'
                                {$sql_mmses1}
                            GROUP BY ymd_unit
                            ORDER BY ymd_unit
                            )
                        ) AS db_table
                        GROUP BY ymd_unit
                ";
                // echo $sql.'<br>';
                // 최고값 & 합계 추출
                $item_max = array();
                $item_sum = 0;
                $result = sql_query($sql,1);
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    // print_r2($row);
                    $item_max[] = $row['arm_count_sum'];    // 배열중에서 나중에 최고값을 찾으면 됨
                    $item_sum += $row['arm_count_sum'];
                }

                $result = sql_query($sql,1);
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    // print_r2($row);

                    // 비율
                    $row['rate'] = ($item_sum) ? $row['arm_count_sum'] / $item_sum * 100 : 0 ;
                    $row['rate_color'] = '#d1c594';
                    $row['rate_color'] = ($row['rate']>=80) ? '#72ddf5' : $row['rate_color'];
                    $row['rate_color'] = ($row['rate']>=100) ? '#ff9f64' : $row['rate_color'];

                    // 그래프
                    if($item_sum && $row['arm_count_sum']) {
                        // $row['rate_percent'] = $row['arm_count_sum'] / max($item_max) * 100;
                        $row['rate_percent'] = $row['arm_count_sum'] / $item_sum * 100;
                        $row['graph'] = '<img class="graph_output" src="../img/dot.gif" style="width:'.$row['rate_percent'].'%;background:'.$row['rate_color'].';" height="8px">';
                    }

                    // item_name
                    $row['item_name_disp'] = $row['ymd_unit']; // 월간일때 2022-02
                    $due_unit[$i] = $chart_item_names[$i] = $row['ymd_unit']; // 월간(그래프 항목명)
                    if($mode=='week') {
                        $row['year'] = substr($row['ymd_unit'],0,4);
                        $row['week'] = substr($row['ymd_unit'],-2);
                        $due_unit[$i] = $row['ymd_unit']; // 주간(그래프 항목명)
                        $row['week_start_date'] = date('Y-m-d', strtotime($row['year'].'W'.$row['week'])-86400);
                        $row['item_name_disp'] = $chart_item_names[$i] = $row['week_start_date'];   // 주간일때 2022-02-06
                    }

                    // 정비링크 (코드없이 기간만)
                    if($maintain_unit[$row['ymd_unit']]) {
                        // 주간별
                        if($mode=='week') {
                            $row['st_date'] = date('Y-m-d', strtotime($row['year'].'W'.$row['week'])-86400);
                            $row['en_date'] = date('Y-m-d', strtotime($row['year'].'W'.$row['week'])+86400*5);
                        }
                        // 월간
                        else {
                            $row['st_date'] = substr($row['ymd_unit'],0,7).'-01';
                            $row['en_date'] = substr($row['ymd_unit'],0,7).'-31';
                        }
                        $row['en_time'] = '23:59:59';
                        $row['maintin_count'] = '<a href="'.G5_USER_ADMIN_URL.'/maintain_list.php?st_date='
                                                    .$row['st_date'].'&en_date='.$row['en_date'].'&en_time='.$row['en_time'].'" target="_blank">'
                                                    .number_format($maintain_unit[$row['ymd_unit']]).'</a>';
                    }
                    else {
                        $row['maintin_count'] = 0;
                    }

                    // 챠트 값 배열
                    $month_values[0]['data'][$i] = (int)$row['arm_count_sum'];    //발생수
                    $month_values[1]['data'][$i] = (int)$maintain_unit[$row['ymd_unit']];  //정비수

                    echo '
                    <tr class="tr_stat_normal">
                        <td class="text_left">'.$row['item_name_disp'].'</td>
                        <td class="text_right pr_5">'.number_format($row['arm_count_sum']).'</td><!-- 발생수 -->
                        <td class="text_right pr_5">'.number_format($row['rate'],1).'%</td><!-- 비율 -->
                        <td class="td_graph text_left pl_0">'.$row['graph'].'</td>
                        <td class="td_maintain_count text_right pr_5">'.$row['maintin_count'].'</td><!-- 정비수 -->
                    </tr>
                    ';
                }
                // print_r2($month_values);
                if ($i == 0)
                    echo '<tr class="tr_empty"><td class="td_empty" colspan="6">자료가 없습니다.</td></tr>';
                ?>
            </tbody>
            </table>

            <!-- ========================================================================================= -->
            <div class="div_title_02"><i class="fa fa-check" aria-hidden="true"> 코드별알람발생량</i></div>
            <div id="chart_code"></div>
            <div style="display:block;height:1000px;overflow:scroll;overflow-x:hidden;">
                <table class="table01">
                    <thead class="tbl_head">
                    <tr>
                        <th scope="col" rowspan="2" class="td_line_right">알람</th>
                        <th scope="col" rowspan="2" class="td_line_right" style="display:none;">코드</th>
                        <?php
                        // 주
                        for($j=0;$j<sizeof($due_unit);$j++) {
                            // echo $due_unit[$j].'<br>';
                            echo '<th scope="col" colspan="2" class="td_line_right">'.$chart_item_names[$j].'</th>';
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        for($j=0;$j<sizeof($due_unit);$j++) {
                            // echo $due_unit[$j].'<br>';
                            echo '<th scope="col">알람수</th>';
                            echo '<th scope="col" class="td_line_right">정비수</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody class="tbl_body">
                    <?php
                    $sql_arm_unit = '';
                    for($j=0;$j<sizeof($due_unit);$j++) {
                        // echo $due_unit[$j].'<br>';
                        $sql_arm_unit .= ($mode=='week') ? " , SUM( IF ( YEARWEEK(arm_reg_dt,4) = '".$due_unit[$j]."', 1, 0) ) AS '".$due_unit[$j]."' "
                                                        : " , SUM( IF( SUBSTRING( CAST(arm_reg_dt AS CHAR),1,7 ) = '".$due_unit[$j]."', 1, 0) ) AS '".$due_unit[$j]."' " ;
                    }
                    $sql = "SELECT arm_cod_code
                                , COUNT(arm_idx) AS arm_count_sum
                                {$sql_arm_unit}
                            FROM g5_1_alarm AS arm
                            WHERE arm_reg_dt >= '".$st_date." 00:00:00' AND arm_reg_dt <= '".$en_date." 23:59:59'
                                AND arm.com_idx='".$com_idx."'
                                {$sql_mmses1}
                            GROUP BY arm_cod_code
                            ORDER BY COUNT(arm_idx) DESC, arm_cod_code
                            /* LIMIT 10 */
                    ";
                    // echo $sql.'<br>';
                    $result = sql_query($sql,1);
                    for ($i=0; $row=sql_fetch_array($result); $i++) {
                        // print_r2($row);

                        // 챠트 이름 배열 (20개까지만 표시)
                        if($i<10) {
                            // $month_code_values[$i]['name'] = $row['arm_cod_code'];
                            $month_code_values[$i]['name'] = cut_str($cod_name[$row['arm_cod_code']],25);
                        }

                        // 해당 단위를 전부 다 돌면서 td 생성
                        for($j=0;$j<sizeof($due_unit);$j++) {
                            // echo $due_unit[$j].'<br>';
                            // echo $row['arm_cod_code'].'<br>';

                            // 챠트 값 배열 (20개까지만 표시)
                            if($i<10) {
                                $month_code_values[$i]['data'][$j] = (int)$row[$due_unit[$j]];
                            }

                            // 시간 구간
                            if($mode=='week') {
                                $row['year'][$j] = substr($due_unit[$j],0,4);
                                $row['week'][$j] = substr($due_unit[$j],-2);
                                $row['st_date'][$j] = date('Y-m-d', strtotime($row['year'][$j].'W'.$row['week'][$j])-86400);
                                // 시작날짜가 검색구간 바깥에 있는 경우
                                if($row['st_date'][$j]<$st_date) {
                                    $row['st_date'][$j] = $st_date;
                                }
                                // 종료날짜가 검색구간 바깥에 있는 경우
                                $row['en_date'][$j] = date('Y-m-d', strtotime($row['year'][$j].'W'.$row['week'][$j])+86400*5);
                                if($row['en_date'][$j]>$en_date) {
                                    $row['en_date'][$j] = $en_date;
                                }
                            }
                            else {
                                $row['st_date'][$j] = $due_unit[$j].'-01';
                                $row['en_date'][$j] = $due_unit[$j].'-31';
                            }
                            $row['en_time'][$j] = '23:59:59';

                            // 알람링크 (코드포함)
                            if($row[$due_unit[$j]]) {
                                $row['alarm_count'][$j] = '<a href="'.G5_USER_ADMIN_URL.'/alarm_data_list.php?st_date='
                                                            .$row['st_date'][$j].'&en_date='.$row['en_date'][$j].'&en_time='.$row['en_time'][$j]
                                                            .'&sfl=arm_cod_code&stx='.$row['arm_cod_code'].'" target="_blank">'
                                                            .number_format($row[$due_unit[$j]])
                                .'</a>';
                            }
                            else {
                                $row['alarm_count'][$j] = 0;
                            }

                            // 정비링크 (코드포함)
                            if($maintain_unit_code[$due_unit[$j].'-'.$row['arm_cod_code']]) {
                                $row['maintin_count'][$j] = '<a href="'.G5_USER_ADMIN_URL.'/maintain_list.php?st_date='
                                                            .$row['st_date'][$j].'&en_date='.$row['en_date'][$j].'&en_time='.$row['en_time'][$j]
                                                            .'&sfl=mnt_db_code&stx='.$row['arm_cod_code'].'" target="_blank">'
                                                            .number_format($maintain_unit_code[$due_unit[$j].'-'.$row['arm_cod_code']])
                                .'</a>';
                            }
                            else {
                                $row['maintin_count'][$j] = 0;
                            }

                            // echo $due_unit[$j].'<br>';
                            $data_due_unit[$i] .= ' <td class="td_month td_line_right">'.$row['alarm_count'][$j].'</td> ';
                            $data_due_unit[$i] .= ' <td class="td_maintain_count">'.$row['maintin_count'][$j].'</td> ';
                        }

                        echo '
                        <tr class="tr_normal">
                            <td class="text_left td_line_right" title="'.addslashes($cod_name[$row['arm_cod_code']]).'">'.cut_str($cod_name[$row['arm_cod_code']],25).'</td>
                            <td class="text_left td_line_right" title="'.addslashes($cod_name[$row['arm_cod_code']]).'" style="display:none;">'.$row['arm_cod_code'].'</td>
                            '.$data_due_unit[$i].'
                        </tr>
                        ';
                    }
                    // print_r2($month_values);
                    if ($i == 0)
                        echo '<tr class="tr_empty"><td class="td_empty" colspan="6">자료가 없습니다.</td></tr>';
                    ?>
                </tbody>
                </table>
            </div>
        </div><!-- .div_one -->
    </div><!--  .div_wrapper -->
</div><!-- #report_wrapper -->
<script>
$(function(e) {
    $(document).tooltip({
        track: true
    });
});
</script>
<?php
include_once ('./_tail.php');
?>
