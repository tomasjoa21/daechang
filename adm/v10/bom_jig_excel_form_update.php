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
$maxscreen = 30; // 몇건씩 화면에 보여줄건지?

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
    $arr['image'] = $list[0];
    $arr['machine_name'] = trim($list[1]); // 호기
    $arr['location'] = trim($list[2]);  // 위치
    $arr['bct_name'] = addslashes(trim($list[3]));  // 차종
    $arr['bom_part_no'] = trim($list[4]);    // 품번
    $arr['bom_name'] = addslashes(trim($list[5]));  // 품명
    // print_r3($arr);

    // 조건에 맞는 해당 라인만 추출
    if( preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])
        && $arr['bom_name']
        && $arr['location'] )
    {
        // print_r3($arr['machine_name']);
        // if no machine name, it should be the prev one.
        $arr['machine_name'] = (preg_match("/(SPOT|CO2)/",$arr['machine_name'])) ? '' : $arr['machine_name'];
        // print_r3($arr['machine_name'].'<<<');
        $arr['machine_name'] = (!$arr['machine_name']) ? $machine_name : $arr['machine_name'];
        // print_r3($arr['machine_name'].' !! ---------------');
        // if no car type, it should be the prev one.
        $arr['bct_name'] = (!$arr['bct_name']) ? $bct_name : $arr['bct_name'];

        // print_r3($arr);

        // 설비 찾기 (mms_model에 값을 넣어둬야 함)
        $sql = " SELECT mms_idx, mms_name FROM {$g5['mms_table']} WHERE mms_name = '".$arr['machine_name']."' OR mms_name_ref LIKE '%^".$arr['machine_name']."^%' ";
        $mms = sql_fetch($sql);
        // print_r3($mms);
        // 품번 찾기
        $sql = " SELECT bom_idx, bom_name FROM {$g5['bom_table']} WHERE bom_part_no = '".$arr['bom_part_no']."' ";
        $bom = sql_fetch($sql);
        // print_r3($bom);

        // if only mms_idx, bom_idx exists.
        if($mms['mms_idx'] && $bom['bom_idx']) {

            // 카운터를 하는 지그 표시
            $ar['boj_status'] = (preg_match("/1EA/",$arr['bom_name'])) ? 'ok':'no';

            $ar['table'] = 'g5_1_bom_jig';
            $ar['bom_idx'] = $bom['bom_idx'];
            $ar['mms_idx'] = $mms['mms_idx'];
            $ar['boj_code'] = $arr['location'];
            $ar['boj_plc_ip'] = '192.168.100.143';
            $ar['boj_plc_port'] = '20480';
            // if($demo) {print_r3($arr);}
            // if($ar['mms_idx']==140) {print_r3($arr);print_r3($ar);print_r3('-----------------------<br>');}
            $arr['boj_idx'] = update_db($ar);
            unset($ar);

            $idx++;
            // 메시지 보임
            if(preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])) {
                echo "<script> document.all.cont.innerHTML += '".$idx
                        .". ".$arr['machine_name']." [".$arr['bom_part_no']."]: ".$arr['bom_name']
                        ." ----------->> 완료<br>'; </script>\n";
            }
        }
        // 호기 정보 저장 (바뀔 때 체크해야 함)
        $machine_name = $arr['machine_name'];
        // print_r3($machine_name.' $machine_name');
        // 차종 (없는 경우가 있어서 과거값을 저장)
        $bct_name = $arr['bct_name'];

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