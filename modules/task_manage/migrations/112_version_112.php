<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_112 extends App_module_migration
{

    public function up()
    {

        $CI = get_instance();

        if( !$CI->db->field_exists('task_created_project_status', db_prefix() .'task_manage_tasks') )
        {

            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                            ADD COLUMN `task_created_project_status` int NULL DEFAULT 0 AFTER `assign_project_owner`,
                            ADD COLUMN `task_completed_project_status` int NULL DEFAULT 0 AFTER `task_created_project_status`;');

        }


    }

}
