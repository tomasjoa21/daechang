<?php
if (!defined('_GNUBOARD_')) exit;
?>
<style>
.li_dash_submenu{position:relative;}
.li_dash_submenu > i {cursor:pointer;}
.li_dash_submenu > i:hover{color:yellow;}
.li_dash_submenu > span i{color:#ffff;font-size:0.7em;font-weight:700;}
.li_dash_submenu > span:hover{background:darkorange;}
.li_dash_submenu > span{position:absolute;top:4px;left:-20px;cursor:pointer;background:darkred;text-align:center;width:20px;height:20px;line-height:14px;border-radius:50%;display:none;}
.li_dash_submenu .dash_menu_icon{display:none;}
.li_dash_submenu .fa-pencil-square{position:absolute;top:5px;right:12px;}
.li_dash_submenu .fa-window-close{position:absolute;top:5px;right:-12px;}
.li_dash_submenu a{display:block;width:100%;overflow-x:hidden;height:27px;line-height:27px;}
.li_dash_submenu input{position:absolute;top:0;left:0;width:100px;height:27px;line-height:27px;background:#444;color:#fff;border:1px solid #888;padding:0 2px;}
#ul_dash_add{position:absolute;left:0;bottom:0;border-top:1px solid #20304a;width:100%;}
#ul_dash_add span{display:none;}
#ul_dash_add:after{display:block;visibility:hidden;clear:both;content:'';}
#ul_dash_add li{float:left;width:40px;height:40px;border-right:1px solid #20304a;}
#ul_dash_add li a{display:block;width:100%;height:40px;line-height:40px;text-align:center;font-size:1.5em;background:#121929;}
#ul_dash_add li a i{color:#34548a;}
#ul_dash_add li a:hover i{color:#ecb859 !important;}
#ul_dash_add li.li_dash_set a{}
#ul_dash_add li.li_dash_set a.focus{background:#203863;color:yellow;}
#ul_dash_add li.li_dash_set a.focus i{color:#ecb859;}
</style>
<script>
//실제 서브 메뉴의 부모는 #ul_dash_submenu
$('<li data-menu="'+$('#dash_add_dashboard').parent().attr('data-menu')+'" class="li_dash_set"><a href="javascript:" class="gnb_2da" id="dash_set_toggle"><span>메뉴편집</span><i class="fa fa-cog dash_set" aria-hidden="true"></i></a></li>').appendTo('#ul_dash_add')
$('#dash_add_dashboard').parent().appendTo('#ul_dash_add').removeAttr('mta_idx').removeAttr('sort').removeClass('li_dash_submenu').removeAttr('class').addClass('li_dash_add');
$('<i class="fa fa-pencil-square dash_menu_icon dash_edit" aria-hidden="true"></i>').appendTo('.li_dash_submenu');
$('<i class="fa fa-window-close dash_menu_icon dash_delete" aria-hidden="true"></i>').appendTo('.li_dash_submenu');
// $('<span><i class="fa fa-arrows dash_menu_icon dash_move" aria-hidden="true"></i></span>').appendTo('.li_dash_submenu');
var dash_sub = $('#ul_dash_submenu');
var dash_add = $('#dash_add_dashboard');
var dash_mod = $('.dash_edit');
var dash_del = $('.dash_delete');
var dash_set_toggle = $('.dash_set').parent();
if($('.li_dash_submenu').length > 1){
    $('#ul_dash_submenu').sortable({
        connectWith: '#ul_dash_submenu',
        // handle: '.dash_move'
        update: function(event, ui){
            var sorted = '';
            $('.li_dash_submenu').each(function(){
                sorted += ($(this).index() == 0) ? $(this).attr('mta_idx') : ','+$(this).attr('mta_idx');
            });
            // console.log(sorted);
            var dash_url = g5_user_admin_url+'/ajax/dash_sort.php';
            $.ajax({
                type: 'POST',
                url: dash_url,
                data: {'sorted':sorted},
                // dataType: 'text',
                success: function(res){
                    // console.log(res);
                    location.reload();
                },
                error: function(req){
                    alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
                }
            });
        }
    });
}


dash_add.on('click',function(){
    var dash_url = g5_user_admin_url+'/ajax/dash_add.php';
    $.ajax({
        type: 'POST',
        url: dash_url,
        // dataType: 'text',
        success: function(res){
            // console.log(res);
            location.reload();
        },
        error: function(req){
            alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
        }
    });
});

dash_mod.on('click',function(){
    if($(this).siblings('input').length){
        if($(this).siblings('input').val() != ''){
            var ipt_val = $(this).siblings('input').val();
            var dash_url = g5_user_admin_url+'/ajax/dash_mod_ttl.php';
            var sub_menu_idx = $(this).parent().attr('mta_idx');
            $.ajax({
                type: 'POST',
                url: dash_url,
                data: {'mta_idx':sub_menu_idx, 'mta_title':ipt_val},
                success: function(res){
                    location.reload();
                },
                error: function(req){
                    alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
                }
            });
        }
        return true;
    }
    $('.ipt_ds_ttl').remove();
    $('<input name="mta_title" class="ipt_ds_ttl" value="'+$(this).siblings('a').text()+'">').appendTo($(this).parent());
    ipt_event_on();
});

dash_del.on('click',function(){
    if($('.ipt_ds_ttl').length){
        ipt_event_off();
        $('.ipt_ds_ttl').remove();
        return false;
    }

    if(!confirm("관련 데이터의 복구가 불가능 하오니\n신중하게 결정하세요.\n선택하신 메뉴를 정말로 삭제하시겠습니까?")){
        return false;
    }
    var dash_url = g5_user_admin_url+'/ajax/dash_del.php';
    var sub_menu_cd = $(this).parent().attr('data-menu');
    var sub_menu_idx = $(this).parent().attr('mta_idx');
    $.ajax({
       type: 'POST',
       url: dash_url,
       dataType: 'text',
       data: {'mta_idx':sub_menu_idx,'mta_value':sub_menu_cd},
       success: function(res){
            // console.log(res);
            location.reload();
        },
        error: function(req){
            alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
        }
    });
});

dash_set_toggle.on('click',function(){
    if($(this).hasClass('focus')){
        $(this).removeClass('focus');
        $('.dash_menu_icon').hide();
    }
    else{
        $(this).addClass('focus');
        $('.dash_menu_icon').show();
    }
});

function ipt_event_on(){
    $('.ipt_ds_ttl').on('keypress',function(e){
        if(e.which == 13){
            if($(this).val() != ''){
                var dash_url = g5_user_admin_url+'/ajax/dash_mod_ttl.php';
                var sub_menu_idx = $(this).parent().attr('mta_idx');
                $.ajax({
                    type: 'POST',
                    url: dash_url,
                    data: {'mta_idx':sub_menu_idx, 'mta_title':$(this).val()},
                    success: function(res){
                        location.reload();
                    },
                    error: function(req){
                        alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
                    }
                });
            } 

            ipt_event_off();
            $(this).remove();
        }
    });
}
function ipt_event_off(){
    $('.ipt_ds_ttl').off('keypress');
}
</script>