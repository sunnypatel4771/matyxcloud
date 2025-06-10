<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'clientnotes')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "clientnotes` (
        `id` int(255) NOT NULL AUTO_INCREMENT,
        `userid` int(255) NOT NULL,
        `type` varchar(100) NOT NULL,
        `note` text NOT NULL,
        `date` datetime NOT NULL,
        `staffid` int(10) DEFAULT NULL,
        `msg_status` enum('sent','received') NOT NULL DEFAULT 'sent',
        PRIMARY KEY (`id`)
    );");
    
}