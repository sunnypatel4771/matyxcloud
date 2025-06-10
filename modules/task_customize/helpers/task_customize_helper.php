<?php 


function get_milestone_data($milestone_id){
    if (is_numeric($milestone_id)) {
        $CI = &get_instance();
        $CI->db->where('id' , $milestone_id);
        $data = $CI->db->get(db_prefix() . 'milestones')->result_array();
        if (!empty($data)) {
            $name = isset($data[0]['name']) ? $data[0]['name'] : '';
            return $name;
        }
    }
    return '';
}

function get_milestone_filter_data($project_id){
    $CI = &get_instance();
    $CI->db->select('id, name');
        $CI->db->where('project_id' , $project_id);
    $data = $CI->db->get(db_prefix() . 'milestones')->result_array();
    return $data;
}


function init_relation_tasks_table_change($table_attributes = [], $filtersWrapperId = 'vueApp', $filtersDetached = false)
{
    $table_data = [
        _l('the_number_sign'),
        [
            'name'     => _l('tasks_dt_name'),
            'th_attrs' => [
                'style' => 'width:200px',
            ],
        ],
        _l('task_status'),
        [
            'name'     => _l('tasks_dt_datestart'),
            'th_attrs' => [
                'style' => 'width:75px',
            ],
        ],
        [
            'name'     => _l('task_duedate'),
            'th_attrs' => [
                'style' => 'width:75px',
                'class' => 'duedate',
            ],
        ],
        [
            'name'     => _l('task_assigned'),
            'th_attrs' => [
                'style' => 'width:75px',
            ],
        ],
        _l('tags'),
        _l('tasks_list_priority'),
    ];

    array_unshift($table_data, [
        'name'     => '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="rel-tasks"><label></label></div>',
        'th_attrs' => ['class' => ($table_attributes['data-new-rel-type'] !== 'project' ? 'not_visible' : '')],
    ]);

    $custom_fields = get_custom_fields('tasks', [
        'show_on_table' => 1,
    ]);

    foreach ($custom_fields as $field) {
        array_push($table_data, [
           'name'     => $field['name'],
           'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
       ]);
    }

    $table_data = hooks()->apply_filters('tasks_related_table_columns', $table_data);

    $name = 'rel-tasks';
    if ($table_attributes['data-new-rel-type'] == 'lead') {
        $name = 'rel-tasks-leads';
    }
    
    $tasks_table = App_table::find('related_tasks');

    $table      = '';
    $CI         = &get_instance();
    $table_name = '.table-' . $name;

    $CI->load->view('admin/tasks/filters', [
        'tasks_table'=>$tasks_table,
        'filters_wrapper_id'=>$filtersWrapperId,
        'detached'=>$filtersDetached,
    ]);

    if (staff_can('create',  'tasks')) {
        $disabled   = '';
        $table_name = addslashes($table_name);
        if ($table_attributes['data-new-rel-type'] == 'customer' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows(db_prefix() . 'clients', [
                'active' => 0,
                'userid' => $table_attributes['data-new-rel-id'],
            ]) > 0) {
                $disabled = ' disabled';
            }
        }
        // projects have button on top
        if ($table_attributes['data-new-rel-type'] != 'project') {
            echo "<a href='#' class='btn btn-primary pull-left mright5 new-task-relation" . $disabled . "' onclick=\"new_task_from_relation('$table_name'); return false;\" data-rel-id='" . $table_attributes['data-new-rel-id'] . "' data-rel-type='" . $table_attributes['data-new-rel-type'] . "'><i class=\"fa-regular fa-plus tw-mr-1\"></i>" . _l('new_task') . '</a>';
        }
    }

    if ($table_attributes['data-new-rel-type'] == 'project') {
        echo "<div class='tw-mb-4 tw-space-x-1 rtl:tw-space-x-reverse'>";
        echo "<a href='" . admin_url('tasks/detailed_overview?project_id=' . $table_attributes['data-new-rel-id']) . "' class='btn btn-primary'>" . _l('detailed_overview') . '</a>';
        echo "<a href='" . admin_url('tasks/list_tasks?project_id=' . $table_attributes['data-new-rel-id'] . '&kanban=true') . "' class='btn btn-default hidden-xs !tw-px-3' data-toggle='tooltip' data-title='" . _l('view_kanban') . "' data-placement='top'><i class='fa-solid fa-grip-vertical'></i></a>";
        $milestone_dropdown_data = get_milestone_filter_data($table_attributes['data-new-rel-id']);
        // $milestone_dropdown = render_select('milestone' , $milestone_dropdown_data , ['id' , 'name'] , '');
        $dropdown_html = '';
        $dropdown_html .= "<option value=''>ALL</option>";
        foreach ($milestone_dropdown_data as $key => $value) {
            $dropdown_html .= "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
        }
        
        $milestone_dropdown = "<select id='milstone_name' name='milstone_name' class='selectpicker' onchange=\"dt_custom_view($(this).val(), '.table-rel-tasks','milstone_name'); return false;\">". $dropdown_html ."</select>";

        echo '<div style="width: 15%; display: inline-block; float: right;" class="_filters hidden_inputs">'.$milestone_dropdown . '</div>';
        echo '<div class="_hidden_inputs _filters _tasks_filters">';
        echo form_hidden('milstone_name');
        echo '</div>';
        echo '</div>';
        echo '<div class="clearfix"></div>';
        echo $CI->load->view('admin/tasks/_bulk_actions', ['table' => '.table-rel-tasks'], true);
        echo '<div class="tw-mb-4">';
        echo $CI->load->view('admin/tasks/_summary', ['rel_id' => $table_attributes['data-new-rel-id'], 'rel_type' => 'project', 'table' => $table_name], true);
        echo '</div>';
        echo '<a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-rel-tasks">' . _l('bulk_actions') . '</a>';
    } elseif ($table_attributes['data-new-rel-type'] == 'customer') {
        echo '<div class="clearfix"></div>';
        echo '<div id="tasks_related_filter" class="mtop15">';
        echo '<p class="bold">' . _l('task_related_to') . ': </p>';

        echo '<div class="checkbox checkbox-inline">
        <input type="checkbox" checked value="customer" disabled id="ts_rel_to_customer" name="tasks_related_to[]">
        <label for="ts_rel_to_customer">' . _l('client') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="project" id="ts_rel_to_project" name="tasks_related_to[]">
        <label for="ts_rel_to_project">' . _l('projects') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="invoice" id="ts_rel_to_invoice" name="tasks_related_to[]">
        <label for="ts_rel_to_invoice">' . _l('invoices') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="estimate" id="ts_rel_to_estimate" name="tasks_related_to[]">
        <label for="ts_rel_to_estimate">' . _l('estimates') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="contract" id="ts_rel_to_contract" name="tasks_related_to[]">
        <label for="ts_rel_to_contract">' . _l('contracts') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="ticket" id="ts_rel_to_ticket" name="tasks_related_to[]">
        <label for="ts_rel_to_ticket">' . _l('tickets') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="expense" id="ts_rel_to_expense" name="tasks_related_to[]">
        <label for="ts_rel_to_expense">' . _l('expenses') . '</label>
        </div>

        <div class="checkbox checkbox-inline">
        <input type="checkbox" value="proposal" id="ts_rel_to_proposal" name="tasks_related_to[]">
        <label for="ts_rel_to_proposal">' . _l('proposals') . '</label>
        </div>';
        echo form_hidden('tasks_related_to');
        echo '</div>';
    }
    echo "<div class='clearfix'></div>";

    // If new column is added on tasks relations table this will not work fine
    // In this case we need to add new identifier eq task-relation
    $table_attributes['data-last-order-identifier'] = 'tasks';
    $table_attributes['data-default-order']         = get_table_last_order('tasks');
    if ($table_attributes['data-new-rel-type'] != 'project') {
        echo '<hr />';
    }
    $table_attributes['id'] = 'related_tasks';
    $table .= render_datatable($table_data, $name, ['number-index-1'], $table_attributes);

    return $table;
}

function get_task_detail($task_id){
    if (is_numeric($task_id)) {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->where('id' , $task_id);
        $data = $CI->db->get(db_prefix() . 'tasks')->result_array();
        return $data;        
    }
    return [];
}

function task_custom_field($task_id){
    if (is_numeric($task_id) && $task_id != '') {
        $CI = &get_instance();
        $CI->db->select(db_prefix().'customfields.id, '. db_prefix().'customfieldsvalues.value, '. db_prefix().'customfields.name'); 
        $CI->db->join(db_prefix().'customfieldsvalues', db_prefix().'customfieldsvalues.fieldid = '.db_prefix().'customfields.id');
        $CI->db->where(db_prefix().'customfields.name', 'Work Planned');
        $CI->db->where(db_prefix().'customfieldsvalues.fieldto', 'tasks');
        $CI->db->where(db_prefix().'customfieldsvalues.relid ', $task_id);
        $task_custom_data = $CI->db->get(db_prefix().'customfields')->result_array();
        if (!empty($task_custom_data) && isset($task_custom_data[0]) && !empty($task_custom_data[0])) {
            $task_custom_data = $task_custom_data[0];
            return $task_custom_data;
        }
    }
    return '';
}

// function get_custom_field_for_task(){
//     $CI = &get_instance();
//     $CI->db->select(db_prefix().'customfields.*, '. db_prefix() .'customfieldsvalues.*');
//     $CI->db->from(db_prefix().'customfields');
//     $CI->db->join(db_prefix().'customfieldsvalues', db_prefix().'customfieldsvalues.fieldid = '.db_prefix().'customfields.id');
//     $CI->db->where(db_prefix().'customfields.fieldto', 'tasks');
//     $CI->db->where(db_prefix().'customfields.slug', 'tasks_work_planned');
//     $result = $CI->db->get()->result_array();
//     echo '<pre>';
//     print_r($result);
//     die;
// }

function formatDate($date) {
    // Check if the date is already in Y-m-d format
    $d = DateTime::createFromFormat('Y-m-d', $date);
    
    if ($d && $d->format('Y-m-d') === $date) {
        return $date; // Already in correct format
    }

    // Try different common formats
    $formats = ['d/m/Y', 'm-d-Y', 'd.m.Y', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'M d, Y', 'd M Y'];

    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $date);
        if ($d) {
            return $d->format('Y-m-d'); // Convert to Y-m-d
        }
    }

    return $$date; // Invalid date
}

function update_custom_field_value($task_id , $value , $field_id){

    if ($field_id == '') {
        $field_id = WORK_PLANNED;
    }

    $CI =& get_instance();
    $CI->db->where('fieldto', 'tasks');
    $CI->db->where('relid', $task_id);
    $CI->db->where('fieldid', $field_id);
    $query = $CI->db->get(db_prefix().'customfieldsvalues');
    $value = formatDate($value);
    if ($query->num_rows() > 0) {
        $CI->db->where('fieldto', 'tasks');
        $CI->db->where('relid', $task_id);
        $CI->db->where('fieldid', $field_id);
        $CI->db->update(db_prefix().'customfieldsvalues', ['value' => $value]);
    } else {
        $CI->db->insert(db_prefix().'customfieldsvalues', [
            'fieldto' => 'tasks',
            'relid' => $task_id,
            'fieldid' => $field_id,
            'value' => $value
        ]);
    }
}


function my_tasks_rel_name_select_query()
{
    return '(CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM ' . db_prefix() . 'contracts WHERE ' . db_prefix() . 'contracts.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM ' . db_prefix() . 'estimates WHERE ' . db_prefix() . 'estimates.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM ' . db_prefix() . 'proposals WHERE ' . db_prefix() . 'proposals.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'tasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",' . db_prefix() . 'tickets.ticketid), " - ", ' . db_prefix() . 'tickets.subject) FROM ' . db_prefix() . 'tickets WHERE ' . db_prefix() . 'tickets.ticketid=' . db_prefix() . 'tasks.rel_id)
        WHEN "lead" THEN (SELECT CASE ' . db_prefix() . 'leads.email WHEN "" THEN ' . db_prefix() . 'leads.name ELSE CONCAT(' . db_prefix() . 'leads.name, " - ", ' . db_prefix() . 'leads.email) END FROM ' . db_prefix() . 'leads WHERE ' . db_prefix() . 'leads.id=' . db_prefix() . 'tasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'contacts WHERE userid = ' . db_prefix() . 'clients.userid and is_primary = 1) ELSE company END FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.userid=' . db_prefix() . 'tasks.rel_id)
         WHEN "project" THEN (
            SELECT 
                CONCAT(
                    (SELECT CASE company WHEN "" 
                        THEN (SELECT CONCAT(firstname, " ", lastname) FROM ' . db_prefix() . 'contacts WHERE userid = ' . db_prefix() . 'clients.userid AND is_primary = 1) 
                        ELSE company END 
                    FROM ' . db_prefix() . 'clients WHERE userid = ' . db_prefix() . 'projects.clientid),
                    " - ",
                    CONCAT("#", ' . db_prefix() . 'projects.id, " - ", ' . db_prefix() . 'projects.name)
                )
            FROM ' . db_prefix() . 'projects WHERE ' . db_prefix() . 'projects.id = ' . db_prefix() . 'tasks.rel_id
        )
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN ' . db_prefix() . 'expenses_categories.name ELSE
         CONCAT(' . db_prefix() . 'expenses_categories.name, \' (\',' . db_prefix() . 'expenses.expense_name,\')\') END FROM ' . db_prefix() . 'expenses JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category WHERE ' . db_prefix() . 'expenses.id=' . db_prefix() . 'tasks.rel_id)
        ELSE NULL
        END)';

}


function my_get_relation_values($relation, $type)
{
    if ($relation == '') {
        return [
            'name'      => '',
            'id'        => '',
            'link'      => '',
            'addedfrom' => 0,
            'subtext'   => '',
            ];
    }

    $addedfrom = 0;
    $name      = '';
    $id        = '';
    $link      = '';
    $subtext   = '';

    if ($type == 'customer' || $type == 'customers') {
        if (is_array($relation)) {
            $id   = $relation['userid'];
            $name = $relation['company'];
        } else {
            $id   = $relation->userid;
            $name = $relation->company;
        }
        $link = admin_url('clients/client/' . $id);
    } elseif ($type == 'contact' || $type == 'contacts') {
        if (is_array($relation)) {
            $userid = isset($relation['userid']) ? $relation['userid'] : $relation['relid'];
            $id     = $relation['id'];
            $name   = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $userid = $relation->userid;
            $id     = $relation->id;
            $name   = $relation->firstname . ' ' . $relation->lastname;
        }
        $subtext = get_company_name($userid);
        $link    = admin_url('clients/client/' . $userid . '?contactid=' . $id);
    } elseif ($type == 'invoice') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_invoice_number($id);
        $link = admin_url('invoices/list_invoices/' . $id);
    } elseif ($type == 'credit_note') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_credit_note_number($id);
        $link = admin_url('credit_notes/list_credit_notes/' . $id);
    } elseif ($type == 'estimate') {
        if (is_array($relation)) {
            $id        = $relation['estimateid'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
        }
        $name = format_estimate_number($id);
        $link = admin_url('estimates/list_estimates/' . $id);
    } elseif ($type == 'contract' || $type == 'contracts') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $name      = $relation['subject'];
            $addedfrom = $relation['addedfrom'];
        } else {
            $id        = $relation->id;
            $name      = $relation->subject;
            $addedfrom = $relation->addedfrom;
        }
        $link = admin_url('contracts/contract/' . $id);
    } elseif ($type == 'ticket') {
        if (is_array($relation)) {
            $id   = $relation['ticketid'];
            $name = '#' . $relation['ticketid'];
            $name .= ' - ' . $relation['subject'];
        } else {
            $id   = $relation->ticketid;
            $name = '#' . $relation->ticketid;
            $name .= ' - ' . $relation->subject;
        }
        $link = admin_url('tickets/ticket/' . $id);
    } elseif ($type == 'expense' || $type == 'expenses') {
        if (is_array($relation)) {
            $id        = $relation['expenseid'];
            $name      = $relation['category_name'];
            $addedfrom = $relation['addedfrom'];

            if (!empty($relation['expense_name'])) {
                $name .= ' (' . $relation['expense_name'] . ')';
            }
        } else {
            $id        = $relation->expenseid;
            $name      = $relation->category_name;
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->expense_name)) {
                $name .= ' (' . $relation->expense_name . ')';
            }
        }
        $link = admin_url('expenses/list_expenses/' . $id);
    } elseif ($type == 'lead' || $type == 'leads') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
            if ($relation['email'] != '') {
                $name .= ' - ' . $relation['email'];
            }
        } else {
            $id   = $relation->id;
            $name = $relation->name;
            if ($relation->email != '') {
                $name .= ' - ' . $relation->email;
            }
        }
        $link = admin_url('leads/index/' . $id);
    } elseif ($type == 'proposal') {
        if (is_array($relation)) {
            $id        = $relation['id'];
            $addedfrom = $relation['addedfrom'];
            if (!empty($relation['subject'])) {
                $name .= ' - ' . $relation['subject'];
            }
        } else {
            $id        = $relation->id;
            $addedfrom = $relation->addedfrom;
            if (!empty($relation->subject)) {
                $name .= ' - ' . $relation->subject;
            }
        }
        $name = format_proposal_number($id);
        $link = admin_url('proposals/list_proposals/' . $id);
    } elseif ($type == 'tasks' || $type == 'task') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('tasks/view/' . $id);
    } elseif ($type == 'staff') {
        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);
    } elseif ($type == 'project') {
        if (is_array($relation)) {
            $id       = $relation['id'];
            $name     = $relation['name'];
            $clientId = $relation['clientid'];
        } else {
            $id       = $relation->id;
            $name     = $relation->name;
            $clientId = $relation->clientid;
        }

        $name = get_company_name($clientId) . ' - #' . $id . ' - ' . $name;


        $link = admin_url('projects/view/' . $id);
    }

    return hooks()->apply_filters('relation_values', [
        'id'        => $id,
        'name'      => $name,
        'link'      => $link,
        'addedfrom' => $addedfrom,
        'subtext'   => $subtext,
        'type'      => $type,
        'relation'  => $relation,
    ]);
}

//get_comments_count
function get_comments_count($task_id){
    if (is_numeric($task_id)) {
        $CI = &get_instance();
        $CI->db->select('count(*) as total_comments');
        $CI->db->where('taskid' , $task_id);
        $CI->db->from(db_prefix() . 'task_comments');
        $data = $CI->db->get()->result_array();
        if (!empty($data) && isset($data[0]) && !empty($data[0])) {
            $total_comments = $data[0]['total_comments'];
            return $total_comments;
        }
    }
    return 0;
}



/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $sGroupBy group results
 * @return array
 */
function task_data_tables_init($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
{
    $CI          = &get_instance();
    $data      = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = '';
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = 'LIMIT ' . intval($CI->input->post('start')) . ', ' . intval($CI->input->post('length'));
    }

    $allColumns = [];

    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (startsWith($_column[0], db_prefix())) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($allColumns, $_prefix);
                } else {
                    array_push($allColumns, $column);
                }
            } else {
                array_push($allColumns, $_column[0]);
            }
        } else {
            array_push($allColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();

    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY is_poked = 1 DESC, ';
        foreach ($CI->input->post('order') as $key => $val) {
            $columnName = $aColumns[intval($data['order'][$key]['column'])];
            $dir        = strtoupper($data['order'][$key]['dir']);
            $type       = $data['order'][$key]['type'] ?? null;

            // Security
            if (!in_array($dir, ['ASC', 'DESC'])) {
                $dir = 'ASC';
            }

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            // first checking is for eq tablename.column name
            // second checking there is already prefixed table name in the column name
            // this will work on the first table sorting - checked by the draw parameters
            // in future sorting user must sort like he want and the duedates won't be always last
            if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
                || in_array($columnName, $nullColumnsAsLast))) {
                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
            } else {
                // Custom fields sorting support for number type custom fields
                if ($type === 'number') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as SIGNED)', $sTable);
                } elseif ($type === 'date_picker') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATE)', $sTable);
                } elseif ($type === 'date_picker_time') {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', 'CAST(' . $columnName . ' as DATETIME)', $sTable);
                } else {
                    $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);
                }
            }

            $sOrder .= ' ' . $dir . ', ';
        }

        if (trim($sOrder) == 'ORDER BY') {
            $sOrder = '';
        }

        $sOrder = rtrim($sOrder, ', ');

        if (
            get_option('save_last_order_for_tables') == '1'
            && $CI->input->post('last_order_identifier')
            && $CI->input->post('order')
        ) {
            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object

            $indexedOnly = [];
            foreach ($CI->input->post('order') as $row) {
                $indexedOnly[] = array_values($row);
            }

            $meta_name = $CI->input->post('last_order_identifier') . '-table-last-order';

            update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));
        }
    }
   
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = '';
    if ((isset($data['search'])) && $data['search']['value'] != '') {
        $search_value = $data['search']['value'];
        $search_value = trim($search_value);

        $sWhere             = 'WHERE (';
        $sMatchCustomFields = [];

        // Not working, do not use it
        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

        for ($i = 0; $i < count($aColumns); $i++) {
            $columnName = $aColumns[$i];
            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                if (($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                    if (isset($searchAs[$i])) {
                        $columnName = $searchAs[$i];
                    }

                    // Custom fields values are FULLTEXT and should be searched with MATCH
                    // Not working ATM
                    if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith($columnName, 'ctable_')) {
                        $sMatchCustomFields[] = $columnName;
                    } else {
                        $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%' ESCAPE '!' OR ";
                    }
                }
            }
        }

        if (count($sMatchCustomFields) > 0) {
            $s = $CI->db->escape_str($search_value);
            foreach ($sMatchCustomFields as $matchCustomField) {
                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";
            }
        }

        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                if (stripos($columnName, 'AVG(') === false && stripos($columnName, 'SUM(') === false) {
                    // Use index
                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . $CI->db->escape_like_str($search_value) . "%'ESCAPE '!' OR ";
                }
            }
        }

        $sWhere = substr_replace($sWhere, '', -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere      = 'WHERE (';

        foreach ($aColumns as $i => $column) {
            if (isset($data['columns'][$i]) && $data['columns'][$i]['searchable'] == 'true') {
                $search_value = $data['columns'][$i]['search']['value'];
                $columnName = $column;

                if (strpos($columnName, ' as ') !== false) {
                    $columnName = strbefore($columnName, ' as');
                }

                if ($search_value != '') {
                    // Add condition for current column
                    $likeClause = $CI->db->escape_like_str($search_value);
                    $sWhere .= "convert($columnName USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";

                    // Process additional select fields if any
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= "convert($searchAdditionalField USING utf8) LIKE '%$likeClause%' ESCAPE '!' OR ";
                        }
                    }

                    $searchFound++;
                }
            }
        }

        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $additionalColumns = '';
    if (count($additionalSelect) > 0) {
        $additionalColumns = ',' . implode(',', $additionalSelect);
    }

    $where = implode(' ', $where);

    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $resultQuery = '
    SELECT ' . str_replace(' , ', ' ', implode(', ', $allColumns)) . ' ' . $additionalColumns . "
    FROM $sTable
    " . $join . "
    $sWhere
    " . $where . "
    $sGroupBy
    $sOrder
    $sLimit
    ";

    $rResult = hooks()->apply_filters(
        'datatables_sql_query_results',
        $CI->db->query($resultQuery)->result_array(),
        [
            'table' => $sTable,
            'limit' => $sLimit,
            'order' => $sOrder,
        ]
    );

    /* Data set length after filtering */
    $iFilteredTotal = $CI->db->query("
        SELECT COUNT(*) as iFilteredTotal
        FROM $sTable
        " . $join . "
        $sWhere
        " . $where . "
        $sGroupBy
    ")->row()->iFilteredTotal;

    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }

    /* Total data set length */
    $iTotal = $CI->db->query("SELECT COUNT(*) as iTotal from $sTable $join $where")->row()->iTotal;

    return [
        'rResult' => $rResult,
        'output'  => [
            'draw'                 => $data['draw'] ? intval($data['draw']) : 0,
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => [],
        ],
    ];
}



// get_client_name
function get_client_name($rel_type,$rel_id){
    if($rel_type == 'project'){
       //get project name
       $CI = &get_instance();
       $CI->load->model('projects_model');
       $project = $CI->projects_model->get($rel_id);
       return '<a href="'.admin_url('clients/client/'.$project->client_data->userid).'" target="_blank">'.$project->client_data->company.'</a>';
    }else if($rel_type == 'contract'){
       //get contract name
       $CI = &get_instance();
       $CI->load->model('contracts_model');
       $contract = $CI->contracts_model->get($rel_id);
       return '<a href="'.admin_url('clients/client/'.$contract->userid).'" target="_blank">'.$contract->company.'</a>';
    }else{
        return '';
    }
    
}
?>