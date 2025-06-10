<?php


hooks()->add_action("admin_init", function (){

    $CI = &get_instance();

    if (staff_can('perfex_table_manage', 'perfex_table_manage' )) {

        $CI->app_menu->add_setup_menu_item('table_manage_custom', [

            'href'     => admin_url('table_manage/setting/custom_table'),

            'name'     => _l('table_manage_custom_table'),

            'position' => 49,

            'badge'    => [],

        ]);

    }

});

/**
 * @note table columns saving module setting tab
 */
function table_manage_set_table_field_lists( $table_hook , $table_data )
{

    $CI = &get_instance();

    $db_table_name = db_prefix().'table_manage_table_fields';

    $table_info = $CI->db->select('id')->from($db_table_name)->where('table_hook',$table_hook)->get()->row();

    if ( empty( $table_info ) )
    {

        $key_index = 0;

        foreach ( $table_data  as $table_field )
        {

            if ( is_array( $table_field ) )
            {

                $field_text = isset( $table_field['name'] ) ? $table_field['name'] : json_encode( $table_field ) ;

            }
            else
            {

                $field_text = $table_field ;

            }


            $CI->db->insert($db_table_name , [
                'table_hook' => $table_hook ,
                'table_field_index' => $key_index ,
                'table_field_text' => $field_text ,
            ]);

            $key_index++;

        }

    }

}

function table_manage_get_table_fields( $table_hook )
{

    $CI = &get_instance();

    $db_table_name = db_prefix().'table_manage_table_fields';

    $table_fields = $CI->db->select('*')->from($db_table_name)->where('table_hook',$table_hook)->order_by('table_field_index')->get()->result_array();

    if ( !empty( $table_fields ) )
    {

        $new_index = table_manage_get_table_field_new_order( $table_hook );

        if ( !empty( $new_index ) )
        {

            return table_manage_reorder_table_fields( $table_fields , $new_index );

        }

    }

    return $table_fields;

}

function table_manage_get_table_field_new_order( $table_hook )
{

    $CI = &get_instance();

    $table_name = db_prefix().'table_manage_table_fields_new_order';

    $info = $CI->db->select('table_field_index')->from($table_name)->where('table_hook',$table_hook)->get()->row();

    if ( empty( $info->table_field_index ) )
        return [];

    $table_index = json_decode( $info->table_field_index , 1 );

    if ( is_array( $table_index ) )
        return  $table_index;

    return [];

}

/**
 * @note array nre order setting
 */
function table_manage_reorder_table_fields( $data_array , $index_array )
{

    $reordered_array = [];


    foreach ( $index_array as $index)
    {

        if( key_exists( $index ,  $data_array ) )
        {
            $reordered_array[] = $data_array[$index];
        }

    }


    foreach ($data_array as $index => $value)
    {

        if( !in_array( $index,  $index_array ) )
        {
            $reordered_array[] = $value;
        }

    }


    return $reordered_array;

}


hooks()->add_filter('customers_table_columns', function( $table_data )
{

    $table_hook = 'customers_table';

    table_manage_set_table_field_lists($table_hook , $table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('customers_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'customers_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );



hooks()->add_filter('customers_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'customers_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('invoices_table_columns', function( $table_data )
{

    $table_hook = 'invoices_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('invoices_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'invoices_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('invoices_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'invoices_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('leads_table_columns', function( $table_data )
{

    $table_hook = 'leads_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('leads_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'leads_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('leads_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'leads_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('contracts_table_columns', function( $table_data )
{

    $table_hook = 'contracts_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('contracts_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'contracts_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('contracts_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'contracts_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('estimates_table_columns', function( $table_data )
{

    $table_hook = 'estimates_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('estimates_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'estimates_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('estimates_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'estimates_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('expenses_table_columns', function( $table_data )
{

    $table_hook = 'expenses_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('expenses_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'expenses_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('expenses_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'expenses_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('projects_table_columns', function( $table_data )
{

    $table_hook = 'projects_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('projects_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'projects_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('projects_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'projects_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('proposals_table_columns', function( $table_data )
{

    $table_hook = 'proposals_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('proposals_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'proposals_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('proposals_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'proposals_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);





hooks()->add_filter('tasks_table_columns', function( $table_data )
{

    $table_hook = 'tasks_table';

    table_manage_set_table_field_lists($table_hook,$table_data);

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $table_data , $new_index );

    }

    return $table_data;

} , 999 );


hooks()->add_filter('tasks_table_sql_columns', function ( $aColumns )
{

    $table_hook = 'tasks_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $aColumns , $new_index );

    }

    return $aColumns;

} , 999 );


hooks()->add_filter('tasks_table_row_data', function ( $row , $aRow )
{

    $table_hook = 'tasks_table';

    $new_index = table_manage_get_table_field_new_order( $table_hook );

    if ( !empty( $new_index ) )
    {

        return table_manage_reorder_table_fields( $row , $new_index );

    }

    return $row;

},999,2);
