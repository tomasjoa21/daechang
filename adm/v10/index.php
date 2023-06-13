<?php
// $sub_menu = '915110';
include_once('./_common.php');

$g5['title'] = '대시보드';
include_once ('./_head.php');
//$sub_menu : 현재 메뉴코드 915140
//$cur_mta_idx : 현재 메타idx 422

$demo = 0;  // 데모인 경우 1로 설정하세요. (packery 박스가 맨 위에 떠 있어서 디버깅 데이터를 가려버리네요.)
?>
<script src="<?=G5_USER_URL?>/temp/node_modules/gridstack/dist/gridstack-all.js"></script>
<link href="<?=G5_USER_URL?>/temp/node_modules/gridstack/dist/gridstack.min.css" rel="stylesheet"/>
<style type="text/css">
  .grid-stack { background: #FAFAD2; }
  .grid-stack-item-content { background-color: #18BC9C; }
</style>

<div class="grid-stack">
    <div class="grid-stack-item">
        <div class="grid-stack-item-content">Item 1</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-h="2">
        <div class="grid-stack-item-content">Item 2 wider</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item" gs-w="2" gs-no-resize="true">
        <div class="grid-stack-item-content">title</div>
    </div>
    <div class="grid-stack-item">
        <div class="grid-stack-item-content">Item 1</div>
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
GridStack.init({columnCount: 4});
</script>



<?php
include_once ('./_tail.php');
?>
