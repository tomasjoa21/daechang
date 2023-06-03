<?php
include_once('./_common.php');

$demo = 0; //demo mode = 1

$g5['title'] = 'BOM재고량 업데이트';
include_once('./_head.sub.php');

$yesterday = get_dayAddDate(G5_TIME_YMD,-1);
$oneweekday = get_dayAddDate(G5_TIME_YMD,-14);
// $yesterday = G5_TIME_YMD;
// echo $yesterday;
// exit;
// $bom_sql = " SELECT bom_idx, bom_part_no, bom_type, bom_name FROM {$g5['bom_table']}
//     WHERE bom_status = 'ok'
// ";
$bom_sql = " SELECT pri.bom_idx, bom.bom_type FROM {$g5['production_item_table']} pri
                LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
                LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
    WHERE pri_status IN ('confirm','done')
        AND prd_start_date <= '{$yesterday}'
        AND prd_start_date >= '{$oneweekday}'
    GROUP BY pri.bom_idx
";
$bom_res = sql_query($bom_sql,1);

?>
<div class="" style="padding:10px;">
    <span>
        작업시작~~ <font color="crimson"><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
    </span><br><br>
    <span id="cont"></span>
</div>
<?php
include(G5_PATH.'/tail.sub.php');

$countgap = 10; //몇건씩 보낼지 설정
$sleepsec = 10000; //백만분에 몇초간 쉴지 설정(20000/1000000=0.02)(10000/1000000=0.01)(5000/1000000=0.005)
$maxscreen = 50; // 몇건씩 화면에 보여줄건지 설정

flush();
ob_flush();


$cnt = 0;
$result = $bom_res->num_rows;
// echo $result;
// exit;
for($i=0;$row=sql_fetch_array($bom_res);$i++){
    $cnt++;
    
    if($row['bom_type'] == 'product' || $row['bom_type'] == 'half'){
        $stock_sql = " SELECT SUM(itm_value) AS cnt FROM {$g5['item_table']} 
            WHERE bom_idx = '{$row['bom_idx']}'
                AND itm_status NOT IN ('delivery','defect','scrap','trash')
            GROUP BY bom_idx
        ";
    }
    else{
        $stock_sql = " SELECT SUM(mtr_value) AS cnt FROM {$g5['material_table']} 
            WHERE bom_idx = '{$row['bom_idx']}'
                AND mtr_status NOT IN ('delivery','defect','scrap','used','reject','trash')
            GROUP BY bom_idx
        ";
    }
    $stock = sql_fetch($stock_sql);

    $stock_update_sql = " UPDATE {$g5['bom_table']} SET
            bom_stock = '{$stock['cnt']}'
            , bom_update_dt = '".G5_TIME_YMDHIS."'
        WHERE bom_idx = '{$row['bom_idx']}'
    ";
    sql_query($stock_update_sql,1);

    echo "<script>document.all.cont.innerHTML += ['".$cnt."] - ".$row['bom_idx']." 처리됨<br>';</script>\n";

    flush();
    ob_flush();
    ob_end_flush();
    usleep($sleepsec);

    //보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
    if($cnt % $countgap == 0){
        echo "<script>document.all.cont.innerHTML += '<br>';</script>\n";
    }

    //화면 정리! 부하를 줄임 (화면을 싹 지움)
    if($cnt % $maxscreen == 0){
        echo "<script>document.all.cont.innerHTML = '';</script>\n";
    }
}
?>
<script>
    document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($i); ?>건 완료<br><br><font color='crimson'><b>[끝]</b></font>";
</script>