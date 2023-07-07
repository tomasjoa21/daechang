<?php
$sql = " SELECT mms_idx
            , mms_name
            , mms_call_yn
            , mms_pos_x
            , mms_pos_y 
        FROM {$g5['mms_table']}
WHERE com_idx = '{$g5['setting']['set_com_idx']}'
    AND mms_status = 'ok'
    AND mms_pos_yn = '1'
ORDER BY mms_idx
";
$res = sql_query($sql,1);
?>

<script src="<?=G5_MONITOR_JS_URL?>/draggabilly.pkgd.min.js"></script>
<div class="container">
    <?php for($i=0;$row=sql_fetch_array($res);$i++){ 
        $pri_sql = " SELECT pri_ing FROM {$g5['production_item_table']} pri
                LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
            WHERE pri.com_idx = '{$g5['setting']['set_com_idx']}'
                AND prd_status = 'confirm'
                AND pri_ing = '1'
                AND mms_idx = '{$row['mms_idx']}'
                AND prd_start_date = '".G5_TIME_YMD."'
            ORDER BY pri_idx DESC
            LIMIT 1
        "; 
        // echo $pri_sql."<br>";
        $pri = sql_fetch($pri_sql);

        $row['mms_pos_x'] = $row['mms_pos_x'] * 2;
        $row['mms_pos_y'] = $row['mms_pos_y'] * 2;

        $bg_class = ($pri['pri_ing']) ? ' ing' : '';
        $bg_class = ($row['mms_call_yn']) ? ' focus' : $bg_class;

    ?>
    <div class="draggable<?=$bg_class?>" mms_idx="<?=$row['mms_idx']?>" mms_pos_x="<?=$row['mms_pos_x']?>" mms_pos_y="<?=$row['mms_pos_y']?>" style="left:<?=$row['mms_pos_x']?>px;top:<?=$row['mms_pos_y']?>px;"><?=$row['mms_name']?></div>
    <?php } ?>
</div>
<script>
$(function(){
    var $draggbles = $('.draggable').draggabilly({
        containment: true,
        grid: [40, 40]
    });

    setTimeout(function(){
        window.location.reload();
    },3000);
});
</script>