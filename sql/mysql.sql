
CREATE TABLE  `es_timetable_subject` (
  `subject_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL DEFAULT '',
  `subject_school` set('0','1','2','3','4','5','6','7','8','9','10','11','12') DEFAULT NULL,
  `subject_kind` enum('scope','subject') DEFAULT NULL,
  `enable` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`subject_id`)
) ENGINE=MyISAM  COMMENT='科目名稱';

INSERT INTO  es_timetable_subject  (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable`) VALUES 
		(1, '本國語文', '', 'subject', '1'),
		(2, '本土語文', '', 'subject', '1'),
		(3, '數學', '', 'scope', '1'),
		(4, '生活', '', 'scope', '1'),
		(5, '健康與體育', '', 'scope', '1'),
		(6, '綜合活動', '', 'scope', '1'),
		(7, '彈性課程', '', 'scope', '1'),
		(8, '英語', '', 'subject', '1'),
		(9, '自然與生活科技', '', 'scope', '1'),
		(10, '健康與體育_健', '', 'subject', '1'),
		(11, '健康與體育_體', '', 'subject', '1'),
		(12, '社會', '', 'scope', '1'),
		(13, '視覺藝術', '', 'subject', '1'),
		(14, '音樂', '', 'subject', '1'),
		(15, '彈性-書法', '', 'subject', '1'),
		(16, '彈性-國語', '', 'subject', '1'),
		(17, '彈性-英語', '', 'subject', '1'),
		(18, '彈性-數學', '', 'subject', '1'),
		(19, '彈性-資訊', '', 'subject', '1'),
		(20, '彈性-音樂', '', 'subject', '1');



CREATE TABLE   `es_timetable_subject_year` (
  `y_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `grade` tinyint(3) NOT NULL DEFAULT  '0',
  `subject_id` tinyint(3) NOT NULL DEFAULT  '0',
  PRIMARY KEY (`y_id`)
) ENGINE=MyISAM  COMMENT='年段科目';


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
  PRIMARY KEY (`course_id`),
  KEY `class_year` (`class_year`),
  KEY `school_year` (`school_year`,`semester`)
) ENGINE=MyISAM  COMMENT='課表';

CREATE TABLE   `es_timetable_teacher` (
  `teacher_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) NOT NULL DEFAULT  '0',
  `name` varchar(20)  NOT NULL DEFAULT  '',
  `hide` enum('0','1') NOT NULL DEFAULT '0',
  `kind` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`teacher_id`)
) ENGINE=MyISAM  COMMENT='授課教師';
