<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_113 extends App_module_migration
{

    public function up()
    {

        $CI = get_instance();

        $CI->db->query('ALTER TABLE `'.db_prefix().'projects` ADD INDEX(`task_manage_groups`);');

        $CI->db->query('ALTER TABLE `'.db_prefix().'tasks` ADD INDEX(`task_manage_task_id`);');

    }

}
