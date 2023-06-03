<?php
include_once('./_common.php');

$yesterday = get_dayAddDate(G5_TIME_YMD,-1);
$plt_arr = array();
$sql = " SELECT itm.plt_idx
            , itm.bom_idx
            , bom.bom_part_no
            , bom.bom_name
            , bom.bom_ship_count
            , SUM(itm_value) AS plt_count
            , ( SELECT GROUP_CONCAT(mb_id) FROM {$g5['customer_member_table']} WHERE cst_idx = bom.cst_idx_customer ) AS mb_ids
        FROM {$g5['item_table']} itm
            LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
            LEFT JOIN {$g5['pallet_table']} plt ON itm.plt_idx = plt.plt_idx
            LEFT JOIN {$g5['customer_table']} cst ON bom.cst_idx_customer = cst.cst_idx
        WHERE itm.plt_idx != 0
            AND plt_status IN ('ok','delivery')
            AND plt_reg_dt >= '{$yesterday} 00:00:00'
        GROUP BY itm.plt_idx, itm.bom_idx
        ORDER BY itm.plt_idx DESC, itm.bom_idx 
";
// echo $sql;
$result = sql_query($sql,1);

for($i=0;$row=sql_fetch_array($result);$i++){
    if(array_key_exists($row['plt_idx'], $plt_arr)){
        
    }
}

$g5['title'] = '오늘의 배차';
$g5['box_title'] = $member['mb_name'].'님의 '.G5_TIME_YMD.' 배차정보';
include_once('./_head.php');

?>
<div id="plt_list">
    <h4 id="plt_ttl">빠레트정보</h4>
    <ul>
    </ul>
</div>
<?php
include_once('./_tail.php');