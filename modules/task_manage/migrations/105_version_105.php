<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{

    public function up()
    {

        $CI = &get_instance();

        $CI->db->query(' ALTER TABLE `'.db_prefix().'task_manage_tasks` ADD INDEX(`task_order`); ');

        $CI->db->query(' ALTER TABLE `'.db_prefix().'milestones` ADD INDEX(`project_id`); ');

    }

}
