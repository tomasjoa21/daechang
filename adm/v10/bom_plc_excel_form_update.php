<?php
// header('Content-Encoding: none;');

$sub_menu = "940120";
include_once('./_common.php');

if(!$member['mb_manager_yn']) {
    alert('메뉴에 접근 권한이 없습니다.');
}
if(!$excel_type) {
    alert('엑셀 종류를 선택하세요.');
}

$demo = 0;  // 데모모드 = 1

// print_r2($_REQUEST);
// exit;

require_once G5_LIB_PATH.'/PhpSpreadsheet19/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$upload_file_name = $_FILES['file_excel']['name'];
$file_type= pathinfo($upload_file_name, PATHINFO_EXTENSION);
if ($file_type =='xls') {
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();	
}
elseif ($file_type =='xlsx') {
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
}
else {
	echo '처리할 수 있는 엑셀 파일이 아닙니다';
	exit;
}

$upload_file=$_FILES['file_excel']['tmp_name'];
// $reader->setReadDataOnly(TRUE);
$spreadsheet = $reader->load($upload_file);	
$sheetCount = $spreadsheet->getSheetCount();
for ($i = 0; $i < $sheetCount; $i++) {
    $sheet = $spreadsheet->getSheet($i);
    $sheetData = $sheet->toArray(null, true, true, true);
    // echo $i.' ------------- <br>';
    // print_r2($sheetData);
    $allData[$i] = $sheetData;
}
// print_r3($allData[0]);
// print_r3(sizeof($allData));
// exit;



$g5['title'] = '엑셀 업로드';
//include_once('./_top_menu_applicant.php');
include_once('./_head.php');
//echo $g5['container_sub_title'];
?>
<div class="" style="padding:10px;">
	<span>
		작업 시작~~ <font color=crimson><b>[끝]</b></font> 이라는 단어가 나오기 전 중간에 중지하지 마세요.
	</span><br><br>
	<span id="cont"></span>
</div>
<?php
include_once ('./_tail.php');
?>

<?php
$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 1000;  // 백만분의 몇초간 쉴지 설정, default=200
$maxscreen = 100; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

$idx = 0;
// ==============================================================================
// 첫번째 시트
for($i=0;$i<=sizeof($allData[0]);$i++) {
    // print_r3($allData[0][$i]);
    if($demo) {
        if($i>51) {break;} // 51
    }

    // 초기화
    unset($arr);
    unset($list);
    // 한 라인씩 $list 숫자 배열로 변경!!
    if(is_array($allData[0][$i])) {
        foreach($allData[0][$i] as $k1=>$v1) {
            // print_r3($k1.'='.$v1);
            $list[] = trim($v1);
        }
    }
    // print_r3($list);
    $arr['name'] = preg_replace('#\n#i', " ", $list[0]);   // name
    $arr['word_no'] = $list[1];   // Word No
    $arr['bit_no'] = $list[2]; // bit no
    $arr['byte_no'] = $list[3]; // byte no
    $arr['machine_name'] = trim($list[4]);  // 설비구분
    $arr['tag_name'] = addslashes(trim($list[5]));  //  Tag Name(가동시간, 제품생산코드, R1 지그 카운터, L1 지그 카운터...)
    // print_r3($arr);

    // 조건에 맞는 해당 라인만 추출
    if( is_numeric($arr['word_no'])
        && is_numeric($arr['byte_no'])
        && $arr['tag_name'] )
    {
        // if item name, it should be the prev one.
        $arr['name'] = (!$arr['name']) ? $name : $arr['name'];
        // if no machine name, it should be the prev one.
        // 설비명이 줄바꿈이 있는 경우는 윗줄만 사용
        $arr['machine_name_arr'] = explode("\n",$arr['machine_name']);
        if($arr['machine_name_arr'][1]) {
            $arr['machine_name'] = trim($arr['machine_name_arr'][0]);
        }
        unset($arr['machine_name_arr']);
        $arr['machine_name'] = (!$arr['machine_name']) ? $machine_name : $arr['machine_name'];
        // if no car type, it should be the prev one.
        $arr['word_no'] = (!$arr['word_no']) ? $word_no : $arr['word_no'];

        // print_r3($arr);

        // 설비 찾기 (mms_model에 값을 넣어둬야 함)
        $sql = " SELECT mms_idx, mms_name FROM {$g5['mms_table']} WHERE mms_name = '".$arr['machine_name']."' OR mms_name_ref LIKE '%^".$arr['machine_name']."^%' ";
        // print_r3($sql);
        $mms = sql_fetch($sql);
        // print_r3($mms);

        // if only mms_idx exists.
        if($mms['mms_idx'] && preg_match("/지그 카운터/",$arr['tag_name'])) {
            $arr['mms_idx'] = $mms['mms_idx'];
            $arr['plc_no'] = $arr['word_no'] - 1;
            // $arr['jig_code'] = trim(preg_replace("/ 지그 카운터/","",$arr['tag_name'])); // 내용이 바뀜
            preg_match_all('/[RL]\d/', $arr['tag_name'], $matches); // R 또는 L 다음에 숫자 하나가 오는 패턴
            // print_r($matches[0]);
            $arr['jig_code'] = $matches[0][0];
            
            // print_r3($arr);

            // plc_no 업데이트
            $sql = "UPDATE {$g5['bom_jig_table']} SET boj_plc_no = '".$arr['plc_no']."'
                    WHERE mms_idx = '".$mms['mms_idx']."' AND boj_code = '".$arr['jig_code']."'
            ";
            // print_r3($sql);
            sql_query($sql,1);

            $idx++;
            // 메시지 보임
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['word_no']." [".$arr['machine_name']."]: ".$arr['tag_name']
                    ." ----------->> 완료<br>'; </script>\n";
        }
        // 항목명 저장 (없는 경우가 있어서 과거값을 저장)
        $name = $arr['name'];
        // 설비 정보 저장 (바뀔 때 체크해야 함)
        $machine_name = $arr['machine_name'];
        // Word No (없는 경우가 있어서 과거값을 저장)
        $word_no = $arr['word_no'];

    }
    else {continue;}

    flush();
    ob_flush();
    ob_end_flush();
    usleep($sleepsec);
    
    // 보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
    if ($i % $countgap == 0)
        echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";
    
    // 화면 정리! 부하를 줄임 (화면 싹 지움)
    if ($i % $maxscreen == 0)
        echo "<script> document.all.cont.innerHTML = ''; </script>\n";

}
// ==============================================================================
// 두번째 시트
// for($i=0;$i<=sizeof($allData[1]);$i++) {
//     print_r3($allData[1][$i]);
// }





// 관리자 디버깅 메시지
if( is_array($g5['debug_msg']) ) {
    for($i=0;$i<sizeof($g5['debug_msg']);$i++) {
        echo '<div class="debug_msg">'.$g5['debug_msg'][$i].'</div>';
    }
?>
    <script>
    $(function(){
        $("#container").prepend( $('.debug_msg') );
    });
    </script>
<?php
}
?>


<script>
	document.all.cont.innerHTML += "<br><br>총 <?php echo number_format($idx) ?>건 완료<br><br><font color=crimson><b>[끝]</b></font>";
</script>