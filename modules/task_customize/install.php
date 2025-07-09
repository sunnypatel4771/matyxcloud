<?php

defined('BASEPATH') or exit('No direct script access allowed');

$my_projects_path = APPPATH . 'views/admin/tables/my_tasks.php';
$module_my_projects_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/task/my_tasks.php';
if (!file_exists($my_projects_path)) {
  copy($module_my_projects_path, $my_projects_path);
}


defined('BASEPATH') or exit('No direct script access allowed');

$my_projects_path = APPPATH . 'views/admin/tasks/my_view_task_template.php';
$module_my_projects_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/task/my_view_task_template.php';
if (!file_exists($my_projects_path)) {
  copy($module_my_projects_path, $my_projects_path);
}

$my_projects_path = APPPATH . 'views/admin/tables/my_projects.php';
$module_my_projects_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_projects.php';
if (!file_exists($my_projects_path)) {
  copy($module_my_projects_path, $my_projects_path);
}

//for projects
$my_manage_projects_path = APPPATH . 'views/admin/projects/my_manage.php';
$module_my_manage_projects_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/projects/my_manage.php';
if (!file_exists($my_manage_projects_path)) {
  copy($module_my_manage_projects_path, $my_manage_projects_path);
}

//for projects view
$my_projects_view_path = APPPATH . 'views/admin/projects/my_view.php';
$module_my_projects_view_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/projects/my_view.php';
if (!file_exists($my_projects_view_path)) {
  copy($module_my_projects_view_path, $my_projects_view_path);
}

//for project 
$my_project_path = APPPATH . 'views/admin/projects/my_project.php';
$module_my_project_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/projects/my_project.php';
if (!file_exists($my_project_path)) {
  copy($module_my_project_path, $my_project_path);
}

//for project overview
$my_project_overview_path = APPPATH . 'views/admin/projects/my_project_overview.php';
$module_my_project_overview_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/projects/my_project_overview.php';
if (!file_exists($my_project_overview_path)) {
  copy($module_my_project_overview_path, $my_project_overview_path);
}

$my_project_groups_path = APPPATH . 'views/admin/clients/groups/my_projects.php';
$module_my_project_groups_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/projects/client_groups/my_projects.php';
if (!file_exists($my_project_groups_path)) {
  copy($module_my_project_groups_path, $my_project_groups_path);
}

//make table qury for projects_notes 
// CREATE TABLE `tblprojects_notes` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
//   `project_id` int(11) NOT NULL,
//   `staffid` int(11) NOT NULL,
//   `contact_id` int(11) NOT NULL DEFAULT '0',
//   `file_id` int(11) NOT NULL DEFAULT '0',
//   `dateadded` datetime NOT NULL,
//   PRIMARY KEY (`id`),
//   KEY `file_id` (`file_id`),
//   KEY `project_id` (`project_id`)
// )
if (!$CI->db->table_exists(db_prefix() . 'projects_notes_custome')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'projects_notes_custome` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `staffid` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT 0,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

//add colum in task table new colum name is_poked tinyint
if (!$CI->db->field_exists('is_poked', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `is_poked` TINYINT(1) NOT NULL DEFAULT 0;');
}


//add field in clients table 
if (!$CI->db->field_exists('cam_id', db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `cam_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('optimizer_id', db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `optimizer_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('organic_social_id', db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `organic_social_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('seo_lead_id', db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `seo_lead_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('sale_rep_id', db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients` ADD `sale_rep_id` INT(11) NULL DEFAULT;');
}
//tasks
if (!$CI->db->field_exists('cam_id', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `cam_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('optimizer_id', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `optimizer_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('organic_social_id', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `organic_social_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('seo_lead_id', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `seo_lead_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('sale_rep_id', db_prefix() . 'tasks')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'tasks` ADD `sale_rep_id` INT(11) NULL DEFAULT;');
}
// projects
if (!$CI->db->field_exists('cam_id', db_prefix() . 'projects')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'projects` ADD `cam_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('optimizer_id', db_prefix() . 'projects')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'projects` ADD `optimizer_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('organic_social_id', db_prefix() . 'projects')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'projects` ADD `organic_social_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('seo_lead_id', db_prefix() . 'projects')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'projects` ADD `seo_lead_id` INT(11) NULL DEFAULT;');
}
if (!$CI->db->field_exists('sale_rep_id', db_prefix() . 'projects')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'projects` ADD `sale_rep_id` INT(11) NULL DEFAULT;');
}


// CREATE TABLE `tblproject_timer` ( `id` INT NOT NULL AUTO_INCREMENT, `project_id` INT NOT NULL, `start_time` DATETIME DEFAULT NULL, `pause_time` DATETIME DEFAULT NULL, PRIMARY KEY (`id`) ); 

if (!$CI->db->table_exists(db_prefix() . 'project_timer')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'project_timer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `pause_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
