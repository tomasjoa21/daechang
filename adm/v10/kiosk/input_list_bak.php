<?php
include_once('./_common.php');
include_once('./_head.php');

$sql_common = " FROM {$g5['material_order_item_table']} moi
                LEFT JOIN {$g5['material_order_table']} mto ON moi.mto_idx = mto.mto_idx
                LEFT JOIN {$g5['bom_table']} bom ON moi.bom_idx = bom.bom_idx
";
$where = array();
// 디폴트 검색조건 (used 제외)
$where[] = " mb_id_driver = '{$member['mb_id']}' ";
$where[] = " moi_input_dt LIKE '".G5_TIME_YMD."%' ";
/*
pending=대기 
ok=발주완료, 
ready=준비,
input=입고완료, 
reject=반려, 
cancel=취소, 
trash=삭제
*/
$where[] = " moi_status = 'input' ";

// 검색어 설정
if ($stx != "") {
    switch ($sfl) {
		case ( $sfl == 'moi_idx') :
			$where[] = " {$sfl} = '".trim($stx)."' ";
            break;
		case ( $sfl == 'mto_idx') :
			$where[] = " mto.{$sfl} = '".trim($stx)."' ";
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
    $sst = "moi_input_dt";
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

$sql = " SELECT moi_idx
                , moi.mto_idx
                , moi.bom_idx
                , mto_type
                , bom_name
                , bct_idx
                , bom_part_no
                , mb_id_driver
                , moi_count
                , moi_input_date
                , moi_input_dt
                , moi_reg_dt
                , moi_update_dt
                , moi_status
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
        <span id="qr_ttl">입고처리 스캔을 시작하세요.</span><br>
        <p id="qr_desc">반드시 본인 ID로 로그인 되어 있는지 확인해 주세요.<br>스캔이 안되면 하얀색 사각형 박스를 터치 또는 클릭해 주세요.<br>입고스캔작업을 완료했으면 반드시 홈으로 나가 주세요.</p>
        <input type="text" name="qr_scan" value="" id="qr_scan" class="frm_input" style="width:150px;"><br>
        <strong id="qr_status"></strong>
    </div>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
            <tr>
                <th scope="col">발주제품ID</th>
                <th scope="col">제품정보</th>
                <th scope="col">배송기사</th>
                <th scope="col">수량</th>
                <th scope="col">입고일시</th>
                <th scope="col">취소</th>
            </tr>
        </thead>
        <tbody>
        <?php for($i=0;$row=sql_fetch_array($result);$i++){ 
            $bg = 'bg'.($i%2);
            $mb = sql_fetch(" SELECT mb_name FROM {$g5['member_table']} WHERE mb_id = '{$row['mb_id_driver']}' ");
            $row['mb_name'] = $mb['mb_name'];
        ?>
        <tr class="<?php echo $bg; ?>" tr_id="<?=$row['plt_idx']?>">
            <td class="td_moi_idx"><?=$row['moi_idx']?></td>
            <td class="td_bom_info">
                <p><?=$row['bom_name']?></p>
                <span>[ <?=$row['bom_part_no']?> ]</span>
            </td>
            <td class="td_mb_id_driver"><?=$row['mb_name']?></td>
            <td class="td_moi_count"><?=$row['moi_count']?></td>
            <td class="td_moi_input_dt"><?=$row['moi_input_dt']?></td>
            <td class="td_cancel">
                <a href="javascript:" class="btn btn04 btn_cancel" plt_idx="<?=$row['plt_idx']?>">취소</a>
            </td>
        </tr>
        <?php }
        if($i == 0)
            echo "<tr><td colspan='6' class=\"empty_table\">자료가 없습니다.</td></tr>";
        ?>
        </tbody>
        </table>
    </div><!--//.tbl_wrap-->
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<div class="btn_fixed_top">
    <div class="local_ov">
        <?php echo $listall ?>
        <span class="btn_ov01"><span class="ov_txt">총 </span><span class="ov_num"> <?php echo number_format($total_count) ?>건 </span></span>
    </div>

    <form id="fsearch" name="fsearch" method="get" autocomplete="off">
        <input type="hidden" name="sfl" value="moi_idx">
        <input type="hidden" name="token" value="<?php echo get_session('ss_admin_token'); ?>">
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" readonly placeholder="발주제품ID" value="<?php echo $stx ?>" id="stx" class="frm_input input_cnt">
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
            var except_url = "<?=G5_USER_ADMIN_MOBILE_URL?>/input_check.php?";
            var sub_vars = except_url.substring(except_url.indexOf("?") + 1);
            var sub_arr = sub_vars.split("=");

            if(sub_arr[0] == 'moi_cnt'){
                $('#qr_scan').removeClass('error').addClass('ok');
                $('#qr_status').removeClass('error').addClass('ok').text('스캔했습니다.');
                
                var inp_ajax_url = '<?=G5_USER_ADMIN_KIOSK_AJAX_URL?>/input_update.php';
                $.ajax({
                    url: inp_ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {'w':'','mb_id_driver':'<?=$member['mb_id']?>','moi_idx':$(this).val()},
                    async: false,
                    success: function(res){
                        //출하처리 성공이면 새로고침
                        if(res.ok){
                            // location.reload();
                            location.href = '<?=G5_USER_ADMIN_KIOSK_URL?>/delivery_list.php';
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

//취소버튼 클릭시
$(document).on('click','.btn_cancel',function(e){
    $('#qr_scan').removeClass('ok').removeClass('error');
    $('#qr_status').removeClass('ok').removeClass('error').text('');
    var plt_idx = $(this).attr('plt_idx');
    if(plt_idx){
        if(!confirm(plt_idx + '번 파레트의 출하처리를 정말로 취소하시겠습니까?'))
            return;
        
        var dlv_ajax_url = '<?=G5_USER_ADMIN_KIOSK_AJAX_URL?>/dlv_update.php';
        $.ajax({
            url: dlv_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {'w':'c','mb_id_delivery':'<?=$member['mb_id']?>','plt_idx':plt_idx},
            async: false,
            success: function(res){
                //출하처리 성공이면 새로고침
                if(res.ok){
                    // location.reload();
                    location.href = '<?=G5_USER_ADMIN_KIOSK_URL?>/delivery_list.php';
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
       
        $('#qr_scan').val('').select().focus();
    }
    else{
        alert('plt_idx값이 없습니다.');
    }
});
</script>
<?php
include_once('./_tail.php');
