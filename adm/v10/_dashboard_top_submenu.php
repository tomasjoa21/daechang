<?php
// 대시보드 공통 파일 (대시보드에 공통 include)
// meta 테이블에서 default 대시보드를 찾아서 할당
$tmta_sql = " SELECT mta_idx,mta_value,mta_title,mta_number FROM {$g5['meta_table']} 
                WHERE mta_db_table = 'member' 
                    AND mta_db_id = '{$member['mb_id']}'
                    AND mta_key = 'dashboard_menu'
                ORDER BY mta_number
";
// print_r3($tmta_sql);
$tmresult = sql_query($tmta_sql,1);
$sub_menus = array();
$sub_menu_titles = array();

// 없으면 생성
if(!$tmresult->num_rows){
    // 메타 정보 입력
    $ar['mta_db_table'] = 'member';
    $ar['mta_db_id'] = $member['mb_id'];
    $ar['mta_key'] = 'dashboard_menu';
    $ar['mta_value'] = '915110';
    $ar['mta_title'] = '대시보드';
    meta_update($ar);
    unset($ar);

    $sql = " SELECT mta_idx,mta_value,mta_title,mta_number FROM {$g5['meta_table']} 
        WHERE mta_db_table = 'member' 
            AND mta_db_id = '{$member['mb_id']}'
            AND mta_key = 'dashboard_menu'
        ORDER BY mta_number
    ";
    // print_r3($tmta_sql);
    $tmresult = sql_query($tmta_sql,1);
}
for($i=0;$tmrow=sql_fetch_array($tmresult);$i++){
    $sub_menus[$tmrow['mta_idx']] = $tmrow['mta_value'];    // 메뉴코드(915110...)
    $sub_menu_titles[$tmrow['mta_idx']] = $tmrow['mta_title'];  // 대시보드명
}
// print_r3($sub_menus);
// print_r3($sub_menu_titles);

$cur_mta_idx = 0;
if($tmresult->num_rows && !$idx) {
    // print_r3(2);
    // print_r3(key($sub_menus));
    $sub_menu = $sub_menus[key($sub_menus)];
    $cur_mta_idx = array_search($sub_menu,$sub_menus);
}
else if($tmresult->num_rows && $idx) {
    // print_r3($idx);
    $sub_menu = $sub_menus[$idx];
    $cur_mta_idx = array_search($sub_menu,$sub_menus);
    // print_r3($sub_menu);
    // 메뉴가 사라진 경우이므로 새로고침해야 함
    if(!$sub_menu) {
        goto_url(G5_USER_ADMIN_URL);
    }
}
else {
    // print_r3(4);
    $sub_menu = ($menu915_last)?$menu915_last[0]:'915900';
    $cur_mta_idx = array_search($sub_menu,$sub_menus);
}
// print_r3($cur_mta_idx);

$sub_menu_title = ($sub_menu_titles[$cur_mta_idx]) ? '<span clas="ds_ttl">'.$sub_menu_titles[$cur_mta_idx].'</span>'
                                                        :'<span clas="ds_ttl">대시보드</span>';
// 타이틀 생성
$g5['title'] = $sub_menu_title.((!$sub_menu||!$cur_mta_idx) ?: '<i class="fa fa-cogs ds_edit_btn" aria-hidden="true" style="margin-left:10px;cursor:pointer;display:none;"></i>');
// $sub_menu = '915110';