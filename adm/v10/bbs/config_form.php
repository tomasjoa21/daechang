<?php
include_once('./_common.php');
$sub_menu = $board['bo_1'];

$g5['title'] = $board['bo_subject'].' 환경설정';
include_once('./_head.php');
/*
// bo_7=>longtext 환경설정 확장변수로 사용하기 위함
$q = sql_query( 'DESCRIBE '.$g5['board_table'] );
while($row = sql_fetch_array($q)) {
    if($row['Field']=='bo_7' && $row['Type']=='varchar(255)') {
        //echo $row['Field'].' - '.$row['Type'].'<br>';
        sql_query(" ALTER TABLE `{$g5['board_table']}` CHANGE `bo_7` `bo_7` longtext ", true);
    }
}
*/

// print_r2($board_skin_url);exit;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style2.css">', 0);
?>

<div class="local_desc01 local_desc" style="display:no ne;">
    <ol>
        <li>게시판 관련 추가적인 설정(여분필드)을 하는 페이지입니다. 활용하실 여분 필드만 설정해 주시면 되겠습니다.</li>
        <li>기본적인 설정들은 게시판 목록 > 해당 게시판 수정 페이지에서 설정해 주세요.</li>
    </ol>
</div>

<form name="form01" class="config_form" action="./config_form_update.php" onsubmit="return form01_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">


<div id="anc_basic" class="tbl_frm01 tbl_wrap">
	<h2 class="h2_frm">기본설정</h2>
	<?php echo $pg_anchor ?>
	<table>
		<colgroup>
			<col class="grid_4" style="width:5%;">
			<col style="width:25%;">
			<col style="width:70%;">
		</colgroup>
        <thead>
            <th style="text-align:center;">번호</th>
            <th style="text-align:center;">설정요약</th>
            <th style="text-align:center;">설정상세내용</th>
        </thead>
		<tbody>
        <?php for($i=1;$i<=10;$i++){ ?>
        <tr>
            <td><?=$i?></td>
            <td scope="row">
                <input type="text" name="bo_<?=$i?>_subj" value="<?php echo ($i==1)?'관리자단메뉴코드':$board['bo_'.$i.'_subj'] ?>" style="width:55%;background:#2f343e;"<?=(($i==1)?' readonly':'')?> class="frm_input<?=(($i==1)?' readonly':'')?>">&nbsp;( bo_<?=$i?>_subj )
            </td>
            <td scope="row">
                
                <input type="text" name="bo_<?=$i?>" value="<?php echo $board['bo_'.$i] ?>"<?=(($i==1)?' readonly':'')?> style="width:85%;background:#2f343e;" class="frm_input<?=(($i==1)?' readonly':'')?>">&nbsp;( bo_<?=$i?> )
            </td>
        </tr>
        <?php } ?>
		</tbody>
	</table>
</div>
<div id="anc_basic" class="tbl_frm01 tbl_wrap">
	<h2 class="h2_frm">추가설정</h2>
    <p class="local_desc01 local_desc">여기에 추가하는 <strong style="color:#ff0000;">name</strong> 설정값의 변수에는 반드시 "<strong style="color:orange;">bo_adm_xxxx</strong>" 와 같이 "<strong style="color:yellow;">bo_adm_</strong>" 으로 시작하도록 작성해 주세요.</p>
	<?php echo $pg_anchor ?>
	<table>
		<colgroup>
			<col class="grid_4" style="width:5%;">
			<col style="width:25%;">
			<col style="width:70%;">
		</colgroup>
		<tbody>
        <tr>
            <td>11</td>
            <td scope="row">
                <input type="text" value="관리자단 스킨선택" style="width:55%;background:#2f343e;" readonly class="frm_input readonly">
            </td>
            <td scope="row">
            <?php echo get_skin_adm_select('board', 'bo_adm_skin', 'bo_adm_skin', $board['bo_adm_skin']); ?>
            &nbsp;( bo_adm_skin )
            </td>
        </tr>
		</tbody>
	</table>
</div>

<div class="btn_fixed_top">
    <a href="./board.php?bo_table=<?=$bo_table?>" class="btn btn_02">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
</div>
</form>


<script>
$(function(e){

});
</script>

<script>
function form_submit(f)
{

	document.getElementById("btn_submit").disabled = "disabled";

	return true;
}
</script>


<?php
include_once('./_tail.php');
?>