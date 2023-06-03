<?php
$sub_menu = "925800";
include_once('./_common.php');

if(!$member['mb_manager_yn']) {
    alert('메뉴에 접근 권한이 없습니다.');
}

$demo = 0;  // 데모모드 = 1

// 업데이트 함수
if(!function_exists('func_db_update')){
function func_db_update($arr) {
    global $g5,$demo;

    // print_r3($arr);
    $arr['com_idx'] = 15;

    $sql_common = " com_idx	= '".$arr['com_idx']."',
        imp_idx	            = '".$arr['imp_idx']."',
        mms_idx	            = '".$arr['mms_idx']."',
        cod_code	        = '".$arr['cod_code']."',
        trm_idx_category    = '".$arr['trm_idx_category']."',
        cod_offline_yn      = '".$arr['cod_offline_yn']."',
        cod_quality_yn      = '".$arr['cod_quality_yn']."',
        cod_group	        = '".$arr['cod_group']."',
        cod_type	        = '".$arr['cod_type']."',
        cod_interval	    = '".$arr['cod_interval']."',
        cod_count	        = '".$arr['cod_count']."',
        cod_count_limit     = '".$arr['cod_count_limit']."',
        cod_min_sec	        = '".$arr['cod_min_sec']."',
        cod_name	        = '".$arr['cod_name']."',
        cod_memo	        = '".$arr['cod_memo']."'
    ";

    // create if not exists, update for existing
    $sql = "	SELECT cod_idx FROM {$g5['code_table']} 
                WHERE mms_idx = '".$arr['mms_idx']."'
                    AND cod_code = '".$arr['cod_code']."'
                    AND cod_group = '".$arr['cod_group']."'
                    AND cod_status = 'ok'
    ";
    // print_r3($sql);
    $row = sql_fetch($sql,1);
    // 삭제 우선 처리
    if($arr['mnt_status']=='삭제') {
        if($row['cod_idx']) {
            $sql = "DELETE FROM {$g5['code_table']} WHERE cod_idx = '".$row['cod_idx']."' ";
            if(!$demo) {sql_query($sql,1);}
            else {print_r3($sql);}
        }
    }
    else {
        // 없으면 등록
        if(!$row['cod_idx']) {
            $sql = "INSERT INTO {$g5['code_table']} SET
                    {$sql_common}
                    , cod_send_type = 'email,push'
                    , cod_status = 'ok'
                    , cod_reg_dt = '".G5_TIME_YMDHIS."'
                    , cod_update_dt = '".G5_TIME_YMDHIS."'
            ";
            if(!$demo) {sql_query($sql,1);}
            $row['cod_idx'] = sql_insert_id();
        }
        // 있으면 수정
        else {
            $sql = "UPDATE {$g5['code_table']} SET
                    {$sql_common}
                    , cod_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE cod_idx = '".$row['cod_idx']."'
            ";
            if(!$demo) {sql_query($sql,1);}
        }
        if($demo) {print_r3($sql);}
        // print_r3($sql);
    }
 
    return $row['cod_idx'];
}
}


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
    $sheetData = $sheet->toArray(null, true, false, true);
    // echo $i.' ------------- <br>';
    // print_r2($sheetData);
    $allData[$i] = $sheetData;
}
// print_r3($allData[0]);
// print_r3(sizeof($allData));
// exit;


$g5['title'] = '엑셀 업로드';
// include_once('./_top_menu_stat_data.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];
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
$idx = 0;  // 엑셀 카운터
$mms_imp_array = array(58=>31,59=>31,60=>32,61=>32);


$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 100; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();


// // 첫번째 시트 (네트워크_IP_ADDRESS)
// for($i=0;$i<=sizeof($allData[0]);$i++) {
//     print_r3($i);
//     print_r3($allData[0][$i]);
// }
// // print_r3('===========================================');
// // 두번째 시트 (접속 ID)
// for($i=0;$i<=sizeof($allData[1]);$i++) {
//     print_r3($i);
//     print_r3($allData[1][$i]);
// }
// print_r3('===========================================');
// 세번째 시트 (iMP1_iMMS1_17호기 주조기)
for($i=0;$i<=sizeof($allData[2]);$i++) {
    // print_r3($i);
    // print_r3($allData[2][$i]);
	if($demo) {
        if($i>4) {break;}
    }

    // 초기화
    unset($arr);
    unset($list);
    // 한 라인씩 $list 숫자 배열로 변경!!
    if(is_array($allData[2][$i])) {
        foreach($allData[2][$i] as $k1=>$v1) {
            // print_r3($k1.'='.$v1);
            $list[] = trim($v1);
        }
    }

    // ERROR 라인 이후로만 계산합니다.
    if(preg_match("/ERROR/",$list[0])) {
        define('ERROR',true);
    }

    // 해당 라인만 계산을 진행합니다.
    if (defined('ERROR')) {

        // print_r3($list);
        $arr['cod_name'] = $list[1];
        $arr['cod_code'] = trim($list[6]);
        $arr['cod_memo'] = $list[9];    // 예지내용
        // print_r3($arr);
    
        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[0-9A-Z]/",$arr['cod_code'])
            && $arr['cod_name'] && $list[5] == 'BOOL' )
        {
            // print_r3($arr);
    
            $arr['mms_idx'] = 58;   // LPM05(17호기)
            $arr['imp_idx'] = $mms_imp_array[$arr['mms_idx']];
            $arr['cod_group'] = 'err';
            $arr['cod_type'] = 'a';
            $arr['cod_memo'] = addslashes($arr['cod_memo']);
            // print_r3($arr);
    
            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);
    
            $idx++;
        }
        else {continue;}
    
    
        // 메시지 보임
        if($arr['cod_name']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['cod_name']." (".$arr['cod_code'].") "
                    ." ----------->> 완료<br>'; </script>\n";
        }

    }



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
// 네번째 시트 (iMP1_iMMS2_18호기 주조기)
for($i=0;$i<=sizeof($allData[3]);$i++) {
    // print_r3($i);
    // print_r3($allData[3][$i]);
	if($demo) {
        if($i>4) {break;}
    }

    // 초기화
    unset($arr);
    unset($list);
    // 한 라인씩 $list 숫자 배열로 변경!!
    if(is_array($allData[3][$i])) {
        foreach($allData[3][$i] as $k1=>$v1) {
            // print_r3($k1.'='.$v1);
            $list[] = trim($v1);
        }
    }

    // ERROR 라인 이후로만 계산합니다.
    if(preg_match("/ERROR/",$list[0])) {
        define('ERROR',true);
    }

    // 해당 라인만 계산을 진행합니다.
    if (defined('ERROR')) {

        // print_r3($list);
        $arr['cod_name'] = $list[1];
        $arr['cod_code'] = trim($list[6]);
        $arr['cod_memo'] = $list[9];    // 예지내용
        // print_r3($arr);
    
        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[0-9A-Z]/",$arr['cod_code'])
            && $arr['cod_name'] && $list[5] == 'BOOL' )
        {
            // print_r3($arr);
    
            $arr['mms_idx'] = 59;   // LPM04(18호기) ------------------------
            $arr['imp_idx'] = $mms_imp_array[$arr['mms_idx']];
            $arr['cod_group'] = 'err';
            $arr['cod_type'] = 'a';
            $arr['cod_memo'] = addslashes($arr['cod_memo']);
            // print_r3($arr);
    
            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);
    
            $idx++;
        }
        else {continue;}
    
    
        // 메시지 보임
        if($arr['cod_name']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['cod_name']." (".$arr['cod_code'].") "
                    ." ----------->> 완료<br>'; </script>\n";
        }

    }



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
// 다섯번째 시트 (iMP1_iMMS2_19호기 주조기)
for($i=0;$i<=sizeof($allData[4]);$i++) {
    // print_r3($i);
    // print_r3($allData[4][$i]);
	if($demo) {
        if($i>4) {break;}
    }

    // 초기화
    unset($arr);
    unset($list);
    // 한 라인씩 $list 숫자 배열로 변경!!
    if(is_array($allData[4][$i])) {
        foreach($allData[4][$i] as $k1=>$v1) {
            // print_r3($k1.'='.$v1);
            $list[] = trim($v1);
        }
    }

    // ERROR 라인 이후로만 계산합니다.
    if(preg_match("/ERROR/",$list[0])) {
        define('ERROR',true);
    }

    // 해당 라인만 계산을 진행합니다.
    if (defined('ERROR')) {

        // print_r3($list);
        $arr['cod_name'] = $list[1];
        $arr['cod_code'] = trim($list[6]);
        $arr['cod_memo'] = $list[9];    // 예지내용
        // print_r3($arr);
    
        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[0-9A-Z]/",$arr['cod_code'])
            && $arr['cod_name'] && $list[5] == 'BOOL' )
        {
            // print_r3($arr);
    
            $arr['mms_idx'] = 60;   // LPM03(19호기) ------------------------
            $arr['imp_idx'] = $mms_imp_array[$arr['mms_idx']];
            $arr['cod_group'] = 'err';
            $arr['cod_type'] = 'a';
            $arr['cod_memo'] = addslashes($arr['cod_memo']);
            // print_r3($arr);
    
            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);
    
            $idx++;
        }
        else {continue;}
    
    
        // 메시지 보임
        if($arr['cod_name']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['cod_name']." (".$arr['cod_code'].") "
                    ." ----------->> 완료<br>'; </script>\n";
        }

    }



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
// 여섯번째 시트 (iMP1_iMMS2_20호기 주조기)
for($i=0;$i<=sizeof($allData[5]);$i++) {
    // print_r3($i);
    // print_r3($allData[5][$i]);
	if($demo) {
        if($i>4) {break;}
    }

    // 초기화
    unset($arr);
    unset($list);
    // 한 라인씩 $list 숫자 배열로 변경!!
    if(is_array($allData[5][$i])) {
        foreach($allData[5][$i] as $k1=>$v1) {
            // print_r3($k1.'='.$v1);
            $list[] = trim($v1);
        }
    }

    // ERROR 라인 이후로만 계산합니다.
    if(preg_match("/ERROR/",$list[0])) {
        define('ERROR',true);
    }

    // 해당 라인만 계산을 진행합니다.
    if (defined('ERROR')) {

        // print_r3($list);
        $arr['cod_name'] = $list[1];
        $arr['cod_code'] = trim($list[6]);
        $arr['cod_memo'] = $list[9];    // 예지내용
        // print_r3($arr);
    
        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[0-9A-Z]/",$arr['cod_code'])
            && $arr['cod_name'] && $list[5] == 'BOOL' )
        {
            // print_r3($arr);
    
            $arr['mms_idx'] = 61;   // LPM03(19호기) ------------------------
            $arr['imp_idx'] = $mms_imp_array[$arr['mms_idx']];
            $arr['cod_group'] = 'err';
            $arr['cod_type'] = 'a';
            $arr['cod_memo'] = addslashes($arr['cod_memo']);
            // print_r3($arr);
    
            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);
    
            $idx++;
        }
        else {continue;}
    
    
        // 메시지 보임
        if($arr['cod_name']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['cod_name']." (".$arr['cod_code'].") "
                    ." ----------->> 완료<br>'; </script>\n";
        }

    }



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