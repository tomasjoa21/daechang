<?php
$sub_menu = "910110";
include_once('./_common.php');

if(!$member['mb_manager_yn']) {
    alert('메뉴에 접근 권한이 없습니다.');
}

function column_char($i) { return chr( 65 + $i ); }


// 각 항목 설정
$headers = array('테이블명','테이블ID','컬럼명','컬럼ID','Datatype','PK','FK','NULL허용','비고');
$widths  = array(18, 30, 15, 30, 20, 20, 20, 20, 20);
$header_bgcolor = 'FFABCDEF';
$last_char = column_char(count($headers) - 1);

// 엑셀 데이타 출력
include_once(G5_LIB_PATH.'/PHPExcel.php');

$sql = " show tables ";
// echo $sql.'<br>';
$rs = sql_query($sql,1);
for($i=0;$row=sql_fetch_array($rs);$i++) {
//    print_r2($row);
    $row['db_table'] = $row['Tables_in_'.G5_MYSQL_DB];

    // 스킵해야할 디비(환경설정에 정의)
    if(in_array($row['db_table'],$g5['set_db_table_skip'])) {
        continue;
    }

    // $sql2 = " desc `".$row['db_table']."` ";
    $sql2 = " show full columns from `".$row['db_table']."` ";
    // echo $sql2.'<br>';
    $result = sql_query($sql2,1);
    while($fields = sql_fetch_array($result)) {
        // print_r2($fields);
        // 테이블명
        $row['db_table_name'] = $g5['set_db_table_name_value'][$row['db_table']] ?
                                    $g5['set_db_table_name_value'][$row['db_table']]
                                    : $row['db_table']
        ;

        // // 컬럼명
        $row['db_field_name'] = $fields['Comment'] ? $fields['Comment'] : $fields['Field'];

        // PK
        $fields['Key'] = preg_match("/PRI/",$fields['Key']) ? 'Y':'';
        
        // Null
        $fields['Null'] = preg_match("/NO/i",$fields['Null']) ? 'not null':'null';
        
        // 비고. 비밀번호인 경우
        // $fields['Extra'] = preg_match("/pass/",$fields['Field']) ? 'sha256':'';

        
        $rows[] = array($row['db_table_name']
                      , $row['db_table']
                      , $row['db_field_name']
                      , $fields['Field']
                      , $fields['Type']
                      , $fields['Key']
                      , ' '
                      , $fields['Null']
                      , $fields['Extra']
                  );
    }
    
}
// print_r2($rows);
// exit;


$data = array_merge(array($headers), $rows);

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0)->getStyle( "A1:${last_char}1" )->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($header_bgcolor);
$excel->setActiveSheetIndex(0)->getStyle( "A:$last_char" )->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
foreach($widths as $i => $w) $excel->setActiveSheetIndex(0)->getColumnDimension( column_char($i) )->setWidth($w);
$excel->getActiveSheet()->fromArray($data,NULL,'A1');

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"db_tables-".date("ymdHi", time()).".xls\"");
header("Cache-Control: max-age=0");

$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');