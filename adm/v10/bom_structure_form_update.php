<?php
$sub_menu = "940120";
include_once("./_common.php");

auth_check($auth[$sub_menu], 'w');

// print_r2($_POST);

// 초기값 정의 (외부 함수들에서 사용)
$g5['bit']['num'] = array();
$g5['bit']['reply'] = array();
$g5['bit_num'] = 0;


$data = json_decode(stripslashes($_POST['serialized']),true);
// print_r2(stripslashes($_POST['serialized']));
// echo '<br>';
// print_r2($data);
// exit;
function create_categories(&$arr, $parent_id=0) {
    global $g5,$bom_idx_arr;
    
    foreach($arr as $key => $item) {
		//id값이 셋팅되어 있지 않으면 빈값이므로 건너띈다.
        if(!array_key_exists('id',$item)) continue;
		
        $item['parent_id'] = $parent_id;
        $list = array();
        $list = $item;
        unset($list['children']);   // 서브까지 다 보이면 복잡해서 숨김
        $list['bit_reply'] = get_bom_reply($list['id'], $list['parent_id'], $list['depth']);
        $list['bom_idx'] = $_POST['bom_idx'];   // 넘겨받은 bom_idx
        $list['bit_idx'] = update_bom_item($list);
        $g5['bit_idxs'][] = $list['bit_idx'];   // 삭제를 위한 배열
        // print_r2($list);
        // print_r2($g5['bit']['reply']);    // global 공통 배열 변수(함수 내부에서 공통사용)

        // bom_bcj_json in bom table update
        update_bom_bct_json($list['bom_idx_child']);
        $bom_idx_arr[] = $list['bom_idx_child'];    // 체크를 위해서 배열 저장

        // 하위가 있으면 재귀함수
        if(isset($item['children'])){
            create_categories($item['children'], $list['id']);
        }
    }
}
create_categories($data, 0);

// 항목 전부 삭제인 경우
if(!sizeof($data)) {
    $g5['bit_idxs'][] = 0;
}


// 리스트에서 사라진 항목 디비에서 삭제처리
if(is_array($g5['bit_idxs'])) {
    $sql_bit_idx = " AND bit_idx NOT IN (".implode(',',$g5['bit_idxs']).") ";
    $sql = " SELECT * FROM {$g5['bom_item_table']} WHERE bom_idx = '".$_POST['bom_idx']."' {$sql_bit_idx} ";
    // echo $sql.BR;
    $rs = sql_query($sql,1);
    for($i=0;$row=sql_fetch_array($rs);$i++) {
        // print_r2($rs);
        update_bom_bct_json($row['bom_idx_child']); // bom_bct_json 업데이트 후..
        $sql = "DELETE FROM {$g5['bom_item_table']} WHERE bom_idx = '".$_POST['bom_idx']."' {$sql_bit_idx} ";   // 디비 삭제
        sql_query($sql,1);
    }
}


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

// print_r2($bom_idx_arr);
$msg = '';
for($i=0;$i<sizeof($bom_idx_arr);$i++) {
    $bom = get_table('bom','bom_idx',$bom_idx_arr[$i]);
    // print_r2($bom);
    if($bom['bom_type']=='product') {
        $msg = 'BOM 하위 구조에 완성품(product)이 포함되어 있습니다.\n확인하시고 반드시 수정해 주세요.';
    }
}

// exit;
$qstr .= '&sca='.$sca.'&file_name='.$file_name; // 추가로 확장해서 넘겨야 할 변수들

if($msg) {
    alert($msg,'./bom_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
    exit;
}
goto_url('./bom_list.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
// goto_url('./'.$fname.'_form.php?'.$qstr.'&w=u&'.$pre.'_idx='.${$pre."_idx"}, false);
?>
