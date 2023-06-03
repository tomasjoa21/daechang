<?php
$sub_menu = "922110";
include_once('./_common.php');

check_demo();

if (!@count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

// print_r2($_POST);
// exit;
auth_check($auth[$sub_menu], 'w');

check_admin_token();

// print_r2($_POST);exit;
//지금은 선택수정을 사용하지 않음
if ($_POST['act_button'] == "선택수정" || $_POST['act_button'] == "선택문자전송") {
    // print_r2($_POST);exit;
    foreach($_POST['chk'] as $prd_idx_v){
        $_POST['pri_value'][$prd_idx_v] = preg_replace("/,/","",$_POST['pri_value'][$prd_idx_v]);

        //우선 production테이블 정보를 수정한다.
        $sql = "UPDATE {$g5['production_table']} SET
                    prd_start_date = '{$_POST['prd_start_date'][$prd_idx_v]}',
                    prd_status = '".$_POST['prd_status'][$prd_idx_v]."',
                    prd_update_dt = '".G5_TIME_YMDHIS."'
                WHERE prd_idx = '".$prd_idx_v."'
        ";
        sql_query($sql,1);

        //해당 완제품의 하위제품 구조나 내용에 변경이 발생했을 수 있으니 한 번 더 추출해서 확인하자
        $sql1 = " SELECT bom.bom_idx, bit.bit_count
                            FROM {$g5['bom_item_table']} AS bit
                                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                            WHERE bit.bom_idx = '".$_POST['bom_idx'][$prd_idx_v]."'
                            ORDER BY bit.bit_reply
                    ";
        $res = sql_query($sql1,1);

        if($res->num_rows){
            //우선 해당 prd_idx를 가진 모든 pri_idx를 완제품을 제외하고 전부 trash상태로 수정한다.
            $pri_trash_sql = " UPDATE {$g5['production_item_table']} SET pri_status = 'trash' WHERE prd_idx = '{$prd_idx}' AND bom_idx != '{$_POST['bom_idx'][$prd_idx_v]}' ";
            sql_query($pri_trash_sql, 1);

            //우선 완제품의 pri_idx를 수정하자
            $bom_sql = " UPDATE {$g5['production_item_table']} SET
                        pri_value = '{$_POST['pri_value'][$prd_idx_v]}'
                        , pri_status = '{$_POST['prd_status'][$prd_idx_v]}'
                        , pri_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE pri_idx = '{$_POST['pri_idx'][$prd_idx_v]}'
            ";
            sql_query($bom_sql,1);

            for($i=0;$row=sql_fetch_array($res);$i++) {
                //해당 제품의 설비번호를 확인하자
                $smms = sql_fetch(" SELECT mms_idx FROM {$g5['bom_jig_table']} WHERE bom_idx = '{$row['bom_idx']}' AND boj_status = 'ok' LIMIT 1 ");
                //우선 해당제품의 prd_idx와 bom_idx가 존재하는지 확인부터 하자
                $pri = sql_fetch(" SELECT pri_idx FROM {$g5['production_item_table']}
                                    WHERE prd_idx = '{$_POST['prd_idx'][$prd_idx_v]}' 
                                        AND bom_idx = '{$row['bom_idx']}' 
                                    LIMIT 1
                ");
                //위에서 해당 pri_idx가 존재하면 수정하자
                $sub_count = $_POST['pri_value'][$prd_idx_v] * $row['bit_count'];
                if($pri['pri_idx']){
                    $sql = " UPDATE {$g5['production_item_table']} SET
                                pri_value = '{$sub_count}'
                                , pri_status = '{$_POST['prd_status'][$prd_idx_v]}'
                                , pri_update_dt = '".G5_TIME_YMDHIS."'
                            WHERE pri_idx = '{$pri['pri_idx']}'
                    ";
                    sql_query($sql,1);
                }
                //pri_idx가 존재하지 않으면 추가하자
                else {
                    $sub_sql = " INSERT INTO {$g5['production_item_table']} SET
                                    prd_idx = '{$_POST['prd_idx'][$prd_idx_v]}'
                                    , bom_idx = '{$row['bom_idx']}'
                                    , mms_idx = '{$smms['mms_idx']}'
                                    , trm_idx_operation = '0'
                                    , trm_idx_line = '0'
                                    , pri_value = '{$sub_count}'
                                    , pri_memo = ''
                                    , pri_status = '{$_POST['prd_status'][$prd_idx_v]}'
                                    , pri_reg_dt = '".G5_TIME_YMDHIS."'
                                    , pri_update_dt = '".G5_TIME_YMDHIS."'
                    ";
                    sql_query($sub_sql,1);
                }
            }
        }
    }

    if ($_POST['act_button'] == "선택문자전송") {
        //우선 각 항목별로 주간담당자 1명씩에게 문자를 전송한다.
        // print_r2($_POST);exit;
    }

} else if ($_POST['act_button'] == "선택삭제") {

    foreach($_POST['chk'] as $prd_idx_v){
        $sql = " UPDATE {$g5['production_item_table']} SET
                    prd_idx = '0'
                    ,pri_memo = '{$prd_idx_v}'
                    ,pri_status = 'trash'
                WHERE prd_idx = '".$prd_idx_v."'
        ";
        sql_query($sql,1);

        $sql = " UPDATE {$g5['production_table']} SET
                    prd_memo = '{$prd_idx_v}'
                    ,prd_status = 'trash'
                WHERE prd_idx = '".$prd_idx_v."'
        ";
        sql_query($sql,1);
    }

}


if ($msg)
    //echo '<script> alert("'.$msg.'"); </script>';
    alert($msg);

//exit;
// $qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
// if($schrows)
//     $qstr .= '&schrows='.$schrows;
goto_url('./production_list.php?'.$qstr);
?>
