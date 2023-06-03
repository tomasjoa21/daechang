<?php
include_once('./_common.php');
// define('_INDEX_', true);

$g5['title'] = '빠레트등록';
include_once(G5_PATH.'/head.sub.php');
echo '<h2 style="line-height:2em;">'.$g5['title'].'</h2>';
?>
<link rel="stylesheet" href="<?=G5_URL?>/_make_data/_css/common.css">
<style>
.prd_start_date{width:80px;}
.prd_done_date{width:80px;}
</style>
<?php
include_once(G5_PATH.'/_make_data/head_menu.php');

$action_url = G5_URL.'/_make_data/data_pallet/pallet.php?start=1';
$start_tag = '<p>입력하신 생산시작일을 기준으로 데이터를 생성합니다.<br>반드시 적어도 1개 이상의 기준 날짜범위를 입력하고 [시작]버튼을 누르세요.</p>';
$start_tag .= '<br><input type="text" readonly name="prd_start_date" value="'.$prd_start_date.'" class="frm_inpu prd_start_date">'.PHP_EOL;
$start_tag .= ' ~ <input type="text" readonly name="prd_done_date" value="'.$prd_done_date.'" class="frm_inpu prd_done_date">'.PHP_EOL;
$start_tag .= '<div class="top_box"><a href="javascript:submit();" class="btn bg_primary">시작</a></div>';
echo $start_tag;
?>
<script>
var action_url = '<?=$action_url?>';
$("input[name=prd_start_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect: function(selectedDate){$("input[name=prd_done_date]").datepicker('option','minDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}} });
$("input[name=prd_done_date]").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", onSelect:function(selectedDate){$("input[name=prd_start_date]").datepicker('option','maxDate',selectedDate);},closeText:'취소', onClose: function(){ if($(window.event.srcElement).hasClass('ui-datepicker-close')){ $(this).val('');}}});


function submit(){
    var prd_start_date = $('.prd_start_date').val();
    var prd_done_date = $('.prd_done_date').val();
    var start_date = new Date(prd_start_date);
    var done_date = new Date(prd_done_date);

    if(!prd_start_date && !prd_done_date){
        alert('데이터 생성의 기준이 되는 생산시작일을\n적어도 1개이상 입력해 주세요.');
        return false;
    }
    else if(prd_start_date && prd_done_date){
        if(start_date.getTime() > done_date.getTime()){
            alert('시작날짜보다 종료날짜가 이전날짜가 되면 안됩니다\n다시 날짜설정을 해 주세요.');
        }
    }

    action_url += '&prd_start_date=' + prd_start_date + '&prd_done_date=' + prd_done_date;
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

$where_date = '';
if($prd_start_date && !$prd_done_date){
    $where_date .= " AND prd_start_date >= '{$prd_start_date}' ";
}
else if(!$prd_start_date && $prd_done_date){
    $where_date .= " AND prd_start_date <= '{$prd_done_date}' ";
}
else if($prd_start_date && $prd_done_date){
    if($prd_start_date == $prd_done_date){
        $where_date .= " AND prd_start_date = '{$prd_start_date}' ";
    }
    else{
        $where_date .= " AND prd_start_date >= '{$prd_start_date}' ";
        $where_date .= " AND prd_start_date <= '{$prd_done_date}' ";
    }
}

$prd_sql = " SELECT pri.bom_idx
                , pri.prd_idx
                , pri_idx
                , prd_start_date
                , bom.bom_ship_count  
            FROM {$g5['production_table']} prd
                INNER JOIN {$g5['production_item_table']} pri ON prd.prd_idx = pri.prd_idx 
                                                            AND prd.bom_idx = pri.bom_idx
                INNER JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
            WHERE bom_type = 'product'
                {$where_date}
                AND pri_status != 'trash'
";
// echo $prd_sql;exit;
$cnt = 0;
$res = sql_query($prd_sql,1);
for($i=0;$row=sql_fetch_array($res);$i++){
    $cnt++;

    $itm_chk = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$g5['item_table']}
        WHERE com_idx = '{$_SESSION['ss_com_idx']}'
            AND prd_idx = '{$row['prd_idx']}'
            AND bom_idx = '{$row['bom_idx']}'
            AND itm_status NOT IN ('trash','ing','delivery','defect','scrap')
    ");
    $p_cnt = ceil($itm_chk['cnt'] / $row['bom_ship_count']);
    for($j=0;$j<$p_cnt;$j++){
        $plt_sql = " INSERT INTO {$g5['pallet_table']}
                SET com_idx = '{$_SESSION['ss_com_idx']}'
                    , plt_status = 'ok'
                    , plt_reg_dt = '{$row['prd_start_date']} 19:10:10'
                    , plt_update_dt = '{$row['prd_start_date']} 19:10:10'
        ";
        sql_query($plt_sql,1);
        $plt_idx = sql_insert_id();

        $itm_sql = " UPDATE {$g5['item_table']}
                SET plt_idx = '{$plt_idx}'
            WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                AND prd_idx = '{$row['prd_idx']}'
                AND bom_idx = '{$row['bom_idx']}'
                AND plt_idx = '0'
                AND itm_status NOT IN ('trash','ing','delivery','defect','scrap')
            ORDER BY itm_idx
            LIMIT {$row['bom_ship_count']}
        ";
        sql_query($itm_sql,1);
    }

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