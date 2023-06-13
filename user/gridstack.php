<?php
include_once('./_common.php');
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$g5['title'] = '대창사용자메인';
include_once(G5_USER_PATH.'/_head.php');
?>

<script src="temp/node_modules/gridstack/dist/gridstack-all.js"></script>
<link href="temp/node_modules/gridstack/dist/gridstack.min.css" rel="stylesheet"/>
<style type="text/css">
  .grid-stack { background: #FAFAD2; }
  .grid-stack-item-content { background-color: #18BC9C; }
</style>

<div class="grid-stack">
  <div class="grid-stack-item">
    <div class="grid-stack-item-content">Item 1</div>
  </div>
  <div class="grid-stack-item" gs-w="2">
    <div class="grid-stack-item-content">Item 2 wider</div>
  </div>
</div>


<script type="text/javascript">
// var items = [
//   {content: 'my first widget'}, // will default to location (0,0) and 1x1
//   {w: 1, content: 'another longer widget!'} // will be placed next at (1,0) and 2x1
// ];

// var grid = GridStack.init();
// grid.load(items);
// using serialize data instead of .addWidget()
// const serializedData = [
//   {x: 0, y: 0, w: 2, h: 2},
//   {x: 2, y: 3, w: 3, content: 'item 2'},
//   {x: 1, y: 3}
// ];
// grid.load(serializedData);
GridStack.init();
</script>


<?php
include_once(G5_USER_PATH.'/_tail.php');