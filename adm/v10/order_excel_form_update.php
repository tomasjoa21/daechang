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
$maxscreen = 100; // 몇건씩 화면에 보여줄건지?

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
    // 발주일자
    if($i==3) {
        // print_r3($list);
        // print_r3($list[4]);
        // print_r3( preg_replace("|/|","",$list[4]) );
        // print_r3( is_numeric(preg_replace("|/|","",$list[4])) );
        if( is_numeric(preg_replace("|/|","",$list[4])) ) {
            $ord_date_arr = explode("/",$list[4]);
            // $ord_date = $ord_date_arr[2].'-'.sprintf("%02d",$ord_date_arr[0]).'-'.sprintf("%02d",$ord_date_arr[1]);
            $ord_date = $ord_date_arr[0].'-'.sprintf("%02d",$ord_date_arr[1]).'-'.sprintf("%02d",$ord_date_arr[2]);
            // print_r3($ord_date);
        }
    }


    $arr['cst_name_customer'] = $list[0]; // 고객사
    $arr['bct_name'] = addslashes(trim($list[1]));    // 차종
    $arr['bom_part_no'] = trim($list[2]);    // 품번
    $arr['bom_name'] = addslashes($list[3]);  // 품명
    $arr['count'] = preg_replace("/,/","",$list[4]);      // 발주수량
    $ord_date = $ori_date ? $ori_date : G5_TIME_YMD;      // 발주일
    // print_r3($arr);

    // 조건에 맞는 해당 라인만 추출
    if( preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])
        && ($arr['count']>0)
        && is_numeric($arr['count']) )
    {
        // print_r3($arr);

        // LX2 차종만 일단은 등록합니다.
        // print_r3($arr['bct_name'].'->'.$arr['count']);
        // if($arr['bct_name']=='LX2') {
        //     print_r3($arr['bct_name'].'->'.$arr['count']);
        // }
        if($arr['bct_name']!='LX2') {continue;}

        // 납품처(고객사) 디비 생성
        $ar['table']  = 'g5_1_customer';
        $ar['com_idx']  = $_SESSION['ss_com_idx'];
        $ar['cst_name']  = $arr['cst_name_customer'];
        $ar['cst_type']  = 'customer';
        $arr['cst_idx'] = update_db($ar);
        unset($ar);

        // 카테고리 디비 생성
        $sql = "SELECT bct_idx FROM {$g5['bom_category_table']}
                WHERE bct_name = '".$arr['bct_name']."'
        ";
        // print_r3($sql);
        $bct = sql_fetch($sql,1);
        if(!$bct['bct_idx']) {
            $sql = " SELECT MAX(convert(bct_idx, decimal)) AS bct_max FROM {$g5['bom_category_table']} ";
            // print_r3($sql);
            $bct_max = sql_fetch($sql,1);
            $ar['table']  = 'g5_1_bom_category';
            $ar['bct_idx']  = $bct_max['bct_max']+10;
            $ar['com_idx']  = $_SESSION['ss_com_idx'];
            $ar['bct_name']  = $arr['bct_name'];
            $arr['bct_idx'] = update_db($ar);
            unset($ar);
        }
        else {
            $arr['bct_idx'] = $bct['bct_idx'];
        }


        // // bom 없으면 생성
        // $arr['bom_usage'] = $arr['bom_usage'] ?: 1;
        // $ar['table']  = 'g5_1_bom';
        // $ar['com_idx']  = $_SESSION['ss_com_idx'];
        // $ar['bom_part_no'] = $arr['bom_part_no'];
        // $ar['bom_name'] = $arr['bom_name'];
        // $ar['bom_spec'] = $arr['bom_spec'];
        // $ar['bom_usage'] = $arr['bom_usage'];
        // $ar['bct_idx'] = $arr['bct_idx'];
        // $ar['cst_idx_provider'] = $arr['cst_idx_provider'];
        // $ar['cst_idx_customer'] = $arr['cst_idx'];
        // $ar['bom_type'] = 'product';
        // $ar['bom_status'] = 'ok';
        // $arr['bom_idx_child'] = update_db($ar);
        // unset($ar);

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
            $ar['ori_id'] = preg_replace("/-/","",substr($ord_date,2)).'_'.sprintf("%03d",$idx+1);  // 230614_002
            $ar['ori_count'] = $arr['count'];
            $ar['ori_type'] = 'normal';
            $ar['ori_status'] = 'ok';
            $ar['ori_date'] = $ord_date;
            $arr['ori_idx'] = update_db($ar);
            // print_r3($ar);
            unset($ar);

            $idx++; 
            // 메시지 보임
            if(preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])) {
                echo "<script> document.all.cont.innerHTML += '".$idx
                        .". [".$arr['bct_name']."] ".$arr['bom_part_no'].": ".$arr['count']
                        ." ----------->> 완료<br>'; </script>\n";
            }

        }

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