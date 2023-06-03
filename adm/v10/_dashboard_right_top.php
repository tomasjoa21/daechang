<?php
if (!defined('_GNUBOARD_')) exit;
//$sub_menu : 현재 메뉴코드 915140
//$cur_mta_idx : 현재 메타idx 422

// print_r2($g5['set_pkr_size_value']);
$g5['set_pkr_size_value_opts'] = '';
$pn = 0;
$first_pn = 0;
foreach($g5['set_pkr_size_value'] as $pk => $pv){
    if($pn == 0){$first_pn = $pv;}
    $kan = (int)trim($pv) / $first_pn;
    $g5['set_pkr_size_value_opts'] .= '<option value="'.trim($pk).'">'.$kan.'칸</option>';
    $pn++;
}

?>
<style>
#bs_top_right{position:absolute;top:0px;right:0px;padding-right:15px;}
#bs_top_right #sp_x{margin-left:0px;margin-right:0px;}
#bs_top_right select{border:1px solid #888;}
#bs_top_right button{margin-left:20px;}
</style>
<div id="bs_top_right" class="bs_edit">
    <span>크기: </span>
    <select name="pkr_width" id="pkr_width">
        <option value="">가로</option>
        <?php echo $g5['set_pkr_size_value_opts']; ?>
    </select>
    <span id="sp_x">×</span>
    <select name="pkr_height" id="pkr_height">
        <option value="">세로</option>
        <?php echo $g5['set_pkr_size_value_opts']; ?>
    </select>
    <!--
    <span>공통패딩: </span>
    <select name="pkr_padding" id="pkr_padding">
    <?php ;//echo $g5['set_pkr_padding_value_options']; ?>
    </select>
    -->
    <button class="btn btn_01" id="grid_add">위젯추가</button>
</div><!--//#bs_top_right-->
<script>
$('#grid_add').on('click',function(){
    var ajax_url = g5_user_admin_ajax_url+'/grid_add.php';
    // var sub_menu = '<?=$sub_menu?>';
    var mta_idx = <?=$cur_mta_idx?>;
    var wd = $(this).siblings('#pkr_width').val();
    var ht = $(this).siblings('#pkr_height').val();

    if(!wd || !ht){
        alert('[가로 X 세로]크기를 제대로 선택해 주세요.');
        return false;
    }
    // var pd = $(this).siblings('#pkr_padding').val();
    // console.log('mta_idx:'+mta_idx+', width:'+wd+', height:'+ht);
    $.ajax({
        type: 'POST',
        url: ajax_url,
        // dataType: 'text'
        data: {'menu_mta_idx':mta_idx,'grid_width':wd,'grid_height':ht},
        success: function(res){
            location.reload();
        },
        error: function(req){
            alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
        }
    });
});
</script>