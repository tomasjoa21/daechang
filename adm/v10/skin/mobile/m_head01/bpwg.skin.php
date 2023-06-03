<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
//add_stylesheet('<link rel="stylesheet" href="'.$bwgs_skin_url.'/bpwg_style.css">', 3);
/*################################## 메뉴 데이터(시작) #####################################*/
$sql = " 
	SELECT
		*
		,CONCAT(
			RPAD((SELECT (me_order+1) FROM {$g5['menu_table']} WHERE me_code = SUBSTRING(me.me_code,1,2)),2,0)
			,SUBSTRING(me.me_code,1,1)
			
			,RPAD(IFNULL((SELECT (me_order) FROM {$g5['menu_table']} WHERE LENGTH(me_code) = 4 AND me_code = SUBSTRING(me.me_code,1,4)),0),2,0)
			,RPAD(SUBSTRING(me.me_code,3,1),1,0)
			
			,RPAD(IFNULL((SELECT (me_order) FROM {$g5['menu_table']} WHERE LENGTH(me_code) = 6 AND  me_code = SUBSTRING(me.me_code,1,6)),0),2,0)
			,RPAD(SUBSTRING(me.me_code,5,1),1,0)
		) AS me_sort 
	FROM {$g5['menu_table']} AS me
	WHERE  
		(me_mobile_use = '1' AND LENGTH(me_code) = 2)
		OR
		(me_mobile_use = '1' AND LENGTH(me_code) = 4 AND (SELECT me_mobile_use FROM {$g5['menu_table']} WHERE me_code = SUBSTRING(me.me_code,1,2)) = '1')
		OR
		(me_mobile_use = '1' AND LENGTH(me_code) = 6 AND (SELECT me_mobile_use FROM {$g5['menu_table']} WHERE me_code = SUBSTRING(me.me_code,1,2)) = '1' AND (SELECT me_mobile_use FROM {$g5['menu_table']} WHERE me_code = SUBSTRING(me.me_code,1,4)) = '1')
	ORDER BY CONVERT( me_sort, char ), me_code
";
$result = sql_query($sql);

$nlist = array();//1차메뉴 [me_code] => [n]의 형식으로 저장
$tlist = array();//1차메뉴가 들어있는 배열
$slist = array();//2차, 3차 메뉴가 한꺼번에 들어있는 배열
$i1 = 0;
$i2 = 0;
$i3 = 0;

for($i=0; $row=sql_fetch_array($result); $i++){
	$row['me_link'] = (substr($row['me_link'],0,1)=='/' && !preg_match("/http/i",$row['me_link'])) ? G5_URL.$row['me_link'] : bpwg_set_http($row['me_link']);
	if(strlen($row['me_code']) == 2){
		$i2 = 0;
		//echo $i1."<br>";
		array_push($tlist,$row);
		array_push($slist,array());
		array_push($nlist,$row);
		$nlist[$i1]['me_2'] = array();
		
		$i1++;
	}
	else if(strlen($row['me_code']) == 4){
		$i3 = 0;
		//echo "&nbsp;&nbsp;&nbsp;&nbsp;".($i1-1).'>'.$i2."<br>";
		array_push($nlist[$i1-1]['me_2'],$row);
		$nlist[$i1-1]['me_2'][$i2]['me_3'] = array();
		$slist[$i1-1][$i2] = $row;
		$slist[$i1-1][$i2]['me_3'] = array();
		
		$i2++;
	}
	else if(strlen($row['me_code']) == 6){
		//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".($i1-1).'>'.($i2-1).'>'.$i3."<br>";
		array_push($nlist[$i1-1]['me_2'][$i2-1]['me_3'],$row);
		array_push($slist[$i1-1][$i2-1]['me_3'],$row);		
		
		$i3++;
	}
}
//print_r2($nlist);
//print_r2($tlist);
//print_r2($slist);
/*################################## 메뉴 데이터(종료) #####################################*/
if(count($bwg_arr['config'])){
	foreach($bwg_arr['config'] as $key=>$val){
		if($key == 'mb_id') ${'bwgs_mb_id'} = $val; //변수 충돌을 피하기 위해서
		else ${$key} = $val;
	}
}
$option_flag = (count($bwg_arr['option'])) ? true : false;
if($option_flag){
//if(true){
	foreach($bwg_arr['option'] as $key=>$val){
		${$key} = $val;
		if($key == 'file'){
			foreach($file as $k=>$v){
				${$k} = $v;
			}
		}
	}
	//관리자버튼에 할당할 id값
	$adid = 'ad_'.$bid.bpwg_uniqid();
	//로고객체 id 할당(접두어:로고=lg,헤더=hd...등등)
	$lgid = 'mhd'.$bid.bpwg_uniqid();
	//판넬객체 id 할당(접두어:판넬=pnl)
	$pnl = 'pnl'.$bid.bpwg_uniqid();

	include_once($bwgs_skin_path.'/bpwg_style.php');
	include_once($bwgs_skin_path.'/bpwg_style.head.php');
	include_once($bwgs_skin_path.'/bpwg_head.skin.php');
	include_once($bwgs_skin_path.'/bpwg_panel.skin.php');
}else{
	echo '<div class="bwg_empty" style="background:#f1f1f1;text-align:center;padding:50px 0;border:1px solid #ddd;"><a style="color:blue;text-decoration:underline;" href="'.G5_BPWIDGET_ADMIN_URL.'/bpwidget_form.php?bwgs_idx='.$bwgs_idx.'&w=u" target="_blank">['.strtoupper($bwg_arr['config']['bwgs_name']).']</a>의 내용이 존재하지 않습니다.</div>'.PHP_EOL;
}
?>