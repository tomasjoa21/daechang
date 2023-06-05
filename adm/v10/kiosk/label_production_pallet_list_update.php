<?php
include_once('./_common.php');

// print_r2($_POST);
if(!count($chk)){
    alert('선택삭제할 항목을 1개이상 선택하세요.');
}
//chk요소의 값은 plt_idx이다.
for($i=0;$i<count($chk);$i++){
    $plt_idx = $chk[$i];

    $itm_sql = " UPDATE {$g5['item_table']} SET
                plt_idx = '0'
            WHERE plt_idx = '{$plt_idx}'
    ";
    sql_query($itm_sql,1);

    $plt_sql = " DELETE FROM {$g5['pallet_table']} WHERE plt_idx = '{$plt_idx}' ";
    sql_query($plt_sql,1);
}

goto_url('./label_production_pallet_list.php?bom_idx='.$bom_idx,false);