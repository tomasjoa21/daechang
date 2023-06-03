<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
//여기는 이 게시판에만 해당하는 환경설정 관련 소스 페이지 입니다.
//그래서 /adm/v10/bbs/_common.php 파일 제일 하단에 include한 파일입니다.
// wr_id 값이 있으면 글읽기
if ((isset($wr_id) && $wr_id) || (isset($wr_seo_title) && $wr_seo_title)) {

}
// print_r3($write);