<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//여기는 이 게시판에만 해당하는 환경설정 관련 소스 페이지 입니다.
//그래서 /adm/v10/bbs/_common.php 파일 제일 하단에 include한 파일입니다.

for($i=0;$i<count($list);$i++){
    $mms = get_table_meta('mms','mms_idx',$list[$i]['wr_2']);
    $com = get_table_meta('company','com_idx',$mms['com_idx']);
    $list[$i] = @array_merge($list[$i],$mms);
    $list[$i] = @array_merge($list[$i],$com);

    $list[$i]['wr_alarm_list'] = json_decode($list[$i]['wr_alarm_list'], true);
    // print_r2($list[$i]['wr_7']);
    if( is_array($list[$i]['wr_alarm_list']) ) {
        foreach($list[$i]['wr_alarm_list'] as $k1 => $v1) {
            for($j=0;$j<@sizeof($v1);$j++) {
                $list[$i]['wr_reports'][$j][$k1] = $v1[$j];
            }
        }
    }
}