<?php
include_once('./_common.php');

// echo $pri_idx.','.$mb_id;
$sql = " SELECT SUM(pic_value) AS total FROM {$g5['production_item_count_table']}
        WHERE pri_idx = '{$pri_idx}'
            AND mb_id = '{$mb_id}'
";

$res = sql_fetch($sql);
$total = ($res['total'] == NULL) ? 0 : $res['total'];
echo $total;