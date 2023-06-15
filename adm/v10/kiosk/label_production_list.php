<?php
include_once('./_common.php');
include_once('./_head.php');

$shif_date = statics_date(G5_TIME_YMDHIS);
// $shif_date = statics_date('2023-06-05 09:15:20');

$sql = " SELECT prd.prd_idx
            , prd.prd_start_date
            , pri.pri_idx
            , pri.bom_idx
            , bom.bom_part_no
            , bom.bom_name
            , bom.cst_idx_customer
            , bom.bom_type
            , bom.bom_ship_count
            , bom.bom_delivery_check_yn
            , cst.cst_name
            , pri_value
            , pri.mms_idx
            , mms.mms_name
            , pri.mb_id
            , pri_memo
        FROM {$g5['production_item_table']} pri
            LEFT JOIN {$g5['mms_table']} mms ON pri.mms_idx = mms.mms_idx
            LEFT JOIN {$g5['production_table']} prd ON pri.prd_idx = prd.prd_idx
            LEFT JOIN {$g5['bom_table']} bom ON pri.bom_idx = bom.bom_idx
            LEFT JOIN {$g5['customer_table']} cst ON bom.cst_idx_customer = cst.cst_idx
            LEFT JOIN {$g5['member_table']} mb ON pri.mb_id = mb.mb_id
        WHERE pri.mb_id = '{$member['mb_id']}'
            AND bom.bom_type = 'product'
            AND prd.prd_start_date = '{$shif_date}'
            AND prd.prd_status = 'confirm'
";
$result = sql_query($sql,1);
?>
<style>
  
</style>
<div id="main" class="<?=$main_type_class?>">
<form name="form01" id="form01" action="./label_production_form.php" onsubmit="return form01_submit(this);" method="post">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">
<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
<input type="hidden" name="mb_name" value="<?=$member['mb_name']?>">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">선택</th>
        <th scope="col" style="min-width:60px;">생산계획ID</th>
        <th scope="col">품번</th>
        <th scope="col">적재수량</th>
        <th scope="col">품명</th>
        <th scope="col">타입</th>
        <th scope="col">생산시작일</th>
        <th scope="col">지시수량</th>
        <th scope="col">재고</th>
        <th scope="col">파레트수량</th>
        <th scope="col">재발행</th>
    </tr>
    </thead>
    <tbody>
    <?php for($i=0;$row=sql_fetch_array($result);$i++){ 

        $bg = 'bg'.($i%2);
        
        // 완제품에 대한 재고수량만 가지고 옴
        $sql2 = " SELECT SUM(itm_value) AS itm_total
                FROM {$g5['item_table']}
                WHERE bom_idx = '{$row['bom_idx']}' 
                    AND com_idx = '{$_SESSION['ss_com_idx']}'
                    AND plt_idx = '0'
                    AND itm_status IN ('finish','check')
        ";
        // echo $sql2;
        $pri = sql_fetch($sql2,1);
        // print_r3($pri);
        $row['itm_total'] = $pri['itm_total'];

        // $row['bom_ship_count'] = ($row['bom_ship_count'] > $row['itm_total']) ? $row['itm_total'] : $row['bom_ship_count'];

        // 해당 prd_idx와 pri_idx를 담고 있는 plt_idx의 갯수를 조회
        $sql3 = " SELECT COUNT(DISTINCT plt_idx) AS plt_count
            FROM {$g5['pallet_table']} plt
            WHERE EXISTS (
                SELECT * FROM {$g5['item_table']} itm
                    WHERE bom_idx = '{$row['bom_idx']}'
                        AND itm_status IN ('finish','check','delivery')
                        AND itm.plt_idx != '0'
                        AND mb_id_worker = '{$member['mb_id']}'
                        AND plt.plt_idx = itm.plt_idx
            )
            AND plt_reg_dt >= '".statics_date(G5_TIME_YMDHIS)." 00:00:00'
        ";
        // echo $sql3;
        $itm = sql_fetch($sql3,1);
        // print_r3($itm);
        $row['plt_count'] = $itm['plt_count'];


    ?>
    <tr class="<?php echo $bg; ?>" tr_id="<?php echo $row['prd_idx'] ?>">
        <td class="td_chk">
            <input type="checkbox" class="inp_chk" name="chk[]" value="<?=$i?>" id="chk_<?php echo $i ?>" stock="<?=$row['itm_total']?>" plt_cnt="<?=$row['plt_count']?>">
            <label for="chk_<?php echo $i ?>" class="chk"></label>
            <input type="hidden" name="prd_idx[<?=$i?>]" value="<?=$row['prd_idx']?>">
            <input type="hidden" name="prd_start_date[<?=$i?>]" value="<?=$row['prd_start_date']?>">
            <input type="hidden" name="cst_idxs[<?=$i?>]" value="<?=$row['cst_idx_customer']?>">
            <input type="hidden" name="bom_idx[<?=$i?>]" value="<?=$row['bom_idx']?>">
            <input type="hidden" name="bom_name[<?=$i?>]" value="<?=$row['bom_name']?>">
            <input type="hidden" name="bom_part_no[<?=$i?>]" value="<?=$row['bom_part_no']?>">
            <input type="hidden" name="bom_delivery_check_yn[<?=$i?>]" value="<?=$row['bom_delivery_check_yn']?>">
            <input type="hidden" name="mms_idx[<?=$i?>]" value="<?=$row['mms_idx']?>">
            <input type="hidden" name="mms_name[<?=$i?>]" value="<?=$row['mms_name']?>">
        </td>
        <td class="td_prd_id"><?=$row['prd_idx']?></td><!-- 생산계획ID -->
        <td class="td_prd_bom_part_no"><?=$row['bom_part_no']?></td><!-- 품번 -->
        <td class="td_plt_in_cnt">
            <input type="text" id="plt_in_cnt_<?=$i?>" name="plt_in_cnt[<?=$i?>]" readonly value="<?=$row['bom_ship_count']?>" class="frm_input input_cnt" size="10" maxlength="10" placeholder="적재수량">
        </td>
        <td class="td_prd_bom_name font_size_7"><?=$row['bom_name']?></td><!-- 품명 -->
        <td class="td_prd_bom_type"><?=$g5['set_bom_type_value'][$row['bom_type']]?></td><!-- 품명 -->
        <td class="td_prd_start_date"><?=$row['prd_start_date']?></td><!-- 시작일 -->
        <td class="td_prd_count"><?=number_format($row['pri_value'])?></td><!-- 지시수량 -->
        <td class="td_prd_stock"><?=number_format($row['itm_total'])?></td><!-- 재고 -->
        <td class="td_plt_count"><?=number_format($row['plt_count'])?></td><!-- 파레트수량 -->
        <td class="td_plt_reoutput">
            <?php if($row['plt_count']){ ?>
            <a href="<?=G5_USER_ADMIN_KIOSK_URL?>/label_production_pallet_list.php?bom_idx=<?=$row['bom_idx']?>" class="btn btn04 btn_reoutput">상세</a>
            <?php } ?>
        </td><!-- 재발행 -->
    </tr>
    <?php 
    }
    if($i == 0)
        echo "<tr><td colspan='11' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div><!--.tbl_head01-->
<div class="btn_fixed_top">
    <input type="submit" name="act_button" value="선택라벨출력" onclick="document.pressed=this.value" class="btn btn05 btn_label">
</div>
</form><!--#form01-->
</div><!--#main-->
<script>
$('.input_cnt').on('click',function(){
    $('.input_cnt').removeClass('input_cnt_on');
    $(this).addClass('input_cnt_on');
    $('#mdl_num').css('display','flex');
    $('#input_num').val($(this).val());  
    $('#input_num').focus();
});

function form01_submit(f){
    $submit_flag = true;
    if(!is_checked("chk[]")){
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        $submit_flag = false;
        return false;
    }

    $('.inp_chk:checked').each(function(){
        // if($(this).attr('plt_cnt') != '0'){
        //     alert('파레트수량이 존재하는 제품은 새롭게 라벨을 출력할 수 없습니다.\n상세페이지에서 재출력은 가능합니다.');
        //     $submit_flag = false;
        //     return false;
        // }
        if($(this).attr('stock') == '0' || $(this).attr('stock') == ''){
            alert('재고가 없는 제품은 라벨을 출력할 수 없습니다.');
            $submit_flag = false;
            return false;
        }

        if(!$('#plt_in_cnt_'+$(this).val()).val()){
            alert('적재수량을 입력해주세요.');
            $('#plt_in_cnt_'+$(this).val()).focus();
            $submit_flag = false;
            return false;
        }
        else{
            var stock = Number($(this).attr('stock'));
            var cnt = Number($('#plt_in_cnt_'+$(this).val()).val());
            if(cnt > stock){
                alert('적재수량이 재고량을 초과할 수는 없습니다.');
                $('#plt_in_cnt_'+$(this).val()).focus();
                $submit_flag = false;
                return false;
            }
        }
    });

    //

    return $submit_flag;
}
</script>
<?php
include_once('./_tail.php');