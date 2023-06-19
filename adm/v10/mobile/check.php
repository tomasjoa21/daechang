<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//http://daechang2.epcs.co.kr/adm/v10/mobile/check.php?plt_idx=116

$plt = sql_fetch(" SELECT * FROM {$g5['pallet_table']} WHERE plt_idx = '{$plt_idx}' ");

$itm_sql = " SELECT itm.bom_idx
                , itm_name
                , itm_part_no
                , bct_idx
                , bom_delivery_check_yn
                , SUM(itm_value) AS itm_sum
            FROM {$g5['item_table']} itm
            LEFT JOIN {$g5['bom_table']} bom ON itm.bom_idx = bom.bom_idx
            WHERE plt_idx = '{$plt_idx}'
            GROUP BY itm.bom_idx
";
$itm_res = sql_query($itm_sql,1);

include_once('./_head.php');
?>
<div id="plt_box">
    <h4 id="plt_ttl"><?=(($plt_idx)?'['.$plt_idx.']파레트의 ':'')?>출하검사</h4>
    <?php if($plt_idx){ ?>
    <div class="plt_cont">

    </div><!--//.plt_cont-->
    <?php } else { ?>
    <div class="plt_empty">파레트 데이터가 없습니다.</div>
    <?php } ?>
</div><!--//.plt_box-->
<script>

</script>
<?php
include_once('./_tail.php');