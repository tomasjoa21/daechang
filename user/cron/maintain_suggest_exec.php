<?php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = 'Maintain Suggest Machine Learning';
include_once('./_head.sub.php');

?>
<style>
#hd_login_msg {display:none;}
.div_result {font-size:1.5em;font-weight:bold;}
.div_result2 {font-size:1.4em;}
</style>

<div style='font-size:9pt;text-align:center;'>
	<p><img src="<?=G5_USER_ADMIN_URL?>/img/loading.gif"><p>
</div>
<div style="text-align:center;display:no ne;" id="cont"></div>


<?php
include_once ('./_tail.sub.php');

// $command = 'python /home/admin/maintain/maintain.py';
// // $command = 'python /home/admin/maintain/maintain_test.py';
// // exec($command, $out, $status);
// system($command);
// passthru($command);
// print_r2($out);
// echo $status;

// $py_suggest = exec('python /home/admin/maintain/maintain_test.py');
// $py_suggest = exec('python /home/admin/maintain/maintain.py 2>&1');
// echo $py_suggest;

?>

<script>
// document.all.cont.innerHTML += "<br><?=$ymd?> 완료되었습니다.<br><font color=crimson><b>[끝]</b></font>";
$.getJSON(g5_user_url+'/ajax/maintain_suggest_exec.php',{"aj":"su"},function(res) {
    // console.log(res);
    opener.location.reload();
    window.close();
});
</script>
