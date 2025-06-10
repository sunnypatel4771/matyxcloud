<?php

/**
 * @property Task_manage_model $task_manage_model
 */

class Manage extends AdminController
{

    protected static $json = Array( 'success' => false , 'message' => '' , 'data' => '');

    private $table = "task_manage_groups";

    private $milestion_default_color = '#28B8DA';

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


            $this->check_the_db();

            $this->load->model('task_manage_model');

            $this->load->helper('task_manage/task_manage');

            $this->table = $this->task_manage_model->table_group;

        }
        else
            access_denied( _l('task_manage') );


    }

    public function index()
    {
        $data['title'] = _l('task_manage');

        $this->load->view('v_task_manage', $data);
    }

    /**
     *
     * Task group functions
     * @return void
     */
    public function lists(){

        $sTable       = $this->table;

        $select = [

            'id',

            'group_name',

            'added_date',

            'status',

        ];

        $where = [];

        $sIndexColumn = 'id';

        $join = [ ];

        $result = data_tables_init( $select, $sIndexColumn, $sTable, $join, $where  );

        $output  = $result['output'];

        $rResult = $result['rResult'];

        foreach ($rResult as $aRow)
        {

            $row = [];

            $item_id = $aRow["id"];

            $href = admin_url("task_manage/manage/detail/".$item_id);
            $href_delete = admin_url("task_manage/manage/group_delete/".$item_id);

            $content = '<div class="row-options">';
                $content .= '<a href="'.$href.'" >'._l('view').'</a>';
                $content .= ' | <a href="#" onclick="task_manage_model( '.$item_id.' )" >'._l('edit').'</a>';
                $content .= ' | <a href="'.$href_delete.'" class="_delete text-danger" >'._l('delete').'</a>';
            $content .= '</div>';

            $row[] = $item_id;

            $row[] = $aRow['group_name'].$content;

            $row[] = _dt( $aRow['added_date'] );


            $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="Status">

                                <input type="checkbox" data-switch-url="' . admin_url() . 'task_manage/manage/group_change_status" name="onoffswitch" 
                                        class="onoffswitch-checkbox" id="snack_' . $aRow['id'] . '" 
                                        data-id="' . $aRow['id'] . '" ' . ($aRow['status'] == 1 ? 'checked' : '') . '>
                            
                                <label class="onoffswitch-label" for="snack_' . $aRow['id'] . '"></label>
                        
                            </div>';

            $toggleActive .= '<span class="hide">' . ($aRow['status'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

            $row[] = $toggleActive;



            $output['aaData'][] = $row;
        }

        echo json_encode($output);

        die;
    }

    public function group_detail()
    {

        if ( $this->input->is_ajax_request() )
        {

            $group_id = $this->input->post('group_id');

            $detail = $this->task_manage_model->get_group( $group_id );

            $data['detail'] = $detail;

            echo json_encode( $data );

        }

    }

    public function group_save()
    {

        if ( $this->input->post() )
        {

            $group_id   = $this->input->post('group_id');
            $group_name = $this->input->post('group_name');

            if( empty( $group_id ) )
            {

                $this->db->insert( $this->table , [ 'group_name' => $group_name , 'added_staff' => get_staff_user_id() , 'added_date' => date('Y-m-d H:i:s') ] );

                $group_id = $this->db->insert_id();

            }
            else
            {

                $this->db->where('id',$group_id)->update( $this->table , [ 'group_name' => $group_name ] );

            }

            if( !empty( $group_id ) )
            {

                set_alert('success',_l('task_manage_successful'));

                redirect( admin_url("task_manage/manage/detail/$group_id"));

            }
            else
            {
                set_alert('danger',_l('task_manage_error'));

                redirect( admin_url("task_manage/manage/"));
            }

        }

    }

    public function group_change_status($id, $status)
    {

        if ($this->input->is_ajax_request()) {

            $this->db->where('id', $id);

            $this->db->update($this->table, [

                'status' => $status,

            ]);

            if ($this->db->affected_rows() > 0)
                return true;
            else
                return false;

        }

    }

    public function group_delete( $group_id = 0 )
    {

        $this->db->where('id',$group_id)->delete( $this->table );

        set_alert('success',_l('task_manage_successful'));

        redirect( admin_url('task_manage/manage') );

    }

    /**
     * Task group manage funtions
     * @param $item_id
     * @return void
     */
    public function detail( $item_id = 0 )
    {

        $item_info = $this->task_manage_model->get_group( $item_id );

        if ( !empty( $item_info ) )
        {

            $data = [];

            $data["title"]      = $item_info->group_name;

            $data["item_id"]    = $item_id;

            $data["item_info"]  = $item_info;

            $data["milestones"] = $this->task_manage_model->get_item_milestones( $item_id );

            $data["active_tab"] = !empty( $this->input->get('tab') ) ? $this->input->get('tab') : 'task' ;


            // tasks
            $data["item_tasks"] = $this->task_manage_model->get_item_tasks( $item_id );
            $groups = [] ;
            $max_group_value = 1;

            if ( !empty( $data["item_tasks"] ) )
            {

                foreach ( $data["item_tasks"] as $item_task )
                {
                    $max_group_value = $item_task->task_order;
                    $groups[ $item_task->task_order ][ $item_task->id ] = $item_task->name ;

                }

            }

            if( $max_group_value < 1 )
                $max_group_value = 1;

            $data["item_groups"] = $groups;
            $data["max_group_value"] = $max_group_value;

            $this->load->view('v_task_manage_detail',$data);

        }
        else
        {
            set_alert( "danger" , _l('task_manage_group_not_found') );
            redirect(admin_url('task_manage/manage'));
        }

    }

    /**
     * Task Lists
     */
    public function list_tasks( $item_id = 0 )
    {
        $sTable             = $this->task_manage_model->table_tasks;
        $milestone_table    = $this->task_manage_model->table_milestones;

        $select = [

            $sTable.'.id as id',

            'name',

            'milestone_name' ,

            'task_status' ,

            'priority' ,

            'task_order' ,

        ];


        $where = [];

        $where[] = "AND $sTable.group_id = $item_id";

        $sIndexColumn = 'id';

        $join = ['LEFT JOIN ' . $milestone_table . ' ON ' . $milestone_table . '.id = ' . $sTable . '.milestone'];


        $result = data_tables_init( $select, $sIndexColumn, $sTable, $join, $where  );

        $output  = $result['output'];

        $rResult = $result['rResult'];

        $delete_link = admin_url('task_manage/manage/delete_task/'.$item_id.'/');

        foreach ($rResult as $aRow)
        {
            $row = [];

            $task_id = $aRow["id"];

            $content = '<div class="row-options">';

                $content .= " <a href='#' onclick='task_manage_task_detail( $task_id )' > ". _l('edit') ." </a> ";

                $content .= " | <a href='#' class='text-warning' onclick='task_manage_task_detail( $task_id , 1 )' > ". _l('copy') ." </a> ";

                $content .= ' | <a class="_delete text-danger" href="'.$delete_link.$task_id.'">'._l('delete').'</a>';

            $content .= '</div>';

            $row[] = $task_id;

            $row[] = $aRow['name'].$content;

            $row[] = $aRow['milestone_name'];

            $row[] = format_task_status( $aRow['task_status'] ) ;

            $row[] = task_priority( $aRow['priority'] ) ;

            $row[] = $aRow['task_order'];


            $row['DT_RowClass'] = 'has-row-options';

            $output['aaData'][] = $row;

        }

        echo json_encode($output);

        die;

    }

    /**
     * @note Task saving
     */
    public function save_task()
    {

        if ($this->input->post())
        {

            $id         = $this->input->post('id');
            $item_id    = $this->input->post('group_id');

            $redirect_item_id = $item_id;

            $db_data    = $this->input->post();

            $db_data["description"] = $this->input->post('description',false);

            $custom_fields = [];
            if ( isset( $db_data['custom_fields'] ) )
            {

                $custom_fields = $db_data['custom_fields'];

                unset( $db_data['custom_fields'] );

            }


            // task saving for new item
            if( isset( $db_data["perfex_item"] ) && !empty( $db_data["perfex_item"] )  )
            {

                $item_id = $db_data["perfex_item"];

                $db_data["group_id"] = $item_id;

                unset( $db_data["perfex_item"] );

            }


            if( !empty( $db_data["assign_project_owner"] ) )
                $db_data["assign_project_owner"] = 1 ;
            else
                $db_data["assign_project_owner"] = 0 ;

            if( !empty( $db_data["task_is_public"] ) )
                $db_data["task_is_public"] = 1 ;
            else
                $db_data["task_is_public"] = 0 ;

            if( !empty( $db_data["task_is_billable"] ) )
                $db_data["task_is_billable"] = 1 ;
            else
                $db_data["task_is_billable"] = 0 ;

            if( !empty( $db_data["task_visible_to_client"] ) )
                $db_data["task_visible_to_client"] = 1 ;
            else
                $db_data["task_visible_to_client"] = 0 ;





            $db_data["assignees"]       = !empty( $db_data["assignees"] )       ? json_encode( $db_data["assignees"] )      : '';
            $db_data["followers"]       = !empty( $db_data["followers"] )       ? json_encode( $db_data["followers"] )      : '';
            $db_data["checklist_items"] = !empty( $db_data["checklist_items"] ) ? json_encode( $db_data["checklist_items"] ) : '';


            $task_table = $this->task_manage_model->table_tasks;

            if( !empty( $id ) )
            {

                $this->db->where('id',$id)->update( $task_table , $db_data );

            }
            else
            {

                $db_data['task_order'] = 0;

                $this->db->insert( $task_table , $db_data );

                $id = $this->db->insert_id();

            }


            $this->task_manage_model->save_custom_fields( $id , $custom_fields );

            set_alert('success',_l('task_manage_successful'));

            redirect( admin_url('task_manage/manage/detail/'.$redirect_item_id.'?tab=task') );

        }

    }

    public function task_detail()
    {

        if( $this->input->is_ajax_request() )
        {

            $item_id = $this->input->post('item_id');
            $task_id = $this->input->post('task_id');
            $is_copy = $this->input->post('is_copy');

            $data['data'] = $this->task_manage_model->get_item_task( $item_id , $task_id );

            if( empty( $data['data'] ) )
            {
                $data['data'] = new stdClass();
                $data['data']->name = "";
                $data['data']->milestone = 0;
                $data['data']->repeat_every = '';
                $data['data']->priority = 0;
                $data['data']->assignees = null;
                $data['data']->followers = null;
                $data['data']->checklist_items = null;
                $data['data']->description = "";
                $data['data']->start_date = 0;
                $data['data']->due_date = 0;
                $data['data']->task_id = 0;
                $data['data']->task_status = 0;
                $data['data']->related = 'project';
                $data['data']->assign_project_owner = 0;
                $data['data']->task_completed_project_status = 0;
                $data['data']->task_created_project_status = 0;
                $data['data']->task_is_public = 0;
                $data['data']->task_is_billable = 0;
                $data['data']->task_visible_to_client = 0;

            }
            else
                $data['data']->task_id = $task_id;

            $data["item_id"]    = $item_id;

            $data["milestones"] = $this->task_manage_model->get_item_milestones( $item_id );

            $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();

            $data['task_status']        = $this->tasks_model->get_statuses();

            $this->load->model('projects_model');

            $data['project_status']     = $this->projects_model->get_project_statuses();

            $data['members'] = $this->staff_model->get();


            // task custom fields

            if( !empty( $is_copy ) )
            {

                $data["is_copy"]        = $is_copy;

                $data['data']->task_id  = 0;

                $data["perfex_items"]   = $this->task_manage_model->get_groups();

            }

            $task_content = $this->load->view('v_product_modal_task',$data,true);

            echo json_encode( [ "task_content" => $task_content , "data" => $data["data"] ] );

        }

    }

    public function delete_task( $item_id = 0 ,  $task_id = 0 )
    {

        $task_table = $this->task_manage_model->table_tasks;

        $this->db->where('id',$task_id)->where('group_id',$item_id)->delete($task_table);

        set_alert('success',_l('task_manage_successful'));

        redirect( admin_url('task_manage/manage/detail/'.$item_id.'?tab=task') );

    }

    /**
     * @note task milestone detail
     */
    public function milestone_detail()
    {

        if( $this->input->is_ajax_request() )
        {

            $item_id = $this->input->post('item_id');
            $milestone_id = $this->input->post('milestone_id');

            $data = $this->task_manage_model->get_item_milestone( $item_id , $milestone_id );

            if ( empty( $data ) )
            {
                $data = new stdClass();

                $data->milestone_name   = "";
                $data->milestone_order  = 3;
                $data->milestone_color  = $this->milestion_default_color;

            }

            echo json_encode( [ "data" => $data ] );
        }

    }

    /**
     * @note milestone saving the db
     */
    public function save_milestone()
    {

        if ($this->input->post())
        {


            $id         = $this->input->post('id');
            $item_id    = $this->input->post('group_id');

            $db_data    = $this->input->post();

            $milestone_table = $this->task_manage_model->table_milestones;

            if( !empty( $id ) )
            {
                $this->db->where('id',$id)->update( $milestone_table , $db_data );
            }
            else
            {
                $this->db->insert( $milestone_table , $db_data );
            }

            set_alert('success',_l('task_manage_successful'));

            redirect( admin_url('task_manage/manage/detail/'.$item_id.'?tab=milestone') );

        }



    }

    public function delete_milestone( $item_id = 0 ,  $milestone_id = 0 )
    {

        $milestone_table = $this->task_manage_model->table_milestones;

        $this->db->where('id',$milestone_id)->where('group_id',$item_id)->delete($milestone_table);

        set_alert('success',_l('task_manage_successful'));

        redirect( admin_url('task_manage/manage/detail/'.$item_id.'?tab=milestone') );

    }

    public function add_checklist()
    {

        if( $this->input->is_ajax_request() )
        {

            $this->db->set( 'description' , $this->input->post('checklist_name') )
                    ->insert(db_prefix().'tasks_checklist_templates');

            echo json_encode( [ 'success' => true ] );

        }

    }

    // Saving task create order
    public function save_task_group_order()
    {

        $item_id        = $this->input->post('item_id');
        $task_groups    = $this->input->post('task_groups');


        $task_manage_table = $this->task_manage_model->table_tasks;;

        $this->db->set('task_order',0)
                ->where('group_id',$item_id)
                ->update($task_manage_table);


        if( !empty( $task_groups ) )
        {

            $index = 1 ;

            foreach ( $task_groups as $task_group_id )
            {

                $tasks = $this->input->get('task_group_'.$task_group_id);

                if( !empty( $tasks ) )
                {
                    $this->db->set('task_order',$index)
                            ->where('group_id',$item_id)
                            ->where_in('id',$tasks)
                            ->update($task_manage_table);

                }

                $index++;

            }

        }

        echo json_encode( [ "status" => true ] );

    }


    /**
     * @note checking the project add/edit screen
     */
    public function check_project_page()
    {

        if( $this->input->is_ajax_request() )
        {

            $page_url = $_SERVER['HTTP_REFERER'];

            $page_url   = str_replace( site_url() , '' , $page_url );


            if( strpos( $page_url , 'projects/project' ) !== false )
            {

                $page_url   = str_replace( '/','',$page_url);
                $project_id = str_replace( 'adminprojectsproject','',$page_url);

                $project_id = task_manage_number_cast( $project_id );

                $selected = [];
                if( !empty( $project_id ) )
                {

                    $project_info = $this->db->select('task_manage_groups')->from(db_prefix().'projects')->where('id',$project_id)->get()->row();

                    if( !empty( $project_info->task_manage_groups ) )
                    {

                        $selected = json_decode( $project_info->task_manage_groups , 1 );

                    }

                }

                $task_manage_groups = $this->task_manage_model->get_groups( true );

                $content_html = render_select( 'task_manage_groups[]' , $task_manage_groups , [ 'id' , [ 'group_name' ] ] , 'task_manage_group_name' , $selected , [ 'multiple' => true ] );

                echo json_encode( [ 'content' => $content_html ] );

            }

        }

    }


    /**
     * Project Task milestones
     */
    public function project_task_diagrams()
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
                        $task_group_data[ $group_id ]['task_data']  = [];

                        $group_tasks = $this->task_manage_model->get_item_tasks( $group_id );

                        if( !empty( $group_tasks ) )
                        {

                            // found the current task order
                            foreach ( $group_tasks as $group_task )
                            {

                                if( !empty( $created_tasks[ $group_task->id ] ) )
                                {

                                    $group_task->project_status_id = $created_tasks[ $group_task->id ]->status;
                                    $group_task->project_status = format_task_status( $created_tasks[ $group_task->id ]->status , true );

                                    if( $process_current_order < $group_task->task_order )
                                        $process_current_order = $group_task->task_order;

                                }

                                $task_group_data[ $group_id ]['task_data'][ $group_task->task_order ][] = $group_task;
                                $task_group_data[ $group_id ]['current_group']  = $process_current_order;


                            }


                        }


                    }

                }

                $data['task_group_data'] = $task_group_data;

            }


            $diagram_content = $this->load->view('v_project_view_task' , $data ,true);

            echo json_encode( [ "content" => $diagram_content ] );

        }

    }


    /**
     *
     * @note database tables checking
     *
     */
    public function check_the_db()
    {


        $CI = &get_instance();


        if ( !$CI->db->table_exists( db_prefix() . 'task_manage_groups' ) )
        {

            $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_groups` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `group_name` varchar(255) DEFAULT NULL,
                    `status` tinyint(4) DEFAULT 1,
                    `added_staff` int(11) DEFAULT NULL,
                    `added_date` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }


        if ( !$CI->db->table_exists( db_prefix() . 'task_manage_tasks' ) )
        {

            $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_tasks` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `group_id` int(11) DEFAULT NULL,
                      `name` varchar(255) DEFAULT NULL,
                      `milestone` int(11) DEFAULT NULL,
                      `priority` int(11) DEFAULT NULL,
                      `repeat_every` varchar(50) DEFAULT NULL,
                      `assignees` varchar(100) DEFAULT NULL,
                      `followers` varchar(100) DEFAULT NULL,
                      `checklist_items` varchar(100) DEFAULT NULL,
                      `tags` varchar(255) DEFAULT NULL,
                      `description` text DEFAULT NULL,
                      `task_status` int(11) DEFAULT NULL,
                      `task_order` int(11) DEFAULT 0,
                      PRIMARY KEY (`id`),
                      KEY `group_id` (`group_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }


        if ( !$CI->db->table_exists( db_prefix() . 'task_manage_milestones' ) )
        {

            $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_milestones` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                      `group_id` int(11) DEFAULT NULL,
                      `milestone_name` varchar(255) DEFAULT NULL,
                      `milestone_order` int(11) DEFAULT 2,
                      `milestone_color` varchar(10) DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      KEY `group_id` (`group_id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }


        if ( !$CI->db->table_exists( db_prefix() . 'task_manage_custom_fields_values' ) )
        {

            $CI->db->query("
                    CREATE TABLE `".db_prefix()."task_manage_custom_fields_values` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `relid` int(11) NOT NULL,
                    `fieldid` int(11) NOT NULL,
                    `value` text NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `relid` (`relid`),
                    KEY `fieldid` (`fieldid`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");

        }


        if ( !$CI->db->field_exists('task_manage_groups', db_prefix() .'projects'))
        {
            $CI->db->query('ALTER TABLE `'.db_prefix().'projects`
                                ADD COLUMN `task_manage_groups` varchar(150) NULL AFTER `addedfrom`;');
        }


        if (!$CI->db->field_exists('start_date', db_prefix() .'task_manage_tasks'))
        {
            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                            ADD COLUMN `start_date` int NULL AFTER `description`,
                            ADD COLUMN `due_date` int NULL AFTER `start_date`;');
        }


        if (!$CI->db->field_exists('assign_project_owner', db_prefix() .'task_manage_tasks'))
        {
            $CI->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                ADD COLUMN `assign_project_owner` tinyint NULL DEFAULT 0 AFTER `task_order`;');
        }


        if ( !$this->db->field_exists('task_manage_task_id', db_prefix() .'tasks'))
        {

            $this->db->query('ALTER TABLE `'.db_prefix().'tasks`
                                ADD COLUMN `task_manage_task_id` int NULL AFTER `deadline_notified`;');

        }

        /**
         * @Version 1.1.2
         */
        if( !$this->db->field_exists('task_created_project_status', db_prefix() .'task_manage_tasks') )
        {

            $this->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                            ADD COLUMN `task_created_project_status` int NULL DEFAULT 0 AFTER `assign_project_owner`,
                            ADD COLUMN `task_completed_project_status` int NULL DEFAULT 0 AFTER `task_created_project_status`;');

        }


        if( !$this->db->field_exists('related', db_prefix() .'task_manage_tasks') )
        {

            $this->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                    ADD COLUMN `related` varchar(20) NULL DEFAULT \'project\' AFTER `task_completed_project_status`;');

        }


        /**
         * @Version 1.1.5
         */
        if( !$this->db->field_exists('task_is_public', db_prefix() .'task_manage_tasks') )
        {

            $this->db->query('ALTER TABLE `'.db_prefix().'task_manage_tasks`
                                ADD COLUMN `task_is_public` tinyint NULL DEFAULT 0 AFTER `related`,
                                ADD COLUMN `task_is_billable` tinyint NULL DEFAULT 0 AFTER `task_is_public`,
                                ADD COLUMN `task_visible_to_client` tinyint NULL DEFAULT 0 AFTER `task_is_billable`;');

        }






    }

}
