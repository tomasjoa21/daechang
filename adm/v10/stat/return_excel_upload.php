<?php
$sub_menu = "935110";
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

    $arr['ret_ym'] = $arr['ret_ym'].'-01';

    $sql_common = " ret_ym	= '".$arr['ret_ym']."',
        ret_type	        = '".$arr['ret_type']."',
        ret_count	        = '".$arr['ret_count']."'
    ";

    // create if not exists, update for existing
    $sql = "	SELECT ret_idx FROM {$g5['return_table']} 
                WHERE ret_ym = '".$arr['ret_ym']."' AND ret_type = '".$arr['ret_type']."'
    ";
    // print_r3($sql);
    $row = sql_fetch($sql,1);
    // 삭제 우선 처리
    if($arr['mnt_status']=='삭제') {
        if($row['ret_idx']) {
            $sql = "DELETE FROM {$g5['return_table']} WHERE ret_idx = '".$row['ret_idx']."' ";
            if(!$demo) {sql_query($sql,1);}
            else {print_r3($sql);}
        }
    }
    else {
        // 없으면 등록
        if(!$row['ret_idx']) {
            $sql = "INSERT INTO {$g5['return_table']} SET
                    {$sql_common}
                    , ret_reg_dt = '".G5_TIME_YMDHIS."'
                    , ret_update_dt = '".G5_TIME_YMDHIS."'
            ";
            if(!$demo) {sql_query($sql,1);}
            $row['ret_idx'] = sql_insert_id();
        }
        // 있으면 수정
        else {
            $sql = "UPDATE {$g5['return_table']} SET
                    {$sql_common}
                    , ret_update_dt = '".G5_TIME_YMDHIS."'
                    WHERE ret_idx = '".$row['ret_idx']."'
            ";
            if(!$demo) {sql_query($sql,1);}
        }
        if($demo) {print_r3($sql);}
        // print_r3($sql);
    }
 
    return $row['ret_idx'];
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
$countgap = 10; // 몇건씩 보낼지 설정
$sleepsec = 200;  // 백만분의 몇초간 쉴지 설정
$maxscreen = 100; // 몇건씩 화면에 보여줄건지?

flush();
ob_flush();


// print_r3($allData);
$idx = 0;

// 첫번째 시트
for ($x=0;$x<sizeof($allData);$x++) {
    // print_r3(sizeof($allData[$x]));
    // print_r3($allData[$x]);
    for($i=0;$i<=sizeof($allData[$x]);$i++) {
        // print_r3($i);
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

        // 월이 나타나는 라인의 항목을 일단 배열로 저장 (아래쪽에 해당 라인의 수량을 찾기 위해서)
        if($list[1]=='工程' && !$mon_arr[0]) {
            for($j=0;$j<=sizeof($list);$j++) {
                // print_r3($list[$j]);
                if(preg_match("/년/",$list[$j])) {
                    $year_arr[] = preg_replace("/[^0-9]*/s", "", $list[$j]);
                }
                if(preg_match("/월/",$list[$j])) {
                    $mon_arr[] = $j;
                }
            }
            // print_r3($year_arr);
            // print_r3($mon_arr);
            $year = $year_arr[sizeof($year_arr)-1]; // 해당년도 추출
        }

        // 보은 첫 단어 등장시 설정하고 계속 유지하다가 $list[2]='보은 불량율'을 만나면 다시 0으로 reset
        if(preg_match("/사외/",$list[1])) {
            define('BOEUN',true);
        }
        // $list[2]='보은 불량율'을 만나면 다시 0으로 reset
        if(preg_match("/보은 불량율/",$list[2])) {
            define('BOEUN_DONE',true);
        }

        // 해당 라인만 계산을 진행합니다.
        if (defined('BOEUN')) {
            // print_r3($list);
            // print_r3($g5['set_return_item_value2']);
            // 환경 설정단어에 포함된 항목만 추출
            foreach($g5['set_return_item_value2'] as $k1=>$v1) {
                // print_r3($k1.'=>'.$v1);
                // 두번째 항목일 수도 있고 세번째일 수도 있음
                if(preg_match("|".$k1."|",$list[2]) || preg_match("|".$k1."|",$list[3])) {
                    // print_r3($list);
                    $arr['ret_type_name'] = $list[3] ? $list[3]:$list[2];
                    $arr['ret_type'] = $k1;
                    for($j=0;$j<sizeof($mon_arr);$j++) {
                        $arr['ret_ym'] = $year.'-'.sprintf("%02d",($j+1));
                        $arr['ret_count'] = intval(preg_replace("/,/","",$list[$mon_arr[$j]]));
                        // print_r3($arr);

                        // 데이터 입력&수정&삭제
                        $db_idx = func_db_update($arr);
                        $idx++;

                        // 메시지 보임
                        if($arr['ret_ym']) {
                            echo "<script> document.all.cont.innerHTML += '".$idx
                                    .". ".$arr['ret_type_name']."(".$arr['ret_ym']."): ".number_format($arr['ret_count'])
                                    ." ----------->> 완료<br>'; </script>\n";
                        }

                    }
                }
            }

        }
        // 계산을 종료하고 이후에는 루프를 빠져나감
        if (defined('BOEUN_DONE')) {
            break;
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