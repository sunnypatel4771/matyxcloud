<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">

    <div class="col-md-6 project-overview-left">

        <div class="panel_s">

            <div class="panel-body">

                <div class="row">

                    <div class="col-md-12">

                        <p class="project-info tw-mb-0 tw-font-medium tw-text-base tw-tracking-tight">

                            <?php echo _l('project_progress_text'); ?> <span class="tw-text-neutral-500"><?php echo $percent; ?>%</span>

                        </p>

                        <div class="progress progress-bar-mini">

                            <div class="progress-bar progress-bar-success no-percent-text not-dynamic"

                                 role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0"

                                 aria-valuemax="100" style="width: <?php echo $percent; ?>%" data-percent="<?php echo $percent; ?>">

                            </div>

                        </div>

                        <?php hooks()->do_action('admin_area_after_project_progress') ?>

                        <hr class="hr-panel-separator" />

                    </div>


                    <div class="col-md-12">

                        <h4 class="tw-font-semibold tw-text-base tw-mb-4">

                            <?php echo _l('project_overview'); ?>

                            <?php if (has_permission('projects', '', 'edit')) { ?>

                                <a class="btn text-success project_overview_view" style="float: right" onclick="$('.project_overview_edit').removeClass('hide'); $('.project_overview_view').addClass('hide'); return false;"> <span class="fa fa-edit"></span> <?php echo _l('edit')?> </a>
                                <a class="btn text-danger project_overview_edit hide" style="float: right" onclick="$('.project_overview_edit').addClass('hide'); $('.project_overview_view').removeClass('hide'); return false;"> <span class="fa fa-times"></span> <?php echo _l('cancel')?> </a>

                            <?php } ?>

                        </h4>

                        <dl class="tw-grid tw-grid-cols-1 tw-gap-x-4 tw-gap-y-5 sm:tw-grid-cols-2 project_overview_view">

                            <div class="sm:tw-col-span-1 project-overview-id">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project'); ?> <?php echo _l('the_number_sign'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900"><?php echo $project->id; ?></dd>

                            </div>



                            <div class="sm:tw-col-span-1 project-overview-customer">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project_customer'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                    <a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>">

                                        <?php echo $project->client_data->company; ?>

                                    </a>

                                </dd>

                            </div>



                            <?php if (has_permission('projects', '', 'edit')) { ?>

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

                                                <?php echo  app_format_money($project->project_cost, $currency); ?>

                                            </dd>

                                        <?php  } else { ?>

                                            <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                                <?php echo _l('project_rate_per_hour'); ?>

                                            </dt>

                                            <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                                <?php echo app_format_money($project->project_rate_per_hour, $currency); ?>

                                            </dd>

                                        <?php } ?>

                                    </div>

                                <?php } } ?>



                            <div class="sm:tw-col-span-1 project-overview-status">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project_status'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                    <?php echo $project_status['name']; ?>

                                </dd>

                            </div>



                            <div class="sm:tw-col-span-1 project-overview-date-created">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project_datecreated'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                    <?php echo _d($project->project_created); ?>

                                </dd>

                            </div>

                            <div class="sm:tw-col-span-1 project-overview-start-date">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project_start_date'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                    <?php echo _d($project->start_date); ?>

                                </dd>

                            </div>

                            <?php if ($project->deadline) { ?>

                                <div class="sm:tw-col-span-1 project-overview-deadline">

                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                        <?php echo _l('project_deadline'); ?>

                                    </dt>

                                    <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                        <?php echo _d($project->deadline); ?>

                                    </dd>

                                </div>

                            <?php } ?>



                            <?php if ($project->date_finished) { ?>

                                <div class="sm:tw-col-span-1 project-overview-date-finished">

                                    <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                        <?php echo _l('project_completed_date'); ?>

                                    </dt>

                                    <dd class="tw-mt-1 tw-text-sm text-success">

                                        <?php echo _dt($project->date_finished); ?>

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

                                        <?php echo str_replace('.', ':', $project->estimated_hours); ?>

                                    </dd>

                                </div>

                            <?php } ?>



                            <div class="sm:tw-col-span-1 project-overview-total-logged-hours">

                                <dt class="tw-text-sm tw-font-medium tw-text-neutral-500">

                                    <?php echo _l('project_overview_total_logged_hours'); ?>

                                </dt>

                                <dd class="tw-mt-1 tw-text-sm tw-text-neutral-900">

                                    <?php echo seconds_to_time_format($project_total_logged_time); ?>

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

                                            <?php echo ucfirst($field['name']); ?>

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

                        <!--
                        Edit section
                        -->
                        <?php if ( has_permission('projects', '', 'edit') ) { ?>

                            <div class="project_overview_edit hide">

                                <?php echo form_open( admin_url('project_kanban/Project_kanban/save_project') , [ 'id' => 'project_overview_form' ]); ?>

                                <input type="hidden" name="project_id" value="<?php echo $project->id?>" />
                                <input type="hidden" name="billing_type" value="<?php echo $project->billing_type?>" />

                                <div class="row">

                                    <div class="col-md-6">

                                        <?php

                                        $input_field_hide_class_total_cost = '';

                                        if (!isset($project)) {

                                            if ($auto_select_billing_type && $auto_select_billing_type->billing_type != 1 || !$auto_select_billing_type) {

                                                $input_field_hide_class_total_cost = 'hide';

                                            }

                                        } elseif (isset($project) && $project->billing_type != 1) {

                                            $input_field_hide_class_total_cost = 'hide';

                                        }

                                        ?>

                                        <div id="project_cost" class="<?php echo $input_field_hide_class_total_cost; ?>">

                                            <?php $value = (isset($project) ? $project->project_cost : ''); ?>

                                            <?php echo render_input('project_cost', 'project_total_cost', $value, 'number'); ?>

                                        </div>

                                        <?php

                                        $input_field_hide_class_rate_per_hour = '';

                                        if (!isset($project)) {

                                            if ($auto_select_billing_type && $auto_select_billing_type->billing_type != 2 || !$auto_select_billing_type) {

                                                $input_field_hide_class_rate_per_hour = 'hide';

                                            }

                                        } elseif (isset($project) && $project->billing_type != 2) {

                                            $input_field_hide_class_rate_per_hour = 'hide';

                                        }

                                        ?>

                                        <div id="project_rate_per_hour" class="<?php echo $input_field_hide_class_rate_per_hour; ?>">

                                            <?php $value = (isset($project) ? $project->project_rate_per_hour : ''); ?>

                                            <?php

                                            $input_disable = [];

                                            if ($disable_type_edit != '') {

                                                $input_disable['disabled'] = true;

                                            }

                                            ?>

                                            <?php echo render_input('project_rate_per_hour', 'project_rate_per_hour', $value, 'number', $input_disable); ?>

                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">

                                            <label for="status"><?php echo _l('project_status'); ?></label>

                                            <div class="clearfix"></div>

                                            <select name="status" id="status" class="selectpicker" data-width="100%"

                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                                                <?php foreach ($statuses as $status) { ?>

                                                    <option value="<?php echo $status['id']; ?>" <?php if (!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])) {

                                                        echo 'selected';

                                                    } ?>><?php echo $status['name']; ?></option>

                                                <?php } ?>

                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-md-12">

                                        <?php

                                        $selected = [];

                                        if (isset($project_members)) {

                                            foreach ($project_members as $member) {

                                                array_push($selected, $member['staff_id']);

                                            }

                                        } else {

                                            array_push($selected, get_staff_user_id());

                                        }

                                        echo render_select('project_members[]', $staff, ['staffid', ['firstname', 'lastname']], 'project_members', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);

                                        ?>

                                    </div>

                                    <div class="col-md-6">

                                        <?php $value = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>

                                        <?php echo render_date_input('start_date', 'project_start_date', $value); ?>

                                    </div>

                                    <div class="col-md-6">

                                        <?php $value = (isset($project) ? _d($project->deadline) : ''); ?>

                                        <?php echo render_date_input('deadline', 'project_deadline', $value); ?>

                                    </div>

                                    <div class="col-md-12">

                                        <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>

                                        <?php echo render_custom_fields('projects', $rel_id_custom_field); ?>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">

                                        <button type="submit" class="btn btn-primary" style="float: right"> <?php echo _l('save')?> </button>

                                    </div>

                                </div>

                                <?php echo form_close(); ?>

                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 project-overview-right">

        <div class="row">

            <div class="col-md-<?php echo($project->deadline ? 6 : 12); ?> project-progress-bars">

                <div class="project-overview-open-tasks">

                    <div class="panel_s">

                        <div class="panel-body !tw-px-5 !tw-py-4">

                            <div class="row">

                                <div class="col-md-9">

                                    <p class="bold text-dark tw-mb-2">

                                        <span dir="ltr"><?php echo $tasks_not_completed; ?> /

                                            <?php echo $total_tasks; ?></span>

                                        <?php echo _l('project_open_tasks'); ?>

                                    </p>

                                    <p class="text-muted bold tw-mb-0"><?php echo $tasks_not_completed_progress; ?>%</p>

                                </div>

                                <div class="col-md-3 text-right">

                                    <i class="fa-regular fa-check-circle<?php echo $tasks_not_completed_progress >= 100 ? ' text-success' : ' text-muted'; ?>"

                                       aria-hidden="true"></i>

                                </div>

                                <div class="col-md-12 mtop5">

                                    <div class="progress no-margin progress-bar-mini">

                                        <div class="progress-bar progress-bar-success no-percent-text not-dynamic"

                                             role="progressbar"

                                             aria-valuenow="<?php echo $tasks_not_completed_progress; ?>"

                                             aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $tasks_not_completed_progress; ?>%"

                                             data-percent="<?php echo $tasks_not_completed_progress; ?>">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <?php if ($project->deadline) { ?>

                <div class="col-md-6 project-progress-bars project-overview-days-left">

                    <div class="panel_s">

                        <div class="panel-body !tw-px-5 !tw-py-4">

                            <div class="row">

                                <div class="col-md-9">

                                    <p class="bold text-dark tw-mb-2">

                                    <span dir="ltr"><?php echo $project_days_left; ?> /

                                        <?php echo $project_total_days; ?></span>

                                        <?php echo _l('project_days_left'); ?>

                                    </p>

                                    <p class="text-muted bold tw-mb-0"><?php echo $project_time_left_percent; ?>%</p>

                                </div>

                                <div class="col-md-3 text-right">

                                    <i class="fa-regular fa-calendar-check<?php echo $project_time_left_percent >= 100 ? ' text-success' : ' text-muted'; ?>"

                                       aria-hidden="true"></i>

                                </div>

                                <div class="col-md-12 mtop5">

                                    <div class="progress no-margin progress-bar-mini">

                                        <div class="progress-bar<?php if ($project_time_left_percent == 0) {

                                            echo ' progress-bar-warning ';

                                        } else {

                                            echo ' progress-bar-success ';

                                        } ?>no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $project_time_left_percent; ?>"

                                             aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $project_time_left_percent; ?>%"

                                             data-percent="<?php echo $project_time_left_percent; ?>">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>



        <?php if (has_permission('projects', '', 'create')) { ?>

            <div class="row">

                <?php if ($project->billing_type == 3 || $project->billing_type == 2) { ?>

                    <div class="col-md-12 project-overview-logged-hours-finance">

                        <div class="panel_s !tw-mb-3">

                            <div class="panel-body !tw-px-5 !tw-py-4">

                                <div class="row">

                                    <div class="col-md-3">

                                        <?php

                                        $data = $this->projects_model->total_logged_time_by_billing_type($project->id);

                                        ?>

                                        <p class="tw-mb-0 text-muted"><?php echo _l('project_overview_logged_hours'); ?> <span

                                                class="bold"><?php echo $data['logged_time']; ?></span></p>

                                        <p class="bold font-medium tw-mb-0">

                                            <?php echo app_format_money($data['total_money'], $currency); ?></p>

                                    </div>

                                    <div class="col-md-3">

                                        <?php

                                        $data = $this->projects_model->data_billable_time($project->id);

                                        ?>

                                        <p class="tw-mb-0 text-info"><?php echo _l('project_overview_billable_hours'); ?> <span

                                                class="bold"><?php echo $data['logged_time'] ?></span></p>

                                        <p class="bold font-medium tw-mb-0">

                                            <?php echo app_format_money($data['total_money'], $currency); ?></p>

                                    </div>

                                    <div class="col-md-3">

                                        <?php

                                        $data = $this->projects_model->data_billed_time($project->id);

                                        ?>

                                        <p class="tw-mb-0 text-success"><?php echo _l('project_overview_billed_hours'); ?> <span

                                                class="bold"><?php echo $data['logged_time']; ?></span></p>

                                        <p class="bold font-medium tw-mb-0">

                                            <?php echo app_format_money($data['total_money'], $currency); ?></p>

                                    </div>

                                    <div class="col-md-3">

                                        <?php

                                        $data = $this->projects_model->data_unbilled_time($project->id);

                                        ?>

                                        <p class="tw-mb-0 text-danger"><?php echo _l('project_overview_unbilled_hours'); ?>

                                            <span class="bold"><?php echo $data['logged_time']; ?></span>

                                        </p>

                                        <p class="bold font-medium tw-mb-0">

                                            <?php echo app_format_money($data['total_money'], $currency); ?></p>

                                    </div>

                                </div>

                            </div>

                        </div>



                    </div>

                <?php } ?>

            </div>

            <div class="row">

                <div class="col-md-12 project-overview-expenses-finance">

                    <div class="panel_s">

                        <div class="panel-body !tw-px-5 !tw-py-4">

                            <div class="row">

                                <div class="col-md-3">

                                    <p class="tw-mb-0 text-muted"><?php echo _l('project_overview_expenses'); ?></p>

                                    <p class="bold font-medium tw-mb-0">

                                        <?php echo app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'amount']), $currency); ?>

                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <p class="tw-mb-0 text-info"><?php echo _l('project_overview_expenses_billable'); ?></p>

                                    <p class="bold font-medium tw-mb-0">

                                        <?php echo app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'billable' => 1], 'field' => 'amount']), $currency); ?>

                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <p class="tw-mb-0 text-success"><?php echo _l('project_overview_expenses_billed'); ?>

                                    </p>

                                    <p class="bold font-medium tw-mb-0">

                                        <?php echo app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid !=' => 'NULL', 'billable' => 1], 'field' => 'amount']), $currency); ?>

                                    </p>

                                </div>

                                <div class="col-md-3">

                                    <p class="tw-mb-0 text-danger"><?php echo _l('project_overview_expenses_unbilled'); ?>

                                    </p>

                                    <p class="bold font-medium tw-mb-0">

                                        <?php echo app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid IS NULL', 'billable' => 1], 'field' => 'amount']), $currency); ?>

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        <?php } ?>

        <div class="project-overview-timesheets-chart">

            <div class="clearfix"></div>

            <div class="panel_s">

                <div class="panel-body !tw-px-5 !tw-py-4">

                    <canvas id="timesheetsChart" style="max-height:300px;" width="300" height="300"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>
