<?php
// http://daechang.epcs.co.kr/adm/v10/dashboard/mms.php?w=1&h=1
include_once('./_common.php');

$g5['title'] = 'UPH';
include_once('./_head.sub.php');

// st_date, en_date
$st_date = $st_date ?: date("Y-m-d",G5_SERVER_TIME);
$st_time = $st_time ?: '00:00:00';
$en_time = $en_time ?: '23:59:59';
$st_datetime = $st_date.' '.$st_time;
$en_datetime = $st_date.' '.$en_time;

// 검색일자
$stat_date = $st_date ?: statics_date(G5_TIME_YMDHIS);
// echo $stat_date;



if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/style.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/style.css">', 2);
}
if(is_file(G5_USER_ADMIN_PATH.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css')) {
    add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/'.$g5['dir_name'].'/css/'.$g5['file_name'].'.css">', 2);
}
?>
<style>
.box_header {color:#9c9c9c;margin:2px 6px;}
.box_header:after {display:block;visibility:hidden;clear:both;content:'';}
.box_header .top_left {float:left;}
.box_header .top_right {float:right;}
.box_body {color:white;text-align: center;border:0px solid red;}
.box_body:after {display:block;visibility:hidden;clear:both;content:'';}
.box_body p {color:#c3c393;font-size:3.7em;font-weight:550;}
.box_footer {position:fixed;bottom:0;width:100%;color:#9c9c9c;text-align:center;padding:2px 0px 10px;border:0px solid blue;}
.box_footer:after {display:block;visibility:hidden;clear:both;content:'';}
</style>
<div class="box_header">
    <div class="top_left">
        <p class="title_main"><?=G5_TIME_YMD?> (<?=$g5['week_names'][date("w",G5_SERVER_TIME)]?>)</p>
    </div>
    <div class="top_right">
        <p><a href="javascript:" class="btn_reload"><i class="fa fa-repeat"></i></a></p>
    </div>
</div>
<div class="box_body">
    <p>52.6</p>
</div>
<div class="box_footer">
    <p>47호기</p>
</div>

<script>
$(document).on('click','.btn_reload',function(){
    self.location.reload();
});
</script>

<?php
include_once ('./_tail.sub.php');
?>
