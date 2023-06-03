<?php
/*
//추가 옵션이 없으면 아래소스는 반드시 작성해야 한다. (필수)
//옵션 요소들 분석해서 추가요소는 인서트, 수정요소는 업데이트, 필요없는 요소는 삭제하는 함수
//새로운 추가옵션이 없기때문에 혹시 기존에 추가옵션 데이터가 DB 에 있을 경우 전부 삭제한다.
bpwidget_option_update($bwo,$bwgs_idx,$bwgs_device,$bwgs_skin);
*/
/*
$g5['bpwidget_option_table']
['bwo'] => Array
	['autoplay'] => 1
	['autoplaySpeed'] => 3
	['speed'] => 0.3
	['infinite'] => 1
	['dots'] => 1
	['arrows'] => 1
	['fade'] => 
	['swipe'] => 1
	['pauseOnFocus'] => 1
	['pauseOnHover'] => 1
	['pauseOnDotsHover'] => 1
	['slidesToShow'] => 1
	['bgBlind'] => rgba(255, 0, 0, 0)
	['vertical'] => 
	['verticalSwiping'] => 
*/

//g5_1_bpwidget_option 테이블에 기존 레코드가 한 개도 없으면 무조건 시퀀스는 1부터 시작한다.
bwg_dbtable_sequence_reset($g5['bpwidget_option_table']);

//회사기본정보 업데이트
$sql = " update {$g5['g5_shop_default_table']}
			set de_admin_company_owner        = '{$de_admin_company_owner}',
                de_admin_company_name         = '{$de_admin_company_name}',
                de_admin_company_saupja_no    = '{$de_admin_company_saupja_no}',
                de_admin_company_tel          = '{$de_admin_company_tel}',
                de_admin_company_fax          = '{$de_admin_company_fax}',
                de_admin_tongsin_no           = '{$de_admin_tongsin_no}',
                de_admin_buga_no           	  = '{$de_admin_buga_no}',
                de_admin_company_zip          = '{$de_admin_company_zip}',
                de_admin_company_addr         = '{$de_admin_company_addr}',
                de_admin_info_name            = '{$de_admin_info_name}',
                de_admin_info_email           = '{$de_admin_info_email}',
				de_bank_account               = '{$de_bank_account}'
				";
sql_query($sql);

//print_r2($bwo);exit;
//echo $w."<br>";
//echo $bwgs_idx."<br>";
//echo $bwgs_device."<br>";
//echo $bwgs_skin."<br>";
if(isset($bwo['logo_url'])){
	$g5_dns = preg_replace('/^http(s|)\:\/\//i','',G5_URL); //$g5_dns = preg_replace('/^http(s|)\:\/\//i','',G5_URL); //http(s)://이후의 url를 담는 배열
	$bwo['logo_url'] = isset($bwo['logo_url']) ? trim(strip_tags($bwo['logo_url'])) : '';
	if(preg_match("/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?[\#]{0,1}$/i",$bwo['logo_url'])){
		if(preg_match('/'.$g5_dns.'/',$bwo['logo_url'])){
			$bwo['logo_url'] = preg_replace('/^(http(s|)\:\/\/|)(www\.|)?/i','',$bwo['logo_url']);
			$bwo['logo_url'] = str_replace($g5_dns,'',$bwo['logo_url']);
		}
	}
}

$bwo_arr = (isset($bwo)) ? $bwo : array();

//옵션 요소들 분석해서 추가요소는 인서트, 수정요소는 업데이트, 필요없는 요소는 삭제하는 함수
bpwidget_option_update($bwo_arr,$bwgs_idx,$bwgs_device,$bwgs_skin);

//인수($_FILES, ['option' || 'content'], BP위젯idx) 파일업로드
bpwidget_optfile_reg($_FILES,$bwgs_idx);
//exit;
?>