<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo form_hidden('project_id', $project->id) ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <div class="row">
                        <div class="col-md-7 project-heading">
                            <div class="tw-flex tw-flex-wrap tw-items-center">
                                <h3 class="hide project-name"><?php echo e($project->name); ?></h3>
                                <div id="project_view_name" class="tw-mr-3 tw-max-w-[350px]">
                                    <div class="tw-w-full">
                                        <select class="selectpicker" id="project_top" data-width="100%"
                                            <?php if (count($other_projects) > 6) { ?> data-live-search="true"
                                            <?php } ?>>
                                            <option value="<?php echo e($project->id); ?>" selected
                                                data-content="<?php echo e($project->name); ?> - <small><?php echo e($project->client_data->company); ?></small>">
                                                <?php echo e($project->client_data->company); ?>
                                                <?php echo e($project->name); ?>
                                            </option>
                                            <?php foreach ($other_projects as $op) { ?>
                                                <option value="<?php echo e($op['id']); ?>"
                                                    data-subtext="<?php echo e($op['company']); ?>">#<?php echo e($op['id']); ?> -
                                                    <?php echo e($op['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="visible-xs">
                                    <div class="clearfix"></div>
                                </div>

                                <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex">
                                    <div class="tw-flex -tw-space-x-1">
                                        <?php foreach ($members as $member) { ?>
                                            <span class="tw-group tw-relative"
                                                data-title="<?php echo e(get_staff_full_name($member['staff_id']) . (staff_can('create',  'projects') || $member['staff_id'] == get_staff_user_id() ? ' - ' . _l('total_logged_hours_by_staff') . ': ' . e(seconds_to_time_format($member['total_logged_time'])) : '')); ?>"
                                                data-toggle="tooltip">
                                                <?php if (staff_can('edit',  'projects')) { ?>
                                                    <a href="<?php echo admin_url('projects/remove_team_member/' . $project->id . '/' . $member['staff_id']); ?>"
                                                        class="_delete group-hover:tw-inline-flex tw-hidden tw-rounded-full tw-absolute tw-items-center tw-justify-center tw-bg-neutral-300/50 tw-h-7 tw-w-7 tw-cursor-pointer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="tw-w-4 tw-h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </a>
                                                <?php } ?>
                                                <?php echo staff_profile_image($member['staff_id'], ['tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white', '']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <a href="#" data-target="#add-edit-members" data-toggle="modal"
                                        class="tw-mt-1.5 rtl:tw-mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </div>
                                <?php
                                //make dropdown for status

                                if (staff_can('edit', 'projects')) {
                                    $outputStatus = '<div class="dropdown inline-block table-export-exclude">';
                                    $outputStatus .= '<a href="#" class="dropdown-toggle label tw-flex tw-items-center tw-gap-1 tw-flex-nowrap hover:tw-opacity-80 tw-align-middle" style="color:' . $project_status['color'] . ';border:1px solid ' . adjust_hex_brightness($project_status['color'], 0.4) . ';background: ' . adjust_hex_brightness($project_status['color'], 0.04) . ';" task-status-table="' . e($project->status) . '" id="projectStatus-' . $project->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    $outputStatus .= e($project_status['name']);
                                    $outputStatus .= '<i class="chevron tw-shrink-0"></i>';
                                    $outputStatus .= '</a>';

                                    $outputStatus .= '<ul class="dropdown-menu" aria-labelledby="ProjectStatus-' . $project->id . '">';
                                    foreach ($statuses as $status) {
                                        $outputStatus .= '<li><a href="#" onclick="project_mark_as_view(' . $status['id'] . ', ' . $project->id . ', this); return false;">' . _l('project_mark_as', $status['name']) . '</a></li>';
                                    }
                                    $outputStatus .= '</ul>';
                                    $outputStatus .= '</div>';

                                    echo $outputStatus;
                                } else {
                                    echo '<span class="tw-ml-1 project_status tw-inline-block label project-status-' . $project->status . '" style="color:' . $project_status['color'] . ';border:1px solid ' . adjust_hex_brightness($project_status['color'], 0.4) . ';background: ' . adjust_hex_brightness($project_status['color'], 0.04) . ';">' . e($project_status['name']) . '</span>';
                                }


                                ?>

                                <!-- //add chat icon with modal open  -->
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#project-comment-modal"
                                    class="tw-ml-2 tw-text-neutral-500 hover:tw-text-neutral-700">
                                    <i class="fa fa-comment"></i>
                                </a>

                                <div class="tw-ml-2 play_pause_section">
                                <?php
                                $this->db->where('project_id', $project->id);
                                $this->db->where('pause_time', null);
                                $active_timer = $this->db->get('tblproject_timer')->row();

                                if ($project->status != '9') {
                                    if ($active_timer) {
                                        // Timer is running — show pause icon
                                        echo '<a href="#" class="btn btn-warning" onclick="toggleProjectTimer(' . $project->id . ')">
                                               <i class="fa fa-pause"></i>
                                               </a>';
                                    } else {
                                        // Timer is paused or not started — show play icon
                                        echo '<a href="#" class="btn btn-success" onclick="toggleProjectTimer(' . $project->id . ')">
                                               <i class="fa fa-play"></i>
                                               </a>';
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-right tw-space-x-1">
                            <?php if (staff_can('create',  'tasks')) { ?>
                                <a href="#"
                                    onclick="new_task_from_relation(undefined,'project',<?php echo e($project->id); ?>); return false;"
                                    class="btn btn-primary">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('new_task'); ?>
                                </a>
                            <?php } ?>
                            <?php
                            $invoice_func = 'pre_invoice_project';
                            ?>
                            <?php if (staff_can('create',  'invoices')) { ?>
                                <a href="#"
                                    onclick="<?php echo e($invoice_func); ?>(<?php echo e($project->id); ?>); return false;"
                                    class="invoice-project btn btn-primary<?php if ($project->client_data->active == 0) {
                                                                                echo ' disabled';
                                                                            } ?>">
                                    <i class="fa-solid fa-file-invoice tw-mr-1"></i>
                                    <?php echo _l('invoice_project'); ?>
                                </a>
                            <?php } ?>
                            <?php
                            $project_pin_tooltip = _l('pin_project');
                            if (total_rows(db_prefix() . 'pinned_projects', ['staff_id' => get_staff_user_id(), 'project_id' => $project->id]) > 0) {
                                $project_pin_tooltip = _l('unpin_project');
                            }
                            ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <?php echo _l('more'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                                    <li>
                                        <a href="<?php echo admin_url('projects/pin_action/' . $project->id); ?>">
                                            <?php echo e($project_pin_tooltip); ?>
                                        </a>
                                    </li>
                                    <?php if (staff_can('edit',  'projects')) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('projects/project/' . $project->id); ?>">
                                                <?php echo _l('edit_project'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (staff_can('create',  'projects')) { ?>
                                        <li>
                                            <a href="#" onclick="copy_project(); return false;">
                                                <?php echo _l('copy_project'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (staff_can('create',  'projects') || staff_can('edit',  'projects')) { ?>
                                        <li class="divider"></li>
                                        <?php foreach ($statuses as $status) {
                                            if ($status['id'] == $project->status) {
                                                continue;
                                            } ?>
                                            <li>
                                                <a href="#" data-name="<?php echo _l('project_status_' . $status['id']); ?>"
                                                    onclick="project_mark_as_modal(<?php echo e($status['id']); ?>,<?php echo e($project->id); ?>, this); return false;"><?php echo e(_l('project_mark_as', $status['name'])); ?></a>
                                            </li>
                                        <?php
                                        } ?>
                                    <?php } ?>
                                    <li class="divider"></li>
                                    <?php if (staff_can('create',  'projects')) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('projects/export_project_data/' . $project->id); ?>"
                                                target="_blank"><?php echo _l('export_project_data'); ?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if (is_admin()) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('projects/view_project_as_client/' . $project->id . '/' . $project->clientid); ?>"
                                                target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                                        </li>
                                    <?php } ?>
                                    <?php if (staff_can('delete',  'projects')) { ?>
                                        <li>
                                            <a href="<?php echo admin_url('projects/delete/' . $project->id); ?>"
                                                class="_delete">
                                                <span class="text-danger"><?php echo _l('delete_project'); ?></span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="project-menu-panel tw-my-5">
                    <?php hooks()->do_action('before_render_project_view', $project->id); ?>
                    <?php $this->load->view('admin/projects/project_tabs'); ?>
                </div>
                <?php
                if ((staff_can('create',  'projects') || staff_can('edit',  'projects'))
                    && $project->status == 1
                    && $this->projects_model->timers_started_for_project($project->id)
                    && $tab['slug'] != 'project_milestones'
                ) {
                ?>
                    <div class="alert alert-warning project-no-started-timers-found mbot15">
                        <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
                    </div>
                <?php
                } ?>
                <?php
                if (
                    $project->deadline && date('Y-m-d') > $project->deadline
                    && $project->status == 2
                    && $tab['slug'] != 'project_milestones'
                ) {
                ?>
                    <div class="alert alert-warning bold project-due-notice mbot15">
                        <?php echo _l('project_due_notice', floor((abs(time() - strtotime($project->deadline))) / (60 * 60 * 24))); ?>
                    </div>
                <?php
                } ?>
                <?php
                if (
                    !has_contact_permission('projects', get_primary_contact_user_id($project->clientid))
                    && total_rows(db_prefix() . 'contacts', ['userid' => $project->clientid]) > 0
                    && $tab['slug'] != 'project_milestones'
                ) {
                ?>
                    <div class="alert alert-warning project-permissions-warning mbot15">
                        <?php echo _l('project_customer_permission_warning'); ?>
                    </div>
                <?php
                } ?>

                <?php $this->load->view(($tab ? $tab['view'] : 'admin/projects/project_overview')); ?>

            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/add_edit_members/' . $project->id)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
                $selected = [];
                foreach ($members as $member) {
                    array_push($selected, $member['staff_id']);
                }
                echo render_select('project_members[]', $staff, ['staffid', ['firstname', 'lastname']], 'project_members', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary" autocomplete="off"
                    data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade" id="project-comment-modal" tabindex="-1" role="dialog" aria-labelledby="project-comment-modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open(admin_url('task_customize/add_project_comments'), ['id' => 'project-comment-form']); ?>
            <div class="modal-header">
                <h4 class="modal-title">Add Comments</h4>
            </div>
            <div class="modal-body">





                <div class="form-group">
                    <textarea name="comment" id="comment" class="form-control" rows="5"></textarea>
                </div>
                <input type="hidden" name="projectid" id="project_id_comment">
                <!-- add section for project comment history  -->
                <div class="project-comment-history">
                    <div class="project-comment-history-header">
                        <h4>Comments History</h4>
                    </div>
                    <div class="project-comment-history-body">

                    </div>
                </div>
                <!-- end project comment history section  -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- /.modal -->
<?php if (isset($discussion)) {
    echo form_hidden('discussion_id', $discussion->id);
    echo form_hidden('discussion_user_profile_image_url', $discussion_user_profile_image_url);
    echo form_hidden('current_user_is_admin', $current_user_is_admin);
}
echo form_hidden('project_percent', $percent);
?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php $this->load->view('admin/projects/_mark_tasks_finished'); ?>
<?php init_tail(); ?>
<!-- For invoices table -->
<script>
    taskid = '<?php echo $this->input->get('taskid'); ?>';
</script>
<script>
    init_editor("#comment");
    var gantt_data = {};
    <?php if (isset($gantt_data)) { ?>
        gantt_data = <?php echo json_encode($gantt_data); ?>;
    <?php } ?>
    var discussion_id = $('input[name="discussion_id"]').val();
    var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
    var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
    var project_id = $('input[name="project_id"]').val();
    if (typeof(discussion_id) != 'undefined') {
        discussion_comments('#discussion-comments', discussion_id, 'regular');
    }

    
    function toggleProjectTimer(project_id) {
            
            $.post(admin_url + 'task_customize/toggle_project_timer', {
                project_id: project_id
            }).done(function(response) {
                var res = JSON.parse(response);
                if (res.status == true) {
                    if (res.status == 1) {
                        alert_float('success', res.message);
                        // play_pause_section refresh
                        $('.play_pause_section').load(location.href + ' .play_pause_section');
                    } else {
                        alert_float('danger', res.message);
                    }
                }
            });
        }

    $(function() {





        var project_progress_color =
            '<?php echo hooks()->apply_filters('admin_project_progress_color', '#84c529'); ?>';
        var circle = $('.project-progress').circleProgress({
            fill: {
                gradient: [project_progress_color, project_progress_color]
            }
        }).on('circle-animation-progress', function(event, progress, stepValue) {
            $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
        });








        //set task id on modal open
        $('#project-comment-modal').on('show.bs.modal', function(event) {

            var project_id = <?php echo $project->id; ?>;

            // //get project comments
            $.post(admin_url + 'task_customize/get_project_comments', {
                project_id: project_id
            }).done(function(response) {
                var res = JSON.parse(response);
                if (res.status == true) {
                    var comments = res.comments;
                    $('.project-comment-history-body').html(comments);
                }
            });

            $('#project_id_comment').val(project_id);
        });

        //model hidden reset form
        $('#project-comment-modal').on('hidden.bs.modal', function() {
            $('#project-comment-form').trigger("reset");
            $('#project-comment-form button[type="submit"]').prop('disabled', false);

        });

        //project-comment-form submit
        $('#project-comment-form').submit(function(event) {
            //save button make disabled
            $('#project-comment-form button[type="submit"]').prop('disabled', true);
            event.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();
            $.post(url, data).done(function(success) {
                var res = JSON.parse(success);
                if (res.status == true) {
                    alert_float('success', res.message);
                    //reload table
                    $('#project-comment-modal').modal('hide');
                } else {
                    //save button make enabled
                    $('#project-comment-form button[type="submit"]').prop('disabled', false);

                    alert_float('danger', res.message);
                }
            });
        });
    });

    function project_mark_as_view(status, project_id) {
        $.ajax({
            url: admin_url + 'projects/mark_as',
            type: 'POST',
            data: {
                status_id: status,
                project_id: project_id,
                notify_project_members_status_change: 1,
                mark_all_tasks_as_completed: 0,
                cancel_recurring_tasks: 'false',
                send_project_marked_as_finished_email_to_contacts: 0
            },
            success: function(response) {
                $("body").find(".dt-loader").remove();

                var response = JSON.parse(response);
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                    location.reload();
                }
            }
        });
    }




    function discussion_comments(selector, discussion_id, discussion_type) {
        var defaults = _get_jquery_comments_default_config(
            <?php echo json_encode(get_project_discussions_language_array()); ?>);
        var options = {
            // https://github.com/Viima/jquery-comments/pull/169
            wysiwyg_editor: {
                opts: {
                    enable: true,
                    is_html: true,
                    container_id: 'editor-container',
                    comment_index: 0,
                },
                init: function(textarea, content) {
                    var comment_index = textarea.data('comment_index');
                    var editorConfig = _simple_editor_config();
                    editorConfig.setup = function(ed) {
                        initializeTinyMceMentions(ed, function() {
                            return $.getJSON(admin_url + 'projects/get_staff_names_for_mentions/' + project_id)
                        })

                        textarea.data('wysiwyg_editor', ed);

                        ed.on('change', function() {
                            var value = ed.getContent();
                            if (value !== ed._lastChange) {
                                ed._lastChange = value;
                                textarea.trigger('change');
                            }
                        });

                        ed.on('keyup', function() {
                            var value = ed.getContent();
                            if (value !== ed._lastChange) {
                                ed._lastChange = value;
                                textarea.trigger('change');
                            }
                        });

                        ed.on('Focus', function(e) {
                            setTimeout(function() {
                                textarea.trigger('click');
                            }, 500)
                        });

                        ed.on('init', function() {
                            if (content) ed.setContent(content);
                        })
                    }

                    editorConfig.content_style = 'span.mention {\
                     background-color: #eeeeee;\
                     padding: 3px;\
                }';

                    var containerId = this.get_container_id(comment_index);
                    tinyMCE.remove('#' + containerId);

                    setTimeout(function() {
                        init_editor('#' + containerId, editorConfig)
                    }, 100)
                },
                get_container: function(textarea) {
                    if (!textarea.data('comment_index')) {
                        textarea.data('comment_index', ++this.opts.comment_index);
                    }

                    return $('<div/>', {
                        'id': this.get_container_id(this.opts.comment_index)
                    });
                },
                get_contents: function(editor) {
                    return editor.getContent();
                },
                on_post_comment: function(editor, evt) {
                    editor.setContent('');
                },
                get_container_id: function(comment_index) {
                    var container_id = this.opts.container_id;
                    if (comment_index) container_id = container_id + "-" + comment_index;
                    return container_id;
                }
            },
            currentUserIsAdmin: current_user_is_admin,
            getComments: function(success, error) {
                $.get(admin_url + 'projects/get_discussion_comments/' + discussion_id + '/' + discussion_type,
                    function(response) {
                        success(response);
                    }, 'json');
            },
            postComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/add_discussion_comment/' + discussion_id + '/' +
                        discussion_type,
                    data: commentJSON,
                    success: function(comment) {
                        comment = JSON.parse(comment);
                        success(comment)
                    },
                    error: error
                });
            },
            putComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/update_discussion_comment',
                    data: commentJSON,
                    success: function(comment) {
                        comment = JSON.parse(comment);
                        success(comment)
                    },
                    error: error
                });
            },
            deleteComment: function(commentJSON, success, error) {
                $.ajax({
                    type: 'post',
                    url: admin_url + 'projects/delete_discussion_comment/' + commentJSON.id,
                    success: success,
                    error: error
                });
            },
            uploadAttachments: function(commentArray, success, error) {
                var responses = 0;
                var successfulUploads = [];
                var serverResponded = function() {
                    responses++;
                    // Check if all requests have finished
                    if (responses == commentArray.length) {
                        // Case: all failed
                        if (successfulUploads.length == 0) {
                            error();
                            // Case: some succeeded
                        } else {
                            successfulUploads = JSON.parse(successfulUploads);
                            success(successfulUploads)
                        }
                    }
                }
                $(commentArray).each(function(index, commentJSON) {
                    // Create form data
                    var formData = new FormData();
                    if (commentJSON.file.size && commentJSON.file.size > app
                        .max_php_ini_upload_size_bytes) {
                        alert_float('danger', "<?php echo _l('file_exceeds_max_filesize'); ?>");
                        serverResponded();
                    } else {
                        $(Object.keys(commentJSON)).each(function(index, key) {
                            var value = commentJSON[key];
                            if (value) formData.append(key, value);
                        });

                        if (typeof(csrfData) !== 'undefined') {
                            formData.append(csrfData['token_name'], csrfData['hash']);
                        }
                        $.ajax({
                            url: admin_url + 'projects/add_discussion_comment/' + discussion_id +
                                '/' + discussion_type,
                            type: 'POST',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(commentJSON) {
                                successfulUploads.push(commentJSON);
                                serverResponded();
                            },
                            error: function(data) {
                                var error = JSON.parse(data.responseText);
                                alert_float('danger', error.message);
                                serverResponded();
                            },
                        });
                    }
                });
            }
        }
        var settings = $.extend({}, defaults, options);
        $(selector).comments(settings);
    }
</script>
</body>

</html>