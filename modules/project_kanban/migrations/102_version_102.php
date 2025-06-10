<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{

    public function up()
    {

        $CI = get_instance();

        if ( !$CI->db->table_exists( db_prefix() . 'project_kanban_settings' ) )
        {

            $CI->db->query("
                    CREATE TABLE `".db_prefix()."project_kanban_settings` (
                        `status_id` int(11) NOT NULL 
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }

    }

}
