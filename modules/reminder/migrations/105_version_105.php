<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_105 extends App_module_migration
{
  public function up()
  {
    $CI = &get_instance();
    $Table = db_prefix() . 'reminders';
    if (!$CI->db->field_exists('customer', db_prefix() . 'reminders')) {
      $CI->db->query('ALTER TABLE `' . $Table . '` ADD `user_type` varchar(50) DEFAULT "customer";');
    }

    $row_exists = $CI->db->query('SELECT * FROM ' . db_prefix() . 'emailtemplates WHERE type = "lead" and slug = "reminder-send-to-lead" and language = "english";')->row();
    if (!$row_exists) {
      $message = '<p>Hi {name}<br /><br /><strong>Description:</strong> {item_description}<br /></p>';
      $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('leads', 'reminder-send-to-lead', 'english', 'Reminder', 'New Reminder','" . $message . "','', NULL, 0, 1, 0);");
      foreach ($CI->app->get_available_languages() as $avLanguage) {
        if ($avLanguage != 'english' && total_rows(db_prefix() . "emailtemplates", ['slug' => 'reminder-send-to-lead', 'type' => 'lead', 'language' => $avLanguage]) == 0) {
          $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('leads', 'reminder-send-to-lead', '" . $avLanguage . "', 'Reminder [" . $avLanguage . "]', 'New Reminder','" . $message . "','', NULL, 0, 1, 0);");
        }
      }
    }
    if ($CI->db->field_exists('contact', db_prefix() . 'reminders')) {
      $CI->db->query('ALTER TABLE `' . $Table . '` CHANGE `contact` `contact` VARCHAR(100) NULL DEFAULT NULL;');
    }
    if ($CI->db->field_exists('rel_type', db_prefix() . 'reminders')) {
      $CI->db->query('ALTER TABLE `' . $Table . '` CHANGE `rel_type` `rel_type` VARCHAR(40) NULL DEFAULT NULL;');
    }
    $row_exists = $CI->db->query('SELECT * FROM ' . db_prefix() . 'emailtemplates WHERE type = "leads" and slug = "reminder-service-send-to-lead" and language = "english";')->row();
    if (!$row_exists) {
      $message = '<p>Hi {contact_name}<br /><br /><strong>Description:</strong> {item_description}<br /></p>';
      $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('leads', 'reminder-service-send-to-lead', 'english', 'Services', 'New Service','" . $message . "','', NULL, 0, 1, 0);");
      foreach ($CI->app->get_available_languages() as $avLanguage) {
        if ($avLanguage != 'english') {
          $CI->db->query("INSERT INTO `" . db_prefix() . "emailtemplates` (`type`, `slug`, `language`, `name`, `subject`, `message`, `fromname`, `fromemail`, `plaintext`, `active`, `order`) VALUES ('leads', 'reminder-service-send-to-lead', '" . $avLanguage . "', 'Services [" . $avLanguage . "]', 'New Service','" . $message . "','', NULL, 0, 1, 0);");
        }
      }
    }
  }
}
