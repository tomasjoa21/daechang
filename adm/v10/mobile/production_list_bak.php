<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//http://daechang2.epcs.co.kr/adm/v10/mobile/production_list.php?mms_idx=157
$where_mms_idx = "";
$mms_name = "";
if($mms_idx){
    $mms_name = $g5['mms_arr'][$mms_idx];
    $where_mms_idx = " AND pri.mms_idx = '{$mms_idx}' ";
}
$plt_arr = array();
$sql = " SELECT prd.prd_idx
            , prd.prd_start_date
            , pri.pri_idx
            , pri.bom_idx
            , bom.bct_idx
            , bom.bom_part_no
            , bom.bom_name
            , bom.cst_idx_customer
            , bom.bom_type
            , cst.cst_name
            , pri_value
            , pri.mms_idx
            , mms.mms_call_yn
            , pri.mb_id
            , pri_memo
            , pri_ing
            , pri_status
            , pri_reg_dt
            , pri_update_dt
        FROM {$g5['production_item_table']} pri
            LEFT JOIN {$g5['mms_table']} mms ON pri.mms_idx = mms.mms_idx
            LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
            LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
            LEFT JOIN {$g5['customer_table']} cst ON bom.cst_idx_customer = cst.cst_idx
            LEFT JOIN {$g5['member_table']} mb ON pri.mb_id = mb.mb_id
        WHERE pri.mb_id = '{$member['mb_id']}'
            AND pri.mms_idx = '{$mms_idx}'
            AND prd.prd_start_date = '".statics_date(G5_TIME_YMDHIS)."'
            AND prd.prd_status = 'confirm'
            AND bom.bom_type IN ('product','half')
";
// echo $sql;
$result = sql_query($sql,1);

$g5['title'] = '생산작업설정(Production Setting)';
$g5['box_title'] = $member['mb_name'].'님의 '.statics_date(G5_TIME_YMDHIS).' 생산설정';
$g5['box_title'] .= '<br>Prodution for '.$member['mb_name'].' at '.statics_date(G5_TIME_YMDHIS);
include_once('./_head.php');
?>
<div id="plt_list">
    <h4 id="plt_ttl"><?=(($mms_name)?'['.$mms_name.']설비에서의 ':'')?>생산제품</h4>
    <ul class="ul_item">
        <?php for($i=0;$row=sql_fetch_array($result);$i++){ 
            /*
            $prf_tbl = ($row['bom_type'] == 'product') ? $g5['item_table'] : $g5['material_table'];
            $prf = ($row['bom_type'] == 'product') ? 'itm' : 'mtr';
            //작업자 상관없이 전체 현재 재고갯수를 추출하기 위한 코드
            $tsql = " SELECT SUM({$prf}_value) AS total FROM {$prf_tbl}
                WHERE {$prf}_status NOT IN ('trash','delete')
                    AND bom_idx = '{$row['bom_idx']}'
                    AND pri_idx = '{$row['pri_idx']}'
                GROUP BY pri_idx
            ";
            */
            $tsql = " SELECT SUM(pic_value) AS total FROM {$g5['production_item_count_table']}
                        WHERE pri_idx = '{$row['pri_idx']}'
            ";

            $tres = sql_fetch($tsql);
            $total = ($tres['total']) ? $tres['total'] : 0;
            
            //작업자별 현재 재고갯수를 추출하기 위한 코드
            /*
            $psql = " SELECT SUM({$prf}_value) AS ptotal FROM {$prf_tbl}
                WHERE {$prf}_status NOT IN ('trash','delete')
                    AND bom_idx = '{$row['bom_idx']}'
                    AND pri_idx = '{$row['pri_idx']}'
                    AND mb_id = '{$member['mb_id']}'
                GROUP BY mb_id
            ";
            */
            $psql = " SELECT SUM(pic_value) AS ptotal FROM {$g5['production_item_count_table']}
                        WHERE pri_idx = '{$row['pri_idx']}'
                            AND mb_id = '{$member['mb_id']}'
            ";
            $pres = sql_fetch($psql);
            $ptotal = ($pres['ptotal']) ? $pres['ptotal'] : 0;
            
            //작업하는 제품의 이미지를 추출하는 코드
            $flesql = " SELECT * FROM {$g5['file_table']}
                    WHERE fle_db_table = 'bom'
                        AND fle_type = 'bomf1'
                        AND fle_db_id = '{$row['bom_idx']}' 
                    ORDER BY fle_reg_dt DESC, fle_idx DESC LIMIT 1
            ";
            $frow = sql_fetch($flesql);
            //작업이미지의 섬네일을 추출하는 코드
            $twd = 400;
            $tht = 300;
            $orig_path = G5_PATH.$frow['fle_path'];
            $goal_path = G5_PATH.$frow['fle_path'];
            $goal_url = G5_URL.$frow['fle_path'];
            $fle_name = $frow['fle_name'];
            $thumb_img = $goal_url.'/'.thumbnail($fle_name,$orig_path,$goal_path,$twd,$tht,false,true,'center');
            $noimg_img = G5_USER_ADMIN_MOBILE_IMG_URL.'/no_image.png';
            $thumb_url = (!$frow) ? $noimg_img : $thumb_img;
        ?>
        <li class="li_desc">
            <dt class="dt_ttl">설비명(Equipment name)</dt>
            <dd class="dd_des dd_mms">
                <?=$g5['mms_arr'][$row['mms_idx']]?>
                <?php if(!$row['mms_call_yn']){ ?>
                    <?php if($row['pri_ing']){ ?>
                        <strong class="st_ing">Running!</strong>
                    <?php } ?>
                <?php } else { ?>
                    <strong class="st_call">Calling!</strong>
                <?php } ?>
            </dd>
            <dt class="dt_ttl">고객사(Cutomer)</dt>
            <dd class="dd_des"><?=$row['cst_name']?></dd>
            <dt class="dt_ttl">품번(Item number)</dt>
            <dd class="dd_des"><?=$row['bom_part_no']?></dd>
            <dt class="dt_ttl">품명(Item name)</dt>
            <dd class="dd_des"><?=$row['bom_name']?></dd>
            <dt class="dt_ttl">유형(Item type)</dt>
            <dd class="dd_des"><?=$g5['set_bom_type_value'][$row['bom_type']]?>(<?=$row['bom_type']?>)</dd>
            <dt class="dt_ttl">나의 생산목표(Production goal)</dt>
            <dd class="dd_des"><?=$row['pri_value']?></dd>
            <dt class="dt_ttl">현재 나의생산량(Current my count)</dt>
            <dd class="dd_des"><?=$ptotal?></dd>
            <dt class="dt_ttl">현재 전체생산량(Current total count)</dt>
            <dd class="dd_des"><?=$total?></dd>
            <dt class="dt_ttl">제품이미지(Item image)</dt>
            <dd class="dd_des"><img src="<?=$thumb_url?>" alt="<?=$row['bom_name']?>_이미지"></dd>
            <div class="btn_box">
                <form name="formA<?=$i?>" class="formA" id="formA<?=$i?>" action="./production_list_update.php" onsubmit="return form01_submit(this);" method="post">
                    <input type="hidden" name="call" value="0">
                    <input type="hidden" name="mms_idx" value="<?=$row['mms_idx']?>">
                    <input type="hidden" name="prd_idx" value="<?=$row['prd_idx']?>">
                    <input type="hidden" name="pri_idx" value="<?=$row['pri_idx']?>">
                    <input type="hidden" name="bom_idx" value="<?=$row['bom_idx']?>">
                    <input type="hidden" name="bom_type" value="<?=$row['bom_type']?>">
                    <input type="hidden" name="pri_ing" value="<?=$row['pri_ing']?>">
                    <input type="submit" value="<?=(($row['pri_ing'])?'END':'START')?>" onclick="document.pressed=this.value" class="mbtn btn_toggle<?=(($row['pri_ing'])?' focus':'')?>">
                    <?php if($row['pri_ing']){ ?>
                    <div class="tooltip">
                        <span class="tooltip_close"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                        <input type="text" name="pri_cnt" value="" placeholder="생산수량" class="frm_input pri_cnt" autocomplete="off">
                    </div>
                    <?php } ?>
                </form>
                <form name="formB<?=$i?>" class="formB" id="formB<?=$i?>" action="./production_list_update.php" onsubmit="return form01_submit(this);" method="post">
                    <input type="hidden" name="call" value="1">
                    <input type="hidden" name="mms_idx" value="<?=$row['mms_idx']?>">
                    <input type="hidden" name="prd_idx" value="<?=$row['prd_idx']?>">
                    <input type="hidden" name="pri_idx" value="<?=$row['pri_idx']?>">
                    <input type="hidden" name="bom_idx" value="<?=$row['bom_idx']?>">
                    <input type="hidden" name="call_yn" value="<?=(($row['mms_call_yn'])?'0':'1')?>">
                    <input type="submit" value="<?=(($row['mms_call_yn'])?'NoCall':'Call')?>" class="mbtn btn_call<?=(($row['mms_call_yn'])?' focus':'')?>">
                </form>
            </div>
        </li>
        <?php } ?>
        <?php if($i == 0){ ?>
        <li class="li_empty">데이터가 존재하지 않습니다.</li>
        <?php } ?>
    </ul>
</div>
<script>
var url = '<?=G5_USER_ADMIN_MOBILE_URL?>/production_list.php<?=(($mms_idx)?'?mms_idx='.$mms_idx:'')?>';


$('.tooltip_close').on('click',function(){
    $(this).siblings('input').val('');
    $(this).parent().removeClass('focus');
});

$('input[name="pri_cnt"]').on('input',function(){
    var num = $(this).val().replace(/[^0-9]/g,"");
    num = (num == '0') ? '' : num;
    $(this).val(num);
});

function form01_submit(f){ //inp
    var num = 0;
    var flag = true;
    if(document.pressed == 'END'){
        var ajax_stock_check_url = '<?=G5_USER_ADMIN_MOBILE_URL?>/ajax/end_stock_check.php';
        $.ajax({
            type: "POST",
            url: ajax_stock_check_url,
            dataType: "text",
            data: {"pri_idx":f.pri_idx.value,"mb_id": '<?=$member['mb_id']?>'},
            async: false,
            success: function(res){
                num = Number(res);
            },
            error: function(xmlReq){
                alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
            }
        });
        //종료시점에 재고가 0이면 수기입력하자
        if(num == 0 && !f.pri_cnt.value){
            $(f).find('.tooltip').addClass('focus');
            flag = false;
            return flag;
        }
        else{
            $(f).find('.tooltip').removeClass('focus');
        }
    }

    if(!confirm(f.pri_cnt.value + '개가 정확합니까?')){
        f.pri_cnt.value = '';
        flag = false;
        return flag;
    }
    else{
        $('#loading_box').addClass('focus');
        var ajax_stock_insert_url = '<?=G5_USER_ADMIN_MOBILE_URL?>/ajax/end_stock_insert.php';
        $.ajax({
            type: "POST",
            url: ajax_stock_insert_url,
            dataType: "text",
            data: {
                "prd_idx": f.prd_idx.value,
                "pri_idx":f.pri_idx.value,
                "mb_id": '<?=$member['mb_id']?>',
                "bom_idx": f.bom_idx.value,
                "bom_type": f.bom_type.value,
                "pri_cnt": f.pri_cnt.value
            },
            async: false,
            success: function(res){
                if(res == 'ok')
                    $('#loading_box').removeClass('focus');
            },
            error: function(xmlReq){
                alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
            }
        });
    }
    return flag;
}



</script>
<?php
include_once('./_tail.php');