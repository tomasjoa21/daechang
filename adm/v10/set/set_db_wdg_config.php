<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_config_sql = " CREATE TABLE `".$g5['wdg_config_table']."` (
	`wgf_idx`     INT(10) NOT NULL,
	`wgf_country` VARCHAR(20) NULL     DEFAULT 'ko_KR' COMMENT '한국=ko_KR, 영어=en_US, 일본=ja_JP',
	`wgf_key`     VARCHAR(70) NOT NULL COMMENT 'ex)common,banner,board,shop,item,content,....',
	`wgf_name`    VARCHAR(70) NULL     COMMENT '위젯환경옵션명을 지정한다.',
	`wgf_value`   TEXT    NULL     COMMENT '위젯환경옵션명에 대한 값을 지정한다.',
	`wgf_auto_yn` TINYINT(1)  NULL     DEFAULT 1 COMMENT '자동로딩'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_config_sql);
sql_query(" ALTER TABLE `".$g5['wdg_config_table']."` ADD PRIMARY KEY (`wgf_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_config_table']."` MODIFY `wgf_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");
//기본 환경설정값 입력
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_country',
	"wgf_value"=>'ko_KR=한국,en_US=영어,zh_CN=중국,ja_JP=일본',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_common_status',
	"wgf_value"=>'pending=대기,ok=정상,hide=숨김,trash=삭제',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_language',
	"wgf_value"=>'ko_KR=한국',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_purpose',
	"wgf_value"=>'graph=그래프',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_device',
	"wgf_value"=>'pc=PC,mobile=MOBILE',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_sub_menu',
	"wgf_value"=>'910130',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_sub_menu1',
	"wgf_value"=>'910140',
	"wgf_auto_yn"=>1
));
wdg_config_update(array(
	"wgf_country"=>"ko_KR",	// 기본 한국어 환경설정
	"wgf_key"=>"common",	// key 값을 별도로 주면 환경설정값 그룹으로 분리됩니다.
	"wgf_name"=>'wgf_sub_menu2',
	"wgf_value"=>'910150',
	"wgf_auto_yn"=>1
));
?>