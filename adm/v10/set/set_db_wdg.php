<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_sql = " CREATE TABLE `".$g5['wdg_table']."` (
	`wgs_idx`         	INT(10)   NOT NULL,
	`wgs_cd`          	VARCHAR(50)  NOT NULL COMMENT '중복되지 않는 식별(위치)코드',
	`wgs_name`          VARCHAR(50)  NOT NULL COMMENT '위젯이름',
	`wgs_desc`          VARCHAR(255)  NOT NULL COMMENT '위젯설명',
	`wgs_country`     	VARCHAR(50)  NOT NULL DEFAULT 'ko_KR' COMMENT '한국=ko_KR, 영어=en_US, 일본=ja_JP',
	`wgs_db_category` 	VARCHAR(50)  NULL     COMMENT '내용=content, 게시판=board, 쇼핑몰=shop, 없음=일반위젯',
	`wgs_db_table`    	VARCHAR(50)  NULL     COMMENT 'content=콘텐츠명, board=게시판명, shop=item',
	`wgs_db_idx`      	VARCHAR(50) NULL     COMMENT '해당 db테이블 레코드의 idx값을 저장',
	`wgs_device`      	VARCHAR(10)  NOT NULL COMMENT 'pc / mobile',
	`wgs_skin`        	VARCHAR(50)  NOT NULL COMMENT '해당스킨',
	`mb_id`            	VARCHAR(20)  NOT NULL COMMENT '최종 작업자 id',
	`wgs_manual_url`    VARCHAR(255) NULL     COMMENT '해당 위젯의 메뉴얼 URL을 저장',
	`wgs_order`   		INT(10)     NOT NULL DEFAULT 0 COMMENT '위젯별 노출 순서',
	`wgs_status`      	VARCHAR(50)  NOT NULL DEFAULT 'ok' COMMENT 'pending=대기,ok=정상,hide=숨김,trash=삭제',
	`wgs_start_dt`    	DATETIME     NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '노출 시작일시',
	`wgs_end_dt`      	DATETIME     NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '노출 종료일시',
	`wgs_reg_dt`      	DATETIME     NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '등록일시',
	`wgs_update_dt`   	DATETIME     NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '수정일시'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_sql);
sql_query(" ALTER TABLE `".$g5['wdg_table']."` ADD PRIMARY KEY (`wgs_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_table']."` MODIFY `wgs_idx` int(10) UNSIGNED NOT NULL AUTO_INCREMENT ");