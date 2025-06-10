<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

    <div id="wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-3 sm:tw-mb-5">
                        <div class="row">

                            <div class="col-md-6">


                                <?php if ( isset( $group_detail ) ) { ?>

                                    <select class="form-control selectpicker" name="group_id" id="group_id"
                                            data-live-search="true" data-width="100%" >

                                        <?php foreach ( $groups as $item ) {

                                            $selected = '';
                                            if ($item->id == $selected_group_id )
                                                $selected = 'selected';

                                            echo "<option $selected value='$item->id'>$item->group_name</option>";

                                        } ?>

                                    </select>

                                <?php } else { ?>

                                    <a href="<?php echo admin_url('task_manage/task_projects' ); ?>"

                                       class="btn btn-default mleft5 pull-left" data-toggle="tooltip" data-placement="top"

                                       data-title="<?php echo _l('switch_to_list_view'); ?>">

                                        <i class="fa-solid fa-table-list"></i>

                                        <?php echo _l('switch_to_list_view'); ?>

                                    </a>

                                <?php } ?>


                            </div>

                            <div class="col-md-2"></div>

                            <div class="col-md-4" data-toggle="tooltip" data-placement="top"
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
                                <div class="col-md-5">
                                    <div class="kanban-leads-sort">
                                        <span class="bold"><?php echo _l('proposals_pipeline_sort'); ?>: </span>
                                        <a href="#" onclick="task_manage_pipeline_sort('datecreated'); return false" class="datecreated">
                                            <?php echo _l('proposals_sort_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="task_manage_pipeline_sort('startdate'); return false" class="startdate">
                                            <?php echo _l('task_single_start_date'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="task_manage_pipeline_sort('duedate');return false;" class="duedate">
                                            <?php echo _l('task_single_due_date'); ?>
                                        </a>
                                    </div>

                                </div>

                                <div class="col-md-7 hide">
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

    <script>

        $(function() {

            task_manage_pipeline();


            <?php if ( isset( $group_detail ) ) { ?>

                $('#group_id').change(function (){

                    task_manage_pipeline();

                })

            <?php } ?>

        });


        function task_manage_pipeline() {

            var pipeline_url = "task_manage/task_projects/pipeline_content";

            <?php if ( isset( $group_detail ) ) { ?>

                pipeline_url = "task_manage/task_projects/group_pipeline_content/"+$('#group_id').val();

            <?php } ?>


            init_kanban(
                pipeline_url ,
                '',
                ".pipeline-status",
                290,
                360
            );

        }

        function task_manage_pipeline_sort( type ) {
            kan_ban_sort(type, task_manage_pipeline );
        }


        function task_manage_task_mark_as( task_id )
        {

            task_status = $('#project_task_id_'+task_id).val();

            url = "tasks/mark_as/" + task_status + "/" + task_id ;

            var taskModalVisible = $("#task-modal").is(":visible");
            url += "?single_task=" + taskModalVisible;

            $("body").append('<div class="dt-loader"></div>');

            requestGetJSON(url).done(function (response) {
                $("body").find(".dt-loader").remove();

                if (response.success === true || response.success == "true")
                {
                    alert_float('success','Task status changed');
                    task_manage_pipeline();
                }

            });

        }


    </script>

    </body>

    </html>

<?php
