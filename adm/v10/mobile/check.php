<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
//http://daechang2.epcs.co.kr/adm/v10/mobile/check.php?plt_idx=116
if($member['mb_9'] != 'admin_quality'){
    alert('품질관리권한을 가지고 계신분만 접근 가능합니다.', G5_USER_ADMIN_MOBILE_URL);
}

if(!$plt_idx){
    alert('QR코드 스캔을 통해서 접근해 주세요.');
}
$plt = sql_fetch(" SELECT * FROM {$g5['pallet_table']} WHERE plt_idx = '{$plt_idx}' ");

$itm_sql = " SELECT itm.bom_idx
                , bct_idx
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
    <?php if($itm_res->num_rows){ ?>
    <div class="plt_cont">
    <ul class="ul_plt">
    <?php for($i=0;$row=sql_fetch_array($itm_res);$i++){ 
        $flesql = " SELECT * FROM {$g5['file_table']}
                WHERE fle_db_table = 'bom'
                    AND fle_type = 'bomf2'
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
        $thumb_path = $goal_path.'/'.thumbnail($fle_name,$orig_path,$goal_path,$twd,$tht,false,true,'center');
        $noimg_img = G5_USER_ADMIN_MOBILE_IMG_URL.'/no_image.png';
        $thumb_url = (!is_file($thumb_path)) ? $noimg_img : $thumb_img;
    ?>
        <li class="li_itm">
            <h5><?=$row['itm_name']?></h5>
            <p class="bom_nick">
                <span>[<?=$g5['cats_key_val'][$row['bct_idx']]?>]</span>
                <strong><?=$row['itm_part_no']?></strong>
            </p>
            <div class="chk_box">
                <img src="<?=$thumb_url?>" alt="<?=$row['bom_name']?>_이미지">
            </div>
        </li>
    <?php } ?>
    </ul>
    <p class="plt_status" style="margin-top:20px;">
        파레트상태 : <?=$g5['set_plt_status_value'][$plt['plt_status']]?>
    </p>
    <form name="form01" id="form01" action="./check_update.php" onsubmit="return form01_submit(this);">
        <input type="hidden" name="plt_idx" value="<?=$plt_idx?>">
        <div id="radio_box">
            <label class="radio">
                <input type="radio" class="check_yn" name="plt_check_yn"<?=((!$plt['plt_check_yn'])?' checked':'')?> value="0"> 
                <p class="radio_tag">아직 검사전</p>
            </label>
            <label class="radio">
                <input type="radio" class="check_yn" name="plt_check_yn"<?=(($plt['plt_check_yn'])?' checked':'')?> value="1"> 
                <p class="radio_tag">검사완료(합격)!</p>
            </label>
        </div>
        <!--select name="plt_defect_type" id="plt_defect_type">
            <option value="">::불량유형::</option>
            <?php //echo $g5['set_defect_type_options']?>
        </select-->
        <!--textarea name="plt_defect_text" id="plt_defect_text" rows="3" placeholder="구체적인 내용을 기입하세요."><?php //echo $plt['plt_defect_text']?></textarea-->
        <div id="btn_box">
            <input type="submit" value="확인" class="btn btn04">
        </div>
    </form>
    </div><!--//.plt_cont-->
    <?php } else { ?>
    <div class="plt_empty">파레트 데이터가 없습니다.<br>라벨의 파레트번호를 입력박스에<br>입력하고 검색해 주세요.</div>
    <form id="fplt_idx" method="GET">
        <input type="text" name="plt_idx" value="<?=$plt_idx?>">
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