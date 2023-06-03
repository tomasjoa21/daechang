<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_extend_sql = " CREATE TABLE `".$g5['wdg_content_extend_table']."` (
	`wgx_idx`     	INT(10) NOT NULL,
	`wgc_idx`     	INT(10) UNSIGNED NOT NULL COMMENT 'wgc_idx번호',
	`wgx_name`      VARCHAR(45) NOT NULL COMMENT '내용확장옵션이름',
	`wgx_value` 	TEXT    NULL     COMMENT '내용확장옵션값'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_extend_sql);
sql_query(" ALTER TABLE `".$g5['wdg_content_extend_table']."` ADD PRIMARY KEY (`wgx_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_content_extend_table']."` MODIFY `wgx_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");
?>