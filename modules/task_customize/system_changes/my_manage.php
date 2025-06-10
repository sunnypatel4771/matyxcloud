<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row _buttons tw-mb-2 sm:tw-mb-4">
            <div class="col-md-8">
                <?php if (staff_can('create',  'tasks')) { ?>
                <a href="#" onclick="new_task(<?php if ($this->input->get('project_id')) {
    echo "'" . admin_url('tasks/task?rel_id=' . $this->input->get('project_id') . '&rel_type=project') . "'";
} ?>); return false;" class="btn btn-primary pull-left new">
                    <i class="fa-regular fa-plus tw-mr-1"></i>
                    <?php echo _l('new_task'); ?>
                </a>
                <?php } ?>
                <a 
                    href="<?php echo admin_url(!$this->input->get('project_id') ? ('tasks/switch_kanban/' . $switch_kanban) : ('projects/view/' . $this->input->get('project_id') . '?group=project_tasks')); ?>" class="btn btn-default mleft10 pull-left hidden-xs" data-toggle="tooltip" 
                    data-placement="top"
                    data-title="<?php echo $switch_kanban == 1 ? _l('switch_to_list_view') : _l('leads_switch_to_kanban'); ?>"
                >
                    <?php if ($switch_kanban == 1) { ?>
                    <i class="fa-solid fa-table-list"></i>
                    <?php } else { ?>
                    <i class="fa-solid fa-grip-vertical"></i>
                    <?php }; ?>
                </a>
            </div>
            <div class="col-md-4">
                <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('search_by_tags'); ?>">
                    <?php echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'tasks_kanban();', 'placeholder' => _l('search_tasks')], [], 'no-margin') ?>
                </div>
                <?php } else { ?>
                <?php $this->load->view('admin/tasks/filters',['filters_wrapper_id'=>'vueApp']); ?>
                <a href="<?php echo admin_url('tasks/detailed_overview'); ?>"
                    class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                  if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                    <div class="row">
                        <div id="kanban-params">
                            <?php echo form_hidden('project_id', $this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                            <div id="kan-ban"></div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php $this->load->view('admin/tasks/_summary', ['table' => '.table-tasks']); ?>
                        <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions"
                            class="hide bulk-actions-btn table-btn"
                            data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                        <div class="panel-table-full">
                            <?php $this->load->view('admin/tasks/_table', ['bulk_actions' => true]); ?>
                        </div>
                        <?php $this->load->view('task_customize/_bulk_actions'); ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<!-- task-comment-modal modal html  -->
<div class="modal fade" id="task-comment-modal" tabindex="-1" role="dialog" aria-labelledby="task-comment-modal"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open(admin_url('task_customize/add_comments'), ['id' => 'task-comment-form']); ?>
            <div class="modal-header">
                <h4 class="modal-title">Add Comments</h4>
            </div>
            <div class="modal-body">

               



                <div class="form-group">
                    <textarea name="comment" id="comment" class="form-control" rows="5"></textarea>
                </div>
                <input type="hidden" name="taskid" id="task_id_comment">
                  <!-- add section for task comment history  -->
                  <div class="task-comment-history">
                    <div class="task-comment-history-header">
                        <h4>Comments History</h4>
                    </div>
                    <div class="task-comment-history-body">
                        
                    </div>
                </div>
                <!-- end task comment history section  -->
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
<!-- end task-comment-modal modal html  -->
<?php init_tail(); ?>
<script>
    taskid = '<?= e($taskid); ?>';
    
    init_editor("#comment");

    function my_tasks_bulk_action(event) {
        if (confirm_delete()) {
            var ids = [],
                data = {},
                mass_delete = $("#mass_delete").prop("checked");
            if (mass_delete == false || typeof mass_delete == "undefined") {
                data.status = $("#move_to_status_tasks_bulk_action").val();

                var assignees = $("#task_bulk_assignees");
                data.assignees = assignees.length ? assignees.selectpicker("val") : "";

                var tags_bulk = $("#tags_bulk");
                data.tags = tags_bulk.length ? tags_bulk.tagit("assignedTags") : "";

                var milestone = $("#task_bulk_milestone");
                data.milestone = milestone.length ? milestone.selectpicker("val") : "";

                data.billable = $("#task_bulk_billable").val();
                data.billable = typeof data.billable == "undefined" ? "" : data.billable;

                data.priority = $("#task_bulk_priority").val();
                data.priority = typeof data.priority == "undefined" ? "" : data.priority;

                data.startdate = $("#startdate").val();
                data.startdate = typeof data.startdate == "undefined" ? "" : data.startdate;

                data.duedate = $("#duedate").val();
                data.duedate = typeof data.duedate == "undefined" ? "" : data.duedate;

                if (
                    data.status === "" &&
                    data.priority === "" &&
                    data.tags === "" &&
                    data.assignees === "" &&
                    data.milestone === "" &&
                    data.billable === "" &&
                    data.startdate === "" &&
                    data.duedate === ""
                ) {
                    return;
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $($("#tasks_bulk_actions").attr("data-table")).find("tbody tr");
            $.each(rows, function() {
                var checkbox = $($(this).find("td").eq(0)).find("input");
                if (checkbox.prop("checked") === true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass("disabled");
            setTimeout(function() {
                $.post(admin_url + "task_customize/bulk_action", data).done(function() {
                    window.location.reload();
                });
            }, 200);
        }
    }


    $(function() {
        tasks_kanban();
        
        $(".table-tasks").on('draw.dt', function() {
            init_selectpicker();
            init_datepicker();

        });
        
        
        
        //set task id on modal open
     $('#task-comment-modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var task_id = button.data('task-id');

            //get task comments
            $.post(admin_url + 'task_customize/get_task_comments', {
                task_id: task_id
            }).done(function(response) {
                var res = JSON.parse(response);
                if (res.status == true) {
                    var comments = res.comments;
                    $('.task-comment-history-body').html(comments);
                }
            });

            $('#task_id_comment').val(task_id);
        });

        //model hidden reset form
        $('#task-comment-modal').on('hidden.bs.modal', function() {
            $('#task-comment-form').trigger("reset");
            $('#task-comment-form button[type="submit"]').prop('disabled', false);

        });

        //task-comment-form submit
        $('#task-comment-form').submit(function(event) {
            //save button make disabled
            $('#task-comment-form button[type="submit"]').prop('disabled', true);
            event.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();
            $.post(url, data).done(function(success) {
                var res = JSON.parse(success);
                if (res.status == true) {
                    alert_float('success', res.message);
                    //reload table
                    reload_tasks_tables();
                    $('#task-comment-modal').modal('hide');
                } else {
                    //save button make enabled
                    $('#task-comment-form button[type="submit"]').prop('disabled', false);

                    alert_float('danger', res.message);
                }
            });
        });



        $(document).on("change", "#task-single-work_planned", function(event) {
                event.stopImmediatePropagation(); // Prevent duplicate triggers

                var $this = $(this);
                var newVal = $this.val();
                var oldVal = $this.data("lastValue"); // Store last value

                // Prevent duplicate calls if value hasn't actually changed
                if (newVal === oldVal) {
                    console.log("Duplicate value detected, ignoring...");
                    return;
                }

                $this.data("lastValue", newVal); // Update stored value
                var task_id = $this.data("task_id");
                var field_id = $this.data("field_id");

                if ($this.data("processing")) return;
                $this.data("processing", true);

                console.log("Event Triggered on Change:", newVal); // Debugging

                $.ajax({
                    url: admin_url + "task_customize/update_custom_field_value",
                    type: "POST",
                    data: {
                        val: newVal,
                        task_id: task_id,
                        field_id: field_id
                    },
                    success: function(res) {
                        console.log("AJAX Success", res);
                        reload_tasks_tables();
                    },
                    complete: function() {
                        $this.data("processing", false);
                    }
                });
            });


    });
</script>
</body>

</html>