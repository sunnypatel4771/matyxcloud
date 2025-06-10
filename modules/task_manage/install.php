<?php


defined('BASEPATH') or exit('No direct script access allowed');


if ( !$CI->db->table_exists( db_prefix() . 'task_manage_groups' ) )
{

    $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_groups` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `group_name` varchar(255) DEFAULT NULL,
                    `status` tinyint(4) DEFAULT 1,
                    `added_staff` int(11) DEFAULT NULL,
                    `added_date` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

}

if ( !$CI->db->table_exists( db_prefix() . 'task_manage_tasks' ) )
{

    $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_tasks` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `group_id` int(11) DEFAULT NULL,
                      `name` varchar(255) DEFAULT NULL,
                      `milestone` int(11) DEFAULT NULL,
                      `priority` int(11) DEFAULT NULL,
                      `repeat_every` varchar(50) DEFAULT NULL,
                      `assignees` varchar(100) DEFAULT NULL,
                      `followers` varchar(100) DEFAULT NULL,
                      `checklist_items` varchar(100) DEFAULT NULL,
                      `tags` varchar(255) DEFAULT NULL,
                      `description` text DEFAULT NULL,
                      `task_status` int(11) DEFAULT NULL,
                      `task_order` int(11) DEFAULT 0,
                      PRIMARY KEY (`id`),
                      KEY `group_id` (`group_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

}

if ( !$CI->db->table_exists( db_prefix() . 'task_manage_milestones' ) )
{

    $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_milestones` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                      `group_id` int(11) DEFAULT NULL,
                      `milestone_name` varchar(255) DEFAULT NULL,
                      `milestone_order` int(11) DEFAULT 2,
                      `milestone_color` varchar(10) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `group_id` (`group_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

}


if ( !$CI->db->table_exists( db_prefix() . 'task_manage_custom_fields_values' ) )
{

    $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_custom_fields_values` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `relid` int(11) NOT NULL,
                    `fieldid` int(11) NOT NULL,
                    `value` text NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `relid` (`relid`),
                    KEY `fieldid` (`fieldid`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

}

