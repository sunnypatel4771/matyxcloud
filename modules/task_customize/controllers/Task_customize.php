<?php

use Dom\XPath;

defined('BASEPATH') or exit('No direct script access allowed');

class Task_customize extends AdminController
{

    public function __construct()
    {
        parent::__construct(); // Call the parent constructor
        $this->load->model('projects_model');
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_tasks');
        $total_deleted = 0;

        if ($this->input->post()) {
            $status    = $this->input->post('status');
            $ids       = $this->input->post('ids');
            $tags      = $this->input->post('tags');
            $assignees = $this->input->post('assignees');
            $milestone = $this->input->post('milestone');
            $priority  = $this->input->post('priority');
            $billable  = $this->input->post('billable');
            $startdate  = $this->input->post('startdate');
            $duedate  = $this->input->post('duedate');
            $is_admin  = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if (staff_can('delete',  'tasks')) {
                            if ($this->tasks_model->delete_task($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {
                            if (
                                $this->tasks_model->is_task_creator(get_staff_user_id(), $id)
                                || $is_admin
                                || $this->tasks_model->is_task_assignee(get_staff_user_id(), $id)
                            ) {
                                $this->tasks_model->mark_as($status, $id);
                            }
                        }
                        if ($priority || $milestone || ($billable === 'billable' || $billable === 'not_billable')) {
                            $update = [];

                            if ($priority) {
                                $update['priority'] = $priority;
                            }

                            if ($milestone) {
                                $update['milestone'] = $milestone;
                            }

                            if ($billable) {
                                $update['billable'] = $billable === 'billable' ? 1 : 0;
                            }

                            $this->db->where('id', $id);
                            $this->db->update(db_prefix() . 'tasks', $update);
                        }
                        if ($startdate) {
                            $this->db->where('id', $id);
                            $this->db->update(db_prefix() . 'tasks', ['startdate' => to_sql_date($startdate)]);
                        }
                        if ($duedate) {
                            $this->db->where('id', $id);
                            $this->db->update(db_prefix() . 'tasks', ['duedate' => to_sql_date($duedate)]);
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'task');
                        }
                        if ($assignees) {
                            $notifiedUsers = [];
                            foreach ($assignees as $user_id) {
                                if (!$this->tasks_model->is_task_assignee($user_id, $id)) {
                                    $this->db->select('rel_type,rel_id');
                                    $this->db->where('id', $id);
                                    $task = $this->db->get(db_prefix() . 'tasks')->row();
                                    if ($task->rel_type == 'project') {
                                        // User is we are trying to assign the task is not project member
                                        if (total_rows(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]) == 0) {
                                            $this->db->insert(db_prefix() . 'project_members', ['project_id' => $task->rel_id, 'staff_id' => $user_id]);
                                        }
                                    }
                                    $this->db->insert(db_prefix() . 'task_assigned', [
                                        'staffid'       => $user_id,
                                        'taskid'        => $id,
                                        'assigned_from' => get_staff_user_id(),
                                    ]);
                                    if ($user_id != get_staff_user_id()) {
                                        $notification_data = [
                                            'description' => 'not_task_assigned_to_you',
                                            'touserid'    => $user_id,
                                            'link'        => '#taskid=' . $id,
                                        ];

                                        $notification_data['additional_data'] = serialize([
                                            get_task_subject_by_id($id),
                                        ]);
                                        if (add_notification($notification_data)) {
                                            array_push($notifiedUsers, $user_id);
                                        }
                                    }
                                }
                            }
                            pusher_trigger_notification($notifiedUsers);
                        }
                    }
                }
            }
            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tasks_deleted', $total_deleted));
            }
        }
    }

    public function update_custom_field_value()
    {
        $post  = $_POST;
        if (!empty($post)) {
            $value = isset($post['val']) ? $post['val'] : '';
            $task_id = isset($post['task_id']) ? $post['task_id'] : '';
            $field_id = isset($post['field_id']) ? $post['field_id'] : '';
            if ($task_id != '' && is_numeric($task_id)) {
                update_custom_field_value($task_id, $value, $field_id);
                exit;
            }
        }
    }


    public function add_comments()
    {
        $data = $this->input->post();
        if (!empty($data)) {
            $data['content'] = html_purify($this->input->post('comment', false));
            if ($data['content'] == '') {
                echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
                return;
            }

            if ($this->tasks_model->add_task_comment($data)) {
                echo json_encode(array('status' => true, 'message' => "Comment Added Successfully"));
            } else {
                echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
            }
        } else {
            echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
        }
    }


    //get_task_comments function
    public function get_task_comments()
    {
        $task_id = $this->input->post('task_id');

        if ($task_id != '') {
            $tasks_where = [];

            if (staff_cant('view', 'tasks')) {
                $tasks_where = get_tasks_where_string(false);
            }

            $task = $this->tasks_model->get($task_id, $tasks_where);
            $comments_html = '';
            if ($task->comments) {
                $comments = $task->comments;
                $len                        = count($task->comments);
                $i                          = 0;
                $comments_html = '<div id="task-comments" class="mtop10">';
                if ($len > 2) {
                    $comments_html .= '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">';
                }
                $comments = '';


                foreach ($task->comments as $comment) {
                    $comments .= '<div id="comment_' . $comment['id'] . '" data-commentid="' . $comment['id'] . '" data-task-attachment-id="' . $comment['file_id'] . '" class="tc-content tw-group/comment task-comment' . (strtotime($comment['dateadded']) >= strtotime('-16 hours') ? ' highlight-bg' : '') . '" style="background: aliceblue;padding: 8px;margin: 10px;">';
                    $comments .= '<a data-task-comment-href-id="' . $comment['id'] . '" href="' . admin_url('tasks/view/' . $task->id) . '#comment_' . $comment['id'] . '" class="task-date-as-comment-id"><span class="tw-text-sm"><span class="text-has-action inline-block" data-toggle="tooltip" data-title="' . e(_dt($comment['dateadded'])) . '">' . e(time_ago($comment['dateadded'])) . '</span></span></a>';
                    if ($comment['staffid'] != 0) {
                        $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . staff_profile_image($comment['staffid'], [
                            'staff-profile-image-small',
                            'media-object img-circle pull-left mright10',
                        ]) . '</a>';
                    } elseif ($comment['contact_id'] != 0) {
                        $comments .= '<img src="' . e(contact_profile_image_url($comment['contact_id'])) . '" class="client-profile-image-small media-object img-circle pull-left mright10">';
                    }
                    // if ($comment['staffid'] == get_staff_user_id() || is_admin()) {
                    //     $comment_added = strtotime($comment['dateadded']);
                    //     $minus_1_hour  = strtotime('-1 hours');
                    //     if (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour) || is_admin()) {
                    //         $comments .= '<span class="pull-right tw-mx-2.5 tw-opacity-0 group-hover/comment:tw-opacity-100"><a href="#" onclick="remove_task_comment(' . $comment['id'] . '); return false;" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"><i class="fa fa-trash-can"></i></span></a>';
                    //         $comments .= '<span class="pull-right tw-opacity-0 group-hover/comment:tw-opacity-100"><a href="#" onclick="edit_task_comment(' . $comment['id'] . '); return false;" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"><i class="fa-regular fa-pen-to-square"></i></span></a>';
                    //     }
                    // }

                    $comments .= '<div class="media-body comment-wrapper">';
                    $comments .= '<div class="mleft40">';

                    if ($comment['staffid'] != 0) {
                        $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . e($comment['staff_full_name']) . '</a> <br />';
                    } elseif ($comment['contact_id'] != 0) {
                        $comments .= '<span class="label label-info mtop5 mbot5 inline-block">' . _l('is_customer_indicator') . '</span><br /><a href="' . admin_url('clients/client/' . get_user_id_by_contact_id($comment['contact_id']) . '?contactid=' . $comment['contact_id']) . '" class="pull-left" target="_blank">' . e(get_contact_full_name($comment['contact_id'])) . '</a> <br />';
                    }

                    $comments .= '<div data-edit-comment="' . $comment['id'] . '" class="hide edit-task-comment"><textarea rows="5" id="task_comment_' . $comment['id'] . '" class="ays-ignore form-control">' . str_replace('[task_attachment]', '', $comment['content']) . '</textarea>
                  <div class="clearfix mtop20"></div>
                  <button type="button" class="btn btn-primary pull-right" onclick="save_edited_comment(' . $comment['id'] . ',' . $task->id . ')">' . _l('submit') . '</button>
                  <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_comment(' . $comment['id'] . ')">' . _l('cancel') . '</button>
                  </div>';

                    $comments .= '<div class="comment-content mtop10">' . app_happy_text(check_for_links($comment['content'])) . '</div>';
                    $comments .= '</div>';
                    if ($i >= 0 && $i != $len - 1) {
                        $comments .= '<hr class="task-info-separator" />';
                    }
                    $comments .= '</div>';
                    $comments .= '</div>';
                    $i++;
                }

                $comments_html .= $comments;
                if ($len > 3) {
                    $comments_html .= '</div>'; // Close the scroll wrapper
                }
                $comments_html .= '</div>';
            } else {
                $comments_html = '<div id="task-comments" class="mtop10">';
                $comments_html .= '<div class="tc-content tw-group/comment task-comment">';
                $comments_html .= '<div class="media-body comment-wrapper">';
                $comments_html .= '<div class="mleft40">';
                $comments_html .= '<div class="comment-content mtop10">No Comments Found</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
            }
            echo json_encode(array('status' => true, 'comments' => $comments_html));
        } else {
            echo json_encode(array('status' => false, 'message' => "Comments Not Found"));
        }
    }

    public function recurring_tasks()
    {
        //load view file
        $data['tasks_table'] = App_table::find('tasks');
        $data['bulk_actions'] = true;
        $this->load->view('recurring_tasks', $data);
    }

    public function task_customize_task_status_changed($status, $task_id)
    {
        $CI = &get_instance();
        // $status = isset($data['status']) ? $data['status'] : '';
        // $task_id = isset($data['task_id']) ? $data['task_id'] : '';
        // echo $status;die;
        if ($status != '' && $task_id != '') {

            if ($status == Tasks_model::STATUS_COMPLETE) {
                $CI->db->select('id,addedfrom,recurring_type,repeat_every,last_recurring_date,startdate,duedate,recurring ,is_recurring_from');
                $CI->db->where('id', $task_id);
                $recurring_tasks = $CI->db->get(db_prefix() . 'tasks')->result_array();
                if (!empty($recurring_tasks)) {
                    foreach ($recurring_tasks as $task) {
                        if ((isset($task['is_recurring_from']) && $task['is_recurring_from'] != '') || (isset($task['recurring']) && $task['recurring'] == 1)) {
                            $last_recurring_date = $task['last_recurring_date'];
                            $type                = $task['recurring_type'];
                            $repeat_every        = $task['repeat_every'];
                            $task_date           = $task['startdate'];

                            if (isset($task['is_recurring_from']) && $task['is_recurring_from'] != NULL) {
                                $task_setail = get_task_detail($task['is_recurring_from']);
                                $last_recurring_task_id = isset($task_setail[0]['id']) ? $task_setail[0]['id'] : '';
                                $last_recurring_date = isset($task_setail[0]['last_recurring_date']) ? $task_setail[0]['last_recurring_date'] : '';
                                $type                = isset($task_setail[0]['recurring_type']) ? $task_setail[0]['recurring_type'] : '';
                                $repeat_every        = isset($task_setail[0]['repeat_every']) ? $task_setail[0]['repeat_every'] : '';
                                $task_date           = isset($task_setail[0]['startdate']) ? $task_setail[0]['startdate'] : '';
                                if (isset($task_setail[0]['total_cycles']) && isset($task_setail[0]['cycles']) && $task_setail[0]['total_cycles'] == $task_setail[0]['cycles']) {
                                    continue;
                                }
                            }
                            if ($task['recurring'] == 1) {
                                if (isset($task[0]['total_cycles']) && isset($task[0]['cycles']) && $task[0]['total_cycles'] == $task[0]['cycles']) {
                                    continue;
                                }
                            }

                            $date = new DateTime(date('Y-m-d'));
                            // Check if is first recurring
                            if (!$last_recurring_date) {
                                $last_recurring_date = date('Y-m-d', strtotime($task_date));
                            } else {
                                $last_recurring_date = date('Y-m-d', strtotime($last_recurring_date));
                            }

                            $re_create_at = date('Y-m-d', strtotime('+' . $repeat_every . ' ' . strtoupper($type), strtotime($last_recurring_date)));

                            $task_id = $task['id'];
                            if (isset($last_recurring_task_id) && $last_recurring_task_id != '') {
                                $task_id = $last_recurring_task_id;
                            } else {
                                $task_id = $task['id'];
                            }
                            $copy_task_data['copy_task_followers']       = 'true';
                            $copy_task_data['copy_task_checklist_items'] = 'true';
                            $copy_task_data['copy_from']                 = $task_id;

                            $overwrite_params = [
                                'startdate'           => $re_create_at,
                                'status'              => ASSIGN_STATUS,
                                'recurring_type'      => null,
                                'repeat_every'        => 0,
                                'cycles'              => 0,
                                'recurring'           => 0,
                                'custom_recurring'    => 0,
                                'last_recurring_date' => null,
                                'is_recurring_from'   => $task_id,
                            ];

                            if (!empty($task['duedate'])) {
                                $dStart                      = new DateTime($task['startdate']);
                                $dEnd                        = new DateTime($task['duedate']);
                                $dDiff                       = $dStart->diff($dEnd);
                                $overwrite_params['duedate'] = date('Y-m-d', strtotime('+' . $dDiff->days . ' days', strtotime($re_create_at)));
                            }
                            $newTaskID = $CI->tasks_model->copy($copy_task_data, $overwrite_params);

                            if ($newTaskID) {
                                $task_id = $task['id'];
                                if (isset($last_recurring_task_id) && $last_recurring_task_id != '') {
                                    $task_id = $last_recurring_task_id;
                                } else {
                                    $task_id = $task['id'];
                                }
                                $CI->db->where('id', $task_id);
                                $CI->db->update(db_prefix() . 'tasks', [
                                    'last_recurring_date' => $re_create_at,
                                ]);

                                $CI->db->where('id', $task_id);
                                $CI->db->set('total_cycles', 'total_cycles+1', false);
                                $CI->db->update(db_prefix() . 'tasks');

                                $CI->db->where('taskid', $task_id);
                                $assigned = $CI->db->get(db_prefix() . 'task_assigned')->result_array();
                                foreach ($assigned as $assignee) {
                                    $assigneeId = $CI->tasks_model->add_task_assignees([
                                        'taskid'   => $newTaskID,
                                        'assignee' => $assignee['staffid'],
                                    ], true);

                                    if ($assigneeId) {
                                        $CI->db->where('id', $assigneeId);
                                        $CI->db->update(db_prefix() . 'task_assigned', ['assigned_from' => $task['addedfrom']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($status == ASSIGN_STATUS) {
                $CI->db->where('relid', $task_id);
                $CI->db->where('fieldid', WORK_PLANNED);
                $CI->db->where('fieldto', 'tasks');
                $CI->db->delete(db_prefix() . 'customfieldsvalues');
            }
        }
    }


    public function project_mark_as($status, $project_id)
    {
        $CI = &get_instance();
        $CI->db->where('id', $project_id);
        $CI->db->update(db_prefix() . 'projects', ['status' => $status]);
        echo json_encode(array('success' => true, 'message' => 'Project status updated successfully'));
    }

    public function project_change_custom_field_value($project_id, $custom_field_id, $value)
    {
        $CI = &get_instance();

        //- remove and apply space in value
        $value = str_replace('-', ' ', $value);

        // Check if custom field value exists
        $CI->db->where('relid', $project_id);
        $CI->db->where('fieldid', $custom_field_id);
        $CI->db->where('fieldto', 'projects');
        $exists = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();

        if ($exists) {
            // Update existing value
            $CI->db->where('relid', $project_id);
            $CI->db->where('fieldid', $custom_field_id);
            $CI->db->where('fieldto', 'projects');
            $CI->db->update(db_prefix() . 'customfieldsvalues', [
                'value' => $value
            ]);
        } else {
            // Insert new value if doesn't exist
            $CI->db->insert(db_prefix() . 'customfieldsvalues', [
                'relid' => $project_id,
                'fieldid' => $custom_field_id,
                'fieldto' => 'projects',
                'value' => $value
            ]);
        }

        echo json_encode(array('success' => true, 'message' => 'Project custom field updated successfully'));
    }

    public function project_change_custom_field_value_multiselect($project_id, $custom_field_id)
    {
        $CI = &get_instance();
        $value = $CI->input->post('value');
        $value = implode(',', $value);

        // Check if custom field value exists
        $CI->db->where('relid', $project_id);
        $CI->db->where('fieldid', $custom_field_id);
        $CI->db->where('fieldto', 'projects');
        $exists = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();

        if ($exists) {
            // Update existing value
            $CI->db->where('relid', $project_id);
            $CI->db->where('fieldid', $custom_field_id);
            $CI->db->where('fieldto', 'projects');
            $CI->db->update(db_prefix() . 'customfieldsvalues', [
                'value' => $value
            ]);
        } else {
            // Insert new value if doesn't exist
            $CI->db->insert(db_prefix() . 'customfieldsvalues', [
                'relid' => $project_id,
                'fieldid' => $custom_field_id,
                'fieldto' => 'projects',
                'value' => $value
            ]);
        }

        echo json_encode(array('success' => true, 'message' => 'Project custom field updated successfully'));
    }

    public function project_custom_fields()
    {
        $service = $this->input->get('service');
        $data['service'] = $service;
        $this->load->view('project_custom_fields', $data);
    }

    public function project_type()
    {
        $type = $this->input->get('type');
        $data['type'] = $type;
        $this->load->view('project_type', $data);
    }

    public function get_project_data($project_id)
    {
        $CI = &get_instance();
        $CI->db->where('project_id', $project_id);
        $project = $CI->db->get(db_prefix() . 'projects_notes')->row();
        echo json_encode($project);
    }

    public function add_project_comments()
    {
        $post_data = $this->input->post();
        $CI = &get_instance();
        if (!empty($post_data)) {

            $data['content'] = html_purify($this->input->post('comment', false));
            if ($data['content'] == '') {
                echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
                return;
            }
            $data['project_id'] = $this->input->post('projectid', false);
            $data['staffid'] = get_staff_user_id();
            $data['contact_id'] = 0;
            $data['dateadded'] = date('Y-m-d H:i:s');


            //insert in tblprojects_notes
            $CI->db->insert(db_prefix() . 'projects_notes_custome', [
                'content' => $data['content'],
                'project_id' => $data['project_id'],
                'staffid' => $data['staffid'],
                'contact_id' => $data['contact_id'],
                'dateadded' => $data['dateadded']
            ]);
            $insert_id = $CI->db->insert_id();
            if ($insert_id) {
                echo json_encode(array('status' => true, 'message' => "Comment Added Successfully"));
            } else {
                echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
            }
        } else {
            echo json_encode(array('status' => false, 'message' => "Comment Not Added"));
        }
    }


    //get_project_comments function
    public function get_project_comments()
    {
        $project_id = $this->input->post('project_id');

        if ($project_id != '') {
            $projects_where = [];

            // if (staff_cant('view', 'projects')) {
            //     $projects_where = get_projects_where_string(false);
            // }

            //mke query for project comments
            $project = $this->db->query('SELECT * FROM ' . db_prefix() . 'projects_notes_custome WHERE project_id = ' . $project_id . ' ORDER BY id DESC')->result_array();
            $comments_html = '';

            if (!empty($project)) {
                $comments = $project;
                $len                        = count($project);
                $i                          = 0;
                $comments_html = '<div id="project-comments" class="mtop10">';
                if ($len > 2) {
                    $comments_html .= '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">';
                }
                $comments = '';


                foreach ($project as $comment) {
                    $comments .= '<div id="comment_' . $comment['id'] . '" data-commentid="' . $comment['id'] . '" class="tc-content tw-group/comment project-comment' . (strtotime($comment['dateadded']) >= strtotime('-16 hours') ? ' highlight-bg' : '') . '" style="background: aliceblue;padding: 8px;margin: 10px;">';
                    $comments .= '<a data-project-comment-href-id="' . $comment['id'] . '" href="' . admin_url('projects/view/' . $project_id) . '#comment_' . $comment['id'] . '" class="project-date-as-comment-id"><span class="tw-text-sm"><span class="text-has-action inline-block" data-toggle="tooltip" data-title="' . e(_dt($comment['dateadded'])) . '">' . e(time_ago($comment['dateadded'])) . '</span></span></a>';
                    if ($comment['staffid'] != 0) {
                        $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . staff_profile_image($comment['staffid'], [
                            'staff-profile-image-small',
                            'media-object img-circle pull-left mright10',
                        ]) . '</a>';
                    } elseif ($comment['contact_id'] != 0) {
                        $comments .= '<img src="' . e(contact_profile_image_url($comment['contact_id'])) . '" class="client-profile-image-small media-object img-circle pull-left mright10">';
                    }


                    $comments .= '<div class="media-body comment-wrapper">';
                    $comments .= '<div class="mleft40">';

                    if ($comment['staffid'] != 0) {
                        $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . get_staff_full_name($comment['staffid']) . '</a> <br />';
                    } elseif ($comment['contact_id'] != 0) {
                        $comments .= '<span class="label label-info mtop5 mbot5 inline-block">' . _l('is_customer_indicator') . '</span><br /><a href="' . admin_url('clients/client/' . get_user_id_by_contact_id($comment['contact_id']) . '?contactid=' . $comment['contact_id']) . '" class="pull-left" target="_blank">' . e(get_contact_full_name($comment['contact_id'])) . '</a> <br />';
                    }

                    $comments .= '<div data-edit-comment="' . $comment['id'] . '" class="hide edit-project-comment"><textarea rows="5" id="project_comment_' . $comment['id'] . '" class="ays-ignore form-control">' . str_replace('[project_attachment]', '', $comment['content']) . '</textarea>
                  <div class="clearfix mtop20"></div>
                  <button type="button" class="btn btn-primary pull-right" onclick="save_edited_comment(' . $comment['id'] . ',' . $project_id . ')">' . _l('submit') . '</button>
                  <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_comment(' . $comment['id'] . ')">' . _l('cancel') . '</button>
                  </div>';

                    $comments .= '<div class="comment-content mtop10">' . app_happy_text(check_for_links($comment['content'])) . '</div>';
                    $comments .= '</div>';
                    if ($i >= 0 && $i != $len - 1) {
                        $comments .= '<hr class="project-info-separator" />';
                    }
                    $comments .= '</div>';
                    $comments .= '</div>';
                    $i++;
                }

                $comments_html .= $comments;
                if ($len > 3) {
                    $comments_html .= '</div>'; // Close the scroll wrapper
                }
                $comments_html .= '</div>';
            } else {
                $comments_html = '<div id="project-comments" class="mtop10">';
                $comments_html .= '<div class="tc-content tw-group/comment project-comment">';
                $comments_html .= '<div class="media-body comment-wrapper">';
                $comments_html .= '<div class="mleft40">';
                $comments_html .= '<div class="comment-content mtop10">No Comments Found</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
                $comments_html .= '</div>';
            }
            echo json_encode(array('status' => true, 'comments' => $comments_html));
        } else {
            echo json_encode(array('status' => false, 'message' => "Comments Not Found"));
        }
    }


    public function update_is_poked()
    {
        $task_id = $this->input->post('task_id');
        $is_poked = $this->input->post('is_poked');

        $CI = &get_instance();
        $CI->db->where('id', $task_id);
        $CI->db->update(db_prefix() . 'tasks', ['is_poked' => $is_poked]);
    }


    // get_project_details
    public function get_project_details($project_id)
    {
        $CI = &get_instance();
        $project = $CI->db->where('id', $project_id)->get(db_prefix() . 'projects')->row();
        echo json_encode($project);
    }

    public function get_customer_details($customer_id)
    {
        $CI = &get_instance();
        $customer = $CI->db->where('userid', $customer_id)->get(db_prefix() . 'clients')->row();
        echo json_encode($customer);
    }

    public function get_contract_details($contract_id)
    {
        $CI = &get_instance();
        $contract = $CI->db->where('id', $contract_id)->get(db_prefix() . 'contracts')->row();
        echo json_encode($contract);
    }

    public function toggle_project_timer()
    {
        $project_id = $this->input->post('project_id');


        $this->db->where('project_id', $project_id);
        $this->db->where('pause_time', null);
        $active = $this->db->get(db_prefix() . 'project_timer')->row();

        $status = 0;
        if ($active) {
            // Pause it
            $this->db->where('id', $active->id);
            $this->db->update(db_prefix() . 'project_timer', ['pause_time' => date('Y-m-d H:i:s')]);
            $message = 'Project Paused';
            $status = 1;
        } else {
            // Start it
            $this->db->insert(db_prefix() . 'project_timer', [
                'project_id' => $project_id,
                'start_time' => date('Y-m-d H:i:s')
            ]);
            $message = 'Project Started';
            $status = 1;
        }
        echo json_encode(['message' => $message, 'status' => $status]);
    }


    public function project_change_custom_notes_field_value($project_id, $custom_field_id)
    {
        $CI = &get_instance();

        $value = $CI->input->post('value');
        //- remove and apply space in value


        // Check if custom field value exists
        $CI->db->where('relid', $project_id);
        $CI->db->where('fieldid', $custom_field_id);
        $CI->db->where('fieldto', 'projects');
        $exists = $CI->db->get(db_prefix() . 'customfieldsvalues')->row();

        if ($exists) {
            // Update existing value
            $CI->db->where('relid', $project_id);
            $CI->db->where('fieldid', $custom_field_id);
            $CI->db->where('fieldto', 'projects');
            $CI->db->update(db_prefix() . 'customfieldsvalues', [
                'value' => $value
            ]);
        } else {
            // Insert new value if doesn't exist
            $CI->db->insert(db_prefix() . 'customfieldsvalues', [
                'relid' => $project_id,
                'fieldid' => $custom_field_id,
                'fieldto' => 'projects',
                'value' => $value
            ]);
        }

        echo json_encode(array('success' => true, 'message' => 'Project custom field updated successfully'));
    }


    //view_active_days
    public function view_active_days()
    {
        $project_id = $this->input->post('project_id');
        $CI = &get_instance();
        $project = $CI->db->where('project_id', $project_id)->get(db_prefix() . 'project_timer')->result_array();
      
        $table_data = '';
        foreach ($project as $timer) {
            $table_data .= '<tr>';
            $table_data .= '<td>' . $timer['start_time'] . '</td>';
            $table_data .= '<td>' . $timer['pause_time'] . '</td>';
            $table_data .= '<td class="text-right">
                <a href="javascript:void(0);" class="text-success" onclick="edit_custome_project_timer(' . $timer['id'] . ',' . $timer['project_id'] . '); return false;"><i class="fa fa-pencil"></i></a>
                <a href="javascript:void(0);" class="text-danger" onclick="delete_custome_project_timer(' . $timer['id'] . ',' . $timer['project_id'] . '); return false;"><i class="fa fa-trash"></i></a>
            </td>';
            $table_data .= '</tr>';
        }
        $response = [
            'table_data' => $table_data,
            'day_count' => get_active_days($project_id),
             'status' => true
        ];
        echo json_encode($response);
    }

    //edit_custome_project_timer
    public function save_custome_project_timer()
    {
        $CI = &get_instance();
        $timer_id = $this->input->post('timer_id');
        $date = DateTime::createFromFormat('m-d-Y h:i A', $this->input->post('start_time'));
        $start_time = $date->format('Y-m-d H:i:s');
        $date = DateTime::createFromFormat('m-d-Y h:i A', $this->input->post('pause_time'));
        $pause_time = $date->format('Y-m-d H:i:s');
        $project_id = $this->input->post('project_id');

        //check start time not small that pause time
        if($start_time > $pause_time){
            echo json_encode(array('success' => false, 'message' => 'Start time should be less than pause time'));
            return;
        }

        //check that alredy same time in that project 
        $CI->db->where('project_id', $project_id);
        $CI->db->where('start_time <', $pause_time);
        $CI->db->where('pause_time >', $start_time);
        $exists = $CI->db->get(db_prefix() . 'project_timer')->row();
        if($exists){
            echo json_encode(array('success' => false, 'message' => 'Time slot already exists'));
            return;
        }


        if($timer_id > 0){
            $CI->db->where('id', $timer_id);
            $CI->db->update(db_prefix() . 'project_timer', [
                'start_time' => $start_time,
                'pause_time' => $pause_time
            ]);
        } else{
            $CI->db->insert(db_prefix() . 'project_timer', [
                'project_id' => $project_id,
                'start_time' => $start_time,
                'pause_time' => $pause_time
            ]);
        }

        echo json_encode(array('success' => true, 'message' => 'Project timer updated successfully'));
    }

    //get_custome_project_timer
    public function get_custome_project_timer()
    {
        $CI = &get_instance();
        $timer_id = $this->input->post('timer_id');
        $CI->db->where('id', $timer_id);
        $timer = $CI->db->get(db_prefix() . 'project_timer')->row();
        if($timer){
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $timer->start_time);
            $start_time = $date->format('m-d-Y h:i A');
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $timer->pause_time);
            $pause_time = $date->format('m-d-Y h:i A');
            $response = [
                'timer' => $timer,
                'start_time' => $start_time,
                'pause_time' => $pause_time,
                'status' => true
            ];
        } else {
            $response = [
                'status' => false
            ];
        }
        echo json_encode($response);
    }


    // delete_custome_project_timer
    public function delete_custome_project_timer()
    {
        $CI = &get_instance();
        $timer_id = $this->input->post('timer_id');
        $CI->db->where('id', $timer_id);
        $CI->db->delete(db_prefix() . 'project_timer');
        echo json_encode(array('status' => true, 'message' => 'Project timer deleted successfully'));
    }
}
