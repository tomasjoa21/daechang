<?php
$sub_menu = "918110";
include_once('./_common.php');

$demo = 0;  // 데모모드 = 1

// print_r2($_REQUEST);
// print_r2($_FILES);
// exit;

foreach($_FILES as $k1=>$v1) {
    // echo $k1.$v1.'<br>';
    // print_r2($v1);
    // If file exists. Only one files is processed for conciseness(간결).
    if($k1) {
        $file_name = $k1;
        $upload_file_name = $_FILES[$file_name]['name'];
        $upload_file=$_FILES[$file_name]['tmp_name'];
        break;
    }
}
// exit;

// $upload_file_name = $_FILES['file_excel']['name'];
require_once G5_LIB_PATH.'/PhpSpreadsheet19/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

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

// $upload_file=$_FILES['file_excel']['tmp_name'];
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

$mb_id_array = array('kang','song','park');
$idx = 0;
// ==============================================================================
// 첫번째 시트
for($i=0;$i<=sizeof($allData[0]);$i++) {
    // print_r3($allData[0][$i]);
    if($demo) {
        if($i>11) {break;} // 170
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

    // 금강 수주파일 ----------------------------------
    if($file_name == 'file_excel_k1') {

        $arr['no'] = $list[0]; // 고객사
        $arr['bct_name'] = addslashes($list[1]);    // 차종
        $arr['bom_part_no'] = trim($list[2]);    // 품번
        $arr['bom_name'] = addslashes($list[3]);  // 품명
        $arr['container'] = $list[4];      // 용기
        $arr['snp'] = $list[5];      // SNP
        $arr['stock'] = $list[6];      // current stock
        $arr['count'] = $list[7];      // order amount
        // print_r3($arr);
    
        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])
            && ($arr['count']>0)
            && is_numeric($arr['count']) )
        {
            // 거래처 == (주)금강
            $arr['cst_idx'] = 100;
            // print_r3($arr);

            // bom_idx
            $sql = " SELECT bom_idx FROM {$g5['bom_table']} WHERE bom_part_no = '".$arr['bom_part_no']."' ";
            // print_r3($sql);
            $bom = sql_fetch($sql,1);
            if($bom['bom_idx']) {

                // 수주제품
                $ar['table'] = 'g5_1_order_item';
                $ar['com_idx'] = $_SESSION['ss_com_idx'];
                $ar['cst_idx'] = $arr['cst_idx'];
                $ar['bom_idx'] = $bom['bom_idx'];
                $ar['ori_count'] = $arr['count'];
                $ar['ori_status'] = 'ok';
                $ar['ori_date'] = $ord_date;
                $arr['ori_idx'] = update_db($ar);
                // print_r3($ar);
                unset($ar);
    
                // 출하
                $ar['table'] = 'g5_1_shipment';
                $ar['com_idx'] = $_SESSION['ss_com_idx'];
                $ar['cst_idx'] = $arr['cst_idx'];
                $ar['ori_idx'] = $arr['ori_idx'];
                $ar['mb_id'] = $mb_id_array[rand(0,sizeof($mb_id_array)-1)];
                $ar['shp_count'] = $arr['count'];
                $ar['shp_dt'] = $ord_date.' 10:00:00';
                $ar['shp_status'] = 'pending';
                $arr['shp_idx'] = update_db($ar);
                // print_r3($ar);
                unset($ar);
    
                $idx++; 
                // 메시지 보임
                if(preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])) {
                    echo "<script> document.all.cont.innerHTML += '".$idx
                            .". ".$arr['bom_part_no'].": ".$arr['count']
                            ." ----------->> 완료<br>'; </script>\n";
                }

            }

        }
        else {continue;}

    }
    // //금강 수주파일 ----------------------------------

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