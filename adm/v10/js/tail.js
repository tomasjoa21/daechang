setTimeout(function(){
    if($('iframe.cheditor-editarea').length){
        $('iframe.cheditor-editarea').each(function(){
            $(this).css('background-color','rgb(6,13,27)')
            $(this).contents().find('body').css('color','rgba(255,255,255)');
        });
    }
},2000);
$(function(){
    var ft_p = $('#ft p');
    // ft_p.find('span').text('EPCS');
    $('<strong style="color:#fff;"><span style="color:yellow;">'+cf_company_title+'</span> SYSTEM</strong>').appendTo(ft_p);
    
    if(Number(mb_level) == 10){
        $('<span id="logo_company_name">'+cf_company_title+'</span>').appendTo('#logo');
        $('<li class="tnb_li"><a href="javascript:" class="tnb_com_select">디폴트업체</a></li>').prependTo($('#tnb > ul'));
        $('<a href="'+g5_url+'/_make_data/" class="">.</a>').appendTo($('#logo_company_name'));

        $('.tnb_com_select').on('click',function(){
            winDefaultCompany = window.open(g5_user_admin_url+'/company_change.popup.php?file_name='+file_name, "winDefaultCompany", "left=10,top=10,width=500,height=600");
		    winDefaultCompany.focus();
        });
    }
});