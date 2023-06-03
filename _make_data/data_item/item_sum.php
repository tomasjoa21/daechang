<?php
include_once('./_common.php');
// define('_INDEX_', true);

$g5['title'] = 'ITEM&SUM등록';
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

$action_url = G5_URL.'/_make_data/data_item/item_sum.php?start=1';
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

//초기 완제품 데이터 설정 작업(완제품을 기준으로 모든 서브의 재고갯수를 추적할것임)
$prd_sql = " SELECT prd.prd_idx
                , prd.com_idx
                , prd.bom_idx
                , bom.cst_idx_provider
                , bom.cst_idx_customer
                , bom.bom_name
                , bom.bom_part_no
                , bom_price
                , prd.ori_idx
                , prd_start_date
                , GROUP_CONCAT(pri.pri_idx) AS pri_idxs
                , GROUP_CONCAT(pri.mms_idx) AS mms_idxs
                , GROUP_CONCAT(pri.mb_id) AS mb_ids
                , SUM(pri.pri_value) AS pri_value 
                , bom.bom_type
        FROM {$g5['production_table']} prd
            LEFT JOIN {$g5['production_item_table']} pri ON prd.prd_idx = pri.prd_idx 
                                                            AND prd.bom_idx = pri.bom_idx
            LEFT JOIN {$g5['bom_table']} bom ON prd.bom_idx = bom.bom_idx
        WHERE prd_status != 'trash'
            AND pri.pri_idx IS NOT NULL
            AND prd.com_idx = '{$_SESSION['ss_com_idx']}'
            {$where_date}
            AND bom.bom_type = 'product'
        GROUP BY prd.prd_idx, pri.bom_idx
        ORDER BY prd.prd_start_date
";
// echo $prd_sql;exit;
$res = sql_query($prd_sql, 1);
$bom_arr = array();
for($i=0;$row=sql_fetch_array($res);$i++){
    $row['pri_arr'] = ($row['pri_idxs']) ? explode(',',$row['pri_idxs']) : array();
    $row['mms_arr'] = ($row['mms_idxs']) ? explode(',',$row['mms_idxs']) : array();
    $row['mb_arr'] = ($row['mb_ids']) ? explode(',',$row['mb_ids']) : array();
    //완제품 정보를 미리 셋팅해 둔다.
    array_push($bom_arr,array(
        'bom_idx'=>$row['bom_idx']
        ,'cst_idx_provider'=>$row['cst_idx_provider']
        ,'cst_idx_customer'=>$row['cst_idx_customer']
        ,'bom_name'=>$row['bom_name']
        ,'bom_part_no'=>$row['bom_part_no']
        ,'bit_count'=>'1'
        ,'bit_reply'=>'0'
        ,'prd_start_date'=>$row['prd_start_date']
        ,'ori_idx'=>$row['ori_idx']
        ,'prd_idx'=>$row['prd_idx']
        ,'pri_idxs'=>$row['pri_arr']
        ,'mms_idxs'=>$row['mms_arr']
        ,'mb_cnt'=>@count($row['mb_arr'])
        ,'mb_ids'=>$row['mb_arr']
        ,'bom_price'=>$row['bom_price']
        ,'bom_type'=>'product'
        ,'pri_total'=>$row['pri_value']
        ,'pri_value'=>(@count($row['mb_arr']))?ceil($row['pri_value']/@count($row['mb_arr'])):$row['pri_value']
    ));

    //서브제품군을 셋팅한다.
    $sql1 = " SELECT bom_idx
                    , cst_idx_provider
                    , bom_part_no
                    , bom_name
                    , bit_count
                    , bit_reply
                    , bom_price
                    , bom_type
                    , (bit_count * {$row['pri_value']}) AS pri_value
                FROM {$g5['v_bom_item_table']} boi
                WHERE bom_idx_product = '{$row['bom_idx']}'
    ";
    $sres = sql_query($sql1,1);
    if($sres->num_rows){
        for($j=0;$srow=sql_fetch_array($sres);$j++){
            $psql = " SELECT prd_idx
                        , pri.bom_idx
                        , GROUP_CONCAT(pri.pri_idx) AS pri_idxs
                        , GROUP_CONCAT(pri.mms_idx) AS mms_idxs
                        , GROUP_CONCAT(pri.mb_id) AS mb_ids
                        , SUM(pri.pri_value) AS pri_value 
                     FROM {$g5['production_item_table']} pri
                        LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
                    WHERE bom_type != 'product'
                        AND prd_idx = '{$row['prd_idx']}'
                        AND pri.bom_idx = '{$srow['bom_idx']}'
                        AND pri_status != 'trash'
                    GROUP BY pri.bom_idx
            ";
            //prd_idx와 bom_idx 조건으로 추려서 bom_idx로 그룹바이 했기에 있다면 1개 레코드만 추출된다.
            $prow = sql_fetch($psql,1);
            $prow['pri_arr'] = ($prow['pri_idxs']) ? explode(',',$prow['pri_idxs']) : array();
            $prow['mms_arr'] = ($prow['mms_idxs']) ? explode(',',$prow['mms_idxs']) : array();
            $prow['mb_arr'] = ($prow['mb_ids']) ? explode(',',$prow['mb_ids']) : array();

            $srow['cst_idx_customer'] = 0;
            $srow['prd_start_date'] = $row['prd_start_date'];
            $srow['ori_idx'] = $row['ori_idx'];
            $srow['prd_idx'] = $row['prd_idx'];
            $srow['pri_idxs'] = $prow['pri_arr'];
            $srow['mms_idxs'] = $prow['mms_arr'];
            $srow['mb_cnt'] = @count($prow['mb_arr']);
            $srow['mb_ids'] = $prow['mb_arr'];
            $srow['pri_total'] = $srow['pri_value'];
            $srow['pri_value'] = (@count($prow['mb_arr']))?ceil($srow['pri_value'] / @count($prow['mb_arr'])):$srow['pri_value'];
            array_push($bom_arr,$srow);
        }
    }
}
// print_r2($bom_arr);
$cnt = 0;
$result = count($bom_arr);
for($i=0;$i<$result;$i++){
    $cnt++;

    $sub_cnt = 0;
    $mb_result = count($bom_arr[$i]['mb_ids']);
    //작업자가 있으면
    if($mb_result){
        //작업자별로 등록
        foreach($bom_arr[$i]['mb_ids'] as $k => $v){
            $sqls = " INSERT INTO {$g5['item_sum_table']} SET
                com_idx = '{$_SESSION['ss_com_idx']}'
                , cst_idx_provider = '{$bom_arr[$i]['cst_idx_provider']}'
                , mms_idx = '{$bom_arr[$i]['mms_idxs'][$k]}'
                , shf_idx = '0'
                , mb_id = '{$v}'
                , bom_idx = '{$bom_arr[$i]['bom_idx']}'
                , bom_part_no = '{$bom_arr[$i]['bom_part_no']}'
                , its_price = '{$bom_arr[$i]['bom_price']}'
                , its_value = '{$bom_arr[$i]['pri_value']}'
                , its_type = '{$bom_arr[$i]['bom_type']}'
                , its_status = 'finish'
                , its_date = '{$bom_arr[$i]['prd_start_date']}'
            ";
            sql_query($sqls,1);

            $dt = strtotime($bom_arr[$i]['prd_start_date']);
            //완제품일때는 item테이블에 등록
            if($bom_arr[$i]['bom_type'] == 'product'){
                $sql = " INSERT INTO {$g5['item_table']}
                    (com_idx, cst_idx_provider, mms_idx, ori_idx, prd_idx, pri_idx, bom_idx, shf_idx, mb_id, itm_part_no, itm_name, itm_type, itm_value, itm_price, itm_status, itm_date, itm_reg_dt, itm_update_dt) VALUES
                ";
                for($j=0;$j<$bom_arr[$i]['pri_value'];$j++){
                    $dt = $dt + $cnt + $k + $j + 10;
                    $datetime = date('Y-m-d H:i:s',$dt);
                    $sql .= ($j > 0) ? ',':'';
                    $sql .= " ('{$_SESSION['ss_com_idx']}'
                        , '{$bom_arr[$i]['cst_idx_provider']}'
                        , '{$bom_arr[$i]['mms_ids'][$k]}'
                        , '{$bom_arr[$i]['ori_idx']}'
                        , '{$bom_arr[$i]['prd_idx']}'
                        , '{$bom_arr[$i]['pri_idxs'][$k]}'
                        , '{$bom_arr[$i]['bom_idx']}'
                        , '{$bom_arr[$i]['shf_idx']}'
                        , '{$v}'
                        , '{$bom_arr[$i]['bom_part_no']}'
                        , '{$bom_arr[$i]['bom_name']}'
                        , '{$bom_arr[$i]['bom_type']}'
                        , '1'
                        , '{$bom_arr[$i]['bom_price']}'
                        , 'finish'
                        , '{$bom_arr[$i]['prd_start_date']}'
                        , '{$datetime}'
                        , '{$datetime}')
                    ";
                }
                sql_query($sql,1);
            }
            //반제품일때는 material테이블에 등록
            else if($bom_arr[$i]['bom_type'] == 'half'){
                $sql = " INSERT INTO {$g5['material_table']}
                    (com_idx, cst_idx_provider, mms_idx, ori_idx, prd_idx, pri_idx, bom_idx, shf_idx, mb_id, mtr_part_no, mtr_name, mtr_type, mtr_value, mtr_price, mtr_status, mtr_date, mtr_reg_dt, mtr_update_dt) VALUES
                ";
                for($j=0;$j<$bom_arr[$i]['pri_value'];$j++){
                    $dt = $dt + $cnt + $k + $j + 10;
                    $datetime = date('Y-m-d H:i:s',$dt);
                    $sql .= ($j > 0) ? ',':'';
                    $sql .= " ('{$_SESSION['ss_com_idx']}'
                        , '{$bom_arr[$i]['cst_idx_provider']}'
                        , '{$bom_arr[$i]['mms_ids'][$k]}'
                        , '{$bom_arr[$i]['ori_idx']}'
                        , '{$bom_arr[$i]['prd_idx']}'
                        , '{$bom_arr[$i]['pri_idxs'][$k]}'
                        , '{$bom_arr[$i]['bom_idx']}'
                        , '{$bom_arr[$i]['shf_idx']}'
                        , '{$v}'
                        , '{$bom_arr[$i]['bom_part_no']}'
                        , '{$bom_arr[$i]['bom_name']}'
                        , '{$bom_arr[$i]['bom_type']}'
                        , '1'
                        , '{$bom_arr[$i]['bom_price']}'
                        , 'finish'
                        , '{$bom_arr[$i]['prd_start_date']}'
                        , '{$datetime}'
                        , '{$datetime}')
                    ";
                }
                sql_query($sql,1);
            }
            //자재일때는 material테이블에 업데이트 mtr_status = used
            else{
                $dt = $dt + $cnt + $k + 10;
                $datetime = date('Y-m-d H:i:s',$dt);
                $sql = " UPDATE {$g5['material_table']} SET
                        mtr_status = 'used'
                        , mtr_status = CONCAT(mtr_history,'\nused|{$datetime}
                    WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                        AND bom_idx = '{$bom_arr[$i]['bom_idx']}'
                        AND mtr_status = 'ok'
                    ORDER BY mtr_idx
                    LIMIT {$bom_arr[$i]['pri_value']}
                ";
                sql_query($sql,1);
            }
        }
    }
    //작업자가 없으면
    else{
        $dt = strtotime($bom_arr[$i]['prd_start_date']);
        //완제품일때는 item테이블에 등록
        if($bom_arr[$i]['bom_type'] == 'product'){
            $sql = " INSERT INTO {$g5['item_table']}
                (com_idx, cst_idx_provider, ori_idx, prd_idx, bom_idx, shf_idx, itm_part_no, itm_name, itm_type, itm_value, itm_price, itm_status, itm_date, itm_reg_dt, itm_update_dt) VALUES
            ";
            for($j=0;$j<$bom_arr[$i]['pri_value'];$j++){
                $dt = $dt + $cnt + $j + 10;
                $datetime = date('Y-m-d H:i:s',$dt);
                $sql .= ($j > 0) ? ',':'';
                $sql .= " ('{$_SESSION['ss_com_idx']}'
                    , '{$bom_arr[$i]['cst_idx_provider']}'
                    , '{$bom_arr[$i]['ori_idx']}'
                    , '{$bom_arr[$i]['prd_idx']}'
                    , '{$bom_arr[$i]['bom_idx']}'
                    , '{$bom_arr[$i]['shf_idx']}'
                    , '{$bom_arr[$i]['bom_part_no']}'
                    , '{$bom_arr[$i]['bom_name']}'
                    , '{$bom_arr[$i]['bom_type']}'
                    , '1'
                    , '{$bom_arr[$i]['bom_price']}'
                    , 'finish'
                    , '{$bom_arr[$i]['prd_start_date']}'
                    , '{$datetime}'
                    , '{$datetime}')
                ";
            }
            sql_query($sql,1);
        }
        //반제품일때는 material테이블에 등록
        else if($bom_arr[$i]['bom_type'] == 'half'){
            $sql = " INSERT INTO {$g5['material_table']}
                (com_idx, cst_idx_provider, ori_idx, prd_idx, bom_idx, shf_idx, mtr_part_no, mtr_name, mtr_type, mtr_value, mtr_price, mtr_status, mtr_date, mtr_reg_dt, mtr_update_dt) VALUES
            ";
            for($j=0;$j<$bom_arr[$i]['pri_value'];$j++){
                $dt = $dt + $cnt + $j + 10;
                $datetime = date('Y-m-d H:i:s',$dt);
                $sql .= ($j > 0) ? ',':'';
                $sql .= " ('{$_SESSION['ss_com_idx']}'
                    , '{$bom_arr[$i]['cst_idx_provider']}'
                    , '{$bom_arr[$i]['ori_idx']}'
                    , '{$bom_arr[$i]['prd_idx']}'
                    , '{$bom_arr[$i]['bom_idx']}'
                    , '{$bom_arr[$i]['shf_idx']}'
                    , '{$bom_arr[$i]['bom_part_no']}'
                    , '{$bom_arr[$i]['bom_name']}'
                    , '{$bom_arr[$i]['bom_type']}'
                    , '1'
                    , '{$bom_arr[$i]['bom_price']}'
                    , 'finish'
                    , '{$bom_arr[$i]['prd_start_date']}'
                    , '{$datetime}'
                    , '{$datetime}')
                ";
            }
            sql_query($sql,1);
        }
        //자재일때는 material테이블에 업데이트 mtr_status = used
        else{
            $dt = $dt + $cnt + $k + 10;
            $datetime = date('Y-m-d H:i:s',$dt);
            $sql = " UPDATE {$g5['material_table']} SET
                    mtr_status = 'used'
                    , mtr_status = CONCAT(mtr_history,'\nused|{$datetime}')
                WHERE com_idx = '{$_SESSION['ss_com_idx']}'
                    AND bom_idx = '{$bom_arr[$i]['bom_idx']}'
                    AND mtr_status = 'ok'
                ORDER BY mtr_idx
                LIMIT {$bom_arr[$i]['pri_value']}
            ";
            sql_query($sql,1);
        }
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