<?php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 메뉴입니다.');

$g5['title'] = 'QR Test';
include_once(G5_PATH.'/head.sub.php');

$imgsrc = base64_encode(file_get_contents("https://static.thenounproject.com/png/4778723-200.png"));
$imgsrc2 = "data:image/png;base64,".$imgsrc;
// echo '<img src="data:image/png;base64,'.$imgsrc.'">';

?>
<style>
body {background-color:white;color:#818181;}
.qr_win {display:block;text-align:center;padding:20px;}
@media print {
  @page { margin: 0; }
  body { margin: 0; }
}
</style>

<div clsas="pr_wrapper" style="text-align:center">
    <img src="<?=$imgsrc2?>" alt="Avatar" style="width:40px;border:solid 1px #ddd;">
    <div style="display:inline-block;">
        <h3 style="font:bold 8px Arial">John Doe</h3>
    </div>
</div>


<script>
$(function(e){
    setTimeout(() => {
        // $('button').trigger('click');
        window.close();
    }, 2000);
});
window.print();
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');