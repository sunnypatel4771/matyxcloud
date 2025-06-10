<?php

/**
 * @property Task_manage_model $task_manage_model
 * @property Projects_model $projects_model
 */

use app\services\projects\HoursOverviewChart;

class Task_projects extends AdminController
{

    private $table = "task_manage_groups";

    public function __construct()
    {

        parent::__construct();

        if(
            staff_can( 'task_manage' , 'task_manage' ) ||
            staff_can('create', 'projects') ||
            staff_can('view', 'projects') ||
            staff_can('view_own', 'projects') ||
            staff_can('edit', 'projects')
        )
        {


            $this->load->model('task_manage_model');
            $this->load->model('projects_model');

            $this->load->helper('task_manage/task_manage');

            $this->table = $this->task_manage_model->table_group;


            $this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

        }
        else
            access_denied( _l('task_manage') );


    }

    public function index()
    {
        $data['title'] = _l('projects');

        $data['statuses']   = $this->projects_model->get_project_statuses();

        if( !class_exists('Staff_model') )
            $this->ci->load->model('staff_model');


        $data['staff']      = $this->staff_model->get('', ['active' => 1]);

        $data['groups']     = $this->task_manage_model->get_groups( true );

        $this->load->view('v_task_projects', $data);
    }

    /**
     *
     * Task project list functions
     * @return void
     */
    public function lists(){

        $sTable       = db_prefix()."projects";


        $select = [

            db_prefix() . 'projects.id as id',

            'name',

            get_sql_select_client_company(),

            'start_date',

            'deadline',

            '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',

            'status',

            'task_manage_groups',

        ];

        $where = [];

        $where = [
            ' AND task_manage_groups is not null '
        ];



        if (!has_permission('projects', '', 'view') )
        {

            $where[] = ' AND '.$sTable.'.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';

        }

        $task_project_status_id = $this->input->post('task_project_status_id');
        if ( !empty( $task_project_status_id ) )
        {

            $where[] = ' AND '.$sTable.'.status = '.$task_project_status_id;

        }

        $from_date      = $this->input->post('from_date');
        $to_date        = $this->input->post('to_date');
        $filter_groups  = $this->input->post('filter_groups');
        $filter_staff   = $this->input->post('filter_staff');

        if ( !empty( $from_date ) )
        {

            $from_date = to_sql_date( $from_date );
            $where[] = " AND $sTable.start_date >= '$from_date' ";

        }

        if ( !empty( $to_date ) )
        {

            $to_date = to_sql_date( $to_date );
            $where[] = " AND $sTable.start_date <= '$to_date' ";

        }


        if ( !empty( $filter_groups ) )
        {

            $sql_groups = '';
            foreach ( $filter_groups as $f_group )
            {

                if ( $sql_groups != '' )
                    $sql_groups .= ' OR ';

                $sql_groups .= " ( task_manage_groups like '%\"$f_group\"%' ) ";

            }

            $where[] = " AND ( $sql_groups ) ";

        }


        if ( !empty( $filter_staff ) )
        {

            $staff_sql = implode(', ', $filter_staff );

            $where[] = " AND $sTable.id IN ( SELECT project_id FROM ".db_prefix()."project_members WHERE staff_id IN ( $staff_sql ) ) ";

        }



        $sIndexColumn = 'id';


        $join = [

            'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',

        ];


        $result = data_tables_init( $select, $sIndexColumn, $sTable, $join, $where , [

            'clientid',

            '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids',

        ] );

        $output  = $result['output'];

        $rResult = $result['rResult'];

        $task_all_groups = $this->task_manage_model->get_groups();

        foreach ( $task_all_groups as $task_all_group )
        {

            $task_all_group->task_quantity = $this->task_manage_model->get_group_task_quantity( $task_all_group->id );

        }

        $canChangeStatus = true;
        $project_statuses = $this->projects_model->get_project_statuses();

        foreach ($rResult as $aRow)
        {

            $row = [];


            $link = admin_url('projects/view/' . $aRow['id']);

            $task_diagram = '<div class="row-options"> 
                            <a href="#" onclick="project_task_diagram( '.$aRow['id'].' ); return false;">' . _l('task_manage_show_diagram') . '</a>  |
                            <a href="#" onclick="init_project_preview( '.$aRow['id'].' ); return false;">' . _l('task_manage_show_project') . '</a> 
                            </div> ';

            $row[] = '<a href="' . $link . '">' . $aRow['id'] . '</a>';

            $row[] = '<a href="' . $link . '">' . $aRow['name'] . '</a>'.$task_diagram;

            $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

            $row[] = _d($aRow['start_date']);

            $row[] = _d($aRow['deadline']);

            $membersOutput = '<div class="tw-flex -tw-space-x-1">';

            $members       = explode(',', $aRow['members']);

            $exportMembers = '';

            foreach ($members as $key => $member) {

                if ($member != '') {

                    $members_ids = explode(',', $aRow['members_ids']);

                    $member_id   = $members_ids[$key];

                    $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .

                        staff_profile_image($member_id, [

                            'tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white',

                        ], 'small', [

                            'data-toggle' => 'tooltip',

                            'data-title'  => $member,

                        ]) . '</a>';

                    // For exporting

                    $exportMembers .= $member . ', ';

                }

            }



            $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';

            $membersOutput .= '</div>';

            $row[] = $membersOutput;



            $status = get_project_status_by_id($aRow['status']);

            //$row[]  = '<span class="label project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';">' . $status['name'] . '</span>';
            $outputStatus = '<span class="label" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';" task-status-table="' . $aRow['status'] . '">';
            $outputStatus .= $status['name'];

            if ($canChangeStatus)
            {

                $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';

                $outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';

                $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa-solid fa-chevron-down tw-opacity-70"></i></span>';

                $outputStatus .= '</a>';



                $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $aRow['id'] . '">';

                foreach ($project_statuses as $prjChangeStatus) {

                    if ($aRow['status'] != $prjChangeStatus['id']) {


                        $outputStatus .= '<li>

                          <a href="#"  data-name="'._l('project_status_' . $prjChangeStatus['id']).'" onclick="task_manage_project_mark_as_modal(' . $prjChangeStatus['id'] . ',' . $aRow['id'] . ' , this ); return false;">
        
                             ' . _l('task_mark_as', $prjChangeStatus['name']) . '
        
                          </a>
        
                       </li>';

                    }

                }

                $outputStatus .= '</ul>';

                $outputStatus .= '</div>';

            }

            $row[] = $outputStatus;

            $task_group_label = "";

            $total_task_quantity = 0;
            $created_task_quantity = 0;

            if( !empty( $aRow['task_manage_groups'] ) )
            {

                $project_groups = json_decode( $aRow['task_manage_groups'] , 1 );

                if( !empty( $project_groups ) )
                {

                    $created_task_quantity = $this->task_manage_model->get_created_task_quantity_for_project( $aRow['id'] );

                    foreach ( $task_all_groups as $task_all_group )
                    {

                        if( in_array( $task_all_group->id , $project_groups ) )
                        {

                            $total_task_quantity += $task_all_group->task_quantity ?? 0;

                            $task_group_label .= " <span class='label' style='color:#2563eb;border:1px solid #a8c1f7;background: #f6f9fe;'> $task_all_group->group_name </span> ";

                        }

                    }

                }

            }


            $row[] = $task_group_label ;


            $percent = 0;

            if( $total_task_quantity > 0 && $created_task_quantity > 0 )
            {

                $percent = ceil( ( $created_task_quantity / $total_task_quantity ) * 100 );

                if( $percent > 100 )
                    $percent = 100;
            }


            $task_percent = "<div style='width: 300px'>
                                <div class='task_manage_progress_bar'> 
                                    <div class='task_manage_progress' style='width: $percent%'></div> 
                                    <div class='task_manage_percent'>$percent%</div>
                                </div>
                                <div style='width: 60px; float: right'>$created_task_quantity / $total_task_quantity </div> 
                            </div>";

            $row[] = $task_percent." ";


            $row['DT_RowClass'] = 'has-row-options';


            $output['aaData'][] = $row;
        }

        echo json_encode($output);

        die;
    }


    public function diagram_detail()
    {

        if( $this->input->is_ajax_request() )
        {

            $project_id = $this->input->post('project_id');

            $project_detail = $this->db->select('task_manage_groups')->from(db_prefix().'projects')->where('id',$project_id)->get()->row();

            $data = [];

            if( !empty( $project_detail->task_manage_groups ) )
            {

                // created tasks lists @fields => name, status, task_manage_task_id
                $project_tasks = $this->task_manage_model->get_project_tasks( $project_id );

                $created_tasks = [];

                if( !empty( $project_tasks ) )
                {

                    foreach ( $project_tasks as $project_task )
                    {

                        $created_tasks[$project_task->task_manage_task_id] = $project_task;

                    }

                }

                // groups that assigned the project
                $project_groups = json_decode( $project_detail->task_manage_groups , 1 );

                $task_group_data = [];

                foreach ( $project_groups as $group_id )
                {

                    $group_detail = $this->task_manage_model->get_group( $group_id );

                    if( !empty( $group_detail ) )
                    {

                        $process_current_order = 0;

                        $task_group_data[ $group_id ]['group_name'] = $group_detail->group_name ;

                        $group_tasks = $this->task_manage_model->get_item_tasks( $group_id );

                        if( !empty( $group_tasks ) )
                        {

                            // found the current task order
                            foreach ( $group_tasks as $group_task )
                            {

                                if( !empty( $created_tasks[ $group_task->id ] ) )
                                {

                                    $group_task->project_status = format_task_status( $created_tasks[ $group_task->id ]->status , true );
                                    $group_task->project_status_id = $created_tasks[ $group_task->id ]->status;

                                    if( $process_current_order < $group_task->task_order )
                                        $process_current_order = $group_task->task_order;

                                }

                            }


                            foreach ( $group_tasks as $group_task )
                            {

                                if ( $process_current_order > $group_task->task_order )
                                    $task_group_data[ $group_id ]['task_completed'][$group_task->id] = $group_task;
                                elseif ( $process_current_order == $group_task->task_order )
                                    $task_group_data[ $group_id ]['task_in_process'][$group_task->id] = $group_task;
                                else
                                    $task_group_data[ $group_id ]['task_pending'][$group_task->id] = $group_task;

                            }

                        }


                    }

                }

                $data['task_group_data'] = $task_group_data;

            }


            $diagram_content = $this->load->view('v_project_diagram' , $data ,true);

            echo json_encode( [ "diagram_content" => $diagram_content ] );

        }

    }


    /**
     * @Version 1.0.5 pipeline
     */
    public function pipeline()
    {

        $data['bodyclass']       = 'proposals-pipeline';

        $data['switch_pipeline'] = false;

        $data['title'] = _l('switch_to_pipeline');


        $this->load->view('v_pipeline_manage', $data);

    }

    public function pipeline_content()
    {

        $data['title']      = _l('switch_to_pipeline');

        $data['items']      = $this->task_manage_model->get_pipeline_groups();


        if (!class_exists('tasks_model') )
            $this->load->model('tasks_model');

        $data['task_statuses'] = $this->tasks_model->get_statuses();

        $this->load->view('v_pipeline_detail', $data);

    }

    public function group_pipeline( $group_id = 0 )
    {

        $data['bodyclass']       = 'proposals-pipeline';

        $data['switch_pipeline'] = false;

        $data['title'] = _l('task_manage_groups_pipeline');


        $data['group_detail']       = 1;

        $data['selected_group_id']  = $group_id;
        
        $data['groups']      = $this->task_manage_model->get_pipeline_groups();

        $this->load->view('v_pipeline_manage', $data);

    }



    public function group_pipeline_content( $group_id = 0 )
    {

        $data['title']      = _l('task_manage_groups_pipeline');

        $data['items']      = $this->task_manage_model->get_pipeline_group_orders( $group_id );

        if (!class_exists('tasks_model') )
            $this->load->model('tasks_model');

        $data['task_statuses'] = $this->tasks_model->get_statuses();

        $data['group_detail'] = 1;

        $data['group_id'] = $group_id;

        $this->load->view('v_pipeline_detail', $data);

    }


    /**
     * Project Status Kanban
     */
    public function kanban()
    {


        $data['bodyclass']       = 'proposals-pipeline';

        $data['switch_pipeline'] = false;

        $data['title'] = _l('leads_switch_to_kanban');


        if( !class_exists('Staff_model') )
            $this->ci->load->model('staff_model');


        $data['staff']      = $this->staff_model->get('', ['active' => 1]);

        $data['groups']     = $this->task_manage_model->get_groups( true );


        $this->load->view('v_kanban_project', $data);


    }

    public function kanban_content()
    {

        $data['title']      = _l('leads_switch_to_kanban');


        if (!class_exists('projects_model') )
            $this->load->model('projects_model');

        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $this->load->view('v_kanban_detail', $data);

    }

    /**
     * Project status update function
     */
    public function kanban_status_update()
    {

        $success = false;

        $message = '';

        if( $this->input->is_ajax_request() && $this->input->post('projects') && $this->input->post('status') )
        {


            if (!class_exists('projects_model') )
                $this->load->model('projects_model');


            if (staff_can('create', 'projects') || staff_can('edit', 'projects') )
            {

                $projects   = $this->input->post('projects');
                $status_id  = $this->input->post('status');

                $status     = get_project_status_by_id($status_id);

                foreach ( $projects as $project_id )
                {

                    $project_info = $this->db->select('status')->from(db_prefix().'projects')->where('id',$project_id)->get()->row();

                    if ( $project_info->status != $status_id )
                    {

                        $post_data = [
                            'project_id' => $project_id ,
                            'status_id' => $status_id ,
                            'notify_project_members_status_change' => false ,
                            'mark_all_tasks_as_completed' => false ,
                        ];

                        $success = $this->projects_model->mark_as( $post_data );

                        $message = _l('project_marked_as_failed', $status['name']);

                        if ($success) {

                            $message = _l('project_marked_as_success', $status['name']);

                        }

                    }

                }

            }

        }

        echo json_encode([

            'success' => $success,

            'message' => $message,

        ]);


    }


    public function preview( $project_id )
    {


        $this->load->helper('date');

        $data = [];

        $project = $this->projects_model->get( $project_id );



        $project->settings->available_features = unserialize($project->settings->available_features);

        $data['statuses']                      = $this->projects_model->get_project_statuses();




        $data['project']  = $project;

        $data['currency'] = $this->projects_model->get_currency($project_id);



        $data['project_total_logged_time'] = $this->projects_model->total_logged_time($project_id);


        $percent         = $this->projects_model->calc_progress($project_id);

        $data['members'] = $this->projects_model->get_project_members($project_id);

        foreach ($data['members'] as $key => $member)
        {

            $data['members'][$key]['total_logged_time'] = 0;

            $member_timesheets                          = $this->tasks_model->get_unique_member_logged_task_ids($member['staff_id'], ' AND task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $this->db->escape_str($project_id) . '")');



            foreach ($member_timesheets as $member_task) {

                $data['members'][$key]['total_logged_time'] += $this->tasks_model->calc_task_total_time($member_task->task_id, ' AND staff_id=' . $member['staff_id']);

            }

        }




        $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);

        $data['project_days_left']         = $data['project_total_days'];

        $data['project_time_left_percent'] = 100;

        if ($data['project']->deadline)
        {

            if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {

                $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);

                $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;

                $data['project_time_left_percent'] = round($data['project_time_left_percent'], 2);

            }

            if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {

                $data['project_days_left']         = 0;

                $data['project_time_left_percent'] = 0;

            }

        }



        $__total_where_tasks = 'rel_type = "project" AND rel_id=' . $this->db->escape_str($project_id);

        if (!staff_can('view', 'tasks')) {

            $__total_where_tasks .= ' AND ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';



            if (get_option('show_all_tasks_for_project_member') == 1) {

                $__total_where_tasks .= ' AND (rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . '))';

            }

        }



        $__total_where_tasks = hooks()->apply_filters('admin_total_project_tasks_where', $__total_where_tasks, $project_id);



        $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;



        $data['tasks_not_completed'] = total_rows(db_prefix() . 'tasks', $where);

        $total_tasks                 = total_rows(db_prefix() . 'tasks', $__total_where_tasks);

        $data['total_tasks']         = $total_tasks;



        $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = ' . Tasks_model::STATUS_COMPLETE . ' AND rel_type="project" AND rel_id="' . $project_id . '"';



        $data['tasks_completed'] = total_rows(db_prefix() . 'tasks', $where);



        $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);

        $data['tasks_not_completed_progress'] = round($data['tasks_not_completed_progress'], 2);


        $data['project_overview_chart'] = (new HoursOverviewChart(

            $id,

            ($this->input->get('overview_chart') ? $this->input->get('overview_chart') : 'this_week')

        ))->get();

        @$percent_circle        = $percent / 100;

        $data['percent_circle'] = $percent_circle;

        $data['percent'] = $percent;



        $other_projects_where = 'id != ' . $project_id;



        $statuses = $this->projects_model->get_project_statuses();



        $other_projects_where .= ' AND (';

        foreach ($statuses as $status) {

            if (isset($status['filter_default']) && $status['filter_default']) {

                $other_projects_where .= 'status = ' . $status['id'] . ' OR ';

            }

        }



        $other_projects_where = rtrim($other_projects_where, ' OR ');



        $other_projects_where .= ')';



        if (!staff_can('view', 'projects')) {

            $other_projects_where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';

        }



        $data['other_projects'] = $this->projects_model->get('', $other_projects_where);

        $data['title']          = $data['project']->name;

        $data['project_status'] = get_project_status_by_id($project->status);





        $data['statuses'] = $this->projects_model->get_project_statuses();

        $data['staff']    = $this->staff_model->get('', ['active' => 1]);


        $this->load->view('v_project_modal' , $data);

    }


    public function save_project()
    {

        if( $this->input->post() )
        {

            if ( !has_permission('projects', '', 'edit') )
            {

                echo json_encode([
                    'success' => false,
                    'message' => 'Permission error',
                ]);

                die();

            }



            $id = $this->input->post('project_id');

            $data = $this->input->post();

            unset($data['project_id']);


            if (isset($data['custom_fields'])) {

                $custom_fields = $data['custom_fields'];

                handle_custom_fields_post($id, $custom_fields);

                unset($data['custom_fields']);

            }


            $this->db->select('status');

            $this->db->where('id', $id);

            $old_status = $this->db->get(db_prefix() . 'projects')->row()->status;



            $send_created_email = false;

            $send_project_marked_as_finished_email_to_contacts = false;


            $original_project = $this->projects_model->get($id);



            $data['project_cost']    = !empty($data['project_cost']) ? $data['project_cost'] : null;

            $data['estimated_hours'] = !empty($data['estimated_hours']) ? $data['estimated_hours'] : null;



            if ($old_status == 4 && $data['status'] != 4) {

                $data['date_finished'] = null;

            } elseif (isset($data['date_finished'])) {

                $data['date_finished'] = to_sql_date($data['date_finished'], true);

            }



            if (isset($data['progress_from_tasks'])) {

                $data['progress_from_tasks'] = 1;

            } else {

                $data['progress_from_tasks'] = 0;

            }



            if (!empty($data['deadline'])) {

                $data['deadline'] = to_sql_date($data['deadline']);

            } else {

                $data['deadline'] = null;

            }



            $data['start_date'] = to_sql_date($data['start_date']);

            if ($data['billing_type'] == 1) {

                $data['project_rate_per_hour'] = 0;

            } elseif ($data['billing_type'] == 2) {

                $data['project_cost'] = 0;

            } else {

                $data['project_rate_per_hour'] = 0;

                $data['project_cost']          = 0;

            }

            if (isset($data['project_members'])) {

                $project_members = $data['project_members'];

                unset($data['project_members']);

            }

            $_pm = [];

            if (isset($project_members)) {

                $_pm['project_members'] = $project_members;

            }


            $this->projects_model->add_edit_members($_pm, $id);




            $data = hooks()->apply_filters('before_update_project', $data, $id);



            $this->db->where('id', $id);

            $this->db->update(db_prefix() . 'projects', $data);


            $affectedRows = 0;

            if ( $this->db->affected_rows() > 0 )
            {

                $affectedRows++;

            }

            if ( $affectedRows > 0 )
            {

                $this->projects_model->log_activity($id, 'project_activity_updated');

                log_activity('Project Updated [ID: ' . $id . ']');



                if ($original_project->status != $data['status']) {

                    hooks()->do_action('project_status_changed', [

                        'status'     => $data['status'],

                        'project_id' => $id,

                    ]);

                    // Give space this log to be on top

                    sleep(1);

                    if ($data['status'] == 4) {

                        $this->projects_model->log_activity($id, 'project_marked_as_finished');

                        $this->db->where('id', $id);

                        $this->db->update(db_prefix() . 'projects', ['date_finished' => date('Y-m-d H:i:s')]);

                    } else {

                        $this->projects_model->log_activity($id, 'project_status_updated', '<b><lang>project_status_' . $data['status'] . '</lang></b>');

                    }



                }

                hooks()->do_action('after_update_project', $id);



                echo json_encode([
                    'success' => true,
                    'message' => _l("task_manage_successful"),
                ]);


            }


        }

    }



}
