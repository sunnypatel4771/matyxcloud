<?php
defined('BASEPATH') or exit('No direct script access allowed');

$modulePath = __DIR__ . '/../advanced_task_status_manager';

function deleteFiles($path)
{
    if (is_dir($path))
    {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file)
        {
            $fullPath = "$path/$file";

            if (is_dir($fullPath))
            {
                deleteFiles($fullPath);
            }
            else
            {
                unlink($fullPath);
            }
        }
        rmdir($path);
    }
}

if (is_dir($modulePath))
{
    deleteFiles($modulePath);
}


// Create database schema with relations 

if (!$CI->db->table_exists(db_prefix() . 'task_statuses')) {
  $CI->db->query(
    'CREATE TABLE IF NOT EXISTS`' . db_prefix() . 'task_statuses` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `order` INT(11) NOT NULL,
        `name` TEXT NOT NULL,
        `color` TEXT NOT NULL,
        `filter_default` BOOLEAN,
        PRIMARY KEY (`id`) )
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
  );
}

if (!$CI->db->table_exists(db_prefix() . 'task_status_dont_have_staff')) {
    $CI->db->query(
        'CREATE TABLE IF NOT EXISTS`' . db_prefix() . 'task_status_dont_have_staff` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `task_status_id` INT(11) NOT NULL,
        `staff_id` INT(11) NOT NULL,
        PRIMARY KEY (`id`))
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
    );
}

$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "task_status_dont_have_staff' 
    AND CONSTRAINT_NAME = 'task_status_dont_have_staff_task_status_id'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "task_status_dont_have_staff`
        ADD CONSTRAINT `task_status_dont_have_staff_task_status_id` FOREIGN KEY (`task_status_id`) 
        REFERENCES `" . db_prefix() . "task_statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}

$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "task_status_dont_have_staff' 
    AND CONSTRAINT_NAME = 'task_status_dont_have_staff_staff_id'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "task_status_dont_have_staff`
        ADD CONSTRAINT `task_status_dont_have_staff_staff_id` FOREIGN KEY (`staff_id`) 
        REFERENCES `" . db_prefix() . "staff` (`staffid`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}


if (!$CI->db->table_exists(db_prefix() . 'task_status_can_change')) {
    $CI->db->query(
        'CREATE TABLE IF NOT EXISTS`' . db_prefix() . 'task_status_can_change` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `task_status_id` INT(11) NOT NULL,
        `task_status_id_can_change_to` INT(11) NOT NULL,
        PRIMARY KEY (`id`))
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
    );
}
$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "task_status_can_change' 
    AND CONSTRAINT_NAME = 'task_status_can_change_task_status_id'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "task_status_can_change`
        ADD CONSTRAINT `task_status_can_change_task_status_id` FOREIGN KEY (`task_status_id`) 
        REFERENCES `" . db_prefix() . "task_statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}

$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "task_status_can_change' 
    AND CONSTRAINT_NAME = 'task_status_can_change_task_status_id_2'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "task_status_can_change`
        ADD CONSTRAINT `task_status_can_change_task_status_id_2` FOREIGN KEY (`task_status_id_can_change_to`) 
        REFERENCES `" . db_prefix() . "task_statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}

$CI->load->model('Tasks_model');

$statuses = $CI->Tasks_model->get_statuses();

// Create default statuses
foreach ($statuses as $status) {
  $CI->db->query("INSERT INTO " . db_prefix() . "task_statuses (`id`, `name`, `color` ,`order`, `filter_default`) VALUES ({$status['id']},'{$status['name']}','{$status['color']}',{$status['order']}," . intval($status['filter_default']) . ") ON DUPLICATE KEY UPDATE id={$status['id']}");
}


// -------------- Project Statuses ------------------

if (!$CI->db->table_exists(db_prefix() . 'project_statuses'))
{
    $CI->db->query(
        'CREATE TABLE `' . db_prefix() . 'project_statuses` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `order` INT(11) NOT NULL,
          `name` TEXT NOT NULL,
          `color` TEXT NOT NULL,
          `filter_default` BOOLEAN,
          PRIMARY KEY (`id`) )
         DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
    );
}

if (!$CI->db->table_exists(db_prefix() . 'project_status_can_change'))
{
    $CI->db->query(
        'CREATE TABLE `' . db_prefix() . 'project_status_can_change` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `project_status_id` INT(11) NOT NULL,
          `project_status_id_can_change_to` INT(11) NOT NULL,
          PRIMARY KEY (`id`))
        DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
    );
}



$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "project_status_can_change' 
    AND CONSTRAINT_NAME = 'project_status_can_change_project_status_id'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "project_status_can_change`
        ADD CONSTRAINT `project_status_can_change_project_status_id` FOREIGN KEY (`project_status_id`) 
        REFERENCES `" . db_prefix() . "project_statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}

$result = $CI->db->query("
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = '" . db_prefix() . "project_status_can_change' 
    AND CONSTRAINT_NAME = 'project_status_can_change_project_status_id_2'
");

if ($result->num_rows() == 0)
{
    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "project_status_can_change`
        ADD CONSTRAINT `project_status_can_change_project_status_id_2` FOREIGN KEY (`project_status_id_can_change_to`) 
        REFERENCES `" . db_prefix() . "project_statuses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");
}


$CI->load->model('Projects_model');

$statuses = $CI->Projects_model->get_project_statuses();

// Create default statuses
foreach ($statuses as $status) {
  $CI->db->query("INSERT INTO " . db_prefix() . "project_statuses (`id`, `name`, `color` ,`order`, `filter_default`) VALUES ({$status['id']},'{$status['name']}','{$status['color']}',{$status['order']}," . intval($status['filter_default']) . ") ON DUPLICATE KEY UPDATE id={$status['id']}");
}

// -------------- Tickets Statuses ------------------
//  We use basic tables tickets_status
//  Add new column is_active on client view
//  Update exists ticket statuses on is_active = 1

$query = $CI->db->query("SHOW COLUMNS FROM `" . db_prefix() . "tickets_status` LIKE 'is_active'");

if ($query->num_rows() == 0)
{
    $CI->db->query(
        "ALTER TABLE `" . db_prefix() . "tickets_status`
        ADD COLUMN `is_active` TINYINT(1)
        DEFAULT 1;"
    );

    $CI->db->query(
        "UPDATE `" . db_prefix() . "tickets_status`
        SET `is_active` = 1;"
    );
}




