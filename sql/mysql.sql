
CREATE TABLE  `es_timetable_subject` (
  `subject_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL DEFAULT '',
  `subject_school` set('0','1','2','3','4','5','6','7','8','9','10','11','12') DEFAULT NULL,
  `subject_kind` enum('scope','subject') DEFAULT NULL,
  `enable` enum('1','0') NOT NULL DEFAULT '1',
  `subject_scope` varchar(80) DEFAULT NULL,
  `e_subject` varchar(80) DEFAULT NULL,
  `s_subject` varchar(80) DEFAULT NULL,
  `subject_id_size` int(11) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`subject_id`)
) ENGINE=MyISAM  COMMENT='科目名稱';

INSERT INTO es_timetable_subject (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable`, `subject_scope`,e_subject ,s_subject) VALUES
(1, '國語', '', 'subject', '1', '語文領域', '國語/文', '國語'),
(2, '本土語言', '', 'subject', '1', '語文領域', '本土語言', '本土語言'),
(3, '數學', '', 'scope', '1', '數學領域', '數學', '數學'),
(4, '生活', '', 'scope', '1', ' 生活領域', '生活', '生活'),
(5, '健康與體育', '', 'scope', '1', '健康與體育領域', '體育', '健康與體育'),
(6, '綜合活動', '', 'scope', '1', '綜合活動領域', '綜合合科', '綜合活動'),
(7, '彈性課程', '', 'scope', '1', '彈性課程 ', '彈性課程', '彈性課程'),
(8, '英語', '', 'subject', '1', '語文領域', '英語/文', '英語'),
(9, '自然', '', 'scope', '1', '自然與生活科技領域', '自然', '自然與生活科技'),
(10, '健康', '', 'subject', '1', '健康與體育領域', '健康', '健康'),
(11, '體育', '', 'subject', '1', '健康與體育領域', '體育', '體育'),
(12, '社會', '', 'scope', '1', '社會領域', '社會', '社會'),
(13, '視覺藝術', '', 'subject', '1', '藝術與人文領域', '視覺藝術(美勞)', '視覺藝術(美勞)'),
(14, '音樂', '', 'subject', '1', '藝術與人文領域', '音樂', '音樂'),
(15, '書法', '', 'subject', '1', '彈性課程 ', '彈性課程', '書法'),
(16, '國語-彈', '', 'subject', '1', '彈性課程 ', '彈性課程', '國語'),
(17, '英語-彈', '', 'subject', '1', '彈性課程 ', '彈性課程', '英語'),
(18, '數學-彈', '', 'subject', '1', '彈性課程 ', '彈性課程', '數學'),
(19, '電腦', '', 'subject', '1', '彈性課程' , '彈性課程', '電腦');



CREATE TABLE   `es_timetable_subject_year` (
  `y_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade` tinyint(3) NOT NULL DEFAULT  '0',
  `subject_id` int(11) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`y_id`)
) ENGINE=MyISAM  COMMENT='年段科目';

INSERT INTO es_timetable_subject_year (`y_id`, `grade`, `subject_id`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 2, 1),
(9, 2, 2),
(10, 2, 3),
(11, 2, 4),
(12, 2, 5),
(13, 2, 6),
(14, 2, 7),
(15, 3, 6),
(16, 3, 3),
(17, 3, 2),
(18, 3, 1),
(19, 4, 2),
(20, 4, 1),
(21, 4, 3),
(22, 5, 3),
(23, 5, 2),
(24, 5, 1),
(25, 6, 2),
(26, 6, 3),
(27, 6, 1),
(28, 4, 6),
(29, 5, 6),
(30, 6, 6),
(31, 3, 8),
(32, 4, 8),
(33, 5, 8),
(34, 6, 8),
(36, 3, 9),
(37, 4, 9),
(38, 5, 9),
(39, 6, 10),
(40, 6, 9),
(41, 5, 10),
(42, 3, 10),
(43, 3, 11),
(44, 4, 11),
(45, 6, 11),
(46, 5, 11),
(47, 3, 12),
(48, 3, 13),
(49, 3, 14),
(50, 4, 14),
(51, 4, 13),
(52, 4, 12),
(53, 5, 12),
(54, 5, 14),
(55, 5, 13),
(56, 6, 12),
(57, 6, 13),
(58, 6, 14),
(59, 3, 19),
(60, 4, 19),
(61, 5, 19),
(62, 6, 19);

CREATE TABLE   `es_timetable` (
  `course_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `school_year` smallint(5) unsigned NOT NULL DEFAULT '0',
  `semester` enum('1','2') NOT NULL DEFAULT '1',
  `class_id` varchar(11) NOT NULL DEFAULT '',
  `teacher` varchar(20)  NOT NULL DEFAULT  '',
  `class_year` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `day` enum('0','1','2','3','4','5','6','7') DEFAULT NULL,
  `sector` tinyint(1) NOT NULL DEFAULT '0',
  `ss_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `room` varchar(10) DEFAULT NULL,
  `allow` enum('0','1') NOT NULL DEFAULT '0',
  `c_kind` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `week_d` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `self_chk`  tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`course_id`),
  KEY `class_year` (`class_year`),
  KEY `school_year` (`school_year`,`semester`)
) ENGINE=MyISAM  COMMENT='課表';

CREATE TABLE   `es_timetable_teacher` (
  `teacher_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) NOT NULL DEFAULT  '0',
  `name` varchar(20)  NOT NULL DEFAULT  '',
  hide tinyint(4) NOT NULL DEFAULT '0',
  `kind` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`teacher_id`)
) ENGINE=MyISAM  COMMENT='授課教師';



CREATE TABLE `es_timetable_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weekday` varchar(30) NOT NULL,
  `sect` varchar(30) NOT NULL,
  `class_year` varchar(30) NOT NULL,
  `class_id` varchar(30) NOT NULL,
  `teacher` varchar(30) NOT NULL,
  `teacher_id` varchar(30) NOT NULL,
  `subject_mode` varchar(30) NOT NULL,
  `subject_class` varchar(30) NOT NULL,
  `subject` varchar(30) NOT NULL,
  `subject_lang` varchar(30) NOT NULL,
  `subject_short` varchar(30) NOT NULL,
  `in_week` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM COMMENT='教育部匯入課表';
