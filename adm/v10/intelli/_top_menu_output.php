<?php
if (!defined('_GNUBOARD_')) exit;
// 카테고리 관리 상단 공통 탭 링크들입니다.

${'active_'.$g5['file_name']} = ' btn_top_menu_active';

// 최고관리자인 경우만
if($member['mb_level']>=9) {
    // $sub_title_list = ' <a href="'.G5_BBS_URL.'/board.php?bo_table=setting1" class="btn_top_menu '.$active_term_list.'">환경설정게시판</a>
    // ';
}

$g5['container_sub_title'] = '
<h2 id="container_sub_title">
    <a href="./output_list.php" class="btn_top_menu '.$active_output_list.'">제품현황</a>
    <a href="./qr_cast_list.php" class="btn_top_menu '.$active_qr_cast_list.'">주조코드조회</a>
    <a href="./best_list.php" class="btn_top_menu '.$active_best_list.'">최적파라메타</a>
    <a href="./output_graph.php" class="btn_top_menu '.$active_output_graph.'">생산현황그래프</a>
	'.$sub_title_list.'
</h2>
';
?>
