<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$wdg_content_sql = " CREATE TABLE `".$g5['wdg_content_table']."` (
	`wgc_idx`     				INT(10) NOT NULL,
	`wgs_idx`     				INT(10) UNSIGNED NOT NULL COMMENT '위젯idx : bwgs_idx번호',
	`wgc_name`     			    VARCHAR(100) NOT NULL COMMENT '이름',
	`wgc_title`     			VARCHAR(100) NOT NULL COMMENT '타이틀',	
	`bo_table`     				VARCHAR(20)  NOT NULL COMMENT '게시판이름',
	`wr_id`     				INT(11) 	 NOT NULL COMMENT '게시물아이디',
	`co_id`     				VARCHAR(20)  NOT NULL COMMENT '내용아이디',
	`mb_id`     				VARCHAR(100) NOT NULL COMMENT '회원아이디',
	`it_id`     				VARCHAR(20)  NOT NULL COMMENT '상품아이디',
	`od_id`     				BIGINT(20)   NOT NULL COMMENT '주문아이디',
	`wgc_order`   				INT(10)     NOT NULL DEFAULT 0 COMMENT '내용 순서',
	`wgc_content` 				TEXT     NULL     COMMENT '키워드에 해당하는 단락의 내용을 입력',
	`wgc_key`     				VARCHAR(255) NOT NULL COMMENT '각 종 키워드 또는 키값을 입력',
	`wgc_date`   				DATE     NULL     DEFAULT '0000-00-00' COMMENT '이 위젯내용의 날짜',
	`wgc_start_date`   		    DATE     NULL     DEFAULT '0000-00-00' COMMENT '이 위젯내용의 시작일',
	`wgc_end_date`   			DATE     NULL     DEFAULT '0000-00-00' COMMENT '이 위젯내용의 종료일',
	`wgc_time`   				TIME     NULL     DEFAULT '00:00:00' COMMENT '이 위젯내용의 시간',
	`wgc_start_time`   		    TIME     NULL     DEFAULT '00:00:00' COMMENT '이 위젯내용의 시작시간',
	`wgc_end_time`   			TIME     NULL     DEFAULT '00:00:00' COMMENT '이 위젯내용의 종료시간',
	`wgc_reg_dt`   		        DATETIME NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '위젯내용의 등록일',
	`wgc_update_dt`   		    DATETIME NULL     DEFAULT '0000-00-00 00:00:00' COMMENT '위젯내용의 수정일'
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ";
sql_query($wdg_content_sql,1);
sql_query(" ALTER TABLE `".$g5['wdg_content_table']."` ADD PRIMARY KEY (`wgc_idx`) ");
sql_query(" ALTER TABLE `".$g5['wdg_content_table']."` MODIFY `wgc_idx` int(20) UNSIGNED NOT NULL AUTO_INCREMENT ");
