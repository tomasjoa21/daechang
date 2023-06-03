<?php
$sub_menu = "930120";
include_once('./_common.php');

if(!$member['mb_manager_yn']) {
    alert('메뉴에 접근 권한이 없습니다.');
}

$demo = 0;  // 데모모드 = 1

$mms_array = array('HJ-MH-061'=>58,'HJ-MH-017'=>58,'HJ-MH-060'=>59,'HJ-MH-071'=>60,'HJ-MH-075'=>61);

// 업데이트 함수
if(!function_exists('func_db_update')){
function func_db_update($arr) {
    global $g5,$demo,$mms_array;

    // print_r3($arr);
    // print_r3($mms_array);
    // print_r3($mms_array[$arr['machine_no']]);

    $arr['mms_idx'] = $mms_array[$arr['machine_no']];

    // 조치분류 업데이트 = 고장부위
    $trm_idx = trm_idx_update($arr['mnt_part']);

    // 관련 알람코드 추출
    $sql = "SELECT *
            FROM g5_1_code
            WHERE com_idx = '15'
                AND mms_idx = '".$arr['mms_idx']."'
                AND cod_code = '".$arr['alarm_code']."'
            ORDER BY cod_idx DESC LIMIT 1
    ";
    $cod = sql_fetch($sql,1);

    // 조치시간분
    $arr['mnt_start_dt'] = $arr['mnt_date'].' '.$arr['mnt_start_time'].':00';
    if($arr['mnt_start_time'] > $arr['mnt_end_time']) {
        $arr['mnt_end_dt'] = date("Y-m-d",strtotime($arr['mnt_date'])+86400).' '.$arr['mnt_end_time'].':00';
    }
    else {
        $arr['mnt_end_dt'] = $arr['mnt_date'].' '.$arr['mnt_end_time'].':00';
    }
    // print_r3($arr['mnt_start_dt'].'~'.$arr['mnt_end_dt']);
    $arr['mnt_minute'] = sec2m(strtotime($arr['mnt_end_dt'])-strtotime($arr['mnt_start_dt']));


    $arr['mnt_people'] = 1;
    $arr['mnt_status'] = 'ok';


    // 정보 입력
    $sql_common = " com_idx = '15'
                    , mms_idx = '".$arr['mms_idx']."'
                    , trm_idx_maintain = '".$trm_idx."'
                    , mb_id = '".$arr['mb_id']."'
                    , mnt_name = '".$arr['mnt_name']."'
                    , mnt_db_table = 'code'
                    , mnt_db_idx = '".$cod['cod_idx']."'
                    , mnt_db_code = '".$cod['cod_code']."'
                    , mnt_date = '".$arr['mnt_date']."'
                    , mnt_start_dt = '".$arr['mnt_start_dt']."'
                    , mnt_end_dt = '".$arr['mnt_end_dt']."'
                    , mnt_minute = '".$arr['mnt_minute']."'
                    , mnt_people = '".$arr['mnt_people']."'
                    , mnt_price = '".$arr['mnt_price']."'
                    , mnt_subject = '".$arr['mnt_subject']."'
                    , mnt_content = '".$arr['mnt_content']."'
                    , mnt_status = '".$arr['mnt_status']."'
    ";
    $sql = "SELECT *
            FROM {$g5['maintain_table']}
            WHERE mms_idx = '".$arr['mms_idx']."'
                AND trm_idx_maintain = '".$trm_idx."'
                AND mnt_name = '".$arr['mnt_name']."'
                AND mnt_date = '".$arr['mnt_date']."'
                AND mnt_minute = '".$arr['mnt_minute']."'
    ";
    $row = sql_fetch($sql,1);
    // 삭제 우선 처리
    if($arr['mnt_status']=='삭제') {
        if($row['mnt_idx']) {
            $sql = "DELETE FROM {$g5['maintain_table']} WHERE mnt_idx = '".$row['mnt_idx']."' ";
            if(!$demo) {sql_query($sql,1);}
            else {print_r3($sql);}
        }
    }
    else {
        // 없으면 등록
        if(!$row['mnt_idx']) {
            $sql = " INSERT INTO {$g5['maintain_table']} SET
                        {$sql_common}
                        , mnt_reg_dt = '".G5_TIME_YMDHIS."'
                        , mnt_update_dt = '".G5_TIME_YMDHIS."'
            ";
            if(!$demo) {sql_query($sql,1);}
            $row['mnt_idx'] = sql_insert_id();
        }
        // 있으면 수정
        else {
            $sql = "UPDATE {$g5['maintain_table']} SET
                        {$sql_common}
                        , mnt_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE mnt_idx = '".$row['mnt_idx']."'
            ";
            if(!$demo) {sql_query($sql,1);}
        }
        if($demo) {print_r3($sql);}
        // print_r3($sql);

    }
 
    return $row['mnt_idx'];
}
}

// 조치사항 업데이트
if(!function_exists('trm_idx_update')){
function trm_idx_update($str) {
    global $g5,$demo;

    $sql = "SELECT *
            FROM {$g5['term_table']}
            WHERE trm_status NOT IN ('trash','delete')
                AND trm_taxonomy = 'maintain'
                AND trm_name = '".trim($str)."'
    ";
    $row = sql_fetch($sql,1);
    // 없으면 등록
    if(!$row['trm_idx']) {

        $sql1 = "SELECT * FROM {$g5['term_table']}
                WHERE trm_status NOT IN ('trash','delete')
                    AND trm_taxonomy = 'maintain'
                ORDER BY trm_sort DESC
                LIMIT 1
        ";
        $one = sql_fetch($sql1,1);

        $sql = " INSERT INTO {$g5['term_table']} SET
                    trm_country = 'ko_KR'
                    , trm_name = '".$str."'
                    , trm_taxonomy = 'maintain'
                    , trm_sort = '".($one['trm_sort']+1)."'
                    , trm_left = '".($one['trm_right']+1)."'
                    , trm_right = '".($one['trm_right']+2)."'
                    , trm_status = 'ok'
                    , trm_reg_dt = '".G5_TIME_YMDHIS."'
                    , trm_update_dt = '".G5_TIME_YMDHIS."'
        ";
        if(!$demo) {sql_query($sql,1);}
        $row['trm_idx'] = sql_insert_id();
    }
    if($demo) {print_r3('조치사항 분류 쿼리: '.$sql);}
    // print_r3($sql);
 
    return $row['trm_idx'];
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
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 200; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();

// 시트를 돌면서 알람을 배열로 먼저 생성해 두고 나중에 사용
for ($x=0;$x<sizeof($allData);$x++) {
    // print_r3($x);
    // print_r3(sizeof($allData[$x]));
    // print_r3($allData[$x]);
    for($i=0;$i<=sizeof($allData[$x]);$i++) {
        // print_r3($allData[$x][$i]);
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
        $arr['machine_no'] = $list[0];
        $arr['machine_name'] = $list[1];
        $arr['mnt_date'] = $list[2];
        $arr['alarm_no'] = $list[13];
        $arr['alarm_name'] = $list[14];
        $arr['alarm_code'] = $list[15];
        // print_r3($arr);

        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[-0-9A-Z]/",$arr['machine_no'])
            && preg_match("/[가-힝]/",$arr['machine_name'])
            && preg_match("/[-0-9]/",$arr['mnt_date'])
            && preg_match("/[0-9A-Z]/",$arr['alarm_code']) )
        {
            // print_r3($arr);

            // 배열생성
            $alarm_arr[$arr['machine_no']][$arr['alarm_no']] = $arr['alarm_code'];

        }
        else {continue;}

    }
}
// print_r3($alarm_arr);


// print_r3($allData);
$idx = 0;

// 시트를 돌면서 전체 처리
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
        $arr['machine_no'] = $list[0];
        $arr['machine_name'] = $list[1];
        $arr['mnt_date'] = $list[2];
        $arr['mnt_reason'] = $list[3];    // 사유
        $arr['mnt_content'] = $list[4];
        $arr['mnt_part'] = $list[5];    // 고장부위
        $arr['mnt_name'] = $list[6];
        $arr['mnt_start_time'] = $list[7];
        // $arr['mnt_start_time'] = '23:40';
        $arr['mnt_end_time'] = $list[8];
        // $arr['mnt_end_time'] = '00:40';
        $arr['mnt_minutes'] = $list[9];
        $arr['mnt_company'] = $list[10];
        $arr['alarm_no'] = $list[11];    // 알람번호
        // print_r3($arr);

        // 조건에 맞는 해당 라인만 추출
        if( preg_match("/[-0-9A-Z]/",$arr['machine_no'])
            && preg_match("/[가-힝]/",$arr['machine_name'])
            && preg_match("/[-0-9]/",$arr['mnt_date']) )
        {
            // print_r3($arr);

            // 제목, 내용
            $arr['mnt_content_arr'] = explode("=>",$arr['mnt_content']);
            // print_r3(sizeof($arr['mnt_content_arr']));
            // print_r3($arr['mnt_content_arr']);
            $arr['mnt_content_new'] = $arr['mnt_content_arr'][sizeof($arr['mnt_content_arr'])-1];
            for($j=0;$j<=sizeof($arr['mnt_content_arr'])-2;$j++) {
                // print_r3($arr['mnt_content_arr'][$j]);
                $arr['mnt_subject_arr'][] = addslashes($arr['mnt_content_arr'][$j]);
            }
            $arr['mnt_content'] = addslashes($arr['mnt_content_new']);
            $arr['mnt_subject'] = implode(" => ",$arr['mnt_subject_arr']);

            // 알람코드
            $arr['alarm_code'] = $alarm_arr[$arr['machine_no']][$arr['alarm_no']];

            // print_r3($arr);
            // 데이터 입력&수정&삭제
            $db_idx = func_db_update($arr);

            $idx++;
        }
        else {continue;}


        // 메시지 보임
        if($arr['mnt_subject']) {
            echo "<script> document.all.cont.innerHTML += '".$idx
                    .". ".$arr['mnt_subject']." [".$arr['mnt_part']."]: ".$arr['mnt_content']
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
// for($i=0;$i<=sizeof($allData[0]);$i++) {
//     // print_r3($allData[0][$i]);
// 	if($demo) {
//         if($i>4) {break;}
//     }

//     // 초기화
//     unset($arr);
//     unset($list);
//     // 한 라인씩 $list 숫자 배열로 변경!!
//     if(is_array($allData[0][$i])) {
//         foreach($allData[0][$i] as $k1=>$v1) {
//             // print_r3($k1.'='.$v1);
//             $list[] = trim($v1);
//         }
//     }
//     // print_r3($list);
//     $arr['machine_no'] = $list[0];
//     $arr['machine_name'] = $list[1];
//     $arr['mnt_date'] = $list[2];
//     $arr['mnt_reason'] = $list[3];    // 사유
//     $arr['mnt_content'] = $list[4];
//     $arr['mnt_part'] = $list[5];    // 고장부위
//     $arr['mnt_name'] = $list[6];
//     // $arr['mnt_start_time'] = $list[7];
//     $arr['mnt_start_time'] = '23:40';
//     // $arr['mnt_end_time'] = $list[8];
//     $arr['mnt_end_time'] = '00:40';
//     $arr['mnt_minutes'] = $list[9];
//     $arr['mnt_company'] = $list[10];
//     // print_r3($arr);

//     // 조건에 맞는 해당 라인만 추출
//     if( preg_match("/[-0-9A-Z]/",$arr['machine_no'])
//         && preg_match("/[가-힝]/",$arr['machine_name'])
//         && preg_match("/[-0-9]/",$arr['mnt_date']) )
//     {
//         // print_r3($arr);

//         // 제목, 내용
//         $arr['mnt_content_arr'] = explode("=>",$arr['mnt_content']);
//         // print_r3(sizeof($arr['mnt_content_arr']));
//         // print_r3($arr['mnt_content_arr']);
//         $arr['mnt_content_new'] = $arr['mnt_content_arr'][sizeof($arr['mnt_content_arr'])-1];
//         for($j=0;$j<=sizeof($arr['mnt_content_arr'])-2;$j++) {
//             // print_r3($arr['mnt_content_arr'][$j]);
//             $arr['mnt_subject_arr'][] = $arr['mnt_content_arr'][$j];
//         }
//         $arr['mnt_content'] = $arr['mnt_content_new'];
//         $arr['mnt_subject'] = implode(" => ",$arr['mnt_subject_arr']);

//         // 데이터 입력&수정&삭제
//         $db_idx = func_db_update($arr);

//         $idx++;
//     }
//     else {continue;}


//     // 메시지 보임
//     if($arr['mnt_subject']) {
//         echo "<script> document.all.cont.innerHTML += '".$idx
//                 .". ".$arr['mnt_subject']." [".$arr['mnt_part']."]: ".$arr['mnt_content']
//                 ." ----------->> 완료<br>'; </script>\n";
//     }

//     flush();
//     ob_flush();
//     ob_end_flush();
//     usleep($sleepsec);
    
//     // 보기 쉽게 묶음 단위로 구분 (단락으로 구분해서 보임)
//     if ($i % $countgap == 0)
//         echo "<script> document.all.cont.innerHTML += '<br>'; </script>\n";
    
//     // 화면 정리! 부하를 줄임 (화면 싹 지움)
//     if ($i % $maxscreen == 0)
//         echo "<script> document.all.cont.innerHTML = ''; </script>\n";

// }
// // 두번째 시트
// for($i=0;$i<=sizeof($allData[1]);$i++) {
//     // print_r3($allData[1][$i]);
// }
// ................





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