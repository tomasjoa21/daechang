<?php
header("Content-Type: text/plain; charset=utf-8");
include_once('./_common.php');
if(isset($_SERVER['HTTP_ORIGIN'])){
 header("Access-Control-Allow-Origin:{$_SERVER['HTTP_ORIGIN']}");
 header("Access-Control-Allow-Credentials:true");
 header("Access-Control-Max-Age:86400"); //cache for 1 day
}

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
 if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
  header("Access-Control-Allow-Methods:GET,POST,OPTIONS");
 if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
  header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 exit(0);
}

//-- 디폴트 상태 (실패) --//
$response = new stdClass();
$response->result=false;

// print_r2($_REQUEST);

// 그래프 내보내기
if ($aj == "put") {

    // from변수 serialize된 변수를 분리
	$params = array();
	parse_str($frm_data, $params);
	foreach($params as $key => $value) {
		${$key} = $value;
    }

    // 그래프 이름 생성
    for($i=0;$i<sizeof($data_series);$i++) {
        $dta_names[] = $data_series[$i]['name'];
    }
    $dta_name = implode(", ",$dta_names);
    // echo $dta_name.'<br>';
    
    // data_series 정보 분리
    // print_r2($data_series).'<br>';
    $data_series = json_encode($data_series, JSON_UNESCAPED_UNICODE);
    // echo $data_series.'<br>';

    // 디폴트 대시보드에 추출
    $sql = " SELECT mta_idx,mta_value,mta_title,mta_number
                    FROM {$g5['meta_table']} 
                    WHERE mta_db_table = 'member' 
                        AND mta_db_id = '{$member['mb_id']}'
                        AND mta_key = 'dashboard_menu'
                    ORDER BY mta_number
                    LIMIT 1
    ";
    // echo $sql;
    $mta = sql_fetch($sql,1);

    // 설정 정보
    $mbd_setting = '';
    $mbd_setting = serialized_update('graph_name',$dta_name,$mbd_setting);
    $mbd_setting = serialized_update('st_date',$st_date,$mbd_setting);
    $mbd_setting = serialized_update('st_time',$st_time,$mbd_setting);
    $mbd_setting = serialized_update('en_date',$en_date,$mbd_setting);
    $mbd_setting = serialized_update('en_time',$en_time,$mbd_setting);
    $mbd_setting = serialized_update('dta_item',$dta_item,$mbd_setting);
    $mbd_setting = serialized_update('dta_unit',$dta_unit,$mbd_setting);
    $mbd_setting = serialized_update('data_series',$data_series,$mbd_setting);

    $sql_common = " mb_id = '".$member['mb_id']."'
                    , com_idx = '".$com_idx."'
                    , mms_idx = '".$mms_idx."'
                    , mta_idx = '{$mta['mta_idx']}'
                    , mbd_type = 'graph '
                    , mbd_setting = '".$mbd_setting."'
    ";

    $sql = " SELECT mbd_idx FROM {$g5['member_dash_table']} WHERE mbd_idx = '".$mbd_idx."' ";
    $mbd = sql_fetch($sql,1);
    //echo $sql.'<br>';
    if($mbd['mbd_idx']) {
        $sql = " UPDATE {$g5['member_dash_table']} SET
                    {$sql_common}
                WHERE mbd_idx = '".$mbd['mbd_idx']."'
        ";
        sql_query($sql,1);
    }
    else {

        // 기존거 다 +1 한 후에 (정렬을 맨 앞으로 가지고 오기 위해서)
        $sql = " UPDATE {$g5['dash_grid_table']} SET dsg_order = dsg_order + 1 WHERE mta_idx = '".$mta['mta_idx']."' ";
        sql_query($sql,1);
        // 입력
        // $mores = sql_fetch(" SELECT MAX(dsg_order) AS max_order FROM {$g5['dash_grid_table']} WHERE mta_idx = '{$mta['mta_idx']}' ");
        // $next_order = ($mores['max_order']) ? $mores['max_order'] + 1 : 1;
        $sql = " INSERT INTO {$g5['dash_grid_table']} SET
                    mta_idx = '{$mta['mta_idx']}'
                    ,dsg_width_num = '2'
                    ,dsg_height_num = '1'
                    ,dsg_order = '1'
                    ,dsg_reg_dt = '".G5_TIME_YMDHIS."'
                    ,dsg_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $dsg_idx = sql_insert_id();

        // 기존거 다 +1 한 후에 (큰 의미는 없네)
        $sql = " UPDATE {$g5['member_dash_table']} SET
                        mbd_value = mbd_value + 1
                    WHERE mb_id = '".$member['mb_id']."'
                        AND  mms_idx = '".$mms_idx."'
                        AND mbd_type = 'graph '
        ";
        sql_query($sql,1);
        // 삽입
        $sql = " INSERT INTO {$g5['member_dash_table']} SET
                    {$sql_common}
                    , dsg_idx = '".$dsg_idx."'
                    , mbd_value = '1'
                    , mbd_status = 'show'
                    , mbd_reg_dt = '".G5_TIME_YMDHIS."'
        ";
        // echo $sql.'<br>';
        sql_query($sql,1);
        $mbd['mbd_idx'] = sql_insert_id();
    }

 	$response->result = true;
	$response->mbd_idx = $mbd['mbd_idx'];
	$response->msg = "그래프 정보를 추가하였습니다.";

}
// 그래프 타이틀 수정
else if ($aj == "tit") {

    if($mbd_idx) {
        $mbd = get_table_meta('member_dash','mbd_idx',$mbd_idx);
        $mbd['sried'] = get_serialized($mbd['mbd_setting']);
        $mbd['data'] = json_decode($mbd['sried']['data_series'],true);
        for($j=0;$j<sizeof($mbd['data']);$j++) {
            // print_r2($row['data'][$j]);
        }

        // 설정 정보
        $mbd_setting = $mbd['mbd_setting'];
        $mbd_setting = serialized_update('graph_name',$mbd_title,$mbd_setting);
        
        $sql = " UPDATE {$g5['member_dash_table']} SET
                    mbd_setting = '".$mbd_setting."'
                WHERE mbd_idx = '".$mbd['mbd_idx']."'
        ";
        sql_query($sql,1);

        $response->result = true;
        $response->mbd_idx = $mbd_idx;
        $response->msg = "그래프 이름변경 완료!";	
    }
    else {
        $response->msg = "그래프 정보가 없습니다.";	
    }

}
// 그래프 삭제
else if ($aj == "del") {

    if($mbd_idx) {
        $sql = " DELETE FROM {$g5['member_dash_table']}
                WHERE mbd_idx = '".$mbd_idx."'
        ";
        sql_query($sql,1);

        $response->result = true;
        $response->mbd_idx = $mbd_idx;
        $response->msg = "그래프를 삭제하였습니다.";	
    }
    else {
        $response->msg = "그래프 정보가 없습니다.";	
    }

}
// 그래프 재정렬
else if ($aj == "srt") {

    if(is_array($mbd_idxs)) {

        for($i=0;$i<sizeof($mbd_idxs);$i++) {
            $sql = " UPDATE {$g5['member_dash_table']} SET
                        mbd_value = '".($i+1)."'
                    WHERE mbd_idx = '".$mbd_idxs[$i]."'
            ";
            sql_query($sql,1);
        }
        $response->result = true;
        $response->mbd_idx = $mbd_idx;
        $response->msg = "그래프를 재정렬하였습니다.";
    }
    else {
        $response->msg = "그래프 정보가 없습니다.";
    }

}
// 위젯이동
else if ($aj == "mv1") {

    $mbd = get_table('member_dash','mbd_idx',$mbd_idx);
    // print_r2($mbd);

    $sql = " UPDATE {$g5['member_dash_table']} SET
                mta_idx = '".$mta_idx."'
            WHERE mbd_idx = '".$mbd_idx."'
    ";
    // echo $sql.'<br>';
    sql_query($sql,1);

    // 기존거 다 +1 한 후에 (정렬을 맨 앞으로 가지고 오기 위해서)
    $sql = " UPDATE {$g5['dash_grid_table']} SET dsg_order = dsg_order + 1 WHERE mta_idx = '".$mta_idx."' ";
    sql_query($sql,1);
    // 업데이트
    $sql = " UPDATE {$g5['dash_grid_table']} SET
                mta_idx = '".$mta_idx."'
            WHERE dsg_idx = '".$mbd['dsg_idx']."'
    ";
    // echo $sql.'<br>';
    sql_query($sql,1);

    $response->sql = $sql;
    $response->result = true;
    $response->msg = "위젯을 이동하였습니다.";

}
else {
	$response->err_code='E00';
	$response->msg = "잘못된 접근입니다. \n정상적인 방법으로 이용해 주세요.";
}


$response->sql = $sql;

echo json_encode($response);
exit;
?>