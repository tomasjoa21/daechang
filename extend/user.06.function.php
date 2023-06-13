<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 141000 같은 값을 초로 변환하는 함수
if(!function_exists('num2seconds')){
function num2seconds($str)
{
    $hour = intval(substr($str,0,2));
    $minute = intval(substr($str,2,2));
    $second = intval(substr($str,4,2));
    $sec = $hour*3600 + $minute*60 + $second;
    return $sec;
}
}

// ct_history를 배열로 반환하는 함수
if(!function_exists('get_ct_history')){
function get_ct_history($text)
{
    $a = array();
    $b = explode("\n",$text);
    for($i=0;$i<sizeof($b);$i++) {
        list($ct_status,$mb_id,$ct_date,$ct_ip) = explode('|', trim($b[$i]));
        //echo $ct_status.' | '.$mb_id.' | '.$ct_date.' | '.$ct_ip.'<br>';
        // 상태값이 한글이 아니면 무시
        if(preg_match("/^[가-힝]/",$ct_status) && $ct_date) {
            $a[] = trim($b[$i]);
        }
    }
    return $a;
}
}

// 생산 카운터 처리
if(!function_exists('production_count')){
function production_count($arr)
{
	global $g5;
    // print_r2($arr);
    
	if(!$arr['pri_idx']||!$arr['pic_value']||!$arr['sck_dt']) {
		return false;
    }
    // 생산수량이 0보다 적을 수는 없음
	else if($arr['pic_value']<0) {
		return false;
    }

    $pri = get_table('production_item','pri_idx',$arr['pri_idx']);
    $prd = get_table('production','prd_idx',$pri['prd_idx']);
    $bom = get_table('bom','bom_idx',$pri['bom_idx']);
    $arr['pic_date'] = statics_date($arr['sck_dt']);
    // echo $arr['pic_date'].BR;
    $arr['shf_idx'] = shift_idx($arr['sck_dt']);;

    // 테스트 입력인 경우 막 들어갈 수 있으므로 생산지시량 이상을 넘어가면 안 됨
    if($g5['setting']['set_production_test_yn']) {
        $sql = "SELECT SUM(pic_value) AS pic_sum
                FROM {$g5['production_item_count_table']}
                WHERE pri_idx = '".$pri['pri_idx']."' AND mb_id = '".$pri['mb_id']."' AND pic_date = '".$arr['pic_date']."'
        ";
        // echo $sql.BR;
        $sum = sql_fetch($sql,1);
        // echo $sum['pic_sum'].'>='.$pri['pri_value'].BR;
        if($g5['setting']['set_production_test_yn'] && $sum['pic_sum']>=$pri['pri_value']) {
            return false;
        }
    }
    
    // 작업자 생산제품 입력(production_item_count)
    $sql = "INSERT INTO {$g5['production_item_count_table']} SET
                pri_idx = '".$arr['pri_idx']."',
                mb_id = '".$pri['mb_id']."',
                pic_ing = '".$pri['pri_ing']."',
                pic_value = '".$arr['pic_value']."',
                pic_date = '".$arr['pic_date']."',
                pic_reg_dt = '".$arr['sck_dt']."',
                pic_update_dt = '".G5_TIME_YMDHIS."'
    ";
    // echo $sql.BR;
    sql_query($sql,1);
    $pic['pic_idx'] = sql_insert_id();

    // material & item 처리!!
    $sql = " SELECT bit_main_yn,bom_idx FROM {$g5['bom_item_table']} WHERE bom_idx_child = '".$pri['bom_idx']."' ";
    // echo $sql.BR;
    $main = sql_fetch($sql,1);
    // 내가 대표상품이거나 혹은 최상위 상품인 경우, 하위의 모든 제품 재고를 사용으로 처리해야 함
    if($main['bit_main_yn'] || $bom['bom_type']=='product') {
        // 대표상품(main_yn)인 경우 최상위 부모 bom_idx 추출
        $bom_idx = $main['bit_main_yn'] ? $main['bom_idx'] : $pri['bom_idx'];

        // 모든 하위 구조 추출
        $sql1 = "SELECT bom.bom_idx, bom.bom_type, bom.bom_name, bom_part_no, bom_price, bom_status, cst_idx_provider, cst_idx_customer
                    , bit.bit_idx, bit.bom_idx AS bit_bom_idx, bit.bit_main_yn, bit.bom_idx_child, bit.bit_reply, bit.bit_count
                FROM {$g5['bom_item_table']} AS bit
                    LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                WHERE bit.bom_idx = '".$bom_idx."'
                ORDER BY bit.bit_reply
        ";
        // echo $sql1.BR;
        $rs1 = sql_query($sql1,1);
        for ($j=0; $row1=sql_fetch_array($rs1); $j++) {
            // print_r2($row1);
            // insert for half
            if($row1['bom_type']=='half') {
                $ar['table'] = 'g5_1_material';
                $ar['com_idx'] = $_SESSION['ss_com_idx'];
                $ar['cst_idx_provider'] = $row1['cst_idx_provider'];
                $ar['cst_idx_customer'] = $row1['cst_idx_customer'];
                $ar['mms_idx'] = $pri['mms_idx'];
                $ar['ori_idx'] = $prd['ori_idx'];
                $ar['prd_idx'] = $prd['prd_idx'];
                $ar['pri_idx'] = $arr['pri_idx'];
                $ar['bom_idx'] = $row1['bom_idx'];
                $ar['shf_idx'] = $arr['shf_idx'];
                $ar['mb_id'] = '';
                $ar['mtr_part_no'] = $row1['bom_part_no'];
                $ar['mtr_name'] = $row1['bom_name'];
                $ar['mtr_type'] = $row1['bom_type'];
                $ar['mtr_value'] = $arr['pic_value']*$row1['bit_count'];   // 2가 될 수도 있음
                $ar['mtr_price'] = $row1['bom_price'];
                $ar['mtr_history'] = "finish|".G5_TIME_YMDHIS;
                $ar['mtr_status'] = 'finish';
                $ar['mtr_date'] = $arr['pic_date'];
                update_db($ar);
                unset($ar);
            }
            // update for material by the count of $arr['pic_value']
            else if($row1['bom_type']=='material') {
                // 해당 갯수만큼 추출 (2이면 2개 업데이트)
                $sql2 = "   UPDATE {$g5['material_table']} SET
                                mms_idx = '".$pri['mms_idx']."',
                                ori_idx = '".$prd['ori_idx']."',
                                prd_idx = '".$prd['prd_idx']."',
                                pri_idx = '".$arr['pri_idx']."',
                                shf_idx = '".$arr['shf_idx']."',
                                mb_id = '',
                                mtr_history = '\nused|".G5_TIME_YMDHIS."',
                                mtr_status = 'used',
                                mtr_update_dt = '".G5_TIME_YMDHIS."'
                            WHERE bom_idx = '".$row1['bom_idx']."' AND prd_idx = '0' AND pri_idx = '0' AND mtr_type = 'material' AND mtr_status = 'ok'
                            ORDER BY mtr_reg_dt LIMIT ".($arr['pic_value']*$row1['bit_count'])."
                ";
                // echo $sql2.BR;
                sql_query($sql2,1);
            }
        }

        // item 테이블 레코드 생성
        $ar['table'] = 'g5_1_item';
        $ar['com_idx'] = $_SESSION['ss_com_idx'];
        $ar['cst_idx_provider'] = $bom['cst_idx_provider'];
        $ar['cst_idx_customer'] = $bom['cst_idx_customer'];
        $ar['mms_idx'] = $pri['mms_idx'];
        $ar['ori_idx'] = $prd['ori_idx'];
        $ar['prd_idx'] = $prd['prd_idx'];
        $ar['pri_idx'] = $arr['pri_idx'];
        $ar['bom_idx'] = $bom_idx;      // 최상위 bom_idx.
        $ar['shf_idx'] = $arr['shf_idx'];
        $ar['mb_id'] = $pri['mb_id'];
        $ar['itm_part_no'] = $bom['bom_part_no'];
        $ar['itm_name'] = $bom['bom_name'];
        $ar['itm_type'] = $bom['bom_type'];
        $ar['itm_value'] = $arr['pic_value'];
        $ar['itm_price'] = $bom['bom_price'];
        $ar['itm_history'] = "finish|".G5_TIME_YMDHIS;
        $ar['itm_status'] = 'finish';
        $ar['itm_date'] = $arr['pic_date'];
        // print_r2($ar);
        update_db($ar);
        unset($ar);
    }

    return $pic['pic_idx'];
}
}



// 생산아이템 업데이트
// 추가변수: prd_value(대표제품 생산수량)
if(!function_exists('get_production_item')){
function get_production_item($prd)
{
	global $g5;
	
	if(!$prd['prd_idx']) {
		return false;
    }

    // 배열 초기화
    $list = array();

    $sql = "SELECT * FROM {$g5['production_item_table']}
            WHERE prd_idx = '".$prd['prd_idx']."' AND pri_status NOT IN ('trash','delete')
            ORDER BY pri_idx
    ";
    $rs = sql_query($sql,1);
    $row['rows'] = sql_num_rows($rs);
    // 구성품이 없는 경우는 BOM 구조를 따라서 생성
    if(!$row['rows']) {

        $sql1 = "   SELECT *
                    FROM (
                            (
                            SELECT com_idx, bom.bom_idx, bom.bom_name, bom_type, bom_part_no, bom_price, bom_status, 'MIP' AS cst_name
                                , 0 AS bit_idx, 0 AS bit_bom_idx, 0 AS bit_main_yn, 0 AS bom_idx_child, '' AS bit_reply, bom_usage AS bit_count
                            FROM g5_1_bom AS bom
                            WHERE bom_idx = '".$prd['bom_idx']."' AND bom_type IN ('product','half')
                            )
                        UNION ALL
                            (
                            SELECT bom.com_idx, bom.bom_idx, bom.bom_name, bom_type, bom_part_no, bom_price, bom_status, cst_name
                                , bit.bit_idx, bit.bom_idx, bit.bit_main_yn, bit.bom_idx_child, bit.bit_reply, bit.bit_count
                            FROM {$g5['bom_item_table']} AS bit
                                LEFT JOIN {$g5['bom_table']} AS bom ON bom.bom_idx = bit.bom_idx_child
                                LEFT JOIN {$g5['customer_table']} AS cst ON cst.cst_idx = bom.cst_idx_provider
                            WHERE bit.bom_idx = '".$prd['bom_idx']."' AND bom_type IN ('product','half')
                            ORDER BY bit.bit_reply
                            )
                    ) AS db1
                    ORDER BY bit_reply
        ";
        // echo $sql1.BR;
        $rs1 = sql_query($sql1,1);
        $row['rows'] = sql_num_rows($rs1);
        // echo $rowspan.'<br>';
        for ($j=0; $row1=sql_fetch_array($rs1); $j++) {
            // print_r2($row1);
            // 야간이 있는지
            $sql2 = "   SELECT bmw_type
                        FROM {$g5['bom_mms_worker_table']} AS bmw
                        WHERE bmw_status NOT IN ('trash','delete') AND bom_idx = '".$row1['bom_idx']."' AND  bmw_type IN ('night')
                        GROUP BY bmw_type 
            ";
            // echo $sql2.BR;
            $rs2 = sql_query($sql2,1);
            $row1['night'] = intval(sql_num_rows($rs2)); // onlyDay = 1, and&night = 2
            // 주, 야간 생산량 할당
            $prd['prd_value_night'] = $row1['night'] ? intval($g5['set_bmw_type_share_value']['night']*$prd['prd_value']/100) : 0;   // 소수점 나오면 끝자리 버림
            $prd['prd_value_day'] = $prd['prd_value'] - $prd['prd_value_night'];    // 주간 생산량 전체 (정수)
            // echo $prd['prd_value_night'].'/'.$prd['prd_value_day'].BR;
            
            // 설비수
            $sql2 = "   SELECT mms_idx
                        FROM {$g5['bom_mms_worker_table']}
                        WHERE bmw_status NOT IN ('trash','delete') AND bom_idx = '".$row1['bom_idx']."'
                        GROUP BY mms_idx 
            ";
            // echo $sql2.BR;
            $rs2 = sql_query($sql2,1);
            $row1['mms_count'] = intval(sql_num_rows($rs2)); // machine count
            // 설비수에 따라 생산량 할당
            $prd['prd_value_night_each'] = $row1['mms_count'] ? intval($prd['prd_value_night']/$row1['mms_count']) : $prd['prd_value_night'];   // 소수점일 수 있음
            $prd['prd_value_day_each'] = $row1['mms_count'] ? intval($prd['prd_value_day']/$row1['mms_count']) : $prd['prd_value_day'];   // 소수점일 수 있음
            // echo $prd['prd_value_night_each'].'/'.$prd['prd_value_day_each'].BR;
            // echo '---------'.BR;

            $arr = array();
            $arr_done = array();
            $idx = 0;
            // 설비별 작업자 입력 ---------------------------------------------------------------
            $sql2 = "   SELECT bmw_idx, bmw.mms_idx AS mms_idx, mms_name, bmw.mb_id AS mb_id, bom_idx, mb_name, bmw_type, bmw_sort
                        FROM {$g5['bom_mms_worker_table']} AS bmw
                            LEFT JOIN {$g5['mms_table']} AS mms ON mms.mms_idx = bmw.mms_idx
                            LEFT JOIN {$g5['member_table']} AS mb ON mb.mb_id = bmw.mb_id
                        WHERE bmw_status NOT IN ('trash','delete') AND bom_idx = '".$row1['bom_idx']."'
                        ORDER BY bmw.mms_idx, bmw_type, bmw_sort
            ";
            // echo $sql2.BR;
            $rs2 = sql_query($sql2,1);
            $row1['bmw_rows'] = sql_num_rows($rs2); // 몇 개
            // echo $row1['bmw_rows'];
            for ($k=0; $row2=sql_fetch_array($rs2); $k++) {
                // print_r2($row2);
                $row2['mms_n_type'] = $row2['mms_idx'].'_'.$row2['bmw_type'];
                // 제품, 설비, 교대가 바뀌면서 & 중복 건너뜀(맨 처음것만 입력)
                // echo !in_array($row2['mms_n_type'],$arr_done) .' && '. $mms_idx_odd .' != '. $row2['mms_idx'] .'&&'. $bmw_type_odd .'!='. $row2['bmw_type'].BR;
                if(!in_array($row2['mms_n_type'],$arr_done) && ($bom_idx_odd != $row2['bom_idx'] || $mms_idx_odd != $row2['mms_idx'] || $bmw_type_odd != $row2['bmw_type'])) {
                    // echo $k.'------------'.BR;
                    $arr[$idx]['com_idx'] = $row1['com_idx'];
                    $arr[$idx]['bom_idx'] = $row1['bom_idx'];
                    $arr[$idx]['mms_idx'] = $row2['mms_idx'];
                    $arr[$idx]['mb_id'] = $row2['mb_id'];
                    $arr[$idx]['pri_value'] = $prd['prd_value_'.$row2['bmw_type'].'_each'];  // $prd['prd_value_day_each'], 생산량재할당
                    // print_r2($arr);
                    // 합계수량
                    $pri_value_total[$j] += $arr[$idx]['pri_value'];
                    $arr_done[] = $row2['mms_n_type'];   // 이미 처리한 설비_교대값을 저장(중복방지) arr('138_day','138_night','139_day','139_night')
                    // print_r2($arr_done);
                    $idx++;
                }
                // 이전값 저장
                $bom_idx_odd = $row2['bom_idx'];
                $mms_idx_odd = $row2['mms_idx'];
                $bmw_type_odd = $row2['bmw_type'];
            }
            // 소수점 처리하다가 수량이 혹시 안 맞으면 맨 처음 작업자에게 나머지 잔량 전부 할당
            if($arr[0] && $pri_value_total[$j]!=$prd['prd_value']) {
                $prd['prd_value_rest'] = $prd['prd_value'] - $pri_value_total[$j];
                $arr[0]['pri_value'] = $arr[0]['pri_value']+$prd['prd_value_rest'];
            }
            // print_r2($arr);

            // 최종 만들어진 배열을 가지고 디비 입력
            for ($i=0; $i<@sizeof($arr); $i++) {
                // print_r2($arr[$i]);
                // 생산아이템 정보 입력 ---------------------------------------------------------------
                $sql3 = "   INSERT INTO {$g5['production_item_table']} SET
                                prd_idx = '".$prd['prd_idx']."'
                                , com_idx = '".$arr[$i]['com_idx']."'
                                , bom_idx = '".$arr[$i]['bom_idx']."'
                                , mms_idx = '".$arr[$i]['mms_idx']."'
                                , mb_id = '".$arr[$i]['mb_id']."'
                                , trm_idx_operation = ''
                                , trm_idx_line = ''
                                , pri_value = '".$arr[$i]['pri_value']."'
                                , pri_memo = ''
                                , pri_status = 'pending'
                                , pri_reg_dt = '".G5_TIME_YMDHIS."'
                                , pri_update_dt = '".G5_TIME_YMDHIS."'
                ";
                // echo $sql3.BR;
                sql_query($sql3,1);
                $row1['pri_idx'] = sql_insert_id();
                // 리턴할 배열 생성
                $one = get_table('production_item','prd_idx',$row1['pri_idx']);
                $list[$i] = $one; 
            }
    
        }
    }
    // 존재하는 경우는 list 배열 생성
    else {
        for ($i=0; $row=sql_fetch_array($rs); $i++) {
            // print_r2($row1);
            $list[$i] = $row; 
        }
    }

    return $list;
}
}

// 생산아이템 업데이트
if(!function_exists('update_production_item')){
function update_production_item($arr)
{
	global $g5;
	
	if(!$arr['prd_idx']||!$arr['bom_idx']||!$arr['mms_idx']||!$arr['mb_id']) {
		return false;
    }

    $g5_table_name = $g5['production_item_table'];
    $fields = sql_field_names($g5_table_name);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;
    // $arr[$pre.'_end_ym'] = $arr[$pre.'_end_year'].'-'.$arr[$pre.'_end_month'];   // 년월

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
    // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    // 중복 조건은 함수마다 다르게 설정!!
    $sql = "SELECT * FROM {$g5_table_name} 
            WHERE pri_idx = '{$arr['pri_idx']}'
    ";
    // echo $sql.'<br>';
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = "UPDATE {$g5_table_name} SET 
                    {$sql_common} 
				WHERE ".$pre."_idx = '".$row[$pre."_idx"]."'
        ";
		sql_query($sql,1);
	}
	else {
		$sql = "INSERT INTO {$g5_table_name} SET 
                    {$sql_common} 
                    , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
	}
//    echo $sql.'<br>';

    // 생산자 아이템 테이블 production_member update
    $ar['table'] = 'g5_1_production_member';
    $ar['mb_id'] = $arr['mb_id'];
    $ar['pri_idx'] = $row['pri_idx'];
    $ar['prm_status'] = 'ok';
    $prm_idx = update_db($ar);
    unset($ar);

    return $row[$pre."_idx"];
}
}


// 엑셀 열문자 생성 함수
if(!function_exists('update_mms_worker')){
function numberToColumnName($num) {
    $numeric = ($num - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($num - 1) / 26);
    if ($num2 > 0) {
        return numberToColumnName($num2) . $letter;
    } else {
        return $letter;
    }
}
}

// BOM-설비-작업자 정보 업데이트
if(!function_exists('update_bom_mms_worker')){
function update_bom_mms_worker($arr)
{
	global $g5;
	
	if(!$arr['bom_idx'] || !$arr['mb_id'] || !$arr['mms_idx'])
		return false;

	$arr['bmw_status'] = $arr['bmw_status'] ?: 'ok';
    // calculate sort_no
    if(!$arr['bmw_sort']) {
        $sql = "SELECT max(bmw_sort) AS max_sort FROM {$g5['bom_mms_worker_table']}
                WHERE bom_idx='{$arr['bom_idx']}'
                    AND mms_idx='".$arr['mms_idx']."'
                    AND bmw_type='".$arr['bmw_type']."'
                    AND bmw_status='ok'
        ";
        $one1 = sql_fetch($sql,1);
        $arr['bmw_sort'] = $one1['max_sort'] + 1;
    }

	$arr['bmw_sort'] = $arr['bmw_sort'] ?: 1;
	$arr['bmw_type'] = $arr['bmw_type'] ?: 'day';

	$sql_common = " mb_id = '{$arr['mb_id']}'
					, bom_idx = '{$arr['bom_idx']}'
					, mms_idx = '{$arr['mms_idx']}'
					, bmw_type = '{$arr['bmw_type']}'
					, bmw_sort = '{$arr['bmw_sort']}'
					, bmw_memo = '{$arr['bmw_memo']}'
					, bmw_status = '{$arr['bmw_status']}'
					, bmw_update_dt = '".G5_TIME_YMDHIS."'
	";	

    $sql = "SELECT * FROM {$g5['bom_mms_worker_table']}
            WHERE mb_id='{$arr['mb_id']}'
                AND bom_idx='{$arr['bom_idx']}'
                AND mms_idx='{$arr['mms_idx']}'
                AND bmw_type='{$arr['bmw_type']}'
                AND bmw_status='{$arr['bmw_status']}'
    ";
    // if($arr['mb_id']=='01053818229') {
    //     print_r3($sql);
    // }
    $one = sql_fetch($sql,1);
	// 있으면 UPDATE
	if($one['bmw_idx']) {
		$sql = "UPDATE {$g5['bom_mms_worker_table']} SET
                    {$sql_common}
                WHERE bmw_idx='".$one['bmw_idx']."'
        ";
		sql_query($sql,1);
	}
	// 없으면 INSERT
	else {
		$sql = "INSERT INTO {$g5['bom_mms_worker_table']} SET
                    {$sql_common}
                    , bmw_reg_dt='".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
		$one['bmw_idx'] = sql_insert_id();
	}
    // if($arr['mb_id']=='01053818229') {
    //     print_r3($sql);
    // }

    return $one['bmw_idx'];
}
}

// 설비-작업자 정보 업데이트
if(!function_exists('update_mms_worker')){
function update_mms_worker($arr)
{
	global $g5;
	
	if(!$arr['mb_id'] || !$arr['mms_idx'])
		return false;

	$arr['mmw_status'] = $arr['mmw_status'] ?: 'ok';
    // calculate sort_no
    if(!$arr['mmw_sort']) {
        $sql = "SELECT max(mmw_sort) AS max_sort FROM {$g5['mms_worker_table']}
                WHERE mms_idx='{$arr['mms_idx']}'
                    AND mmw_type='".$arr['mmw_type']."'
                    AND mmw_status='ok'
        ";
        $one1 = sql_fetch($sql,1);
        $arr['mmw_sort'] = $one1['max_sort'] + 1;
    }

	$arr['mmw_sort'] = $arr['mmw_sort'] ?: 1;
	$arr['mmw_type'] = $arr['mmw_type'] ?: 'day';

	$sql_common = " mb_id = '{$arr['mb_id']}'
					, mms_idx = '{$arr['mms_idx']}'
					, mmw_type = '{$arr['mmw_type']}'
					, mmw_sort = '{$arr['mmw_sort']}'
					, mmw_memo = '{$arr['mmw_memo']}'
					, mmw_status = '{$arr['mmw_status']}'
					, mmw_update_dt = '".G5_TIME_YMDHIS."'
	";	

    $sql = "SELECT * FROM {$g5['mms_worker_table']}
            WHERE mb_id='{$arr['mb_id']}'
                AND mms_idx='{$arr['mms_idx']}'
                AND mmw_type='{$arr['mmw_type']}'
                AND mmw_status='{$arr['mmw_status']}'
    ";
    // if($arr['mb_id']=='01053818229') {
    //     print_r3($sql);
    // }
    $one = sql_fetch($sql,1);
	// 있으면 UPDATE
	if($one['mmw_idx']) {
		$sql = "UPDATE {$g5['mms_worker_table']} SET
                    {$sql_common}
                WHERE mmw_idx='".$one['mmw_idx']."'
        ";
		sql_query($sql,1);
	}
	// 없으면 INSERT
	else {
		$sql = "INSERT INTO {$g5['mms_worker_table']} SET
                    {$sql_common}
                    , mmw_reg_dt='".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
		$one['mmw_idx'] = sql_insert_id();
	}
    // if($arr['mb_id']=='01053818229') {
    //     print_r3($sql);
    // }

    return $one['mmw_idx'];
}
}

// 업체-회원 정보 업데이트 (mb_id, com_idx, cmm_title(직급번호), cmm_memo, cmm_status)
if(!function_exists('company_member_update')){
function company_member_update($arr)
{
	global $g5;
	
	if(!$arr['mb_id'] || !$arr['com_idx'])
		return false;

	$arr['cmm_status'] = $arr['cmm_status'] ?: 'ok';

	$sql_common = " mb_id = '{$arr['mb_id']}'
					, com_idx = '{$arr['com_idx']}'
					, cmm_title = '{$arr['cmm_title']}'
					, cmm_memo = '{$arr['cmm_memo']}'
					, cmm_status = '{$arr['cmm_status']}'
					, cmm_update_dt = '".G5_TIME_YMDHIS."'
	";	

    $one = sql_fetch("  SELECT * FROM {$g5['company_member_table']}
                        WHERE mb_id='{$arr['mb_id']}'
                            AND com_idx='{$arr['com_idx']}'
                            AND cmm_status='{$arr['cmm_status']}'
    ");
	// 있으면 UPDATE
	if($one['cmm_idx']) {
		$sql = "UPDATE {$g5['company_member_table']} SET
                    {$sql_common}
                WHERE cmm_idx='".$one['cmm_idx']."'
        ";
		sql_query($sql,1);
//		echo $sql.'<br>';
	}
	// 없으면 INSERT
	else {
		$sql = "INSERT INTO {$g5['company_member_table']} SET
                    {$sql_common}
                    , cmm_reg_dt='".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
//		echo $sql.'<br>';
		$one['cmm_idx'] = sql_insert_id();
	}

    return $one['cmm_idx'];
}
}


// make phone number from english name inevitably
if(!function_exists('make_cell_number')){
function make_cell_number($str,$pre='010')
{
    $name = base64_encode($str);
    $name_array = unpack("C*", base64_encode($name));
    // print_r3($name_array);
    // print_r3('---');
    krsort($name_array);
    // print_r3($name_array);
    $name_number = implode("",$name_array);
    $cell = $pre.'-'.substr($name_number,-4).'-'.substr($name_number,4,4);
    return $cell;
}
}

// db 업데이트.. 왠만한 것들은 이것 하나로 해결하면 되겠네!!
if(!function_exists('update_db')){
function update_db($arr)
{
	// print_r2($arr);
    if($arr['table']=='g5_1_bom') {
        // return false;
        if(!$arr['bom_part_no']||!$arr['bom_name']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_bom_jig') {
        if(!$arr['bom_idx']||!$arr['mms_idx']||!$arr['boj_status']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_customer') {
        if(!$arr['cst_name']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_bom_category') {
        if(!$arr['bct_name']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_mms') {
        if(!$arr['mms_name']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_order_item') {
        if(!$arr['cst_idx']||!$arr['bom_idx']||!$arr['ori_date']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_shipment') {
        if(!$arr['cst_idx']||!$arr['ori_idx']||!$arr['shp_count']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_production') {
        if(!$arr['com_idx']||!$arr['ori_idx']||!$arr['bom_idx']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_production_item') {
        if(!$arr['prd_idx']||!$arr['bom_idx']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_production_member') {
        if(!$arr['mb_id']||!$arr['pri_idx']||!$arr['prm_status']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_item') {
        if(!$arr['mms_idx']||!$arr['ori_idx']||!$arr['prd_idx']||!$arr['pri_idx']||!$arr['bom_idx']||!$arr['itm_status']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_material') {
        if(!$arr['mms_idx']||!$arr['ori_idx']||!$arr['prd_idx']||!$arr['pri_idx']||!$arr['bom_idx']||!$arr['mtr_status']) {
            return false;
        }
    }
	else if($arr['table']=='g5_1_alarm') {
        if(!$arr['mms_idx']||!$arr['com_idx']) {
            return false;
        }
    }

    $fields = sql_field_names($arr['table']);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
	if($arr['table']=='g5_1_bom') { // 업체명이 이상하면 건너뜀 (#N/A, #REF!..)
        if( in_array($arr['cst_idx_provider'], array(74,115))) {
            $skips[] = 'cst_idx_provider';
        }
    }
	else if($arr['table']=='g5_1_mms') { // 참고변수(엑셀관련) 수정 불가
        $skips[] = 'mms_name_ref';
    }
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
	if($arr['table']=='g5_1_bom') {
        // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";
    }
	else if($arr['table']=='g5_1_bom_category') {
        $sql_commons[] = " bct_idx = '".$arr['bct_idx']."' ";
    }

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    // 중복 조건은 함수마다 다르게 설정!!
	if($arr['table']=='g5_1_bom') {
        $where = " bom_part_no = '{$arr['bom_part_no']}' ";
    }
	else if($arr['table']=='g5_1_bom_jig') {
        $where = " bom_idx = '{$arr['bom_idx']}' AND mms_idx = '{$arr['mms_idx']}' AND boj_code = '{$arr['boj_code']}' ";
    }
	else if($arr['table']=='g5_1_customer') {
        $where = " cst_name = '{$arr['cst_name']}' ";
    }
	else if($arr['table']=='g5_1_bom_category') {
        $where = " bct_name = '{$arr['bct_name']}' ";
    }
	else if($arr['table']=='g5_1_mms') {
        $where = " mms_name = '{$arr['mms_name']}' ";
    }
	else if($arr['table']=='g5_1_order_item') {
        $where = " cst_idx = '{$arr['cst_idx']}' AND bom_idx = '{$arr['bom_idx']}' AND ori_date = '{$arr['ori_date']}' ";
    }
	else if($arr['table']=='g5_1_shipment') {
        $where = " cst_idx = '{$arr['cst_idx']}' AND ori_idx = '{$arr['ori_idx']}' AND shp_count = '{$arr['shp_count']}' ";
    }
	else if($arr['table']=='g5_1_production') {
        $where = " bom_idx = '{$arr['bom_idx']}' AND prd_start_date = '{$arr['prd_start_date']}' ";
    }
	else if($arr['table']=='g5_1_production_item') {
        $where = " prd_idx = '{$arr['prd_idx']}' AND bom_idx = '{$arr['bom_idx']}' AND mms_idx = '{$arr['mms_idx']}' AND mb_id = '{$arr['mb_id']}' AND pri_status = '{$arr['pri_status']}' ";
    }
	else if($arr['table']=='g5_1_production_member') {
        // $where = " mb_id = '{$arr['mb_id']}' AND pri_idx = '{$arr['pri_idx']}' AND prm_status = '{$arr['prm_status']}' ";
        $where = " prm_idx = '{$arr['prm_idx']}' ";
    }
	else if($arr['table']=='g5_1_item') {
        $where = " itm_idx = '{$arr['itm_idx']}' ";
    }
	else if($arr['table']=='g5_1_material') {
        $where = " mtr_idx = '{$arr['mtr_idx']}' ";
    }
	else if($arr['table']=='g5_1_alarm') {
        $where = " cod_idx = '{$arr['cod_idx']}' AND arm_reg_dt = '{$arr['arm_reg_dt']}' ";
    }

    $sql = "SELECT * FROM {$arr['table']} WHERE {$where} ";
    // print_r2($sql);
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = " UPDATE {$arr['table']} SET {$sql_common} WHERE ".$pre."_idx = '".$row[$pre."_idx"]."' ";
		sql_query($sql,1);

        if($arr['table']=='g5_1_bom') {
            //기존의 bct_json배열을 확인한다.
            $chk_sql = " SELECT bom_bct_json FROM {$arr['table']} WHERE ".$pre."_idx = '".$row[$pre."_idx"]."' ";
            $chk_res = sql_fetch($chk_sql,1);
            $chk_arr = ($chk_res['bom_bct_json'])?json_decode($chk_res['bom_bct_json']):array();
            if(!in_array($arr['bct_idx'],$chk_arr)){
                array_push($chk_arr,$arr['bct_idx']);
                $tmp_json = json_encode($chk_arr);
                $sql2 = " UPDATE {$arr['table']} SET bom_bct_json = json_compact('{$tmp_json}') WHERE ".$pre."_idx = '".$row[$pre."_idx"]."' ";
                sql_query($sql2,1);
            }
        }
	}
	else {
		$sql = " INSERT INTO {$arr['table']} SET {$sql_common}, ".$pre."_reg_dt = '".G5_TIME_YMDHIS."' ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
        
        if($arr['table']=='g5_1_bom' && $arr['bct_idx']){
            $sql2 = " UPDATE {$arr['table']} SET bom_bct_json = '[{$arr['bct_idx']}]' WHERE ".$pre."_idx = '".$row[$pre."_idx"]."' ";
            sql_query($sql2,1);
        }
        else if($arr['table']=='g5_1_alarm' && $row[$pre."_idx"]){
            $sql2 = " UPDATE {$arr['table']} SET arm_reg_dt = '{$arr['arm_reg_dt']}' WHERE ".$pre."_idx = '".$row[$pre."_idx"]."' ";
            // echo $sql2.BR;
            sql_query($sql2,1);
        }
	}
    // print_r2($sql);
    // print_r2($row[$pre."_idx"]);
    return $row[$pre."_idx"];
}
}

// 회원생성하기
if(!function_exists('make_member')){
function make_member($arr)
{
	global $g5;
	
	if(!$arr['mb_id'] || !$arr['mb_hp'] || !$arr['mb_name'])
		return false;

    $arr['mb_hp2'] = preg_replace("/-|\s+/","",$arr['mb_hp']);
    $arr['mb_password'] = $arr['mb_password'] ?: get_encrypt_string($arr['mb_id'].'abcd');
    $arr['mb_level'] = $arr['mb_level'] ?: 2;

	$sql_common = " mb_id	= '".$arr['mb_id']."'
        , mb_password = '".$arr['mb_password']."'
        , mb_name = '".$arr['mb_name']."'
        , mb_nick = '".$arr['mb_id']."'
        , mb_nick_date = now()
        , mb_email = '".$arr['mb_email']."'
        , mb_hp = '".$arr['mb_hp']."'
        , mb_level = '".$arr['mb_level']."'
        , mb_zip1 = '".$arr['mb_zip1']."'
        , mb_zip2 = '".$arr['mb_zip2']."'
        , mb_addr1 = '".$arr['mb_addr1']."'
        , mb_addr2 = '".$arr['mb_addr2']."'
        , mb_addr3 = '".$arr['mb_addr3']."'
        , mb_addr_jibeon = '".$arr['mb_addr_jibeon']."'
        , mb_login_ip = '".$_SERVER['REMOTE_ADDR']."'
        , mb_ip = '".$_SERVER['REMOTE_ADDR']."'
        , mb_email_certify = now()
        , mb_mailling = '1'
        , mb_sms = '1'
        , mb_open = '1'
        , mb_open_date = now()
        , mb_1 = '".$arr['mb_1']."'
        , mb_4 = '".$arr['mb_4']."'
	";

    $sql = "SELECT * FROM {$g5['member_table']} WHERE mb_id = '".trim($arr['mb_id'])."' ";
    // echo $sql.'<br>';
    $one = sql_fetch($sql,1);
	// 있으면 UPDATE
	if($one['mb_id']) {
		$sql = "UPDATE {$g5['member_table']} SET
                    {$sql_common}
                WHERE mb_id='".$one['mb_id']."'
        ";
		sql_query($sql,1);
//		echo $sql.'<br>';
	}
	// 없으면 INSERT
	else {
		$sql = "INSERT INTO {$g5['member_table']} SET
                    {$sql_common}
                    , mb_datetime='".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
//		echo $sql.'<br>';
		$one['mb_id'] = $arr['mb_id'];
	}
    
    // 엑셀업로드회원 메타정보
    // $ar['mta_db_table'] = 'member';
    // $ar['mta_db_id'] = $one['mb_id'];
    // $ar['mta_key'] = 'mb_excel_yn';
    // $ar['mta_value'] = 1;
    // meta_update($ar);
    // unset($ar);

    return $one['mb_id'];
}
}


// bom_item 정보 입력
if(!function_exists('update_bom_item')){
function update_bom_item($arr) {
    global $g5;

    if(!$arr['bom_idx']||!$arr['bom_idx_child']||!$arr['bit_reply']) {
        return false;
    }

    $list = $arr; 
    unset($arr);

    $sql_common = " bom_idx = '".$list['bom_idx']."',
                    bom_idx_child = '".$list['bom_idx_child']."',
                    bit_count = '".$list['bit_count']."',
                    bit_main_yn = '".$list['bit_main_yn']."',
                    bit_num = '".$list['bit_num']."',
                    bit_reply = '".$list['bit_reply']."',
                    bit_update_dt = '".G5_TIME_YMDHIS."'
    ";

    $sql = "SELECT * FROM {$g5['bom_item_table']}
            WHERE bom_idx = '".$list['bom_idx']."'
            AND bom_idx_child = '".$list['bom_idx_child']."'
    ";
    // echo $sql.'<br>';
    $bit = sql_fetch($sql,1);
    if(!$bit['bit_idx']) {
        $sql = " INSERT INTO {$g5['bom_item_table']} SET
                    {$sql_common}
                    , bit_reg_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $bit_idx = sql_insert_id();
    }
    else {
        $sql = "UPDATE {$g5['bom_item_table']} SET
                    {$sql_common}
                WHERE bit_idx = '".$bit['bit_idx']."'
        ";
        sql_query($sql,1);
        $bit_idx = $bit['bit_idx'];
    }
//	echo $sql.'<br>';

    return $bit_idx;
}
}

// bom_bct_json 정보 update
if(!function_exists('update_bom_bct_json')){
function update_bom_bct_json($bom_idx) {
    global $g5;

    if(!$bom_idx) {
        return false;
    }

    // 완성품이 아닌 경우만 적용
    $bom = get_table('bom','bom_idx',$bom_idx);
    if(!in_array($bom['bom_type'],array('half','material'))) {
        return false;
    }

    // bom_item 전체를 돌면서 나의 부모를 모두 추출
    $sql = " SELECT * FROM {$g5['bom_item_table']} WHERE bom_idx_child = '".$bom_idx."' ";
    // echo $sql.BR;
    $rs = sql_query($sql,1);
    for($i=0;$row=sql_fetch_array($rs);$i++) {
        // print_r2($rs);
        $bom = get_table('bom','bom_idx',$row['bom_idx']);
        if($bom['bct_idx']) {
            $bct_idx_arr[] = (string)$bom['bct_idx'];
        }
    }
    if($bct_idx_arr[0]) {
        // print_r2($bct_idx_arr);
        $bct_idxs = array_unique($bct_idx_arr); // 중복제거
        // print_r2($bct_idxs);
        $bct_idxs = array_values($bct_idxs);    // key, value 구조에서 value값만 추출
    
        // 추출한 부모의 차종(bct_idx)를 배열로 생성
        $bct_idx_json = json_encode($bct_idxs);
        // echo $bct_idx_json.BR;
        $sql = " UPDATE {$g5['bom_table']} SET bom_bct_json = '".$bct_idx_json."' WHERE bom_idx = '".$bom_idx."' ";
        $rs = sql_query($sql,1);
    }

    return $bct_idxs;
}
}


// 게시판 reply 생성 함수
// 초기값 정의
//$g5['bit']['num'] = array();
//$g5['bit']['reply'] = array();
//$g5['bit_num'] = 0;
if(!function_exists('get_num_reply')){
function get_num_reply($idx, $parent, $depth) {
    global $g5;

    // parent=0이면 num--
    if(!$parent)
        $g5['bit_num']--;

    // reply 코드 앞부분은 부모코드
    $reply_char1 = $g5['bit']['reply'][$parent];

    // 부모코드로 시작 & 한단계 높은(정규식 regexp="/^정규식.$/") 배열들 전부 추출
    // reply 코드 뒷부분은 같은 단계의 맨 끝값을 추출해서 나중에 +1 코드로 만들어야 함
    foreach($g5['bit']['reply'] as $key=>$val) if(preg_match('/^'.$reply_char1.'.$/', $val)) {
        //echo $key.'='.$val.'<br>';
        //echo $g5['bit']['num'][$key].'<>'.$g5['bit_num'].'<br>';
        // 같은 wr_num 그룹안에서만 찾아야 함
        if( $g5['bit']['num'][$key]==$g5['bit_num'] ) {
            $reply_last = $val;
        }
    }
    // 같은 단계값이 없으면 초기값, 있으면 마지막 한문자값+1
    if (!$reply_last)
        $reply_char2 = 'A';
    else
        $reply_char2 = chr(ord( substr($reply_last,-1) ) + 1);

    $g5['bit']['num'][$idx] = $g5['bit_num'];
    $g5['bit']['reply'][$idx] = ($depth) ? $reply_char1.$reply_char2 : '';

    return array($g5['bit']['num'][$idx], $g5['bit']['reply'][$idx]);

}
}

// BOM reply 변수 생성 (쇼핑몰 카테고리 비슷한 구조로 변경됨, 2자리씩 끊어서 계층 구조를 만듦)
//$g5['bit']['reply'] = array();
if(!function_exists('get_bom_reply')){
function get_bom_reply($idx, $parent, $depth) {
    global $g5;

    // 카테고리 구조 변수.. 2자리씩 묶어서 계층구조 만들 예정
    $cats = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
    $cat_arr = array();
    for($i=0;$i<count($cats);$i++){
        if($i == 0) continue;
        for($j=0;$j<count($cats);$j++){
            //echo $cats[$i].$cats[$j]."<br>";
            array_push($cat_arr,$cats[$i].$cats[$j]);
        }
    }
    // print_r2($cat_arr);
    // echo array_search('za',$cat_arr);

    // reply 코드 앞부분은 부모코드
    $reply_char1 = $g5['bit']['reply'][$parent];
    // echo '$reply_char1 >>>> '.$reply_char1.BR;

    // 부모코드로 시작 & 한단계 높은(정규식 regexp="/^정규식.$/") 배열들 전부 추출한 다음 마지막 + 1 배열값 할당
    // reply 코드 뒷부분은 같은 단계의 맨 끝 두자리값을 추출해서 그 댜음 배열값 코드로 만들어야 함
    foreach($g5['bit']['reply'] as $key=>$val) if(preg_match('/^'.$reply_char1.'..$/', $val)) {
    // foreach($g5['bit']['reply'] as $key=>$val) {
        // echo $key.'='.$val.BR;
        //echo $g5['bit']['num'][$key].'<>'.$g5['bit_num'].'<br>';
        // 같은 depth 그룹 안에서만 찾아야 함
        if( strlen($val) == ($depth+1)*2 ) {
            $reply_last = substr($val,-2);
        }
    }
    // echo '$reply_last:---------------------->'.$reply_last.BR;
    // 같은 단계값이 없으면 초기값, 있으면 마지막 한문자값+1
    if (!$reply_last)
        $reply_char2 = '10';
    else {
        // $reply_char2 = chr(ord( substr($reply_last,-1) ) + 1);
        $cat_arr_next = array_search($reply_last,$cat_arr)+1;
        // echo '$cat_arr_next: '.$cat_arr_next.BR;
        $reply_char2 = $cat_arr[$cat_arr_next];
    }

    $g5['bit']['reply'][$idx] = $reply_char1.$reply_char2;

    return $g5['bit']['reply'][$idx];

}
}


// Get the pressure and temperature arrays for specific machine. 
if(!function_exists('get_graph_array')){
function get_graph_array($arr) {
    global $g5;
    // print_r2($arr);
    
    // 압력인 경우
    if($arr['tag']=='pressure') {

        // 수집된 항목값이 존재하는 경우만 가지고 배열생성 (값이 0인 경우를 무시하기 위해서..)
        $sql = "SELECT *
                FROM g5_1_cast_shot_pressure
                WHERE machine_id = '".$arr['machine_id']."' AND event_time >= '".$arr['st_dt']."' AND event_time <= '".$arr['en_dt']."'
        ";
        // echo $sql.'<br>';
        $rs = sql_query_pg($sql,1);
        $dta = array();
        for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
            // print_r2($row);
            // 각 태그별로 데이터 설정
            // hold_temp=보온로온도, upper_heat=상형히트, lower_heat=하형히트, upper_1_temp=상금형1, upper_2_temp=상금형2, upper_3_temp=상금형3, upper_4_temp=상금형4, upper_5_temp=상금형5
            // upper_6_temp=상금형6, lower_1_temp=하금형1, lower_2_temp=하금형2, lower_3_temp=하금형3
            // detect_pressure=검출압력, target_pressure=목표압력, control_pressure=조작압력, deviation_pressure=편차, temp_avg=평균온도, temp_max=온도최대, temp_min=온도최소
            // hum_avg=평균습도, hum_max=습도최대, hum_min=습도최소
            foreach($g5['set_data_name_value'] as $k1=>$v1) {
                // echo $k1.'=>'.$v1.'<br>';
                if($row[$k1] && !in_array($k1,$dta)) { // 값이 존재하는 것만!
                    $dta[] = $k1;
                }
            }
        }
        // print_r2($dta);
        // 수집된 항목만 가지고 배열생성
        for ($j=0; $j<sizeof($dta); $j++) {
            // echo $dta[$j].'<br>';
            // echo $g5['set_data_name_value'][$dta[$j]].'<br>';
            // echo $g5['set_data_pressure_no_value'][$dta[$j]].'<br>';
            // graph id 생성
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['dta_type'] = 8;    // 압력
            $ar['dta_no'] = $g5['set_data_pressure_no_value'][$dta[$j]];
            $ar['type1'] = '';
            $graph_id = get_graph_id($ar);
            // echo $graph_id.'<br>';

            $array[$j]['name'] = $g5['set_data_name_value'][$dta[$j]];
            $array[$j]['id']['dta_data_url_host'] = 'hanjoo.epcs.co.kr';
            $array[$j]['id']['dta_data_url_path'] = '/user/json';
            $array[$j]['id']['dta_data_url_file'] = 'measure.php';
            $array[$j]['id']['mms_idx'] = $arr['mms_idx'];
            $array[$j]['id']['dta_type'] = $ar['dta_type'];
            $array[$j]['id']['dta_no'] = $ar['dta_no'];
            $array[$j]['id']['type1'] = '';
            $array[$j]['id']['graph_name'] = urlencode($array[$j]['name']);
            $array[$j]['id']['graph_id'] = $graph_id;
            $array[$j]['type'] = 'spline';
            $array[$j]['dashStyle'] = 'solid';
        }
    }
    // 온도
    else {

        // 수집된 항목값이 존재하는 경우만 가지고 배열생성 (값이 0인 경우를 무시하기 위해서..)
        $sql = "SELECT *
                FROM g5_1_cast_shot_sub
                WHERE machine_id = '".$arr['machine_id']."' AND event_time >= '".$arr['st_dt']."' AND event_time <= '".$arr['en_dt']."'
        ";
        // echo $sql.'<br>';
        $rs = sql_query_pg($sql,1);
        $dta = array();
        for ($j=0; $row=sql_fetch_array_pg($rs); $j++) {
            // print_r2($row);
            // 각 태그별로 데이터 설정
            // hold_temp=보온로온도, upper_heat=상형히트, lower_heat=하형히트, upper_1_temp=상금형1, upper_2_temp=상금형2, upper_3_temp=상금형3, upper_4_temp=상금형4, upper_5_temp=상금형5
            // upper_6_temp=상금형6, lower_1_temp=하금형1, lower_2_temp=하금형2, lower_3_temp=하금형3
            // detect_pressure=검출압력, target_pressure=목표압력, control_pressure=조작압력, deviation_pressure=편차, temp_avg=평균온도, temp_max=온도최대, temp_min=온도최소
            // hum_avg=평균습도, hum_max=습도최대, hum_min=습도최소
            foreach($g5['set_data_name_value'] as $k1=>$v1) {
                // echo $k1.'=>'.$v1.'<br>';
                if($row[$k1] && !in_array($k1,$dta)) { // 값이 존재하는 것만!
                    $dta[] = $k1;
                }
            }
        }
        // print_r2($dta);
        // 수집된 항목만 가지고 배열생성
        for ($j=0; $j<sizeof($dta); $j++) {
            // echo $dta[$j].'<br>';
            // echo $g5['set_data_name_value'][$dta[$j]].'<br>';
            // echo $g5['set_data_pressure_no_value'][$dta[$j]].'<br>';
            // graph id 생성
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['dta_type'] = 1;    // 온도
            $ar['dta_no'] = $g5['set_data_temp_no_value'][$dta[$j]];
            $ar['type1'] = '';
            $graph_id = get_graph_id($ar);
            // echo $graph_id.'<br>';

            $array[$j]['name'] = $g5['set_data_name_value'][$dta[$j]];
            $array[$j]['id']['dta_data_url_host'] = 'hanjoo.epcs.co.kr';
            $array[$j]['id']['dta_data_url_path'] = '/user/json';
            $array[$j]['id']['dta_data_url_file'] = 'measure.php';
            $array[$j]['id']['mms_idx'] = $arr['mms_idx'];
            $array[$j]['id']['dta_type'] = $ar['dta_type'];
            $array[$j]['id']['dta_no'] = $ar['dta_no'];
            $array[$j]['id']['type1'] = '';
            $array[$j]['id']['graph_name'] = urlencode($array[$j]['name']);
            $array[$j]['id']['graph_id'] = $graph_id;
            $array[$j]['type'] = 'spline';
            $array[$j]['dashStyle'] = 'solid';
        }
    }

    return $array;
}
}


if(!function_exists('get_graph_id')){
function get_graph_id($arr) {
    // print_r2($arr);
    $graph_id1 = $arr['mms_idx'].'_'.$arr['dta_type'].'_'.$arr['dta_no'].'_'.$arr['type1'];
    $graph_id2 = preg_replace("/=/","",base64_encode($graph_id1));
    // echo 'f encoded > '.$graph_id2.'<br>';
    // $graph_id3 = base64_decode($graph_id2); // decode
    // echo 'f decoded > '.$graph_id3.'<br>';
    return $graph_id2;
}
}

// Seconds to H:M:s 초를 시:분:초
// t = seconds, f = separator 
if(!function_exists('sectohis')){
function sectohis($t,$f=':') {
    return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}
}

// 조직코드가 변경된 경우 처리할 함수
if(!function_exists('department_change')){
function department_change($mb_id, $dept1, $dept2) {
    global $g5;
	
	// 전부다 값이 있어야 함
	if(!$mb_id||!$dept1||!$dept2)
		return;
	
	// 이전코드랑 값이 같으면 리턴
	if($dept1==$dept2)
		return;

//	// 업체 연결 코드 전부 업데이트
//	$sql = "	UPDATE {$g5['company_member_table']}
//					SET trm_idx_department = '".$dept2."'
//				WHERE mb_id_saler = '".$mb_id."'
//	";
//    sql_query($sql,1);
//	
//	// 모든 게시판(gr_id=intra) 조직 코드(wr_7)를 수정
//	$sql = " SELECT bo_table FROM {$g5['board_table']} WHERE gr_id = 'intra' ";
//    $rs = sql_query($sql);
//    for($i=0;$row=sql_fetch_array($rs);$i++) {
//        //echo $row['bo_table'].'<br>';
//        $write_table = $g5['write_prefix'].$row['bo_table'];
//        $sql = "UPDATE ".$write_table." SET
//                    wr_7 = '".$dept2."'
//                WHERE wr_6 = '".$mb_id."' AND wr_7 = '".$dept1."'
//        ";
//        sql_query($sql,1);
//        //echo $sql.'<br>';
//    }
//	
//	// 모든 신청항목 조직코드 수정(g5_shop_order)
//    $sql = "UPDATE {$g5['g5_shop_order_table']} SET
//                trm_idx_department = '".$dept2."'
//            WHERE mb_id_saler = '".$mb_id."' AND trm_idx_department = '".$dept1."'
//    ";
//    sql_query($sql,1);
//    //echo $sql.'<br>';
//	
//	// 모든 신청상품 조직코드 수정(g5_shop_cart)
//    $sql = "UPDATE {$g5['g5_shop_cart_table']} SET
//                trm_idx_department = '".$dept2."'
//            WHERE mb_id_saler = '".$mb_id."' AND trm_idx_department = '".$dept1."'
//    ";
//    sql_query($sql,1);
//    //echo $sql.'<br>';
//
//	// 모든 매출의 조직코드 수정(g5_1_sales)
//    $sql = "UPDATE {$g5['sales_table']} SET
//                trm_idx_department = '".$dept2."'
//                , sls_department_name = '".$g5['department_name'][$dept2]."'
//            WHERE mb_id_saler = '".$mb_id."' AND trm_idx_department = '".$dept1."'
//    ";
//    sql_query($sql,1);
//    //echo $sql.'<br>';
    
	return true;
}
}

// 회원 레이어 - 원본 함수는 super일 때만 회원정보수정, 포인트관리가 나와서 수정함
if(!function_exists('get_sideview2')){
function get_sideview2($mb_id, $name='', $email='', $homepage='', $memo_yn=0, $formmail_yn=0, $profile_yn=0)
{
    global $config;
    global $g5;
    global $bo_table, $sca, $is_admin, $member;

    $email_enc = new str_encrypt();
    $email = $email_enc->encrypt($email);
    $homepage = set_http(clean_xss_tags($homepage));

    $name     = get_text($name, 0, true);
    $email    = get_text($email);
    $homepage = get_text($homepage);

    $tmp_name = "";
    if ($mb_id) {
        //$tmp_name = "<a href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" class=\"sv_member\" title=\"$name 자기소개\" target=\"_blank\" onclick=\"return false;\">$name</a>";
        $tmp_name = '<a href="'.G5_BBS_URL.'/profile.php?mb_id='.$mb_id.'" class="sv_member" title="'.$name.' 자기소개" target="_blank" onclick="return false;">';

        if ($config['cf_use_member_icon']) {
            $mb_dir = substr($mb_id,0,2);
            $icon_file = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb_id.'.gif';

            if (file_exists($icon_file)) {
                $width = $config['cf_member_icon_width'];
                $height = $config['cf_member_icon_height'];
                $icon_file_url = G5_DATA_URL.'/member/'.$mb_dir.'/'.$mb_id.'.gif';
                $tmp_name .= '<img src="'.$icon_file_url.'" width="'.$width.'" height="'.$height.'" alt="">';

                if ($config['cf_use_member_icon'] == 2) // 회원아이콘+이름
                    $tmp_name = $tmp_name.' '.$name;
            } else {
                  $tmp_name = $tmp_name." ".$name;
            }
        } else {
            $tmp_name = $tmp_name.' '.$name;
        }
        $tmp_name .= '</a>';

        $title_mb_id = '['.$mb_id.']';
    } else {
        if(!$bo_table)
            return $name;

        $tmp_name = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;sca='.$sca.'&amp;sfl=wr_name,1&amp;stx='.$name.'" title="'.$name.' 이름으로 검색" class="sv_guest" onclick="return false;">'.$name.'</a>';
        $title_mb_id = '[비회원]';
    }

    $str = "<span class=\"sv_wrap\">\n";
    $str .= $tmp_name."\n";

    $str2 = "<span class=\"sv\">\n";
    if($mb_id && $memo_yn)
        $str2 .= "<a href=\"".G5_BBS_URL."/memo_form.php?me_recv_mb_id=".$mb_id."\" onclick=\"win_memo(this.href); return false;\">쪽지보내기</a>\n";
    if($email && $formmail_yn)
        $str2 .= "<a href=\"".G5_BBS_URL."/formmail.php?mb_id=".$mb_id."&amp;name=".urlencode($name)."&amp;email=".$email."\" onclick=\"win_email(this.href); return false;\">메일보내기</a>\n";
    if($homepage)
        $str2 .= "<a href=\"".$homepage."\" target=\"_blank\">홈페이지</a>\n";
    if($mb_id && $profile_yn)
        $str2 .= "<a href=\"".G5_BBS_URL."/profile.php?mb_id=".$mb_id."\" onclick=\"win_profile(this.href); return false;\">자기소개</a>\n";
    if($bo_table) {
        if($mb_id)
            $str2 .= "<a href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=mb_id,1&amp;stx=".$mb_id."\">아이디로 검색</a>\n";
        else
            $str2 .= "<a href=\"".G5_BBS_URL."/board.php?bo_table=".$bo_table."&amp;sca=".$sca."&amp;sfl=wr_name,1&amp;stx=".$name."\">이름으로 검색</a>\n";
    }
    if($mb_id)
        $str2 .= "<a href=\"".G5_BBS_URL."/new.php?mb_id=".$mb_id."\">전체게시물</a>\n";
    if($member['mb_level'] >= 8 && $mb_id) {
        $str2 .= "<a href=\"".G5_ADMIN_URL."/member_form.php?w=u&amp;mb_id=".$mb_id."\" target=\"_blank\">회원정보변경</a>\n";
        $str2 .= "<a href=\"".G5_ADMIN_URL."/point_list.php?sfl=mb_id&amp;stx=".$mb_id."\" target=\"_blank\">포인트내역</a>\n";
    }
    $str2 .= "</span>\n";
    $str .= $str2;
    $str .= "\n<noscript class=\"sv_nojs\">".$str2."</noscript>";

    $str .= "</span>";

    return $str;
}
}


// 내 소속 조직을 SELECT 형식으로 얻음
if(!function_exists('get_dept_select')){
function get_dept_select($trm_idx=0,$sub_menu,$select_type='form')
{
    global $g5,$auth,$member,$department_form_options,$department_select_options;
    
    // form의 select 박스이면 <option value='20'></option>과 같은 특정 trm_idx 한개
    // 리스트 페이지의 조직 select에서는 <option value='1,43,20,35'></option>과 같은 trm_idx 여러개
    if(!$select_type)
        $select_type = 'select';

    // 삭제 권한이 있고 모든법인 접근 권한이 있는 경우는 전부
    if(!auth_check($auth[$sub_menu],'d',1) && $member['mb_firm_yn'] ) {
        return ${'department_'.$select_type.'_options'};
    }

    if(${'department_'.$select_type.'_options'}) {
        // 기본적으로는 나의 그룹 조직만 표현
        for($i=0; $i<sizeof($g5['department']); $i++) {
            if(preg_match("/".$g5['department_name'][$g5['department_uptop_idx'][$member['mb_2']]]."/", $g5['department'][$i]['up_names'])) {
                if($select_type=='form')
                    $str .= '<option value="'.$g5['department'][$i]['term_idx'].'"';
                else
                    $str .= '<option value="'.$g5['department'][$i]['down_idxs'].'"';
                if ($k1 == $selected)
                    $str .= ' selected="selected"';
                $str .= ">".$g5['department'][$i]['up_names']."</option>\n";
            }
        }
    }

    return $str;
}
}


// 팀 idxs 추출 함수, 접근 범위에 따라 idxs 추출이 달라짐
// 매개변수 level (1=직속상위까지, 2=그룹전체까지, 9=전체조직)
if(!function_exists('get_dept_idxs')){
function get_dept_idxs($level=0) {
    global $g5,$member;
    
    // 수퍼인 경우 모든 조직코드 리턴, $level=10인 경우는 조직 코드조건 필요없음 -> 전부
    if($member['mb_allauth_yn']) {
        $trm = sql_fetch(" SELECT GROUP_CONCAT(trm_idx) AS trm_idxs FROM {$g5['term_table']} WHERE trm_taxonomy = 'department' ");  // 삭제 포함 모든 조직코드값
        return false;
    }
    else if($member['mb_level']>=6) {
        //print_r3($member['mb_2']);
        //print_r3($g5['department_up1_idx'][$member['mb_2']].'(바로상위idx)의 down_idxs = '.$g5['department_down_idxs'][$g5['department_up1_idx'][$member['mb_2']]]);
        //print_r3($g5['department_uptop_idx'][$member['mb_2']].'(그룹최상위idx)의 down_idxs = '.$g5['department_down_idxs'][$g5['department_uptop_idx'][$member['mb_2']]]);
        //print_r3($g5['department_uptop_idx'][$member['mb_2']].'(최상위 삭제조직idx)의 idxs = '.$g5['department_trash_idxs'][$g5['department_uptop_idx'][$member['mb_2']]]);
        //print_r3($member);
        // 개별 설정이 있는 경우는 개별 설정이 우선함
        if($member['mb_group_level']) {
            // 직속상위까지만
            if($member['mb_group_level']==1) {
                $trm_idx = $g5['department_up1_idx'][$member['mb_2']];
            }
            // 그룹전체까지
            else if($member['mb_group_level']==2) {
                $trm_idx = $g5['department_uptop_idx'][$member['mb_2']];
            }
        }
        // 개별 설정이 없는 경우는 접근범위(매개변수)에 따라 설정
        else if($level) {
            // 직속상위까지만
            if($level==1) {
                $trm_idx = $g5['department_up1_idx'][$member['mb_2']];
            }
            // 그룹전체까지
            else if($level==2) {
                $trm_idx = $g5['department_uptop_idx'][$member['mb_2']];
            }
        }
        // 디폴트는 내 조직만
        else {
            $trm_idx = $member['mb_2'];
        }
        
        // 삭제조직코드도 포함해서 리턴
        return $g5['department_down_idxs'][$trm_idx].$g5['department_trash_idxs'][$member['mb_2']];
    }
    else {
        return false;
    }
}
}

// 
if(!function_exists('qr_cast_code_update')){
function qr_cast_code_update($arr)
{
	global $g5;
	
	if(!$arr['qrcode']||!$arr['cast_code']) {
		return false;
    }

    $g5_table_name = $g5['qr_cast_code_table'];
    $fields = sql_field_names($g5_table_name);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;
    // $arr[$pre.'_end_ym'] = $arr[$pre.'_end_year'].'-'.$arr[$pre.'_end_month'];   // 년월

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
    // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    // 중복 조건은 함수마다 다르게 설정!!
    $sql = "SELECT * FROM {$g5_table_name} 
            WHERE qrcode = '{$arr['qrcode']}' AND cast_code = '{$arr['cast_code']}'
    ";
    // echo $sql.'<br>';
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = "UPDATE {$g5_table_name} SET 
                    {$sql_common} 
				WHERE ".$pre."_idx = '".$row[$pre."_idx"]."'
        ";
		sql_query($sql,1);
	}
	else {
		$sql = "INSERT INTO {$g5_table_name} SET 
                    {$sql_common} 
                    , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
	}
//    echo $sql.'<br>';
    return $row[$pre."_idx"];
}
}

// 시간범위를 추출 (데이터가 없는 경우 최근 시간 범위로 설정)
// type=(data:데이터기준, current:현재시점기준), st_date, st_time, en_date, en_time, mms_idx, dta_type, dta_no
if(!function_exists('get_start_end_dt')){
function get_start_end_dt($arr) {

    // 시간차이(초)
    $diff_timestamp = strtotime($arr['en_date'].' '.$arr['en_time'])-strtotime($arr['st_date'].' '.$arr['st_time']);
    // 현재시점 기준으로 계산
    if($arr['type']=='current') {
        $en_date = date("Y-m-d",G5_SERVER_TIME);
        $en_time = date("H:i:s",G5_SERVER_TIME);
        $st_date = date("Y-m-d",G5_SERVER_TIME-$diff_timestamp);
        $st_time = date("H:i:s",G5_SERVER_TIME-$diff_timestamp);
        $start = $st_date.' '.$st_time;
        $end = $en_date.' '.$en_time;
    }
    // data 기반, 넘어온 시간을 기준으로 계산
    else {
        $st_date = $arr['st_date'];
        $st_time = $arr['st_time'];
        $en_date = $arr['en_date'];
        $en_time = $arr['en_time'];
        $start = $st_date.' '.$st_time;
        $end = $en_date.' '.$en_time;
    }

    $sql = "SELECT * FROM g5_1_data_measure_".$arr['mms_idx']."
        WHERE dta_type = '".$arr['dta_type']."' AND dta_no = '".$arr['dta_no']."'
            AND dta_dt >= '".$start."' AND dta_dt <= '".$end."'
        ORDER BY dta_dt DESC LIMIT 1
    ";
    // echo $sql.'<br>';
    $one1 = sql_fetch_pg($sql,1);
    // print_r2($one1);
    // 해당 범위에 값이 없으면 재설정
    if(!$one1['dta_idx']) {
        $sql = "SELECT * FROM g5_1_data_measure_".$arr['mms_idx']."
                WHERE dta_type = '".$arr['dta_type']."' AND dta_no = '".$arr['dta_no']."'
                ORDER BY dta_dt DESC LIMIT 1
        ";
        // echo $sql.'<br>';
        $one2 = sql_fetch_pg($sql,1);
        // print_r2($one2);
        // 마지막 시점을 기준으로 시간 범위를 거꾸로 역산 설정
        $end = substr($one2['dta_dt'],0,19);
        $en_timestamp = strtotime($end);
        $en_date = substr($end,0,10);;
        $en_time = substr($end,11,8);;
        $start = date("Y-m-d H:i:s",$en_timestamp-$diff_timestamp);
        $st_date = substr($start,0,10);;
        $st_time = substr($start,11,8);;
    }
    // echo $start.'~'.$end.'<br>';
    return array('start'=>$start,'st_date'=>$st_date,'st_time'=>$st_time,'end'=>$end,'en_date'=>$en_date,'en_time'=>$en_time);
}
}

// 초를 분으로 변환
if(!function_exists('sec2m')){
function sec2m($t) {
    return floor($t/60);
}
}

// 초를 시:분:초 로 변환 ex)00:11:25
if(!function_exists('sec2hms')){
function sec2hms($t,$f=':') { // t = seconds, f = separator 
    return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}
}

// 임계치 범위 추출
if(!function_exists('get_range')){
function get_range($val, $arr) {
    global $g5;

    $str = 'ok';  // 정상
    if(!$arr[0]||!$arr[1]||!$arr[2]||!$arr[3]||!$arr[4]||!$arr[5]) {
        return $str;
    }
    if($val >= $arr[2])
        $str = 't3';    // 상단위험
    else if($val >= $arr[1])
        $str = 't2';    // 상단경고
    else if($val >= $arr[0])
        $str = 't1';    // 상단주의
    else if($val <= $arr[5])
        $str = 'b3';    // 하단위험
    else if($val <= $arr[4])
        $str = 'b2';    // 하단경고
    else if($val <= $arr[3])
        $str = 'b1';    // 하단주의

    return $str;
}
}    


// 알람 메시지 발송 업데이트
if(!function_exists('update_alarm_send')){
function update_alarm_send($arr) {
    global $g5;

    $sql = " INSERT INTO {$g5['alarm_send_table']} SET
            arm_idx = '".$arr['alarm_idx']."'
            , mms_idx = '".$arr['mms_idx']."'
            , ars_cod_code = '".$arr['code']."'
            , ars_send_type = '".$arr['send_type']."'
            , ars_hp = '".$arr['hp']."'
            , ars_email = '".$arr['email']."'
            , ars_status = 'ok'
            , ars_reg_dt = '".G5_TIME_YMDHIS."'
    ";
    sql_query($sql,1);
    $idx = sql_insert_id();

    return $idx;
}
}    

// 태그알람 메시지 발송 업데이트
if(!function_exists('update_alarm_tag_send')){
function update_alarm_tag_send($arr) {
    global $g5;

    $sql = " INSERT INTO {$g5['alarm_tag_send_table']} SET
            amt_idx = '".$arr['alarm_idx']."'
            , mms_idx = '".$arr['mms_idx']."'
            , ats_tgc_code = '".$arr['code']."'
            , ats_send_type = '".$arr['send_type']."'
            , ats_hp = '".$arr['hp']."'
            , ats_email = '".$arr['email']."'
            , ats_status = 'ok'
            , ats_reg_dt = '".G5_TIME_YMDHIS."'
    ";
    // echo $sql.PHP_EOL;
    sql_query($sql,1);
    $idx = sql_insert_id();

    return $idx;
}
}    


// 푸시발송함수
// send_number, arm_table=('alarm','alarm_tag'),towhom_hp, arm_name, alarm_idx, mms_idx, arm_code, msg_body, push_url
if(!function_exists('send_push')){
function send_push($arr) {
    global $g5,$config;

    $arr["push_title"] = '['.$arr['arm_code'].'] '.$arr['arm_name'];

    for($j=0;$j<sizeof($arr['towhom_hp']);$j++) {
        // 회원정보 검색
        $sql = "SELECT mb_id, mb_6 FROM {$g5['member_table']}
                WHERE mb_leave_date = ''
                    AND REPLACE(mb_hp,'-','') = '".preg_replace('/-/','',$arr['towhom_hp'][$j])."'
                LIMIT 1
        ";
        // echo $sql.'<br>';
        $mb = sql_fetch($sql,1);
        if(!$mb['mb_id']||!$mb['mb_6']) {
            return false;
        }
        $arr['push_key'] = $mb['mb_6']; // 푸시키 정보 추출

        $headings = array(
            "en" => $arr["push_title"]
        );
        $content = array(
            "en" => $arr["msg_body"]
        );
        $fields = array(
            'app_id' => $g5['setting']['set_onesignal_id'],
            'include_player_ids' => array($arr['push_key']),
            'data' => array(
                "url" => $arr['push_url']
            ),
            'headings' => $headings,
            'contents' => $content
        );
        $fields = json_encode($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.$g5['setting']['set_onesignal_key']
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);

        // 발송기록 저장
        $ar['alarm_idx'] = $arr['alarm_idx'];
        $ar['mms_idx'] = $arr['mms_idx'];
        $ar['code'] = $arr['arm_code'];
        $ar['send_type'] = 'push';
        $ar['hp'] = $arr['towhom_hp'][$j];
        $ar['email'] = '';
        $response['alarm_idx'] = ($arr['arm_table']=='alarm') ? update_alarm_send($ar):update_alarm_tag_send($ar);
        unset($ar);
    }
    // print_r2($response);

    return $response;
}
}    

// 문자발송함수
// arm_table=('alarm','alarm_tag'),towhom_hp, send_number, alarm_idx, mms_idx, arm_code, msg_body
if(!function_exists('send_sms_lms')){
function send_sms_lms($arr) {
    global $g5,$config;

    if($config['cf_sms_type'] == 'LMS') {
        $port_setting = get_icode_port_type($config['cf_icode_id'], $config['cf_icode_pw']);

        // SMS 모듈 클래스 생성
        if($port_setting !== false) {
            $SMS = new LMS;
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $port_setting);

            for($j=0;$j<sizeof($arr['towhom_hp']);$j++) {

                $strDest[]   = preg_replace("/[^0-9]/", "", $arr['towhom_hp'][$j]);

                // 발송기록 저장, 일단 발송했다고 봄
                $ar['alarm_idx'] = $arr['alarm_idx'];
                $ar['mms_idx'] = $arr['mms_idx'];
                $ar['code'] = $arr['arm_code'];
                $ar['send_type'] = 'lms';
                $ar['hp'] = $arr['towhom_hp'][$j];
                $ar['email'] = '';
                $alarm_idx = ($arr['arm_table']=='alarm') ? update_alarm_send($ar):update_alarm_tag_send($ar);
                unset($ar);

            }
            // $strDest[]   = $receive_number;
            $strCallBack = $arr['send_number'];
            $strCaller   = iconv_euckr(trim($config['cf_title']));
            $strSubject  = '';
            $strURL      = '';
            $strData     = iconv_euckr($arr['msg_body']);
            $strDate     = '';
            $nCount      = count($strDest);

            $res = $SMS->Add($strDest, $strCallBack, $strCaller, $strSubject, $strURL, $strData, $strDate, $nCount);

            $SMS->Send();
            $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
        }
    }
    else {
        $SMS = new SMS; // SMS 연결
        $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
        // $SMS->Add($receive_number, $arr['send_number'], $config['cf_icode_id'], iconv_euckr(stripslashes($arr['msg_body'])), "");
        for($j=0;$j<sizeof($arr['towhom_hp']);$j++) {

            $SMS->Add(preg_replace("/[^0-9]/", "", $arr['towhom_hp'][$j]), $arr['send_number'], $config['cf_icode_id'], iconv_euckr(stripslashes($arr['msg_body'])), "");

            // 발송기록 저장, 일단 발송했다고 봄
            $ar['alarm_idx'] = $arr['alarm_idx'];
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['code'] = $arr['arm_code'];
            $ar['send_type'] = 'sms';
            $ar['hp'] = $arr['towhom_hp'][$j];
            $ar['email'] = '';
            $alarm_idx = ($arr['arm_table']=='alarm') ? update_alarm_send($ar):update_alarm_tag_send($ar);
            unset($ar);
        }
        $SMS->Send();
        $SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
    }

    return $alarm_idx;
}
}    

// 이메일발송함수
// arm_table=('alarm','alarm_tag'),towhom_email, towhom_name, mms_name, arm_name, arm_code, alarm_idx, mms_idx, msg_body
if(!function_exists('send_email')){
function send_email($arr) {
    global $g5,$config;

    for($j=0;$j<sizeof($arr['towhome_email']);$j++) {

        $sw = preg_match("/[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*@[0-9a-zA-Z_]+(\.[0-9a-zA-Z_]+)*/", $arr['towhome_email'][$j]);
        // 올바른 메일 주소 & if is is under today limit
        if ($sw == true) {
            // echo $arr['towhome_email'][$j].'<br>';
            $patterns = array ( '/{이름}/'
                                ,'/{설비명}/','/{코드명}/'
                                ,'/{코드}/','/{내용}/'
                                ,'/{년월일}/','/{HOME_URL}/'
                            );
                            // print_r2($patterns);
            $replace = array (  $arr['towhom_name'][$j]
                                ,$arr['mms_name'], $arr['arm_name']
                                ,$arr['arm_code'], conv_content($arr['msg_body'],2)
                                ,G5_TIME_YMD, G5_URL
                            );
                            // print_r2($replace);

            $towhom['subject'] = preg_replace($patterns,$replace
                                            ,$g5['setting']['set_tag_subject']);
            $towhom['content'] = preg_replace($patterns,$replace
                                            ,$g5['setting']['set_tag_content']);
            // echo $towhom['subject'].'<br>';
            // echo $towhom['content'].'<br>';

            // 메일발송
            mailer($config['cf_admin_email_name'].'(발신전용)', $config['cf_admin_email'], $arr['towhome_email'][$j], $towhom['subject'], $towhom['content'], 1);

            // 발송기록 저장
            $ar['alarm_idx'] = $arr['alarm_idx'];
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['code'] = $arr['arm_code'];
            $ar['send_type'] = 'email';
            $ar['hp'] = '';
            $ar['email'] = $arr['towhome_email'][$j];
            $alarm_idx = ($arr['arm_table']=='alarm') ? update_alarm_send($ar):update_alarm_tag_send($ar);
            unset($ar);
        
        }

    }
    return $alarm_idx;
}
}  

// 메시지 발송 함수
// arm_table=('alarm','alarm_tag'), arm_idx=알람번호, amt_idx=알람태그번호, msg_type=메세지타입(sms,push,email), com_msg_type=업체메시지타입, mms_idx=설비번호, mms_name=설비명
// arm_code=태그코드, arm_name=태그명, reports=알림대상(json), msg_limit=알림과부하설정, msg_body=내용
if(!function_exists('send_message')){
function send_message($arr)
{
    global $g5, $config;

    // 하루 메시지(모든 메시지 email, sms, push 전부 중에서..) 발송 횟수를 넘어가면 발송 중지
    if($arr['arm_table']=='alarm') {
        $sql = "SELECT COUNT(ars_idx) AS cnt FROM {$g5['alarm_send_table']}
                WHERE mms_idx = '".$arr['mms_idx']."' AND ars_cod_code = '".$arr['arm_code']."'
                    AND ars_reg_dt > DATE_ADD(now(), INTERVAL -24 HOUR)
        ";
    }
    else {
        $sql = "SELECT COUNT(ats_idx) AS cnt FROM {$g5['alarm_tag_send_table']}
                WHERE mms_idx = '".$arr['mms_idx']."' AND ats_tgc_code = '".$arr['arm_code']."'
                    AND ats_reg_dt > DATE_ADD(now(), INTERVAL -24 HOUR)
        ";
    }
    // echo $sql.PHP_EOL;
	$one = sql_fetch($sql);
    if($one['cnt'] >= $arr['msg_limit']) {
        return 0;
    }

    // 발신자번호
    $send_number = preg_replace("/[^0-9]/", "", $g5['setting']['set_from_number']);

    // 발송자 정보 추출
    $infos = json_decode($arr['reports'], true);
    if(is_array($infos)) {
        foreach($infos as $k1 => $v1) {
            // echo $k1.'<br>';
            // print_r2($v1);
            for($j=0;$j<sizeof($v1);$j++) {
                // cell phone array
                if($k1=='r_name') {
                    $towhom_name[] = trim($v1[$j]);
                }
                // cell phone array, remove '-' mark from hp numbers.
                if($k1=='r_hp') {
                    $towhom_hp[] = preg_replace("/[^0-9]/","",trim($v1[$j]));
                }
                // set email array
                else if($k1=='r_email') {
                    $towhom_email[] = trim($v1[$j]);
                }
            }
        }
    }
    // print_r2($towhom_hp);
    // print_r2($towhom_email);

    // 메시지 발송 타입
    $msg_types = explode(",",$arr['msg_type']);

	//문자 발송
	if(in_array("sms",$msg_types)) {
        // 문자 발송
        if ($config['cf_sms_use'] == 'icode' && count($towhom_hp) > 0) {
            // send_number, arm_table=('alarm','alarm_tag'),towhom_hp, alarm_idx, mms_idx, arm_code, msg_body
            $ar['send_number'] = $send_number;
            $ar['arm_table'] = $arr['arm_table'];
            $ar['towhom_hp'] = $towhom_hp;
            $ar['alarm_idx'] = ($arr['arm_table']=='alarm') ? $arr['arm_idx'] : $arr['amt_idx'];
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['arm_code'] = $arr['arm_code']; // tgc_code or cod_code
            $ar['msg_body'] = $arr['msg_body'];
            send_sms_lms($ar);  // 함수 호출
            // print_r2($ar);
            unset($ar);
        }
	}

	//이메일 발송
	if(in_array("email",$msg_types)) {
        // arm_table=('alarm','alarm_tag'),towhom_email, towhom_name, mms_name, arm_name, arm_code, alarm_idx, mms_idx
        $ar['arm_table'] = $arr['arm_table'];
        $ar['towhome_email'] = $towhom_email;
        $ar['towhom_name'] = $towhom_name;
        $ar['mms_name'] = $arr['mms_name'];
        $ar['arm_name'] = $arr['arm_name'];
        $ar['alarm_idx'] = ($arr['arm_table']=='alarm') ? $arr['arm_idx'] : $arr['amt_idx'];
        $ar['mms_idx'] = $arr['mms_idx'];
        $ar['arm_code'] = $arr['arm_code']; // tgc_code or cod_code
        $ar['msg_body'] = $arr['msg_body'];
        send_email($ar);  // 함수 호출
        unset($ar);
	}

	//푸시 발송
	if(in_array("push",$msg_types)) {
        if (count($towhom_hp) > 0) {
            // send_number, arm_table=('alarm','alarm_tag'),towhom_hp, arm_name, alarm_idx, mms_idx, arm_code, msg_body
            $ar['send_number'] = $send_number;
            $ar['arm_table'] = $arr['arm_table'];
            $ar['towhom_hp'] = $towhom_hp;
            $ar['arm_name'] = $arr['arm_name'];
            $ar['alarm_idx'] = ($arr['arm_table']=='alarm') ? $arr['arm_idx'] : $arr['amt_idx'];
            $ar['mms_idx'] = $arr['mms_idx'];
            $ar['arm_code'] = $arr['arm_code']; // tgc_code or cod_code
            $ar['msg_body'] = $arr['msg_body'];
            $ar['push_url'] = G5_USER_ADMIN_URL.'/message_list.php';
            send_push($ar);  // 함수 호출
            unset($ar);
        }
	}

    return true;
}
}

// Message send_type setting
// array: prefix, com_idx, value
if(!function_exists('set_send_type')){
function set_send_type($arr) {
	global $g5;
	
    // Get the company info.
    $com = get_table_meta('company','com_idx',$arr['com_idx']);

    $set_values = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_send_type']));
    foreach ($set_values as $set_value) {
        list($key, $value) = explode('=', $set_value);
        // echo $key.'/',$value.'<br>';
        // 해당 업체 발송 설정을 먼저 체크해서 비활성 표현
        if(!preg_match("/".$key."/i",$com['com_send_type'])) {
            ${"disable_".$key} = ' disabled'; 
        }
        ${"checked_".$key} = (preg_match("/".$key."/i",$arr['value'])) ? 'checked':''; 
        $str .= '<label for="set_send_type_'.$key.'" class="set_send_type" '.${"disable_".$key}.'>
                <input type="checkbox" id="set_send_type_'.$key.'"
                    name="'.$arr['prefix'].'_send_type[]" value="'.$key.'"
                     '.${"checked_".$key}.${"disable_".$key}.'>'.$value.'('.$key.')
            </label>';
    }

    return $str;
}
}


// 직책, 직급을 SELECT 형식으로 얻음
if(!function_exists('get_set_options_select')){
function get_set_options_select($set_variable, $start=0, $end=200, $selected="",$sub_menu)
{
    global $g5,$auth;

    // 삭제 권한이 있으면 전부
    if(!auth_check($auth[$sub_menu],'d',1)) {
        return $g5[$set_variable.'_options_value'];
    }
    
    if(is_array($g5[$set_variable.'_value'])) {
        foreach ($g5[$set_variable.'_value'] as $k1=>$v1) {
            if($k1 >= $start && $k1 <= $end) {
                $str .= '<option value="'.$k1.'"';
                if ($k1 == $selected)
                    $str .= ' selected="selected"';
                $str .= ">{$v1}</option>\n";
            }
        }    
    }

    return $str;
}
}

// token 체크 판단
if(!function_exists('check_token1')){
function check_token1($token) {

    $str = true;
    $expire_date = 86400*30*6; // 약 6개월 정도

    // 기존 방법 체크, 12자리수 보다 적은 경우, ex) 1099de5drf09
    if( strlen($token) <= 12 ) {
        $to[] = substr($token,0,2);
        $to[] = substr($token,2,2);
        $to[] = substr($token,-2);
        $to[] = substr((string)((int)$to[0]+(int)$to[1]),-2);
        //print_r2($to);
        if($to[2]!=$to[3]) {
            $str = false;
        }
    }
    // 공개키 같은 경우 기간 제한 있음 ex) 2451RNC4xg161355065075
    else {
        $to[] = substr($token,0,2);
        $to[] = substr($token,2,2);
        $to[] = substr($token,-2);
        $to[] = substr((string)((int)$to[0]+(int)$to[1]),-2);
        $to[] = substr($token,10,-2);
        // print_r2($to);
        if($to[2]!=$to[3] || $to[4] < time()-$expire_date) {
            $str = false;
        }
    }
    return $str;
}
}

// make token 함수
if(!function_exists('make_token1')){
function make_token1() {
	// 토큰 생성
	$to[] = rand(10,99);
	$to[] = rand(10,99);
	$to[] = G5_SERVER_TIME;
	$to[] = sprintf("%02d",substr($to[0]+$to[1],-2));
	$token = $to[0].$to[1].random_str(6).$to[2].$to[3];
	//echo $token.'<br>';
    return $token;
}
}

// 배너출력
if(!function_exists('display_banner10')){
function display_banner10($bo_table, $device, $skin_dir='', $skin='', $position, $subject_len=40, $cache_time=1)
{
    global $g5;

    if (!$position) $position = 'main';
    if (!$skin) $skin = 'boxbanner.skin.php';

    if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
        if (G5_IS_MOBILE) {
            $banner_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            if(!is_dir($banner_skin_path))
                $banner_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            $banner_skin_url = str_replace(G5_PATH, G5_URL, $banner_skin_path);
        } else {
            $banner_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            $banner_skin_url = str_replace(G5_PATH, G5_URL, $banner_skin_path);
        }
        $skin_dir = $match[1];
    } else {
        if(G5_IS_MOBILE) {
            $banner_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/board/'.$skin_dir;
            $banner_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/board/'.$skin_dir;
        } else {
            $banner_skin_path = G5_SKIN_PATH.'/board/'.$skin_dir;
            $banner_skin_url  = G5_SKIN_URL.'/board/'.$skin_dir;
        }
    }

    // $cache_fwrite = false;
    $cache_fwrite = true;
    if(G5_USE_CACHE) {
        $cache_file = G5_DATA_PATH."/cache/banner10-{$bo_table}-{$skin_dir}-{$skin}-{$device}-serial.php";

        if(!file_exists($cache_file)) {
            $cache_fwrite = true;
        } else {
            if($cache_time > 0) {
                $filetime = filemtime($cache_file);
                if($filetime && $filetime < (G5_SERVER_TIME - 3600 * $cache_time)) {
                    @unlink($cache_file);
                    $cache_fwrite = true;
                }
            }
            
            if(!$cache_fwrite) {
                try{
                    $file_contents = file_get_contents($cache_file);
                    $file_ex = explode("\n\n", $file_contents);
                    $caches = unserialize(base64_decode($file_ex[1]));
                    echo $file_contents;

                    $list = (is_array($caches) && isset($caches['list'])) ? $caches['list'] : array();
                    $bo_subject = (is_array($caches) && isset($caches['bo_subject'])) ? $caches['bo_subject'] : '';
                } catch(Exception $e){
                    $cache_fwrite = true;
                    $list = array();
                }
            }
        }
    }

    if(!G5_USE_CACHE || $cache_fwrite) {
        $list = array();
        
        // $device 관련
        $sql_device = (G5_IS_MOBILE) ? " AND wr_8 IN ('all','mo') " : " AND wr_8 IN ('all','pc') ";

        // 게시물 관련 정보
		$tmp_write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
		$sql = "SELECT * FROM {$tmp_write_table}
                WHERE ca_name = '".$position."' AND wr_is_comment = 0
                    {$sql_device}
                ORDER BY convert(wr_7, decimal)
        ";
        // echo $sql.'<br>';
        $result = sql_query($sql,1);
        for ($i=0; $row = sql_fetch_array($result); $i++) {
	        $row['file'] = get_file($bo_table, $row['wr_id']);
			
            $list[$i] = $row;
        }
        

        if($cache_fwrite) {
            $handle = fopen($cache_file, 'w');
            $caches = array(
                'list' => $list,
                );
            $cache_content = "<?php if (!defined('_GNUBOARD_')) exit; ?>\n\n";
            $cache_content .= serialize($caches);  //serialize

            fwrite($handle, $cache_content);
            fclose($handle);

            @chmod($cache_file, 0640);
        }
    }
    //print_r2($list);

    ob_start();
    include $banner_skin_path.'/'.$skin;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
}


// 네비 메뉴 출력
if(!function_exists('board_navi10')){
function board_navi10($bo_table, $device, $skin_dir='', $skin='', $subject_len=40, $cache_time=1)
{
    global $g5;

    if (!$skin) $skin = 'navi.skin.php';
    
    if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
        if (G5_IS_MOBILE) {
            $navi_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            if(!is_dir($navi_skin_path))
                $navi_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            $navi_skin_url = str_replace(G5_PATH, G5_URL, $navi_skin_path);
        } else {
            $navi_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/board/'.$match[1];
            $navi_skin_url = str_replace(G5_PATH, G5_URL, $navi_skin_path);
        }
        $skin_dir = $match[1];
    } else {
        if(G5_IS_MOBILE) {
            $navi_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/board/'.$skin_dir;
            $navi_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/board/'.$skin_dir;
        } else {
            $navi_skin_path = G5_SKIN_PATH.'/board/'.$skin_dir;
            $navi_skin_url  = G5_SKIN_URL.'/board/'.$skin_dir;
        }
    }

    //$cache_fwrite = false;
    $cache_fwrite = true;
    if(G5_USE_CACHE) {
        $cache_file = G5_DATA_PATH."/cache/navi10-{$bo_table}-{$skin_dir}-{$skin}-{$device}-serial.php";

        if(!file_exists($cache_file)) {
            $cache_fwrite = true;
        } else {
            if($cache_time > 0) {
                $filetime = filemtime($cache_file);
                if($filetime && $filetime < (G5_SERVER_TIME - 3600 * $cache_time)) {
                    @unlink($cache_file);
                    $cache_fwrite = true;
                }
            }
            
            if(!$cache_fwrite) {
                try{
                    $file_contents = file_get_contents($cache_file);
                    $file_ex = explode("\n\n", $file_contents);
                    $caches = unserialize(base64_decode($file_ex[1]));
                    echo $file_contents;

                    $list = (is_array($caches) && isset($caches['list'])) ? $caches['list'] : array();
                    $bo_subject = (is_array($caches) && isset($caches['bo_subject'])) ? $caches['bo_subject'] : '';
                } catch(Exception $e){
                    $cache_fwrite = true;
                    $list = array();
                }
            }
        }
    }

    if(!G5_USE_CACHE || $cache_fwrite) {
        $list = array();
        
        // $device 관련
        $sql_device_pc = ($device=='pc') ? " AND wr1.wr_1 = '' " : "";
        $sql_device_mobile = ($device=='mobile') ? " AND wr1.wr_2 = '' " : "";

        // 게시물 관련 정보
		$tmp_write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
		$sql = "	SELECT wr1.wr_id, wr1.wr_reply, wr1.wr_subject, wr1.wr_link1, wr1.wr_file, wr1.wr_1, wr1.wr_2, wr1.wr_3, wr1.wr_4, wr1.wr_5, wr1.wr_10
						,GROUP_CONCAT(wr2.wr_subject ORDER BY wr2.wr_reply SEPARATOR '^') AS group_subject
						,GROUP_CONCAT(wr2.wr_content ORDER BY wr2.wr_reply SEPARATOR '^') AS group_content
						,GROUP_CONCAT(wr2.wr_link1 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_link1
						,GROUP_CONCAT(wr2.wr_1 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_1
						,GROUP_CONCAT(wr2.wr_2 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_2
						,GROUP_CONCAT(wr2.wr_3 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_3
						,GROUP_CONCAT(wr2.wr_4 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_4
						,GROUP_CONCAT(wr2.wr_5 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_5
						,GROUP_CONCAT(wr2.wr_6 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_6
						,GROUP_CONCAT(wr2.wr_10 ORDER BY wr2.wr_reply SEPARATOR '^') AS group_wr_10
						,COUNT(wr2.wr_id) AS group_count
					FROM {$tmp_write_table} AS wr1
						JOIN {$tmp_write_table} AS wr2
					WHERE wr1.wr_is_comment = 0 
                        {$sql_device_pc}
                        {$sql_device_mobile}
                        AND wr1.wr_4 = ''
                        AND wr1.wr_9 = '' AND wr2.wr_9 = ''
						AND wr1.wr_num = wr2.wr_num
						AND wr2.wr_reply LIKE CONCAT(wr1.wr_reply,'%')
					GROUP BY wr1.wr_num, wr1.wr_reply
					ORDER BY wr1.wr_num DESC, wr1.wr_reply
		";
        $result = sql_query($sql);
        for ($i=0; $row = sql_fetch_array($result); $i++) {
			// 단계
			$row['wr_depth'] = strlen($row['wr_reply']);
			
			// 그룹 배열 
			$row['group_subject_items'] = explode('^', $row['group_subject']);
			$row['group_content_items'] = explode('^', $row['group_content']);
			for ($j=0; $j<count($row['group_content_items']); $j++) {
				$row['group_content_items'][$j] = unserialize($row['group_content_items'][$j]);
			}
			$row['group_link1_items'] = explode('^', $row['group_link1']);
			$row['group_wr_1_items'] = explode('^', $row['group_wr_1']);
			$row['group_wr_2_items'] = explode('^', $row['group_wr_2']);
			$row['group_wr_3_items'] = explode('^', $row['group_wr_3']);
			$row['group_wr_4_items'] = explode('^', $row['group_wr_4']);
			$row['group_wr_5_items'] = explode('^', $row['group_wr_5']);
			$row['group_wr_6_items'] = explode('^', $row['group_wr_6']);
			$row['group_wr_10_items'] = explode('^', $row['group_wr_10']);
			
            $list[$i] = $row;
        }
        

        if($cache_fwrite) {
            $handle = fopen($cache_file, 'w');
            $caches = array(
                'list' => $list,
                );
            $cache_content = "<?php if (!defined('_GNUBOARD_')) exit; ?>\n\n";
            $cache_content .= base64_encode(serialize($caches));  //serialize

            fwrite($handle, $cache_content);
            fclose($handle);

            @chmod($cache_file, 0640);
        }
    }
    //print_r2($list);

    ob_start();
    include $navi_skin_path.'/'.$skin;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}
}

// 네비 정보 출력 (사용자단), 관리자단 부분은 skin단 list.html 참조
// $rtn 값이 없으면(디폴트) echo 로 li를 바로 뿌려준다.
if(!function_exists('put_navi_menu')){
function put_navi_menu($row, $rtn=0, $class_name='active')
{
    global $g5,$ca,$it,$bo_table,$cont;

    // 배열전체를 복사
    $list = $row;
    unset($row);
//    print_r2($list);

	// 메뉴 active 설정
//    echo $g5['navi_menu'].'<br>';
//    echo $list['wr_10'].'='.substr($g5['navi_menu'],0,($list['wr_depth']+1)*2).'<br>';
    // 게시물 링크인 경우 기본 active룰을 따른다.
	if($g5['navi_menu'])
		$list['wr_active'] = ( substr($g5['navi_menu'],0,($list['wr_depth']+1)*2) == $list['wr_10'] ) ? $class_name : '';
    
    // 게시판 링크만 있는 경우 (1단계메뉴와 2단계 메뉴가 같은 링크일 수 있으므로 예외처리해 줘야 함)
    if( !parse_url2($list['wr_link1'],"wr_id") && parse_url2($list['wr_link1'],"bo_table") ) {
        //print_r2($board);
        //echo $g5['navi_menu'].'/'.$board['bo_10'].'<br>';
        // 1단계인 경우는 게시판 앞에 두자리 코드만 같으면 active 처리해 줘야 함
        // 2단계인 경우는 bo_table까지 일치해야 한다.
        if($list['wr_depth']==0)
            $list['wr_active'] = ( substr($g5['navi_menu'],0,2) == $list['wr_10'] ) ? $class_name : '';
        else
            $list['wr_active'] = ( parse_url2($list['wr_link1'],"bo_table") == $bo_table && $g5['navi_menu'] == $list['wr_10'] ) ? $class_name : '';
    }
    // 쇼핑몰 카테고리 링크인 경우
    if( parse_url2($list['wr_link1'],"ca_id") ) {
        //print_r2($ca);
		$list['wr_active'] = ( $g5['navi_menu'] == $ca['ca_10'] ) ? $class_name : '';
    }
    // 상품 상세보기 링크인 경우
    if( parse_url2($list['wr_link1'],"it_id") ) {
        //print_r2($it);
		$list['wr_active'] = ( $g5['navi_menu'] == $it['it_10'] ) ? $class_name : '';
    }
    // 내용 콘텐츠인 경우
    if( parse_url2($list['wr_link1'],"co_id") ) {
        //print_r2($it);
		$list['wr_active'] = ( $g5['navi_menu'] == $cont['co_10'] ) ? $class_name : '';
    }
	
    // 새창 띄우기인지
    $list['wr_target'] = (!$list['wr_3']) ? '' : ' target="_blank"';
    //print_r2($list);
    
    // 링크 있으면
    if($list['wr_link1']) {
        // 링크에 link= 있으면 내부링크
        if( preg_match("/link=/",$list['wr_link1']) )
            $list['wr_link1'] = "javascript:scrollto('".parse_url2($list['wr_link1'],"link")."')";
        else
            $list['wr_link1'] = add_g5_url($list['wr_link1']);
    }
    else {
		$list['wr_link1'] = "javascript:";
    }
    // 링크 닫기
    $list['_a'] = ( $list['wr_link1'] ) ? '</a>' : '';


    // 첨부 파일이 있는 경우 (메뉴 이미지로 활용, 두개인 경우 오버 효과까지!)
    if( is_array($list['wr_file']) ) {
        $list['file'] = get_file($list['bo_table'], $list['wr_id']);
        // 첨부파일 썸네일 추출
        $list['thumb'] = get_list_thumbnail($list['bo_table'], $list['wr_id'], $thumb_width, $thumb_height, false, true);
        if($list['thumb']['src']) {
            $list['img'] = $list['thumb']['src'];
        } else {
            $list['img'] = G5_IMG_URL.'/no_img.png';
            $list['thumb']['alt'] = '이미지가 없습니다.';
        }
        $list['img_content'] = '<img src="'.$list['img'].'" alt="'.$list['thumb']['alt'].'" >';
    }
   
    $list['depth_no'] = ($list['wr_depth']==1) ? '1010' : '10';
    $list['depth_no'] = ($list['wr_depth']==2) ? '101010' : $list['depth_no'];
    
    // 항목 출력
    $list['li'] = '
        <li class="li'.$list['depth_no'].' '.$list['wr_active'].'">
            <a href="'.$list['wr_link1'].'" class="" '.$list['wr_target'].'>'.$list['wr_subject'].'</a>
    '.PHP_EOL;
    if(!$rtn) {echo $list['li'];}

    return $list;
}
}


// 뿌리오 메시지 발송
if(!function_exists('ppurio_send')){
function ppurio_send($arr)
{
	global $g5,$member;
    
    $_api_url = 'https://message.ppurio.com/api/send_utf8_json.php';     // UTF-8 인코딩과 JSON 응답용 호출 페이지

    $_param['userid'] = $g5['setting']['set_ppurio_userid'];           // [필수] 뿌리오 아이디
//    $_param['callback'] = hyphen_hp_remove($g5['setting']['set_ppurio_callback']);    // [필수] 발신번호 - 숫자만
    $_param['callback'] = trim($arr['from_number']);    // [필수] 발신번호 - 숫자만
    $_param['phone'] = hyphen_hp_remove($arr['to_number']);       // [필수] 수신번호 - 여러명일 경우 |로 구분 '010********|010********|010********'
    $_param['msg'] = $arr['content'];   // [필수] 문자내용 - 이름(names)값이 있다면 [*이름*]가 치환되서 발송됨
//    $_param['names'] = '';            // [선택] 이름 - 여러명일 경우 |로 구분 '홍길동|이순신|김철수'

    $_curl = curl_init();
    curl_setopt($_curl,CURLOPT_URL,$_api_url);
    curl_setopt($_curl,CURLOPT_POST,true);
    curl_setopt($_curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($_curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($_curl,CURLOPT_POSTFIELDS,$_param);
    $_result = curl_exec($_curl);
    curl_close($_curl);

    $_result = json_decode($_result);
/*
 * 응답값
 *
 *  <성공시>
 *    result : 'ok'                           - 전송결과 성공
 *    type   : 'sms'                          - 단문은 sms 장문은 lms 포토문자는 mms
 *    msgid  : '123456789'                    - 발송 msgid (예약취소시 필요)
 *    ok_cnt : 1                              - 발송건수
 *
 *  <실패시>
 *    result : 'invalid_member'               - 연동서비스 신청이 안 됐거나 없는 아이디
 *    result : 'under_maintenance'            - 요청시간에 서버점검중인 경우
 *    result : 'allow_https_only'             - http 요청인 경우
 *    result : 'invalid_ip'                   - 등록된 접속가능 IP가 아닌 경우
 *    result : 'invalid_msg'                  - 문자내용에 오류가 있는 경우
 *    result : 'invalid_names'                - 이름에 오류가 있는 경우
 *    result : 'invalid_subject'              - 제목에 오류가 있는 경우
 *    result : 'invalid_sendtime'             - 예약발송 시간에 오류가 있는 경우 (10분이후부터 다음해말까지 가능)
 *    result : 'invalid_sendtime_maintenance' - 예약발송 시간에 서버점검 예정인 경우
 *    result : 'invalid_phone'                - 수신번호에 오류가 있는 경우
 *    result : 'invalid_msg_over_max'         - 문자내용이 너무 긴 경우
 *    result : 'invalid_callback'             - 발신번호에 오류가 있는 경우
 *    result : 'once_limit_over'              - 1회 최대 발송건수 초과한 경우
 *    result : 'daily_limit_over'             - 1일 최대 발송건수 초과한 경우
 *    result : 'not_enough_point'             - 잔액이 부족한 경우
 *    result : 'over_use_limit'               - 한달 사용금액을 초과한 경우
 *    result : 'server_error'                 - 기타 서버 오류
 */
    
//    print_r2($_result);
    return $_result;
    
}
}

// 메시지발송로그
if(!function_exists('message_insert')){
function message_insert($arr)
{
	global $g5;
	
	if(!$arr['msg_content'])
		return 0;

	$sql = " INSERT INTO {$g5['message_table']} SET
				com_idx        		= '".$arr['com_idx']."'
				, mb_id        		= '".$arr['mb_id']."'
				, msg_db_table      = '".$arr['db_table']."'
				, msg_db_id      	= '".$arr['db_id']."'
				, msg_type        	= '".$arr['msg_type']."'
				, msg_hp        	= '".$arr['msg_hp']."'
				, msg_email        	= '".$arr['msg_email']."'
				, msg_subject       = '".$arr['msg_subject']."'
				, msg_content       = '".$arr['msg_content']."'
				, msg_result        = '".$arr['msg_result']."'
				, msg_status        = '".$arr['msg_status']."'
				, msg_reg_dt        = '".G5_TIME_YMDHIS."'
				, msg_update_dt     = '".G5_TIME_YMDHIS."'
	";
    sql_query($sql,1);
    $msg_idx = sql_insert_id();
    
    return $msg_idx;
}
}

                                             
// 추가사항
if(!function_exists('additional_update')){
function additional_update($arr)
{
	global $g5,$config;
	
	if(!$arr['apc_idx'])
		return 0;

    $g5_table_name = $g5['additional_table'];
    $fields = sql_field_names($g5_table_name);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;
    $arr['add_start_ym'] = $arr['add_start_year'].'-'.$arr['add_start_month'];   // 년월
    $arr['add_end_ym'] = $arr['add_end_year'].'-'.$arr['add_end_month'];   // 년월

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
    // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    $sql = "SELECT * FROM {$g5_table_name} 
            WHERE add_idx = '{$arr['add_idx']}'
    ";
//    echo $sql.'<br>';
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = "UPDATE {$g5_table_name} SET 
                    {$sql_common} 
				WHERE ".$pre."_idx = '".$row[$pre."_idx"]."'
        ";
		sql_query($sql,1);
	}
	else {
		$sql = "INSERT INTO {$g5_table_name} SET 
                    {$sql_common} 
                    , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
	}
//    echo $sql.'<br>';
    return $row[$pre."_idx"];
}
}

// 경력사항
if(!function_exists('career_update')){
function career_update($arr)
{
	global $g5,$config;
	
	if(!$arr['apc_idx'])
		return 0;

    $g5_table_name = $g5['career_table'];
    $fields = sql_field_names($g5_table_name);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;
    $arr['crr_start_ym'] = $arr['crr_start_year'].'-'.$arr['crr_start_month'];   // 년월
    $arr['crr_end_ym'] = $arr['crr_end_year'].'-'.$arr['crr_end_month'];   // 년월

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
    // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    $sql = "SELECT * FROM {$g5_table_name} 
            WHERE crr_idx = '{$arr['crr_idx']}'
    ";
//    echo $sql.'<br>';
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = "UPDATE {$g5_table_name} SET 
                    {$sql_common} 
				WHERE ".$pre."_idx = '".$row[$pre."_idx"]."'
        ";
		sql_query($sql,1);
	}
	else {
		$sql = "INSERT INTO {$g5_table_name} SET 
                    {$sql_common} 
                    , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
	}
//    echo $sql.'<br>';
    return $row[$pre."_idx"];
}
}


// 학력/자격/교육/어학
if(!function_exists('school_update')){
function school_update($arr)
{
	global $g5,$config;
	
	if(!$arr['apc_idx'])
		return 0;

    $g5_table_name = $g5['school_table'];
    $fields = sql_field_names($g5_table_name);
    $pre = substr($fields[0],0,strpos($fields[0],'_'));
    
    // 변수 재설정
    $arr[$pre.'_update_dt'] = G5_TIME_YMDHIS;
    $arr['shl_yearmonth'] = $arr['shl_year'].'-'.$arr['shl_month'];   // 년월

    // 공통쿼리
    $skips[] = $pre.'_idx';	// 건너뛸 변수 배열
    $skips[] = $pre.'_reg_dt';
    for($i=0;$i<sizeof($fields);$i++) {
        if(in_array($fields[$i],$skips)) {continue;}
        $sql_commons[] = " ".$fields[$i]." = '".$arr[$fields[$i]]."' ";
    }

    // after sql_common value setting
    // $sql_commons[] = " com_idx = '".$arr['ss_com_idx']."' ";

    // 공통쿼리 생성
    $sql_common = (is_array($sql_commons)) ? implode(",",$sql_commons) : '';
    
    $sql = "SELECT * FROM {$g5_table_name} 
            WHERE shl_idx = '{$arr['shl_idx']}'
    ";
//    echo $sql.'<br>';
    $row = sql_fetch($sql,1);
	if($row[$pre."_idx"]) {
		$sql = "UPDATE {$g5_table_name} SET 
                    {$sql_common} 
				WHERE ".$pre."_idx = '".$row[$pre."_idx"]."'
        ";
		sql_query($sql,1);
	}
	else {
		$sql = "INSERT INTO {$g5_table_name} SET 
                    {$sql_common} 
                    , ".$pre."_reg_dt = '".G5_TIME_YMDHIS."'
        ";
		sql_query($sql,1);
        $row[$pre."_idx"] = sql_insert_id();
	}
//    echo $sql.'<br>';
    return $row[$pre."_idx"];
}
}


// 휴대폰번호 정상적인지 체크 return=1 일 때 정상
if(!function_exists('check_hp')){
function check_hp($hp)
{
    $hp = hyphen_hp_number($hp);    // 하이픈(-)을 넣어준다.
//    echo $hp.'<br>';
    if ( preg_match( '/^(010|011|016|017|018|019)-[^0][0-9]{3,4}-[0-9]{4}/',$hp) ) {
        return 1;
    }
    else {
//        echo "잘못된 휴대폰 번호입니다. 숫자, - 를 포함한 숫자만 입력하세요.";
        return 0;
    }
}
}

// 휴대폰번호의 하이픈(-)을 제거하고 숫자만
if(!function_exists('hyphen_hp_remove')){
function hyphen_hp_remove($hp)
{
    $hp = preg_replace("/-/", "", trim($hp));
    return $hp;
}
}

//KOSMO에 log데이터 전송 함수
if(!function_exists('send_kosmo_log')){
function send_kosmo_log(){
	global $g5, $board, $is_member, $member, $w, $stx, $mb;
	if(!$is_member)
		return;
	//print_r2($_SESSION);exit;
	if(!$_SEESSION['ss_com_kosmolog_key'])
		return;

	if(!$member['mb_id'])
		return;
	
	$user_status = '';
	if(preg_match('/update$/i',$g5['file_name'])){
		if(!$w) $user_status = '등록';
		else if($w == 'u') $user_status = '수정';
		else if($w == 'd') $user_status = '삭제';
	}
	else if(preg_match('/list$/i',$g5['file_name'])){
		if($stx) $user_status = '검색';
	}
	else{
		if($g5['file_name'] == 'login_check'){
			//print_r2($member);exit;
			$user_status = '접속';
		}
		else if($g5['file_name'] == 'logout'){
			$user_status = '종료';
		}
	}
	
	if(!$user_status)
		return;
	//print_r3($user_status);return;
	$url = 'https://log.smart-factory.kr/apisvc/sendLogData.json';
	/*
	$crtcKey = $_SEESSION['ss_com_kosmolog_key'];
	$logDt = G5_TIME_YMDHIS;
	$useSe = $user_status;
	$sysUser = $member['mb_id'];
	$conectIp = $member['mb_login_ip'];
	$dataUsgqty = '';
	*/
	$darr = array(
		'crtfcKey' => $_SEESSION['ss_com_kosmolog_key'],
		'logDt' => G5_TIME_YMDHIS,
		'useSe' => $user_status,
		'sysUser' => $member['mb_id'],
		'conectIp' => $member['mb_login_ip'],
		'dataUsgqty' => ''
	);

	$opt = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($darr)
		)
	);
	$context = stream_context_create($opt); //데이터 가공
	$result = file_get_contents($url, false, $context); //전송 ~ 결과값 반환
	$data = json_decode($result, true);
}
}

// update bom_price_history
// bom_idx, bom_start_date, bom_price
if(!function_exists('bom_price_history')){
function bom_price_history($arr) {
    global $g5;

    // Update price table info. Update for same price and date, Insert for not existing.
    $sql = "SELECT * FROM {$g5['bom_price_table']}
        WHERE bom_idx = '".$arr['bom_idx']."'
            AND bop_start_date = '".$arr['bom_start_date']."'
    ";
    $bop = sql_fetch($sql,1);
    if($bop['bop_idx']) {
        $sql = "UPDATE {$g5['bom_price_table']} SET
                    bop_price = '".$arr['bom_price']."',
                    bop_start_date = '".$arr['bom_start_date']."',
                    bop_update_dt = '".G5_TIME_YMDHIS."'
                WHERE bop_idx = '".$bop['bop_idx']."'
        ";
        sql_query($sql,1);
    }
    else {
        $sql = " INSERT INTO {$g5['bom_price_table']} SET
                    bom_idx = '".$arr['bom_idx']."',
                    bop_price = '".$arr['bom_price']."',
                    bop_start_date = '".$arr['bom_start_date']."',
                    bop_reg_dt = '".G5_TIME_YMDHIS."',
                    bop_update_dt = '".G5_TIME_YMDHIS."'
        ";
        sql_query($sql,1);
        $bop['bop_idx'] = sql_insert_id();
    }

    return $bop['bop_idx'];
}
}

// set the today's proper price according the date registered in the bom_price table.
if(!function_exists('set_bom_price')){
function set_bom_price($bom_idx) {
    global $g5;

    // get the latest price info and update the mms_item table info.
    $sql = "UPDATE {$g5['bom_table']} AS bom SET
                    bom_price = (
                        SELECT bop_price
                        FROM {$g5['bom_price_table']}
                        WHERE bom_idx = bom.bom_idx
                            AND bop_start_date <= '".G5_TIME_YMD."'
                        ORDER BY bop_start_date DESC
                        LIMIT 1
                    )
                WHERE bom_idx = '".$bom_idx."' AND bom_status NOT IN ('delete','trash')
    ";
    sql_query($sql,1);

}
}

// get the today's proper price according the date registered in the bom_price table.
if(!function_exists('get_bom_price')){
function get_bom_price($bom_idx) {
    global $g5;

    $sql = "SELECT bop_price
            FROM {$g5['bom_price_table']}
            WHERE bom_idx = '".$bom_idx."'
                AND bop_start_date <= '".G5_TIME_YMD."'
            ORDER BY bop_start_date DESC
            LIMIT 1
    ";
    $row = sql_fetch($sql,1);
    return (int)$row['bop_price'];

}
}


// 사원 정보를 얻는다. (외부 인트라인 경우 내부인트라에서 )
if(!function_exists('get_saler_idx')){
function get_saler_idx($mb_name, $mb_intra='', $mb_intra_id='') {
    global $g5;
    
    if(!$mb_name)
        return false;

    $sql = " SELECT mb_2, mb_9 FROM {$g5['member_table']} WHERE mb_name = TRIM('$mb_name') ";
    $rs = sql_query($sql,1);
    // 한명 이상인 경우는 mb_9 keys 값을 분리해서 해당 회원을 찾아야 함
    if(sql_num_rows($rs) > 1) {
        for($i=0;$row=sql_fetch_array($rs);$i++) {
            // mb9에 기존 인트라 정보 저장됨 (:mb_intra=31:,:mb_intra31_id=jamesjoa:,)
            $row['keys'] = get_keys($row['mb_9']);
            if($row['keys']['mb_intra']==$mb_intra && $row['keys']['mb_intra'.$mb_intra.'_id']==$mb_intra_id)
            $trm_idx = $row['mb_2'];
        }
    }
    else {
        $mb = sql_fetch($sql);
        $trm_idx = $mb['mb_2'];
    }

    return $trm_idx;
}
}


// 게시판 변수설정들을 불려온다. wr_7 serialized 풀어서 배열로 가지고 옴
if(!function_exists('get_board')){
function get_board($bo_table) {
    global $g5;
    
    $sql = " SELECT * FROM ".$g5['board_table']." WHERE bo_table = '$bo_table' ";
    $board = sql_fetch($sql,1);
    $unser = unserialize($board['bo_7']);
    if( is_array($unser) ) {
        foreach ($unser as $k1=>$v1) {
            $board[$k1] = stripslashes64($v1);
        }    
    }
    return $board;
}
}

// number to hangle display
if(!function_exists('num_to_han')){
function num_to_han($mny){
    $stlen = strlen($mny)-1;
    //숫자를 4단위로 한글 단위를 붙인다.
    $names = array("원","만원","억","조","경"); // 단위의 한글발음 (조 다음으로 계속 추가 가능)
    $nums = str_split($mny); // 숫자를 배열로 분리
    $nums = array_reverse($nums);
    $units = array();
    // 역으로 자리숫자마다 숫자 단위를 붙여서 배열로 만듦
    for($i=0,$m=count($nums);$i<$m;$i++){
        $units[] = $names[floor($i/4)];
    }
    // print_r2($units);
    $cu = '';
    $str = '';
    $flag = floor($stlen/4)*4;
    // echo $flag.'<br>';
    // 4자리 단위로 flag 기준 범위만 돌면서 값을 생성 
    for($i=$flag,$m=count($nums); $i<$m; $i++){
        $arr = $nums[$i];
        // echo $t.'<br>';
        // 단위가 바뀔 때만 단위값을 붙여줌
        if($cu != $units[$i]){
            $unit = $units[$i];
        }
        // 숫자를 역으로 돌면서 앞에다 숫자를 붙여줌
        $str = $arr.$str;
    }
    // 만단위 이상인 경우는 끝에 한자리만 더 추가
    if($flag>3) {
            $str .= '.'.$nums[$flag-1];
    }
    $str = $str ?: 0;
    // return($str); 
    return(array($str,$unit)); 
}
}


// 대시보드 기본 삭제함수
if(!function_exists('dash_delete')){
function dash_delete(){
    global $g5;
    //상태값이 trash로 된 이후 일주일이 지난 데이터는 회원을 불문하고 전부 삭제한다. 
    $mta_del_sql = " DELETE FROM {$g5['meta_table']}
    WHERE mta_db_table = 'member'
        AND mta_key = 'dashboard_menu'
        AND mta_status = 'trash'
        AND mta_update_dt < DATE_SUB(NOW(), interval 7 day) ";
    sql_query($mta_del_sql);
    //해당 g5_1_dash_grid 테이블의 레코드도 상태값이 trash로 된 이후 일주일이 지난 데이터는 삭제
    $dsg_del_sql = " DELETE FROM {$g5['dash_grid_table']}
    WHERE dsg_status = 'trash'
        AND dsg_update_dt < DATE_SUB(NOW(), interval 7 day) ";
    sql_query($dsg_del_sql);
    //해당 g5_1_member_dash 테이블의 레코드도 상태값이 trash로 된 이후 일주일이 지난 데이터는 삭제
    $mbd_del_sql = " DELETE FROM {$g5['member_dash_table']}
    WHERE mbd_status = 'trash'
        AND mbd_update_dt < DATE_SUB(NOW(), interval 7 day) ";
    sql_query($mbd_del_sql);
}
}

if(!function_exists('dash_test')){
function dash_test(){
    return 'dash_test';
}
}

// 생산반영일(통계날짜) 반환하는 함수
if(!function_exists('statics_date')){
function statics_date($dt){
    global $g5;
    //기본적으로 당일날짜를 반환한다.
    $date = substr($dt,0,10);
    $time = substr($dt,-8);
    $statics_std = $g5['setting']['mng_statics_std'];
    if($statics_std == 'shift'){
        $sql = " SELECT shf_end_time FROM {$g5['shift_table']} 
                    WHERE com_idx = '{$g5['setting']['set_com_idx']}'
                        AND shf_end_prevday = '1'
                        AND shf_period_type = '1'
                        AND shf_status = 'ok'
                ORDER BY shf_idx DESC LIMIT 1
        ";
        $res = sql_fetch($sql);
        $start_time = '00:00:00';
        $end_time = $res['shf_end_time'];

        if($end_time){
            $start_stamp = strtotime($start_time);
            $end_stamp = strtotime($end_time);
            $time_stamp = strtotime($time);
            if($time_stamp >= $start_stamp && $time_stamp <= $end_stamp){
                $date = get_dayAddDate($date,-1);
            }
        }
    }

    return $date;
}
}

// 생간시간구간(shf_idx)을 반환하는 함수
if(!function_exists('shift_idx')){
function shift_idx($dt){
    global $g5;
    //기본적으로 당일날짜를 반환한다.
    $date = substr($dt,0,10);
    $time = substr($dt,-8);
    $t_stamp = strtotime($time);
    $shf_idx = 0;
    
    $sql = " SELECT shf_idx, shf_start_time, shf_end_time FROM {$g5['shift_table']} 
                WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                    AND shf_period_type = '1'
                    AND shf_status = 'ok'
            ORDER BY shf_idx
    ";
    $res = sql_query($sql);

    for($i=0;$row=sql_fetch_array($res);$i++){
        $s_time = substr($row['shf_start_time'],-8);
        $e_time = substr($row['shf_end_time'],-8);
        $s_stamp = strtotime($s_time);
        $l_stamp = strtotime('23:59:59');//하루의 마지막 시간
        $f_stamp = strtotime('00:00:00');//하루의 첫 시간
        $e_stamp = strtotime($e_time);

        //일반적인 시작시간이 종료시간보다 작은경우
        if($e_stamp > $s_stamp){
            if($t_stamp >= $s_stamp && $t_stamp <= $e_stamp){
                $shf_idx = $row['shf_idx'];
            }
        }
        // 종료시간이 시작시간 보다 작은 경우는 하루를 넘겼다는 뜻이므로 
        else{
            if(($t_stamp >= $s_stamp && $t_stamp <= $l_stamp) || $t_stamp >= $f_stamp && $t_stamp <= $e_stamp){
                $shf_idx = $row['shf_idx'];
            }
        }
    }
    return $shf_idx;
}
}

// 하루 작업 시간을 반환하는 함수 (2023-05-29 07:00:00~2023-05-30 06:00:00~)
if(!function_exists('shift_period')){
function shift_period($date){
    global $g5;
    if(!$date) {
        return false;
    }
    
    $start_date = $date;
    $start_time = '23:59:59';
    $end_date = $start_date;
    $end_time = '00:00:00';
    $sql = " SELECT * FROM {$g5['shift_table']} 
                WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                    AND shf_status = 'ok'
            ORDER BY shf_end_prevday
    ";
    // echo $sql.BR;
    $res = sql_query($sql,1);
    for($i=0;$row=sql_fetch_array($res);$i++){
        if($row['shf_start_time'] < $start_time){
            $start_time = $row['shf_start_time'];
        }
        if($row['shf_end_time'] > $end_time){
            $end_time = $row['shf_end_time'];
        }
        // 작일(다음날)인 경우
        if($row['shf_end_prevday']){
            $end_date = date("Y-m-d", strtotime($end_date)+86400);
            $end_time = $row['shf_end_time'];
        }
        // echo $start_time.' / '.$end_time.BR;
    }
    $start_time = ($start_time=='23:59:59') ? '00:00:00':$start_time;
    $end_time = ($end_time=='00:00:00') ? '23:59:59':$end_time;
    return array('start_dt'=>$start_date.' '.$start_time,'end_dt'=>$end_date.' '.$end_time);
}
}
?>