<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{

    public function up()
    {

        $CI = get_instance();



        if( !$CI->db->field_exists('es_status_change_date', db_prefix() .'projects') )
        {

            $CI->db->query('ALTER TABLE `'.db_prefix().'projects`
                                ADD COLUMN `es_status_change_date` datetime NULL AFTER `status`;');

        }



    }

}
