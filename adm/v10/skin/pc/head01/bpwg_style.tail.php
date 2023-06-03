<script>
$(function(){
	$('<div id="anchor_top" style="margin-top:0.2px;"></div>').insertBefore('#wrapper');
	head_sticky_relocate();
	$(window).scroll(function(){
		head_sticky_relocate();
	});
});
function head_sticky_relocate(){
	var win_top = $(window).scrollTop();
	var anchor_top = $('#anchor_top').offset().top;
	var <?=$bid?>_ht = $('#<?=$bid?>').height();
	if(win_top > anchor_top){
		$('#hd').addClass('head_sticky');
		$('body').css('padding-top',<?=$bid?>_ht);
	}else{
		$('#hd').removeClass('head_sticky');
		$('body').css('padding-top',0);
	}
}
</script>