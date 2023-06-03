<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_option_sql = " CREATE TABLE `".$g5['wdg_option_table']."` (
	`wgo_idx`     	INT(10) NOT NULL,
	`wgs_idx`     	INT(10) UNSIGNED  NOT NULL COMMENT '위젯idx : wgs_idx번호',
	`wgo_device`  	VARCHAR(10)  NOT NULL COMMENT 'pc / mobile',
	`wgo_skin`    	VARCHAR(50)  NOT NULL COMMENT '해당스킨에 필요한 설정값들을 정의하고 지정',
	`wgo_name`      VARCHAR(45) NOT NULL COMMENT '추가옵션설정에 해당하는 컬럼 slick의 autoplay, arrow, dots등등',
	`wgo_value` 	TEXT    NULL     COMMENT '추가옵션설정에 대한 값'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_option_sql);
sql_query(" ALTER TABLE `".$g5['wdg_option_table']."` ADD PRIMARY KEY (`wgo_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_option_table']."` MODIFY `wgo_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");