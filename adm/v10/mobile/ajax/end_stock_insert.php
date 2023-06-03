<?php
include_once('./_common.php');

/*
$sql = " SELECT SUM(pic_value) AS total FROM {$g5['production_item_count_table']}
        WHERE pri_idx = '{$pri_idx}'
            AND mb_id = '{$mb_id}'
";

$res = sql_fetch($sql);
$total = ($res['total'] == NULL) ? 0 : $res['total'];
echo $total;
*/
/*
"prd_idx": f.prd_idx.value,
"pri_idx":f.pri_idx.value,
"mb_id": '<?=$member['mb_id']?>',
"bom_idx": f.bom_idx.value,
"bom_type": f.bom_type.value,
"pri_cnt": f.pri_cnt.value
*/

//우선 작업 시작시간을 확인한다.
$start_res = sql_fetch(" SELECT pri_update_dt FROM {$g5['production_item_table']} WHERE pri_idx = '{$pri_idx}' ");
$start_date = statics_date($start_res['pri_update_dt']);
$start_dt = $start_res['pri_update_dt'];

$end_date = statics_date(G5_TIME_YMDHIS);
$end_dt = G5_TIME_YMDHIS;

//g5_1_production_item_count에 첫레코드는 $start_dt 시간으로 등록하고 나머지는 현재시간으로 등록한다.
$pic_sql = " INSERT INTO {$g5['production_item_count_table']} 
            (pri_idx, mb_id, pic_ing, pic_value, pic_date, pic_reg_dt, pic_update_dt) VALUES ";
for($i=0;$i<$pri_cnt;$i++){
    if($i == 0){
        $pic_sql .= " 
            ('{$pri_idx}','{$mb_id}','1','1','{$start_date}','{$start_dt}','{$start_dt}') 
        ";
    }
    else{
        $pic_sql .= " 
            ,('{$pri_idx}','{$mb_id}','1','1','{$end_date}','{$end_dt}','{$end_dt}') 
        ";
    }
}
sql_query($pic_sql,1);

//생성된 재고데이터에 mb_id를 지정한다.


echo 'ok';