<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();?>

<div id="wrapper" >

    <div class="content">


        <div class="row mbot10">

            <div class="col-md-12">

                <?php if (has_permission('projects', '', 'create')) { ?>

                    <a href="<?php echo admin_url('projects/project'); ?>" class="btn btn-primary pull-left display-block mright5"  data-placement="top" data-title="<?php echo _l('new_project'); ?>">

                        <i class="fa-regular fa-plus tw-mr-1"></i>

                        <?php echo _l('new_project'); ?>

                    </a>

                <?php } ?>


                <a href="<?php echo admin_url('task_manage/task_projects/pipeline'); ?>" class="btn btn-default pull-left switch-pipeline hidden-xs" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('switch_to_pipeline'); ?>">

                    <i class="fa-solid fa-grip-vertical"></i>

                    <?php echo _l('switch_to_pipeline')?>

                </a>



                <a href="<?php echo admin_url('task_manage/task_projects/kanban'); ?>" class="btn btn-default pull-left switch-pipeline hidden-xs" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('leads_switch_to_kanban'); ?>">

                    <i class="fa-solid fa-grip-vertical"></i>

                    <?php echo _l('leads_switch_to_kanban')?>

                </a>

                <h4 class="text-info hide "> <div class="fa fa-info"></div> <?php echo _l('task_manage_project_info_message')?> </h4>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="panel_s">

                    <div class="panel-body">


                        <div class="row mbot15">

                            <div class="col-md-12">

                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"

                                         stroke-width="1.5" stroke="currentColor"

                                         class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">

                                        <path stroke-linecap="round" stroke-linejoin="round"

                                              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />

                                    </svg>

                                    <span> <?php echo _l('projects_summary'); ?> </span>

                                </h4>

                                <?php

                                $_where = ' task_manage_groups is not null ';

                                if (!has_permission('projects', '', 'view')) {

                                    $_where .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';

                                }

                                ?>

                            </div>

                            <div >

                                <input type="hidden" name="task_project_status_id" id="task_project_status_id" value="0">

                                <?php foreach ( $statuses as $status ) { ?>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">

                                        <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id']; ?>

                                        <a href="#"

                                           class="tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center"

                                           onclick="task_project_status_filter( <?php echo $status['id']; ?>); return false;">

                                        <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">

                                            <?php echo total_rows(db_prefix() . 'projects', $where); ?>

                                        </span>

                                            <span style="color:<?php echo $status['color']; ?>"

                                                  project-status-<?php echo $status['id']; ?>">

                                            <?php echo $status['name']; ?>

                                            </span>

                                        </a>

                                    </div>

                                    <?php

                                } ?>

                            </div>

                        </div>

                        <hr class="hr-panel-separator" />

                        <div class="clearfix"></div>

                        <div class="row">

                            <div class="col-md-4">
                                <input type="hidden" name="from_date" id="from_date" value="">
                                <input type="hidden" name="to_date" id="to_date" value="">
                                <?php $this->load->view('_statement_period_select', ['onChange' => 'reload_the_table()']); ?>
                            </div>

                            <div class="col-md-4">
                                <?php echo render_select('filter_groups' , $groups , [ 'id' , 'group_name' ] , '' , '' , [ 'multiple' => true , 'onChange' => 'project_list_table_load()' , 'data-none-selected-text' => _l('task_manage_groups') ]) ?>
                            </div>

                            <div class="col-md-4">
                                <?php echo render_select('filter_staff' , $staff , [ 'staffid' , 'full_name' ] , '' , '' , [ 'multiple' => true , 'onChange' => 'project_list_table_load()' , 'data-none-selected-text' => _l('project_members') ]) ?>
                            </div>


                        </div>

                        <div class="clearfix"></div>

                        <hr class="hr-panel-separator" />


                        <div class="panel-table">

                            <table class="table table_task_projects">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('the_number_sign')?></th>
                                        <th><?php echo _l('project_name')?></th>
                                        <th><?php echo _l('project_customer')?></th>
                                        <th><?php echo _l('project_start_date')?></th>
                                        <th><?php echo _l('project_deadline')?></th>
                                        <th><?php echo _l('project_members')?></th>
                                        <th><?php echo _l('project_status')?></th>
                                        <th><?php echo _l('task_manage_groups')?></th>
                                        <th><?php echo _l('task_manage_percent')?> </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="modal fade" id="project_task_diagram" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="myModalLabel">

                    <span class="edit-title"> <?php echo _l('task_manage_diagram_title')?> </span>

                </h4>

            </div>


            <div class="modal-body" id="project_task_diagram_detail">

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

            </div>

        </div>

    </div>

</div>



<div class="modal fade" id="mark_tasks_finished_modal" tabindex="-1" role="dialog" data-toggle="modal">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"><?php echo _l('additional_action_required'); ?></h4>

            </div>

            <div class="modal-body">

                <div class="checkbox checkbox-primary">

                    <input type="checkbox" name="notify_project_members_status_change" id="notify_project_members_status_change">

                    <label for="notify_project_members_status_change"><?php echo _l('notify_project_members_status_change'); ?></label>

                </div>

                <div class="checkbox checkbox-primary">

                    <input type="checkbox" name="mark_all_tasks_as_completed" id="mark_all_tasks_as_completed">

                    <label for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>

                </div>

                <?php if (is_email_template_active('project-finished-to-customer') ) { ?>

                    <div class="form-group project_marked_as_finished hide no-mbot">

                        <hr />

                        <div class="checkbox checkbox-primary">

                            <input type="checkbox" name="project_marked_as_finished_email_to_contacts" id="project_marked_as_finished_email_to_contacts">

                            <label for="project_marked_as_finished_email_to_contacts"><?php echo _l('project_marked_as_finished_to_contacts'); ?></label>

                        </div>

                    </div>

                <?php } ?>

            </div>

            <div class="modal-footer">

                <button class="btn btn-primary" id="project_mark_status_confirm" onclick="confirm_task_manage_project_status_change(this); return false;"><?php echo _l('project_mark_tasks_finished_confirm'); ?></button>

            </div>

        </div>

    </div>

</div>



<div class="modal fade " id="project-preview-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="project-preview-content">

        </div>
    </div>
</div>

<?php init_tail(); ?>

<style>

    .task_manage_progress_bar {
        width: 220px;
        height: 20px;
        border: 1px solid #ccc;
        position: relative;
        float: left;
    }

    .task_manage_progress {
        height: 100%;
        background-color: #4CAF50;
        width: 0;
        position: absolute;
    }

    .task_manage_percent {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #850c0c;
        font-weight: bold;
    }

</style>

<style>

    .project-kanban-loading-spinner {
        border: 4px solid #3498db; /* Spinner color */
        border-radius: 50%;
        border-top: 4px solid #fff;
        width: 35px;
        height: 35px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>


<script>


    (function($) {
        "use strict";

        $(function() {

            var taskProjectParams = {};

            taskProjectParams['task_project_status_id'] = '[name="task_project_status_id"]';
            taskProjectParams['from_date'] = '#from_date';
            taskProjectParams['to_date'] = '#to_date';
            taskProjectParams['filter_groups'] = '#filter_groups';
            taskProjectParams['filter_staff'] = '#filter_staff';

            initDataTable('.table_task_projects', admin_url + 'task_manage/task_projects/lists', [ 7 , 8 ] , [ 7 , 8 ] , taskProjectParams , [ 0 , 'desc' ] );

        });


    })(jQuery);

    function task_project_status_filter( status_id )
    {

        $('#task_project_status_id').val(status_id);

        $('.table_task_projects').DataTable().ajax.reload();

    }

    function project_task_diagram( project_id = 0 )
    {

        $.post(admin_url+"task_manage/task_projects/diagram_detail" , { project_id : project_id } ).done(function ( response ){

            response = JSON.parse( response );

            $('#project_task_diagram_detail').html(response.diagram_content);


            $('#project_task_diagram').modal();

        })

    }


    function task_manage_project_mark_as_modal(status_id, $project_id, target) {

        $("#mark_tasks_finished_modal").modal("show");

        $("#project_mark_status_confirm").attr("data-status-id", status_id);

        $("#project_mark_status_confirm").attr("data-project-id", $project_id);

        var $projectMarkedAsFinishedInput = $(

            "#project_marked_as_finished_email_to_contacts"

        );

        if (status_id == 4) {

            if ($projectMarkedAsFinishedInput.length > 0) {

                $projectMarkedAsFinishedInput

                    .parents(".project_marked_as_finished")

                    .removeClass("hide");

            }

        } else {

            if ($projectMarkedAsFinishedInput.length > 0) {

                $projectMarkedAsFinishedInput.prop("checked", false);

                $projectMarkedAsFinishedInput

                    .parents(".project_marked_as_finished")

                    .addClass("hide");

            }

        }

        var noticeWrapper = $(".recurring-tasks-notice");

        if (status_id == 4 || status_id == 5 || status_id == 3) {

            if (noticeWrapper.length) {

                var notice = noticeWrapper.data("notice-text");

                notice = notice.replace("{0}", $(target).data("name"));

                noticeWrapper.html(notice);

                noticeWrapper.append(

                    '<input type="hidden" name="cancel_recurring_tasks" value="true">'

                );

                noticeWrapper.removeClass("hide");

            }

            //$("#mark_all_tasks_as_completed").prop("checked", true);

        } else {

            noticeWrapper.html("").addClass("hide");

            //$("#mark_all_tasks_as_completed").prop("checked", false);

        }

        $("#mark_all_tasks_as_completed").prop("checked", false);

    }



    function confirm_task_manage_project_status_change(e) {

        var data = {};



        $(e).attr("disabled", true);



        data.project_id = $(e).data("project-id");

        data.status_id = $(e).data("status-id");



        if (data.status_id == 4) {

            var $projectMarkedAsFinishedInput = $(

                "#project_marked_as_finished_email_to_contacts"

            );

            if ($projectMarkedAsFinishedInput.length > 0) {

                data.send_project_marked_as_finished_email_to_contacts =

                    $projectMarkedAsFinishedInput.prop("checked") === true ? 1 : 0;

            }

        }



        data.mark_all_tasks_as_completed = $("#mark_all_tasks_as_completed").prop("checked") === true ? 1 : 0;

        data.cancel_recurring_tasks = $('input[name="cancel_recurring_tasks"]').val();



        if (!data.cancel_recurring_tasks) {

            data.cancel_recurring_tasks = false;

        } else {

            data.cancel_recurring_tasks = true;

        }



        data.notify_project_members_status_change =

            $("#notify_project_members_status_change").prop("checked") === true ? 1 : 0;



        $.post(admin_url + "projects/mark_as", data)

            .done(function (response) {

                response = JSON.parse(response);

                alert_float(

                    response.success === true ? "success" : "warning",

                    response.message

                );

                $('.table_task_projects').DataTable().ajax.reload();

                $("#mark_tasks_finished_modal").modal("hide");

            })

            .fail(function (data) {

                $('.table_task_projects').DataTable().ajax.reload();

                $("#mark_tasks_finished_modal").modal("hide");

            });

    }



    function reload_the_table()
    {
        var $statementPeriod = $('#range');
        var value = $statementPeriod.selectpicker('val');
        var period = new Array();
        if (value != 'period') {
            period = JSON.parse(value);
        } else {
            period[0] = $('input[name="period-from"]').val();
            period[1] = $('input[name="period-to"]').val();

            if (period[0] == '' || period[1] == '') {
                return false;
            }
        }

        $('#from_date').val(period[0]);
        $('#to_date').val(period[1]);


        project_list_table_load();

    }

    function project_list_table_load()
    {

        $('.table_task_projects').DataTable().ajax.reload();

    }

    function init_project_preview( project_id )
    {

        $("#project-preview-modal").modal("show");

        $('#project-preview-content').html('<div class="project-kanban-loading-spinner"></div>');

        requestGet("task_manage/task_projects/preview/" + project_id )

            .done(function (response) {


                $('#project-preview-content').html( response );


            })

            .fail(function (data) {

                $("#project-preview-modal").modal("hide");

                alert_float("danger", data.responseText);

            });


    }

</script>


</body>

</html>
