// 추가 관리자단(v10) 설정을 위한 js입니다.
// /extend/user.01.default.php 에서 선언되었습니다.

$(function(e) {

    // 목록에서 [전체목록] 총건수 등을 위쪽으로 이동해서 공간 확보 (title 오른편)
    $('#container_title').append( $('.local_ov').addClass('display_inline_block ml_20 visibility_unset') );

    // 마우스 hover 설정
    $(".tbl_head01 tbody tr").on({
        mouseenter: function () {
            $('tr[tr_id='+$(this).attr('tr_id')+']').addClass('tr_hover');            
        },
        mouseleave: function () {
            $('tr[tr_id='+$(this).attr('tr_id')+']').removeClass('tr_hover');
        }    
    });

    // 관리자 상단 오른편, 홈페이지로 이동 target="_blank" 제거 - 자꾸 새창이 떠서 너무 귀찮아요.
    $('.tnb_li .tnb_shop, .tnb_li .tnb_community').removeAttr('target');

    // 스크롤탑 이동 버튼 제거(필요없어요)
    $('#ft .scroll_top').remove();

    // 관리자단 게시판관리 버튼 설정
    if(mb_level>4) {
        $('.btn_bo_user').prepend('<li><a href="./config_form.php?bo_table='+g5_bo_table+'" class="btn_admin btn"><i class="fa fa-gears fa-fw"></i>&nbsp;환경설정</a></li>'); // 환경설정버튼 추가
        $('.btn_bo_user .btn_admin i').removeClass('fa-spin');  // 설정 버튼 빙글이 제거
    }

    // 메뉴 업데이트
    $('#gnb .gnb_li').each(function(e){
        
        // 하단에 아무것도 없으면 숨김(관리자가 아닌 경우만 숨김)
        if( $(this).find('.gnb_oparea ul li').length == 0) {
            $(this).closest('.gnb_li').remove();
        }
        
        // 메뉴 삭제, 링크수정 등 탑메뉴 정리
        var menu1txt = $(this).find('.btn_op').text();   //1차 메뉴명

        // 하단 메뉴 아이콘이 숨김이면 상위 li 도 함께 숨김
        //console.log( menu1txt +' is hidden? / '+ $(this).find('.btn_op').css('display') +'/'+ $(this).find('.btn_op').is(':hidden') );
        if( $(this).find('.btn_op').css('display')=='none' ) {
            //$(this).closest('.gnb_li').css('display','none');
            $(this).closest('.gnb_li').remove();
        }
        
        // 1차 메뉴 통째 제거
        if(menu1txt=='쇼핑몰관리') {
            $(this).remove();
        }
        if(menu1txt=='쇼핑몰현황/기타') {
            $(this).remove();
        }
        if(menu1txt=='SMS 관리') {
            $(this).remove();
        }
        

        $(this).find('.gnb_oparea li').each(function(e){
            var menu2txt = $(this).find('a').text();    //2차 메뉴명
//            console.log( menu1txt +' / '+ menu2txt );
            if(menu1txt=='환경설정') {
                if(menu2txt=='테마설정') { $(this).remove(); }
                if(menu2txt=='세션파일 일괄삭제') { $(this).remove(); }
                if(menu2txt=='캐시파일 일괄삭제') { $(this).remove(); }
                if(menu2txt=='캡챠파일 일괄삭제') { $(this).remove(); }
                if(menu2txt=='썸네일파일 일괄삭제') { $(this).remove(); }
                if(menu2txt=='Browscap 업데이트') { $(this).remove(); }
                if(menu2txt=='DB업그레이드') { $(this).remove(); }
                if(menu2txt=='메뉴설정') { $(this).remove(); }
                if(menu2txt=='접속로그 변환') { $(this).remove(); }
                if(menu2txt=='부가서비스') { $(this).remove(); }
            }
            if(menu1txt=='회원관리') {
                if(menu2txt=='투표관리') { $(this).remove(); }
                if(menu2txt=='포인트관리') { $(this).remove(); }
            }
            if(menu1txt=='게시판관리') {
                if(menu2txt=='글,댓글 현황') { $(this).remove(); }
                if(menu2txt=='인기검색어관리') { $(this).remove(); }
                if(menu2txt=='인기검색어순위') { $(this).remove(); }
                if(menu2txt=='1:1문의설정') { $(this).remove(); }
                if(menu2txt=='내용관리') { $(this).remove(); }
                if(menu2txt=='FAQ관리') { $(this).remove(); }
            }
            if(menu1txt=='쇼핑몰현황/기타') {
                if(menu2txt=='이벤트관리') { $(this).remove(); }
                if(menu2txt=='이벤트일괄처리') { $(this).remove(); }
                if(menu2txt=='보관함현황') { $(this).remove(); }
            }
            if(menu1txt=='인트라게시판') {
                if(menu2txt=='업체게시판') { $(this).remove(); }
            }
            if(menu1txt=='인트라게시판') {
                if(menu2txt=='환경설정') { $(this).remove(); }
            }
            if(menu1txt=='iCMMS') {
                if(!g5_is_mobile) {
                    if(menu2txt=='설비(iMMS)관리') { $(this).remove(); }
                    if(menu2txt=='API연동가이드') { $(this).remove(); }
                }
            }
        });
    });
    // 맨 처음 메뉴에 on 클래스 추가
    var onidx = 0;
    $('#gnb .gnb_li').each(function(e){
        if( $(this).hasClass('on') ) {
            onidx++;
            return false;
        }
    });
    // on 클래스가 하나도 없으면
    if(!onidx) {
        $('#gnb .gnb_li').eq(0).addClass('on');
    }

    // 숨겼던 gnb_li 버튼들 보임 (adm.css 에서 숨겼음)
    $('#gnb .gnb_li').show();

    // 메뉴에 포커스 제거
    $('#gnb .gnb_li .btn_op').css('outline','none');

    // 오른편 상단 접속자 이름 표시
    $('.tnb_mb_btn').html(mb_name+'<span class="<?php echo G5_ADMIN_URL?>/img/op_btn.png">메뉴열기</span>').show();
    
    // 커뮤니티 바로가기 > 홈페이지 바로가기
    $('.tnb_community').attr('title','홈페이지 바로가기').text('홈페이지 바로가기');

    // 사용자단 게시판인 경우
    if( $('#container_sub_title').length >= 1 ) {
        $('#container').css('margin-top','128px');
    }
	
    // ADMINISTRATOR 클릭시 인트라메인으로
    $('#logo a').attr('href',g5_user_admin_url).text('EPCS ADMIN').css({'font-size':'1.4em;'});

    // if localhost, favicon input
    $('<link rel="shortcut icon" href="'+g5_url+'/favicon.ico" type="image/x-ico" />').insertBefore($('title'));

    // remove YoungCart copyright at the admin mode bottom.
    if(g5_print_version) {
        $('#ft p').html( $('#ft p').html().replace(g5_print_version,"") );
    }

    // 이게 왜 안 먹혔을까나?
    $(".scroll_top").click(function(){
        $("body,html").animate({scrollTop:0},150);
    });
    
    url = document.location.href;
    // 팝업레이어에서 구분 항목 숨김
    if( /adm\/newwinform/.test( url ) ) {
        $('.tbl_frm01 tbody tr:first-child').hide();
	}
});	// ehd of $(document).ready(function()	-------------

// Highchart 관련 함수들
// highchart.com이라는 로고 제거
function removeLogo() {
    //Highcharts.com 로고 제거
    setTimeout(function(e){
        $('.highcharts-credits').remove();
    },10);
}
