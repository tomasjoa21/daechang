<?php
$sub_menu = "910110";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

$g5['title'] = '프로젝트스케줄';
include_once('./_top_menu_setting.php');
include_once('./_head.php');
echo $g5['container_sub_title'];


$page = ($page) ? $page : 1;
$rows = ($rows) ? $rows : 100;
$start = ($page - 1) * $rows;

$secret_key = 'secret_qYGZP8jQfeswIUTixLAKPxKFZ8wPJwP3oYfE6tRDous';

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
if($next_cursor) {
	$_param['start_cursor'] = $next_cursor;	// Starting item.
}
$_param['page_size'] = $rows;	// how many for one call. default=100
// // 업체=대창공업인 것만 추출
// $_param['filter']['property'] = '업체';
// $_param['filter']['select'] = [];
// $_param['filter']['select']['equals'] = '대창공업';

$_param['filter']['timestamp'] = 'created_time';
$_param['filter']['created_time'] = [];
$_param['filter']['created_time']['before'] = '2022-05-22T07:04:00';


// $_param['filter']['property'] = 'id';
// $_param['filter']['contains'] = '1d8432e1-7571-4abe-893d-cb2f77ddbb30';
// 이건 안 된다. 왜??

print_r2($_param);
echo json_encode($_param);


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
print_r2($_result);
// print_r3($_result['next_cursor']);
// print_r3($_result['has_more']);

// print_r2($_result['results']);
if($_result['results'][0]) {
	$arr = array();
	for($i=0;$i<sizeof($_result['results']);$i++) {
		// echo $i.' ================== <br>';
		// print_r2($_result['results'][$i]);
		// print_r2($_result['results'][$i]['properties']);
		
		// 회의
		$list[$i]['title'] = $_result['results'][$i]['properties']['회의']['title'][0]['plain_text'];

		// print_r2($_result['results'][$i]['properties']['구분']['multi_select']);
		// 구분 항목
		$arr['type_array'] = $_result['results'][$i]['properties']['구분']['multi_select'];
		// print_r2($arr['type_array']);
		for($j=0;$j<sizeof($arr['type_array']);$j++) {
			// print_r2($arr['type_array'][$j]);
			$list[$i]['types'][] = $arr['type_array'][$j]['name'];
		}

		// 진행율
		$list[$i]['rate'] = $_result['results'][$i]['properties']['진행율']['number'];

		// 사업명
		$arr['project_array'] = $_result['results'][$i]['properties']['사업명']['multi_select'];
		// print_r2($arr['type_array']);
		for($j=0;$j<sizeof($arr['project_array']);$j++) {
			// print_r2($arr['type_array'][$j]);
			$list[$i]['projects'][] = $arr['project_array'][$j]['name'];
		}
		
		// ING담당자
		$arr['ings_array'] = $_result['results'][$i]['properties']['ING담당자']['people'];
		// print_r2($arr['type_array']);
		for($j=0;$j<sizeof($arr['ings_array']);$j++) {
			// print_r2($arr['type_array'][$j]);
			$list[$i]['ings'][] = $arr['ings_array'][$j]['name'];
		}
		
		// 관련부서(팀)
		$arr['related_array'] = $_result['results'][$i]['properties']['관련부서(팀)']['multi_select'];
		for($j=0;$j<sizeof($arr['related_array']);$j++) {
			$list[$i]['related'][] = $arr['related_array'][$j]['name'];
		}
		
		// 업체
		$list[$i]['company'] = $_result['results'][$i]['properties']['업체']['select']['name'];
		
		// 일정
		$list[$i]['start'] = $_result['results'][$i]['properties']['일정']['date']['start'];
		$list[$i]['end'] = $_result['results'][$i]['properties']['일정']['date']['end'];
		
		// 고객사담당자
		$arr['clients_array'] = $_result['results'][$i]['properties']['고객사담당자']['multi_select'];
		for($j=0;$j<sizeof($arr['clients_array']);$j++) {
			$list[$i]['clients'][] = $arr['clients_array'][$j]['name'];
		}
		
	}
}

// print_r2($list);
?>

<?php
if($list[0]) {
	for($i=0;$i<sizeof($list);$i++) {
		$list[$i]['types'] = $list[$i]['types'][0] ? implode(", ",$list[$i]['types']):'';
		$list[$i]['projects'] = $list[$i]['projects'][0] ? implode(", ",$list[$i]['projects']):'';
		$list[$i]['ings'] = $list[$i]['ings'][0] ? implode(", ",$list[$i]['ings']):'';
		$list[$i]['related'] = $list[$i]['related'][0] ? implode(", ",$list[$i]['related']):'';
		$list[$i]['clients'] = $list[$i]['clients'][0] ? implode(", ",$list[$i]['clients']):'';
		// print_r2($list[$i]);
	}
}
?>


<?php
include_once ('./_tail.php');
?>
