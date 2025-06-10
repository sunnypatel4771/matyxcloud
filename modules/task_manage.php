<?php

defined("BASEPATH") or exit("No direct script access allowed");

/*
Module Name: Task Manage
Description: Manage project tasks in stages
Author: Halil
Author URI: https://codecanyon.net/user/halilaltndg/portfolio
Version: 1.1.5
*/


define("TASK_MANAGE_MODULE_NAME", "task_manage");

hooks()->add_action("admin_init", "task_manage_permission");

hooks()->add_action("admin_init", "task_manage_module_init_menu_items");

hooks()->add_action('task_status_changed', 'task_manage_task_status_changed');

hooks()->add_action("app_admin_footer", "task_manage_include_footer_static_assets");


/**
 * Project Hooks before add/edit
 */
hooks()->add_action("before_add_project", "task_manage_hook_before_add_edit_project");

hooks()->add_action("before_update_project", "task_manage_hook_before_add_edit_project");


/**
 * Project Hooks after add/edit
 */
hooks()->add_action("after_add_project", "task_manage_hook_after_add_edit_project");

hooks()->add_action("after_update_project", "task_manage_hook_after_add_edit_project");


/**
 * @note Language uploading
 */
register_language_files(TASK_MANAGE_MODULE_NAME, [TASK_MANAGE_MODULE_NAME]);

register_activation_hook(TASK_MANAGE_MODULE_NAME, "task_manage_module_activation_hook");


/**
 * @note task manage db installing
 */
function task_manage_module_activation_hook()
{

    $CI = &get_instance();

    require_once __DIR__ . "/install.php";

}


/**
 * @note task permission
 *
 * @return void
 */
function task_manage_permission()
{

    $capabilities = [];

    $capabilities["capabilities"] = [

        "task_manage"      => _l('task_manage_permission') ,

    ];

    register_staff_capabilities("task_manage", $capabilities , _l('task_manage_permission') );

}


/**
 * @note task menu
 *
 * @return void
 */
function task_manage_module_init_menu_items()
{

    $CI = & get_instance();

    if(
        staff_can( 'task_manage' , 'task_manage' ) ||
        staff_can('create', 'projects') ||
        staff_can('view', 'projects') ||
        staff_can('view_own', 'projects') ||
        staff_can('edit', 'projects')
    )
    {

        $CI->app_menu->add_sidebar_menu_item("task_manage_menu", [

            'collapse' => true,

            'name' => _l("task_manage"),

            'position' => 20,

            'icon' => 'fa fa-cogs',

        ]);


        if( staff_can( 'task_manage' , 'task_manage' ) )
        {


            $CI->app_menu->add_sidebar_children_item('task_manage_menu', [

                'slug' => 'task_manage_child_task',

                'name' => _l('task_manage_groups'),

                'href' => admin_url('task_manage/manage'),

                'position' => 3,

            ]);

            $CI->app_menu->add_sidebar_children_item('task_manage_menu', [

                'slug' => 'task_manage_child_groups_pipeline',

                'name' => _l('task_manage_groups_pipeline'),

                'href' => admin_url('task_manage/task_projects/group_pipeline'),

                'position' => 7,

            ]);

        }

        if (
            staff_can('create', 'projects') ||
            staff_can('view', 'projects') ||
            staff_can('view_own', 'projects') ||
            staff_can('edit', 'projects')
        )
        {

            $CI->app_menu->add_sidebar_children_item('task_manage_menu', [

                'slug' => 'task_manage_child_project',

                'name' => _l('projects'),

                'href' => admin_url('task_manage/task_projects'),

                'position' => 10,

            ]);


        }


    }

}


/**
 * @note task status change trigger
 *
 * @param $taskData
 */
function task_manage_task_status_changed( $taskData )
{

    // task completed
    if( !empty( $taskData['task_id'] ) && !empty( $taskData['status'] ) && $taskData['status'] == 5 )
    {

        $taskId     = $taskData['task_id'];

        $CI        = &get_instance();

        $CI->load->model('task_manage/task_manage_model');

        /**
         * @Version 1.1.1
         */
        $CI->load->model('projects_model');

        $CI->load->model('tasks_model');

        $CI->task_manage_model->hook_task_completed( $taskId );

    }

}



/**
 * Module js file
 */
function task_manage_include_footer_static_assets()
{


        echo "
    
    <script src='" . base_url("modules/task_manage/assets/task_manage_js.js?v=3") ."'></script> ";


}




/**
 * Project before add / edit hooks
 */
function task_manage_hook_before_add_edit_project( $data = null , $project_id = 0 )
{

    if( !empty( $data['task_manage_groups'] ) )
    {

        $data['task_manage_groups'] = json_encode( $data['task_manage_groups'] );

    }
    else
    {

        $data['task_manage_groups'] = null;

    }


    return $data;
}


/**
 * Project before add / edit hooks
 */
function task_manage_hook_after_add_edit_project( $project_id = 0 )
{

    if( !empty( $project_id ) )
    {

        $CI        = &get_instance();

        $CI->load->model('task_manage/task_manage_model');

        $CI->task_manage_model->hook_project_saved( $project_id );

    }

}





/**
 * Special customize file
 */
if ( file_exists(__DIR__ . '/includes/my_customize.php') )
{

    require_once __DIR__ . '/includes/my_customize.php';

}
