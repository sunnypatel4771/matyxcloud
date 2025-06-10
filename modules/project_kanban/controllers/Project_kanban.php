<?php

/**
 * @property Projects_model $projects_model
 */


use app\services\projects\HoursOverviewChart;


class Project_kanban extends AdminController
{

    public function __construct()
    {

        parent::__construct();

        if( !staff_can( 'project_kanban' , 'project_kanban' ) )
            access_denied( _l('project_kanban') );


        $this->load->model('projects_model');


        /**
         * Db Checking
         */
        if ( !$this->db->table_exists( db_prefix() . 'project_kanban_settings' ) )
        {

            $this->db->query("
                    CREATE TABLE `".db_prefix()."project_kanban_settings` (
                        `status_id` int(11) NOT NULL 
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }


        if ( !$this->db->table_exists( db_prefix() . 'project_kanban_project_statuses' ) )
        {


            $this->db->query("
                    CREATE TABLE `".db_prefix()."project_kanban_project_statuses` (
                        `status_id` int(11) NOT NULL AUTO_INCREMENT,
                        `status_name` varchar(255) NULL,
                        `status_order` int NULL,
                        `status_color` varchar(50) NULL,
                        `filter_default` tinyint NULL,
                        PRIMARY KEY (`status_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }




        if( !$this->db->field_exists('es_status_change_date', db_prefix() .'projects') )
        {

            $this->db->query('ALTER TABLE `'.db_prefix().'projects`
                                ADD COLUMN `es_status_change_date` datetime NULL AFTER `status`;');

        }




    }


    /**
     * Project Status Kanban view index
     */
    public function index()
    {


        $data['bodyclass']       = 'proposals-pipeline';

        $data['switch_pipeline'] = false;

        $data['title']          = _l('leads_switch_to_kanban');

        if( !class_exists('Staff_model') )
            $this->ci->load->model('staff_model');


        $data['staff']      = $this->staff_model->get('', ['active' => 1]);

        $data['project_statuses']   = $this->projects_model->get_project_statuses();

        $data['active_statuses']    = $this->get_settings();

        $this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

        $this->load->view('v_kanban_project', $data);


    }

    public function kanban_content()
    {

        $data['title']              = _l('leads_switch_to_kanban');

        $data['project_statuses']   = $this->projects_model->get_project_statuses();

        $data['active_statuses']    = $this->get_settings();

        $this->load->view('v_kanban_detail', $data);

    }

    public function kanban_content_load( $status_id = 0 )
    {

        $this->load->view('v_kanban_load_more' , [ 'status_id' => $status_id ] );

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


        // files
        $data['files']          = $this->projects_model->get_files($project_id);

        // invoices
        $data['invoices']       = $this->db->select('id, currency, number, total, total_tax, YEAR(date) as year, date, status')
                                            ->from(db_prefix().'invoices')
                                            ->where('project_id',$project_id)
                                            ->get()
                                            ->result();


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
                    'message' =>  _l('updated_successfully', _l('project')),
                ]);


            }


        }

    }


    public function save_settings()
    {

        $table = db_prefix().'project_kanban_settings';


        $this->db->truncate($table);

        $statuses = $this->input->post('project_kanban_status');

        if ( $statuses )
        {

            foreach ( $statuses as $status )
            {

                $this->db->insert( $table , [ 'status_id' => $status ] );

            }

        }

        $enable_status = $this->input->post('project_kanban_status_management');

        if ( !empty( $enable_status ) )
            $enable_status = 1;
        else
            $enable_status = 0;

        if ( option_exists('project_kanban_status_management') )
        {
            update_option( 'project_kanban_status_management' , $enable_status  , 0 );
        }
        else
        {
            add_option( 'project_kanban_status_management' , $enable_status  , 0 );
        }


        if ( empty( $enable_status ) )
        {

            $this->db->truncate(db_prefix().'project_kanban_project_statuses');

        }

        redirect( admin_url('project_kanban') );

    }

    public function get_settings()
    {

        $active_statuses = $this->db->select('*')->from(db_prefix().'project_kanban_settings')->get()->result();

        if ( !empty( $active_statuses ) )
        {

            $return_status = [] ;

            foreach ( $active_statuses as $status )
            {

                $return_status[ $status->status_id ] = $status->status_id;

            }

            return $return_status;

        }

        return [];

    }


    /**
     * Project status changes
     */
    public function status_detail( $status_id = 0 )
    {

        $data['status']     = $this->db->select('*')->from(db_prefix().'project_kanban_project_statuses')->where('status_id',$status_id)->get()->row();

        if ( !empty( $data['status'] ) )
        {

            $data['title']      = $data['status']->status_name;

        }
        else
        {
            $status_id = 0 ;

            $data['status'] = new stdClass();

            $data['status']->status_order   = '' ;
            $data['status']->status_name    = '';
            $data['status']->status_color   = '';
            $data['status']->filter_default = '';

            $data['title']      = _l('project_kanban_new_status');

        }

        $data['status_id']  = $status_id;

        $this->load->view('v_project_status_detail' , $data );

    }


    public function status_save( $status_id = 0 )
    {


        if ( $this->input->post() )
        {


            $post_data = $this->input->post();

            if( empty( $post_data['filter_default'] ) )
                $post_data['filter_default'] = 0;


            if ( $status_id > 0 )
            {

                $this->db->where('status_id',$status_id)->update(db_prefix().'project_kanban_project_statuses',$post_data);


                set_alert('success', _l('added_successfully', _l('project_kanban_project_status') ) );

            }
            else
            {

                $this->db->insert(db_prefix().'project_kanban_project_statuses',$post_data);

                set_alert('success', _l('updated_successfully', _l('project_kanban_project_status') ) );

            }


            redirect( admin_url('project_kanban') );

        }



    }


    public function status_remove( $status_id = 0 )
    {

        $status     = $this->db->select('*')->from(db_prefix().'project_kanban_project_statuses')->where('status_id',$status_id)->get()->row();

        if ( $status )
        {

            $data['status']  = $status;

            $data['status_id']  = $status_id;

            $data['statuses']   = $this->projects_model->get_project_statuses();

            $this->load->view('v_project_status_remove' , $data );

        }

    }

    // new_status_id  status_delete
    public function status_delete()
    {

        if ( $this->input->post() )
        {

            $table_name = db_prefix().'project_kanban_project_statuses';

            $status_id      = $this->input->post('status_id');

            $new_status_id  = $this->input->post('new_status_id');


            $status     = $this->db->select('status_name')->from($table_name)->where('status_id',$status_id)->get()->row();

            $this->db->where('status_id',$status_id)->delete($table_name);

            log_activity( "Project status deleted [ Status ID : $status_id  Status name : $status->status_name ] " );


            if ( !empty( $new_status_id ) && $new_status_id != $status_id )
            {


                $projects = $this->db->select('')->from(db_prefix().'projects')->where('status',$status_id)->get()->result();

                if ( !empty( $projects ) )
                {

                    foreach ( $projects as $project )
                    {

                        $post_data = [
                            'project_id' => $project->id ,
                            'status_id' => $new_status_id ,
                            'notify_project_members_status_change' => false ,
                            'mark_all_tasks_as_completed' => false ,
                        ];

                        $this->projects_model->mark_as( $post_data );

                    }

                }


            }


            set_alert('success', _l('deleted', _l('project_kanban_project_status') ) );

            redirect( admin_url('project_kanban') );


        }

    }

}
