<?php

class Migration_Version_120 extends App_module_migration
{

    /**
     * We use basic table tickets_status
     * Add new column is_active on client view
     * Update exists ticket statuses on is_active = 1
     */
    public function up()
    {
        $CI = &get_instance();

        $query = $CI->db->query("SHOW COLUMNS FROM `" . db_prefix() . "tickets_status` LIKE 'is_active'");

        if ($query->num_rows() == 0)
        {
            $CI->db->query(
                "ALTER TABLE `" . db_prefix() . "tickets_status`
                    ADD COLUMN `is_active` TINYINT(1)
                    DEFAULT 1;"
            );

            $CI->db->query(
                "UPDATE `" . db_prefix() . "tickets_status`
                    SET `is_active` = 1;"
            );
        }
    }

    /**
     * We use basic table tickets_status
     * Remove column is_active
     */
    public function down()
    {
        $CI = &get_instance();

        $CI->db->query(
            "ALTER TABLE `" . db_prefix() . "tickets_status`
                DROP COLUMN `is_active`;"
        );
    }

}
