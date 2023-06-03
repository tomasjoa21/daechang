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
        cod_memo	        = '".$arr['cod_memo']."',
        cod_update_ny       = '".$arr['cod_update_ny']."'
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
    $sheetData = $sheet->toArray(null, true, true, true);
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

$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 100; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();


// print_r3($allData);
$idx = 0;

// 첫번째 시트
for ($x=0;$x<sizeof($allData);$x++) {
    // print_r3($x);
    // print_r3(sizeof($allData[$x]));
    // print_r3($allData[$x]);
    for($i=0;$i<=sizeof($allData[$x]);$i++) {
        // print_r3($allData[$x][$i]);
        if($demo) {
            if($i>4) {break;}
        }

        // 초기화
        unset($arr);
        unset($list);
        // 한 라인씩 $list 숫자 배열로 변경!!
        if(is_array($allData[$x][$i])) {
            foreach($allData[$x][$i] as $k1=>$v1) {
                // print_r3($k1.'='.$v1);
                $list[] = trim($v1);
            }
        }
        // print_r3($list);
        $arr['cod_idx'] = $list[0];      // 고유번호
        $arr['com_idx'] = $list[1];     // 업체번호
        $arr['imp_idx'] = $list[2];     // IMP
        $arr['mms_idx'] = $list[3];     // mms
        $arr['cod_code'] = $list[4];    // 코드(iMMS)
        $arr['trm_idx_category'] = $list[5];    // 분류
        $arr['cod_offline_yn'] = $list[6];      // 비가동영향
        $arr['cod_quality_yn'] = $list[7];      // 품질영향
        $arr['cod_group'] = $list[8];           // 코드그룹
        $arr['cod_type'] = $list[9];            // 코드타입
        $arr['cod_interval'] = $list[10];       // 주기시간
        $arr['cod_count'] = $list[11];          // 횟수
        $arr['cod_count_limit'] = $list[12];    // 하루최대
        $arr['cod_min_sec'] = $list[13];        // 발생지연
        $arr['cod_name'] = $list[14];           // 내용
        $arr['cod_memo'] = $list[15];           // 메모(알림내용)
        $arr['cod_update_ny'] = $list[16];     // 보호
        // print_r3($arr);

        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[-0-9A-Z]/",$arr['mms_idx'])
            && preg_match("/[a-z]/",$arr['cod_type'])
            && preg_match("/[-0-9]/",$arr['imp_idx']) )
        {
            // print_r3($arr);

            // remove all characters which is not number
            $arr['cod_idx'] = trim( preg_replace("/[^0-9]*/s", "", $arr['cod_idx']) );
            $arr['com_idx'] = trim( preg_replace("/[^0-9]*/s", "", $arr['com_idx']) );
            $arr['imp_idx'] = trim( preg_replace("/[^0-9]*/s", "", $arr['imp_idx']) );
            $arr['mms_idx'] = trim( preg_replace("/[^0-9]*/s", "", $arr['mms_idx']) );    //MMS번호

            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);

            $idx++;
        }
        else {continue;}


        // 메시지 보임
        if($arr['cod_code']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['cod_code'].": ".$arr['cod_name']
                    ." ----------->> 완료<br>'; </script>\n";
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