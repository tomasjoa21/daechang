<?php
include_once('./_common.php');
include_once('./_head.php');

$sql_common = " FROM {$g5['pallet_table']} ";
$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " mb_id_delivery = '{$member['mb_id']}' ";
$where[] = " plt_date = '".G5_TIME_YMD."' ";
$where[] = " plt_status = 'delivery' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'plt_idx') :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
        default :
			$where[] = " $sfl LIKE '%".trim($stx)."%' ";
            break;
    }
}

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "plt_update_dt";
    $sod = "desc";
}


$sql_order = " ORDER BY {$sst} {$sod} ";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT plt_idx
                , mb_id_delivery
                , plt_date
                , plt_reg_dt
                , plt_update_dt
                , plt_status
        {$sql_common} {$sql_search} {$sql_order}
        LIMIT {$from_record}, {$rows}
";
// print_r3($sql);//exit;
$result = sql_query($sql,1);

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
$qstr .= '&sca='.$sca.'&ser_cod_type='.$ser_cod_type; // 추가로 확장해서 넘겨야 할 변수들
?>
<div id="main" class="<?=$main_type_class?>">

    <div id="inp_box">
        <span id="qr_ttl">출하처리 스캔을 시작하세요.</span><br>
        <p id="qr_desc">반드시 본인 ID로 로그인 되어 있는지 확인해 주세요.<br>스캔이 안되면 하얀색 사각형 박스를 터치 또는 클릭해 주세요.<br>출하스캔작업을 완료했으면 반드시 홈으로 나가 주세요.</p>
        <input type="text" name="qr_scan" value="" id="qr_scan" class="frm_input" style="width:150px;"><br>
        <strong id="qr_status"></strong>
    </div>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
            <tr>
                <th scope="col">파레트ID</th>
                <th scope="col">제품정보</th>
                <th scope="col">배송기사</th>
                <th scope="col">적재수량</th>
                <th scope="col">취소</th>
            </tr>
        </thead>
        <tbody>
        <?php for($i=0;$row=sql_fetch_array($result);$i++){ 
            $bg = 'bg'.($i%2);
        ?>
        <tr class="<?php echo $bg; ?>" tr_id="<?=$row['plt_idx']?>">
            <td class="td_plt_idx"><?=$row['plt_idx']?></td>
            <td class="td_bom_info"><?=$row['bom_idx']?></td>
            <td class="td_mb_id_delivery"><?=$row['mb_id_delivery']?></td>
            <td class="td_plt_count"><?=$row['plt_count']?></td>
            <td class="td_cancel">
                <a href="" class="btn btn04 dlv_cancel">취소</a>
            </td>
        </tr>
        <?php }
        if($i == 0)
            echo "<tr><td colspan='5' class=\"empty_table\">자료가 없습니다.</td></tr>";
        ?>
        </tbody>
        </table>
    </div><!--//.tbl_wrap-->
</div>
<div class="btn_fixed_top">
    <div class="local_ov">
        <?php echo $listall ?>
        <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    </div>

    <form id="fsearch" name="fsearch" method="get" autocomplete="off">
        <input type="hidden" name="sfl" value="plt_idx">
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" readonly placeholder="파레트번호" value="<?php echo $stx ?>" id="stx" class="frm_input input_cnt">
        <input type="submit" class="btn_submit" value="검색">
    </form>
</div>
<script>
$('.input_cnt').on('click',function(){
    $('.input_cnt').removeClass('input_cnt_on');
    $(this).addClass('input_cnt_on');
    $('#mdl_num').css('display','flex');
    $('#input_num').val($(this).val());  
    $('#input_num').focus();
});
$('#qr_scan').select().focus();

//값이 바뀌면 처리하고 다시 포커스
$(document).on('input','#qr_scan',function(e){
    $('#qr_scan').removeClass('ok').removeClass('error');
    $('#qr_status').removeClass('ok').removeClass('error').text('');
    setTimeout(() => {
        if($(this).val()){
            var pattern = /^\d+$/;
            if(pattern.test($(this).val())){
                $('#qr_scan').removeClass('error').addClass('ok');
                $('#qr_status').removeClass('error').addClass('ok').text('스캔했습니다.');
                var dlv_ajax_url = '<?=G5_USER_ADMIN_KIOSK_AJAX_URL?>/dlv_update.php';
                $.ajax({
                    url: dlv_ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {'w':'','mb_id_delivery':'<?=$member['mb_id']?>','plt_idx':$(this).val()},
                    async: false,
                    success: function(res){
                        //출하처리 성공이면 새로고침
                        if(res.ok){
                            location.reload();
                        }
                        //출하처리 실패면 네모박스와 상태문자 error처리
                        else {
                            $('#qr_scan').removeClass('ok').addClass('error');
                            $('#qr_status').removeClass('ok').addClass('error').text(res.msg);
                        }
                    },
                    error: function(xmlReq){
                        alert('Status: ' + xmlReq.status + ' \n\rstatusText: ' + xmlReq.statusText + ' \n\rresponseText: ' + xmlReq.responseText);
                        //로딩끝
                        $('#loading_box').removeClass('focus');
                    }
                });
            }
            else{
                $('#qr_scan').removeClass('ok').addClass('error');
                $('#qr_status').removeClass('ok').addClass('error').text('형식이 맞지 않는 데이터입니다.');
            }
            $('#qr_scan').val('').select().focus();
        }
    }, 500);
});
</script>
<?php
include_once('./_tail.php');
