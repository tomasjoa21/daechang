<div id="modal" class="modal mdl_hide">
    <div class="mdl_bg"></div>
    <div class="mdl_box">
        <?=svg_icon('close','mdl_close',50,50)?>
        <div class="mdl_head">
            <h1 class="mdl_title"><strong class="mdl_st_ttl"></strong> 발주QR코드</h1>
        </div>
        <div class="mdl_cont">
            <div class="mdl_qr_img_box"></div>
            <p><span>발주ID : </span><strong class="mdl_moi_idx"></strong></p>
            <p><span>제품품번 : </span><strong class="mdl_bom_part_no"></strong></p>
            <p><span>발주수량 : </span><strong class="mdl_moi_count"></strong></p>
        </div>
        <div class="mdl_tail">
            <a href="#" class="btn btn_05 mdl_qr_download">다운로드</a>
        </div>
    </div>
</div>