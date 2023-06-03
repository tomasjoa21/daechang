<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//$sql_search에 조건절을 추가하는 방식으로 값을 지정해라
//예) $sql_search .= ($sql_search != '')?'WHERE (1)'.implode(' AND ', $where):implode(' AND ', $where);

$where = array();
$where[] = " wr_10 NOT IN ('trash','delete') ";   // 디폴트 검색조건

if($sfl=='mms_name') {
    // $where2[] = " wr_8 REGEXP 'mms_name=[가-힝]*(".trim($stx).")+[가-힝]*:' ";
    // $where2[] = " wr_8 LIKE '%".trim($stx)."%' ";
    $mms_sql = " SELECT mms_idx FROM {$g5['mms_table']} WHERE mms_name LIKE '%".trim($stx)."%' AND com_idx = '".$_SESSION['ss_com_idx']."' AND mms_status = 'ok' ";
    $where[] = " wr_2 IN (".$mms_sql.") ";
}
else if($sfl == 'wr_subject' && $stx){
    $where[] = " wr_subject LIKE '%".$stx."%' ";
}
else if($sfl == 'wr_content' && $stx){
    $where[] = " wr_content LIKE '%".$stx."%' ";
}
else if($sfl == 'wr_2' && $stx){
    $where[] = " wr_2 = '".$stx."' ";
}

if ($ser_wr_10) {
    $where[] = " wr_10 = '{$ser_wr_10}' ";
}

if($where){
    if($sql_search) {
        $sql_search .= implode(' AND ', $where);
    }
    else {
        $sql_search = implode(' AND ', $where);
    }
}

/*
// 관리자 레벨이 아니면 자기 업체 것만 리스트에 나옴
if ($member['mb_level']<9) {
    $where[] = " wr_1 IN (".$member['mb_4'].") ";
}
// 관리자인 경우는 com_idx가 있을 때만 검색조건
else if ($ser_com_idx) {
    $where[] = " wr_1 IN (".$ser_com_idx.") ";
}
// 추가 다른 조건들 
// wr_2 = mms_idx
if($ser_wr_2) {
    $where[] = " wr_2 = '".$ser_wr_2."' ";
}
// wr_5 = AS업체명
if($ser_wr_5) {
    $where[] = " wr_5 LIKE '%".$ser_wr_5."%' ";
}
// wr_6 = AS업체주소
if($ser_wr_6) {
    $where[] = " wr_6 LIKE '%".$ser_wr_6."%' ";
}
// wr_7 = AS담당자명
if($ser_wr_7) {
    $where[] = " wr_7 LIKE '%".$ser_wr_7."%' ";
}

if($sfl=='mms_name') {
    // $where2[] = " wr_8 REGEXP 'mms_name=[가-힝]*(".trim($stx).")+[가-힝]*:' ";
    // $where2[] = " wr_8 LIKE '%".trim($stx)."%' ";
    $where[] = " wr_8 REGEXP 'mms_name=.*".trim($stx).".*:' ";
}
if($sch_mb_asign_worker=='pending') {
    $where[] = " wr_8 LIKE '%:mb_name_worker=:%' OR wr_8 = '' ";
}
else if($sch_mb_asign_worker=='asigned') {
    $where[] = " wr_8 REGEXP 'mb_name_worker=[가-힝]+:' ";
}
*/