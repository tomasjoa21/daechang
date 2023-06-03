<?php 
echo PHP_EOL; 
//그리드간의 간격
$padding = 10;
?>
<style>
@charset "utf-8";

.dash_empty{text-align:center;border:1px solid #354667;background:#0c162a;padding:200px 0;color:#555;}

/* ---- pkr ---- */
.pkr {max-width: 100%;}

/* clear fix */
.pkr:after {content: '';display: block;clear: both;}

/* ---- .pkr-item ---- */
.pkr-sizer,.pkr-item {
  <?php if($g5['setting']['set_pkr_size']) {
  echo 'width:'.$g5['set_pkr_size_value'][1].'%;'.PHP_EOL;
  } else { ?>
  width: 25%;
  <?php } ?>
}

.pkr-item {
  <?php if($g5['setting']['set_pkr_size']) {
  echo 'padding-bottom:'.$g5['set_pkr_size_value'][1].'%;'.PHP_EOL;
  } else { ?> 
  padding-bottom: 25%;
  <?php } ?>
  float: left;position:relative;
}

.pkr-item .grid_edit{position:absolute;top:<?=($padding*2)?>px;cursor:pointer;font-size:1.4em;color:#888;display:none;}
.pkr-item .grid_mod{left:<?=($padding*2)?>px;}
.pkr-item .grid_mod:hover{color:yellow;}
.pkr-item .grid_mod.focus{color:yellow;}
.pkr-item .grid_del{right:<?=($padding*2)?>px;}
.pkr-item .grid_del:hover{color:orange;}
.pkr-item .pkr-cont{position:absolute;
  top:<?=$padding?>px;bottom:<?=$padding?>px;left:<?=$padding?>px;right:<?=$padding?>px;
  /* background-image:url(https://icongr.am/clarity/line-chart.svg?size=148&color=2f426c); */
  background-position: center center;background-repeat:no-repeat;
}
.pkr-item .pkr-cont.focus{border:2px solid yellow;}

<?php if($g5['setting']['set_pkr_size']) {
foreach($g5['set_pkr_size_value'] as $pkr_k => $pkr_v){
  echo '.pkr-item-w'.$pkr_k.'{width:'.$pkr_v.'%;}'.PHP_EOL;
  echo '.pkr-item-h'.$pkr_k.'{padding-bottom:'.$pkr_v.'%;}'.PHP_EOL;
}
} else { ?>

.pkr-item-w1 { width: 25%; }
.pkr-item-w2 { width: 50%; }
.pkr-item-w3 { width: 75%; }
.pkr-item-w4 { width: 100%; }

.pkr-item-h1 { padding-bottom: 25%; }
.pkr-item-h2 { padding-bottom: 50%; }
.pkr-item-h3 { padding-bottom: 75%; }
.pkr-item-h4 { padding-bottom: 100%; }

<?php } ?>

.packery-drop-placeholder {border: 3px dotted #333;background: hsla(0, 0%, 0%, 0.3);}
.pkr-item.is-dragging,.pkr-item.is-positioning-post-drag {z-index: 2;}

.pkr-btn{position:absolute;top:0;left:0;z-index:1;width:50px;height:50px;background:#000;}

.ds_edit_btn{color:#aaa;}
.ds_edit_btn:hover{color:#ddd;}
.ds_edit_btn.focus{color:yellow;}
.bs_edit{display:none;}

/* 대시보드 모달 */
#dsm{position:fixed;top:0;left:0;width:100%;height:100%;z-index:4000;display:flex;justify-content:center;align-items:center;display:none;}
#dsm #dsm_bg{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:0;}
#dsm #dsm_box{position:relative;z-index:1;width:100%;height:100%;max-width:800px;max-height:800px;border:1px solid #555;background:rgba(0,0,0,0.6);box-shadow:0 0 10px rgba(255,255,255,0.4);}
#dsm #dsm_box #dsm_close{position:absolute;cursor:pointer;top:-50px;right:-50px;}
#dsm #dsm_box #dsm_cont{}
</style>
<?php echo PHP_EOL; ?>