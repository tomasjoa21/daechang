<?php
include_once('./_common.php');

// print_r2($_REQUEST);
// 변수 재설정
if($_REQUEST['break_start_time']) {
    $_REQUEST['break_start_time'] = date("H:i:s", strtotime($_REQUEST['break_start_time']));
}
if($_REQUEST['set_policy_content']) {
    $_REQUEST['set_policy_content'] = base64_encode($_REQUEST['set_policy_content']);
}

// REQUEST 변수 재정의
// while( list($key, $val) = each($_REQUEST) ) {
//     // echo $key.'='.$_REQUEST[$key].'<br>';
// 	if(substr($key,0,3)=='set') {
//     	$a[$key] = $_REQUEST[$key];
//     }
// }
// $bo_7 = addslashes(serialize($a));
// unset($a);


// 게시판 변수 수정 (개인 정보가 포함되므로 목록을 제외한 나머지 권한은 무조건 8 이상으로 설정해서 일반인들이 못 보게 해야 됨)
// 필요 시 입력해 주세요.
$sql_board = '  bo_read_level = 8
                , bo_write_level = 8
                , bo_reply_level = 8
                , bo_comment_level = 8
                , bo_link_level = 8
                , bo_upload_level = 8
                , bo_download_level = 8
                , bo_html_level = 8
';

$sql = "UPDATE {$g5['board_table']} SET 
            bo_1_subj = '".$bo_1_subj."'
            , bo_2_subj = '".$bo_2_subj."'
            , bo_3_subj = '".$bo_3_subj."'
            , bo_4_subj = '".$bo_4_subj."'
            , bo_5_subj = '".$bo_5_subj."'
            , bo_6_subj = '".$bo_6_subj."'
            , bo_7_subj = '".$bo_7_subj."'
            , bo_8_subj = '".$bo_8_subj."'
            , bo_9_subj = '".$bo_9_subj."'
            , bo_10_subj = '".$bo_10_subj."'
            , bo_1 = '".$bo_1."'
            , bo_2 = '".$bo_2."'
            , bo_3 = '".$bo_3."'
            , bo_4 = '".$bo_4."'
            , bo_5 = '".$bo_5."'
            , bo_6 = '".$bo_6."'
            , bo_7 = '".$bo_7."'
            , bo_8 = '".$bo_8."'
            , bo_9 = '".$bo_9."'
            , bo_10 = '".$bo_10."'
        WHERE bo_table = '".$bo_table."'
";
//echo $sql.'<br>';
sql_query($sql,1);


//-- 필드명 추출 wr_ 와 같은 앞자리 3자 추출 --//
$r = sql_query(" desc {$g5['board_table']} ");
while ( $d = sql_fetch_array($r) ) {$db_fields[] = $d['Field'];}
$db_prefix = substr($db_fields[0],0,2);
// print_r2($_REQUEST);
//-- 메타 입력 (디비에 있는 설정된 값은 입력하지 않는다.) --//
$db_fields[] = "bo_zip";	// 건너뛸 변수명은 배열로 추가해 준다.
$db_fields[] = "bo_sido_cd";	// 건너뛸 변수명은 배열로 추가해 준다.
foreach($_REQUEST as $key => $value ) {
	//-- 해당 테이블에 있는 필드 제외하고 테이블 prefix 로 시작하는 변수들만 업데이트 --//
	if(!in_array($key,$db_fields) && substr($key,0,2)==$db_prefix) {
		// echo $key."=".$_REQUEST[$key]."<br>";
		meta_update(array("mta_db_table"=>"board/".$bo_table,"mta_db_id"=>$bo_table,"mta_key"=>$key,"mta_value"=>$value));
	}
}


// exit;
goto_url('./config_form.php?bo_table='.$bo_table, false);
?>