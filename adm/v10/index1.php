<?php
$sub_menu = '915110';
include_once('./_common.php');

$rn_num = get_random_integer(0,14);
// print_r3($rn_num);
$g5['title'] = '대시보드1';
include_once ('./_head.php');
?>
<div class="pkr">
    <div class="pkr-sizer"></div>
    <div class="pkr-item pkr-item-w4 pkr-item-h2" pkr-id="1" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/orange-tree.jpg);">1</div>
    <div class="pkr-item" pkr-id="2" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/submerged.jpg);">2</div>
    <div class="pkr-item" pkr-id="3" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/look-out.jpg);">3</div>
    <div class="pkr-item" pkr-id="4" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/one-world-trade.jpg);">4</div>
    <div class="pkr-item" pkr-id="5" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/drizzle.jpg);">5</div>
    <div class="pkr-item" pkr-id="6" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/golden-hour.jpg);">6</div>
    <div class="pkr-item" pkr-id="7" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/cat-nose.jpg);">7</div>
    <div class="pkr-item" pkr-id="8" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/contrail.jpg);">8</div>
    <div class="pkr-item" pkr-id="9" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/flight-formation.jpg);">9</div>
    <div class="pkr-item" pkr-id="10" style="background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/82/orange-tree.jpg);">10</div>
    <!--
    -->
</div>
<script>
// get JSON-friendly data for items positions
Packery.prototype.getShiftPositions = function( attrName ) {
  attrName = attrName || 'id';
  var _this = this;
  return this.items.map( function( item ) {
    return {
      attr: item.element.getAttribute( attrName ),
      x: item.rect.x / _this.packer.width
    }
  });
};

Packery.prototype.initShiftLayout = function( positions, attr ) {
  if ( !positions ) {
    // if no initial positions, run packery layout
    this.layout();
    return;
  }
  // parse string to JSON
  if ( typeof positions == 'string' ) {
    try {
      positions = JSON.parse( positions );
    } catch( error ) {
      console.error( 'JSON parse error: ' + error );
      this.layout();
      return;
    }
  }
  
  attr = attr || 'id'; // default to id attribute
  this._resetLayout();
  // set item order and horizontal position from saved positions
  this.items = positions.map( function( itemPosition ) {
    var selector = '[' + attr + '="' + itemPosition.attr  + '"]'
    var itemElem = this.element.querySelector( selector );
    var item = this.getItem( itemElem );
    item.rect.x = itemPosition.x * this.packer.width;
    return item;
  }, this );
  this.shiftLayout();
};

// -----------------------------//

// init Packery
var $pkr = $('.pkr').packery({
  itemSelector: '.pkr-item',
  columnWidth: '.pkr-sizer',
  percentPosition: true,
  initLayout: false // disable initial layout
});

// remove item('pkrPosition1') in localStorgae
localStorage.removeItem('pkrPosition1');
// get saved dragged positions
var initPositions = localStorage.getItem('pkrPosition1'); //call the pkrPosition1 from DB 
console.log('init : '+initPositions);
// init layout with saved positions
$pkr.packery( 'initShiftLayout', initPositions, 'pkr-id' );

// make draggable
$pkr.find('.pkr-item').each( function( i, itemElem ) {
  var draggie = new Draggabilly( itemElem );
  $pkr.packery( 'bindDraggabillyEvents', draggie );
});

// save drag positions on event
$pkr.on( 'dragItemPositioned', function(e) {
  // save drag positions
  var positions = $pkr.packery( 'getShiftPositions', 'pkr-id' );
  console.log('save : ' + JSON.stringify(positions));
  localStorage.setItem( 'pkrPosition1', JSON.stringify( positions ) );//save the positions as pkrPosition1 to DB 
});
</script>
<?php
include_once ('./_tail.php');