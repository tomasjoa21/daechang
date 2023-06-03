<?php
$sub_menu = "920110";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '실시간모니터링';
// include_once('./_top_menu_db.php');
include_once('./_head.php');
// echo $g5['container_sub_title'];


add_stylesheet('<link rel="stylesheet" href="'.G5_USER_ADMIN_URL.'/css/intelli/index.css">', 2);
?>
<style>
</style>

<div class="local_desc01 local_desc" style="display:none;">
    <p>작업중!!</p>
</div>

<div class="div_recommend">
    <div class="title01">
        추천파라미터
        <span class="btn_more">더보기</span>
    </div>
    <?php
    // 각 설비별로 최적값 일단 추출
    if(is_array($g5['set_dicast_mms_idxs_array'])) {
        foreach($g5['set_dicast_mms_idxs_array'] as $k1=>$v1) {
            // echo $k1.'=>'.$v1.'<br>';
            $mms = get_table_meta('mms', 'mms_idx', $v1);  // mms meta 값으로 태그명들이 쭉 들어가 있음 (태그명 표현에서 사용함)
            // print_r2($mms);
            foreach($mms as $k2=>$v2) {
                if(preg_match("/dta_type_label/",$k2)) {
                    // echo $k2.' => '.$v2.'<br>';  // dta_type_label-1-1 => 보온로온도
                    $key_arr = explode("-",$k2);    // 1,2 array will be used.
                    // print_r2($key_arr);
                    $sql = "SELECT *
                            FROM {$g5['data_measure_best_table']}
                            WHERE mms_idx = '".$v1."'
                                AND dta_type = '".$key_arr[1]."' AND dta_no = '".$key_arr[2]."'
                            ORDER BY dmb_reg_dt DESC
                            LIMIT 1
                    ";
                    // echo $sql.'<br>';
                    $one = sql_fetch($sql,1);
                    if($one['dta_value']) {
                        // 태그명
                        $one['tag_name'] = $mms['dta_type_label-'.$one['dta_type'].'-'.$one['dta_no']] ? 
                                                        $mms['dta_type_label-'.$one['dta_type'].'-'.$one['dta_no']]
                                                            : $g5['set_data_type_value'][$one['dta_type']].'-'.$one['dta_no'];
                        // echo $one['tag_name'].'<br>';
                        // print_r2($one);
                        // 최적값 배열 생성
                        $one['tag_value'] = round($one['dta_value'],2);

                        $ar['mms_idx'] = $one['mms_idx'];
                        $ar['dta_type'] = $one['dta_type'];
                        $ar['dta_no'] = $one['dta_no'];
                        $ar['dta_value'] = $one['tag_value'];
                        $ar['dta_name'] = $one['tag_name'];
                        $best[$one['dta_type']][$one['dta_no']][] = $ar;
                    }
    
                }

            }
        }
    }
    // print_r2($best);
    ?>
    <div class="cont01">
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
        <div class="rec_item">
            <p>보온로온도</p>
            <strong>687.4</strong>
            <span>22-07-15 10:00</span>
        </div>
    </div>
</div>





<?php
include_once ('./_tail.php');
?>
