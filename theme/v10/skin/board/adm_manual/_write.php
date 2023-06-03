<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//여기는 이 게시판에만 해당하는 환경설정 관련 소스 페이지 입니다.
//그래서 /adm/v10/bbs/_common.php 파일 제일 하단에 include한 파일입니다.

$mms = get_table_meta('mms','mms_idx',$write['wr_2']);
$com = get_table_meta('company','com_idx',$mms['com_idx']);

if(!$mms['mms_idx']) {
    $write['mms_info'] = '선택된 설비가 없습니다. 설비를 선택하세요.';
}

$wr_alarmlist = json_decode($write['wr_alarm_list'], true);
if(is_array($wr_alarmlist)) {
    foreach($wr_alarmlist as $k1 => $v1) {
        // echo $k1.'<br>';
        // print_r2($v1);
        for($i=0;$i<sizeof($v1);$i++) {
            $towhom_li[$i][$k1] = $v1[$i];
        }
    }
}

$write = @array_merge($write,$mms);
$write = @array_merge($write,$com);

