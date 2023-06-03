<?php if (!defined('_GNUBOARD_')) exit; ?>
<?php if($result->num_rows){ ?>
<script>
var pos_json = '<?=$pos_json?>';
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

// init layout with saved positions
$pkr.packery( 'initShiftLayout', pos_json, 'dsg_idx' );

// make draggable
$pkr.find('.pkr-item').each( function( i, itemElem ) {
    var draggie = new Draggabilly( itemElem );
    $pkr.packery( 'bindDraggabillyEvents', draggie );
});


// save drag positions on event
$pkr.on( 'dragItemPositioned', function(e) {
    // save drag positions
    var positions = $pkr.packery( 'getShiftPositions', 'dsg_idx' );
    //그리드가 최소 2개이상일때 위치이동 이벤트 및 위치저장이 적용된다.
    if(positions.length > 1){
        // var pos_json_str = JSON.stringify(positions);
        var pos_json_str = JSON.stringify(positions);
        var ajax_url = g5_user_admin_ajax_url+'/grid_sort_save.php';
        var mta_idx = <?=$cur_mta_idx?>;
        // console.log(pos_json_str);
        $.ajax({
            type: 'POST',
            url: ajax_url,
            // dataType: 'text',
            timeout: 30000,
            data: {'mta_idx': mta_idx,'pos_json_str': JSON.parse(pos_json_str)},
            success: function(res){
                // console.log(res);
                location.reload();
            },
            error: function(req){
                alert('Status: ' + req.status + ' \n\rstatusText: ' + req.statusText + ' \n\rresponseText: ' + req.responseText);
            }
        });
    }//if(positions.length > 1)
});
</script>
<?php } ?>

