<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Tasks -->
<?php
    if ($project->settings->hide_tasks_on_main_tasks_table == '1') {
        echo '<i class="fa fa-exclamation fa-2x pull-left" data-toggle="tooltip" data-title="' . _l('project_hide_tasks_settings_info') . '"></i>';
    }
?>
<div class="panel_s">
    <div class="panel-body advdasvasdvv">
        <div class="tasks-table panel-table-full" id="vueApp">
            <?php init_relation_tasks_table_change(['data-new-rel-id' => $project->id, 'data-new-rel-type' => 'project'], 'vueApp', true); ?>
        </div>
    </div>
</div>


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