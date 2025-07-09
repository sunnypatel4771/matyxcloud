<?php
defined('BASEPATH') or exit('No direct script access allowed');

$unlink_files = array(
    APPPATH . 'views/admin/tasks/my_manage.php',
    APPPATH . 'views/admin/projects/my_project_contracts.php',
    APPPATH . 'views/admin/contracts/my_contract.php',
    APPPATH . 'views/admin/tables/my_tasks_relations.php',
    APPPATH . 'views/admin/tables/my_tasks.php',
    APPPATH . 'views/admin/tasks/my_view_task_template.php',
    APPPATH . 'views/admin/tasks/my_task.php',
    APPPATH . 'views/admin/tables/my_projects.php',
    APPPATH . 'views/admin/projects/my_manage.php',
    APPPATH . 'views/admin/projects/my_view.php',
    APPPATH . 'views/admin/projects/my_project.php',
    APPPATH . 'views/admin/projects/my_project_overview.php',
    APPPATH . 'views/admin/clients/groups/my_projects.php',

);

foreach ($unlink_files as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}
