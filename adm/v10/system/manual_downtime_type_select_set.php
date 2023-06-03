<?php
include_once('./_common.php');

//불량유형 중복체크를 목적으로 접속했을때 (아작스처리)
$opts = '';
if($mms_idx){
    $sql = " SELECT mst_idx,mst_name FROM {$g5['mms_status_table']} WHERE mms_idx = '{$mms_idx}' AND mst_type = 'offwork' AND mst_status NOT IN('delete','del','trash','cancel')  ";
    $res = sql_query($sql);

    if($res->num_rows){
        for($i=0;$row=sql_fetch_array($res);$i++){
            $opts .= '<option value="'.$row['mst_idx'].'">'.$row['mst_name'].'</option>'.PHP_EOL;
        }
    }
    
}

echo $opts;
