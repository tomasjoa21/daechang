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

// 카테고리 구조 변수.. 2자리씩 묶어서 계층구조 만들 예정
$cats = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
$cat_arr = array();
for($i=0;$i<count($cats);$i++){
    if($i == 0) continue;
    for($j=0;$j<count($cats);$j++){
        //echo $cats[$i].$cats[$j]."<br>";
        array_push($cat_arr,$cats[$i].$cats[$j]);
    }
}
// print_r2($cat_arr);
// echo array_search('za',$cat_arr);


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
    $arr['no'] = $list[0];
    $arr['cst_name_customer'] = addslashes($list[1]); // 고객사(납품처)
    $arr['bct_name'] = addslashes($list[2]);    // 차종
    $arr['level1'] = $list[3];
    $arr['level2'] = $list[4];
    $arr['level3'] = $list[5];
    $arr['level4'] = $list[6];
    $arr['level5'] = $list[7];
    $arr['level6'] = $list[8];
    $arr['level7'] = $list[9];
    $arr['level8'] = $list[10];
    $arr['level9'] = $list[11];
    $arr['level10'] = $list[12];
    $arr['level11'] = $list[13];
    $arr['level12'] = $list[14];
    $arr['bom_spec'] = $list[15];        // 사양(규격)
    $arr['image'] = $list[16];
    $arr['bom_part_no'] = $list[17];    // 품번
    $arr['bom_name'] = addslashes($list[18]);  // 품명
    $arr['bom_usage'] = $list[19] ?: 1;  // US/구성품수
    $arr['cst_name_provider'] = addslashes($list[20]);  // 공급처(업체명)
    $arr['remark'] = $list[21]; // 비고(설비)
    $arr['worker1'] = $list[22]; // 작업자 주(메인)
    $arr['worker2'] = $list[23]; // 작업자 야간
    $arr['worker3'] = $list[24]; // 작업자 부
    // print_r3($arr);

    // 조건에 맞는 해당 라인만 추출
    if( preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])
        && $arr['bom_name']
        && is_numeric($arr['bom_usage']) )
    {
        // if no car type, it should be the prev one.
        $arr['bct_name'] = (!$arr['bct_name']) ? $bct_name : $arr['bct_name'];

        // print_r3($arr);

        // 대창공업 ITEM LIST_REV1(22.12.22)-개발이범희GJ_REV12.xlxs ================================================================
        if($excel_type=='01') {

            // 레벨 추출
            for($j=1;$j<13;$j++) {
                if($arr['level'.$j]) {
                    $bom_level = $j;
                    break;
                }
            }

            // NE인 경우 납품처가 없으면 (주)금강 230118 서종현부장
            if($bom_level == 1 && $arr['bct_name']=='NE') {
                $arr['cst_name_customer'] = ($arr['cst_name_customer']) ?: '(주)금강';
            }
            // 업체명 치환 #N/A -> MIP (0118 박정석 부장)
            if($arr['worker1']||$arr['worker2']||$arr['worker3']) {
                $arr['cst_name_provider'] = ($arr['cst_name_provider']=='#N/A') ? 'MIP' : $arr['cst_name_provider'];
            }
            // print_r3($arr);

            // 완성품이 바뀌면 초기화
            if($bom_level == 1) {
                // 초기값 정의 (외부 함수들에서 사용할 global 변수)
                $reply = '';
                $num = 0;
                $bom_type = 'product';
            }
            else {
                $bom_type = 'material';
                if($arr['remark']) {
                    $bom_type = 'half';
                }
            }

            // 납품처(고객사) 디비 생성
            $ar['table']  = 'g5_1_customer';
            $ar['com_idx']  = $_SESSION['ss_com_idx'];
            $ar['cst_name']  = $arr['cst_name_customer'];
            $ar['cst_type']  = 'customer';
            $arr['cst_idx_'.$ar['cst_type']] = update_db($ar);
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

            // 공급처(거래처) 디비 생성
            $ar['table']  = 'g5_1_customer';
            $ar['com_idx']  = $_SESSION['ss_com_idx'];
            $ar['cst_name']  = $arr['cst_name_provider'];
            $ar['cst_type']  = 'provider';
            $arr['cst_idx_'.$ar['cst_type']] = update_db($ar);
            unset($ar);

            // 작업자 디비 추출
            // $arr['worker1_array'] = explode(",",$arr['worker1']);
            $arr['worker1_array'] = preg_split("/[\/,]/", $arr['worker1']);
            // print_r3(sizeof($arr['worker1_array']));
            for($j=0;$j<=sizeof($arr['worker1_array']);$j++) {
                if($arr['worker1_array'][$j] && !is_numeric($arr['worker1_array'][$j])) {
                    $pp = preg_replace("/[^0-9]*/s", "", $arr['worker1_array'][$j]);
                    // print_r3($arr['worker1_array'][$j]);
                    // print_r3(make_cell_number($arr['worker1_array'][$j]));
                    $ar['mb_name_no'] = preg_replace("/[^0-9]*/s", "", $arr['worker1_array'][$j]);
                    $ar['mb_name_str'] = preg_replace("/".$ar['mb_name_no']."/", "", $arr['worker1_array'][$j]);
                    $ar['mb_name'] = $ar['mb_name_str'];
                    $sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_name = '".$ar['mb_name']."' ";
                    // print_r3($sql);
                    $mb1 = sql_fetch($sql,1);
                    if($mb1['mb_id']) {
                        $arr['day_mmses'][] = $ar['mb_name_no'];
                        $arr['day_ids'][] = $mb1['mb_id'];  // array value for day workers
                    }
                    unset($ar);
                }
            }
            // print_r3($arr['day_mmses']);
            // print_r3($arr['day_ids']);
            // $arr['worker2_array'] = explode(",",$arr['worker2']);
            $arr['worker2_array'] = preg_split("/[\/,]/", $arr['worker2']);
            // print_r3(sizeof($arr['worker2_array']));
            for($j=0;$j<=sizeof($arr['worker2_array']);$j++) {
                if($arr['worker2_array'][$j] && !is_numeric($arr['worker2_array'][$j])) {
                    // print_r3($arr['worker2_array'][$j]);
                    $ar['mb_name_no'] = preg_replace("/[^0-9]*/s", "", $arr['worker2_array'][$j]);
                    $ar['mb_name_str'] = preg_replace("/".$ar['mb_name_no']."/", "", $arr['worker2_array'][$j]);
                    $ar['mb_name'] = $ar['mb_name_str'];
                    $sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_name = '".$ar['mb_name']."' ";
                    // print_r3($sql);
                    $mb2 = sql_fetch($sql,1);
                    if($mb2['mb_id']) {
                        $arr['night_mmses'][] = $ar['mb_name_no'];
                        $arr['night_ids'][] = $mb2['mb_id'];  // array value for night workers
                    }
                    unset($ar);
                }
            }
            // print_r3($arr['night_ids']);
            // $arr['worker3_array'] = explode(",",$arr['worker3']);
            $arr['worker3_array'] = preg_split("/[\/,]/", $arr['worker3']);
            // print_r3(sizeof($arr['worker3_array']));
            for($j=0;$j<=sizeof($arr['worker3_array']);$j++) {
                if($arr['worker3_array'][$j] && !is_numeric($arr['worker3_array'][$j])) {
                    // print_r3($arr['worker3_array'][$j]);
                    $ar['mb_name_no'] = preg_replace("/[^0-9]*/s", "", $arr['worker3_array'][$j]);
                    $ar['mb_name_str'] = preg_replace("/".$ar['mb_name_no']."/", "", $arr['worker3_array'][$j]);
                    $ar['mb_name'] = $ar['mb_name_str'];
                    $sql = "SELECT mb_id FROM {$g5['member_table']} WHERE mb_name = '".$ar['mb_name']."' ";
                    // print_r3($sql);
                    $mb3 = sql_fetch($sql,1);
                    if($mb3['mb_id']) {
                        $arr['sub_mmses'][] = $ar['mb_name_no'];
                        $arr['sub_ids'][] = $mb3['mb_id'];  // array value for sub workers
                    }
                    unset($ar);
                }
            }
            // print_r3($arr['sub_ids']);

            // bom 생성
            $ar['table']  = 'g5_1_bom';
            $ar['com_idx']  = $_SESSION['ss_com_idx'];
            $ar['bom_part_no'] = $arr['bom_part_no'];
            $ar['bom_name'] = $arr['bom_name'];
            $ar['bom_spec'] = $arr['bom_spec'];
            $ar['bom_usage'] = $arr['bom_usage'];
            $ar['bct_idx'] = $arr['bct_idx'];
            $ar['cst_idx_provider'] = $arr['cst_idx_provider'];
            $ar['cst_idx_customer'] = $arr['cst_idx_customer'];
            $ar['bom_type'] = $bom_type;
            $ar['bom_status'] = 'ok';
            $arr['bom_idx_child'] = update_db($ar);
            unset($ar);
            if($bom_level == 1) {
                // 최상위 부모 코드는 완제품이 다시 나올 때까지 계속 유지되어야 함
                $bom_idx = $arr['bom_idx_child'];
                // reset DB bit_reply value for provihibiting cunfustion.
                $sql = "UPDATE {$g5['bom_item_table']} SET bit_num = '0', bit_reply = ''
                        WHERE bom_idx = '".$bom_idx."'
                ";
                sql_query($sql,1);
            }
            else {
                // 계층 구조 생성
                // print_r3($arr['bom_part_no']);
                $len1 = 2*($bom_level-1) - 1; // 2*(1)-1
                $len2 = 2*($bom_level-2);   // 2*(0)
                // if($demo) {print_r3($len2.' ----> '.$bom_level.' level');}
                // if($demo) {print_r3($reply.' ----');}
                $reply_pre = substr($reply,0,$len2);   // prior 0digit letters for level 2, 2digit for level 3, 4digit for level 4
                // if($demo) {print_r3($reply_pre.' << ');}
                // 2*(1)-1, 2*(0).. ()부분이 내가 있는 레벨
                $sql = "SELECT MAX(SUBSTRING(bit_reply,{$len1},2)) AS max_2digit
                        FROM {$g5['bom_item_table']}
                        WHERE bom_idx = '".$bom_idx."' AND SUBSTRING(bit_reply,1,{$len2}) = '".$reply_pre."'
                ";
                // if($demo) {print_r3($sql);}
                $bit = sql_fetch($sql,1);
                $cat_arr_next = array_search($bit['max_2digit'],$cat_arr)+1;
                // if($demo) {print_r3('cat_arr_next = '.$cat_arr_next);}
                $reply_char = (!$bit['max_2digit']) ? '10' : $reply_char = $cat_arr[$cat_arr_next];
                $reply = $reply_pre.$reply_char;    // $reply (prev $reply value should be compared every time.)
                // if($demo) {print_r3('reply = '.$reply);}

                // bom_bcj_json in bom table update
                update_bom_bct_json($arr['bom_idx_child']);
            }


            // 설비 디비 생성
            $arr['mms_name_array'] = explode(",",$arr['remark']);
            for($j=0;$j<=sizeof($arr['mms_name_array']);$j++) {
                if(is_numeric($arr['mms_name_array'][$j])) {
                    $arr['mms_name_array'][$j] .= '호기';
                }
                // print_r3($arr['mms_name_array'][$j].' > ------------');
                if($arr['mms_name_array'][$j]) {    // in case exists.
                    $ar['table'] = 'g5_1_mms';
                    $ar['com_idx'] = $_SESSION['ss_com_idx'];
                    $ar['imp_idx'] = 27;    // 대표 imp 한개에 일단 전부 연결
                    $ar['imp_name'] = '대표IMP';
                    $ar['mmg_idx'] = 29;
                    $ar['mms_name'] = $arr['mms_name_array'][$j];
                    $ar['mms_model'] = $arr['mms_name_array'][$j];
                    $ar['mms_set_output'] = 'shift';
                    $ar['mms_data_url_host'] = 'daechang.epcs.co.kr';
                    $ar['mms_output_yn'] = 'Y';
                    $ar['mms_sort'] = 1;
                    $ar['mms_status'] = 'ok';
                    $arr['mms_idx'] = update_db($ar);
                    // print_r3($arr['mms_idx'].' - '.$ar['mms_name'].'----------------------------------------------------------------------------');
                    unset($ar);

                    // 설비-작업자 연결 day
                    // print_r3($arr['day_mmses']);
                    // print_r3($arr['day_ids']);
                    for($x=0;$x<@sizeof($arr['day_ids']);$x++) {
                        // print_r3($arr['day_mmses'][$x]);
                        // print_r3($arr['day_ids'][$x]);
                        // mmses 번호가 있는 것은 해당 번호랑 매칭해야 함, 설비번호 순서가 다를 수 있음
                        if($arr['day_mmses'][$x]) {
                            $sql = " SELECT mms_idx FROM {$g5['mms_table']} WHERE mms_name = '".$arr['day_mmses'][$x]."호기' ";
                            // print_r3($sql);
                            $one = sql_fetch($sql);
                            $arr['mms_idx_day'] = $one['mms_idx'];
                            // print_r3($arr['mms_idx']);
                        }
                        $ar['mb_id'] = $arr['day_ids'][$x];
                        $ar['bom_idx'] = $arr['bom_idx_child'];
                        $ar['mms_idx'] = $arr['mms_idx_day'] ? $arr['mms_idx_day']:$arr['mms_idx'];
                        $ar['bmw_status'] = 'ok';
                        // print_r3($ar);
                        update_bom_mms_worker($ar);
                        unset($ar);
                    }
                    // night
                    // print_r3($arr['night_mmses']);
                    // print_r3($arr['night_ids']);
                    for($x=0;$x<@count($arr['night_ids']);$x++) {
                        // print_r3($arr['night_mmses'][$x]);
                        // print_r3($arr['night_ids'][$x]);
                        // print_r3($x.'===');
                        // mmses 번호가 있는 것은 해당 번호랑 매칭해야 함, 설비번호 순서가 다를 수 있음
                        if($arr['night_mmses'][$x]) {
                            $sql = " SELECT mms_idx FROM {$g5['mms_table']} WHERE mms_name = '".$arr['night_mmses'][$x]."호기' ";
                            // print_r3($sql);
                            $one = sql_fetch($sql);
                            $arr['mms_idx_night'] = $one['mms_idx'];
                            // print_r3($arr['mms_idx']);
                        }
                        $ar['mb_id'] = $arr['night_ids'][$x];
                        $ar['bom_idx'] = $arr['bom_idx_child'];
                        $ar['mms_idx'] = $arr['mms_idx_night'] ? $arr['mms_idx_night']:$arr['mms_idx'];
                        $ar['bmw_type'] = 'night';
                        $ar['bmw_status'] = 'ok';
                        // print_r3($ar);
                        update_bom_mms_worker($ar);
                        unset($ar);
                    }
                    // sub
                    // print_r3($arr['sub_mmses']);
                    // print_r3($arr['sub_ids']);
                    for($x=0;$x<@sizeof($arr['sub_ids']);$x++) {
                        // print_r3($arr['sub_mmses'][$x]);
                        // print_r3($arr['sub_ids'][$x]);
                        // mmses 번호가 있는 것은 해당 번호랑 매칭해야 함, 설비번호 순서가 다를 수 있음
                        if($arr['sub_mmses'][$x]) {
                            $sql = " SELECT mms_idx FROM {$g5['mms_table']} WHERE mms_name = '".$arr['sub_mmses'][$x]."호기' ";
                            // print_r3($sql);
                            $one = sql_fetch($sql);
                            $arr['mms_idx_sub'] = $one['mms_idx'];
                            // print_r3($arr['mms_idx']);
                        }
                        $ar['mb_id'] = $arr['sub_ids'][$x];
                        $ar['bom_idx'] = $arr['bom_idx_child'];
                        $ar['mms_idx'] = $arr['mms_idx_sub'] ? $arr['mms_idx_sub']:$arr['mms_idx'];
                        $ar['bmw_type'] = 'sub';
                        $ar['bmw_status'] = 'ok';
                        // print_r3($ar);
                        update_bom_mms_worker($ar);
                        unset($ar);
                    }
                }
            }


            // NE 차종에서 1차 조립인 경우 대표제품으로 봄
            $arr['bit_main_yn'] = ($arr['bct_name']=='NE'&&$arr['remark']=='1차조립') ? 1:0;

            $arr['bom_idx'] = $bom_idx;
            $arr['bit_num'] = $num;
            $arr['bit_reply'] = stripslashes($reply);
            $arr['bit_count'] = $arr['bom_usage'];
            // if($demo) {print_r3($arr);}
            // 구조 입력
            update_bom_item($arr);
            // unset($arr); // resetted at the top.

            // 차종 (없는 경우가 있어서 과거값을 저장)
            $bct_name = $arr['bct_name'];

        }

        $idx++; 
    }
    else {continue;}


    // 메시지 보임
    if(preg_match("/[-0-9A-Z]/",$arr['bom_part_no'])) {
        echo "<script> document.all.cont.innerHTML += '".$idx
                .". ".$arr['bct_name']." [".$arr['bom_part_no']."]: ".$arr['bom_name']
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
// ==============================================================================
// 두번째 시트
// for($i=0;$i<=sizeof($allData[1]);$i++) {
//     print_r3($allData[1][$i]);
// }


//계층구조를 확인할 수 있는 뷰테이블을 기존테이블 있으면 삭제하고 다시 생성
$drop_v_sql = " DROP VIEW {$g5['v_bom_item_table']} ";
@sql_query($drop_v_sql);

$create_v_sql = " CREATE VIEW IF NOT EXISTS {$g5['v_bom_item_table']} 
    AS
    SELECT bom.bom_idx
        , cst_idx_provider
        , bom.bom_name
        , bom_part_no
        , bom_type
        , bom_price
        , bom_status
        , cst_name
        , bit.bit_idx
        , bit.bom_idx AS bom_idx_product
        , bit.bit_main_yn
        , bit.bom_idx_child
        , bit.bit_reply
        , bit.bit_count
    FROM {$g5['bom_item_table']} AS bit
        LEFT JOIN {$g5['bom_table']} bom ON bom.bom_idx = bit.bom_idx_child
        LEFT JOIN {$g5['customer_table']} cst ON cst.cst_idx = bom.cst_idx_provider
    ORDER BY bit.bom_idx, bit.bit_reply
";
@sql_query($create_v_sql);


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