<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//여기는 이 게시판에만 해당하는 환경설정 관련 소스 페이지 입니다.
//그래서 /adm/v10/bbs/_common.php 파일 제일 하단에 include한 파일입니다.

$mms = get_table_meta('mms','mms_idx',$view['wr_2']);
$com = get_table_meta('company','com_idx',$mms['com_idx']);

$view = @array_merge($view,$mms);
$view = @array_merge($view,$com);

// towhom_info variable
$wr_alarmlist = json_decode($view['wr_alarm_list'], true);
if(is_array($wr_alarmlist)) {
    foreach($wr_alarmlist as $k1 => $v1) {
        // echo $k1.'<br>';
        // print_r2($v1);
        for($i=0;$i<sizeof($v1);$i++) {
            $towhom_li[$i][$k1] = $v1[$i];
        }
    }
}

$copy_href = '';
$move_href = '';
// // 로그인중이고 자신의 글이라면 또는 관리자라면 비밀번호를 묻지 않고 바로 수정, 삭제 가능
// if (($member['mb_id'] && ($member['mb_id'] === $write['mb_id'])) || preg_match("/d/",$auth[$sub_menu]) || $is_admin) {
//     // $delete_href = $board_skin_url.'/delete.user.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;token='.$token.'&amp;page='.$page.urldecode($qstr);
//     $delete_href = G5_USER_ADMIN_BBS_URL.'/delete.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id.'&amp;token='.$token.'&amp;page='.$page.urldecode($qstr);
// }
