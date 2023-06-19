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
include_once ('./_tail.sub.php');
?>
