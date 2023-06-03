<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 링크 주소 업데이트(g5_url 부분 제거, 다른 주소로 접속해도 문제 없도록..)
// 링크 주소 앞에 G5_URL 치환 (여러 도메인으로 옮겨다녀도 제대로 연결되도록 링크 수정)
$sql = " UPDATE {$write_table} SET 
                wr_link1 = '".strip_g5_url($wr_link1)."'
                , wr_link2 = '".strip_g5_url($wr_link2)."'
            WHERE wr_id = '".$wr_id."'
";
sql_query($sql,1);


delete_cache_latest($bo_table);

$qstr .= '&sca='.$sca;
$redirect_url = G5_USER_ADMIN_URL.'/bbs/board.php?bo_table='.$bo_table.'&'.$qstr;
goto_url($redirect_url);
