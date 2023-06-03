<script>
if(is_explorer != 1){
	/*
	<?=$lgid?>_anim_ok		= 1
	<?=$lgid?>_fill_ok		= 1
	<?=$lgid?>_path_time		= 3
	<?=$lgid?>_time_diff		= 0.3
	<?=$lgid?>_path_color	= rgba(255,255,255,1)	
	<?=$lgid?>_fill_speed	= 0.5	
	<?=$lgid?>_fill_delay	= 5
	<?=$lgid?>_fill_color	= rgba(255,255,255,1)
	<?=$lgid?>_fillcolor_ini	= rgba(255,255,255,0)
	<?=$lgid?>_svgcss		= '#bwg_top_logo01 svg'
	<?=$lgid?>_pthcss		= '#bwg_top_logo01 svg path'
	<?=$lgid?>_kfr_lineani	= 'bwg_top_logo01_kfr_lineani'
	<?=$lgid?>_kfr_fill		= 'bwg_top_logo01_kfr_fill'	
	<?=$lgid?>_class_cnt = 1
	<?=$lgid?>_addarr['logo_path1'] = [rgba(255,255,255,1),rgba(255,255,255,0),rgba(255,255,255,1)]
	*/
	//console.log(<?=$lgid?>_anim_ok+':'+<?=$lgid?>_fill_ok);
	sv_<?=$lgid?>.removeAttr('id');
	var <?=$lgid?>_stroke_miterlimit = ($(<?=$lgid?>_pthcss).css('stroke-miterlimit')) ? $(<?=$lgid?>_pthcss).css('stroke-miterlimit') : 0;
	//alert($(<?=$lgid?>_svgcss+' style').length);
	$(<?=$lgid?>_svgcss+' style').remove();
	
	if(pt_<?=$lgid?>.length){
		var st_<?=$lgid?> = '<style>';
		
		st_<?=$lgid?> += <?=$lgid?>_pthcss+'{\
		fill:'+<?=$lgid?>_fillcolor_ini + ';\
		stroke:'+<?=$lgid?>_path_color+';\
		stroke-miterlimit:'+<?=$lgid?>_stroke_miterlimit+';\
		}';
		
		if(<?=$lgid?>_class_cnt > 0){
			for(var <?=$lgid?>_key in <?=$lgid?>_addarr){
				st_<?=$lgid?> += <?=$lgid?>_pthcss+'.'+<?=$lgid?>_key+'{\
				fill:'+ <?=$lgid?>_addarr[<?=$lgid?>_key]['fill_ini'] + ';\
				stroke:'+<?=$lgid?>_addarr[<?=$lgid?>_key]['path']+';\
				}';
			}
		}
		
		for(let i_<?=$lgid?> = 0; i_<?=$lgid?> < pt_<?=$lgid?>.length; i_<?=$lgid?>++){
			var <?=$lgid?>_len = Math.floor(pt_<?=$lgid?>[i_<?=$lgid?>].getTotalLength());
			st_<?=$lgid?> += <?=$lgid?>_pthcss+':nth-child('+(i_<?=$lgid?>+1)+'){\
			stroke-dasharray:'+<?=$lgid?>_len+';\
			stroke-dashoffset:'+<?=$lgid?>_len+';';
			if(<?=$lgid?>_class_cnt > 0){
				for(var <?=$lgid?>_key in <?=$lgid?>_addarr){
					if($(pt_<?=$lgid?>[i_<?=$lgid?>]).hasClass(<?=$lgid?>_key)){
						if(<?=$lgid?>_anim_ok == 'yes' && <?=$lgid?>_fill_ok != 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_key+'_lineani;\
							animation-duration:'+<?=$lgid?>_path_time+'s;\
							animation-timing-function:ease;\
							animation-fill-mode:forwards;\
							animation-delay:'+(i_<?=$lgid?> * <?=$lgid?>_time_diff)+'s;';
						}else if(<?=$lgid?>_anim_ok != 'yes' && <?=$lgid?>_fill_ok == 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_key+'_fill;\
							animation-duration:'+<?=$lgid?>_fill_speed+'s;\
							animation-timing-function:ease;\
							animation-fill-mode:forwards;\
							animation-delay:'+<?=$lgid?>_fill_delay+'s;';
						}else if(<?=$lgid?>_anim_ok == 'yes' && <?=$lgid?>_fill_ok == 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_key+'_lineani,'+<?=$lgid?>_key+'_fill;\
							animation-duration:'+<?=$lgid?>_path_time+'s,'+<?=$lgid?>_fill_speed+'s;\
							animation-timing-function:ease,ease;\
							animation-fill-mode:forwards,forwards;\
							animation-delay:'+(i_<?=$lgid?> * <?=$lgid?>_time_diff)+'s,'+<?=$lgid?>_fill_delay+'s;';
						}
					}else{
						if(<?=$lgid?>_anim_ok == 'yes' && <?=$lgid?>_fill_ok != 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_kfr_lineani+';\
							animation-duration:'+<?=$lgid?>_path_time+'s;\
							animation-timing-function:ease;\
							animation-fill-mode:forwards;\
							animation-delay:'+(i_<?=$lgid?> * <?=$lgid?>_time_diff)+'s;';
						}else if(<?=$lgid?>_anim_ok != 'yes' && <?=$lgid?>_fill_ok == 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_kfr_fill+';\
							animation-duration:'+<?=$lgid?>_fill_speed+'s;\
							animation-timing-function:ease;\
							animation-fill-mode:forwards;\
							animation-delay:'+<?=$lgid?>_fill_delay+'s;';
						}else if(<?=$lgid?>_anim_ok == 'yes' && <?=$lgid?>_fill_ok == 'yes'){
							st_<?=$lgid?> += 'animation-name:'+<?=$lgid?>_kfr_lineani+','+<?=$lgid?>_kfr_fill+';\
							animation-duration:'+<?=$lgid?>_path_time+'s,'+<?=$lgid?>_fill_speed+'s;\
							animation-timing-function:ease,ease;\
							animation-fill-mode:forwards,forwards;\
							animation-delay:'+(i_<?=$lgid?> * <?=$lgid?>_time_diff)+'s,'+<?=$lgid?>_fill_delay+'s;';
						}
					}
				}
			}
			st_<?=$lgid?> += '}';
		}
		
		st_<?=$lgid?> += '@keyframes '+<?=$lgid?>_kfr_lineani+'{\
		to{stroke-dashoffset:0;}\
		}';
		st_<?=$lgid?> += '@keyframes '+<?=$lgid?>_kfr_fill+'{\
		from{fill:'+<?=$lgid?>_fillcolor_ini+';}\
		to{fill:'+<?=$lgid?>_fill_color+';}\
		}';
		
		if(<?=$lgid?>_class_cnt > 0){
			for(var <?=$lgid?>_key in <?=$lgid?>_addarr){
				st_<?=$lgid?> += '@keyframes '+<?=$lgid?>_key+'_lineani{\
				to{stroke-dashoffset:0;}\
				}';
				st_<?=$lgid?> += '@keyframes '+<?=$lgid?>_key+'_fill{\
				from{fill:'+<?=$lgid?>_addarr[<?=$lgid?>_key]['fill_ini']+';}\
				to{fill:'+<?=$lgid?>_addarr[<?=$lgid?>_key]['fill']+';}\
				}';
			}
		}
		
		st_<?=$lgid?> += '</style>';
		document.write(st_<?=$lgid?>);
	}
}else{
	sv_<?=$lgid?>.removeAttr('id');
	var <?=$lgid?>_stroke_miterlimit = ($(<?=$lgid?>_pthcss).css('stroke-miterlimit')) ? $(<?=$lgid?>_pthcss).css('stroke-miterlimit') : 0;
	$(<?=$lgid?>_svgcss+' style').remove();
	//$(<?=$lgid?>_svgcss).css({'width':'<?=$sv_wd?>','height':'<?=$sv_ht?>'});
	if(pt_<?=$lgid?>.length){
		var st_<?=$lgid?> = '<style>';

		st_<?=$lgid?> += <?=$lgid?>_pthcss+'{\
		fill:'+<?=$lgid?>_fill_color + ';\
		stroke:'+<?=$lgid?>_path_color+';\
		stroke-miterlimit:'+<?=$lgid?>_stroke_miterlimit+';\
		}';
		
		if(<?=$lgid?>_class_cnt > 0){
			for(var <?=$lgid?>_key in <?=$lgid?>_addarr){
				st_<?=$lgid?> += <?=$lgid?>_pthcss+'.'+<?=$lgid?>_key+'{\
				fill:'+<?=$lgid?>_addarr[<?=$lgid?>_key]['fill']+';\
				stroke:'+<?=$lgid?>_addarr[<?=$lgid?>_key]['path']+';\
				}';
			}
		}
		
		st_<?=$lgid?> += '</style>';
		document.write(st_<?=$lgid?>);
	}	
}
</script>