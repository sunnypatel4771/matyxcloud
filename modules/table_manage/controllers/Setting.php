<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Setting extends AdminController {

    protected static $json = [ 'success' => false , 'message' => '' ];

    public function __construct()
    {
        parent::__construct();

        // Db table checking
        if ( !$this->db->table_exists(db_prefix() . 'table_manage_settings'))
        {

            $this->db->query("CREATE TABLE `".db_prefix()."table_manage_settings` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `page_url` varchar(255) DEFAULT NULL,
                                `table_class` varchar(500) DEFAULT NULL,
                                `staff_id` int(11) DEFAULT 0,
                                `hiddens` varchar(150) DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `staff_id` (`staff_id`),
                                KEY `page_url` (`page_url`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                        ");

        }

        if ( !$this->db->table_exists(db_prefix() . 'table_manage_role_settings'))
        {

            $this->db->query("CREATE TABLE `".db_prefix()."table_manage_role_settings` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `page_url` varchar(255) DEFAULT NULL,
                                `table_class` varchar(500) DEFAULT NULL,
                                `role_id` int(11) DEFAULT 0,
                                `hiddens` varchar(150) DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `role_id` (`role_id`),
                                KEY `page_url` (`page_url`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                        ");

        }


        if ( !$this->db->field_exists('hiddens_export', db_prefix().'table_manage_settings') )
        {

            $this->db->query("ALTER TABLE `".db_prefix()."table_manage_settings` 
                                ADD COLUMN `hiddens_export` varchar(150) NULL AFTER `hiddens`;
                        ");

        }


        if ( !$this->db->field_exists('hiddens_export', db_prefix().'table_manage_role_settings') )
        {

            $this->db->query("ALTER TABLE `".db_prefix()."table_manage_role_settings` 
                                ADD COLUMN `hiddens_export` varchar(150) NULL AFTER `hiddens`;
                        ");

        }

        /**
         * Version 1.0.4
         */

        if ( !$this->db->table_exists(db_prefix() . 'table_manage_table_fields'))
        {

            $this->db->query("CREATE TABLE `".db_prefix()."table_manage_table_fields` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `table_hook` varchar(255) DEFAULT NULL,
                                  `table_field_text` varchar(255) DEFAULT NULL,
                                  `table_field_index` smallint(6) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `table_hook` (`table_hook`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                        ");

        }


        if ( !$this->db->table_exists(db_prefix() . 'table_manage_table_fields_new_order'))
        {

            $this->db->query("CREATE TABLE `".db_prefix()."table_manage_table_fields_new_order` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `table_hook` varchar(255) DEFAULT NULL,
                                  `table_field_index` varchar(500) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `table_hook` (`table_hook`)
                                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                        ");

        }


    }


    /**
     * Saving table header visible info
     */
    public function set_info()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');

        if( $this->input->is_ajax_request() && !empty( $page_url )  && !empty( $table_class ) )
        {


            $table_name = db_prefix().'table_manage_settings';
            $staff_id   = get_staff_user_id();

            $hide_index = $this->input->post('index');
            $checked    = $this->input->post('checked');

            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens')
                                    ->from($table_name)
                                    ->where('page_url',$page_url)
                                    ->where('table_class',$table_class)
                                    ->where('staff_id',$staff_id)
                                    ->get()
                                    ->row();

            $record_id      = 0;
            $hidden_columns = [];
            if( empty( $setting ) )
            {
                // new record

                if( $checked == 2 ) // will be hidden
                    $hidden_columns[ $hide_index ] = $hide_index;

            }
            else
            {
                $record_id = $setting->id;

                $hidden_columns = json_decode( $setting->hiddens , 1 );

                if( $checked == 2 )
                    $hidden_columns[$hide_index] = $hide_index ;
                elseif( isset( $hidden_columns[$hide_index] ) )
                    unset( $hidden_columns[$hide_index] );

            }

            $db_set = [
                'page_url'  => $page_url ,
                'table_class'   => $table_class ,
                'staff_id'  => $staff_id ,
                'hiddens'   => json_encode( $hidden_columns ) ,
            ];


            if( empty( $record_id ) )
                $this->db->insert( $table_name , $db_set );
            else
                $this->db->where('id',$record_id)->update( $table_name , $db_set );



        }

    }


    /**
     * Saving table export information
     */
    public function set_info_export()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');

        if( $this->input->is_ajax_request() && !empty( $page_url )  && !empty( $table_class ) )
        {


            $table_name = db_prefix().'table_manage_settings';
            $staff_id   = get_staff_user_id();

            $hide_index = $this->input->post('index');
            $checked    = $this->input->post('checked');

            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens_export')
                                    ->from($table_name)
                                    ->where('page_url',$page_url)
                                    ->where('table_class',$table_class)
                                    ->where('staff_id',$staff_id)
                                    ->get()
                                    ->row();

            $record_id      = 0;
            $hidden_columns = [];
            if( empty( $setting ) )
            {
                // new record

                if( $checked == 2 ) // will be not export
                    $hidden_columns[ $hide_index ] = $hide_index;

            }
            else
            {
                $record_id = $setting->id;

                $hidden_columns = json_decode( $setting->hiddens_export , 1 );

                if( $checked == 2 )
                    $hidden_columns[$hide_index] = $hide_index ;
                elseif( isset( $hidden_columns[$hide_index] ) )
                    unset( $hidden_columns[$hide_index] );

            }

            $db_set = [
                'page_url'  => $page_url ,
                'table_class'   => $table_class ,
                'staff_id'  => $staff_id ,
                'hiddens_export'   => json_encode( $hidden_columns ) ,
            ];


            if( empty( $record_id ) )
                $this->db->insert( $table_name , $db_set );
            else
                $this->db->where('id',$record_id)->update( $table_name , $db_set );


            echo json_encode( [
                'hidden_columns' => $hidden_columns ,
                'setting' => $setting
            ] );

        }

    }

    /**
     * Getting table header visible info
     */
    public function get_info()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');

        $has_record = 0 ;

        if( $this->input->is_ajax_request() && !empty( $page_url ) && !empty( $table_class ) )
        {


            $table_name = db_prefix().'table_manage_settings';
            $staff_id   = get_staff_user_id();


            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens,hiddens_export')
                                    ->from($table_name)
                                    ->where('page_url',$page_url)
                                    ->where('table_class',$table_class)
                                    ->where('staff_id',$staff_id)
                                    ->get()
                                    ->row();


            // table hidden columns
            $hidden_columns = [];
            $hiddens_export_columns = [];

            if( !empty( $setting ) )
            {

                $has_record = 1;

                if ( !empty( $setting->hiddens ) )
                {

                    $hiddens = json_decode( $setting->hiddens , 1 );

                    foreach ( $hiddens as $item )
                    {

                        $hidden_columns[ $item ] = $item;

                    }

                }


                if ( !empty( $setting->hiddens_export ) )
                {

                    $hiddens_export = json_decode( $setting->hiddens_export , 1 );

                    foreach ( $hiddens_export as $item )
                    {

                        $hiddens_export_columns[ $item ] = $item;

                    }

                }


            }



            // checking staff roles.

            $staff_role_info = get_staff( $staff_id );

            if( !is_admin() && !empty( $staff_role_info->role ) )
            {

                $setting_roles = $this->db->select('id,hiddens,hiddens_export')
                                        ->from(db_prefix().'table_manage_role_settings')
                                        ->where('page_url',$page_url)
                                        ->where('table_class',$table_class)
                                        ->where('role_id',$staff_role_info->role)
                                        ->get()
                                        ->row();


                if( !empty( $setting_roles ) )
                {

                    $has_record = 1;

                    if ( !empty( $setting_roles->hiddens ) )
                    {

                        $hiddens = json_decode( $setting_roles->hiddens , 1 );

                        foreach ( $hiddens as $item )
                        {

                            $hidden_columns[ $item ] = $item;

                        }

                    }

                    if ( !empty( $setting_roles->hiddens_export ) )
                    {

                        $hiddens_export = json_decode( $setting_roles->hiddens_export , 1 );

                        foreach ( $hiddens_export as $item )
                        {

                            $hiddens_export_columns[ $item ] = $item;

                        }

                    }

                }


            }


            /**
             * Bug fixed. $setting empty control changed to $hidden_columns
             */
            if( !empty( $hidden_columns ) || !empty( $hiddens_export_columns ) )
            {

                echo json_encode( [ 'headers' => $hidden_columns ,  'headers_export' => $hiddens_export_columns , 'has_record' => $has_record ] );

                die();

            }


        }

        echo json_encode( [ 'headers' => null , 'headers_export' => null , 'has_record' => $has_record ] );

    }


    /**
     * Getting table header for staff roles.
     */
    public function get_role_info()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');
        $role_id    = $this->input->post('role_id');

        if( $this->input->is_ajax_request() && !empty( $page_url ) && !empty( $table_class ) && !empty( $role_id ) )
        {


            $table_name = db_prefix().'table_manage_role_settings';

            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens,hiddens_export')
                                    ->from($table_name)
                                    ->where('page_url',$page_url)
                                    ->where('table_class',$table_class)
                                    ->where('role_id',$role_id)
                                    ->get()
                                    ->row();


            if( !empty( $setting ) )
            {

                echo json_encode( [

                    'headers' => json_decode( $setting->hiddens , 1 ) ,

                    'headers_export' => json_decode( $setting->hiddens_export , 1 ) ,

                ] );

                die();

            }


        }

        echo json_encode( [ 'headers' => null , 'headers_export' => null ] );

    }

    /**
     * Saving table header for staff roles
     */
    public function set_role_info()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');
        $role_id    = $this->input->post('role_id');

        if( $this->input->is_ajax_request() && !empty( $page_url ) && !empty( $table_class ) && !empty( $role_id ) )
        {


            $table_name = db_prefix().'table_manage_role_settings';
            $hiddens    = $this->input->post('hiddens');
            $hiddens_export = $this->input->post('hiddens_export');

            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens,hiddens_export')
                                ->from($table_name)
                                ->where('page_url',$page_url)
                                ->where('table_class',$table_class)
                                ->where('role_id',$role_id)
                                ->get()
                                ->row();

            $hidden_columns = [];
            $hidden_export_columns = [];

            if( !empty( $hiddens ) )
            {

                foreach ( $hiddens as $hidden)
                {

                    $hidden_columns[ $hidden ] = $hidden;

                }

            }


            if( !empty( $hiddens_export ) )
            {

                foreach ( $hiddens_export as $hidden)
                {

                    $hidden_export_columns[ $hidden ] = $hidden;

                }

            }


            $db_set = [
                'page_url'      => $page_url ,
                'table_class'   => $table_class ,
                'role_id'       => $role_id ,
                'hiddens'       => json_encode( $hidden_columns ) ,
                'hiddens_export'       => json_encode( $hidden_export_columns ) ,
            ];


            if( empty( $setting ) )
                $this->db->insert( $table_name , $db_set );
            else
                $this->db->where('id',$setting->id)->update( $table_name , $db_set );


        }

    }


    /**
     * Saving table header for current roles
     */
    public function set_info_multi()
    {

        $page_url   = $this->input->post('page_url');
        $table_class= $this->input->post('table_class');

        if( $this->input->is_ajax_request() && !empty( $page_url ) && !empty( $table_class )  )
        {


            $table_name = db_prefix().'table_manage_settings';
            $staff_id   = get_staff_user_id();

            $hiddens    = $this->input->post('hiddens');
            $hiddens_export = $this->input->post('hiddens_export');

            $page_url   = str_replace( site_url() , '' , $page_url );
            $page_url   = preg_replace('/\d+/', '', $page_url);


            $setting    = $this->db->select('id,hiddens,hiddens_export')
                                ->from($table_name)
                                ->where('page_url',$page_url)
                                ->where('table_class',$table_class)
                                ->where('staff_id',$staff_id)
                                ->get()
                                ->row();

            $hidden_columns = [];
            $hidden_export_columns = [];

            if( !empty( $hiddens ) )
            {

                foreach ( $hiddens as $hidden)
                {

                    $hidden_columns[ $hidden ] = $hidden;

                }

            }


            if( !empty( $hiddens_export ) )
            {

                foreach ( $hiddens_export as $hidden)
                {

                    $hidden_export_columns[ $hidden ] = $hidden;

                }

            }


            $db_set = [
                'page_url'      => $page_url ,
                'table_class'   => $table_class ,
                'staff_id'      => $staff_id ,
                'hiddens'       => json_encode( $hidden_columns ) ,
                'hiddens_export'    => json_encode( $hidden_export_columns ) ,
            ];


            if( empty( $setting ) )
                $this->db->insert( $table_name , $db_set );
            else
                $this->db->where('id',$setting->id)->update( $table_name , $db_set );

            echo json_encode( [ true ] );

        }

    }



    /**
     * @Version 1.0.4
     *
     * Custom table order column
     */
    public function custom_table()
    {

        if (!has_permission('perfex_table_manage', '', 'perfex_table_manage')) {

            access_denied('table_manage');

        }

        $data['title'] = _l('table_manage');

        $data['tabs'] = $this->setting_tabs();

        $active_tab = $this->input->get('tab');

        if ( empty( $active_tab ) )
            $active_tab = 'invoices_table';

        $data['table_columns']  = table_manage_get_table_fields( $active_tab );

        $data['active_tab'] = $active_tab;

        $this->load->view('v_custom_table' , $data);

    }

    private function setting_tabs()
    {

        $tabs = [];

        $tabs[] = [  'slug' => 'invoices_table'    , 'text' => _l('invoice') , 'url' => 'invoices', 'icon' => 'fa fa-file' ];

        $tabs[] = [  'slug' => 'customers_table'   , 'text' => _l('client')  , 'url' => 'clients' , 'icon' => 'fa fa-user' ];

        $tabs[] = [  'slug' => 'leads_table'       , 'text' => _l('lead')    , 'url' => 'leads' , 'icon' => 'fa fa-tty' ];

        $tabs[] = [  'slug' => 'contracts_table'       , 'text' => _l('contract')    , 'url' => 'contracts' , 'icon' => 'fa fa-file-contract' ];

        $tabs[] = [  'slug' => 'estimates_table'       , 'text' => _l('estimate')    , 'url' => 'estimates' , 'icon' => 'fa fa-file' ];

        $tabs[] = [  'slug' => 'expenses_table'       , 'text' => _l('expense')    , 'url' => 'expenses' , 'icon' => 'fa fa-file-lines' ];

        $tabs[] = [  'slug' => 'projects_table'       , 'text' => _l('project')    , 'url' => 'projects' , 'icon' => 'fa fa-chart-gantt' ];

        $tabs[] = [  'slug' => 'proposals_table'       , 'text' => _l('proposal')    , 'url' => 'proposals' , 'icon' => 'fa fa-file' ];

        $tabs[] = [  'slug' => 'tasks_table'       , 'text' => _l('task')    , 'url' => 'tasks' , 'icon' => 'fa fa-circle-check' ];

        return $tabs;

    }

    public function reset_custom_table( $table_hook = '' )
    {

        if ( $this->input->is_ajax_request() && $table_hook != '' )
        {

            if ( staff_can('perfex_table_manage',  'perfex_table_manage') )
            {

                $this->db->where('table_hook',$table_hook)->delete( db_prefix().'table_manage_table_fields');

            }

        }

        echo json_encode( true );

    }

    public function save_custom_table( )
    {

        if ( $this->input->post() && staff_can('perfex_table_manage',  'perfex_table_manage') )
        {

            $table_hook     = $this->input->post('table_hook');
            $field_index    = $this->input->post('field_index');

            if ( !empty( $table_hook ) && !empty( $field_index ) )
            {

                $table_name = db_prefix().'table_manage_table_fields_new_order';

                $info = $this->db->select('id')->from($table_name)->where('table_hook',$table_hook)->get()->row();

                $field_index_json = [];

                foreach ( $field_index  as $field_index )
                {
                    $field_index_json[] = $field_index;
                }

                $field_index_json = json_encode( $field_index_json );

                if ( empty( $info ) )
                {

                    $this->db->insert($table_name,[ 'table_hook' => $table_hook , 'table_field_index' => $field_index_json ] );

                }
                else
                {

                    $this->db->where('id',$info->id)->update($table_name,[ 'table_hook' => $table_hook , 'table_field_index' => $field_index_json ] );

                }



            }

        }

        set_alert('success' , _l('updated_successfully'  , _l('table_manage') ) );

        redirect($_SERVER['HTTP_REFERER']);


    }

}
