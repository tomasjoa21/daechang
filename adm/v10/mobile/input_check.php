<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//http://daechang2.epcs.co.kr/adm/v10/mobile/check.php?plt_idx=116
if($member['mb_9'] != 'admin_quality'){
    alert('품질관리권한을 가지고 계신분만 접근 가능합니다.', G5_USER_ADMIN_MOBILE_URL);
}

if($moi_cnt){
    $moi_cnt_arr = explode('_',$moi_cnt);
    $moi_idx = $moi_cnt_arr[0];
    $moi_count = $moi_cnt_arr[1];
}
else if($moi_idx){
    ;
}
else{
    alert('올바르게 접속해 주세요.');
}



if(!$moi_idx){
    alert('QR코드 스캔을 통해서 접근해 주세요.');
}

$moi_sql = " SELECT moi.moi_idx
                , moi.bom_idx
                , moi_check_yn
                , moi_check_text
                , moi_status
                , bct_idx
                , bom_name
                , bom_part_no
                , bom_stock_check_yn
    FROM {$g5['material_order_item_table']} moi
    LEFT JOIN {$g5['bom_table']} bom ON moi.bom_idx = bom.bom_idx
    WHERE moi.moi_idx = '{$moi_idx}'
";
$moi = sql_fetch($moi_sql);

$flesql = " SELECT * FROM {$g5['file_table']}
        WHERE fle_db_table = 'bom'
            AND fle_type = 'bomf2'
            AND fle_db_id = '{$moi['bom_idx']}' 
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
$thumb_path = $goal_path.'/'.thumbnail($fle_name,$orig_path,$goal_path,$twd,$tht,false,true,'center');
$noimg_img = G5_USER_ADMIN_MOBILE_IMG_URL.'/no_image.png';
$thumb_url = (!is_file($thumb_path)) ? $noimg_img : $thumb_img;

include_once('./_head.php');
?>
<div id="moi_box">
    <h4 id="moi_ttl"><?=(($moi_idx)?'['.$moi_idx.']번 발주제품의 ':'')?>입고검사</h4>
    <?php if(@count($moi)){ ?>
    <div class="moi_cont">
    <ul class="ul_moi">
        <li class="li_moi">
            <h5><?=$moi['bom_name']?></h5>
            <p class="bom_nick">
                <span>[<?=$g5['cats_key_val'][$moi['bct_idx']]?>]</span>
                <strong><?=$moi['bom_part_no']?></strong>
            </p>
            <div class="chk_box">
                <img src="<?=$thumb_url?>" alt="<?=$moi['bom_name']?>_이미지">
            </div>
        </li>
    </ul>
    <p class="mtr_status" style="margin-top:20px;">
        발주제품상태 : <?=$g5['set_moi_status_value'][$moi['moi_status']]?>
    </p>
    <form name="form01" id="form01" action="./input_check_update.php" onsubmit="return form01_submit(this);">
        <input type="hidden" name="moi_idx" value="<?=$moi_idx?>">
        <div id="radio_box">
            <label class="radio">
                <input type="radio" class="check_yn" name="moi_check_yn"<?=((!$moi['moi_check_yn'])?' checked':'')?> value="0"> 
                <p class="radio_tag">아직 검사전</p>
            </label>
            <label class="radio">
                <input type="radio" class="check_yn" name="moi_check_yn"<?=(($moi['moi_check_yn'])?' checked':'')?> value="1"> 
                <p class="radio_tag">검사완료(합격)!</p>
            </label>
        </div>
        <!--select name="plt_defect_type" id="plt_defect_type">
            <option value="">::불량유형::</option>
            <?php //echo $g5['set_defect_type_options']?>
        </select-->
        <p style="color:red;font-weight:700;padding-top:20px;">
            제품에 문제가 있으면 "검사완료(합격)!"는 누르지 말고<br>
            반려사유만 기입하고, "확인"버튼을 눌러주세요.
        </p>
        <textarea name="moi_check_text" id="moi_check_text" rows="3" placeholder="반려사유를 기입하세요."><?php echo $moi['moi_check_text']?></textarea>
        <div id="btn_box">
            <input type="submit" value="확인" class="btn btn04">
        </div>
    </form>
    </div><!--//.plt_cont-->
    <?php } else { ?>
    <div class="moi_empty">발주제품ID 데이터가 없습니다.<br>라벨의 발주제품ID 번호를 입력박스에<br>입력하고 검색해 주세요.</div>
    <form id="fmoi_idx" method="GET">
        <input type="text" name="moi_idx" value="<?=$moi_idx?>">
        <input type="submit" value="검색">
    </form>
    <?php } ?>
</div><!--//.plt_box-->
<script>

function form01_submit(f){

    return true;
}
</script>
<?php
include_once('./_tail.php');