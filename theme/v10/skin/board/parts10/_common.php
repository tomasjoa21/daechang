<?php
if (!defined('G5_COMMUNITY_USE')) {
    define('_SHOP_', true); // 쇼핑몰
}

$g5_path['path'] = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], '/theme/'));
if(!$g5_path['path'])
    $g5_path['path'] = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], '/bbs/'));
include_once($g5_path['path'].'/common.php');   // 설정 파일
unset($g5_path);


//-- REQUEST 변수 재정의 (변수명이 너무 길어~) --//
if(is_array($_REQUEST)) {
    foreach($_REQUEST as $key=>$val) {
        ${$key} = $_REQUEST[$key];
    }
}

// 디폴트 게시판
$bo_table = ($bo_table)? $bo_table : 'contact';


// 게시판 환경설정값 추출
if ($bo_table) {
    $board = get_board($bo_table);
    
    // wr_id 가 있으면 $write 배열 확장(+serialized 변수들)
    if($wr_id && is_serialized($write['wr_9'])) {
        $write = array_merge($write, get_serialized($write['wr_9']));
    }
}
//print_r3($board);
//print_r3($write);


//print_r3($board);
// 운영관리 가능자 권한 설정은 /extend/user.10.board.php 참조


// 작업 등급보기 가능 아이디
$board['grade_view_ids'] = explode(',', preg_replace("/\s+/", "", $board['bo_2'].',super'));

// 등급관리 조직 코드 (CS팀, 기획팀)
$board['manager_trm_idxs'] = explode(',', preg_replace("/\s+/", "", $board['bo_3']));

// 등급관리 관리 권한
if( in_array($member['mb_id'],$board['grade_view_ids']) || in_array($member['mb_2'],$board['manager_trm_idxs']) )
    $member['board_manager_yn'] = 1;

// 작업관리 조직 코드(개발팀, 디자인팀)
$board['worker_trm_idxs'] = explode(',', preg_replace("/\s+/", "", $board['bo_4']));

// 상태값
$set_values = explode(',', preg_replace("/\s+/", "", $board['bo_9']));
foreach ($set_values as $set_value) {
	list($key, $value) = explode('=', $set_value);
	$g5['set_board_status'][$key] = $value.' ('.$key.')';
	$g5['set_board_status_value'][$key] = $value;
	$g5['set_board_status_radios'] .= '<label for="set_board_status_'.$key.'" class="set_board_status"><input type="radio" id="set_board_status_'.$key.'" name="set_board_status" value="'.$key.'">'.$value.'('.$key.')</label>';
	$g5['set_board_status_checkboxs'] .= '<label for="set_board_status_'.$key.'" class="set_board_status"><input type="hidden" name="set_board_status_'.$key.'" value=""><input type="checkbox" id="set_board_status_'.$key.'">'.$value.'('.$key.')</label>';
	$g5['set_board_status_buttons'] .= '<a href="javascript:" class="set_board_status" cmm_status="'.$key.'">'.$value.'</a>';
	$g5['set_board_status_options'] .= '<option value="'.trim($key).'">'.trim($value).' ('.$key.')</option>';
	$g5['set_board_status_options_value'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
}
unset($set_values);unset($set_value);



?>