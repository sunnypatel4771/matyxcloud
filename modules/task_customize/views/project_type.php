<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 

init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="_filters _hidden_inputs">
                        <!-- filters[rules][0][id]  -->

                        <?php if ($type == 'landing_page') { ?>
                            <input type="hidden" name="filters[rules][0][id]" value="status">
                            <input type="hidden" name="filters[rules][0][value][0]" value="4">
                            <input type="hidden" name="filters[rules][0][value][1]" value="6">
                            <input type="hidden" name="filters[rules][0][value][2]" value="5">
                            <input type="hidden" name="filters[rules][0][has_dynamic_value]" value="false">
                            <input type="hidden" name="filters[rules][0][operator]" value="not_in">
                            <input type="hidden" name="filters[rules][0][type]" value="MultiSelectRule">
                            <input type="hidden" name="filters[rules][1][id]" value="projects_services_included">
                            <input type="hidden" name="filters[rules][1][value][]" value="Landing Pages">
                            <input type="hidden" name="filters[rules][1][has_dynamic_value]" value="false">
                            <input type="hidden" name="filters[rules][1][operator]" value="in">
                            <input type="hidden" name="filters[rules][1][type]" value="MultiSelectRule">
                            <input type="hidden" name="filters[rules][2][id]" value="projects_service">
                            <input type="hidden" name="filters[rules][2][value]" value="Landing Page">
                            <input type="hidden" name="filters[rules][2][has_dynamic_value]" value="false">
                            <input type="hidden" name="filters[rules][2][operator]" value="equal">
                            <input type="hidden" name="filters[rules][2][type]" value="SelectRule">
                        <?php  } else if ($type == 'website') {  ?>

                            <input type="hidden" name="filters[rules][0][id]" value="projects_services_included">
                            <input type="hidden" name="filters[rules][0][value][]" value="Website">
                            <input type="hidden" name="filters[rules][0][has_dynamic_value]" value="false">
                            <input type="hidden" name="filters[rules][0][operator]" value="in">
                            <input type="hidden" name="filters[rules][0][type]" value="MultiSelectRule">
                            <input type="hidden" name="filters[rules][1][id]" value="status">
                              <input type="hidden" name="filters[rules][1][value][0]" value="4">
                            <input type="hidden" name="filters[rules][1][value][1]" value="6">
                            <input type="hidden" name="filters[rules][1][value][2]" value="5">
                            <input type="hidden" name="filters[rules][1][has_dynamic_value]" value="false">
                            <input type="hidden" name="filters[rules][1][operator]" value="not_in">
                            <input type="hidden" name="filters[rules][1][type]" value="MultiSelectRule">


                        <?php  } ?>
                    </div>

                    <div class="panel_s tw-mt-2 sm:tw-mt-4">
                        <div class="panel-body">
                            <div class="row mbot15">

                            </div>
                            <hr class="hr-panel-separator" />
                            <div class="panel-table-full">
                                <?php $this->load->view('admin/projects/table_html'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- project_status_note -->
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
<?php init_tail(); ?>


<script>
    $(function() {
        var filterParameters = {};
        $.each($('._hidden_inputs._filters input,._hidden_inputs._filters select'), function() {
            filterParameters[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, filterParameters,
            <?php echo hooks()->apply_filters('projects_table_default_order', json_encode([[PROJECT_COLUMN_PRIORITY, "asc"], [PROJECT_COLUMN_PRIORITY_2, "asc"]])); ?>);

        // $("#projects").on('draw.dt', function() {
        //     init_selectpicker();
        //     // caret hide
        //     $('.caret').hide();
        //     init_datepicker();
        // });        



        //  //project_status_note open modal
        //  $(document).on('click', '.project_status_note', function() {
        //     var projectId = $(this).data('project-id');
        //     console.log('projectId:', projectId)
        //     var custom_field_id = $(this).data('custom-field-id');
        //     console.log('custom_field_id:', custom_field_id)
        //     var custom_field_value = $(this).data('custom-field-value');
        //     console.log('custom_field_value:', custom_field_value)
        //     $('#project_status_note').modal('show');
        //     $('#project_status_note').find('#status_note').val(custom_field_value);
        //     $('#project_status_note').find('#project_id').val(projectId);
        //     $('#project_status_note').find('#custom_field_id').val(custom_field_id);
        // });

        // $(document).on('click', '#save_status_note', function() {
        //     var projectId = $('#project_id').val();
        //     var custom_field_id = $('#custom_field_id').val();
        //     var status_note = $('#status_note').val();
        //     console.log('projectId:', projectId)
        //     console.log('custom_field_id:', custom_field_id)
        //     console.log('status_note:', status_note)
        //     project_change_custom_notes_field_value(projectId, custom_field_id, status_note);
        // });

        // $(document).on('change', '.project_launch_eta', function() {
        //     var project_id = $(this).data('project_id');
        //     var value = $(this).val();
        //     var custom_field_id = <?php echo PROJECT_LAUNCH_ETA; ?>;
        //     project_change_custom_notes_field_value(project_id, custom_field_id, value);
        // });
     
    });

    // // project_mark_as function
    // function project_mark_as(status, project_id) {
    //     url = admin_url + 'task_customize/project_mark_as/' + status + '/' + project_id;
    //     $("body").append('<div class="dt-loader"></div>');
    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         success: function(response) {
    //             $("body").find(".dt-loader").remove();
    //             if (response.success) {
    //                 //data-table reload using id #projects
    //                 $('#projects').DataTable().ajax.reload();

    //             } else {
    //                 $('#projects').DataTable().ajax.reload();

    //             }
    //         }
    //     });
    // }


    //    // project_change_custom_field_value function
    //    function project_change_custom_notes_field_value(project_id, custom_field_id, value) {
    //     url = admin_url + 'task_customize/project_change_custom_notes_field_value/' + project_id + '/' + custom_field_id;
    //     $("body").append('<div class="dt-loader"></div>');

    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         data: {
    //             value: value
    //         },
    //         success: function(response) {
    //             var response = JSON.parse(response);
    //             if (response.success) {
    //                 $("body").find(".dt-loader").remove();
    //                 $('#projects').DataTable().ajax.reload();
    //                 $('#project_status_note').modal('hide');
    //             } else {
    //                 $("body").find(".dt-loader").remove();
    //                 alert(response.message);
    //                 $('#project_status_note').modal('hide');
    //             }
    //         }
    //     });
    // }

    // project_change_custom_field_value function
    function project_change_custom_field_value(project_id, custom_field_id, value) {
        url = admin_url + 'task_customize/project_change_custom_field_value/' + project_id + '/' + custom_field_id + '/' + value;
        $("body").append('<div class="dt-loader"></div>');

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response) {
                var response = JSON.parse(response);
                if (response.success) {
                    $("body").find(".dt-loader").remove();
                    $('.table-projects').DataTable().ajax.reload();
                } else {
                    $("body").find(".dt-loader").remove();
                    alert(response.message);
                }
            }
        });
    }

    function project_change_custom_field_value_multiselect(project_id, custom_field_id, value) {
        url = admin_url + 'task_customize/project_change_custom_field_value_multiselect/' + project_id + '/' + custom_field_id;
        $("body").append('<div class="dt-loader"></div>');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                value: value
            },
            success: function(response) {
                $("body").find(".dt-loader").remove();
                $('.table-projects').DataTable().ajax.reload();
            },
            error: function(response) {
                $("body").find(".dt-loader").remove();
                alert(response.message);
            }
        });

    }
</script>
</body>

</html>