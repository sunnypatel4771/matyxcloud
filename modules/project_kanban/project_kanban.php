<?php

defined("BASEPATH") or exit("No direct script access allowed");

/*
Module Name: Project Kanban
Description: Easily track your projects with the project kanban view
Author: Halil
Author URI: https://www.fiverr.com/halilaltndg
Version: 1.0.4
*/


define("PROJECT_KANBAN_MODULE_NAME", "project_kanban");

hooks()->add_action("admin_init", "project_kanban_manage_permission");

hooks()->add_action("admin_init", "project_kanban_module_init_menu_items");


get_instance()->load->helper('project_kanban/project_kanban');


/**
 * @note Language uploading
 */
register_language_files(PROJECT_KANBAN_MODULE_NAME, [ PROJECT_KANBAN_MODULE_NAME ]);


/**
 * @note permission
 *
 * @return void
 */
function project_kanban_manage_permission()
{

    $capabilities = [];

    $capabilities["capabilities"] = [

        "project_kanban"      => _l('project_kanban_permission') ,

    ];

    register_staff_capabilities("project_kanban", $capabilities , _l('project_kanban_permission') );

}


/**
 * @note menu
 *
 * @return void
 */
function project_kanban_module_init_menu_items()
{

    $CI = & get_instance();

    if( staff_can( 'project_kanban' , 'project_kanban' ) )
    {

        $CI->app_menu->add_sidebar_menu_item("project_kanban", [

            'name'      => _l("project_kanban"),

            'position'  => 31,

            'icon'      => 'fa fa-grip-vertical',

            'href'      => admin_url('project_kanban'),

            'badge'     => [],

        ]);


    }

}



hooks()->add_action("app_admin_footer", "project_kanban_module_footer");

function project_kanban_module_footer()
{


    if( staff_can( 'project_kanban' , 'project_kanban' ) )
    {

     echo " 
        <script> var lang_project_kanban = '"._l('project_kanban')."'; </script>
        
        <script src='" . base_url("modules/project_kanban/assets/project_kanban_js.js?v=1") ."'></script> 
        ";

    }


}


hooks()->add_filter('before_get_project_statuses',function ( $statuses ){


    $CI = &get_instance();

    $status_management = get_option('project_kanban_status_management');

    if ( !empty( $status_management ) && $status_management == 1 )
    {

        $db_statuses = $CI->db->select('*')->from(db_prefix().'project_kanban_project_statuses')->get()->result();

        if ( !empty( $db_statuses ) )
        {

            $return_status = [];

            foreach ( $db_statuses as $status )
            {

                $return_status[] = [
                    'id' => $status->status_id ,
                    'color' => $status->status_color ,
                    'name' => $status->status_name ,
                    'order' => $status->status_order ,
                    'filter_default' => $status->filter_default ? true : false ,
                ];

            }

            return $return_status;

        }
        else
        {

            if ( !empty( $statuses ) )
            {

                foreach ( $statuses as $prj_status )
                {

                    $CI->db->insert(db_prefix().'project_kanban_project_statuses' , [
                        'status_id' => $prj_status['id'] ,
                        'status_name' => $prj_status['name'] ,
                        'status_order' => $prj_status['order'] ,
                        'status_color' => $prj_status['color'] ,
                        'filter_default' => $prj_status['filter_default'] ,
                    ]);

                }

            }

        }

    }


    return $statuses;

});


/**
 * Project status changed
 */

hooks()->add_action("project_status_changed", function( $data ) {

    if ( empty( $data['project_id'] ) )
        return true;

    $CI = &get_instance();

    $project_id = $data['project_id'];

    $CI->db->set('es_status_change_date', date('Y-m-d H:i:s') )->where('id',$project_id)->update(db_prefix().'projects');


} );

