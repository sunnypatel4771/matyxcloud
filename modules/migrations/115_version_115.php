<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_115 extends App_module_migration
{

    public function up()
    {

        $CI = get_instance();


        if( !$CI->db->field_exists('related', db_prefix() .'task_manage_tasks') )
        {

            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                    ADD COLUMN `related` varchar(20) NULL DEFAULT \'project\' AFTER `task_completed_project_status`;');

        }



        if( !$CI->db->field_exists('task_is_public', db_prefix() .'task_manage_tasks') )
        {

            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                ADD COLUMN `task_is_public` tinyint NULL DEFAULT 0 AFTER `related`,
                                ADD COLUMN `task_is_billable` tinyint NULL DEFAULT 0 AFTER `task_is_public`,
                                ADD COLUMN `task_visible_to_client` tinyint NULL DEFAULT 0 AFTER `task_is_billable`;');

        }



    }

}
