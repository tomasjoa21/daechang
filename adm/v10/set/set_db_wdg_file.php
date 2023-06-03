<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_file_sql = " CREATE TABLE `".$g5['wdg_file_table']."` (
	`wga_idx` 			INT(10) NOT NULL,
	`wgs_idx` 			INT(10) UNSIGNED NOT NULL COMMENT '위젯테이블의 아이디',
	`wgc_idx` 			INT(10) UNSIGNED NOT NULL COMMENT '위젯콘텐츠의 아이디',
	`wga_type` 			VARCHAR(50) NOT NULL DEFAULT '' COMMENT '파일 용도(option=옵션,content=내용)',
	`wga_content_type` 	VARCHAR(50) NOT NULL DEFAULT '' COMMENT '콘텐츠 파일 용도(bg=배경,img=이미지)',
	`wga_path` 			VARCHAR(255) DEFAULT '' COMMENT '파일이 저장된 절대경로(마지막폴더까지)',
	`wga_array` 		VARCHAR(255) NOT NULL DEFAULT '' COMMENT '파일의 그룹배열명',
	`wga_title` 		VARCHAR(70) NOT NULL DEFAULT '' COMMENT '파일의 의미를 부여하고자 하는 타이틀',
	`wga_name` 			VARCHAR(255) NOT NULL DEFAULT '' COMMENT '파일중복을 피하기 위해 할당되는 파일명',
	`wga_name_orig` 	VARCHAR(255) NOT NULL DEFAULT '' COMMENT '파일의 원래 이름',
	`wga_content`   	TEXT    NULL     COMMENT '파일관련 추가적인 내용을 저장',
	`wga_width` 		INT(5) DEFAULT '0' COMMENT '파일의 가로 사이즈',
	`wga_height` 		INT(5) DEFAULT '0' COMMENT '파일의 세로 사이즈',
	`wga_filesize` 		INT(5) NOT NULL DEFAULT '0' COMMENT '파일의 용량',
	`wga_rank` 			INT(5) NOT NULL DEFAULT '0' COMMENT '파일 순위',
	`wga_sort` 			INT(5) NOT NULL DEFAULT '0' COMMENT '파일 순서',
	`wga_mime_type` 	VARCHAR(50) DEFAULT '' COMMENT '파일 유형',
	`wga_status` 		VARCHAR(20) NOT NULL DEFAULT 'ok' COMMENT '파일 상태(pending=대기,ok=정상,hide=숨김,trash=삭제)',
	`wga_reg_dt` DATETIME DEFAULT '0000-00-00 00:00:00' COMMENT '파일 갱신일'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_file_sql);
sql_query(" ALTER TABLE `".$g5['wdg_file_table']."` ADD PRIMARY KEY (`wga_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_file_table']."` MODIFY `wga_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");