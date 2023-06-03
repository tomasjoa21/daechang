<?php
include_once('./_common.php');

//print_r2($_FILES);//$bwgs_idx,$bwgc_idx,$bwgc_file_maxlength
//exit;

$bwgc_ytb_url = isset($_POST['bwgc_ytb_url']) ? trim(strip_tags($_POST['bwgc_ytb_url'])) : '';
$bwgc_text1 = isset($_POST['bwgc_text1']) ? trim(strip_tags($_POST['bwgc_text1'])) : '';
$bwgc_text2 = isset($_POST['bwgc_text2']) ? trim(strip_tags($_POST['bwgc_text2'])) : '';
$bwgc_text3 = isset($_POST['bwgc_text3']) ? trim(strip_tags($_POST['bwgc_text3'])) : '';
$bwgc_text4 = isset($_POST['bwgc_text4']) ? trim(strip_tags($_POST['bwgc_text4'])) : '';
$bwgc_link0 = isset($_POST['bwgc_link0']) ? trim(strip_tags($_POST['bwgc_link0'])) : '';
$bwgc_link1 = isset($_POST['bwgc_link1']) ? trim(strip_tags($_POST['bwgc_link1'])) : '';
$bwgc_link2 = isset($_POST['bwgc_link2']) ? trim(strip_tags($_POST['bwgc_link2'])) : '';
$bwgc_link3 = isset($_POST['bwgc_link3']) ? trim(strip_tags($_POST['bwgc_link3'])) : '';
$bwgc_link4 = isset($_POST['bwgc_link4']) ? trim(strip_tags($_POST['bwgc_link4'])) : '';
$bwgc_link0_target = isset($_POST['bwgc_link0_target']) ? trim(strip_tags($_POST['bwgc_link0_target'])) : '';
$bwgc_link1_target = isset($_POST['bwgc_link1_target']) ? trim(strip_tags($_POST['bwgc_link1_target'])) : '';
$bwgc_link2_target = isset($_POST['bwgc_link2_target']) ? trim(strip_tags($_POST['bwgc_link2_target'])) : '';
$bwgc_link3_target = isset($_POST['bwgc_link3_target']) ? trim(strip_tags($_POST['bwgc_link3_target'])) : '';
$bwgc_link4_target = isset($_POST['bwgc_link4_target']) ? trim(strip_tags($_POST['bwgc_link4_target'])) : '';
$bwgc_order = isset($_POST['bwgc_order']) ? trim(strip_tags($_POST['bwgc_order'])) : '0';
$bwgc_status = isset($_POST['bwgc_status']) ? trim(strip_tags($_POST['bwgc_status'])) : '';

$g5_dns = preg_replace('/^http(s|)\:\/\//i','',G5_URL); //http(s)://이후의 url를 담는 배열

if(preg_match("/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i",$bwgc_link0)){
	if(preg_match('/'.$g5_dns.'/',$bwgc_link0)){
		$bwgc_link0 = preg_replace('/^(http(s|)\:\/\/|)(www\.|)?/i','',$bwgc_link0);
		$bwgc_link0 = str_replace($g5_dns,'',$bwgc_link0);
	}
}

for($i=1;$i<=4;$i++){
	if(preg_match("/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i",${'bwgc_link'.$i})){
		if(preg_match('/'.$g5_dns.'/',${'bwgc_link'.$i})){
			${'bwgc_link'.$i} = preg_replace('/^(http(s|)\:\/\/|)(www\.|)?/i','',${'bwgc_link'.$i});
			${'bwgc_link'.$i} = str_replace($g5_dns,'',${'bwgc_link'.$i});
		}	
	}
}

//g5_1_bpwidget_content 테이블에 기존 레코드가 한 개도 없으면 무조건 시퀀스는 1부터 시작한다.
bwg_dbtable_sequence_reset($g5['bpwidget_content_table']);

//$g5['bpwidget_attachment_table']
$sql_common = " bwgs_idx = '{$bwgs_idx}'
				,bwgc_file_maxlength = '{$bwgc_file_maxlength}'
				,bwgc_ytb_url = '{$bwgc_ytb_url}'
				,bwgc_text1 = '{$bwgc_text1}' 
				,bwgc_text2 = '{$bwgc_text2}' 
				,bwgc_text3 = '{$bwgc_text3}' 
				,bwgc_text4 = '{$bwgc_text4}' 
				,bwgc_link0 = '{$bwgc_link0}' 
				,bwgc_link1 = '{$bwgc_link1}' 
				,bwgc_link2 = '{$bwgc_link2}' 
				,bwgc_link3 = '{$bwgc_link3}' 
				,bwgc_link4 = '{$bwgc_link4}' 
				,bwgc_link0_target = '{$bwgc_link0_target}' 
				,bwgc_link1_target = '{$bwgc_link1_target}' 
				,bwgc_link2_target = '{$bwgc_link2_target}' 
				,bwgc_link3_target = '{$bwgc_link3_target}' 
				,bwgc_link4_target = '{$bwgc_link4_target}' 
				,bwgc_order = '{$bwgc_order}' 
				,bwgc_status = '{$bwgc_status}' ";

if($w == ''){
	$sql = " INSERT {$g5['bpwidget_content_table']} SET {$sql_common} ";
	sql_query($sql);
	$bwgc_idx = sql_insert_id();
}
//else if($w == 'u'){
//	$sql = " UPDATE {$g5['bpwidget_content_table']} SET {$sql_common} WHERE bwgs_idx = '{$bwgs_idx}' AND bwgc_idx = '{$bwgc_idx}' ";				
//	sql_query($sql);
//}

bpwidget_confile_reg($_FILES,$bwgs_idx,$bwgc_idx);

/*
$_FILES
    [bwcfile] => Array
            [name] => Array
                    [0] => bach-673736_960_720.jpg
                    [1] => banana-flower-4441425_960_720.jpg
            [type] => Array
                    [0] => image/jpeg
                    [1] => image/jpeg
            [tmp_name] => Array
                    [0] => /tmp/phpo4Dxsz
                    [1] => /tmp/phpNnBMuX
            [error] => Array
                    [0] => 0
                    [1] => 0
            [size] => Array
                    [0] => 460816
                    [1] => 184964
*/

$tqstr = $qstr.'&bwgs_idx='.$bwgs_idx.'&w=u&bwg_con=1';
goto_url(G5_BPWIDGET_ADMIN_URL.'/bpwidget_form.php?'.$tqstr, false);
?>