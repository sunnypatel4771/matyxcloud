<?php

defined('BASEPATH') or exit('No direct script access allowed');

return App_table::find('related_tasks')
    ->outputUsing(function ($params) {
        extract($params);

        $hasPermissionEdit   = staff_can('edit', 'tasks');
        $hasPermissionDelete = staff_can('delete', 'tasks');
        $tasksPriorities     = get_tasks_priorities();
        $task_statuses       = $this->ci->tasks_model->get_statuses();

        $aColumns = [
            '1', // bulk actions
            db_prefix() . 'tasks.id as id',
            db_prefix() . 'tasks.name as task_name',
            'status',
            'startdate',
            'duedate',
            get_sql_select_task_asignees_full_names() . ' as assignees',
            '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'tasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
            'priority',
        ];

        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'tasks';

        $additionalColumns = [
            'rel_type',
            'rel_id',
            'recurring',
            my_tasks_rel_name_select_query() . ' as rel_name',
            'billed',
            '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as is_assigned',
            get_sql_select_task_assignees_ids() . ' as assignees_ids',
            '(SELECT MAX(id) FROM ' . db_prefix() . 'taskstimers WHERE task_id=' . db_prefix() . 'tasks.id and staff_id=' . get_staff_user_id() . ' and end_time IS NULL) as not_finished_timer_by_current_staff',
            '(SELECT staffid FROM ' . db_prefix() . 'task_assigned WHERE taskid=' . db_prefix() . 'tasks.id AND staffid=' . get_staff_user_id() . ') as current_user_is_assigned',
            '(SELECT CASE WHEN addedfrom=' . get_staff_user_id() . ' AND is_added_from_contact=0 THEN 1 ELSE 0 END) as current_user_is_creator',
            
        ];
        $where = [];

        if ($filtersWhere = $this->getWhereFromRules()) {
            $where[] = $filtersWhere;
        }
        if($this->ci->input->post('milstone_name')) {
            $where[] = 'AND ' . db_prefix() . 'tasks.milestone=' . $this->ci->input->post('milstone_name');
        }
        if (staff_cant('view', 'tasks')) {
            $where[] = get_tasks_where_string();
        }

        if (! $this->ci->input->post('tasks_related_to')) {
            array_push($where, 'AND rel_id="' . $this->ci->db->escape_str($rel_id) . '" AND rel_type="' . $this->ci->db->escape_str($rel_type) . '"');
        } else {
            // Used in the customer profile filters
            $tasks_related_to = explode(',', $this->ci->input->post('tasks_related_to'));
            $rel_to_query     = 'AND (';

            $lastElement = end($tasks_related_to);

            foreach ($tasks_related_to as $rel_to) {
                if ($rel_to == 'invoice') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE clientid=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'estimate') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'estimates WHERE clientid=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'contract') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'contracts WHERE client=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'ticket') {
                    $rel_to_query .= '(rel_id IN (SELECT ticketid FROM ' . db_prefix() . 'tickets WHERE userid=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'expense') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'expenses WHERE clientid=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'proposal') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'proposals WHERE rel_type=' . $this->ci->db->escape_str($rel_id) . ' AND rel_type="customer")';
                } elseif ($rel_to == 'customer') {
                    $rel_to_query .= '(rel_id IN (SELECT userid FROM ' . db_prefix() . 'clients WHERE userid=' . $this->ci->db->escape_str($rel_id) . ')';
                } elseif ($rel_to == 'project') {
                    $rel_to_query .= '(rel_id IN (SELECT id FROM ' . db_prefix() . 'projects WHERE clientid=' . $this->ci->db->escape_str($rel_id) . ')';
                }

                $rel_to_query .= ' AND rel_type="' . $this->ci->db->escape_str($rel_to) . '")';
                if ($rel_to != $lastElement) {
                    $rel_to_query .= ' OR ';
                }
            }

            $rel_to_query .= ')';

            array_push($where, $rel_to_query);
        }

        $join = [];

        $custom_fields = get_table_custom_fields('tasks');

        // foreach ($custom_fields as $key => $field) {
        //     $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
        //     array_push($customFieldsColumns, $selectAs);
        //     array_push($aColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs);
        // }
        foreach ($custom_fields as $key => $field) {
            $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
            // array_push($customFieldsColumns, $selectAs);
            $customFieldsColumns[$key] = [
                'slug' => $field['slug'],
                'name' => $selectAs
            ];
            if($field['slug'] == 'tasks_eta'){
                array_push($aColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $field['slug']);
            }else{
                array_push($aColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs);
            }

            // array_push($aColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $selectAs);
             array_push($additionalColumns, '(SELECT value FROM ' . db_prefix() . 'customfieldsvalues WHERE ' . db_prefix() . 'customfieldsvalues.relid=' . db_prefix() . 'tasks.id AND ' . db_prefix() . 'customfieldsvalues.fieldid=' . $field['id'] . ' AND ' . db_prefix() . 'customfieldsvalues.fieldto="' . $field['fieldto'] . '" LIMIT 1) as ' . $field['slug']);
        }

        $aColumns = hooks()->apply_filters('tasks_related_table_sql_columns', $aColumns);

        // Fix for big queries. Some hosting have max_join_limit
        if (count($custom_fields) > 4) {
            @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalColumns);

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $aRow) {
            $row = [];

            $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

            $row[] = '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" onclick="init_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['id'] . '</a>';

            $outputName = '';

            if ($aRow['not_finished_timer_by_current_staff']) {
                $outputName .= '<span class="pull-left text-danger"><i class="fa-regular fa-clock fa-fw tw-mr-1"></i></span>';
            }

            $outputName .= '<a href="' . admin_url('tasks/view/' . $aRow['id']) . '" class="main-tasks-table-href-name tw-font-medium" onclick="init_task_modal(' . $aRow['id'] . '); return false;" title="' . e($aRow['task_name']) . '">' . e($aRow['task_name']) . '</a>';

            if ($aRow['recurring'] == 1) {
                $outputName .= '<span class="label label-primary inline-block mtop4"> ' . _l('recurring_task') . '</span>';
            }

            $outputName .= '<div class="row-options">';

            $class = 'text-success bold';
            $style = '';

            $tooltip = '';
            if ($aRow['billed'] == 1 || ! $aRow['is_assigned'] || $aRow['status'] == Tasks_model::STATUS_COMPLETE) {
                $class = 'text-dark disabled';
                $style = 'style="opacity:0.6;cursor: not-allowed;"';
                if ($aRow['status'] == Tasks_model::STATUS_COMPLETE) {
                    $tooltip = ' data-toggle="tooltip" data-title="' . e(format_task_status($aRow['status'], false, true)) . '"';
                } elseif ($aRow['billed'] == 1) {
                    $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
                } elseif (! $aRow['is_assigned']) {
                    $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
                }
            }

            if ($aRow['not_finished_timer_by_current_staff']) {
                $outputName .= '<a href="#" class="text-danger tasks-table-stop-timer" onclick="timer_action(this,' . $aRow['id'] . ',' . $aRow['not_finished_timer_by_current_staff'] . '); return false;">' . _l('task_stop_timer') . '</a>';
            } else {
                $outputName .= '<span' . $tooltip . ' ' . $style . '>
        <a href="#" class="' . $class . ' tasks-table-start-timer" onclick="timer_action(this,' . $aRow['id'] . '); return false;">' . _l('task_start_timer') . '</a>
        </span>';
            }

            if ($hasPermissionEdit) {
                $outputName .= '<span class="tw-text-neutral-300"> | </span><a href="#" onclick="edit_task(' . $aRow['id'] . '); return false">' . _l('edit') . '</a>';
            }

            if ($hasPermissionDelete) {
                $outputName .= '<span class="tw-text-neutral-300"> | </span><a href="' . admin_url('tasks/delete_task/' . $aRow['id']) . '" class="text-danger _delete task-delete">' . _l('delete') . '</a>';
            }
            $outputName .= '</div>';

            $row[]           = $outputName;
            $canChangeStatus = ($aRow['current_user_is_creator'] != '0' || $aRow['current_user_is_assigned'] || staff_can('edit', 'tasks'));
            $status          = get_task_status_by_id($aRow['status']);
            $outputStatus    = '';

            if ($canChangeStatus) {
                $outputStatus .= '<div class="dropdown inline-block table-export-exclude">';
                $outputStatus .= '<a href="#" class="dropdown-toggle label tw-flex tw-items-center tw-gap-1 tw-flex-nowrap hover:tw-opacity-80 tw-align-middle" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';" task-status-table="' . e($aRow['status']) . '" id="tableTaskStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $outputStatus .= e($status['name']);
                $outputStatus .= '<i class="chevron tw-shrink-0"></i>';
                $outputStatus .= '</a>';

                $outputStatus .= '<ul class="dropdown-menu" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';

                foreach ($task_statuses as $taskChangeStatus) {
                    if ($aRow['status'] != $taskChangeStatus['id']) {
                        $outputStatus .= '<li>
                  <a href="#" onclick="task_mark_as(' . $taskChangeStatus['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . e(_l('task_mark_as', $taskChangeStatus['name'])) . '
                  </a>
               </li>';
                    }
                }
                $outputStatus .= '</ul>';
                $outputStatus .= '</div>';
            } else {
                $outputStatus .= '<span class="label" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';" task-status-table="' . e($aRow['status']) . '">' . e($status['name']) . '</span>';
            }

            $row[] = $outputStatus;
            $row[] = e(_d($aRow['startdate']));
            
            foreach ($customFieldsColumns as $customFieldColumn) {
                if ($customFieldColumn['slug'] == 'tasks_eta') {
                    // $row[] = (strpos($customFieldColumn['name'], 'date_picker_') !== false ? _d($aRow[$customFieldColumn['name']]) : $aRow[$customFieldColumn['name']]);  
                                        // $row[] = _d($aRow['tasks_eta']);    
                                         if (staff_can('edit', 'tasks') && $aRow['status'] != Tasks_model::STATUS_COMPLETE){
                        $row[] = '<input name="startdate" tabindex="-1"
                            value="'. _d($aRow['tasks_eta']) .'"
                            id="task-single-work_planned"
                            class="form-control task-info-inline-input-edit datepicker pointer tw-text-neutral-800" data-task_id="'.$aRow['id'].'"
                        data-field_id="" style="width: 100%;">';
                     }else{
                        $row[] = _d($aRow['tasks_eta']);  
                     }

                }
                
            }

            $row[] = e(_d($aRow['duedate']));

            $row[] = format_members_by_ids_and_names($aRow['assignees_ids'], $aRow['assignees']);

            $row[] = render_tags($aRow['tags']);

            if (staff_can('edit', 'tasks') && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
                $outputPriority = '<div class="dropdown inline-block table-export-exclude">';
                $outputPriority .= '<a href="#" style="color:' . e(task_priority_color($aRow['priority'])) . '" class="dropdown-toggle tw-flex tw-items-center tw-gap-1 tw-flex-nowrap hover:tw-opacity-80 tw-align-middle" id="tableTaskPriority-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $outputPriority .= e(task_priority($aRow['priority']));
                $outputPriority .= '<i class="chevron tw-shrink-0"></i>';
                $outputPriority .= '</a>';

                $outputPriority .= '<ul class="dropdown-menu" aria-labelledby="tableTaskPriority-' . $aRow['id'] . '">';

                foreach ($tasksPriorities as $priority) {
                    if ($aRow['priority'] != $priority['id']) {
                        $outputPriority .= '<li>
                  <a href="#" onclick="task_change_priority(' . $priority['id'] . ',' . $aRow['id'] . '); return false;">
                     ' . e($priority['name']) . '
                  </a>
               </li>';
                    }
                }
                $outputPriority .= '</ul>';
                $outputPriority .= '</div>';
            } else {
                $outputPriority = '<span style="color:' . e(task_priority_color($aRow['priority'])) . ';" class="inline-block">' . e(task_priority($aRow['priority'])) . '</span>';
            }

            $row[] = $outputPriority;

            // Custom fields add values
            // foreach ($customFieldsColumns as $customFieldColumn) {
            //     $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
            // }
            foreach ($customFieldsColumns as $customFieldColumn) {
                if ($customFieldColumn['slug'] != 'tasks_eta') {
                    $row[] = (strpos($customFieldColumn['name'], 'date_picker_') !== false ? _d($aRow[$customFieldColumn['name']]) : $aRow[$customFieldColumn['name']]);    
                }
            }

            // $row['DT_RowClass'] = 'has-row-options has-border-left';

            // if ((! empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
            //     $row['DT_RowClass'] .= ' danger';
            // }
            
            $row['DT_RowClass'] = 'has-row-options has-border-left';
              if ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
                $row['DT_RowClass'] .= ' orange';   
            }
            if ((!empty($aRow['tasks_eta']) && $aRow['tasks_eta'] == date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
                $row['DT_RowClass'] .= ' warning';   
            }
            if ((! empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != Tasks_model::STATUS_COMPLETE) {
                $row['DT_RowClass'] .= ' danger';
            }

            $row = hooks()->apply_filters('tasks_related_table_row_data', $row, $aRow);

            $output['aaData'][] = $row;
        }

        return $output;
    });
