<?php

defined("BASEPATH") or exit("No direct script access allowed");

/*
Module Name: User Mention
Description: Enable mentioning staff in statements and sending notifications to your staff
Author: Halil
Author URI: https://codecanyon.net/user/halilaltndg
Version: 1.0.1
*/


define('USER_MENTION_MODULE_NAME', "user_mention");


$CI = &get_instance();

register_language_files(USER_MENTION_MODULE_NAME, [USER_MENTION_MODULE_NAME]);


$CI->load->helper('user_mention/user_mention');


hooks()->add_action("admin_init", "user_mention_permission");

hooks()->add_action("app_admin_footer", "user_mention_include_footer_static_assets");


/**
 *
 * Table js file and model html are included
 *
 */
function user_mention_include_footer_static_assets()
{

    if( staff_can( 'perfex_user_mention' , 'perfex_user_mention'  ) )
    {

        $users = get_instance()->db->select('firstname,lastname')->from(db_prefix().'staff')->where('active',1)->get()->result();

        $user_mention = [];

        if ( !empty( $users ) )
        {

            foreach ( $users as $user )
            {
                if ( !empty( $user->firstname ) || !empty( $user->lastname ) )
                    $user_mention[] = "$user->firstname $user->lastname";
            }

            echo " 
                <script>
                
                    var user_mention_data = ".( json_encode( $user_mention ) )."
                
                </script>
            
                <link rel='stylesheet' href='". base_url( "modules/user_mention/assets/jquery.atwho.min.css?v=1")."'>
                    
                <script src='" . base_url("modules/user_mention/assets/jquery.caret.min.js?v=1") ."'></script> 
                <script src='" . base_url("modules/user_mention/assets/jquery.atwho.min.js?v=1") ."'></script> 
                <script src='" . base_url("modules/user_mention/assets/user_mention.js?v=2") ."'></script> 
                
                ";

        }


    }

}


/**
 * Permission
 */
function user_mention_permission()
{

    $capabilities = [];

    $capabilities["capabilities"] = [

        "perfex_user_mention"      => _l('user_mention_permission') ,

    ];

    register_staff_capabilities("perfex_user_mention", $capabilities , _l('user_mention_permission') );

}


require_once __DIR__ . '/includes/mention_hooks.php';
