<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Google Worksuite Integration
Module URI: https://codecanyon.net/item/google-sheets-module-for-perfex-crm-twoway-spreadsheets-synchronization/53297436
Description: Two-way Spreadsheets and Documents Synchronization between Perfex and Google Docs/Sheets
Version: 1.4.0
Requires at least: 1.0.*
Author: Themesic Interactive
Author URI: https://1.envato.market/themesic
*/

define('GOOGLE_WORKSPACE_MODULE_NAME', 'google_workspace');
define('GOOGLE_WORKSPACE_MODULE', 'google_workspace');

$CI = &get_instance();

require_once __DIR__.'/vendor/autoload.php';

/**
 * Load the module helper
 */
$CI->load->helper(GOOGLE_WORKSPACE_MODULE_NAME . '/google_workspace');


/**
 * Register activation module hook
 */
register_activation_hook(GOOGLE_WORKSPACE_MODULE_NAME, 'google_workspace_activation_hook');

function google_workspace_activation_hook()
{
    $CI = &get_instance();

    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(GOOGLE_WORKSPACE_MODULE_NAME, [GOOGLE_WORKSPACE_MODULE_NAME]);

/**
 * Actions for inject the custom styles
 */
hooks()->add_action('admin_init', 'google_workspace_init_menu_items');

hooks()->add_action('admin_init', 'google_workspace_permissions');

/**
 * Init theme style module menu items in setup in admin_init hook
 * @return null
 */
function google_workspace_init_menu_items()
{
    if (staff_can('setting', 'google_workspace') || staff_can('view', 'google_workspace') || staff_can('create', 'google_workspace') || staff_can('edit', 'google_workspace') || staff_can('delete', 'google_workspace')) {
        $CI = &get_instance();

        /**
         * If the logged in user is administrator, add custom menu in Setup
         */
        $CI->app_menu->add_sidebar_menu_item('google-drive', [
            'name'     => _l('google_workspace'),
            'icon'     => 'fa-solid fa-sheet-plastic',
            'collapse' => true,
            'position' => 65,
        ]);

        if (staff_can('setting', 'google_workspace')) {
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-settings',
                'name'     => _l('google_workspace_settings'),
                'href'     => admin_url('google_workspace/settings'),
                'position' => 10,
                'badge'    => [],
            ]);
        }

        if (get_option('google_workspace_client_id') && get_option('google_workspace_client_secret')) {
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-google-docs',
                'name'     => _l('google_workspace_google_docs'),
                'href'     => admin_url('google_workspace/docs'),
                'position' => 20,
                'badge'    => [],
            ]);
    
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-google-spreadsheets',
                'name'     => _l('google_workspace_google_sheets'),
                'href'     => admin_url('google_workspace/sheets'),
                'position' => 30,
                'badge'    => [],
            ]);

            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-google-slides',
                'name'     => _l('google_workspace_google_slides'),
                'href'     => admin_url('google_workspace/slides'),
                'position' => 40,
                'badge'    => [],
            ]);
    
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-google-forms',
                'name'     => _l('google_workspace_google_forms'),
                'href'     => admin_url('google_workspace/forms'),
                'position' => 50,
                'badge'    => [],
            ]);
    
            $CI->app_menu->add_sidebar_children_item('google-drive', [
                'slug'     => 'google-drive-google-drives',
                'name'     => _l('google_workspace_google_drive'),
                'href'     => admin_url('google_workspace/drives'),
                'position' => 60,
                'badge'    => [],
            ]);
        }
    }
}


function google_workspace_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'setting'   => _l('google_workspace_permission_settings'),
        'view'      => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create'    => _l('permission_create'),
        'edit'      => _l('permission_edit'),
        'delete'    => _l('permission_delete'),
    ];

    register_staff_capabilities('google_workspace', $capabilities, _l('google_workspace'));
}

function google_workspace_supported_until() {

    if (get_option('extra_support_notice') == 0) {
        return;
    } else { 	
    $supported_until = get_option(GOOGLE_WORKSPACE_MODULE.'_supported_until'); 
    if (empty($supported_until)) {
        return;
    }
		$date_only = substr($supported_until, 0, 10);
		$supported_until_timestamp = strtotime($date_only);
		$current_date_timestamp = time();
		if ($supported_until_timestamp < ($current_date_timestamp - (6 * 30 * 24 * 60 * 60))) {
		echo '<div class="supported_until alert alert-warning" style="font-size: 16px; background-color: #fff3cd; border-color: #ffeeba; color: #856404; 
            position: fixed; top: 50px; left: 50%; padding: 20px; transform: translateX(-50%); z-index: 9999; width: 90%; max-width: 600px; box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;">
				<img style="max-width:100px;" src="https://themesic.com/wp-content/uploads/2023/07/cropped-logo-with-text-minus.png"><br><br>
				<p>⚠️ The support period for one of your modules seems over.<br><br>We offer an alternative way to receive <strong>free support</strong> for potential issues,<br>simply by rating our product on <img style="max-width:80px;" src="https://themesic.com/wp-content/plugins/fast-plugin/assets/images/envato.svg">. <a href="https://1.envato.market/themesic" target="_blank" style="text-decoration:underline !important;"><strong> Click here to do that</strong></a> 👈</p><br>
				<p>Your feedback help us continue developing and improving the product!</p>
				<br /><br />
				<a href="?dismiss=true" class="alert-link" style="text-decoration:underline !important;">Okay, thanks for the notice</a> ✔️
			</div></center>';
		}
    } 
}

// Check for the dismiss URL and update the option
if (isset($_GET['dismiss']) && $_GET['dismiss'] === 'true') {
    update_option('extra_support_notice', 0); // Dismiss the notice
    // Redirect to clear the URL parameter and avoid it being triggered again
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

hooks()->add_action('app_admin_head', 'google_workspace_supported_until');

function google_workspace_hide_support_extension() {
    echo "<script>
        jQuery(document).ready(function($) {
            // Get all elements with class 'supported_until'
            var divs = $('.supported_until');
            console.log('Total .supported_until divs:', divs.length); // Log how many divs are rendered
            
            // If more than one div, hide all except the first
            if (divs.length > 1) {
                divs.slice(1).hide(); // Hide all but the first one
            }
        });
    </script>";
}


	
hooks()->add_action('app_admin_footer', 'google_workspace_hide_support_extension');
