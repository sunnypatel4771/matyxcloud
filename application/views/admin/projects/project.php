<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), ['id' => 'project_form']); ?>

            <div class="col-md-8 col-md-offset-2">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo e($title); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_project" aria-controls="tab_project" role="tab" data-toggle="tab">
                                            <?php echo _l('project'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tab_settings" aria-controls="tab_settings" role="tab"
                                            data-toggle="tab">
                                            <?php echo _l('project_settings'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content tw-mt-3">
                            <div role="tabpanel" class="tab-pane active" id="tab_project">


                                <?php
                                $disable_type_edit = '';
                                if (isset($project)) {
                                    if ($project->billing_type != 1) {
                                        if (total_rows(db_prefix() . 'tasks', ['rel_id' => $project->id, 'rel_type' => 'project', 'billable' => 1, 'billed' => 1]) > 0) {
                                            $disable_type_edit = 'disabled';
                                        }
                                    }
                                }
                                ?>
                                <?php $value = (isset($project) ? $project->name : ''); ?>
                                <?php echo render_input('name', 'project_name', $value); ?>
                                <div class="form-group select-placeholder">
                                    <label for="clientid"
                                        class="control-label"><?php echo _l('project_customer'); ?></label>
                                    <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                        class="ajax-search"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php $selected = (isset($project) ? $project->clientid : '');
                                        if ($selected == '') {
                                            $selected = (isset($customer_id) ? $customer_id : '');
                                        }
                                        if ($selected != '') {
                                            $rel_data = get_relation_data('customer', $selected);
                                            $rel_val  = get_relation_values($rel_data, 'customer');
                                            echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox checkbox-success">
                                        <input type="checkbox" <?php if ((isset($project) && $project->progress_from_tasks == 1) || !isset($project)) {
                                                                    echo 'checked';
                                                                } ?> name="progress_from_tasks" id="progress_from_tasks">
                                        <label
                                            for="progress_from_tasks"><?php echo _l('calculate_progress_through_tasks'); ?></label>
                                    </div>
                                </div>
                                <?php
                                if (isset($project) && $project->progress_from_tasks == 1) {
                                    $value = $this->projects_model->calc_progress_by_tasks($project->id);
                                } elseif (isset($project) && $project->progress_from_tasks == 0) {
                                    $value = $project->progress;
                                } else {
                                    $value = 0;
                                }
                                ?>
                                <label for=""><?php echo _l('project_progress'); ?> <span
                                        class="label_progress"><?php echo e($value); ?>%</span></label>
                                <?php echo form_hidden('progress', $value); ?>
                                <div class="project_progress_slider project_progress_slider_horizontal mbot15"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="billing_type"><?php echo _l('project_billing_type'); ?></label>
                                            <div class="clearfix"></div>
                                            <select name="billing_type" class="selectpicker" id="billing_type"
                                                data-width="100%" <?php echo $disable_type_edit; ?>
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""></option>
                                                <option value="1" <?php if (isset($project) && $project->billing_type == 1 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 1) {
                                                                        echo 'selected';
                                                                    } ?>><?php echo _l('project_billing_type_fixed_cost'); ?></option>
                                                <option value="2" <?php if (isset($project) && $project->billing_type == 2 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 2) {
                                                                        echo 'selected';
                                                                    } ?>><?php echo _l('project_billing_type_project_hours'); ?></option>
                                                <option value="3"
                                                    data-subtext="<?php echo _l('project_billing_type_project_task_hours_hourly_rate'); ?>" <?php if (isset($project) && $project->billing_type == 3 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 3) {
                                                                                                                                                echo 'selected';
                                                                                                                                            } ?>><?php echo _l('project_billing_type_project_task_hours'); ?></option>
                                            </select>
                                            <?php if ($disable_type_edit != '') {
                                                echo '<p class="text-danger">' . _l('cant_change_billing_type_billed_tasks_found') . '</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status"><?php echo _l('project_status'); ?></label>
                                            <div class="clearfix"></div>
                                            <select name="status" id="status" class="selectpicker" data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php foreach ($statuses as $status) { ?>
                                                    <option value="<?php echo e($status['id']); ?>" <?php if (!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])) {
                                                                                                        echo 'selected';
                                                                                                    } ?>><?php echo e($status['name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($project) && project_has_recurring_tasks($project->id)) { ?>
                                    <div class="alert alert-warning recurring-tasks-notice hide"></div>
                                <?php } ?>
                                <?php if (is_email_template_active('project-finished-to-customer')) { ?>
                                    <div class="form-group project_marked_as_finished hide">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="project_marked_as_finished_email_to_contacts"
                                                id="project_marked_as_finished_email_to_contacts">
                                            <label
                                                for="project_marked_as_finished_email_to_contacts"><?php echo _l('project_marked_as_finished_to_contacts'); ?></label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (isset($project)) { ?>
                                    <div class="form-group mark_all_tasks_as_completed hide">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="mark_all_tasks_as_completed"
                                                id="mark_all_tasks_as_completed">
                                            <label
                                                for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>
                                        </div>
                                    </div>
                                    <div class="notify_project_members_status_change hide">
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox" name="notify_project_members_status_change"
                                                id="notify_project_members_status_change">
                                            <label
                                                for="notify_project_members_status_change"><?php echo _l('notify_project_members_status_change'); ?></label>
                                        </div>
                                        <hr />
                                    </div>
                                <?php } ?>
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
                                <div id="project_cost" class="<?php echo e($input_field_hide_class_total_cost); ?>">
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
                                <div id="project_rate_per_hour"
                                    class="<?php echo e($input_field_hide_class_rate_per_hour); ?>">
                                    <?php $value = (isset($project) ? $project->project_rate_per_hour : ''); ?>
                                    <?php
                                    $input_disable = [];
                                    if ($disable_type_edit != '') {
                                        $input_disable['disabled'] = true;
                                    }
                                    ?>
                                    <?php echo render_input('project_rate_per_hour', 'project_rate_per_hour', $value, 'number', $input_disable); ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo render_input('estimated_hours', 'estimated_hours', isset($project) ? $project->estimated_hours : '', 'number'); ?>
                                    </div>
                                    <div class="col-md-6">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                                        <?php echo render_date_input('start_date', 'project_start_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($project) ? _d($project->deadline) : ''); ?>
                                        <?php echo render_date_input('deadline', 'project_deadline', $value); ?>
                                    </div>
                                </div>
                                <?php if (isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                                    <?php echo render_datetime_input('date_finished', 'project_completed_date', _dt($project->date_finished)); ?>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        <?php echo _l('tags'); ?></label>
                                    <input type="text" class="tagsinput" id="tags" name="tags"
                                        value="<?php echo (isset($project) ? prep_tags_input(get_tags_in($project->id, 'project')) : ''); ?>"
                                        data-role="tagsinput">
                                </div>
                                <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>
                                <?php echo render_custom_fields('projects', $rel_id_custom_field); ?>




                                <?php
                                $CI = &get_instance();
                                $CI->load->model('staff_model');
                                $CI->load->model('clients_model');
                         
                             





                                $staff = $CI->staff_model->get();

                                $client_data = null;
                                if (isset($customer_id)) {
                                    if ($customer_id > 0) {
                                        //get client data
                                        $client_data = $CI->clients_model->get($customer_id);
                                    }
                                }


                                if (isset($project)) {
                                    $client_data = $project;
                                }

                                ?>

                                <div class="cutome_select <?php if (!isset($customer_id) && !isset($project)) echo 'hide'; ?>">
                                    <div class="row ">
                                        <div class="col-md-12">

                                            <?php echo render_select('cam_id', $staff, ['staffid', ['firstname', 'lastname']], 'cam_id', $client_data->cam_id ?? '', []); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php echo render_select('optimizer_id', $staff, ['staffid', ['firstname', 'lastname']], 'optimizer_id', $client_data->optimizer_id ?? '', []); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php echo render_select('organic_social_id', $staff, ['staffid', ['firstname', 'lastname']], 'organic_social_id',  $client_data->organic_social_id ?? '', []); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php echo render_select('seo_lead_id', $staff, ['staffid', ['firstname',  'lastname']], 'seo_lead_id', $client_data->seo_lead_id ?? '', []); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php echo render_select('sale_rep_id', $staff, ['staffid', ['firstname', 'lastname']], 'sale_rep_id', $client_data->sale_rep_id ?? '', []); ?>
                                        </div>
                                    </div>
                                </div>



                                <p class="bold"><?php echo _l('project_description'); ?></p>
                                <?php $contents = '';
                                if (isset($project)) {
                                    $contents           = $project->description;
                                } ?>
                                <?php echo render_textarea('description', '', $contents, [], [], '', 'tinymce'); ?>

                                <?php if (isset($estimate)) { ?>
                                    <hr class="hr-panel-separator" />
                                    <h5 class="font-medium"><?php echo _l('estimate_items_convert_to_tasks') ?></h5>
                                    <input type="hidden" name="estimate_id" value="<?php echo $estimate->id ?>">
                                    <div class="row">
                                        <?php foreach ($estimate->items as $item) { ?>
                                            <div class="col-md-8 border-right">
                                                <div class="checkbox mbot15">
                                                    <input type="checkbox" name="items[]" value="<?php echo $item['id'] ?>"
                                                        checked id="item-<?php echo $item['id'] ?>">
                                                    <label for="item-<?php echo $item['id'] ?>">
                                                        <h5 class="no-mbot no-mtop text-uppercase">
                                                            <?php echo $item['description'] ?>
                                                        </h5>
                                                        <span class="text-muted"><?php echo $item['long_description'] ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div data-toggle="tooltip"
                                                    title="<?php echo _l('task_single_assignees_select_title'); ?>">
                                                    <?php echo render_select('items_assignee[]', $staff, ['staffid', ['firstname', 'lastname']], '', get_staff_user_id(), ['data-actions-box' => true], [], '', '', false); ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <hr class="hr-panel-separator" />

                                <?php if (is_email_template_active('assigned-to-project')) { ?>
                                    <div class="checkbox checkbox-primary tw-mb-0">
                                        <input type="checkbox" name="send_created_email" id="send_created_email">
                                        <label
                                            for="send_created_email"><?php echo _l('project_send_created_email'); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_settings">
                                <div id="project-settings-area">
                                    <div class="form-group select-placeholder">
                                        <label for="contact_notification" class="control-label">
                                            <span class="text-danger">*</span>
                                            <?php echo _l('projects_send_contact_notification'); ?>
                                        </label>
                                        <select name="contact_notification" id="contact_notification"
                                            class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            required>
                                            <?php
                                            $options = [
                                                ['id' => 1, 'name' => _l('project_send_all_contacts_with_notifications_enabled')],
                                                ['id' => 2, 'name' => _l('project_send_specific_contacts_with_notification')],
                                                ['id' => 0, 'name' => _l('project_do_not_send_contacts_notifications')],
                                            ];
                                            foreach ($options as $option) { ?>
                                                <option value="<?php echo e($option['id']); ?>" <?php if ((isset($project) && $project->contact_notification == $option['id'])) {
                                                                                                    echo ' selected';
                                                                                                } ?>><?php echo e($option['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <!-- hide class -->
                                    <div class="form-group select-placeholder <?php echo (isset($project) && $project->contact_notification == 2) ? '' : 'hide' ?>"
                                        id="notify_contacts_wrapper">
                                        <label for="notify_contacts" class="control-label"><span
                                                class="text-danger">*</span>
                                            <?php echo _l('project_contacts_to_notify') ?></label>
                                        <select name="notify_contacts[]" data-id="notify_contacts" id="notify_contacts"
                                            class="ajax-search" data-width="100%" data-live-search="true"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            multiple>
                                            <?php
                                            $notify_contact_ids = isset($project) ? unserialize($project->notify_contacts) : [];
                                            foreach ($notify_contact_ids as $contact_id) {
                                                $rel_data = get_relation_data('contact', $contact_id);
                                                $rel_val  = get_relation_values($rel_data, 'contact');
                                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php foreach ($settings as $setting) {
                                        $checked = ' checked';
                                        if (isset($project)) {
                                            if ($project->settings->{$setting} == 0) {
                                                $checked = '';
                                            }
                                        } else {
                                            foreach ($last_project_settings as $last_setting) {
                                                if ($setting == $last_setting['name']) {
                                                    // hide_tasks_on_main_tasks_table is not applied on most used settings to prevent confusions
                                                    if ($last_setting['value'] == 0 || $last_setting['name'] == 'hide_tasks_on_main_tasks_table') {
                                                        $checked = '';
                                                    }
                                                }
                                            }
                                            if (count($last_project_settings) == 0 && $setting == 'hide_tasks_on_main_tasks_table') {
                                                $checked = '';
                                            }
                                        } ?>
                                        <?php if ($setting != 'available_features') { ?>
                                            <div class="checkbox">
                                                <input type="checkbox" name="settings[<?php echo e($setting); ?>]"
                                                    <?php echo e($checked); ?> id="<?php echo e($setting); ?>">
                                                <label for="<?php echo e($setting); ?>">
                                                    <?php if ($setting == 'hide_tasks_on_main_tasks_table') { ?>
                                                        <?php echo _l('hide_tasks_on_main_tasks_table'); ?>
                                                    <?php } else { ?>
                                                        <?php echo e(_l('project_allow_client_to', _l('project_setting_' . $setting))); ?>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                        <?php } else { ?>
                                            <div class="form-group mtop15 select-placeholder project-available-features">
                                                <label for="available_features"><?php echo _l('visible_tabs'); ?></label>
                                                <select name="settings[<?php echo e($setting); ?>][]" id="<?php echo e($setting); ?>"
                                                    multiple="true" class="selectpicker" id="available_features"
                                                    data-width="100%" data-actions-box="true" data-hide-disabled="true">
                                                    <?php foreach (get_project_tabs_admin() as $tab) {
                                                        $selected = '';
                                                        if (isset($tab['collapse'])) { ?>
                                                            <optgroup label="<?php echo e($tab['name']); ?>">
                                                                <?php foreach ($tab['children'] as $tab_dropdown) {
                                                                    $selected = '';
                                                                    if (isset($project) && (
                                                                        (isset($project->settings->available_features[$tab_dropdown['slug']])
                                                                            && $project->settings->available_features[$tab_dropdown['slug']] == 1)
                                                                        || !isset($project->settings->available_features[$tab_dropdown['slug']])
                                                                    )) {
                                                                        $selected = ' selected';
                                                                    } elseif (!isset($project) && count($last_project_settings) > 0) {
                                                                        foreach ($last_project_settings as $last_project_setting) {
                                                                            if ($last_project_setting['name'] == $setting) {
                                                                                if (
                                                                                    isset($last_project_setting['value'][$tab_dropdown['slug']])
                                                                                    && $last_project_setting['value'][$tab_dropdown['slug']] == 1
                                                                                ) {
                                                                                    $selected = ' selected';
                                                                                }
                                                                            }
                                                                        }
                                                                    } elseif (!isset($project)) {
                                                                        $selected = ' selected';
                                                                    } ?>
                                                                    <option value="<?php echo e($tab_dropdown['slug']); ?>"
                                                                        <?php echo e($selected); ?><?php if (isset($tab_dropdown['linked_to_customer_option']) && is_array($tab_dropdown['linked_to_customer_option']) && count($tab_dropdown['linked_to_customer_option']) > 0) { ?>
                                                                        data-linked-customer-option="<?php echo implode(',', $tab_dropdown['linked_to_customer_option']); ?>"
                                                                        <?php } ?>><?php echo e($tab_dropdown['name']); ?></option>
                                                                <?php
                                                                } ?>
                                                            </optgroup>
                                                        <?php } else {
                                                            if (isset($project) && (
                                                                (isset($project->settings->available_features[$tab['slug']])
                                                                    && $project->settings->available_features[$tab['slug']] == 1)
                                                                || !isset($project->settings->available_features[$tab['slug']])
                                                            )) {
                                                                $selected = ' selected';
                                                            } elseif (!isset($project) && count($last_project_settings) > 0) {
                                                                foreach ($last_project_settings as $last_project_setting) {
                                                                    if ($last_project_setting['name'] == $setting) {
                                                                        if (
                                                                            isset($last_project_setting['value'][$tab['slug']])
                                                                            && $last_project_setting['value'][$tab['slug']] == 1
                                                                        ) {
                                                                            $selected = ' selected';
                                                                        }
                                                                    }
                                                                }
                                                            } elseif (!isset($project)) {
                                                                $selected = ' selected';
                                                            } ?>
                                                            <option value="<?php echo e($tab['slug']); ?>" <?php if ($tab['slug'] == 'project_overview') {
                                                                                                                echo ' disabled selected';
                                                                                                            } ?> <?php echo e($selected); ?>
                                                                <?php if (isset($tab['linked_to_customer_option']) && is_array($tab['linked_to_customer_option']) && count($tab['linked_to_customer_option']) > 0) { ?>
                                                                data-linked-customer-option="<?php echo implode(',', $tab['linked_to_customer_option']); ?>"
                                                                <?php } ?>>
                                                                <?php echo e($tab['name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        <hr class="tw-my-3 -tw-mx-8" />
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" data-form="#project_form" class="btn btn-primary" autocomplete="off"
                            data-loading-text="<?php echo _l('wait_text'); ?>">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    <?php if (isset($project)) { ?>
        var original_project_status = '<?php echo e($project->status); ?>';
    <?php } ?>

    $(function() {

        $contacts_select = $('#notify_contacts'),
            $contacts_wrapper = $('#notify_contacts_wrapper'),
            $clientSelect = $('#clientid'),
            $contact_notification_select = $('#contact_notification');

        init_ajax_search('contacts', $contacts_select, {
            rel_id: $contacts_select.val(),
            type: 'contacts',
            extra: {
                client_id: function() {
                    return $clientSelect.val();
                }
            }
        });

        if ($clientSelect.val() == '') {
            $contacts_select.prop('disabled', true);
            $contacts_select.selectpicker('refresh');
        } else {
            $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
        }

        $clientSelect.on('changed.bs.select', function() {
            if ($clientSelect.selectpicker('val') == '') {
                $contacts_select.prop('disabled', true);
            } else {
                $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
                $contacts_select.prop('disabled', false);
            }
            deselect_ajax_search($contacts_select[0]);
            $contacts_select.find('option').remove();
            $contacts_select.selectpicker('refresh');


            if ($clientSelect.selectpicker('val') == '') {
                $('.cutome_select').addClass('hide');
            } else {
                $('.cutome_select').removeClass('hide');

                $.get(admin_url + 'task_customize/get_customer_details/' + $clientSelect.selectpicker('val'), function(data) {
                            if (data) {
                                var data = JSON.parse(data);
                                if (!$.isEmptyObject(data)) {
                                    $('#cam_id').val(data.cam_id);
                                    $('#cam_id').selectpicker('refresh');
                                    $('#sale_rep_id').val(data.sale_rep_id);
                                    $('#sale_rep_id').selectpicker('refresh');
                                    $('#optimizer_id').val(data.optimizer_id);
                                    $('#optimizer_id').selectpicker('refresh');
                                    $('#organic_social_id').val(data.organic_social_id);
                                    $('#organic_social_id').selectpicker('refresh');
                                    $('#seo_lead_id').val(data.seo_lead_id);
                                    $('#seo_lead_id').selectpicker('refresh');

                                }
                            }
                        });
            }
        });

        $contact_notification_select.on('changed.bs.select', function() {
            if ($contact_notification_select.selectpicker('val') == 2) {
                $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
                $contacts_wrapper.removeClass('hide');
            } else {
                $contacts_wrapper.addClass('hide');
                deselect_ajax_search($contacts_select[0]);
            }
        });

        $('select[name="billing_type"]').on('change', function() {
            var type = $(this).val();
            if (type == 1) {
                $('#project_cost').removeClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            } else if (type == 2) {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').removeClass('hide');
            } else {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            }
        });

        appValidateForm($('form'), {
            name: 'required',
            clientid: 'required',
            start_date: 'required',
            billing_type: 'required',
            'notify_contacts[]': {
                required: {
                    depends: function() {
                        return !$contacts_wrapper.hasClass('hide');
                    }
                }
            },
        });

        $('select[name="status"]').on('change', function() {
            var status = $(this).val();
            var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
            var notify_project_members_status_change = $('.notify_project_members_status_change');
            mark_all_tasks_completed.removeClass('hide');
            if (typeof(original_project_status) != 'undefined') {
                if (original_project_status != status) {

                    mark_all_tasks_completed.removeClass('hide');
                    notify_project_members_status_change.removeClass('hide');

                    if (status == 4 || status == 5 || status == 3) {
                        $('.recurring-tasks-notice').removeClass('hide');
                        var notice = "<?php echo _l('project_changing_status_recurring_tasks_notice'); ?>";
                        notice = notice.replace('{0}', $(this).find('option[value="' + status + '"]').text()
                            .trim());
                        $('.recurring-tasks-notice').html(notice);
                        $('.recurring-tasks-notice').append(
                            '<input type="hidden" name="cancel_recurring_tasks" value="true">');
                        mark_all_tasks_completed.find('input').prop('checked', true);
                    } else {
                        $('.recurring-tasks-notice').html('').addClass('hide');
                        mark_all_tasks_completed.find('input').prop('checked', false);
                    }
                } else {
                    mark_all_tasks_completed.addClass('hide');
                    mark_all_tasks_completed.find('input').prop('checked', false);
                    notify_project_members_status_change.addClass('hide');
                    $('.recurring-tasks-notice').html('').addClass('hide');
                }
            }

            if (status == 4) {
                $('.project_marked_as_finished').removeClass('hide');
            } else {
                $('.project_marked_as_finished').addClass('hide');
                $('.project_marked_as_finished').prop('checked', false);
            }
        });

        $('form').on('submit', function() {
            $('select[name="billing_type"]').prop('disabled', false);
            $('#available_features,#available_features option').prop('disabled', false);
            $('input[name="project_rate_per_hour"]').prop('disabled', false);
        });

        var progress_input = $('input[name="progress"]');
        var progress_from_tasks = $('#progress_from_tasks');
        var progress = progress_input.val();

        $('.project_progress_slider').slider({
            min: 0,
            max: 100,
            value: progress,
            disabled: progress_from_tasks.prop('checked'),
            slide: function(event, ui) {
                progress_input.val(ui.value);
                $('.label_progress').html(ui.value + '%');
            }
        });

        progress_from_tasks.on('change', function() {
            var _checked = $(this).prop('checked');
            $('.project_progress_slider').slider({
                disabled: _checked
            });
        });

        $('#project-settings-area input').on('change', function() {
            if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == false) {
                $('#create_tasks').prop('checked', false).prop('disabled', true);
                $('#edit_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_comments').prop('checked', false).prop('disabled', true);
                $('#comment_on_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_attachments').prop('checked', false).prop('disabled', true);
                $('#view_task_checklist_items').prop('checked', false).prop('disabled', true);
                $('#upload_on_tasks').prop('checked', false).prop('disabled', true);
                $('#view_task_total_logged_time').prop('checked', false).prop('disabled', true);
            } else if ($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == true) {
                $('#create_tasks').prop('disabled', false);
                $('#edit_tasks').prop('disabled', false);
                $('#view_task_comments').prop('disabled', false);
                $('#comment_on_tasks').prop('disabled', false);
                $('#view_task_attachments').prop('disabled', false);
                $('#view_task_checklist_items').prop('disabled', false);
                $('#upload_on_tasks').prop('disabled', false);
                $('#view_task_total_logged_time').prop('disabled', false);
            }
        });

        // Auto adjust customer permissions based on selected project visible tabs
        // Eq Project creator disable TASKS tab, then this function will auto turn off customer project option Allow customer to view tasks

        $('#available_features').on('change', function() {
            $("#available_features option").each(function() {
                if ($(this).data('linked-customer-option') && !$(this).is(':selected')) {
                    var opts = $(this).data('linked-customer-option').split(',');
                    for (var i = 0; i < opts.length; i++) {
                        var project_option = $('#' + opts[i]);
                        project_option.prop('checked', false);
                        if (opts[i] == 'view_tasks') {
                            project_option.trigger('change');
                        }
                    }
                }
            });
        });
        $("#view_tasks").trigger('change');
        <?php if (!isset($project)) { ?>
            $('#available_features').trigger('change');
        <?php } ?>
    });
</script>
</body>

</html>