<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('projects'); ?></h4>
<?php if (isset($client)) { ?>
    <?php if (staff_can('create',  'projects')) { ?>
        <a href="<?php echo admin_url('projects/project?customer_id=' . $client->userid); ?>"
            class="btn btn-primary mbot15<?php echo $client->active == 0 ? ' disabled' : ''; ?>">
            <i class="fa-regular fa-plus tw-mr-1"></i>
            <?php echo _l('new_project'); ?>
        </a>
    <?php } ?>
    <?php
    $_where = '';
    if (staff_cant('view', 'projects')) {
        $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
    }
    ?>
    <dl class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-5 tw-gap-3 sm:tw-gap-5 tw-mb-5">
        <?php foreach ($project_statuses as $status) { ?>
            <div class="tw-border tw-border-solid tw-border-neutral-200 tw-rounded-md tw-bg-white">
                <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
                    <dt class="tw-text-base tw-font-normal" style="color:<?php echo e($status['color']); ?>">
                        <?php echo e($status['name']); ?>
                    </dt>
                    <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                        <div class="tw-flex tw-items-baseline tw-text-lg tw-font-semibold tw-text-primary-600">
                            <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id'] . ' AND clientid=' . $client->userid; ?>
                            <?php echo total_rows(db_prefix() . 'projects', $where); ?>
                        </div>
                    </dd>
                </div>
            </div>
        <?php } ?>
    </dl>
<?php
    $this->load->view('admin/projects/table_html', ['class' => 'projects-single-client']);
}
?>

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