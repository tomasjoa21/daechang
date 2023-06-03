<?php
$sub_menu = "935110";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '생산보고서';
include_once('./_top_menu_output.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/style.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/style.css">', 2);
}
if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
</style>

<?php
// Serach for at the top
include_once('./_top_search.php');
?>


<div class="report_wrapper">
    <div class="report_container">

            <div class="div_title_02f"><i class="fa fa-check" aria-hidden="true"> 일자별 생산</i></div>
            <div id="chart_day"></div>
            <div class="div_info_body">

                <table class="table01">
                    <thead class="tbl_head">
                    <tr>
                        <th scope="col" style="width:100px;">구분</th>
                        <th scope="col" style="width:15%">생산</th>
                        <th scope="col" style="width:15%;">OK</th>
                        <th scope="col" style="width:15%;">NG</th>
                        <th scope="col" style="width:15%;">불량율</th>
                    </tr>
                    </thead>
                    <tbody class="tbl_body">
                    <?php
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
                                        work_date AS ymd_date
                                        , COUNT(xry_idx) AS output_total
                                        , SUM( CASE WHEN result = 'OK' THEN 1 ELSE 0 END ) AS output_good
                                        , SUM( CASE WHEN result = 'NG' THEN 1 ELSE 0 END ) AS output_defect
                                    FROM g5_1_xray_inspection
                                    WHERE work_date >= '".$st_date."' AND work_date <= '".$en_date."'
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
                    // echo $sql.'<br>';

                    // // 전체 목표가 아니고 날짜별 목표중에서 최고값 추출
                    // $result = sql_query($sql,1);
                    // for ($i=0; $row=sql_fetch_array($result); $i++) {
                    //     // 합계인 경우는 건너뛰고
                    //     if($row['item_name'] != 'total') {
                    //         $item_target[] = $target['date'][preg_replace("/-/","",$row['item_name'])];
                    //     }
                    // }
                    // // echo max($item_target).'<br>';
                    
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

                        // 비율
                        $row['rate'] = ($output_total) ? $row['output_total'] / $output_total * 100 : 0 ;
                        $row['ng_rate'] = ($row['output_defect']) ? $row['output_defect'] / $row['output_total'] * 100 : 0 ;

                        // First line total skip, start from second line.
                        // if($i>0) {
                            echo '
                            <tr class="'.$row['tr_class'].'">
                                <td class="text_left">'.$row['item_name'].'</td>
                                <td class="text_right pr_5">'.number_format($row['output_total']).'</td><!-- 생산 -->
                                <td class="text_right pr_5">'.number_format($row['output_good']).'</td><!-- 양호 -->
                                <td class="text_right pr_5">'.number_format($row['output_defect']).'</td><!-- 불량 -->
                                <td class="text_right pr_5">'.round($row['ng_rate'],2).' %</td><!-- 불량율 -->
                            </tr>
                            ';
                        // }
                    }
                    if ($i == 0)
                        echo '<tr class="tr_empty"><td class="td_empty" colspan="6">자료가 없습니다.</td></tr>';
                    ?>
                </tbody>
                </table>
            </div><!-- .div_info_body -->    

    </div>
    <div class="report_container">
        <div class="report_left">
        
        </div>
        <div class="report_right">
        
        </div>
    </div>

</div>




<?php
include_once ('./_tail.php');
?>
