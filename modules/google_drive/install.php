<?php

defined('BASEPATH') or exit('No direct script access allowed');

add_option('google_drive_client_id', '');
add_option('google_drive_client_secret', '');

if (!$CI->db->table_exists(db_prefix() . 'google_drives')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'google_drives` (
        `id` int(11) NOT NULL,
        `staffid` int(11) NOT NULL,
        `driveid` varchar(256) NOT NULL,
        `title` varchar(256) NOT NULL,
        `description` varchar(1024) NULL,
        `type` varchar(256) NOT NULL,
        `date` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'google_drives` ADD PRIMARY KEY (`id`);');
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'google_drives` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}