<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Google Worksuite Integration
Module URI: https://codecanyon.net/item/google-sheets-module-for-perfex-crm-twoway-spreadsheets-synchronization/53297436
Description: Two-way Spreadsheets and Documents Synchronization between Perfex and Google Docs/Sheets
Version: 1.2.0
Requires at least: 1.0.*
Author: Themesic Interactive
Author URI: https://1.envato.market/themesic
*/

define('GOOGLE_DRIVE_MODULE_NAME', 'google_drive');
define('GOOGLE_DRIVE_MODULE', 'google_drive');
$CI = &get_instance();
require_once __DIR__.'/vendor/autoload.php';

/**
 * Load the module helper
 */
$CI->load->helper(GOOGLE_DRIVE_MODULE_NAME . '/google_drive');

modules\google_drive\core\Apiinit::the_da_vinci_code(GOOGLE_DRIVE_MODULE);
modules\google_drive\core\Apiinit::ease_of_mind(GOOGLE_DRIVE_MODULE);

/**
 * Register activation module hook
 */
register_activation_hook(GOOGLE_DRIVE_MODULE_NAME, 'google_drive_activation_hook');

function google_drive_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(GOOGLE_DRIVE_MODULE_NAME, [GOOGLE_DRIVE_MODULE_NAME]);

/**
 * Actions for inject the custom styles
 */
hooks()->add_action('admin_init', 'google_drive_init_menu_items');
hooks()->add_action('admin_init', 'google_drive_permissions');

/**
 * Init theme style module menu items in setup in admin_init hook
 * @return null
 */
function google_drive_init_menu_items()
{
    if (staff_can('setting', 'google_drive') || staff_can('view', 'google_drive') || staff_can('create', 'google_drive') || staff_can('edit', 'google_drive') || staff_can('delete', 'google_drive')) {
        $CI = &get_instance();

        /**
         * If the logged in user is administrator, add custom menu in Setup
         */
        $CI->app_menu->add_sidebar_menu_item('google-drive', [
            'name'     => _l('google_drive'),
            'icon'     => 'fa-solid fa-sheet-plastic',
            'collapse' => true,
            'position' => 65,
        ]);
        if (staff_can('setting', 'google_drive')) {
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-settings',
                'name'     => _l('google_drive_settings'),
                'href'     => admin_url('google_drive/settings'),
                'position' => 10,
                'badge'    => [],
            ]);
        }
        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-docs',
            'name'     => _l('google_drive_google_docs'),
            'href'     => admin_url('google_drive/docs'),
            'position' => 10,
            'badge'    => [],
        ]);
        $CI->app_menu->add_sidebar_children_item('google-drive', [
            'slug'     => 'google-drive-google-spreadsheets',
            'name'     => _l('google_drive_google_sheets'),
            'href'     => admin_url('google_drive/sheets'),
            'position' => 10,
            'badge'    => [],
        ]);
    }
}

hooks()->add_action('app_init', GOOGLE_DRIVE_MODULE . '_actLib');
function google_drive_actLib()
{
    $CI = &get_instance();
    $CI->load->library(GOOGLE_DRIVE_MODULE . '/Google_drive_aeiou');
    $envato_res = $CI->google_drive_aeiou->validatePurchase(GOOGLE_DRIVE_MODULE);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', GOOGLE_DRIVE_MODULE . '_sidecheck');
function google_drive_sidecheck($module_name)
{
    if (GOOGLE_DRIVE_MODULE == $module_name['system_name']) {
        modules\google_drive\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', GOOGLE_DRIVE_MODULE . '_deregister');
function google_drive_deregister($module_name)
{
    if (GOOGLE_DRIVE_MODULE == $module_name['system_name']) {
        delete_option(GOOGLE_DRIVE_MODULE . '_verification_id');
        delete_option(GOOGLE_DRIVE_MODULE . '_last_verification');
        delete_option(GOOGLE_DRIVE_MODULE . '_product_token');
        delete_option(GOOGLE_DRIVE_MODULE . '_heartbeat');
    }
}

function google_drive_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'setting'   => _l('google_drive_permission_settings'),
        'view'      => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'    => _l('permission_create'),
        'edit'      => _l('permission_edit'),
        'delete'    => _l('permission_delete'),
    ];

    register_staff_capabilities('google_drive', $capabilities, _l('google_drive'));
}