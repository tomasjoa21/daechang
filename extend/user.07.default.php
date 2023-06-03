<?php

// 로그인을 할 때마다 로그 파일 삭제해야 용량을 확보할 수 있음 
if(basename($_SERVER["SCRIPT_FILENAME"]) == 'login_check.php') {
	// 지난시간을 초로 계산해서 적어주시면 됩니다.
	$del_time_interval = 3600 * 2;	// Default = 2 시간

	// 이력서 파일 삭제
	if ($dir=@opendir(G5_DATA_PATH.'/resume')) {
	    while($file=readdir($dir)) {
            if($file == '.' || $file == '..')
                continue;

            $each_file = G5_DATA_PATH.'/resume/'.$file;
//            echo $each_file.'<br>';
	        if (!$atime=@fileatime($each_file))
	            continue;
	        if (time() > $atime + $del_time_interval)
	            unlink($each_file);
	    }
    }
}


$cache_file = G5_DATA_PATH.'/cache/mms-code.php';
if( file_exists($cache_file) ) {
    include($cache_file);
}
$cache_file = G5_DATA_PATH.'/cache/mms-setting.php';
if( file_exists($cache_file) ) {
    include($cache_file);
}
$cache_file = G5_DATA_PATH.'/cache/socket-setting.php';
if( file_exists($cache_file) ) {
    include($cache_file);
}
$cache_file = G5_DATA_PATH.'/cache/socket-alarm.php';
if( file_exists($cache_file) ) {
    include($cache_file);
}



// 뿌리오 발송결과
$set_values = explode("\n", $g5['setting']['set_ppurio_call_status']);
foreach ($set_values as $set_value) {
	list($key, $value) = explode('=', trim($set_value));
    if($key&&$value) {
        $g5['set_ppurio_call_status'][$key] = $value.' ('.$key.')';
        $g5['set_ppurio_call_status_value'][$key] = $value;
        $g5['set_ppurio_call_status_options'] .= '<option value="'.trim($key).'">'.trim($value).' ('.$key.')</option>';
        $g5['set_ppurio_call_status_value_options'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
    }
}
unset($set_values);unset($set_value);

// 디비테이블명
$set_values = explode("\n", $g5['setting']['set_db_table_name']);
foreach ($set_values as $set_value) {
	list($key, $value) = explode('=', trim($set_value));
    if($key&&$value) {
        $g5['set_db_table_name'][$key] = $value.' ('.$key.')';
        $g5['set_db_table_name_value'][$key] = $value;
    }
}
unset($set_values);unset($set_value);

// 디비테이블명 스킵해야 할 디비명
$set_values = explode("\n", $g5['setting']['set_db_table_skip']);
foreach ($set_values as $set_value) {
    if(trim($set_value)) {
        $g5['set_db_table_skip'][] = trim($set_value);
    }
}
unset($set_values);unset($set_value);


// 데이타그룹, 데이터그룹별 그래프 초기값도 추출
$set_values = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_data_group']));
foreach ($set_values as $set_value) {
	list($key, $value) = explode('=', trim($set_value));
	$g5['set_data_group'][$key] = $value.' ('.$key.')';
	$g5['set_data_group_value'][$key] = $value;
	$g5['set_data_group_radios'] .= '<label for="set_data_group_'.$key.'" class="set_data_group"><input type="radio" id="set_data_group_'.$key.'" name="set_data_group" value="'.$key.'">'.$value.'</label>';
	$g5['set_data_group_options'] .= '<option value="'.trim($key).'">'.trim($value).' ('.trim($key).')</option>';
	$g5['set_data_group_value_options'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
    
    // 데이타 그룹별 그래프 디폴트값 추출, $g5['set_graph_run']['default1'], $g5['set_graph_err']['default4'] 등과 같은 배열값으로 디폴트값 추출됨
    $set_values1 = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_graph_'.$key]));
    for($i=0;$i<sizeof($set_values1);$i++) {
        $g5['set_graph_'.$key]['default'.$i] = $set_values1[$i];
    }
    // print_r3($g5['set_graph_'.$key]);
    unset($set_values1);unset($set_value1);
}
unset($set_values);unset($set_value);
// print_r3($g5['set_data_group_value']);

// 불량입력 엑셀항목 설정, 쉼표가 있고 띄어쓰기도 있어서 따로 추출합니다.
$set_values = explode('|', $g5['setting']['set_return_item']);
foreach ($set_values as $set_value) {
	list($key, $value) = explode('=', trim($set_value));
	$g5['set_return_item2'][$key] = $value.' ('.$key.')';
	$g5['set_return_item_value2'][$key] = $value;
	$g5['set_return_item_options2'] .= '<option value="'.trim($key).'">'.trim($value).' ('.trim($key).')</option>';
	$g5['set_return_item_value_options2'] .= '<option value="'.trim($key).'">'.trim($value).'</option>';
}
unset($set_values);unset($set_value);


// 단위별(분,시,일,주,월,년) 초변환수
// 첫번째 변수 = 단위별 초단위 전환값
// 두번째 변수 = 종료일(or시작일)계산시 선택단위, 0이면 기존 선택된 단위값, 아니면 해당숫자 
$seconds = array(
    "daily"=>array(86400,1)
    ,"weekly"=>array(604800,1)
    ,"monthly"=>array(2592000,1)
    ,"yearly"=>array(31536000,1)
    ,"minute"=>array(60,0)
    ,"second"=>array(1,0)
);
$seconds_text = array(
    "86400"=>'일간'
    ,"604800"=>'주간'
    ,"2592000"=>'월간'
    ,"31536000"=>'년간'
    ,"60"=>'분단위'
    ,"1"=>'초단위'
);

// BOM구성 표시
$g5['set_bom_type_displays'] = explode(',', preg_replace("/\s+/", "", $g5['setting']['set_bom_type_display']));

//설비배열
$mms_sql = " SELECT mms_idx,mms_name FROM {$g5['mms_table']}
                WHERE mms_status = 'ok'
                    AND com_idx = '{$g5['setting']['set_com_idx']}'
";
$mms_res = sql_query($mms_sql,1);
$g5['mms_arr'] = array();
$g5['mms_options'] = '';
$g5['mms_options_idx'] = '';
for($j=0;$mrow=sql_fetch_array($mms_res);$j++){
    if(!array_key_exists($mrow['mms_idx'],$g5['mms_arr'])){
        $g5['mms_arr'][$mrow['mms_idx']] = $mrow['mms_name'];
        $g5['mms_options'] .= '<option value="'.$mrow['mms_idx'].'">'.$mrow['mms_name'].'</option>'.PHP_EOL;
        $g5['mms_options_idx'] .= '<option value="'.$mrow['mms_idx'].'">'.$mrow['mms_name'].'('.$mrow['mms_idx'].')</option>'.PHP_EOL;
    }
}

$g5['mmw_arr'] = array();
//설비별 담당자배열
$mmw_sql = " SELECT mmw.mms_idx
                , mmw.mb_id
                , mb.mb_name
                , mmw_type
                , mmw_sort
            FROM {$g5['mms_worker_table']} mmw
                LEFT JOIN {$g5['member_table']} mb ON mmw.mb_id = mb.mb_id
            WHERE mmw_status IN ('ok')
                -- AND mms_name REGEXP '([^포장] | [^검사])$'
                AND mmw.mb_id NOT IN ('없음', '')
                AND mb.mb_name != ''
            ORDER BY mmw.mms_idx, mmw_sort
            ";
$mmw_res = sql_query($mmw_sql,1);

$mmw_types = array(
    'day'=>'주'
    ,'night'=>'야'
    ,'sub'=>'부'
    ,''=>'부'
);

for($l=0;$wrow=sql_fetch_array($mmw_res);$l++){
    if(!array_key_exists($wrow['mms_idx'],$g5['mmw_arr'])){
        $g5['mmw_arr'][$wrow['mms_idx']] = array(
            $wrow['mb_id'] => $wrow['mb_name'].'('.$mmw_types[$wrow['mmw_type']].')'
        );
    }
    else{
        $g5['mmw_arr'][$wrow['mms_idx']][$wrow['mb_id']] = $wrow['mb_name'].'('.$mmw_types[$wrow['mmw_type']].')';
    }   
}
unset($mms_sql);
unset($mms_res);
unset($mmw_sql);
unset($mmw_res);
// print_r2($g5['mmw_arr']);

//카테고리 관련 배열
$cat_sql = " SELECT bct_idx, bct_name FROM {$g5['bom_category_table']} WHERE com_idx = '{$_SESSION['ss_com_idx']}' ORDER BY bct_order,bct_idx ";
$cat_res = sql_query($cat_sql,1);
$g5['cats_key_val'] = array();
$g5['cats_val_key'] = array();
for($i=0;$row=sql_fetch_array($cat_res);$i++){
    $g5['cats_key_val'][$row['bct_idx']] = $row['bct_name'];
    $g5['cats_val_key'][$row['bct_name']] = $row['bct_idx'];
}
unset($cat_sql);
unset($cat_res);
unset($i);

//자재공급업체 배열
$prv_sql = " SELECT DISTINCT(cst_idx_provider) AS cst_idx 
                , cst.cst_name
            FROM {$g5['bom_table']} bom
                LEFT JOIN {$g5['customer_table']} cst ON bom.cst_idx_provider = cst.cst_idx
            WHERE bom_type IN ('material','goods')
                AND cst_idx_provider != 0 
            ORDER BY cst_idx_provider
";
$prv_res = sql_query($prv_sql,1);
$g5['provider_key_val'] = array();
$g5['provider_val_key'] = array();
for($i=0;$row=sql_fetch_array($prv_res);$i++){
    $g5['provider_key_val'][$row['cst_idx']] = $row['cst_name'];
    $g5['provider_val_key'][$row['cst_name']] = $row['cst_idx'];
}
unset($prv_sql);
unset($prv_res);
unset($i);

//완제품고객업체 배열
$cst_sql = " SELECT DISTINCT(cst_idx_customer) AS cst_idx 
                , cst.cst_name
            FROM g5_1_bom bom
                LEFT JOIN g5_1_customer cst ON bom.cst_idx_customer = cst.cst_idx
            WHERE bom_type IN ('product','goods')
                AND cst_idx_customer != 0 
            ORDER BY cst_idx_customer
";
$cst_res = sql_query($cst_sql,1);
$g5['customer_key_val'] = array();
$g5['customer_val_key'] = array();
for($i=0;$row=sql_fetch_array($cst_res);$i++){
    $g5['customer_key_val'][$row['cst_idx']] = $row['cst_name'];
    $g5['customer_val_key'][$row['cst_name']] = $row['cst_idx'];
}
unset($cst_sql);
unset($cst_res);
unset($i);