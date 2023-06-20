<?php
// 호출페이지들
// /adm/v10/applicant_list.php: 지원자관리 - 검색
// /adm/v10/manager_form.php: 관리자등록시 회원검색
// /adm/v10/employee_form.php
// /adm/v10/maintain_form.php
// /adm/v10/mms_worker_form.php
// /adm/v10/shipment_form.php
// /adm/v10/item_stock_list.php
// /adm/v10/item_today_list.php
// /adm/v10/item_form.php
// /adm/v10/material_form.php
// /adm/v10/material_list.php
// /adm/v10/production_item_count_form.php
// /adm/v10/production_item_count_list.php
include_once('./_common.php');

if($member['mb_level']<4)
	alert_close('접근할 수 없는 페이지입니다.');

// 검색어 
$mb_name = isset($_REQUEST['mb_name']) ? clean_xss_tags($_REQUEST['mb_name'], 1, 1) : '';

$html_title = '회원검색';

$g5['title'] = $html_title;
include_once(G5_PATH.'/head.sub.php');

$sql_common = " FROM {$g5['member_table']} ";
$sql_where = " WHERE mb_id <> '{$config['cf_admin']}' AND mb_leave_date = '' AND mb_intercept_date ='' ";

if($item=="mb_id_saler"||$item=="mb_id_worker") {
    $sql_where .= " AND mb_level >= 6 ";
}
else if($item=="mb_id_company") {
    $sql_where .= " AND mb_level = 4 AND mb_4 = '".$_SESSION['ss_com_idx']."' ";
}
else if($item=="mb_id_employee") {
    $sql_where .= " AND mb_4 = '".$_SESSION['ss_com_idx']."' ";
}
else if($item=="mb_id") {
    $sql_where .= " AND mb_level = 2 ";
}

if($mb_name){
    $mb_name = preg_replace('/\!\?\*$#<>()\[\]\{\}/i', '', strip_tags($mb_name));
    $sql_where .= " and (mb_name like '%".sql_real_escape_string($mb_name)."%' or mb_id like '%".sql_real_escape_string($mb_name)."%') ";
}

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common . $sql_where;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "SELECT *
            $sql_common
            $sql_where
        ORDER BY mb_id
        LIMIT $from_record, $rows
";
// echo $sql.'<br>';
$result = sql_query($sql);

$qstr1 = 'mb_name='.urlencode($mb_name).'&file_name='.$file_name.'&item='.$item;
?>

<div id="sch_member_frm" class="new_win scp_new_win">
    <h1>회원선택</h1>

    <form name="fmember" method="get">
    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>" class="frm_input">
    <input type="hidden" name="item" value="<?php echo $item; ?>" class="frm_input">
    <div id="scp_list_find">
        <label for="mb_name" class="sound_only">회원이름</label>
        <input type="text" name="mb_name" id="mb_name" value="<?php echo get_text($mb_name); ?>" class="frm_input required" required size="20" placeholder="이름 or 아이디">
        <input type="submit" value="검색" class="btn_frmline">
    </div>
    <div class="tbl_head01 tbl_wrap new_win_con">
        <table>
        <caption>검색결과</caption>
        <thead>
        <tr>
            <th>회원이름</th>
            <th>회원아이디</th>
            <th>이메일</th>
            <th>선택</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
        ?>
        <tr>
            <td class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
            <td class="td_left"><?php echo $row['mb_id']; ?></td>
            <td class="td_left"><?php echo $row['mb_email']; ?></td>
            <td class="scp_find_select td_mng td_mng_s">
                <button type="button" class="btn btn_03 btn_select"
                    mb_id="<?=$row['mb_id']?>"
                    mb_name="<?=$row['mb_name']?>"
                    mb_nick="<?=$row['mb_nick']?>"
                    mb_hp="<?=$row['mb_hp']?>"
                    mb_tel="<?=$row['mb_tel']?>"
                    mb_email="<?=$row['mb_email']?>"
                    mb_2="<?=$row['mb_2']?>"
                    mb_department="<?=$g5['department_name'][$row['mb_2']]?>"
                    mb_positions="<?=$g5['set_mb_positions_value'][$row['mb_1']]?>"
                >선택</button>
            </td>
        </tr>
        <?php
        }

        if($i ==0)
            echo '<tr><td colspan="4" class="empty_table">검색된 자료가 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
    </form>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr1.'&amp;page='); ?>

</div>

<script>
$('.btn_select').click(function(e){
    e.preventDefault();
    var mb_id = $(this).attr('mb_id');
    var mb_name = $(this).attr('mb_name');
    var mb_nick = $(this).attr('mb_nick');
    var mb_hp = $(this).attr('mb_hp');
    var mb_tel = $(this).attr('mb_tel');
    var mb_email = $(this).attr('mb_email');
    var mb_2 = $(this).attr('mb_2');
    var mb_department = $(this).attr('mb_department');
    var mb_positions = $(this).attr('mb_positions');

    <?php
    if($file_name=='mms_worker_form'||$file_name=='shipment_form'
        ||$file_name=='item_form'||$file_name=='production_item_count_form'
        ||$file_name=='material_form') {
    ?>
        $("input[name=mb_id]", opener.document).val( mb_id );
        $("input[name=mb_name]", opener.document).val( mb_name );
    <?php
    }
    // 지원자관리
    else if($file_name=='applicant_list'||$file_name=='item_stock_list'||$file_name=='item_today_list'||$file_name=='production_item_count_list'
            ||$file_name=='material_list') {
    ?>
        $("input[name=ser_mb_id]", opener.document).val( mb_id );
        $("input[name=ser_mb_name]", opener.document).val( mb_name );
    <?php
    }
    // 관리자 추가인 경우
    else if($file_name=='manager_form') {
        ?>
        window.opener.document.getElementById('reg_mb_id').value = val1;
        window.opener.document.getElementById('mb_name').value = val2;
        window.opener.document.getElementById('reg_mb_nick').value = val4;
		window.opener.document.getElementById('reg_mb_email').value = val_email;
		window.opener.document.getElementById('reg_mb_hp').value = val_hp;
        <?php
    }
    // 사원 추가인 경우
    else if($file_name=='employee_form') {
        ?>
        window.opener.document.getElementById('reg_mb_id').value = mb_id;
        window.opener.document.getElementById('mb_name').value = mb_name;
        window.opener.document.getElementById('reg_mb_nick').value = mb_nick;
		window.opener.document.getElementById('reg_mb_email').value = mb_email;
		window.opener.document.getElementById('reg_mb_hp').value = mb_hp;
		window.opener.document.getElementById('reg_mb_tel').value = mb_tel;
		window.opener.document.getElementById('reg_mb_tel').value = mb_tel;
        $("#mb_password", window.opener.document).remove();
        <?php
    }
    else if($file_name=='maintain_form') {
    ?>
        $("input[name=mb_id]", opener.document).val( mb_id );
        $("input[name=mnt_name]", opener.document).val( mb_name );
    <?php
    }
	// 계약관리 수정
	else if($file_name=='contract_form') {
		if($item=='mb_id') {
    ?>
			$("input[name=mb_id]", opener.document).val( mb_id );
			$("input[name=mb_name]", opener.document).val( mb_name );
		<?php
		}
		else if ($item=='mb_id_saler') {
		?>
			$("input[name=mb_id_saler]", opener.document).val( mb_id );
			$("input[name=mb_name_saler]", opener.document).val( mb_name );
    <?php
		}
    }
    // 게시판 글쓰기
    else if($file_name=='write'||$file_name=='error_code_form') {
    ?>
        $("input[name=com_idx]", parent.document).val( com_idx );
        $("input[name=com_name]", parent.document).val( com_name );
        $("input[name=mb_id]", parent.document).val( mb_id );
        $("input[name=mb_name]", parent.document).val( mb_name );
        $("input[name=imp_idx]", parent.document).val( imp_idx );
        $("input[name=imp_name]", parent.document).val( imp_name );
        $("#mms_info", parent.document).hide();
    <?php
    }
    else if($file_name=='manager_auth_setting') {
    ?>
        $("input[name=mb_id_saler]", opener.document).val( mb_id );
    <?php
    }
    ?>

    // 창닫기
    window.close();
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');