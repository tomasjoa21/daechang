<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_user_option_sql = " CREATE TABLE `".$g5['wdg_user_option_table']."` (
	`wgu_idx`     	INT(10) NOT NULL,
	`wgs_idx`     	INT(10) UNSIGNED NOT NULL COMMENT '위젯idx : wgs_idx번호',
	`mb_id`         VARCHAR(20) NOT NULL COMMENT '회원ID',
	`wgu_name`      VARCHAR(45) NOT NULL COMMENT '추가옵션설정에 해당하는 컬럼 slick의 autoplay, arrow, dots등등',
	`wgu_value` 	TEXT    NULL     COMMENT '추가옵션설정에 대한 값'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_user_option_sql);
sql_query(" ALTER TABLE `".$g5['wdg_user_option_table']."` ADD PRIMARY KEY (`wgu_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_user_option_table']."` MODIFY `wgu_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");