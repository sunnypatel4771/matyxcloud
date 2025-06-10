<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Migration_Version_104 extends App_module_migration

{

     public function up()

     {

         $CI = get_instance();


         if ( !$CI->db->table_exists(db_prefix() . 'table_manage_table_fields'))
         {

             $CI->db->query("CREATE TABLE `".db_prefix()."table_manage_table_fields` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `table_hook` varchar(255) DEFAULT NULL,
                                  `table_field_text` varchar(255) DEFAULT NULL,
                                  `table_field_index` smallint(6) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `table_hook` (`table_hook`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                        ");

         }


         if ( !$CI->db->table_exists(db_prefix() . 'table_manage_table_fields_new_order'))
         {

             $CI->db->query("CREATE TABLE `".db_prefix()."table_manage_table_fields_new_order` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `table_hook` varchar(255) DEFAULT NULL,
                                  `table_field_index` varchar(500) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `table_hook` (`table_hook`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                        ");

         }


     }

}

