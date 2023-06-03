<?php
// 호출 페이지들
// /adm/v10/dashboard_mms_add.php: 대시보드설정 > 설비 추가 팝업 > 업체검색
// /adm/v10/dashboard_mms_group.php: 대시보드설정 > 설비그룹 > 업체검색
// /adm/v10/mms_select.php: 게시판 > AS연락처 > 설비검색 > 업체검색
// /adm/v10/company_member_add.php: 담당자검색
// /adm/v10/company_list.php: 디폴트업체변경
include_once('./_common.php');

if($member['mb_level']<6)
	alert_close('접근할 수 없는 메뉴입니다.');

$where = array();
$where[] = " com_status NOT IN ('trash','delete') ";   // 디폴트 검색조건

// 운영권한이 없으면 자기것만
if (!$member['mb_manager_yn']) {
    $where[] = " mb_id_saler = '".$member['mb_id']."' ";
}

$sql_common = " FROM {$g5['company_table']} AS com 
                LEFT JOIN {$g5['company_saler_table']} AS cms ON cms.com_idx = com.com_idx
"; 

if ($sch_word) {
    switch ($sch_field) {
		case ( $sch_field == 'com_name' ) :
            $where[] = " com_name LIKE '%{$sch_word}%' OR com_names LIKE '%{$sch_word}%' ";
            break;
		case ( $sch_field == 'mb_id' || $sch_field == 'com_idx' ) :
            $where[] = " {$sch_field} = '{$sch_word}' ";
            break;
		case ( $sch_field == 'mb_hp' ) :
			$where[] = " $sch_field LIKE '%".trim($sch_word)."%' ";
            break;
        default :
			$where[] = " $sch_field LIKE '%".trim($sch_word)."%' ";
            break;
    }
}
else 
    $sch_field = 'com_name';

// 최종 WHERE 생성
if ($where)
    $sql_search = ' WHERE '.implode(' AND ', $where);

if (!$sst) {
    $sst = "com_reg_dt";
    $sod = "DESC";
}
$sql_order = " ORDER BY {$sst} {$sod} ";

$rows = $config['cf_page_rows'];
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT SQL_CALC_FOUND_ROWS com.*
		{$sql_common}
		{$sql_search}
        GROUP BY com.com_idx
        {$sql_order}
		LIMIT {$from_record}, {$rows} 
";
//echo $sql;
$result = sql_query($sql,1);
$count = sql_fetch_array( sql_query(" SELECT FOUND_ROWS() as total ") ); 
$total_count = $count['total'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$config['cf_write_pages'] = $config['cf_mobile_pages'] = 5;

// counter display for manager
$total_count_display = ($member['mb_manager_account_yn']) ? ' ('.number_format($total_count).')' : '';

$g5['title'] = '업체 검색'.$total_count_display;
include_once('./_head.sub.php');

$qstr1 = 'frm='.$frm.'&d='.$d.'&sch_field='.$sch_field.'&sch_word='.urlencode($sch_word).'&file_name='.$file_name;
?>
<style>
.td_com_tel, .td_com_president {white-space:nowrap;}
</style>

<div id="sch_target_frm" class="new_win scp_new_win">
    <h1><?php echo $g5['title'];?></h1>

    <form name="ftarget" method="get">
    <input type="hidden" name="frm" value="<?php echo $_GET['frm']; ?>">
    <input type="hidden" name="file_name" value="<?php echo $_GET['file_name']; ?>">
    <input type="hidden" name="d" value="<?php echo $_REQUEST['d']; ?>">

    <div id="scp_list_find">
        <select name="sch_field" id="sch_field">
            <option value="com_name">업체명</option>
            <option value="com_president">대표자</option>
            <option value="mb_name_saler">업체담당자</option>
            <option value="com_idx">업체번호</option>
        </select>
        <script>$('select[name=sch_field]').val('<?php echo $sch_field?>').attr('selected','selected')</script>
        <input type="text" name="sch_word" id="sch_word" value="<?php echo get_text($sch_word); ?>" class="frm_input required" required size="20">
        <input type="submit" value="검색" class="btn_frmline btn btn_10">
        <a href="<?php echo $_SERVER['SCRIPT_NAME']?>?frm=<?php echo $_REQUEST['frm']?>&d=<?php echo $_REQUEST['d']?>" class="btn btn_b10">검색취소</a>
    </div>
    
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th scope="col">업체명</th>
            <th scope="col">대표자</th>
            <th scope="col">대표전화</th>
            <th scope="col">선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
//            print_r2($row);

            // 대표전화
            $row['com_tel'] = substr($row['com_tel'],0,3).'****'.substr($row['com_tel'],-4);
            
            // 회원정보 추출
            $mb1 = get_table_meta('member','mb_id',$row['mb_id']);
            //print_r2($mb1);

        ?>
        <tr>
            <td class="td_com_name"><?php echo $row['com_name']; ?></td>
            <td class="td_com_president"><?php echo $row['com_president']; ?></td>
            <td class="td_com_tel"><?php echo $row['com_tel']; ?></td>
            <td class="td_mng td_mng_s"
                com_idx="<?php echo $row['com_idx']; ?>"
                com_name="<?php echo $row['com_name']; ?>"
                com_tel="<?php echo $row['com_tel']; ?>"
                com_email="<?php echo $row['com_email']; ?>">
                <button type="button" class="btn btn_03 btn_select">선택</button>
            </td>
        </tr>
        <?php
        }
        if($i ==0)
            echo '<tr><td colspan="6" class="empty_table">검색된 자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
    </form>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr1.'&amp;page='); ?>

    <div class="win_btn ">
        <button type="button" onclick="window.close();" class="btn btn-secondary">닫기</button>
    </div>

    <div class="btn_fixed_top">
        <a href="javascript:window.close();" id="member_add" class="btn btn_02">창닫기</a>
    </div>

</div>

<script>
$('.btn_select').click(function(e){
    e.preventDefault();
    
    var com_idx = $(this).closest('td').attr('com_idx');
    // alert(mb_id);return false;
    //-- 디버깅 Ajax --//
    $.ajax({
        url:g5_user_admin_url+'/ajax/company_ss_change.php',
        data:{"aj":"c1","com_idx":com_idx,"mb_id":mb_id},
        dataType:'text', 
        timeout:10000, 
        success:function(res){
            alert(res);
            // reloation opener window
            opener.location.reload();
            window.close();
        },
        error:function(req) {
            alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText 
                + ' \n\rresponseText: ' + req.responseText);
        }
    });
    // window.close();
});
</script>

<?php
include_once('./_tail.sub.php');
?>