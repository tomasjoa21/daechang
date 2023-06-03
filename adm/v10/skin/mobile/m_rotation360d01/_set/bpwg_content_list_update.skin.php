<?php
/*
[w] => u
[bwgs_idx] => 1
[token] => d0fedcfc82d8f8b7fdda37a71906d196
[bwgc_status] => Array
		[1] => ok
		[2] => ok
		[3] => ok
[bwgc_order] => Array
		[1] => 0
		[2] => 0
		[3] => 0
[chk] => Array
		[1] => 
		[2] => 
		[3] => 
[bwgc_ytb_url] => Array
		[1] => 
		[2] => https://youtu.be/Wop6B-HgTEg
		[3] => https://youtu.be/VpNah-3SARM
[bwgc_link0] => Array
		[1] => http://bplug.net/bbs/board.php?bo_table=notice
		[2] => http://bplug.net/bbs/board.php?bo_table=notice
		[3] => http://bplug.net/bbs/board.php?bo_table=notice
[bwgc_link0_target] => Array
		[1] => _self
		[2] => _self
		[3] => _self
[bwgc_text1] => Array
		[1] => aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
		[2] => aaaa
		[3] => vvvv
[bwgc_link1] => Array
		[1] => http://bplug.net/bbs/board.php?bo_table=gallery
		[2] => http://bplug.net/bbs/board.php?bo_table=gallery
		[3] => 
[bwgc_link1_target] => Array
		[1] => _blank
		[2] => _self
		[3] => _blank
[bwgc_text2] => Array
		[1] => bbbb
		[2] => bbbb
		[3] => aaaa
[bwgc_link2] => Array
		[1] => http://bplug.net/bbs/board.php?bo_table=free#test
		[2] => http://bplug.net/bbs/board.php?bo_table=free#test
		[3] => http://bplug.net?#test
[bwgc_link2_target] => Array
		[1] => _self
		[2] => _self
		[3] => _self
[bwgc_text3] => Array
		[1] => cccc
		[2] => cccc
		[3] => ddddd
[bwgc_link3] => Array
		[1] => http://bplug.net#test2
		[2] => http://bplug.net#test2
		[3] => daum.net
[bwgc_link3_target] => Array
		[1] => _blank
		[2] => _self
		[3] => _blank
[bwgc_text4] => Array
		[1] => dddd
		[2] => dddd
		[3] => wwwww
[bwgc_link4_target] => Array
		[1] => _self
		[2] => _self
		[3] => _self
*/
//print_r2($_POST);exit;
//데이터 수정시
if($w == 'u'){
	$g5_dns = preg_replace('/^http(s|)\:\/\//i','',G5_URL); //http(s)://이후의 url를 담는 배열
	
	foreach($chk as $k => $v){
		$bwgc_ytb_url[$k] = isset($_POST['bwgc_ytb_url'][$k]) ? trim(strip_tags($_POST['bwgc_ytb_url'][$k])) : '';
		$bwgc_text1[$k] = isset($_POST['bwgc_text1'][$k]) ? trim(strip_tags($_POST['bwgc_text1'][$k])) : '';
		$bwgc_text2[$k] = isset($_POST['bwgc_text2'][$k]) ? trim(strip_tags($_POST['bwgc_text2'][$k])) : '';
		$bwgc_text3[$k] = isset($_POST['bwgc_text3'][$k]) ? trim(strip_tags($_POST['bwgc_text3'][$k])) : '';
		$bwgc_text4[$k] = isset($_POST['bwgc_text4'][$k]) ? trim(strip_tags($_POST['bwgc_text4'][$k])) : '';
		$bwgc_link0[$k] = isset($_POST['bwgc_link0'][$k]) ? trim(strip_tags($_POST['bwgc_link0'][$k])) : '';
		$bwgc_link1[$k] = isset($_POST['bwgc_link1'][$k]) ? trim(strip_tags($_POST['bwgc_link1'][$k])) : '';
		$bwgc_link2[$k] = isset($_POST['bwgc_link2'][$k]) ? trim(strip_tags($_POST['bwgc_link2'][$k])) : '';
		$bwgc_link3[$k] = isset($_POST['bwgc_link3'][$k]) ? trim(strip_tags($_POST['bwgc_link3'][$k])) : '';
		$bwgc_link4[$k] = isset($_POST['bwgc_link4'][$k]) ? trim(strip_tags($_POST['bwgc_link4'][$k])) : '';
		$bwgc_link0_target[$k] = isset($_POST['bwgc_link0_target'][$k]) ? trim(strip_tags($_POST['bwgc_link0_target'][$k])) : '';
		$bwgc_link1_target[$k] = isset($_POST['bwgc_link1_target'][$k]) ? trim(strip_tags($_POST['bwgc_link1_target'][$k])) : '';
		$bwgc_link2_target[$k] = isset($_POST['bwgc_link2_target'][$k]) ? trim(strip_tags($_POST['bwgc_link2_target'][$k])) : '';
		$bwgc_link3_target[$k] = isset($_POST['bwgc_link3_target'][$k]) ? trim(strip_tags($_POST['bwgc_link3_target'][$k])) : '';
		$bwgc_link4_target[$k] = isset($_POST['bwgc_link4_target'][$k]) ? trim(strip_tags($_POST['bwgc_link4_target'][$k])) : '';
		$bwgc_status[$k] = isset($_POST['bwgc_status'][$k]) ? trim(strip_tags($_POST['bwgc_status'][$k])) : '';


		if(preg_match("/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i",$bwgc_link0[$k])){
			if(preg_match('/'.$g5_dns.'/',$bwgc_link0[$k])){
				$bwgc_link0[$k] = preg_replace('/^(http(s|)\:\/\/|)(www\.|)?/i','',$bwgc_link0[$k]);
				$bwgc_link0[$k] = str_replace($g5_dns,'',$bwgc_link0[$k]);
			}
		}

		for($i=1;$i<=4;$i++){
			//echo '#'.$i.'=>'.${'bwgc_link'.$i}[$k]."<br>";continue;
			if(preg_match("/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i",${'bwgc_link'.$i}[$k])){
				if(preg_match('/'.$g5_dns.'/',${'bwgc_link'.$i}[$k])){
					${'bwgc_link'.$i}[$k] = preg_replace('/^(http(s|)\:\/\/|)(www\.|)?/i','',${'bwgc_link'.$i}[$k]);
					${'bwgc_link'.$i}[$k] = str_replace($g5_dns,'',${'bwgc_link'.$i}[$k]);
				}	
			}
		}
		
		$sql = " UPDATE {$g5['bpwidget_content_table']} SET
					bwgc_status = '{$bwgc_status[$k]}',	
					bwgc_order = '{$bwgc_order[$k]}',	
					bwgc_ytb_url = '{$bwgc_ytb_url[$k]}',	
					bwgc_link0 = '{$bwgc_link0[$k]}',	
					bwgc_link0_target = '{$bwgc_link0_target[$k]}',	
					bwgc_text1 = '{$bwgc_text1[$k]}',	
					bwgc_link1 = '{$bwgc_link1[$k]}',	
					bwgc_link1_target = '{$bwgc_link1_target[$k]}',	
					bwgc_text2 = '{$bwgc_text2[$k]}',	
					bwgc_link2 = '{$bwgc_link2[$k]}',	
					bwgc_link2_target = '{$bwgc_link2_target[$k]}',
					bwgc_text3 = '{$bwgc_text3[$k]}',	
					bwgc_link3 = '{$bwgc_link3[$k]}',	
					bwgc_link3_target = '{$bwgc_link3_target[$k]}',
					bwgc_text4 = '{$bwgc_text4[$k]}',	
					bwgc_link4 = '{$bwgc_link4[$k]}',	
					bwgc_link4_target = '{$bwgc_link4_target[$k]}'
				WHERE bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$k}'
		";
		//echo $sql."<br><br>";
		sql_query($sql,1);
	}
}
//데이터 삭제시
else if($w == 'd'){
	//print_r2($chk);
	foreach($chk as $k=>$v){
		if($v){
			//$g5['bpwidget_attachment_table']에서 bwgs_idx,bwgc_idx,content의 파일명을 추출한다.
			$sqlf = " SELECT bwga_name FROM {$g5['bpwidget_attachment_table']} WHERE bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$k}' AND bwga_type = 'content' ";
			$f_result = sql_query($sqlf,1);
			
			for($i=0;$row=sql_fetch_array($f_result);$i++){
				//G5_DATA_PATH.'/bpwidget/file/'.$bwgs_idx.'/content/'.파일명,섬네일들을 지운다.
				//delete_bpwidget_content_files
				delete_bpwidget_content_files($bwgs_idx, 'content', $row['bwga_name']);
			}
			
			//$g5['bpwidget_attachment_table']에서 bwgc_idx = $k 레코드를 지운다.
			$sqla = " DELETE FROM {$g5['bpwidget_attachment_table']} WHERE bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$k}' AND bwga_type = 'content' ";
			sql_query($sqla,1);
			
			//$g5['bpwidget_content_table']에서 bwgc_idx = $k 레코드를 지운다.
			$sqlc = " DELETE FROM {$g5['bpwidget_content_table']} WHERE bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$k}' ";
			sql_query($sqlc,1);
		}
	}
}
?>