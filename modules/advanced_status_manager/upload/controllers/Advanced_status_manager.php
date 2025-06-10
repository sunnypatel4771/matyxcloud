<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Advanced_status_manager extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!is_admin()) {
            redirect(admin_url());
        }
        $this->load->model('Task_status_model');
        $this->load->model('Project_status_model');
        $this->load->model('Ticket_status_model');
    }

    /* List all task statuses */
    public function index()
    {
        $this->display_index('task');
    }

    /* List all project statuses */
    public function project()
    {
        $this->display_index('project');
    }

    /* List all ticket statuses */
    public function ticket()
    {
        $this->display_index('ticket');
    }

    private function display_index($type)
    {

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('advanced_status_manager', "tables/{$type}_statuses"));
        }
        $data['title'] = _l("Advanced_status_manager ");
        $data['type'] = $type;

        $this->app_scripts->add('advanced_status_manager-js', module_dir_url('advanced_status_manager', 'assets/js/advanced_status_manager.js'), 'admin', ['app-js']);
        $this->app_css->add('advanced_status_manager-css', module_dir_url('advanced_status_manager', 'assets/css/advanced_status_manager.css'), 'admin', ['app-css']);

        $this->load->view('advanced_status_manager/index', $data);
    }



    /**
     * New task status view.
     * Load staff and statuses for multi selects.
     */
    public function create_task()
    {
        $data['title'] = _l("Create new task status ");
        $data['enableNotAssignedStaffIds'] = true;
        $data['type'] = 'task';
        $this->load->model('staff_model');
        $data['staff'] = array_map(
            fn ($x) => [
                'staffid' => $x['staffid'],
                'full_name' => $x['full_name']
            ],
            $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1])
        );
        $data['statuses'] = $this->Task_status_model->get('', false, false);
        $this->load->view('advanced_status_manager/create', $data);
    }

    /**
     * New task status view.
     * Load staff and statuses for multi selects.
     */
    public function create_project()
    {
        $data['title'] = _l("Create new project status ");
        $data['enableNotAssignedStaffIds'] = false;
        $data['type'] = 'project';

        $data['statuses'] = $this->Project_status_model->get('',  false);
        $this->load->view('advanced_status_manager/create', $data);
    }

    /**
     * New ticket status view.
     * Load staff and statuses for multi selects.
     */
    public function create_ticket()
    {
        $data['title'] = _l("Create new ticket status ");
        $data['type'] = 'ticket';

        $data['statuses'] = $this->Ticket_status_model->get('',  false);
        $this->load->view('advanced_status_manager/create', $data);
    }

    /**
     * Create new task status view.
     * Validate data and store using TaskStatusModel
     */
    public function store_task()
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_task/?info=validationErrors'));
        }
        $this->Task_status_model->store($data);
        redirect(admin_url('advanced_status_manager'));
    }

    /**
     * Create new project status view.
     * Validate data and store using ProjectStatusModel
     */
    public function store_project()
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_project/?info=validationErrors'));
        }
        $this->Project_status_model->store($data);
        redirect(admin_url('advanced_status_manager/project'));
    }

    /**
     * Create new ticket status view.
     * Validate data and store using TicketStatusModel
     */
    public function store_ticket()
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_ticket/?info=validationErrors'));
        }
        $this->Ticket_status_model->store($data);
        redirect(admin_url('advanced_status_manager/ticket'));
    }


    /**
     * Validate task status data.
     * Name -> required
     * 
     * Color -> required
     * 
     * Order -> required
     * 
     * notAssignedStaffIds -> is exists must be array of integers
     *  
     * avalibleStatusesForChange -> is exists must be array of integers 
     */
    private function validate($data)
    {
        if (isset($data['order']) && isset($data['color']) && isset($data['name']) && is_numeric($data['order'])) {
            if (isset($data['notAssignedStaffIds'])) {
                if (!is_array($data['notAssignedStaffIds'])) {
                    return false;
                }

                if (count(array_filter($data['notAssignedStaffIds'], fn ($x) => !is_numeric($x))) > 0) {
                    return false;
                }
            }
            if (isset($data['avalibleStatusesForChange'])) {
                if (!is_array($data['avalibleStatusesForChange'])) {
                    return false;
                }

                if (count(array_filter($data['avalibleStatusesForChange'], fn ($x) => !is_numeric($x))) > 0) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Edit task status.
     */
    public function edit_task_status($id)
    {
        $status = $this->Task_status_model->get($id, true, true);
        if (!$status) {
            redirect(admin_url('advanced_status_manager'));
        }
        $data['status'] = $status;
        $data['type'] = 'task';
        $data['enableNotAssignedStaffIds'] = true;
        $data['title'] = _l("Update task status #");
        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['statuses'] = $this->Task_status_model->get('', false, false);

        $this->load->view('advanced_status_manager/edit', $data);
    }

    /**
     * Edit project status.
     */
    public function edit_project_status($id)
    {
        $status = $this->Project_status_model->get($id,  true);
        if (!$status) {
            redirect(admin_url('advanced_status_manager/project'));
        }
        $data['status'] = $status;
        $data['type'] = 'project';
        $data['enableNotAssignedStaffIds'] = false;

        $data['title'] = _l("Update project status #");

        $data['statuses'] = $this->Project_status_model->get('',  false);

        $this->load->view('advanced_status_manager/edit', $data);
    }

    /**
     * Edit ticket status.
     */
    public function edit_ticket_status($id)
    {
        $status = $this->Ticket_status_model->get($id,  true);
        if (!$status) {
            redirect(admin_url('advanced_status_manager/ticket'));
        }
        $data['status'] = $status;
        $data['type'] = 'ticket';

        $data['title'] = _l("Update ticket status #");

        $data['statuses'] = $this->Ticket_status_model->get('',  false);

        $this->load->view('advanced_status_manager/edit', $data);
    }

    /**
     * Update task status.
     */
    public function update_task($id)
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_task/?info=validationErrors'));
        }

        $this->Task_status_model->update($data, $id);
        redirect(admin_url('advanced_status_manager'));
    }

    /**
     * Update project status.
     */
    public function update_project($id)
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_project/?info=validationErrors'));
        }

        $this->Project_status_model->update($data, $id);
        redirect(admin_url('advanced_status_manager/project'));
    }

    /**
     * Update ticket status.
     */
    public function update_ticket($id)
    {
        $data = $this->input->post();
        if (!$this->validate($data)) {
            redirect(admin_url('advanced_status_manager/create_ticket/?info=validationErrors'));
        }

//       Parse data fields into ticket fields
        $data['statuscolor'] = $data['color'];
        $data['statusorder'] = $data['order'];

        $this->Ticket_status_model->update($data, $id);
        redirect(admin_url('advanced_status_manager/ticket'));
    }

    /**
     * Delete task status.
     */
    public function delete_task_status($id)
    {
        $this->Task_status_model->delete($id);
        redirect(admin_url('advanced_status_manager'));
    }

    /**
     * Delete project status.
     */
    public function delete_project_status($id)
    {
        $this->Project_status_model->delete($id);
        redirect(admin_url('advanced_status_manager/project'));
    }

    /**
     * Delete ticket status.
     */
    public function delete_ticket_status($id)
    {
        $this->Ticket_status_model->delete($id);
        redirect(admin_url('advanced_status_manager/ticket'));
    }
}
