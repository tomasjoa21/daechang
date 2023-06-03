<?php
include_once('./_common.php');
// define('_INDEX_', true);

$g5['title'] = '자재등록';
include_once(G5_PATH.'/head.sub.php');
echo '<h2 style="line-height:2em;">'.$g5['title'].'</h2>';
?>
<link rel="stylesheet" href="<?=G5_URL?>/_make_data/_css/common.css">
<style>
.input_date{width:80px;}

</style>
<?php
include_once(G5_PATH.'/_make_data/head_menu.php');
//자재데이터가 200 ~ 400 범위에서 백단위로 랜덤하게 생성된다.
$min = 2;
$max = 4;

$action_url = G5_URL.'/_make_data/data_mtr/mtr_add.php?start=1';
$start_tag = '<p>입력하신 입고일을 기준으로 [LX2] or [LX2 PE]차종에 해당하는 자재데이터를 생성합니다.<br>반드시 입력하고 [시작]버튼을 누르세요.</p>';
$start_tag .= '<br><input type="text" readonly name="input_date" value="'.$prd_start_date.'" class="frm_inpu input_date">'.PHP_EOL;
$start_tag .= '<div class="top_box"><a href="javascript:submit();" class="btn bg_primary">시작</a></div>';
echo $start_tag;
?>
<script>
var action_url = '<?=$action_url?>';
$("input[name=input_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99",closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });

function submit(){
    var input_date = $('.input_date').val();
    if(!input_date){
        alert('입고일을 입력해 주세요.');
        return false;
    }
    action_url += '&input_date=' + input_date;
    location.href = action_url;
}
</script>
<?php
if($start){

?>
<div class="" style="padding:10px;">
    <span>
        작업시작~~ <font color="crimson"><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
    </span><br><br>
    <span id="cont"></span>
</div>
<?php
include_once(G5_PATH.'/tail.sub.php');


$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 5000; //백만분에 몇초간 쉴지 설정(20000/1000000=0.02)(10000/1000000=0.01)(5000/1000000=0.005)
$maxscreen = 50; // 몇건씩 화면에 보여줄건지 설정

flush();
ob_flush();


//LX2 or LX2 PE 차종의 제품만 추출한다.
$prd_sql = " SELECT bom_idx
                , cst_idx_provider
                , bom_part_no
                , bom_name
                , bom_price
                , bom_stock_check_yn
        FROM {$g5['bom_table']} bom
        WHERE bom_status = 'ok'
            AND com_idx = '{$_SESSION['ss_com_idx']}'
            AND bom_type = 'material'
            AND bct_idx IN ('60','70')
        ORDER BY bom_idx
";
// echo $prd_sql;exit;
$res = sql_query($prd_sql, 1);
$cnt = 0;
for($i=0;$row=sql_fetch_array($res);$i++){
    $cnt++;

    $sql = " INSERT INTO {$g5['material_table']}
        (com_idx, cst_idx_provider, bom_idx, mtr_part_no, mtr_name, mtr_type, mtr_value, mtr_price, mtr_status, mtr_date, mtr_reg_dt, mtr_update_dt) VALUES
    ";
    $dt = strtotime($input_date);
    $val = mt_rand($min,$max) * 100;
    $status = ($row['bom_stock_check_yn'] == '1') ? 'pending' : 'ok';
    for($j=0;$j<$val;$j++){
        $dt = $dt + $cnt + $j;
        $datetime = date('Y-m-d H:i:s',$dt);
        $sql .= ($j > 0) ? ',':'';
        $sql .= " ('{$_SESSION['ss_com_idx']}'
            , '{$row['cst_idx_provider']}'
            , '{$row['bom_idx']}'
            , '{$row['bom_part_no']}'
            , '{$row['bom_name']}'
            , 'material'
            , '1'
            , '{$row['bom_price']}'
            , '{$status}'
            , '{$input_date}'
            , '{$datetime}'
            , '{$datetime}')
        ";
    }
    sql_query($sql,1);

    echo "<script>document.all.cont.innerHTML += '".$cnt." - 처리됨<br>';</script>\n";

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
    document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($cnt); ?>건 완료<br><br><font color='crimson'><b>[끝]</b></font>";
</script>
<?php
}
?>