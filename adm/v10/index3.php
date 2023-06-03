<?php
// $sub_menu = '915110';
include_once('./_common.php');

$g5['title'] = '대시보드';
include_once ('./_head.php');
//$sub_menu : 현재 메뉴코드 915140
//$cur_mta_idx : 현재 메타idx 422

$demo = 0;  // 데모인 경우 1로 설정하세요. (packery 박스가 맨 위에 떠 있어서 디버깅 데이터를 가려버리네요.)

// $cur_mta_idx 변수는 _dashboard_top_submenu.php 에서 생성함 (해당 파일 include는 /adm/v10/admin.head.php 참조)
$sql = " SELECT * FROM {$g5['dash_grid_table']} WHERE mta_idx = '{$cur_mta_idx}' AND dsg_status = 'ok' ORDER BY dsg_order ";
// echo $sql.'<br>';
$result = sql_query($sql,1);

add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/index.css">', 2); // index.php 에서는 필요없는 부분
?>
<style>

</style>

<?php
// 불러올 위젯이 있는 경우
if($result->num_rows){ 
echo '<div class="pkr">'.PHP_EOL;
echo '<div class="pkr-sizer"></div>'.PHP_EOL;

$acc_wd = 0;
$pos_x = 0;
$pos_json = '[';
for($i=0;$row=sql_fetch_array($result);$i++) {
    $pkr_item_w = (!$row['dsg_width_num'])?:' pkr-item-w'.$row['dsg_width_num'];
    $pkr_item_h = (!$row['dsg_height_num'])?:' pkr-item-h'.$row['dsg_height_num'];
    $it_wd_per = $g5['set_pkr_size_value'][$row['dsg_width_num']] / 100;
    $test_acc_wd = $acc_wd + $it_wd_per;
    // echo $pkr_item_w.'<br>';
    //첫번째 그리드는 무조건
    if($i==0){
        $pos_x = $acc_wd;
        $acc_wd = $it_wd_per;
        $pos_json .= '{"attr":"'.$row['dsg_idx'].'","x":'.$pos_x.'}';
    }
    else{
        if($test_acc_wd > 1){
            $pos_x = 0;
            $acc_wd = $it_wd_per;
        }
        else{
            $pos_x = $acc_wd;
            $acc_wd += $it_wd_per;
        }
        $pos_json .= ',{"attr":"'.$row['dsg_idx'].'","x":'.$pos_x.'}';
    }
    $row['mbd_graph_name'] = '그래프 '.$i;
?>
<div class="pkr-item<?=$pkr_item_w.$pkr_item_h?> pkr-box" dsg_idx="<?=$row['dsg_idx']?>" mbd_idx="<?=$row['mbd_idx']?>">
    <div class="pkr-cont" style="display:<?=$demo?'none':''?>;">
    <div class="pkt_wrapper">
        <!-- 타이틀 부분 -->
        <div class="pkt_title">
            <span><?=$row['mbd_graph_name']?></span>
            <span class='graph_icons'>
                <a href="javascript:" class="chart_view" style="display:none;"><i class='fa fa-bar-chart'></i></a>
                <a href="javascript:" class="chart_setting"><i class='fa fa-gear'></i></a>
            </span>
            <ul class="graph_setting">
                <li><a href="javascript:" class="graph_view">상세보기</a></li>
                <li><a href="javascript:" class="graph_excel_down">엑셀다운</a></li>
                <li><a href="javascript:" class="graph_name_change">이름변경</a></li>
                <li><a href="javascript:" class="graph_delete">삭제</a></li>
            </ul>
        </div>
        <!--================ 챠트 부분 ==================-->
        <div id="chart_<?=$row['mbd_idx']?>" class="pkt_cont">
            <i class="fa fa-spin fa-circle-o-notch" id="spinner" style="position:absolute;top:80px;left:46%;font-size:4em;color:#38425b;"></i>
        </div>
        <!--================ // 챠트 부분 ==================-->
    </div>
    </div>
    <i class="fa fa-pencil-square grid_edit grid_mod" aria-hidden="true"></i>
    <i class="fa fa-window-close grid_edit grid_del" aria-hidden="true"></i>
</div><!--//.pkr-item-->
<?php 
}
$pos_json .= ']';
// $pos_arr = json_decode($pos_json,true);
// print_r2($pos_arr);
echo '</div>'.PHP_EOL;//.pkr
}
// 불러올 위젯이 없는 경우
else {
?>
<div class="dash_empty" style="display:no ne;">
    <p>대시보드 데이터가 없습니다.</p>
</div>
<?php
}
?>

<?php include_once('./index_1_packery_script.php'); ?>
<script>
<?php if($result->num_rows){ ?>
$(function(){
    //개별 그리드 삭제
    $('.grid_del').on('click',function(){
        if(!confirm("관련 데이터의 복구가 불가능 하오니\n신중하게 결정하세요.\n선택하신 데이터를 정말로 삭제하시겠습니까?")){
            return false;
        }
        var ajax_url = g5_user_admin_ajax_url+'/grid_del.php';
        var mta_idx = <?=$cur_mta_idx?>;
        var dsg_idx = $(this).parent().attr('dsg_idx');
    
        $.ajax({
            type: 'POST',
            url: ajax_url,
            // dataType: 'text',
            timeout: 30000,
            data: {'mta_idx': mta_idx, 'dsg_idx': dsg_idx},
            success: function(res){
                location.reload();
            },
            error: function(req){
                alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
            }
        });
    });
    
    var grid_focus;
    var mta_idx = <?=$cur_mta_idx?>; 
    // 그리드 편집모드 버튼 클릭
    $('.grid_mod').on('click',function(){
        grid_focus = $(this).parent();
        $(this).addClass('focus');
        $(this).siblings('.pkr-cont').addClass('focus');
        $('#dsm').css('display','flex');
    });
    //모달 닫기 버튼 클릭
    $('#dsm_bg,#dsm_close').on('click',function(){
        grid_focus.find('.grid_mod').removeClass('focus');
        grid_focus.find('.pkr-cont').removeClass('focus');
        $('#dsm').css('display','none');

        grid_focus = null;
    });
});
<?php } ?>
//대시보드 타이틀 옆에 표시된 편집모드 토글버튼
$('.ds_edit_btn').on('click',function(){
    if($(this).hasClass('focus')){
        $(this).removeClass('focus');
        $('.bs_edit').hide();
        $('.grid_edit').hide();
    }
    else{
        $(this).addClass('focus');
        $('.bs_edit').show();
        $('.grid_edit').show();
    }
});
</script>

<script>
// 그래프 설정
$(document).on('click','.chart_setting',function(e){
    e.preventDefault();
    var my_graph_setting = $(this).closest('div.pkr-box').find('.graph_setting');
    if( my_graph_setting.is(':hidden') ) {
        $('.graph_setting').hide(); // 다른 모든 설정 팝오버 숨김
        $('.graph_setting').closest('div.pkr-box').find('.chart_setting i').removeClass('fa-times').addClass('fa-gear');
        my_graph_setting.show();
        $(this).find('i').removeClass('fa-gear').addClass('fa-times');
    }
    else {
        my_graph_setting.hide();
        $(this).find('i').removeClass('fa-times').addClass('fa-gear');
    }
});
</script>

<?php
include_once ('./index_2_dash_modal.php');
include_once ('./_tail.php');
?>
