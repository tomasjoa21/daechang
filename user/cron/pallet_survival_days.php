<?php
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

$g5['title'] = '파레트 데이터 삭제처리';
include_once('./_head.sub.php');

//-- 화면 표시
$countgap = ($demo) ? 10 : 20;    // 몇건씩 보낼지 설정
$maxscreen = ($demo) ? 30 : 150;  // 몇건씩 화면에 보여줄건지?
$sleepsec = 100;     // 천분의 몇초간 쉴지 설정 (1sec=1000)

$days = $g5['setting']['mng_pallet_days'];
$max_dt = get_dayAddDate(G5_TIME_YMDHIS,-$days).' 00:00:00';
$sql = " SELECT plt_idx, plt_status, plt_reg_dt FROM {$g5['pallet_table']} 
            WHERE plt_reg_dt < '{$max_dt}'
                AND plt_status != 'delivery'
            ORDER BY plt_reg_dt DESC
";
// echo $sql;
$res = sql_query($sql,1);
/*
$plt_sql = " UPDATE {$g5['pallet_table']} SET
            mb_id_delivery = ''
            , plt_history = CONCAT(plt_history,'\nok|".G5_TIME_YMDHIS."')
            , plt_status = 'ok'
            , plt_date = '0000-00-00'
            , plt_update_dt = '".G5_TIME_YMDHIS."'
        WHERE plt_idx = '{$plt_idx}'
            AND mb_id_delivery = '{$mb_id_delivery}'
";
sql_query($plt_sql,1);

$itm_sql = " UPDATE {$g5['item_table']} SET
            itm_history = CONCAT(itm_history,'\nfinish|".G5_TIME_YMDHIS."')
            , itm_status = 'finish'
            , itm_delivery_dt = '0000-00-00 00:00:00'
            , itm_update_dt = '".G5_TIME_YMDHIS."'
        WHERE plt_idx = '{$plt_idx}'
";
sql_query($itm_sql,1);
*/
?>

<style>
#hd_login_msg {display:none;}
</style>

<span style='font-size:9pt;'>
	<p><?=($db_id)?$db_id:$ym?> 입력시작 ...<p><font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전에는 중간에 중지하지 마세요.<p>
</span>
<span id="cont"></span>

<?php
include_once ('./_tail.sub.php');

flush();
ob_flush();
ob_end_flush();

$cnt=0;

for($i=0;$row=sql_fetch_array($res);$i++){
    $cnt++;
    if($demo){
        if($cnt >= 10) {break;}
    }

    $plt_sql = " UPDATE {$g5['pallet_table']} SET
                plt_history = CONCAT(plt_history,'\ntrash|".G5_TIME_YMDHIS."')
                , plt_status = 'trash'
                , plt_update_dt = '".G5_TIME_YMDHIS."'
            WHERE plt_idx = '{$row['plt_idx']}'
    ";
    sql_query($plt_sql,1);
    $itm_sql = " UPDATE {$g5['item_table']} SET
                itm_history = CONCAT(itm_history,'\nfinish|".G5_TIME_YMDHIS."')
                , plt_idx = '0'
                , itm_status = 'finish'
                , itm_delivery_dt = '0000-00-00 00:00:00'
                , itm_update_dt = '".G5_TIME_YMDHIS."'
            WHERE plt_idx = '{$row['plt_idx']}'
    ";
    sql_query($itm_sql,1);

    echo "<script> document.all.cont.innerHTML += '".$cnt.". ".$row['plt_idx']." (".$row['plt_reg_dt'].") 완료<br>'; </script>\n";

    flush();
    @ob_flush();
    @ob_end_flush();
    usleep($sleepsec);

	// 보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
	if ($cnt % $countgap == 0)
		echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";

	// 화면 정리! 부하를 줄임 (화면 싹 지움)
	if ($cnt % $maxscreen == 0)
		echo "<script> document.all.cont.innerHTML = ''; </script>\n";
}

?>
<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt) ?>건 완료<br><font color=crimson><b>[끝]</b></font>";
</script>