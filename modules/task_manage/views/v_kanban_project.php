<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

    <div id="wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-3 sm:tw-mb-5">

                        <div class="row">

                            <div class="col-md-3">
                                <input type="hidden" name="from_date" id="from_date" value="">
                                <input type="hidden" name="to_date" id="to_date" value="">
                                <?php $this->load->view('_statement_period_select', ['onChange' => 'task_manage_pipeline_date()']); ?>
                            </div>

                            <div class="col-md-3">
                                <?php echo render_select('filter_groups' , $groups , [ 'id' , 'group_name' ] , '' , '' , [ 'onChange' => 'task_manage_pipeline()' , 'data-none-selected-text' => _l('task_manage_groups') ]) ?>
                            </div>

                            <div class="col-md-3">
                                <?php echo render_select('filter_staff' , $staff , [ 'staffid' , 'full_name' ] , '' , '' , [ 'onChange' => 'task_manage_pipeline()' , 'data-none-selected-text' => _l('project_members') ]) ?>
                            </div>

                            <div class="col-md-3" data-toggle="tooltip" data-placement="top"
                                 data-title="<?php echo _l('search_by_tags'); ?>">
                                <?php echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'task_manage_pipeline();', 'placeholder' => 'Search client'], [], 'no-margin') ?>


                                <?php echo form_hidden('sort_type'); ?>
                                <?php echo form_hidden('sort', (get_option('default_proposals_pipeline_sort') != '' ? get_option('default_proposals_pipeline_sort_type') : '')); ?>
                            </div>

                        </div>

                    </div>

                    <div class="animated mtop5 fadeIn">
                        <?php echo form_hidden('proposalid', 0); ?>

                        <div>

                            <div class="row">

                                <div class="col-md-8">
                                    <div class="kanban-leads-sort">
                                        <span class="bold"><?php echo _l('proposals_pipeline_sort'); ?>: </span>
                                        <a href="#" onclick="task_manage_pipeline_sort('project_created'); return false" class="project_created">
                                            <?php echo _l('proposals_sort_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="task_manage_pipeline_sort('start_date'); return false" class="start_date">
                                            <?php echo _l('task_single_start_date'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="task_manage_pipeline_sort('deadline');return false;" class="deadline">
                                            <?php echo _l('task_single_due_date'); ?>
                                        </a>
                                    </div>

                                    <div class="kanban-leads-sort">
                                        <a href="<?php echo admin_url('task_manage/task_projects' ); ?>"

                                           class="btn btn-default mleft5 pull-left" data-toggle="tooltip" data-placement="top"

                                           data-title="<?php echo _l('switch_to_list_view'); ?>">

                                            <i class="fa-solid fa-table-list"></i>

                                            <?php echo _l('switch_to_list_view'); ?>

                                        </a>
                                    </div>

                                </div>

                                <div class="col-md-4 hide">
                                    <h3 id="item_name_text"></h3>
                                </div>

                            </div>

                            <div class="row">
                                <div id="proposals-pipeline">
                                    <div class="container-fluid">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="proposal">
    </div>


    <?php init_tail(); ?>

    <div id="convert_helper"></div>

    <div class="modal fade " id="project-preview-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="project-preview-content">

            </div>
        </div>
    </div>

    <script>

        $(function() {

            task_manage_pipeline();

        });


        function task_manage_pipeline_date()
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


            task_manage_pipeline();

        }


        function task_manage_pipeline() {

            var pipeline_url = "task_manage/task_projects/kanban_content?1";

            if( $('#filter_groups').val() )
            {
                pipeline_url += "&filter_group="+$('#filter_groups').val();
            }

            if( $('#filter_staff').val() )
            {
                pipeline_url += "&filter_staff="+$('#filter_staff').val();
            }

            if( $('#from_date').val() )
            {
                pipeline_url += "&from_date="+$('#from_date').val();
            }

            if( $('#to_date').val() )
            {
                pipeline_url += "&to_date="+$('#to_date').val();
            }

            pipeline_url += "&";

            init_kanban(
                pipeline_url ,
                project_kanban_update,
                ".project-status",
                290,
                360
            );

        }

        function task_manage_pipeline_sort( type ) {
            kan_ban_sort(type, task_manage_pipeline );
        }


        var task_manage_process_start = 0;

        function project_kanban_update( ui , object )
        {

            if ( task_manage_process_start == 1 )
                return true;

            task_manage_process_start = 1;

            var data = {

                projects: [],

                status: $(ui.item.parent()[0]).attr("data-status-id"),

            };


            $.each($(ui.item.parent()[0]).find("[data-project-id]"), function (idx, el) {

                var id = $(el).attr("data-project-id");

                if (id) {

                    data.projects.push(id);

                }

            });


            $("body").append('<div class="dt-loader"></div>');

            $.post(admin_url+'task_manage/task_projects/kanban_status_update',data).done(function ( response ){

                task_manage_process_start = 0;

                response = JSON.parse(response);

                $("body").find(".dt-loader").remove();

                alert_float('success',response.message);

                task_manage_pipeline();

            });

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

    </body>

    </html>

<?php
