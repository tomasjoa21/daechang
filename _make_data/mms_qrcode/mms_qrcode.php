<?php
include_once('./_common.php');
// define('_INDEX_', true);

$g5['title'] = 'QR코드표시';
include_once(G5_PATH.'/head.sub.php');
echo '<h2 style="line-height:2em;">'.$g5['title'].'</h2>';
?>
<link rel="stylesheet" href="<?=G5_URL?>/_make_data/_css/common.css">
<style>
strong{display:inline-block;width:90px;}
.target_url{}
.mms_idx{width:80px;}
</style>
<?php
include_once(G5_PATH.'/_make_data/head_menu.php');

$target_url_arr = array(
    'prd_start_url' => G5_USER_ADMIN_MOBILE_URL.'/production_list.php'
);
$target_url_options = '';
foreach($target_url_arr as $k => $v){
    $target_url_options .= '<option value="'.$k.'">'.$v.'</option>';
}
$action_url = G5_URL.'/_make_data/mms_qrcode/mms_qrcode.php?start=1';
$tags = '<p>입력하신 정보를 바탕으로 QR코드를 생성합니다.<br>해당 url과 필요한 정보데이터를 입력하고 [시작]버튼을 누르세요.<br>작업자 생산시작<br>
'.$target_url_arr['prd_start_url'].'<br><br>
</p>';
$tags .= '<br><strong>target_url:</strong><select name="target_url" class="frm_inpu target_url">'.PHP_EOL;
$tags .= $target_url_options;
$tags .= '</select>'.PHP_EOL;
$tags .= '<br><br><strong>mms_idx:</strong><input type="text" name="mms_idx" value="'.$mms_idx.'" class="frm_inpu mms_idx">'.PHP_EOL;
$tags .= '<br><br><div class="top_box"><a href="javascript:submit();" class="btn bg_primary">시작</a></div>';
echo $tags;
?>
<script>
var action_url = '<?=$action_url?>';
<?php if($target_url){ ?>
$('.target_url').val('<?=$target_url?>');
<?php } ?>
function submit(){
    var target_url = $('.target_url').val();
    var mms_idx = $('.mms_idx').val();

    if(!target_url){
        alert('타겟URL을 반드시 입력해주세요.');
        $('.target_url').focus();
        return false;
    }

    if(target_url){
        action_url += '&target_url=' + target_url;
    }

    if(mms_idx){
        action_url += '&mms_idx=' + mms_idx;
    }
    location.href = action_url;
}
</script>
<?php
if($start){
?>
<div class="" style="padding:10px;" style="text-align:center;">
    <?php if($target_url_arr[$target_url] && $mms_idx){
        $url = $target_url_arr[$target_url].'?mms_idx='.$mms_idx;
    ?>
    <img src="https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=<?=$url?>" style="display:inline-block;">
    <?php } ?>
</div>
<?php
}
include_once(G5_PATH.'/tail.sub.php');
?>