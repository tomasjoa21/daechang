<?php
// $sub_menu = '915110';
include_once('./_common.php');

$g5['title'] = '대시보드';
include_once ('./_head.php');
//$sub_menu : 현재 메뉴코드 915140
//$cur_mta_idx : 현재 메타idx 422

$demo = 0;  // 데모인 경우 1로 설정하세요. (packery 박스가 맨 위에 떠 있어서 디버깅 데이터를 가려버리네요.)
// h1=84, h2=93.9%, h3=95.9%, h4=97%, h5=97.6%, h6=98%
$cont_head = '
<div class="widget_title">
    <span>생산량</span>
    <a href="javascript:" class="chart_setting"><i class="fa fa-gear"></i></a>
</div>
';
$cont = '
<div class="widget_title">
    <span>생산량</span>
    <a href="javascript:" class="chart_setting"><i class="fa fa-gear"></i></a>
</div>
<div class="widget_content">content</div>
';
// $cont = trim($cont, "\n");
$cont_head = str_replace("\n", "", $cont_head);
$cont = str_replace("\n", "", $cont);
?>
<script src="<?=G5_USER_URL?>/temp/node_modules/gridstack/dist/gridstack-all.js"></script>
<link href="<?=G5_USER_URL?>/temp/node_modules/gridstack/dist/gridstack.min.css" rel="stylesheet"/>
<link href="<?=G5_USER_URL?>/temp/node_modules/gridstack/dist/gridstack-extra.min.css" rel="stylesheet"/>
<style type="text/css">
/* .grid-stack { background: #FAFAD2; } */
.grid-stack-item-content { background-color: #1f1f20; }
.grid-stack>.grid-stack-item>.grid-stack-item-content {overflow-y: hidden;}
.widget_title {position:relative;padding:2px 4px 3px;background-color:#1d263a;border-bottom:solid 2px #040816;}
.widget_title .chart_setting {position:absolute;right:5px;top:0;}
.widget_content {padding:2px 4px 3px;height:84.3%;border:solid 0px red;}
.grid-stack-item[gs-h="2"] .widget_content {height:92.5%;}
.grid-stack-item[gs-h="3"] .widget_content {height:95.1%;}
.grid-stack-item[gs-h="4"] .widget_content {height:96.3%;}
.grid-stack-item[gs-h="5"] .widget_content {height:97.1%;}
.grid-stack-item[gs-h="6"] .widget_content {height:97.5%;}
.widget_content iframe {width:100%;height:100%;}
</style>

<div class="grid-stack">
</div>

<textarea id="saved-data" cols="100" rows="20" readonly="readonly"></textarea>

<script type="text/javascript">
var items = [
  {w: 2, h: 1, noResize: true, content: '<?=$cont_head?><div class="widget_content"><iframe id="frame_02" src="<?=G5_USER_ADMIN_URL?>/dashboard/uph.php?w=1&h=1" frameborder="0" scrolling="no"></iframe></div>'},
  {w: 2, h: 1, noResize: true, content: '<?=$cont_head?><div class="widget_content"><iframe id="frame_02" src="<?=G5_USER_ADMIN_URL?>/dashboard/production.php?w=1&h=1" frameborder="0" scrolling="no"></iframe></div>'},
  {w: 2, h: 1, noResize: true, content: '<?=$cont_head?><div class="widget_content"><iframe id="frame_02" src="<?=G5_USER_ADMIN_URL?>/dashboard/uph.php?w=1&h=1" frameborder="0" scrolling="no"></iframe></div>'},
  {w: 5, h: 2, noResize: true, content: '<?=$cont?>'}
];
var options = {
    column:6,
    handle:'.widget_title',
    // removable: '.li_dash_submenu' // .trash .li_dash_submenu #ul_dash_submenu(이건 되네!)
    removable: true
};
var grid = GridStack.init(options);
// var grid = GridStack.init();
grid.load(items);

grid.on('drag', function (e, ui) {
    grid.compact();
});
grid.on('dragstop', function (e, ui) {
    saveGrid();
});
grid.on('removed', function(e, nodes) {
  nodes.forEach(function(node) {
    console.log($(this));
    // console.log($(this).context.baseURI);
    // console.log(e);
    console.log(node);
  });
  grid.compact();
});

function saveGrid() {
    serializedData = grid.save();
    $('#saved-data').val( JSON.stringify(serializedData, null, '  ') );
}
</script>



<?php
include_once ('./_tail.php');
?>
