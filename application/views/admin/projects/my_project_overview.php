<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    .panel-heading {
        background-color: #fff;
        border-bottom: 1px solid #eee;
        padding: 10px 15px;
    }

    .rotate-icon {
        transition: transform 0.25s ease;
        color: #6c757d;
        font-size: 14px;
    }

    .rotate-icon.rotate {
        transform: rotate(90deg);
    }

    .panel-title span {
        color: #495057;
        font-weight: 500;
    }

    .panel-title:hover span {
        color: #007bff;
    }

    .custom_icon_size {
        font-size: 15px !important;
    }
</style>

<div class="row">
    <div class="col-md-6 project-overview-left">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="project-info tw-mb-0 tw-font-medium tw-text-base tw-tracking-tight">
                            <?php echo _l('project_progress_text'); ?> <span
                                class="tw-text-neutral-500"><?php echo e($percent); ?>%</span>
                        </p>
                        <div class="progress progress-bar-mini">
                            <div class="progress-bar progress-bar-success no-percent-text not-dynamic"
                                role="progressbar" aria-valuenow="<?php echo e($percent); ?>" aria-valuemin="0"
                                aria-valuemax="100" style="width: 0%" data-percent="<?php echo e($percent); ?>">
                            </div>
                        </div>
                        <?php hooks()->do_action('admin_area_after_project_progress') ?>
                        <hr class="hr-panel-separator" />
                    </div>

                    <?php if (count($project->shared_vault_entries) > 0) { ?>
                        <?php $this->load->view('admin/clients/vault_confirm_password'); ?>
                        <div class="col-md-12">
                            <p class="tw-font-medium">
                                <a href="#" onclick="slideToggle('#project_vault_entries'); return false;"
                                    class="tw-inline-flex tw-items-center tw-space-x-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                    <span>
                                        <?php echo _l('project_shared_vault_entry_login_details'); ?>
                                    </span>
                                </a>
                            </p>
                            <div id="project_vault_entries"
                                class="hide tw-mb-4 tw-bg-neutral-50 tw-px-4 tw-py-2 tw-rounded-md">
                                <?php foreach ($project->shared_vault_entries as $vault_entry) { ?>
                                    <div class="tw-my-3">
                                        <div class="row" id="<?php echo 'vaultEntry-' . $vault_entry['id']; ?>">
                                            <div class="col-md-6">
                                                <p class="mtop5">
                                                    <b><?php echo _l('server_address'); ?>:
                                                    </b><?php echo e($vault_entry['server_address']); ?>
                                                </p>
                                                <p class="tw-mb-0">
                                                    <b><?php echo _l('port'); ?>:
                                                    </b><?php echo e(!empty($vault_entry['port']) ? $vault_entry['port'] : _l('no_port_provided')); ?>
                                                </p>
                                                <p class="tw-mb-0">
                                                    <b><?php echo _l('vault_username'); ?>:
                                                    </b><?php echo e($vault_entry['username']); ?>
                                                </p>
                                                <p class="no-margin">
                                                    <b><?php echo _l('vault_password'); ?>: </b><span
                                                        class="vault-password-fake">
                                                        <?php echo str_repeat('&bull;', 10); ?> </span><span
                                                        class="vault-password-encrypted"></span> <a href="#"
                                                        class="vault-view-password mleft10" data-toggle="tooltip"
                                                        data-title="<?php echo _l('view_password'); ?>"
                                                        onclick="vault_re_enter_password(<?php echo e($vault_entry['id']); ?>,this); return false;"><i
                                                            class="fa fa-lock" aria-hidden="true"></i></a>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if (!empty($vault_entry['description'])) { ?>
                                                    <p class="tw-mb-0">
                                                        <b><?php echo _l('vault_description'); ?>:
                                                        </b><br /><?php echo process_text_content_for_display($vault_entry['description']); ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-md-12">
                        <h4 class="tw-font-semibold tw-text-base tw-mb-4">
                            <?php echo _l('project_overview'); ?>
                        </h4>
                        <dl class="tw-grid tw-grid-cols-1 tw-gap-x-4 tw-gap-y-5 sm:tw-grid-cols-2">
                            <div class="sm:tw-col-span-1 project-overview-id">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project'); ?> <?php echo _l('the_number_sign'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo e($project->id); ?></dd>
                            </div>

                            <div class="sm:tw-col-span-1 project-overview-customer">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_customer'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <a
                                        href="<?php echo admin_url(); ?>clients/client/<?php echo e($project->clientid); ?>">
                                        <?php echo e($project->client_data->company); ?>
                                    </a>
                                </dd>
                            </div>

                            <?php if (staff_can('edit',  'projects')) { ?>
                                <div class="sm:tw-col-span-1 project-overview-billing">
                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                        <?php echo _l('project_billing_type'); ?>
                                    </dt>
                                    <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                        <?php
                                        if ($project->billing_type == 1) {
                                            $type_name = 'project_billing_type_fixed_cost';
                                        } elseif ($project->billing_type == 2) {
                                            $type_name = 'project_billing_type_project_hours';
                                        } else {
                                            $type_name = 'project_billing_type_project_task_hours';
                                        }
                                        echo _l($type_name);
                                        ?>
                                    </dd>
                                </div>
                                <?php if ($project->billing_type == 1 || $project->billing_type == 2) { ?>
                                    <div class="sm:tw-col-span-1 project-overview-amount">
                                        <?php if ($project->billing_type == 1) { ?>
                                            <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                                <?php echo _l('project_total_cost'); ?>
                                            </dt>
                                            <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                                <?php echo e(app_format_money($project->project_cost, $currency)); ?>
                                            </dd>
                                        <?php  } else { ?>
                                            <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                                <?php echo _l('project_rate_per_hour'); ?>
                                            </dt>
                                            <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                                <?php echo e(app_format_money($project->project_rate_per_hour, $currency)); ?>
                                            </dd>
                                        <?php } ?>
                                    </div>
                            <?php }
                            } ?>

                            <div class="sm:tw-col-span-1 project-overview-status">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_status'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <?php echo e($project_status['name']); ?>
                                </dd>
                            </div>

                            <div class="sm:tw-col-span-1 project-overview-active-days">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_active_days'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <a href="javascript:void(0);" onclick="view_active_days(<?php echo e($project->id); ?>); return false;"><?php echo e(get_active_days($project->id)); ?></a>
                                </dd>
                            </div>

                            <div class="sm:tw-col-span-1 project-overview-date-created">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_datecreated'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <?php echo e(_d($project->project_created)); ?>
                                </dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-start-date">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_start_date'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <?php echo e(_d($project->start_date)); ?>
                                </dd>
                            </div>
                            <?php if ($project->deadline) { ?>
                                <div class="sm:tw-col-span-1 project-overview-deadline">
                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                        <?php echo _l('project_deadline'); ?>
                                    </dt>
                                    <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                        <?php echo e(_d($project->deadline)); ?>
                                    </dd>
                                </div>
                            <?php } ?>

                            <?php if ($project->date_finished) { ?>
                                <div class="sm:tw-col-span-1 project-overview-date-finished">
                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                        <?php echo _l('project_completed_date'); ?>
                                    </dt>
                                    <dd class="tw-mt-1 tw-text-sm text-success">
                                        <?php echo e(_dt($project->date_finished)); ?>
                                    </dd>
                                </div>
                            <?php } ?>

                            <?php if ($project->estimated_hours && $project->estimated_hours != '0') { ?>
                                <div class="sm:tw-col-span-1 project-overview-estimated-hours">
                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                        <?php echo _l('estimated_hours'); ?>
                                    </dt>
                                    <dd
                                        class="tw-mt-1 tw-text-sm <?php echo hours_to_seconds_format($project->estimated_hours) < (int)$project_total_logged_time ? 'text-warning' : 'text-neutral-900'; ?>">
                                        <?php echo e(str_replace('.', ':', $project->estimated_hours)); ?>
                                    </dd>
                                </div>
                            <?php } ?>

                            <div class="sm:tw-col-span-1 project-overview-total-logged-hours">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_overview_total_logged_hours'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                    <?php echo e(seconds_to_time_format($project_total_logged_time)); ?>
                                </dd>
                            </div>


                            <?php $custom_fields = get_custom_fields('projects');
                            if (count($custom_fields) > 0) { ?>
                                <?php foreach ($custom_fields as $field) { ?>
                                    <?php $value = get_custom_field_value($project->id, $field['id'], 'projects');
                                    if ($value == '') {
                                        continue;
                                    } ?>
                                    <div class="sm:tw-col-span-1">
                                        <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                            <?php echo e(ucfirst($field['name'])); ?>
                                        </dt>
                                        <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">
                                            <?php echo $value; ?>
                                        </dd>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <?php $tags = get_tags_in($project->id, 'project'); ?>
                            <?php if (count($tags) > 0) { ?>
                                <div class="sm:tw-col-span-1 project-overview-tags">
                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                        <?php echo _l('tags'); ?>
                                    </dt>
                                    <dd class="tags-read-only-custom tw-mt-1 tw-text-sm tw-text-neutral-900">
                                        <input type="text" class="tagsinput read-only" id="tags" name="tags"
                                            value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
                                    </dd>
                                </div>
                            <?php } ?>

                            <div class="sm:tw-col-span-1 project-overview-cam">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('cam_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->cam_id) && $project->cam_id != '' ? get_staff_full_name($project->cam_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-optimizer">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('optimizer_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->optimizer_id) && $project->optimizer_id != '' ? get_staff_full_name($project->optimizer_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-organic_social">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('organic_social_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->organic_social_id) && $project->organic_social_id != '' ? get_staff_full_name($project->organic_social_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-seo_lead">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('seo_lead_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->seo_lead_id) && $project->seo_lead_id != '' ? get_staff_full_name($project->seo_lead_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-sale_rep">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('sale_rep_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->sale_rep_id) && $project->sale_rep_id != '' ? get_staff_full_name($project->sale_rep_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-content">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('content_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->content_id) && $project->content_id != '' ? get_staff_full_name($project->content_id) : 'N/A'; ?></dd>
                            </div>
                            <div class="sm:tw-col-span-1 project-overview-web_lead">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('web_lead_id'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo isset($project->web_lead_id) && $project->web_lead_id != '' ? get_staff_full_name($project->web_lead_id) : 'N/A'; ?></dd>
                            </div>

                            <div class="clearfix"></div>
                            <div class="sm:tw-col-span-2 project-overview-description tc-content">
                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">
                                    <?php echo _l('project_description'); ?>
                                </dt>
                                <dd class="tw-mt-1 tw-space-y-5 tw-text-sm tw-text-neutral-900">
                                    <?php if (empty($project->description)) { ?>
                                        <p class="text-muted tw-mb-0">
                                            <?php echo _l('no_description_project'); ?>
                                        </p>
                                    <?php } ?>
                                    <?php echo check_for_links($project->description); ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <?php hooks()->do_action('admin_project_overview_end_of_project_overview_left', $project) ?>
    </div>
    <div class="col-md-6 project-overview-right">
        <div class="row">
            <div class="col-md-<?php echo ($project->deadline ? 6 : 12); ?> project-progress-bars">
                <div class="project-overview-open-tasks">
                    <div class="panel_s">
                        <div class="panel-body !tw-px-5 !tw-py-4">
                            <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">

                            <div class="panel-group" id="resourcesAccordion">
                                <!-- Box 1 -->
                                <?php
                                $getProjectResourceData = $this->db->get_where(db_prefix() . 'project_resource_data', ['project_id' => $project->id])->result();
                                $projectResourceMap = [];
                                foreach ($getProjectResourceData as $resource) {
                                    $projectResourceMap[$resource->slug] = $resource->url;
                                }
                                ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#cam_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">CAM Resources</span>
                                        </h4>
                                    </div>
                                    <div id="cam_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="onboarding_form_cam">Onboarding Form</label>
                                                <div class="input-group">
                                                    <input type="text" name="onboarding_form_cam" id="onboarding_form_cam" value="<?php echo isset($projectResourceMap['onboarding_form_cam']) ? $projectResourceMap['onboarding_form_cam'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="ghl_dashboard_cam">GHL Dashboard</label>
                                                <div class="input-group">
                                                    <input type="text" name="ghl_dashboard_cam" id="ghl_dashboard_cam" value="<?php echo isset($projectResourceMap['ghl_dashboard_cam']) ? $projectResourceMap['ghl_dashboard_cam'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="scope_doc_cam">Scope Doc</label>
                                                <div class="input-group">
                                                    <input type="text" name="scope_doc_cam" id="scope_doc_cam" value="<?php echo isset($projectResourceMap['scope_doc_cam']) ? $projectResourceMap['scope_doc_cam'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Box 2 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#web_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">Web Resources</span>
                                        </h4>
                                    </div>
                                    <div id="web_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="dev_site_url">Dev Site URL</label>
                                                <div class="input-group">
                                                    <input type="text" name="dev_site_url" id="dev_site_url" value="<?php echo isset($projectResourceMap['dev_site_url']) ? $projectResourceMap['dev_site_url'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="page_copy_doc_web">Page Copy Doc</label>
                                                <div class="input-group">
                                                    <input type="text" name="page_copy_doc_web" id="page_copy_doc_web" value="<?php echo isset($projectResourceMap['page_copy_doc_web']) ? $projectResourceMap['page_copy_doc_web'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="site_sheet_web">Sitemap Sheet</label>
                                                <div class="input-group">
                                                    <input type="text" name="site_sheet_web" id="site_sheet_web" value="<?php echo isset($projectResourceMap['site_sheet_web']) ? $projectResourceMap['site_sheet_web'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="dns_sheet_web">DNS Sheet</label>
                                                <div class="input-group">
                                                    <input type="text" name="dns_sheet_web" id="dns_sheet_web" value="<?php echo isset($projectResourceMap['dns_sheet_web']) ? $projectResourceMap['dns_sheet_web'] : 'https://docs.google.com/spreadsheets/d/1IZio2ttg4l1uthtuzG99HQyzljfsRke1htj5INV1e4M/edit?usp=sharing'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="drive_folder_web">Drive Folder</label>
                                                <div class="input-group">
                                                    <input type="text" name="drive_folder_web" id="drive_folder_web" value="<?php echo isset($projectResourceMap['drive_folder_web']) ? $projectResourceMap['drive_folder_web'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="current_website_web">Current Website</label>
                                                <div class="input-group">
                                                    <input type="text" name="current_website_web" id="current_website_web" value="<?php echo isset($projectResourceMap['current_website_web']) ? $projectResourceMap['current_website_web'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- box 3 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#seo_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">SEO Resources</span>
                                        </h4>
                                    </div>
                                    <div id="seo_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="content_presentation">Content Presentation</label>
                                                <div class="input-group">
                                                    <input type="text" name="content_presentation" id="content_presentation" value="<?php echo isset($projectResourceMap['content_presentation']) ? $projectResourceMap['content_presentation'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="audit_sheet">Audit Sheet</label>
                                                <div class="input-group">
                                                    <input type="text" name="audit_sheet" id="audit_sheet" value="<?php echo isset($projectResourceMap['audit_sheet']) ? $projectResourceMap['audit_sheet'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- box 4 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#content_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">Content Resources</span>
                                        </h4>
                                    </div>
                                    <div id="content_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="content_strategy_presentation">Content Strategy Presentation</label>
                                                <div class="input-group">
                                                    <input type="text" name="content_strategy_presentation" id="content_strategy_presentation" value="<?php echo isset($projectResourceMap['content_strategy_presentation']) ? $projectResourceMap['content_strategy_presentation'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="master_content_calendar">Master Content Calendar</label>
                                                <div class="input-group">
                                                    <input type="text" name="master_content_calendar" id="master_content_calendar" value="<?php echo isset($projectResourceMap['master_content_calendar']) ? $projectResourceMap['master_content_calendar'] : 'https://airtable.com/appwWcRy7scxY9WT0/shrekMsC1wd9WdN5w'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="content_creation_form">Content Creation Form</label>
                                                <div class="input-group">
                                                    <input type="text" name="content_creation_form" id="content_creation_form" value="<?php echo isset($projectResourceMap['content_creation_form']) ? $projectResourceMap['content_creation_form'] : 'https://docs.google.com/forms/d/e/1FAIpQLSd8NDOPPogAFQsmp5LasA_9je_Rv1RI57JAWIWBB5g6ZcvB3A/viewform'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="content_pipeline_backlinks">Content Pipeline Backlinks</label>
                                                <div class="input-group">
                                                    <input type="text" name="content_pipeline_backlinks" id="content_pipeline_backlinks" value="<?php echo isset($projectResourceMap['content_pipeline_backlinks']) ? $projectResourceMap['content_pipeline_backlinks'] : 'https://docs.google.com/spreadsheets/d/1PpTumUP54lUeRxHs4GwDcSqKlkESRFJFFOjcg8p1TXc/edit?usp=sharing'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="water_treatment_content_creation_form">Water Treatment Content Creation Form</label>
                                                <div class="input-group">
                                                    <input type="text" name="water_treatment_content_creation_form" id="water_treatment_content_creation_form" value="<?php echo isset($projectResourceMap['water_treatment_content_creation_form']) ? $projectResourceMap['water_treatment_content_creation_form'] : 'https://forms.gle/pN9upmqNLyPagrHYAWater Treatment Content'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="pipeline_backlinks">Pipeline Backlinks</label>
                                                <div class="input-group">
                                                    <input type="text" name="pipeline_backlinks" id="pipeline_backlinks" value="<?php echo isset($projectResourceMap['pipeline_backlinks']) ? $projectResourceMap['pipeline_backlinks'] : 'https://docs.google.com/spreadsheets/d/14sSKY2mSugRWf8g_WN-_BCzMs0yXb9oxBvgvh7wQvtI/edit?usp=sharing'; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="organic_content_strategy">Organic Content Strategy</label>
                                                <div class="input-group">
                                                    <input type="text" name="organic_content_strategy" id="organic_content_strategy" value="<?php echo isset($projectResourceMap['organic_content_strategy']) ? $projectResourceMap['organic_content_strategy'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- box 5 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#paid_ads_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">Paid Ads Resources</span>
                                        </h4>
                                    </div>
                                    <div id="paid_ads_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="strategy_doc_url_paid_ads">Strategy Doc</label>
                                                <div class="input-group">
                                                    <input type="text" name="strategy_doc_url_paid_ads" id="strategy_doc_url_paid_ads" value="<?php echo isset($projectResourceMap['strategy_doc_url_paid_ads']) ? $projectResourceMap['strategy_doc_url_paid_ads'] : ''; ?>"
                                                        class="form-control">
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- box 6 -->
                                <div class="panel panel-default">
                                    <div class="panel-heading toggle-header" data-toggle="collapse" data-target="#automation_resources" style="background-color: white;">
                                        <h4 class="panel-title d-flex align-items-center mb-0" style="display: flex; align-items: center; cursor: pointer;">
                                            <i class="fa fa-chevron-right rotate-icon mr-2" style="font-size: 15px; margin-right: 10px;"></i>
                                            <span style="color: black;">Automation Resources</span>
                                        </h4>
                                    </div>
                                    <div id="automation_resources" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="page_copy_doc_url_automation">Page Copy Doc URL</label>
                                                <div class="input-group">
                                                    <input type="text" name="page_copy_doc_url_automation" id="page_copy_doc_url_automation" value="<?php echo isset($projectResourceMap['page_copy_doc_url_automation']) ? $projectResourceMap['page_copy_doc_url_automation'] : ''; ?>"
                                                        class="form-control" disabled>
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="site_sheet_url_automation">Sitemap Sheet URL</label>
                                                <div class="input-group">
                                                    <input type="text" name="site_sheet_url_automation" id="site_sheet_url_automation" value="<?php echo isset($projectResourceMap['site_sheet_url_automation']) ? $projectResourceMap['site_sheet_url_automation'] : ''; ?>"
                                                        class="form-control" disabled>
                                                    <span class="input-group-btn">
                                                        <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- $('#project_active_days').modal('show'); -->
<div id="project_active_days" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Active Days</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- add span tag show day count  -->
                        <p>Active Days: <span id="project_active_days_count"></span></p>
                    </div>
                    <div class="col-md-6" style="text-align: end;">
                        <!-- add button   +  icon base -->
                        <a href="#" class="btn btn-primary" onclick="add_manual_timesheet(<?php echo e($project->id); ?>); return false;">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>

                <div id="project_active_days">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="tw-text-sm tw-bg-neutral-50">
                                        <?= _l('project_overview_start_time'); ?>
                                    </th>
                                    <th class="tw-text-sm tw-bg-neutral-50">
                                        <?= _l('project_overview_end_time'); ?>
                                    </th>
                                    <th class="tw-text-sm tw-bg-neutral-50">
                                        <?= _l('project_overview_action'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="project_overview_chart">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="project_timesheet_add_edit mtop20 hide">
                    <form class="project-overview-edit-timesheet-form" onsubmit="return false;">
                        <input type="hidden" name="timer_id" value="">
                        <input type="hidden" name="time_project_id" value="">
                        <div class="project-overview-start-end-time">
                            <div class="col-md-6">
                                <div class="form-group" app-field-wrapper="start_time"><label for="start_time" class="control-label">Start Time</label>
                                    <div class="input-group date"><input type="text" id="start_time" name="start_time" class="form-control datetimepicker" value="" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" app-field-wrapper="end_time"><label for="end_time" class="control-label">Pause Time</label>
                                    <div class="input-group date"><input type="text" id="end_time" name="end_time" class="form-control datetimepicker" value="" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-right" style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-default edit-manual-timesheet-cancel">Cancel</button>
                            <button class="btn btn-success edit-manual-timesheet-submit">
                                Save</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($project_overview_chart)) { ?>
    <script>
        var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;
    </script>
<?php } ?>