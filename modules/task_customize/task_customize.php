<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: System Customize
Description: Module provides facility change in Task bulk action, create contract from project .
Author: Sunny Patel
Version: 1.0.0
Requires at least: 2.3.*
Author URI: https://palladiumhub.com/
*/



define('TASK_CUSTOMIZE_MODULE_NAME', 'task_customize');
define('WORK_PLANNED', 45);
define('ASSIGN_STATUS', 8);
define('INTERNAL_REVIEW_STATUS', 3);

//project custom field id Services Included
define('PROJECT_SERVICES_INCLUDED', 27);

//project custom field id Priority 1
define('PROJECT_PRIORITY', 49);

//project custom field id priority 2
define('PROJECT_PRIORITY_2', 50);

// Status Note
define('PROJECT_STATUS_NOTE', 46);
define('VERSION_TASK_CUSTOMIZE', 1094);

define('PROJECT_COLUMN_PRIORITY', 11);
define('PROJECT_COLUMN_PRIORITY_2', 12);
define('PROJECT_LAUNCH_ETA', 51);

hooks()->add_action('tasks_related_table_columns', 'tasks_related_table_columns');
hooks()->add_action('tasks_related_table_sql_columns', 'tasks_related_table_sql_columns');

hooks()->add_action('tasks_related_table_row_data', 'tasks_related_table_row_data', 10, 2);

hooks()->add_action('admin_init', 'task_customize_init_menu_items');
hooks()->add_action('task_status_changed', 'task_customize_task_status_changed', 10, 1);

hooks()->add_action('tasks_table_sql_columns', 'task_customize_tasks_table_sql_columns', 10, 1);


hooks()->add_action('tasks_table_columns', 'task_customize_tasks_table_columns', 10, 1);
// $row = hooks()->apply_filters('tasks_table_row_data', $row, $aRow);

hooks()->add_filter('tasks_table_row_data', 'task_customize_tasks_table_row_data', 10, 2);
register_activation_hook(TASK_CUSTOMIZE_MODULE_NAME, 'task_customize_module_activation_hook');
register_deactivation_hook(TASK_CUSTOMIZE_MODULE_NAME, 'task_customize_module_deactivation_hook');

$CI = &get_instance();
$CI->load->helper(TASK_CUSTOMIZE_MODULE_NAME . '/task_customize');

//register languge 
register_language_files(TASK_CUSTOMIZE_MODULE_NAME, [TASK_CUSTOMIZE_MODULE_NAME]);

function task_customize_tasks_table_row_data($row, $aRow)
{
    $row['DT_RowClass'] = '';
    if ((!empty($aRow['duedate']) && $aRow['duedate'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' success';
    } elseif ((!empty($aRow['date_picker_cvalue_2']) && $aRow['date_picker_cvalue_2'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' warning';
    }
    return $row;
}
function task_customize_module_activation_hook()
{

    $CI = &get_instance();
    require_once __DIR__ . '/install.php';

    // //============================================= my__bulk_actions.php
    $my_register_path = APPPATH . 'views/admin/tasks/my_manage.php';
    $module_my_register_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_manage.php';
    if (!file_exists($my_register_path)) {
        copy($module_my_register_path, $my_register_path);
    }


    $my_project_path = APPPATH . 'views/admin/projects/my_project_contracts.php';
    $module_my_project_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_project_contracts.php';
    if (!file_exists($my_project_path)) {
        copy($module_my_project_path, $my_project_path);
    }


    $my_contract_path = APPPATH . 'views/admin/contracts/my_contract.php';
    $module_my_contract_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_contract.php';
    if (!file_exists($my_contract_path)) {
        copy($module_my_contract_path, $my_contract_path);
    }


    $my_task_relation_path = APPPATH . 'views/admin/tables/my_tasks_relations.php';
    $module_my_task_relation_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_tasks_relations.php';
    if (!file_exists($my_task_relation_path)) {
        copy($module_my_task_relation_path, $my_task_relation_path);
    }

    $my_task_path = APPPATH . 'views/admin/tasks/my_task.php';
    $module_my_task_path = module_dir_path(TASK_CUSTOMIZE_MODULE_NAME) . 'system_changes/my_task.php';
    if (!file_exists($my_task_path)) {
        copy($module_my_task_path, $my_task_path);
    }
}


function task_customize_module_deactivation_hook()
{
    require_once __DIR__ . '/uninstall.php';
}

function tasks_related_table_columns($table_data)
{
    $table_data[] = 'Milestones';
    $table_data[] = 'Comments';
    $startDateIndex = null;
    $workPlannedIndex = null;

    // Loop through the array and find indices
    foreach ($table_data as $index => $item) {
        if (is_array($item) && isset($item['name'])) {
            if ($item['name'] === "Start Date") {
                $startDateIndex = $index;
            }
            if ($item['name'] === "Work Planned") {
                $workPlannedIndex = $index;
            }
        }
    }

    // If both are found, move "Work Planned" after "Start Date"
    if ($startDateIndex !== null && $workPlannedIndex !== null) {
        // Remove "Work Planned" from its original position
        $workPlanned = $table_data[$workPlannedIndex];
        unset($table_data[$workPlannedIndex]);

        if (isset($workPlanned['th_attrs']) && is_array($workPlanned['th_attrs'])) {
            $workPlanned['th_attrs']['class'] = 'duedate';
        }

        // Re-index array
        $table_data = array_values($table_data);

        // Insert "Work Planned" after "Start Date"
        array_splice($table_data, $startDateIndex + 1, 0, [$workPlanned]);
    }
    return $table_data;
}


function tasks_related_table_sql_columns($aColumns)
{
    $aColumns[] = 'milestone';

    $startdateIndex = array_search("startdate", $aColumns);
    $duedateIndex = array_search("duedate", $aColumns);
    $moveIndex = null;

    foreach ($aColumns as $index => $value) {
        if (strpos($value, "tasks_eta") !== false) {
            $moveIndex = $index;
            break;
        }
    }

    if ($moveIndex !== null && $startdateIndex !== false && $duedateIndex !== false) {
        $elementToMove = $aColumns[$moveIndex];
        unset($aColumns[$moveIndex]);
        $aColumns = array_values($aColumns);

        $startdateIndex = array_search("startdate", $aColumns);
        $duedateIndex = array_search("duedate", $aColumns);

        array_splice($aColumns, $duedateIndex, 0, $elementToMove);
    }
    $aColumns[] = "2";


    return $aColumns;
}

function tasks_related_table_row_data($row, $aRow)
{
    // $milestone = get_milestone_data($aRow['milestone']);
    // $row[] = $milestone;
    // $row['DT_RowClass'] = 'has-row-options has-border-left';
    // if ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
    //     $row['DT_RowClass'] .= ' orange'; 
    // }
    // if ((! empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
    //     $row['DT_RowClass'] .= ' danger';
    // }
    // if ((!empty($aRow['duedate']) && $aRow['duedate'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
    //     $row['DT_RowClass'] .= ' success';
    // }
    // if ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
    //     $row['DT_RowClass'] .= ' warning';
    // }
    $milestone = get_milestone_data($aRow['milestone']);
    $row[] = $milestone;
    $comments = '<a href="#" class="task-comment" data-task-id="' . $aRow['id'] . '" data-toggle="modal" data-target="#task-comment-modal"><i class="fa fa-comment"></i>   ' . get_comments_count($aRow['id']) . '</a>';
    $row[] = $comments;
    $row['DT_RowClass'] = 'has-row-options has-border-left';

    if ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' orange';
    } elseif ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' warning';
    } elseif ((! empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' danger';
    } elseif ((!empty($aRow['duedate']) && $aRow['duedate'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
        $row['DT_RowClass'] .= ' success';
    }
    return $row;
}

function task_customize_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_tabs->add_project_tab('project_tasks', [
        'name'                      => _l('tasks'),
        'icon'                      => 'fa-regular fa-check-circle',
        'view'                      => TASK_CUSTOMIZE_MODULE_NAME . '/project_tasks',
        'position'                  => 10,
        'linked_to_customer_option' => ['view_tasks'],
    ]);

    //add menu name  reccuring tasks in main sidebar
    $CI->app_menu->add_sidebar_menu_item('recurring_tasks', [
        'name'     => "Repeating Tasks",
        'icon'     => 'fa fa-refresh',
        'position' => 15,
        'href'     => admin_url('task_customize/recurring_tasks'),
    ]);


    //add sidebar menu project custome filed 	Services Included Projects
    $CI->app_menu->add_sidebar_menu_item('project_custom_fields', [
        'name'     => "Services Included",
        'icon'     => 'fa-solid fa-chart-gantt',
        'position' => 16,
        'href'     => '#',
    ]);
    //chield menu project custom fields get fileds from database and make loop and add to menu
    $CI->db->select('options');
    $CI->db->from(db_prefix() . 'customfields');
    $CI->db->where('fieldto', 'projects');
    $CI->db->where('id', PROJECT_SERVICES_INCLUDED);
    $project_custom_fields = $CI->db->get()->result_array();

    //explode options by comma and make loop and add to menu
    $options = explode(',', $project_custom_fields[0]['options']);
    foreach ($options as $option) {
        $option = trim($option);
        //space remove and add _ 
        $value = '';
        if ($option != '') {
            $value = str_replace(' ', '_', $option);
        }


        $CI->app_menu->add_sidebar_children_item('project_custom_fields', [
            'slug'     => 'project_custom_field_' . $value,
            'name'     => $option,
            'icon'     => 'fa-solid fa-chart-gantt',
            'href'     => admin_url('task_customize/project_custom_fields?service=' . $value),
        ]);
    }


    //add new menu project_type
    $CI->app_menu->add_sidebar_menu_item('project_type', [
        'name'     => 'Project Type',
        'icon'     => 'fa-solid fa-chart-gantt',
        'position' => 17,
        'href'     => '#',
    ]);

    //projects
    $CI->app_menu->add_sidebar_children_item('project_type', [
        'slug'     => 'project_type_projects',
        'name'     => 'Projects',
        'href'     => admin_url('projects'),
        'icon'     => 'fa-solid fa-chart-gantt',
    ]);


    //add children menu project type
    $CI->app_menu->add_sidebar_children_item('project_type', [
        'slug'     => 'project_type_website',
        'name'     => 'Website',
        'icon'     => 'fa-solid fa-globe',
        'href'     => admin_url('task_customize/project_type?type=website'),
    ]);

    //landing page projects 
    $CI->app_menu->add_sidebar_children_item('project_type', [
        'slug'     => 'project_type_landing_page',
        'name'     => 'Landing Page',
        'icon'     => 'fa-solid fa-globe',
        'href'     => admin_url('task_customize/project_type?type=landing_page'),
    ]);
}


// i added dhaval 

function task_customize_task_status_changed($data)
{
    $CI = &get_instance();
    $status = isset($data['status']) ? $data['status'] : '';
    $task_id = isset($data['task_id']) ? $data['task_id'] : '';
    if ($status != '' && $task_id != '') {

        if ($status == Tasks_model::STATUS_COMPLETE) {
            $CI->db->select('id,addedfrom,recurring_type,repeat_every,last_recurring_date,startdate,duedate,recurring ,is_recurring_from');
            $CI->db->where('id', $task_id);
            $recurring_tasks = $CI->db->get(db_prefix() . 'tasks')->result_array();
            if (!empty($recurring_tasks)) {
                foreach ($recurring_tasks as $task) {
                    if ((isset($task['is_recurring_from']) && $task['is_recurring_from'] != '') || (isset($task['recurring']) && $task['recurring'] == 1)) {
                        $last_recurring_date = $task['last_recurring_date'];
                        $type                = $task['recurring_type'];
                        $repeat_every        = $task['repeat_every'];
                        $task_date           = $task['startdate'];

                        if (isset($task['is_recurring_from']) && $task['is_recurring_from'] != NULL) {
                            $task_setail = get_task_detail($task['is_recurring_from']);
                            $last_recurring_task_id = isset($task_setail[0]['id']) ? $task_setail[0]['id'] : '';
                            $last_recurring_date = isset($task_setail[0]['last_recurring_date']) ? $task_setail[0]['last_recurring_date'] : '';
                            $type                = isset($task_setail[0]['recurring_type']) ? $task_setail[0]['recurring_type'] : '';
                            $repeat_every        = isset($task_setail[0]['repeat_every']) ? $task_setail[0]['repeat_every'] : '';
                            $task_date           = isset($task_setail[0]['startdate']) ? $task_setail[0]['startdate'] : '';
                            if (isset($task_setail[0]['total_cycles']) && isset($task_setail[0]['cycles']) && $task_setail[0]['total_cycles'] == $task_setail[0]['cycles']) {
                                continue;
                            }
                        }
                        if ($task['recurring'] == 1) {
                            if (isset($task[0]['total_cycles']) && isset($task[0]['cycles']) && $task[0]['total_cycles'] == $task[0]['cycles']) {
                                continue;
                            }
                        }

                        $date = new DateTime(date('Y-m-d'));
                        // Check if is first recurring
                        if (!$last_recurring_date) {
                            $last_recurring_date = date('Y-m-d', strtotime($task_date));
                        } else {
                            $last_recurring_date = date('Y-m-d', strtotime($last_recurring_date));
                        }

                        $re_create_at = date('Y-m-d', strtotime('+' . $repeat_every . ' ' . strtoupper($type), strtotime($last_recurring_date)));

                        $task_id = $task['id'];
                        if (isset($last_recurring_task_id) && $last_recurring_task_id != '') {
                            $task_id = $last_recurring_task_id;
                        } else {
                            $task_id = $task['id'];
                        }
                        $copy_task_data['copy_task_followers']       = 'true';
                        $copy_task_data['copy_task_checklist_items'] = 'true';
                        $copy_task_data['copy_from']                 = $task_id;

                        $overwrite_params = [
                            'startdate'           => $re_create_at,
                            'status'              => ASSIGN_STATUS,
                            'recurring_type'      => null,
                            'repeat_every'        => 0,
                            'cycles'              => 0,
                            'recurring'           => 0,
                            'custom_recurring'    => 0,
                            'last_recurring_date' => null,
                            'is_recurring_from'   => $task_id,
                        ];

                        if (!empty($task['duedate'])) {
                            $dStart                      = new DateTime($task['startdate']);
                            $dEnd                        = new DateTime($task['duedate']);
                            $dDiff                       = $dStart->diff($dEnd);
                            $overwrite_params['duedate'] = date('Y-m-d', strtotime('+' . $dDiff->days . ' days', strtotime($re_create_at)));
                        }
                        $newTaskID = $CI->tasks_model->copy($copy_task_data, $overwrite_params);

                        if ($newTaskID) {
                            $task_id = $task['id'];
                            if (isset($last_recurring_task_id) && $last_recurring_task_id != '') {
                                $task_id = $last_recurring_task_id;
                            } else {
                                $task_id = $task['id'];
                            }
                            $CI->db->where('id', $task_id);
                            $CI->db->update(db_prefix() . 'tasks', [
                                'last_recurring_date' => $re_create_at,
                            ]);

                            $CI->db->where('id', $task_id);
                            $CI->db->set('total_cycles', 'total_cycles+1', false);
                            $CI->db->update(db_prefix() . 'tasks');

                            $CI->db->where('taskid', $task_id);
                            $assigned = $CI->db->get(db_prefix() . 'task_assigned')->result_array();
                            foreach ($assigned as $assignee) {
                                $assigneeId = $CI->tasks_model->add_task_assignees([
                                    'taskid'   => $newTaskID,
                                    'assignee' => $assignee['staffid'],
                                ], true);

                                if ($assigneeId) {
                                    $CI->db->where('id', $assigneeId);
                                    $CI->db->update(db_prefix() . 'task_assigned', ['assigned_from' => $task['addedfrom']]);
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($status == ASSIGN_STATUS || $status == INTERNAL_REVIEW_STATUS) {
            $CI->db->where('relid', $task_id);
            $CI->db->where('fieldid', WORK_PLANNED);
            $CI->db->where('fieldto', 'tasks');
            $CI->db->delete(db_prefix() . 'customfieldsvalues');
        }
    }
}

function task_customize_tasks_table_columns($table_data)
{
    foreach ($table_data as $key => $value) {
        if (is_array($value) && isset($value['name']) && $value['name'] === "Work Planned") {
            $work_planned = array_splice($table_data, $key, 1);

            if (isset($work_planned[0]['th_attrs']) && is_array($work_planned[0]['th_attrs'])) {
                $work_planned[0]['th_attrs']['class'] = 'duedate';
            }
            break;
        }
    }
    foreach ($table_data as $key => $value) {
        if ($value === "Start Date") {
            $start_date_index = $key;
            break;
        }
    }


    array_splice($table_data, $start_date_index + 1, 0, $work_planned);

    $table_data[] = _l('comments');
    return $table_data;
}

function task_customize_tasks_table_sql_columns($aColumns)
{

    $startdateIndex = array_search("startdate", $aColumns);
    $duedateIndex = array_search("duedate", $aColumns);
    $moveIndex = null;

    foreach ($aColumns as $index => $value) {
        if (strpos($value, "tasks_eta") !== false) {
            $moveIndex = $index;
            break;
        }
    }

    if ($moveIndex !== null && $startdateIndex !== false && $duedateIndex !== false) {
        $elementToMove = $aColumns[$moveIndex];
        unset($aColumns[$moveIndex]);
        $aColumns = array_values($aColumns);

        $startdateIndex = array_search("startdate", $aColumns);
        $duedateIndex = array_search("duedate", $aColumns);

        array_splice($aColumns, $duedateIndex, 0, $elementToMove);
    }
    $aColumns[] = "2";
    return $aColumns;
}

hooks()->add_action('app_admin_footer', 'task_customize_hook_app_admin_footer');
function task_customize_hook_app_admin_footer()
{

    // Get the current request URI
    $viewuri = $_SERVER['REQUEST_URI'];

    // Check if the URI contains the desired path
    if (strpos($viewuri, 'group=project_tasks') !== false) {
        //load
        echo '<script src="' . module_dir_url(TASK_CUSTOMIZE_MODULE_NAME, 'assets/js/project_tasks.js') . '?v=' . VERSION_TASK_CUSTOMIZE . '"></script>';
    }

    //url is http://localhost/matyxcloud/admin/projects
    if (strpos($viewuri, 'admin/projects') !== false) {
        echo '<script src="' . module_dir_url(TASK_CUSTOMIZE_MODULE_NAME, 'assets/js/project_change.js') . '?v=' . VERSION_TASK_CUSTOMIZE . '"></script>';
    }
}


hooks()->add_action('before_add_task', 'task_customize_before_add_task');
function task_customize_before_add_task($data)
{

    //check assing exits in project or not
    $CI = &get_instance();
    if ($data['rel_type'] == 'project' && isset($data['assignees'])) {
        $project_id = $data['rel_id'];
        $task_assignees = $data['assignees'];
        $project_assignees = $CI->projects_model->get_project_members($project_id);
        $existingStaffIds = array_column($project_assignees, 'staff_id');
        //array merge 
        $assignees = array_merge($task_assignees, $existingStaffIds);
        $project_data['project_members'] = $assignees;
        //update assignees
        $CI->projects_model->add_edit_members($project_data, $project_id);
    }
    return $data;
}

hooks()->add_action('task_assignee_added', 'task_customize_task_assignee_added');
function task_customize_task_assignee_added($data)
{
    $CI = &get_instance();
    $task_id = $data['task_id'];
    $task = $CI->tasks_model->get($task_id);

    if ($task) {
        $rel_type = $task->rel_type;
        $rel_id = $task->rel_id;
        if ($rel_type == 'project') {
            $project_assignees = $CI->projects_model->get_project_members($rel_id);
            $existingStaffIds = array_column($project_assignees, 'staff_id');
            $task_assignees = array($data['staff_id']);
            $assignees = array_merge($task_assignees, $existingStaffIds);

            $project_data['project_members'] = $assignees;
            $CI->projects_model->add_edit_members($project_data, $rel_id);
        }
    }
}


//after_customer_admins_tab
hooks()->add_action('after_customer_billing_and_shipping_tab', 'client_add_custome_staff');
function client_add_custome_staff($data)
{
    echo '   <li role="presentation">
                        <a href="#client_custome" aria-controls="client_custome" role="tab"
                            data-toggle="tab">
                             Customer Management
                        </a>
                    </li>';
}

hooks()->add_action('after_custom_profile_tab_content', 'client_add_custome_staff_content');
function client_add_custome_staff_content($client)
{
    $CI = &get_instance();
    echo $CI->load->view('task_customize/custome_content',$client);
}
