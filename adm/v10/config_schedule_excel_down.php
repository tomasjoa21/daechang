<?php
$sub_menu = "910110";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');


$page = ($page) ? $page : 1;
$rows = ($rows) ? $rows : 100;

$secret_key = 'secret_qYGZP8jQfeswIUTixLAKPxKFZ8wPJwP3oYfE6tRDous';
// 업체=대창공업인 것만 추출
$_param['filter']['property'] = [];
$_param['filter']['property'] = '업체';
$_param['filter']['select'] = [];
$_param['filter']['select']['equals'] = '대창공업';

// the code recommended from notion orig site.
// curl -X POST https://api.notion.com/v1/databases/4dfb2904d16b4b09bb029099d94b5947/query \
//   -H 'Authorization: Bearer secret_qYGZP8jQfeswIUTixLAKPxKFZ8wPJwP3oYfE6tRDous' \
//   -H "Content-Type: application/json" \
//   -H "Notion-Version: 2022-06-28"

$_api_url = 'https://api.notion.com/v1/databases/4dfb2904d16b4b09bb029099d94b5947/query';     // UTF-8 인코딩과 JSON 응답용 호출 페이지
$header_data = [];
$header_data[] = 'Authorization: Bearer '.$secret_key;
$header_data[] = 'Content-Type: application/json';
$header_data[] = 'Notion-Version: 2022-06-28';
// if($next_cursor) {
// 	$_param['start_cursor'] = $next_cursor;	// Starting item.
// }
$_param['page_size'] = $rows;	// how many for one call. default=100
// print_r2($_param);
// echo json_encode($_param);

$x = 0;
$has_more = 1;	// 맨 처음 리스트 호출
while ($has_more) {
	if($next_cursor) {
		$_param['start_cursor'] = $next_cursor;	// Starting item.
	}

	$ch = curl_init(); //curl 사용 전 초기화 필수(curl handle)
	curl_setopt($ch, CURLOPT_URL, $_api_url); //URL 지정하기
	curl_setopt($ch, CURLOPT_POST, 1); //0이 default 값이며 POST 통신을 위해 1로 설정해야 함
	curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($_param)); //POST로 보낼 데이터 지정하기
	// curl_setopt ($ch, CURLOPT_POSTFIELDSIZE, 0); //이 값을 0으로 해야 알아서 &post_data 크기를 측정하는듯
	 
	curl_setopt($ch, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data); //header 지정하기
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //이 옵션이 0으로 지정되면 curl_exec의 결과값을 브라우저에 바로 보여줌. 이 값을 1로 하면 결과값을 return하게 되어 변수에 저장 가능(테스트 시 기본값은 1인듯?)
	$res = curl_exec($ch);
	curl_close($ch);
	 
	$_result = json_decode($res,true);
	// print_r2($_result);
	// print_r3($_result['next_cursor']);
	// print_r3($_result['has_more']);
	$next_cursor = $_result['next_cursor'];
	$has_more = $_result['has_more'];
	
	// print_r2($_result['results']);
	if($_result['results'][0]) {
		$arr = array();
		for($i=0;$i<sizeof($_result['results']);$i++) {
			// echo $x.' ================== <br>';
			// print_r2($_result['results'][$i]);
			// print_r2($_result['results'][$i]['properties']);
			
			// 회의 (제목)
			$list[$x]['title'] = preg_replace("/d1./","",$_result['results'][$i]['properties']['회의']['title'][0]['plain_text']);
	
			// print_r2($_result['results'][$i]['properties']['구분']['multi_select']);
			// 구분 항목
			$arr['type_array'] = $_result['results'][$i]['properties']['작업분류']['multi_select'];
			// print_r2($arr['type_array']);
			for($j=0;$j<sizeof($arr['type_array']);$j++) {
				// print_r2($arr['type_array'][$j]);
				$list[$x]['types'][] = $arr['type_array'][$j]['name'];
			}
	
			// 진행율
			$list[$x]['rate'] = $_result['results'][$i]['properties']['진행율']['number'];
	
			// 사업명
			$arr['project_array'] = $_result['results'][$i]['properties']['사업(분류)']['multi_select'];
			// print_r2($arr['type_array']);
			for($j=0;$j<sizeof($arr['project_array']);$j++) {
				// print_r2($arr['type_array'][$j]);
				$list[$x]['projects'][] = $arr['project_array'][$j]['name'];
			}
			
			// ING담당자
			$arr['ings_array'] = $_result['results'][$i]['properties']['ING담당자']['people'];
			// print_r2($arr['type_array']);
			for($j=0;$j<sizeof($arr['ings_array']);$j++) {
				// print_r2($arr['type_array'][$j]);
				$list[$x]['ings'][] = $arr['ings_array'][$j]['name'];
			}
			
			// 관련부서(팀)
			$arr['related_array'] = $_result['results'][$i]['properties']['관련부서(팀)']['multi_select'];
			for($j=0;$j<sizeof($arr['related_array']);$j++) {
				$list[$x]['related'][] = $arr['related_array'][$j]['name'];
			}
			
			// 업체
			$list[$x]['company'] = $_result['results'][$i]['properties']['업체']['select']['name'];
			
			// 일정
			$list[$x]['start'] = substr($_result['results'][$i]['properties']['일정']['date']['start'],0,10);
			$list[$x]['end'] = $_result['results'][$i]['properties']['일정']['date']['end'] ? substr($_result['results'][$i]['properties']['일정']['date']['end'],0,10) : $list[$x]['start'];
			$starts_array[] = $list[$x]['start'];
			$ends_array[] = $list[$x]['end'];
			
			// 고객사담당자
			$arr['clients_array'] = $_result['results'][$i]['properties']['고객사담당자']['multi_select'];
			for($j=0;$j<sizeof($arr['clients_array']);$j++) {
				$list[$x]['clients'][] = $arr['clients_array'][$j]['name'];
			}

			$x++;
		}
	}
	$page++;
}
// print_r2($starts_array);
$min_date = min($starts_array);
$sql = " SELECT DATE_ADD('".$min_date."', INTERVAL -5 day) AS day FROM dual";
$one = sql_fetch($sql,1);
$start_date = $one['day'];
// echo $start_date.'<br>';
// print_r2($ends_array);
$max_date = max($ends_array);
$sql = " SELECT DATE_ADD('".$max_date."', INTERVAL +10 day) AS day FROM dual";
$one = sql_fetch($sql,1);
$end_date = $one['day'];
// echo $end_date.'<br>';
// 일정 생성
$sql = " SELECT * FROM g5_5_ymd WHERE ymd_date >= '".$start_date."' AND  ymd_date <= '".$end_date."' AND SUBSTRING(ymd_date,9,2) IN ('05','10','15','20','25')";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
$x = 0; // 날짜항목수 (마지막 주간은 억지로 만들어야 하므로 $x 변수 새로 설정)
for($i=0;$row=sql_fetch_array($rs);$i++) {
	// print_r3($row['ymd_date']);
	// // 05인 경우 시작일 = 01
	// if(substr($row['ymd_date'],-2)=='05') {
	// 	$days_array[$i]['start'] = substr($row['ymd_date'],0,8).'01';
	// }
	$month_days[substr($row['ymd_date'],2,-3)][] = $row['ymd_date'];	// 한달의 날짜수 (타이틀 항목표시를 위해 필요한 변수배열)
	$sql1 = " SELECT DATE_ADD('".$row['ymd_date']."', INTERVAL -4 day) AS day";
	$one = sql_fetch($sql1,1);
	$days_array[$x]['start'] = $one['day'];
	$days_array[$x]['end'] = $row['ymd_date'];
	$x++;
	// 25인 경우 마지막 날짜 항목 추가
	if(substr($row['ymd_date'],-2)=='25') {
		$day_last = substr($row['ymd_date'],0,8).date('t', strtotime($row['ymd_date']));
		// print_r3($day_last);
		$month_days[substr($row['ymd_date'],2,-3)][] = $day_last;	// 한달의 날짜수
		$days_array[$x]['start'] = substr($row['ymd_date'],0,8).'25';
		$days_array[$x]['end'] = $day_last;
		$x++;
	}
}
// print_r2($month_days);
// print_r2($days_array);

// 시작일자 기준으로 재정렬
array_multisort(array_column($list, 'start'), SORT_ASC, $list);
// array_multisort(array_column($list, 'end'), SORT_DESC, $list);
// print_r2($list);







require_once G5_LIB_PATH.'/PhpSpreadsheet19/vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
// $sheet->mergeCells('A1:A2');
// 모든 셀 autowidth (1행기준)
// foreach (range('A', $sheet->getHighestColumn()) as $col) {
// 	$sheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
// 폭조절.. 이거 잘 안 되서 하단 부분에서 처리함
// foreach (range('I','DD') as $col) {
// 	$sheet->getColumnDimension($col)->setAutoSize(true);
// }


$sheet->setCellValue(numberToColumnName(1).'1', '제목');
$sheet->setCellValue(numberToColumnName(2).'1', '업체');
$sheet->setCellValue(numberToColumnName(3).'1', '구분');
$sheet->setCellValue(numberToColumnName(4).'1', 'ING담당자');
$sheet->setCellValue(numberToColumnName(5).'1', '고객사담당자');
$sheet->setCellValue(numberToColumnName(6).'1', '일정시작');
$sheet->setCellValue(numberToColumnName(7).'1', '일정종료');
$sheet->setCellValue(numberToColumnName(8).'1', '진행율');
$didx = $col_no = 9;

// 월표시
if(is_array($month_days)) {
	foreach($month_days as $k1 => $v1) {
		// print_r2(sizeof($month_days[$k1]));
		// echo sizeof($v1).'<br>';
		// print_r2($v1);
		$sheet->setCellValue(numberToColumnName($col_no).'1', substr($k1,-2));
		$sheet->getStyle(numberToColumnName($col_no).'1')->getAlignment()->setHorizontal('center');
		// echo '<th scope="col" colspan="'.sizeof($month_days[$k1]).'">'.substr($k1,-2).'</th>'.PHP_EOL;
		$col_no_old = $col_no;
		// echo numberToColumnName($col_no).' - ';
		$col_no += sizeof($v1);
		// echo numberToColumnName($col_no-1).'<br>';
		// echo $col_no.'<br>';
		if(sizeof($v1)) {
			$sheet->mergeCells(numberToColumnName($col_no_old).'1:'.numberToColumnName($col_no-1).'1'); // 월 셀 합침
		}
	}
}
// 일자표시
$col_no = $didx;	// 날자기준값 다시 초기화하고 시작함
if(is_array($month_days)) {
	foreach($month_days as $k1 => $v1) {
		// print_r2(sizeof($month_days[$k1]));
		// print_r2($v1);
		foreach($v1 as $k2 => $v2) {
			// print_r2(sizeof($month_days[$k1]));
			// print_r2($v1);
			$sheet->setCellValue(numberToColumnName($col_no).'2', substr($v2,-2));
			$sheet->getColumnDimension(numberToColumnName($col_no))->setAutoSize(true);	// 폭 auto
			// $spreadsheet->getActiveSheet()->getColumnDimension(numberToColumnName($col_no).'2')->setWidth(5);
			// echo ' <th scope="col">'.substr($v2,-2).'</th>';
			$col_no++;
		}
	}
}

// 제목 부분 셀합침
for($i=1;$i<9;$i++) {
	$sheet->mergeCells(numberToColumnName($i).'1:'.numberToColumnName($i).'2');
}

// $sheet->mergeCells('D1:G1'); // 05
// $sheet->mergeCells('H1:L1'); // 06


// $sheet->setCellValue("A1", '제목');
// $sheet->setCellValue("B1", '업체');
// $sheet->setCellValue("C1", '진행율');
// $sheet->setCellValue("D1", '05');
// $sheet->setCellValue("H1", '06');

// $sheet->setCellValue("D2", '05');
// $sheet->setCellValue("E2", '10');

// $sheet->mergeCells('A1:A2');
// $sheet->mergeCells('B1:B2');
// $sheet->mergeCells('C1:C2');
// $sheet->mergeCells('D1:G1'); // 05
// $sheet->mergeCells('H1:L1'); // 06

$row_no = 3;
if($list[0]) {
	for($i=0;$i<sizeof($list);$i++) {
		$list[$i]['types'] = $list[$i]['types'][0] ? implode(", ",$list[$i]['types']):'';
		$list[$i]['projects'] = $list[$i]['projects'][0] ? implode(", ",$list[$i]['projects']):'';
		$list[$i]['ings'] = $list[$i]['ings'][0] ? implode(", ",$list[$i]['ings']):'';
		$list[$i]['related'] = $list[$i]['related'][0] ? implode(", ",$list[$i]['related']):'';
		$list[$i]['clients'] = $list[$i]['clients'][0] ? implode(", ",$list[$i]['clients']):'';
		// print_r2($list[$i]);
		$sheet->setCellValue("A".$row_no, $list[$i]['title']);
		$sheet->setCellValue("B".$row_no, $list[$i]['company']);
		$sheet->setCellValue("C".$row_no, $list[$i]['types']);
		$sheet->setCellValue("D".$row_no, $list[$i]['ings']);
		$sheet->setCellValue("E".$row_no, $list[$i]['clients']);
		$sheet->setCellValue("F".$row_no, $list[$i]['start']);
		$sheet->setCellValue("G".$row_no, $list[$i]['end']);
		$sheet->setCellValue("H".$row_no, $list[$i]['rate']*100);

		// 일정 색상 표시
		$dayidx = $didx;
		for($j=0;$j<sizeof($days_array);$j++) {
			// print_r2(sizeof($days_array[$k1]));
			// print_r2($v1);
			// $list[$i]['bgcolor'] = ($list[$i]['start']<=$days_array[$j]['end']&&$list[$i]['end']>=$days_array[$j]['start']) ? '#68fff5':'';
			// echo '<td>'.substr($days_array[$j]['start'],-2).'<br>'.substr($days_array[$j]['end'],-2).'</td>'.PHP_EOL;
			// echo '<td style="background-color:'.$list[$i]['bgcolor'].'">&nbsp;</td>'.PHP_EOL;
			if($list[$i]['start']<=$days_array[$j]['end']&&$list[$i]['end']>=$days_array[$j]['start']) {
				$spreadsheet->getActiveSheet()->getStyle(numberToColumnName($dayidx).$row_no)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffc000');
			}
			$dayidx++;
		}
		$row_no++;
	}
}


// $sheet->setCellValue("A3", '172 최종감리 진행');
// $sheet->setCellValue("B3", '대창공업');
// $sheet->setCellValue("C3", '10');
// $sheet->setCellValue("D3", '1'); 	// 05
// $sheet->setCellValue("E3", '2');
// $sheet->setCellValue("F3", '3');
// $sheet->setCellValue("G3", '4');
// $sheet->setCellValue("H3", '1');	// 06
// $sheet->setCellValue("I3", '2');
// $sheet->setCellValue("J3", '3');
// $sheet->setCellValue("K3", '4');
// $sheet->setCellValue("L3", '5');
// $spreadsheet->getActiveSheet()->getStyle("L3")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dddddd');






// Write an .xlsx file
$filename = 'schedule';

// download 파일 받기
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
$writer->save('php://output');

// html 로 페이지상에 표현하기
// $writer = IOFactory::createWriter($spreadsheet, 'Html');
// $writer->save('php://output');

// 특정 위치에 저장하기
// $writer = new Xlsx($spreadsheet); 
// $writer->save(G5_DATA_PATH.'/'.$filename.'.xlsx');

?>