<?php

function task_manage_task_render_custom_fields( $belongs_to ,  $rel_id = false )
{

    // Is custom fields for items and in add/edit

    $items_add_edit_preview = isset($items_cf_params['add_edit_preview']) && $items_cf_params['add_edit_preview'] ? true : false;



    // Is custom fields for items and in add/edit area for this already added

    $items_applied = isset($items_cf_params['items_applied']) && $items_cf_params['items_applied'] ? true : false;



    // Used for items custom fields to add additional name on input

    $part_item_name = isset($items_cf_params['part_item_name']) ? $items_cf_params['part_item_name'] : '';



    // Is this custom fields for predefined items Sales->Items

    $items_pr = isset($items_cf_params['items_pr']) && $items_cf_params['items_pr'] ? true : false;



    $is_admin = is_admin();



    $CI = & get_instance();

    $CI->db->where('active', 1);

    $CI->db->where('fieldto', $belongs_to);





    $CI->db->order_by('field_order', 'asc');

    $fields = $CI->db->get(db_prefix() . 'customfields')->result_array();



    $fields_html = '';



    if (count($fields)) {

        if (!$items_add_edit_preview && !$items_applied) {

            $fields_html .= '<div class="row custom-fields-form-row">';

        }



        foreach ($fields as $field) {

            if ($field['only_admin'] == 1 && !$is_admin) {

                continue;

            }



            $field['name'] = _maybe_translate_custom_field_name($field['name'], $field['slug']);



            $value = '';

            if ($field['bs_column'] == '' || $field['bs_column'] == 0) {

                $field['bs_column'] = 12;

            }



            if (!$items_add_edit_preview && !$items_applied) {

                $fields_html .= '<div class="col-md-' . $field['bs_column'] . '">';

            } elseif ($items_add_edit_preview) {

                $fields_html .= '<td class="custom_field" data-id="' . $field['id'] . '">';

            } elseif ($items_applied) {

                $fields_html .= '<td class="custom_field">';

            }



            if ($is_admin

                && ($items_add_edit_preview == false && $items_applied == false)

                && (!defined('CLIENTS_AREA') || hooks()->apply_filters('show_custom_fields_edit_link_on_clients_area', false))) {

                $fields_html .= '<a href="' . admin_url('custom_fields/field/' . $field['id']) . '" tabindex="-1" target="_blank" class="custom-field-inline-edit-link"><i class="fa-regular fa-pen-to-square"></i></a>';

            }



            if ($rel_id !== false) {

                if (!is_array($rel_id)) {

                    $value = get_task_manage_task_custom_field_value($rel_id, $field['id'], ($items_pr ? 'items_pr' : $belongs_to), false);

                } else {

                    if (is_custom_fields_smart_transfer_enabled()) {

                        // Used only in:

                        // 1. Convert proposal to estimate, invoice

                        // 2. Convert estimate to invoice

                        // This feature is executed only on CREATE, NOT EDIT

                        $transfer_belongs_to = $rel_id['belongs_to'];

                        $transfer_rel_id     = $rel_id['rel_id'];

                        $tmpSlug             = explode('_', $field['slug'], 2);

                        if (isset($tmpSlug[1])) {

                            $CI->db->where('fieldto', $transfer_belongs_to);

                            $CI->db->group_start();

                            $CI->db->like('slug', $rel_id['belongs_to'] . '_' . $tmpSlug[1], 'after');

                            $CI->db->where('type', $field['type']);

                            $CI->db->where('options', $field['options']);

                            $CI->db->where('active', 1);

                            $CI->db->group_end();

                            $cfTransfer = $CI->db->get(db_prefix() . 'customfields')->result_array();



                            // Don't make mistakes

                            // Only valid if 1 result returned

                            // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%

                            //

                            if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {

                                $value = get_task_manage_task_custom_field_value($transfer_rel_id, $cfTransfer[0]['id'], $transfer_belongs_to, false);

                            }

                        }

                    }

                }

            } elseif ($field['default_value'] && $field['type'] != 'link') {

                if (in_array($field['type'], ['date_picker_time', 'date_picker'])) {

                    if ($timestamp = strtotime($field['default_value'])) {

                        $value = $field['type'] == 'date_picker' ? date('Y-m-d', $timestamp) : date('Y-m-d H:i', $timestamp);

                    }

                } else {

                    $value = $field['default_value'];

                }

            }



            $_input_attrs = [];



            if ($field['required'] == 1) {

                $_input_attrs['data-custom-field-required'] = true;

            }



            if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {

                $_input_attrs['disabled'] = true;

            }



            $_input_attrs['data-fieldto'] = $field['fieldto'];

            $_input_attrs['data-fieldid'] = $field['id'];



            $cf_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';



            if ($part_item_name != '') {

                $cf_name = $part_item_name . '[custom_fields][items][' . $field['id'] . ']';

            }



            if ($items_add_edit_preview) {

                $cf_name = '';

            }



            $field_name = $field['name'];



            if ($field['type'] == 'input' || $field['type'] == 'number') {

                $t = $field['type'] == 'input' ? 'text' : 'number';

                $fields_html .= render_input($cf_name, $field_name, $value, $t, $_input_attrs);

            } elseif ($field['type'] == 'date_picker') {

                $fields_html .= render_date_input($cf_name, $field_name, _d($value), $_input_attrs);

            } elseif ($field['type'] == 'date_picker_time') {

                $fields_html .= render_datetime_input($cf_name, $field_name, _dt($value), $_input_attrs);

            } elseif ($field['type'] == 'textarea') {

                $fields_html .= render_textarea($cf_name, $field_name, $value, $_input_attrs);

            } elseif ($field['type'] == 'colorpicker') {

                $fields_html .= render_color_picker($cf_name, $field_name, $value, $_input_attrs);

            } elseif ($field['type'] == 'select' || $field['type'] == 'multiselect') {

                $_select_attrs = [];

                $select_attrs  = '';

                $select_name   = $cf_name;



                if ($field['required'] == 1) {

                    $_select_attrs['data-custom-field-required'] = true;

                }



                if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {

                    $_select_attrs['disabled'] = true;

                }



                $_select_attrs['data-fieldto'] = $field['fieldto'];

                $_select_attrs['data-fieldid'] = $field['id'];



                if ($field['type'] == 'multiselect') {

                    $_select_attrs['multiple'] = true;

                    $select_name .= '[]';

                }



                foreach ($_select_attrs as $key => $val) {

                    $select_attrs .= $key . '=' . '"' . $val . '" ';

                }



                if ($field['required'] == 1) {

                    $field_name = '<small class="req text-danger">* </small>' . $field_name;

                }



                $fields_html .= '<div class="form-group">';

                $fields_html .= '<label for="' . $cf_name . '" class="control-label">' . $field_name . '</label>';

                $fields_html .= '<select ' . $select_attrs . ' name="' . $select_name . '" class="' . ($items_add_edit_preview == false ? 'select-placeholder ': '') . 'selectpicker form-control' . ($field['type'] == 'multiselect' ? ' custom-field-multi-select' : '') . '" data-width="100%" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '"  data-live-search="true">';



                $fields_html .= '<option value=""' . ($field['type'] == 'multiselect' ? ' class="hidden"' : '') . '></option>';



                $options = explode(',', $field['options']);



                if ($field['type'] == 'multiselect') {

                    $value = explode(',', $value);

                }



                foreach ($options as $option) {

                    $option = trim($option);

                    if ($option != '') {

                        $selected = '';

                        if ($field['type'] == 'select') {

                            if ($option == $value) {

                                $selected = ' selected';

                            }

                        } else {

                            foreach ($value as $v) {

                                $v = trim($v);

                                if ($v == $option) {

                                    $selected = ' selected';

                                }

                            }

                        }



                        $fields_html .= '<option value="' . $option . '"' . $selected . '' . set_select($cf_name, $option) . '>' . $option . '</option>';

                    }

                }

                $fields_html .= '</select>';

                $fields_html .= '</div>';

            } elseif ($field['type'] == 'checkbox') {

                $fields_html .= '<div class="form-group chk">';



                $fields_html .= '<br /><label class="control-label' . ($field['display_inline'] == 0 ? ' no-mbot': '') . '" for="' . $cf_name . '[]">' . $field_name . '</label>' . ($field['display_inline'] == 1 ? ' <br />': '');



                $options = explode(',', $field['options']);



                $value = explode(',', $value);



                foreach ($options as $option) {

                    $checked = '';



                    // Replace double quotes with single.

                    $option = str_replace('"', '\'', $option);



                    $option = trim($option);

                    foreach ($value as $v) {

                        $v = trim($v);

                        if ($v == $option) {

                            $checked = 'checked';

                        }

                    }



                    $_chk_attrs                 = [];

                    $chk_attrs                  = '';

                    $_chk_attrs['data-fieldto'] = $field['fieldto'];

                    $_chk_attrs['data-fieldid'] = $field['id'];



                    if ($field['required'] == 1) {

                        $_chk_attrs['data-custom-field-required'] = true;

                    }



                    if ($field['disalow_client_to_edit'] == 1 && is_client_logged_in()) {

                        $_chk_attrs['disabled'] = true;

                    }

                    foreach ($_chk_attrs as $key => $val) {

                        $chk_attrs .= $key . '=' . '"' . $val . '" ';

                    }



                    $input_id = 'cfc_' . $field['id'] . '_' . slug_it($option) . '_' . app_generate_hash();



                    $fields_html .= '<div class="checkbox' . ($field['display_inline'] == 1 ? ' checkbox-inline': '') . '">';

                    $fields_html .= '<input class="custom_field_checkbox" ' . $chk_attrs . ' ' . set_checkbox($cf_name . '[]', $option) . ' ' . $checked . ' value="' . $option . '" id="' . $input_id . '" type="checkbox" name="' . $cf_name . '[]">';



                    $fields_html .= '<label for="' . $input_id . '" class="cf-chk-label">' . $option . '</label>';

                    $fields_html .= '<input type="hidden" name="' . $cf_name . '[]" value="cfk_hidden">';

                    $fields_html .= '</div>';

                }

                $fields_html .= '</div>';

            } elseif ($field['type'] == 'link') {

                if (startsWith($value, 'http')) {

                    $value = '<a href="' . $value . '" target="_blank">' . $value . '</a>';

                }



                $fields_html .= '<div class="form-group cf-hyperlink" data-fieldto="' . $field['fieldto'] . '" data-field-id="' . $field['id'] . '" data-value="' . html_escape($value) . '" data-field-name="' . html_escape($field_name) . '">';

                $fields_html .= '<label class="control-label" for="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">' . $field_name . '</label></br>';



                $fields_html .= '<a id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover" type="button" href="javascript:">' . _l('cf_translate_input_link_tip') . '</a>';



                $fields_html .= '<input type="hidden" ' . ($field['required'] == 1 ? 'data-custom-field-required="1"' : '') . ' value="" id="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']" name="custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']">';



                $field_template = '';

                $field_template .= '<div id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_popover-content" class="hide cfh-field-popover-template"><div class="form-group">';

                $field_template .= '<div class="row"><div class="col-md-12"><label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title">' . _l('cf_translate_input_link_title') . '</label>';

                $field_template .= '<input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_title" value="" class="form-control">';

                $field_template .= '</div>';

                $field_template .= '</div>';

                $field_template .= '</div>';

                $field_template .= '<div class="form-group">';

                $field_template .= '<div class="row">';

                $field_template .= '<div class="col-md-12">';

                $field_template .= '<label class="control-label" for="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link">' . _l('cf_translate_input_link_url') . '</label>';

                $field_template .= '<div class="input-group"><input type="text"' . ($field['disalow_client_to_edit'] == 1 && is_client_logged_in() ? ' disabled="true" ' : ' ') . 'id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_link" value="" class="form-control"><span class="input-group-addon"><a href="#" id="cf_hyperlink_open_' . $field['id'] . '" target="_blank"><i class="fa fa-globe"></i></a></span></div>';

                $field_template .= '</div>';

                $field_template .= '</div>';

                $field_template .= '</div>';

                $field_template .= '<div class="row">';

                $field_template .= '<div class="col-md-6">';

                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-cancel" class="btn btn-default btn-md pull-left" value="">' . _l('cancel') . '</button>';

                $field_template .= '</div>';

                $field_template .= '<div class="col-md-6">';

                $field_template .= '<button type="button" id="custom_fields_' . $field['fieldto'] . '_' . $field['id'] . '_btn-save" class="btn btn-primary btn-md pull-right" value="">' . _l('apply') . '</button>';

                $field_template .= '</div>';

                $field_template .= '</div>';

                $fields_html .= '<script>';

                $fields_html .= 'cfh_popover_templates[\'' . $field['id'] . '\'] = \'' . $field_template . '\';';

                $fields_html .= '</script>';

                $fields_html .= '</div>';

            }



            $name = $cf_name;



            if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {

                $name .= '[]';

            }



            $fields_html .= form_error($name);

            if (!$items_add_edit_preview && !$items_applied) {

                $fields_html .= '</div>';

            } elseif ($items_add_edit_preview) {

                $fields_html .= '</td>';

            } elseif ($items_applied) {

                $fields_html .= '</td>';

            }

        }



        // close row

        if (!$items_add_edit_preview && !$items_applied) {

            $fields_html .= '</div>';

        }

    }



    return $fields_html;

}



function get_task_manage_task_custom_field_value($rel_id, $field_id_or_slug, $field_to, $format = true)

{

    $CI = & get_instance();

    $table_name = db_prefix()."task_manage_custom_fields_values";


    $CI->db->select($table_name . '.value,' . db_prefix() . 'customfields.type');

    $CI->db->join(db_prefix() . 'customfields', db_prefix() . 'customfields.id=' . $table_name . '.fieldid');

    $CI->db->where($table_name . '.relid', $rel_id);

    if (is_numeric($field_id_or_slug)) {

        $CI->db->where($table_name . '.fieldid', $field_id_or_slug);

    } else {

        $CI->db->where(db_prefix() . 'customfields.slug', $field_id_or_slug);

    }




    $row = $CI->db->get($table_name)->row();



    $result = '';

    if ($row) {

        $result = $row->value;

        if ($format == true) {

            if ($row->type == 'date_picker') {

                $result = _d($result);

            } elseif ($row->type == 'date_picker_time') {

                $result = _dt($result);

            }

        }

    }


    return $result;

}


function task_manage_number_cast( $variable = null )
{

    if( empty( $variable ) ) return 0;

    $variable = trim($variable);

    $variable = str_replace(' ', '', $variable);

    $variable = preg_replace("/[^0-9]/", "", $variable);

    return (is_numeric($variable)) ? $variable : 0;

}


function project_diagram_task_status_text( $group_task , $group_style_ = "")
{

    $content = "<div class='project_diagram_tasks' title='$group_task->name' style='$group_style_'>";

    //$content .= $group_task->id." | ".$group_task->name;
    $content .= $group_task->name;

    if( !empty( $group_task->project_status ) )
        $content .= "<br /> <b>". _l('task_status')." : </b> ".$group_task->project_status;

    $content .= "</div>";

    return $content;

}



/**
 * @Version 1.0.5 pipeline
 */
function task_manage_task_info( $group_id )
{

    $CI = &get_instance();

    if ( !empty( $CI->input->get('search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('search')."%' ",null,false);

    if ( !empty( $CI->input->get('sort_by') ) && !empty( $CI->input->get('sort') ) )
        $CI->db->order_by($CI->input->get('sort_by') , $CI->input->get('sort') );


    $tasks = $CI->db->select('t.id, t.name, t.startdate, t.duedate, t.status , p.name as project_name, p.clientid , c.company, p.id as project_id ')
                    ->from(db_prefix().'task_manage_tasks it')
                    ->join(db_prefix().'tasks t','t.task_manage_task_id = it.id')
                    ->join(db_prefix().'projects p','p.id = t.rel_id')
                    ->join(db_prefix().'clients c','c.userid = p.clientid')
                    ->where('it.group_id',$group_id)
                    ->where('t.rel_type','project')
                    ->where('t.status != 5',null,false)
                    ->get()
                    ->result();


    return $tasks;

}


function task_manage_group_task_info( $group_id , $task_order_id )
{

    $CI = &get_instance();

    if ( !empty( $CI->input->get('search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('search')."%' ",null,false);

    if ( !empty( $CI->input->get('sort_by') ) && !empty( $CI->input->get('sort') ) )
        $CI->db->order_by($CI->input->get('sort_by') , $CI->input->get('sort') );


    $tasks = $CI->db->select('t.id, t.name, t.startdate, t.duedate, t.status , p.name as project_name, p.clientid , c.company, p.id as project_id ')
                    ->from(db_prefix().'task_manage_tasks it')
                    ->join(db_prefix().'tasks t','t.task_manage_task_id = it.id')
                    ->join(db_prefix().'projects p','p.id = t.rel_id')
                    ->join(db_prefix().'clients c','c.userid = p.clientid')
                    ->where('it.group_id',$group_id)
                    ->where('it.task_order',$task_order_id)
                    ->where('t.rel_type','project')
                    ->where('t.status != 5',null,false)
                    ->get()
                    ->result();


    return $tasks;

}


/**
 *
 * @Version 1.1.2
 *
 * Project status kanban view
 *
 */
function task_manage_project_info( $status_id )
{

    $CI = &get_instance();

    if ( !empty( $CI->input->get('search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('search')."%' ",null,false);

    if ( !empty( $CI->input->get('?search') ) )
        $CI->db->where("c.company like '%".$CI->input->get('?search')."%' ",null,false);


    $sort = '';

    if ( !empty( $CI->input->get('sort_by') ) )
        $sort = $CI->input->get('sort_by');

    if ( !empty( $CI->input->get('?sort_by') ) )
        $sort = $CI->input->get('?sort_by');


    if ( !empty( $sort ) && !empty( $CI->input->get('sort') ) )
        $CI->db->order_by( $sort , $CI->input->get('sort') );



    if ( !empty( $CI->input->get('filter_group') ) )
    {

        $where = " ( task_manage_groups like '%\"".$CI->input->get('filter_group')."\"%' ) ";

        $CI->db->where($where,null,false);

    }


    if ( !empty( $CI->input->get('filter_staff') ) )
    {

        $where = " p.id IN ( SELECT project_id FROM ".db_prefix()."project_members WHERE staff_id = ( ".$CI->input->get('filter_staff')." ) ) ";

        $CI->db->where($where,null,false);

    }


    if ( !empty( $CI->input->get('from_date') ) )
    {

        $from_date = to_sql_date( $CI->input->get('from_date') );

        $where = " DATE(p.start_date) >= '$from_date' ";

        $CI->db->where($where,null,false);

    }

    if ( !empty( $CI->input->get('to_date') ) )
    {

        $to_date = to_sql_date( $CI->input->get('to_date') );

        $where = " DATE(p.start_date) <= '$to_date' ";

        $CI->db->where($where,null,false);

    }




    if ( !has_permission('projects', '', 'view')  )
    {
        $CI->db->where('p.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')',null,false);
    }

    $projects = $CI->db->select('p.start_date, p.deadline, p.status , p.name as project_name, p.clientid , c.company, p.id as project_id ')
                        ->from(db_prefix().'projects p')
                        ->join(db_prefix().'clients c','c.userid = p.clientid')
                        ->where('p.status',$status_id)
                        ->where('p.status != 4')
                        ->where('task_manage_groups is not null',null,false)
                        ->get()
                        ->result();

    return $projects;

}
