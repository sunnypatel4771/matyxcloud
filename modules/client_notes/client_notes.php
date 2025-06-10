<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Client Notes
Description: Module provides facility communicate with client.
Author: Sunny Patel
Version: 1.0.0
Requires at least: 2.3.*
Author URI: https://palladiumhub.com/
*/



define('CLIENT_NOTES_MODULE_NAME', 'client_notes');

register_activation_hook(CLIENT_NOTES_MODULE_NAME, 'client_notes_module_activation_hook');

function client_notes_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
register_language_files(CLIENT_NOTES_MODULE_NAME, [CLIENT_NOTES_MODULE_NAME]);

hooks()->add_action('admin_init', 'client_notes_module_init_menu_items');

function client_notes_module_init_menu_items(){
    $CI = &get_instance();
    $CI->app_tabs->add_customer_profile_tab('client_notes', [
        'name'     => '' . _l('client_notes') . '',
        'view'     => 'client_notes/client_notes',
        'position' => 11,
        'icon'     => 'fa-regular fa-note-sticky menu-icon'
    ]);

    $CI->app_scripts->add(CLIENT_NOTES_MODULE_NAME . '-js', base_url('modules/' . CLIENT_NOTES_MODULE_NAME . '/assets/js/' . CLIENT_NOTES_MODULE_NAME . '.js?v=' . time()));
}
$CI = &get_instance();
$CI->load->helper(CLIENT_NOTES_MODULE_NAME . '/client_notes');


hooks()->add_action('clients_init', 'change_default_client_note_menu', 50);

function change_default_client_note_menu()
{
    // if (has_contact_permission('job_card')) {
    if (is_client_logged_in()) {
        $get_client_user_id = get_client_user_id();
            add_theme_menu_item('client_notes', [
                'name'     => _l('client_notes'),
                'href'     => site_url('client_notes/client_notes_client'),
                'position' => 45,
            ]);
    }
}