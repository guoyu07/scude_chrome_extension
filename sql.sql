CREATE TABLE `counter` (
  `counter_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(150) NOT NULL COMMENT '学生ID',
  `course_name` varchar(150) NOT NULL COMMENT '课程名称',
  `counter_name` varchar(150) NOT NULL COMMENT '统计名称',
  `counter_url` varchar(250) NOT NULL COMMENT '统计地址',
  `resource_url` varchar(250) DEFAULT NULL COMMENT '资源地址',
  `counter_num` int(11) NOT NULL DEFAULT '0' COMMENT '统计次数',
  `counter_minutes` int(11) DEFAULT '0' COMMENT '统计时间(min)',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`counter_id`),
  KEY `counter_id` (`counter_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学习统计';

CREATE TABLE `student_jobs` (
  `student_jobs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(150) NOT NULL COMMENT '学生ID',
  `jobs_id` bigint(20) unsigned NOT NULL COMMENT '队列ID',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`student_jobs_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='拉取学生课程任务';
