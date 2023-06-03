<?php
include_once('./_common.php');
// define('_INDEX_', true);

$g5['title'] = 'ITEM&SUM등록';
include_once(G5_PATH.'/head.sub.php');
echo '<h2 style="line-height:2em;">'.$g5['title'].'</h2>';
?>
<link rel="stylesheet" href="<?=G5_URL?>/_make_data/_css/common.css">
<?php
include_once(G5_PATH.'/_make_data/head_menu.php');

$start_btn = '<div class="top_box"><a href="'.$_SERVER['SCRIPT_NAME'].'?start=1" class="btn bg_primary">시작</a></div>';
echo $start_btn;
if($start){
    $truncate_sql = " TRUNCATE {$g5['item_table']} ";
    sql_query($truncate_sql,1);
    $truncate_sum_sql = " TRUNCATE {$g5['item_sum_table']} ";
    sql_query($truncate_sum_sql,1);
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

//초기 데이터 설정 작업
$prd_sql = " SELECT prd.prd_idx
                , prd.com_idx
                , prd.bom_idx
                , bom.cst_idx_provider
                , bom.cst_idx_customer
                , bom.bom_name
                , bom.bom_part_no
                , bom_price
                , ori.ori_idx
                , prd_start_date
                , pri.pri_idx
                , pri.mms_idx
                , pri.pri_value
        FROM {$g5['production_table']} prd
        LEFT JOIN {$g5['order_item_table']} ori ON prd.ori_idx = ori.ori_idx
        INNER JOIN {$g5['production_item_table']} pri ON prd.prd_idx = pri.prd_idx AND prd.bom_idx = pri.bom_idx
        LEFT JOIN {$g5['bom_table']} bom ON prd.bom_idx = bom.bom_idx
            WHERE prd_status != 'trash'
                AND prd.com_idx = '{$_SESSION['ss_com_idx']}'
                AND bom.bom_type = 'product'
        ORDER BY prd.prd_start_date
";

$res = sql_query($prd_sql, 1);
$bom_arr = array();
for($i=0;$row=sql_fetch_array($res);$i++){
    // print_r2($row);
    //완제품 정보를 미리 셋팅해 둔다.
    array_push($bom_arr,array(
        'bom_idx'=>$row['bom_idx']
        ,'cst_idx_provider'=>$row['cst_idx_provider']
        ,'cst_idx_customer'=>$row['cst_idx_customer']
        ,'bom_name'=>$row['bom_name']
        ,'bom_part_no'=>$row['bom_part_no']
        ,'bit_count'=>'1'
        ,'bit_num'=>'0'
        ,'bit_reply'=>'0'
        ,'prd_start_date'=>$row['prd_start_date']
        ,'ori_idx'=>$row['ori_idx']
        ,'pri_idx'=>$row['pri_idx']
        ,'mms_idx'=>$row['mms_idx']
        ,'bom_price'=>$row['bom_price']
        ,'bom_type'=>'product'
        ,'mb_cnt'=>count($g5['mmw_arr'][$row['mms_idx']])
        ,'pri_total'=>$row['pri_value']
        ,'pri_value'=>ceil($row['pri_value']/count($g5['mmw_arr'][$row['mms_idx']]))
        ,'mb_ids'=>$g5['mmw_arr'][$row['mms_idx']]
    ));
    
    //서브제품군을 셋팅한다.
    $sql1 = " SELECT bom.bom_idx
                    , bom.cst_idx_provider
                    , bom.cst_idx_customer
                    , bom.bom_part_no
                    , bom.bom_name
                    , boi.bit_count
                    , boi.bit_num
                    , boi.bit_reply
                    , pri.pri_idx
                    , pri.mms_idx
                    , pri.pri_value
                    , bom_price
                    , bom.bom_type
                FROM {$g5['bom_item_table']} boi
                    LEFT JOIN {$g5['bom_table']} bom ON boi.bom_idx_child = bom.bom_idx
                    LEFT JOIN {$g5['customer_table']} cst ON cst.cst_idx = bom.cst_idx_provider
                    LEFT JOIN {$g5['production_item_table']} pri ON bom.bom_idx = pri.bom_idx
                WHERE boi.bom_idx = '{$row['bom_idx']}'
                    AND pri.prd_idx = '{$row['prd_idx']}'
                    AND pri.pri_status != 'trash'
                ORDER BY boi.bit_num DESC, boi.bit_reply
    ";
    $sres = sql_query($sql1,1);
    if($sres->num_rows){
        for($j=0;$srow=sql_fetch_array($sres);$j++){
            if($srow['bit_reply'] == '') $srow['bit_reply'] = '1';
            $srow['prd_start_date'] = $row['prd_start_date'];
            $srow['ori_idx'] = $row['ori_idx'];
            $srow['mb_cnt'] = count($g5['mmw_arr'][$srow['mms_idx']]);
            $srow['pri_total'] = $srow['pri_value'];
            $srow['pri_value'] = ceil($srow['pri_value'] / count($g5['mmw_arr'][$srow['mms_idx']]));
            $srow['mb_ids'] = $g5['mmw_arr'][$srow['mms_idx']];
            array_push($bom_arr,$srow);
        }
    }
}
// print_r2($bom_arr);exit;
$cnt = 0;
$result = count($bom_arr);
for($i=0;$i<$result;$i++){
    $cnt++;
    
    //부모 루프 개별 데이터 설정 작업
    
    $sub_cnt = 0;
    $sub_result = count($bom_arr[$i]['mb_ids']);
    
    foreach($bom_arr[$i]['mb_ids'] as $k=>$v){
        $sub_cnt++; 

        $sqls = " INSERT INTO {$g5['item_sum_table']} SET
            com_idx = '{$_SESSION['ss_com_idx']}'
            , cst_idx_provider = '{$bom_arr[$i]['cst_idx_provider']}'
            , cst_idx_customer = '{$bom_arr[$i]['cst_idx_customer']}'
            , mms_idx = '{$bom_arr[$i]['mms_idx']}'
            , shf_idx = '0'
            , mb_id = '{$k}'
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
        for($j=0;$j<$bom_arr[$i]['pri_value'];$j++){
            $dt = $dt + ($cnt * 10) + $sub_cnt + $j;
            $datetime = date('Y-m-d H:i:s',$dt);
            $sql = " INSERT INTO {$g5['item_table']} SET
                com_idx = '{$_SESSION['ss_com_idx']}'
                , cst_idx_provider = '{$bom_arr[$i]['cst_idx_provider']}'
                , cst_idx_customer = '{$bom_arr[$i]['cst_idx_customer']}'
                , mms_idx = '{$bom_arr[$i]['mms_idx']}'
                , ori_idx = '{$bom_arr[$i]['ori_idx']}'
                , pri_idx = '{$bom_arr[$i]['pri_idx']}'
                , bom_idx = '{$bom_arr[$i]['bom_idx']}'
                , shf_idx = '{$bom_arr[$i]['shf_idx']}'
                , mb_id = '{$k}'
                , itm_part_no = '{$bom_arr[$i]['bom_part_no']}'
                , itm_name = '{$bom_arr[$i]['bom_name']}'
                , itm_type = '{$bom_arr[$i]['bom_type']}'
                , itm_value = '1'
                , itm_price = '{$bom_arr[$i]['bom_price']}'
                , itm_status = 'finish'
                , itm_date = '{$bom_arr[$i]['prd_start_date']}'
                , itm_reg_dt = '{$datetime}'
                , itm_update_dt = '{$datetime}'
            ";
            sql_query($sql,1);

            $c = $cnt + $sub_cnt + ($j+1);
            echo "<script>document.all.cont.innerHTML += '".$c." - 처리됨<br>';</script>\n";

            flush();
            ob_flush();
            ob_end_flush();
            usleep($sleepsec);
        
            //보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
            if($c % $countgap == 0){
                echo "<script>document.all.cont.innerHTML += '<br>';</script>\n";
            }
        
            //화면 정리! 부하를 줄임 (화면을 싹 지움)
            if($c % $maxscreen == 0){
                echo "<script>document.all.cont.innerHTML = '';</script>\n";
            }

        }

    }
}
?>
<script>
    document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($c); ?>건 완료<br><br><font color='crimson'><b>[끝]</b></font>";
</script>
<?php
}
?>