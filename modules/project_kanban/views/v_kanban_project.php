<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

    <div id="wrapper">
        <div class="content">

            <div class="row">

                <div class="col-md-12">


                    <!-- Settings area-->
                    <div class="row" id="project_kanban_settings" style="display: none">

                        <div class="col-md-12">

                            <?php echo form_open( admin_url('project_kanban/save_settings') ); ?>

                            <div class="panel_s">

                                <div class="panel-heading">
                                    <h4 class="panel-title"><?php echo _l('project_kanban_status_options_title')?></h4>
                                </div>

                                <div class="panel-body">

                                    <?php

                                    $project_kanban_status_management = get_option('project_kanban_status_management') ? 1 : 0 ;

                                    foreach ( $project_statuses as $status )
                                    {

                                        $k_status_id    = $status['id'];

                                        $k_status_name  = $status['name'];

                                        $checked_status = '';

                                        if ( !empty( $active_statuses ) )
                                        {

                                            if ( !empty( $active_statuses[ $k_status_id ] ) )
                                                $checked_status = 'checked';

                                        }
                                        elseif ( $k_status_id != 4 )
                                            $checked_status = 'checked';

                                        ?>

                                        <div class="col-md-4 col-sm-6">

                                            <div class="checkbox pk_checkbox">
                                                <input type="checkbox" class="widget-visibility" value="<?php echo $k_status_id?>" <?php echo $checked_status?> name="project_kanban_status[]" id="project_kanban_status_<?php echo $k_status_id?>">
                                                <label for="project_kanban_status_<?php echo $k_status_id?>"><?php echo $k_status_name?></label>

                                                <?php if ( $project_kanban_status_management == 1 ) : ?>

                                                    <span style="width: 30px">&nbsp;</span>

                                                    <a class="fa fa-edit text-success pk_checkbox_action" onclick="project_kanban_status_detail( <?php echo $k_status_id ?> ); return false;" ></a>

                                                    <a class="fa fa-trash text-danger pk_checkbox_action" onclick="project_kanban_status_remove( <?php echo $k_status_id ?> ); return false;" ></a>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    <?php

                                    }

                                    ?>

                                    <div class="clearfix"></div>
                                    <hr />
                                    <div class="checkbox">
                                        <input type="checkbox" class="widget-visibility" value="1" name="project_kanban_status_management" id="project_kanban_status_management" <?php echo $project_kanban_status_management == 1 ? 'checked': '' ?> >
                                        <label for="project_kanban_status_management"><?php echo _l('project_kanban_status_management')?></label>
                                    </div>

                                </div>

                                <div class="panel-footer">

                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-save"></i>
                                        <?php echo _l('project_kanban_save_status_options')?>
                                    </button>

                                    <?php if ( $project_kanban_status_management == 1 ) : ?>

                                        <a class="btn btn-primary" onclick="project_kanban_status_detail( 0 ); return false;">
                                            <i class="fa fa-plus"></i>
                                            <?php echo _l('project_kanban_add_status')?>
                                        </a>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <?php echo form_close(); ?>

                        </div>

                    </div>

                    <div class="_buttons tw-mb-3 sm:tw-mb-5">

                        <div class="row">

                            <div class="col-md-3">

                                <a href="<?php echo admin_url('projects' ); ?>"

                                   class="btn btn-default mleft5 pull-left" data-toggle="tooltip" data-placement="top"

                                   data-title="<?php echo _l('switch_to_list_view'); ?>">

                                    <i class="fa-solid fa-table-list"></i>

                                    <?php echo _l('switch_to_list_view'); ?>

                                </a>

                                <a href="#" onclick="$('#project_kanban_settings').toggle(500); return false;"

                                   class="btn btn-default mleft5 pull-left" data-toggle="tooltip" data-placement="top"

                                   data-title="<?php echo _l('project_kanban_status_options'); ?>">

                                    <i class="fa fa-cog"></i>

                                    <?php echo _l('project_kanban_status_options'); ?>

                                </a>


                            </div>

                            <div class="col-md-3">
                                <input type="hidden" name="from_date" id="from_date" value="">
                                <input type="hidden" name="to_date" id="to_date" value="">
                                <?php $this->load->view('_statement_period_select', ['onChange' => 'project_kanban_pipeline_date()']); ?>
                            </div>

                            <div class="col-md-3">
                                <?php echo render_select('filter_staff' , $staff , [ 'staffid' , 'full_name' ] , '' , '' , [ 'onChange' => 'project_kanban_pipeline()' , 'data-none-selected-text' => _l('project_members') ]) ?>
                            </div>

                            <div class="col-md-3">

                                <?php //echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'project_kanban_pipeline();', 'placeholder' => 'Search client'], [], 'no-margin') ?>

                                <div class="form-group">

                                    <select name="client_id" id="client_id" class="ajax-search" data-width="100%"

                                            data-live-search="true" onchange="project_kanban_pipeline_client()"

                                            data-none-selected-text="<?php echo _l('client'); ?>">

                                        <option value=""></option>

                                    </select>

                                </div>


                                <?php echo form_hidden('search'); ?>
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
                                        <a href="#" onclick="project_kanban_pipeline_sort('project_created'); return false" class="project_created">
                                            <?php echo _l('proposals_sort_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="project_kanban_pipeline_sort('start_date'); return false" class="start_date">
                                            <?php echo _l('task_single_start_date'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="project_kanban_pipeline_sort('deadline');return false;" class="deadline">
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

    <div class="modal fade " id="project-preview-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xxl">
            <div class="modal-content" id="project-preview-content">

            </div>
        </div>
    </div>

    <div class="modal fade " id="project-status-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog">
            <div class="modal-content" id="project-status-content">

            </div>
        </div>
    </div>

    <script>

        var last_client_id = '';

        $(function() {

            project_kanban_pipeline();

            init_ajax_search('customer','#client_id.ajax-search');

            last_client_id = $("#client_id").val();

        });


        function project_kanban_pipeline_client()
        {

            if ( $("#client_id").val() != last_client_id )
            {

                last_client_id = $("#client_id").val();

                project_kanban_pipeline();
            }


        }

        function project_kanban_pipeline_date()
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


            project_kanban_pipeline();

        }


        function project_kanban_pipeline() {

            var pipeline_url = "project_kanban/project_kanban/kanban_content?1";

            if( $('#filter_staff').val() )
            {
                pipeline_url += "&filter_staff="+$('#filter_staff').val();
            }

            if( $('#client_id').val() )
            {
                pipeline_url += "&filter_client="+$('#client_id').val();
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

        function project_kanban_pipeline_sort( type ) {
            kan_ban_sort(type, project_kanban_pipeline );
        }


        var project_kanban_process_start = 0;

        function project_kanban_update( ui , object )
        {

            if ( project_kanban_process_start == 1 )
                return true;

            project_kanban_process_start = 1;

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

            $.post(admin_url+'project_kanban/project_kanban/kanban_status_update',data).done(function ( response ){

                project_kanban_process_start = 0;

                response = JSON.parse(response);

                $("body").find(".dt-loader").remove();

                alert_float('success',response.message);

                project_kanban_pipeline();

            });

        }


        function init_project_preview( project_id )
        {

            $("#project-preview-modal").modal("show");

            $('#project-preview-content').html('<div class="project-kanban-loading-spinner"></div>');

            requestGet("project_kanban/project_kanban/preview/" + project_id )

                .done(function (response) {


                    $('#project-preview-content').html( response );


                })

                .fail(function (data) {

                    $("#project-preview-modal").modal("hide");

                    alert_float("danger", data.responseText);

                });


        }


        function project_kanban_load_more( status_id , page , total )
        {

            var pipeline_url = "project_kanban/project_kanban/kanban_content_load/"+status_id+"?total="+total;

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

            pipeline_url += "&search="+$('#search').val();

            pipeline_url += "&sort_type="+$('input[name="sort_type"]').val();

            pipeline_url += "&sort="+$('input[name="sort"]').val();

            pipeline_url += "&page="+page;

            requestGet( admin_url + pipeline_url )

                .done(function (response) {

                    $('.kanban-load-more-'+status_id).remove();

                    console.log(response);

                    $('.project-status-' + status_id ).append(response);

                })

        }


        function project_kanban_status_detail( status_id )
        {

            $("#project-status-modal").modal("show");

            $('#project-status-content').html('<div class="project-kanban-loading-spinner"></div>');

            requestGet("project_kanban/project_kanban/status_detail/" + status_id )

                .done(function (response) {


                    $('#project-status-content').html( response );


                })

                .fail(function (data) {

                    $("#project-status-modal").modal("hide");

                    alert_float("danger", data.responseText);

                });



        }


        function project_kanban_status_remove( status_id )
        {

            $("#project-status-modal").modal("show");

            $('#project-status-content').html('<div class="project-kanban-loading-spinner"></div>');

            requestGet("project_kanban/project_kanban/status_remove/" + status_id )

                .done(function (response) {


                    $('#project-status-content').html( response );


                })

                .fail(function (data) {

                    $("#project-status-modal").modal("hide");

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


        .pk_checkbox_action{
            cursor: pointer;
            display: none;
        }

        .pk_checkbox:hover .pk_checkbox_action {
            display: inline-block;
        }

        .pk_checkbox{
            padding: 5px;
        }

    </style>

    </body>

    </html>

<?php
