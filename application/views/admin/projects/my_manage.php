<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div id="vueApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="_buttons tw-mb-2 sm:tw-mb-4">
                        <?php if (staff_can('create',  'projects')) { ?>
                            <a href="<?php echo admin_url('projects/project'); ?>"
                                class="btn btn-primary pull-left display-block mright5">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_project'); ?>
                            </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip"
                            data-title="<?php echo _l('project_gant'); ?>" class="btn btn-default btn-with-tooltip">
                            <i class="fa fa-align-left" aria-hidden="true"></i>
                        </a>
                        <div class="tw-inline pull-right">
                            <app-filters
                                id="<?php echo $table->id(); ?>"
                                view="<?php echo $table->viewName(); ?>"
                                :rules="extra.projectsRules || <?php echo app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([(int) $this->input->get('status')]) : []); ?>"
                                :saved-filters="<?php echo $table->filtersJs(); ?>"
                                :available-rules="<?php echo $table->rulesJs(); ?>">
                            </app-filters>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="panel_s tw-mt-2 sm:tw-mt-4">
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
                                        <span>
                                            <?php echo _l('projects_summary'); ?>
                                        </span>
                                    </h4>
                                    <?php
                                    $_where = '';
                                    if (staff_cant('view', 'projects')) {
                                        $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
                                    }
                                    ?>
                                </div>
                                <div class="_filters _hidden_inputs">
                                    <?php foreach ($statuses as $status) { ?>
                                        <div
                                            class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                            <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id']; ?>
                                            <a href="#"
                                                class="tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center"
                                                @click.prevent="extra.projectsRules = <?php echo app\services\utilities\Js::from($table->findRule('status')->setValue([(int) $status['id']])); ?>">
                                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                                    <?php echo total_rows(db_prefix() . 'projects', $where); ?>
                                                </span>
                                                <span style="color: <?php echo e($status['color']); ?>" class="<?php echo 'project-status-' . $status['color']; ?>">
                                                    <?php echo e($status['name']); ?>
                                                </span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <hr class="hr-panel-separator" />
                            <div class="panel-table-full">
                                <?php echo form_hidden('custom_view'); ?>
                                <?php $this->load->view('admin/projects/table_html'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
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
        initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, {},
            <?php echo hooks()->apply_filters('projects_table_default_order', json_encode([[PROJECT_COLUMN_PRIORITY, "asc"], [PROJECT_COLUMN_PRIORITY_2, "asc"]])); ?>);

        init_ajax_search('customer', '#clientid_copy_project.ajax-search');

        $(".table-projects").on('draw.dt', function() {
            init_selectpicker();
            init_datepicker();

        });

        // //project_status_note open modal
        // $(document).on('click', '.project_status_note', function() {
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


    // project_change_custom_field_value function
    // function project_change_custom_notes_field_value(project_id, custom_field_id, value) {
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
</script>
</body>

</html>