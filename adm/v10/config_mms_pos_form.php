<?php
$sub_menu = "910140";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '설비위치설정';
include_once('./_top_menu_manager.php');
include_once('./_head.php');
echo $g5['container_sub_title'];

$sql = " SELECT mms_idx, mms_name, mms_call_yn, mms_pos_x, mms_pos_y FROM {$g5['mms_table']}
            WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                AND mms_status = 'ok'
                AND mms_pos_yn = '1'
            ORDER BY mms_idx
";
$res = sql_query($sql,1);

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_default">위치설정</a></li>
</ul>';

?>
<script src="<?=G5_MONITOR_JS_URL?>/draggabilly.pkgd.min.js"></script>
<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="" id="token">

<section id="anc_cf_default">
	<h2 class="h2_frm">기본설정</h2>
	<?php echo $pg_anchor; ?>
    <div class="box">
        <ul class="tbl_frm01 tbl_wrap drag_box">
            <?php for($i=0;$row=sql_fetch_array($res);$i++){
            $pri_sql = " SELECT pri_ing FROM {$g5['production_item_table']} pri
                    LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
                WHERE pri.com_idx = '{$_SESSION['ss_com_idx']}'
                    AND prd_status = 'confirm'
                    AND pri_ing = '1'
                    AND mms_idx = '{$row['mms_idx']}'
                    AND prd_start_date = '".G5_TIME_YMD."'
                LIMIT 1
            "; 
            $pri = sql_fetch($pri_sql);
            $bg_class = ($pri['pri_ing']) ? ' ing' : '';
            $bg_class = ($row['mms_call_yn']) ? ' focus' : $bg_class;
            ?>
            <li class="drag<?=$bg_class?>" mms_idx="<?=$row['mms_idx']?>" mms_pos_x="<?=$row['mms_pos_x']?>" mms_pos_y="<?=$row['mms_pos_y']?>" style="left:<?=$row['mms_pos_x']?>px;top:<?=$row['mms_pos_y']?>px;"><?=$row['mms_name']?></li>
            <?php } ?>
        </ul>
    </div>	
</section>
</form>
<form name="mmsselectform" id="mmsselectform" method="post" onsubmit="return mmsselectform_submit(this);">
<div class="btn_fixed_top btn_confirm" style="display:no ne;">
    <select name="mms_idx">
        <option value="">::설비선택::</option>
        <?php foreach($g5['mms_arr'] as $mk => $mv){ ?>
        <option value="<?=$mk?>"><?=$mv?></option>
        <?php } ?>
    </select>
    <input type="submit" name="act_button" value="표시" onclick="document.pressed=this.value" class="btn wg_btn_success">
    <input type="submit" name="act_button" value="비표시" onclick="document.pressed=this.value" class="btn wg_btn_danger">
</div>
</form>

<script>
$(function(){
    var $draggbles = $('.drag').draggabilly({
        containment: true,
        grid: [4, 4]
    });

    $draggbles.on('dragEnd',function(event,pointer){
        // console.log('.drag_box: {' + $('.drag_box').offset().left + '|' + $('.drag_box').offset().top + '}' + $(event.target).attr('mms_idx') + ':{' + $(event.target).offset().left + '|' + $(event.target).offset().top + '}');
        
        $.ajax({
            url: '<?=G5_USER_ADMIN_URL?>/config_mms_pos_form_update.php',
            type: 'POST',
            dataType: 'text',
            data: {
                'mms_idx': $(event.target).attr('mms_idx'),
                'mms_pos_x': $(event.target).offset().left - $('.drag_box').offset().left,
                'mms_pos_y': $(event.target).offset().top - $('.drag_box').offset().top
            },
            success: function(res){
                console.log(res);
            },
            error: function(xmlReq){
                alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
            }
        });
        
    });
});

function fconfigform_submit(f) {

    <?php ;//echo get_editor_js("mng_msg_content"); ?>
    <?php ;//echo chk_editor_js("mng_msg_content"); ?>

    f.action = "./config_mms_pos_form_update.php";
    return true;
}

function mmsselectform_submit(f) {

    <?php ;//echo get_editor_js("mng_msg_content"); ?>
    <?php ;//echo chk_editor_js("mng_msg_content"); ?>

    f.action = "./config_mms_select_form_update.php";
    return true;
}
</script>

<?php
include_once ('./_tail.php');
?>
