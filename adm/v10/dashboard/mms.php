<?php
// http://daechang.epcs.co.kr/adm/v10/dashboard/mms.php?w=1&h=1
include_once('./_common.php');

$g5['title'] = 'UPH';
include_once('./_head.sub.php');

// st_date, en_date
$st_date = $st_date ?: date("Y-m-d",G5_SERVER_TIME);

$mms = get_table('mms','mms_idx',$mms_idx);

$sql = "SELECT *
        FROM {$g5['production_item_count_table']}
        WHERE pri_idx IN (  SELECT pri_idx
                            FROM {$g5['production_item_table']} WHERE mms_idx = '".$mms['mms_idx']."'
                            AND prd_idx IN ( SELECT prd_idx FROM {$g5['production_table']} WHERE prd_start_date = '".$st_date."' )
        )
        ORDER BY pic_idx DESC
";
// echo $sql.BR;
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++) {
    // first item for current production info.
    if($i==0) {
        $mb = get_member($row['mb_id']);
        $mb_name = $mb['mb_name'];
        $pri = get_table('production_item','pri_idx',$row['pri_idx']);
        $bom = get_table('bom','bom_idx',$pri['bom_idx']);
        $bom_part_no = $bom['bom_part_no'];
    }
    $total += $row['pic_value'];
}



if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/style.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/style.css">', 2);
}
if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
</style>


<div class="box_header">
    <div class="top_left">
        <p class="title_main"><?=$mms['mms_name']?> 생산</p>
    </div>
    <div class="top_right">
        <p>
            <a href="../item_worker_today_list.php" class="btn_detail" style="margin-right:10px;"><i class="fa fa-list-alt"></i></a>
            <a href="javascript:" class="btn_reload"><i class="fa fa-repeat"></i></a>
        </p>
    </div>
</div>
<div class="box_body">
    <p><?=number_format($total)?></p>
</div>
<div class="box_footer">
    <p>현재: <?=$mb_name?> (<?=$bom_part_no?>)</p>
</div>

<script>
$(document).on('click','.btn_detail',function(e){
    e.preventDefault();
    parent.location.href = $(this).attr('href');
});
$(document).on('click','.btn_reload',function(){
    self.location.reload();
});
// 10분에 한번 재로딩
setTimeout(function(e){
    self.location.reload();
},1000*600);
</script>

<?php
include_once ('./_tail.sub.php');
?>
