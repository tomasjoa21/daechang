<?php
include_once('./_common.php');
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$mms_idx)
    alert('[mms_idx] does not exist.');

$wsql = " SELECT mb.mb_name, mmw.mb_id FROM {$g5['mms_worker_table']} mmw
            LEFT JOIN {$g5['member_table']} mb ON mmw.mb_id = mb.mb_id
        WHERE mb_leave_date = ''
            AND mb_intercept_date = ''
            AND mms_idx = '{$mms_idx}'
";
// echo $wsql;
$wok_res = sql_query($wsql,1);

$mms = sql_fetch(" SELECT mms_idx, mms_name FROM {$g5['mms_table']} WHERE mms_idx = '{$mms_idx}' ");

//설비에 매칭된 제품을 추출한다.
$isql = " SELECT pri.pri_idx 
            , pri.bom_idx
            , bom.bom_name
            , bom.bom_part_no
            , sub.bom_name AS sub_name
            , sub.bom_part_no AS sub_part_no
            , pri.mms_idx
            , pri_value
            , mms.mms_name
        FROM {$g5['production_item_table']} pri
            LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
            LEFT JOIN {$g5['bom_table']} bom ON prd.bom_idx = bom.bom_idx
            LEFT JOIN {$g5['bom_table']} sub ON pri.bom_idx = sub.bom_idx
            LEFT JOIN {$g5['mms_table']} mms ON pri.mms_idx = mms.mms_idx
        WHERE pri.mms_idx = '{$mms_idx}'
            AND pri.pri_status = 'confirm'
            AND prd.prd_status = 'confirm'
            AND prd.prd_start_date = '".G5_TIME_YMD."'
";

$ires = sql_query($isql,1);

$mb_where = ($mb_id) ? "AND mb_id='".$mb_id."'" : '';

//저장된 production_member중 가장 최근의 것 1개를 추출한다.
$sql = " SELECT mb_id
            , prm.pri_idx
            , prm_pair_code
            , prm_status 
        FROM {$g5['production_member_table']} prm
            LEFT JOIN {$g5['production_item_table']} pri ON prm.pri_idx = pri.pri_idx
        WHERE mms_idx = '{$mms_idx}'
            {$mb_where}
            AND prm_update_dt LIKE '".G5_TIME_YMD."%'
        ORDER BY prm_update_dt DESC
";
// echo $sql;
$old_idxs = array();
$old_idxs_str = '';
$old = array();
if($mb_id){
    $res = sql_query($sql,1);
    for($i=0;$row=sql_fetch_array($res);$i++){
        array_push($old,$row);
        array_push($old_idxs,$row['pri_idx']);
        $old_idxs_str .= ($old_idxs_str == '') ? $row['pri_idx'] : ','.$row['pri_idx'];
    }
}
// print_r2($old);
$g5['title'] = $mms['mms_name'].'-생산계획';
include_once(G5_USER_PATH.'/_head.php');

//테스트로 사용하는 배열(최종적으로는 삭제해야 한다.)
$prs_arr = array(
    '01087269235' => array(
        'start' => 0
        , 'end' => 0.3
    )
    , '01027238699' => array(
        'start' => 0.3
        , 'end' => 0.6
    )
    , '01084602229' => array(
        'start' => 0.6
        , 'end' => 1
    )
);

?>
<div class="alert alert-dark" role="alert">
  <?=$mms['mms_name']?> - Production Setting ( <?=G5_TIME_YMD?>  )
</div>
<form name="form01" id="form01" action="./<?=$g5['file_name']?>_update.php" onsubmit="return form01_submit(this);" method="post" autocomplete="off" >
<div class="d-grid div_worker">
    <select class="form-select form-select-lg mb-3" name="mb_id" id="mb_id" aria-label=".form-select-lg example">
        <option selected>SELECT Worker</option>
        <?php for($i=0;$wrow=sql_fetch_array($wok_res);$i++){ ?>
            <option value="<?=$wrow['mb_id']?>"><?=$wrow['mb_name']?></option>
        <?php } ?>
        <option value="01027238699">떠이</option>
        <option value="01084602229">파실</option>
    </select>
    <?php if($mb_id){ ?>
    <script>
    $('#mb_id').val('<?=$mb_id?>');
    </script>
    <?php } ?>
</div>
<div class="d-grid">
<input type="hidden" name="mms_idx" id="mms_idx" value="<?=$mms_idx?>">
<input type="hidden" name="prm_pair_code" id="prm_pair_code" value="">
<input type="hidden" name="pri_idxs" id="pri_idxs" value="<?=$old_idxs_str?>">
<ul class="ul_itm">
    <?php 
    $tot_pris = '';
    for($i=0;$irow=sql_fetch_array($ires);$i++){ 
        $tot_pris .= ($tot_pris == '') ? $irow['pri_idx'] : ','.$irow['pri_idx'];
        //테스트로 사용하는 변수 나중에는 삭제해라
        $per = ($mb_id) ? $prs_arr[$mb_id][$old[0]['prm_status']] * 100 : 0;
        $cur = ($mb_id) ? number_format($irow['pri_value'] * $prs_arr[$mb_id][$old[0]['prm_status']]) : 0;
    ?>
    <li val="<?=$irow['pri_idx']?>" class="li_itm<?=((in_array($irow['pri_idx'],$old_idxs))?' focus':'')?>">
        <i class="fa fa-check-circle pri_chk" aria-hidden="true" pri_idx="<?=$irow['pri_idx']?>"></i>
        <strong style="font-size:1.1em;"><?=$irow['sub_name']?></strong><br>
        <span style="color:#a85d1f;">[<?=$irow['sub_part_no']?>]</span><br>
        <span style="font-weight:700;font-szie:1.1em;"><?=number_format($irow['pri_value'])?> 개 중에</span> =>
        <span><?=$cur?>개 완료</span> <span>(<?=$per?>%)</span><br>
        <span style="font-size:0.8em;color:darkblue;">(END ITEM) <?=$irow['bom_name']?></span><br>
        <span style="font-size:0.8em;color:darkblue;padding-left:70px;">[<?=$irow['bom_part_no']?>]</span>
        <img src="<?=G5_USER_IMG_URL?>/item.png" class="itm_img">
    </li>
    <?php } ?>
</ul>
</div>
<div class="bottom_btn_box">
    <input type="hidden" name="total_pri_idxs" value="<?=$tot_pris?>">
    <input type="submit" name="act_button" class="btn btn-<?=(($old[0]['prm_status'] == 'start')?'success':'secondary')?>" onclick="document.pressed=this.value" value="START">
    <input type="submit" name="act_button" class="btn btn-<?=(($old[0]['prm_status'] == 'end')?'success':'secondary')?>" onclick="document.pressed=this.value" value="END">
</div>
</form>
<script>
$(function(){
    $('.pri_chk').on('click',function(){
        if($(this).parent().hasClass('focus')){
            $(this).parent().removeClass('focus');
        }else{
            $(this).parent().addClass('focus');
        }

        var pri_idxs = '';
        $('.li_itm').each(function(){
            if($(this).hasClass('focus')){
                pri_idxs += (pri_idxs == '') ? $(this).attr('val') : ',' + $(this).attr('val');
            }
        });
        // console.log(pri_idxs);
        $('#pri_idxs').val(pri_idxs);
    });
});

function form01_submit(f){
    
    //작업자를 선택하세요
    if(!f.mb_id.value){
        alert('Please select a worker.');
        f.mb_id.focus();
        return false;
    }

    //셋팅할 생산계획 제품을 선택하세요
    if(!f.pri_idxs.value){
        alert('Please select a product to work on.');
        return false;
    }

    return true;
}
</script>
<?php
include_once(G5_USER_PATH.'/_tail.php');