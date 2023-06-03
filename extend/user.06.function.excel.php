<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 업데이트 함수
if(!function_exists('update_excel')){
function update_excel($excel_type, $arr) {
    global $g5,$demo;

    if(!$excel_type) {
        return false;
    }
    return false;
    // print_r3($arr);

    // 대창공업 ITEM LIST_REV1(22.12.22)-개발이범희GJ_REV6.xlxs
    if($excel_type=='01') {

        // existing check of bom record
        $sql = "SELECT bom_idx
                FROM {$g5['bom_table']}
                WHERE bom_part_no = '".$arr['bom_part_no']."'
        ";
        $bom = sql_fetch($sql,1);


        $item['parent_id'] = $parent_id;
        $list = array();
        $list = $item;
        unset($list['children']);   // 서브까지 다 보이면 복잡해서 숨김
        $list['reply'] = get_num_reply($list['id'], $list['parent_id'], $list['depth']);
        $list['bit_num'] = $list['reply'][0];
        $list['bit_reply'] = $list['reply'][1];
        $list['bom_idx'] = $_POST['bom_idx'];   // 넘겨받은 bom_idx
        unset($list['reply']);
        $list['bit_idx'] = update_bom_item($list);
        $g5['bit_idxs'][] = $list['bit_idx'];   // 삭제를 위한 배열
        // print_r2($list);
        //print_r2($g5['bit']['num']);    // 공통 배열 변수
        //print_r2($g5['bit']['reply']);    // 공통 배열 변수

        $sql_common = " com_idx = '13'
                , mms_idx = '".$arr['mms_idx']."'
                , trm_idx_maintain = '".$trm_idx."'
                , mb_id = '".$arr['mb_id']."'
                , mnt_name = '".$arr['mnt_name']."'
                , mnt_db_table = 'code'
                , mnt_db_idx = '".$cod['cod_idx']."'
                , mnt_db_code = '".$cod['cod_code']."'
                , mnt_date = '".$arr['mnt_date']."'
                , mnt_start_dt = '".$arr['mnt_start_dt']."'
                , mnt_end_dt = '".$arr['mnt_end_dt']."'
                , mnt_minute = '".$arr['mnt_minute']."'
                , mnt_people = '".$arr['mnt_people']."'
                , mnt_price = '".$arr['mnt_price']."'
                , mnt_subject = '".$arr['mnt_subject']."'
                , mnt_content = '".$arr['mnt_content']."'
                , bom_status = '".$arr['bom_status']."'
        ";
        $sql = "SELECT *
                FROM {$g5['bom_table']}
                WHERE bom_part_no = '".$arr['bom_part_no']."'
        ";
        $row = sql_fetch($sql,1);
        // 삭제 우선 처리
        if($arr['bom_status']=='삭제') {
            if($row['bom_idx']) {
                $sql = "DELETE FROM {$g5['bom_table']} WHERE bom_idx = '".$row['bom_idx']."' ";
                if(!$demo) {sql_query($sql,1);}
                else {print_r3($sql);}
            }
        }
        else {
            // 없으면 등록
            if(!$row['bom_idx']) {
                $sql = " INSERT INTO {$g5['bom_table']} SET
                            {$sql_common}
                            , bom_reg_dt = '".G5_TIME_YMDHIS."'
                            , bom_update_dt = '".G5_TIME_YMDHIS."'
                ";
                if(!$demo) {sql_query($sql,1);}
                $row['bom_idx'] = sql_insert_id();
            }
            // 있으면 수정
            else {
                $sql = "UPDATE {$g5['bom_table']} SET
                            {$sql_common}
                            , bom_update_dt = '".G5_TIME_YMDHIS."'
                        WHERE bom_idx = '".$row['bom_idx']."'
                ";
                if(!$demo) {sql_query($sql,1);}
            }
            if($demo) {print_r3($sql);}
            // print_r3($sql);

        }

    }

    return $row['db_idx'];
}
}

// 업데이트 함수
if(!function_exists('func_db_update')){
function func_db_update($arr) {
    global $g5,$demo,$mms_array;

    return false;
    // print_r3($arr);
    // print_r3($mms_array);
    // print_r3($mms_array[$arr['machine_no']]);

    $arr['mms_idx'] = $mms_array[$arr['machine_no']];

    // 조치분류 업데이트 = 고장부위
    $trm_idx = trm_idx_update($arr['mnt_part']);

    // 관련 알람코드 추출
    $sql = "SELECT *
            FROM g5_1_code
            WHERE com_idx = '13'
                AND mms_idx = '".$arr['mms_idx']."'
                AND cod_code = '".$arr['alarm_code']."'
            ORDER BY cod_idx DESC LIMIT 1
    ";
    $cod = sql_fetch($sql,1);

    // 조치시간분
    $arr['mnt_start_dt'] = $arr['mnt_date'].' '.$arr['mnt_start_time'].':00';
    if($arr['mnt_start_time'] > $arr['mnt_end_time']) {
        $arr['mnt_end_dt'] = date("Y-m-d",strtotime($arr['mnt_date'])+86400).' '.$arr['mnt_end_time'].':00';
    }
    else {
        $arr['mnt_end_dt'] = $arr['mnt_date'].' '.$arr['mnt_end_time'].':00';
    }
    // print_r3($arr['mnt_start_dt'].'~'.$arr['mnt_end_dt']);
    $arr['mnt_minute'] = sec2m(strtotime($arr['mnt_end_dt'])-strtotime($arr['mnt_start_dt']));


    $arr['mnt_people'] = 1;
    $arr['bom_status'] = 'ok';


    // 정보 입력
    $sql_common = " com_idx = '13'
                    , mms_idx = '".$arr['mms_idx']."'
                    , trm_idx_maintain = '".$trm_idx."'
                    , mb_id = '".$arr['mb_id']."'
                    , mnt_name = '".$arr['mnt_name']."'
                    , mnt_db_table = 'code'
                    , mnt_db_idx = '".$cod['cod_idx']."'
                    , mnt_db_code = '".$cod['cod_code']."'
                    , mnt_date = '".$arr['mnt_date']."'
                    , mnt_start_dt = '".$arr['mnt_start_dt']."'
                    , mnt_end_dt = '".$arr['mnt_end_dt']."'
                    , mnt_minute = '".$arr['mnt_minute']."'
                    , mnt_people = '".$arr['mnt_people']."'
                    , mnt_price = '".$arr['mnt_price']."'
                    , mnt_subject = '".$arr['mnt_subject']."'
                    , mnt_content = '".$arr['mnt_content']."'
                    , bom_status = '".$arr['bom_status']."'
    ";
    $sql = "SELECT *
            FROM {$g5['maintain_table']}
            WHERE mms_idx = '".$arr['mms_idx']."'
                AND trm_idx_maintain = '".$trm_idx."'
                AND mnt_name = '".$arr['mnt_name']."'
                AND mnt_date = '".$arr['mnt_date']."'
                AND mnt_minute = '".$arr['mnt_minute']."'
    ";
    $row = sql_fetch($sql,1);
    // 삭제 우선 처리
    if($arr['mnt_status']=='삭제') {
        if($row['mnt_idx']) {
            $sql = "DELETE FROM {$g5['maintain_table']} WHERE mnt_idx = '".$row['mnt_idx']."' ";
            if(!$demo) {sql_query($sql,1);}
            else {print_r3($sql);}
        }
    }
    else {
        // 없으면 등록
        if(!$row['mnt_idx']) {
            $sql = " INSERT INTO {$g5['maintain_table']} SET
                        {$sql_common}
                        , mnt_reg_dt = '".G5_TIME_YMDHIS."'
                        , mnt_update_dt = '".G5_TIME_YMDHIS."'
            ";
            if(!$demo) {sql_query($sql,1);}
            $row['mnt_idx'] = sql_insert_id();
        }
        // 있으면 수정
        else {
            $sql = "UPDATE {$g5['maintain_table']} SET
                        {$sql_common}
                        , mnt_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE mnt_idx = '".$row['mnt_idx']."'
            ";
            if(!$demo) {sql_query($sql,1);}
        }
        if($demo) {print_r3($sql);}
        // print_r3($sql);

    }
 
    return $row['mnt_idx'];
}
}

// 조치사항 업데이트
if(!function_exists('trm_idx_update')){
function trm_idx_update($str) {
    global $g5,$demo;

    $sql = "SELECT *
            FROM {$g5['term_table']}
            WHERE trm_status NOT IN ('trash','delete')
                AND trm_taxonomy = 'maintain'
                AND trm_name = '".trim($str)."'
    ";
    $row = sql_fetch($sql,1);
    // 없으면 등록
    if(!$row['trm_idx']) {

        $sql1 = "SELECT * FROM {$g5['term_table']}
                WHERE trm_status NOT IN ('trash','delete')
                    AND trm_taxonomy = 'maintain'
                ORDER BY trm_sort DESC
                LIMIT 1
        ";
        $one = sql_fetch($sql1,1);

        $sql = " INSERT INTO {$g5['term_table']} SET
                    trm_country = 'ko_KR'
                    , trm_name = '".$str."'
                    , trm_taxonomy = 'maintain'
                    , trm_sort = '".($one['trm_sort']+1)."'
                    , trm_left = '".($one['trm_right']+1)."'
                    , trm_right = '".($one['trm_right']+2)."'
                    , trm_status = 'ok'
                    , trm_reg_dt = '".G5_TIME_YMDHIS."'
                    , trm_update_dt = '".G5_TIME_YMDHIS."'
        ";
        if(!$demo) {sql_query($sql,1);}
        $row['trm_idx'] = sql_insert_id();
    }
    if($demo) {print_r3('조치사항 분류 쿼리: '.$sql);}
    // print_r3($sql);
 
    return $row['trm_idx'];
}
}
