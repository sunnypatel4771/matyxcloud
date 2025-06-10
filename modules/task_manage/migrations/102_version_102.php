<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{

    public function up()
    {

        $CI = &get_instance();

        if( !$CI->db->field_exists('assign_project_owner', db_prefix() .'task_manage_tasks') )
        {


            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                ADD COLUMN `assign_project_owner` tinyint NULL DEFAULT 0 AFTER `task_order`;');

        }


    }

}
