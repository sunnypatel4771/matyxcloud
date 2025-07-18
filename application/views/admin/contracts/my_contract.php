<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $isSignedOrMarkedSigned = isset($contract) && ($contract->signed == 1 || $contract->marked_as_signed == 1); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            if (isset($contract) && $contract->signed == 1) { ?>
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <?php echo  _l('contract_signed_not_all_fields_editable'); ?>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-5 left-column">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('contract_information') ?>
                    <?php
                    if (isset($contract) && $contract->trash > 0) {
                        echo '<div class="label label-default"><span>' . _l('contract_trash') . '</span></div>';
                    }
                    ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string(), ['id' => 'contract-form']); ?>
                        <div class="form-group">
                            <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                                <input type="checkbox" id="trash" name="trash"
                                    <?php echo $contract->trash ?? false == 1 ? 'checked' : ''; ?>>
                                <label for="trash"><i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                        data-placement="right" title="<?php echo _l('contract_trash_tooltip'); ?>"></i>
                                    <?php echo _l('contract_trash'); ?></label>
                            </div>
                            <div class="checkbox checkbox-primary checkbox-inline">
                                <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client"
                                    <?php echo $contract->not_visible_to_client ?? false == 1 ? 'checked' : ''; ?>>
                                <label for="not_visible_to_client">
                                    <?php echo _l('contract_not_visible_to_client'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group select-placeholder f_client_id">
                            <label for="clientid" class="control-label"><span class="text-danger">*
                                </span><?php echo _l('contract_client_string'); ?></label>
                            <select id="clientid" name="client" data-live-search="true" data-width="100%"
                                class="ajax-search"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                <?php echo isset($contract) && $isSignedOrMarkedSigned ? ' disabled' : ''; ?>>
                                <?php $selected = (isset($contract) ? $contract->client : '');
                                if ($selected == '') {
                                    $selected = (isset($customer_id) ? $customer_id : '');
                                }
                                if ($selected != '') {
                                    $rel_data = get_relation_data('customer', $selected);
                                    $rel_val  = get_relation_values($rel_data, 'customer');
                                    echo '<option value="' . $rel_val['id'] . '" selected>' . e($rel_val['name']) . '</option>';
                                } ?>
                            </select>
                        </div>
                        <?php if (isset($_GET['project_id'])) { ?>
                            <div
                                class="form-group select-placeholder projects-wrapper">
                                <label for="project_id">
                                    <?= _l('project'); ?>
                                </label>

                                <div id="project_ajax_search_wrapper">
                                    <select name="project_id" id="project_id" class="projects ajax-search ays-ignore"
                                        data-live-search="true" data-width="100%"
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                        <?= isset($contract) && $isSignedOrMarkedSigned == 1 ? ' disabled' : ''; ?>>

                                        <option
                                            value="<?= $_GET['project_id']; ?>"
                                            selected>
                                            <?= e(get_project_name_by_id($_GET['project_id'])); ?>
                                        </option>

                                    </select>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-group select-placeholder projects-wrapper<?php if ((!isset($contract)) || (isset($contract) && !customer_has_projects($contract->client))) {
                                                                                            echo ' hide';
                                                                                        } ?>">
                                <label for="project_id"><?php echo _l('project'); ?></label>
                                <div id="project_ajax_search_wrapper">
                                    <select name="project_id" id="project_id" class="projects ajax-search ays-ignore"
                                        data-live-search="true" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                        <?php echo isset($contract) && $isSignedOrMarkedSigned == 1 ? ' disabled' : ''; ?>>
                                        <?php
                                        if (isset($contract) && $contract->project_id) {
                                            echo '<option value="' . $contract->project_id . '" selected>' . e(get_project_name_by_id($contract->project_id)) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <?php $value = (isset($contract) ? $contract->subject : ''); ?>
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
                            title="<?php echo _l('contract_subject_tooltip'); ?>"></i>
                        <?php echo render_input('subject', 'contract_subject', $value); ?>
                        <div class="form-group">
                            <label for="contract_value"><?php echo _l('contract_value'); ?></label>
                            <div class="input-group" data-toggle="tooltip"
                                title="<?php echo isset($contract) && $isSignedOrMarkedSigned == 1 ? '' : _l('contract_value_tooltip'); ?>">
                                <input type="number" class="form-control" name="contract_value"
                                    value="<?php echo $contract->contract_value ?? ''; ?>"
                                    <?php echo isset($contract) && $isSignedOrMarkedSigned == 1 ? ' disabled' : ''; ?>>
                                <div class="input-group-addon">
                                    <?php echo e($base_currency->symbol); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $selected = (isset($contract) ? $contract->contract_type : '');
                        if (is_admin() || get_option('staff_members_create_inline_contract_types') == '1') {
                            echo render_select_with_input_group('contract_type', $types, ['id', 'name'], 'contract_type', $selected, '<div class="input-group-btn"><a href="#" class="btn btn-default" onclick="new_type();return false;"><i class="fa fa-plus"></i></a></div>');
                        } else {
                            echo render_select('contract_type', $types, ['id', 'name'], 'contract_type', $selected);
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($contract) ? _d($contract->datestart) : _d(date('Y-m-d'))); ?>
                                <?php echo render_date_input(
                                    'datestart',
                                    'contract_start_date',
                                    $value,
                                    isset($contract) && $isSignedOrMarkedSigned ? ['disabled' => true] : []
                                ); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($contract) ? _d($contract->dateend) : ''); ?>
                                <?php echo render_date_input(
                                    'dateend',
                                    'contract_end_date',
                                    $value,
                                    isset($contract) && $isSignedOrMarkedSigned ? ['disabled' => true] : []
                                ); ?>
                            </div>
                        </div>
                        <?php $value = (isset($contract) ? $contract->description : ''); ?>
                        <?php echo render_textarea('description', 'contract_description', $value, ['rows' => 10]); ?>
                        <?php $rel_id = (isset($contract) ? $contract->id : false); ?>
                        <?php echo render_custom_fields('contracts', $rel_id); ?>



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


                        if (isset($contract)) {
                            $client_data = $contract;
                        }

                        ?>

                        <div class="cutome_select <?php if (!isset($customer_id) && !isset($contract)) echo 'hide'; ?>">
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


                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-primary">
                                <?php echo _l('submit'); ?>
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>

            <?php if (isset($contract)) { ?>
                <div class="col-md-7 right-column">
                    <div class="sm:tw-flex sm:tw-justify-between sm:tw-items-center tw-mb-1 -tw-mt-px">
                        <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-truncate tw-max-w-lg">
                            <?php echo e($contract->subject); ?>
                        </h4>
                        <div>
                            <div class="_buttons tw-space-x-1 tw-flex tw-items-center">
                                <a href="<?php echo site_url('contract/' . $contract->id . '/' . $contract->hash); ?>"
                                    target="_blank">
                                    <?php echo _l('view_contract'); ?>
                                </a>
                                <div class="btn-group !tw-ml-3">
                                    <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"><i
                                            class="fa-regular fa-file-pdf"></i><?php echo is_mobile() ? 'PDF' : ''; ?> <span
                                            class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li class="hidden-xs"><a
                                                href="<?php echo admin_url('contracts/pdf/' . $contract->id . '?output_type=I'); ?>">
                                                <?php echo _l('view_pdf'); ?>
                                            </a>
                                        </li>
                                        <li class="hidden-xs">
                                            <a href="<?php echo admin_url('contracts/pdf/' . $contract->id . '?output_type=I'); ?>"
                                                target="_blank"><?php echo _l('view_pdf_in_new_window'); ?>
                                            </a>
                                        </li>
                                        <li><a
                                                href="<?php echo admin_url('contracts/pdf/' . $contract->id); ?>"><?php echo _l('download'); ?></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo admin_url('contracts/pdf/' . $contract->id . '?print=true'); ?>"
                                                target="_blank">
                                                <?php echo _l('print'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="#" class="btn btn-default" data-target="#contract_send_to_client_modal"
                                    data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip"
                                        data-title="<?php echo _l('contract_send_to_email'); ?>" data-placement="bottom">
                                        <i class="fa-regular fa-envelope"></i></span>
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default pull-left dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo _l('more'); ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="<?php echo site_url('contract/' . $contract->id . '/' . $contract->hash); ?>"
                                                target="_blank">
                                                <?php echo _l('view_contract'); ?>
                                            </a>
                                        </li>
                                        <?php if (!$isSignedOrMarkedSigned && staff_can('edit', 'contracts')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('contracts/mark_as_signed/' . $contract->id); ?>">
                                                    <?php echo _l('mark_as_signed'); ?>
                                                </a>
                                            </li>
                                        <?php } elseif ($contract->signed == 0 && $contract->marked_as_signed == 1 && staff_can('edit', 'contracts')) { ?>
                                            <li>
                                                <a
                                                    href="<?php echo admin_url('contracts/unmark_as_signed/' . $contract->id); ?>">
                                                    <?php echo _l('unmark_as_signed'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php hooks()->do_action('after_contract_view_as_client_link', $contract); ?>
                                        <?php if (staff_can('create',  'contracts')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('contracts/copy/' . $contract->id); ?>">
                                                    <?php echo _l('contract_copy'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($contract->signed == 1 && staff_can('delete',  'contracts')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('contracts/clear_signature/' . $contract->id); ?>"
                                                    class="_delete">
                                                    <?php echo _l('clear_signature'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (staff_can('delete',  'contracts')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('contracts/delete/' . $contract->id); ?>"
                                                    class="_delete">
                                                    <?php echo _l('delete'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="horizontal-scrollable-tabs preview-tabs-top panel-full-width-tabs">
                                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs contract-tab nav-tabs-horizontal mbot15" role="tablist">
                                        <li role="presentation" class="<?php if (!$this->input->get('tab') || $this->input->get('tab') == 'tab_content') {
                                                                            echo 'active';
                                                                        } ?>">
                                            <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
                                                <?php echo _l('contract_content'); ?>
                                            </a>
                                        </li>
                                        <li role="presentation" class="<?php if ($this->input->get('tab') == 'attachments') {
                                                                            echo 'active';
                                                                        } ?>">
                                            <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                                                <?php echo _l('contract_attachments'); ?>
                                                <?php if ($totalAttachments = count($contract->attachments)) { ?>
                                                    <span
                                                        class="badge attachments-indicator"><?php echo e($totalAttachments); ?></span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <li role="presentation">
                                            <a href="#tab_comments" aria-controls="tab_comments" role="tab"
                                                data-toggle="tab" onclick="get_contract_comments(); return false;">
                                                <?php echo _l('contract_comments'); ?>
                                                <?php
                                                $totalComments = total_rows(db_prefix() . 'contract_comments', 'contract_id=' . $contract->id)
                                                ?>
                                                <span
                                                    class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo e($totalComments); ?></span>
                                            </a>
                                        </li>
                                        <li role="presentation" class="<?php if ($this->input->get('tab') == 'renewals') {
                                                                            echo 'active';
                                                                        } ?>">
                                            <a href="#renewals" aria-controls="renewals" role="tab" data-toggle="tab">
                                                <?php echo _l('no_contract_renewals_history_heading'); ?>
                                                <?php if ($totalRenewals = count($contract_renewal_history)) { ?>
                                                    <span class="badge"><?php echo e($totalRenewals); ?></span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <li role="presentation" class="tab-separator">
                                            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab"
                                                onclick="init_rel_tasks_table(<?php echo e($contract->id); ?>,'contract'); return false;">
                                                <?php echo _l('tasks'); ?>
                                            </a>
                                        </li>
                                        <li role="presentation" class="tab-separator">
                                            <a href="#tab_notes"
                                                onclick="get_sales_notes(<?php echo e($contract->id); ?>,'contracts'); return false"
                                                aria-controls="tab_notes" role="tab" data-toggle="tab">
                                                <?php echo _l('contract_notes'); ?>
                                                <span class="notes-total">
                                                    <?php if ($totalNotes > 0) { ?>
                                                        <span class="badge"><?php echo e($totalNotes); ?></span>
                                                    <?php } ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li role="presentation" class="tab-separator">
                                            <a href="#tab_templates"
                                                onclick="get_templates('contracts', <?php echo $contract->id ?>); return false"
                                                aria-controls="tab_templates" role="tab" data-toggle="tab">
                                                <?php echo _l('templates');
                                                $conditions = ['type' => 'contracts'];
                                                if (staff_cant('view_all_templates', 'contracts')) {
                                                    $conditions['addedfrom'] = get_staff_user_id();
                                                    $conditions['type']      = 'contracts';
                                                }
                                                $total_templates = total_rows(db_prefix() . 'templates', $conditions);
                                                ?>
                                                <span
                                                    class="badge total_templates <?php echo $total_templates === 0 ? 'hide' : ''; ?>"><?php echo $total_templates ?></span>
                                            </a>
                                        </li>
                                        <li role="presentation" data-toggle="tooltip"
                                            title="<?php echo _l('emails_tracking'); ?>" class="tab-separator">
                                            <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab"
                                                data-toggle="tab">
                                                <?php if (!is_mobile()) { ?>
                                                    <i class="fa-regular fa-envelope-open" aria-hidden="true"></i>
                                                <?php } else { ?>
                                                    <?php echo _l('emails_tracking'); ?>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <li role="presentation" class="tab-separator toggle_view">
                                            <a href="#" onclick="contract_full_view(); return false;" data-toggle="tooltip"
                                                data-title="<?php echo _l('toggle_full_view'); ?>">
                                                <i class="fa fa-expand"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane<?php if (!$this->input->get('tab') || $this->input->get('tab') == 'tab_content') {
                                                                        echo ' active';
                                                                    } ?>" id="tab_content">
                                    <div class="row mtop20">
                                        <?php if ($contract->signed == 1) { ?>
                                            <div class="col-md-12">
                                                <div class="alert alert-success">
                                                    <?php echo _l(
                                                        'document_signed_info',
                                                        [
                                                            '<b>' . e($contract->acceptance_firstname) . ' ' . e($contract->acceptance_lastname) . '</b> (<a href="mailto:' . e($contract->acceptance_email) . '">' . e($contract->acceptance_email) . '</a>)',
                                                            '<b>' . e(_dt($contract->acceptance_date)) . '</b>',
                                                            '<b>' . e($contract->acceptance_ip) . '</b>',
                                                        ]
                                                    ); ?>
                                                </div>
                                            </div>
                                        <?php } elseif ($contract->marked_as_signed == 1) { ?>
                                            <div class="col-md-12">
                                                <div class="alert alert-info">
                                                    <?php echo _l('contract_marked_as_signed_info'); ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="col-md-12">
                                            <?php if (isset($contract_merge_fields)) { ?>
                                                <p class="bold text-right no-mbot"><a href="#"
                                                        onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a>
                                                </p>
                                                <div class=" avilable_merge_fields mtop15 hide">
                                                    <ul class="list-group">
                                                        <?php
                                                        foreach ($contract_merge_fields as $field) {
                                                            foreach ($field as $f) {
                                                                echo '<li class="list-group-item"><b>' . $f['name'] . '</b>  <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">' . $f['key'] . '</a></li>';
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr class="hr-panel-separator" />
                                    <?php if (staff_cant('edit', 'contracts')) { ?>
                                        <div class="alert alert-warning contract-edit-permissions">
                                            <?php echo _l('contract_content_permission_edit_warning'); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="tc-content<?php
                                                            if (staff_can('edit', 'contracts') && !$isSignedOrMarkedSigned) {
                                                                echo ' editable';
                                                            } ?>" style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;">
                                        <?php
                                        if (empty($contract->content) && staff_can('edit', 'contracts')) {
                                            echo hooks()->apply_filters('new_contract_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
                                        } else {
                                            echo $contract->content;
                                        }
                                        ?>
                                    </div>
                                    <?php if (!empty($contract->signature)) { ?>
                                        <div class="row mtop25">
                                            <div class="col-md-6 col-md-offset-6 text-right">
                                                <div class="bold">
                                                    <p class="no-mbot">
                                                        <?php echo e(_l('contract_signed_by') . ": {$contract->acceptance_firstname} {$contract->acceptance_lastname}"); ?>
                                                    </p>
                                                    <p class="no-mbot">
                                                        <?php echo e(_l('contract_signed_date') . ': ' . _dt($contract->acceptance_date)); ?>
                                                    </p>
                                                    <p class="no-mbot">
                                                        <?php echo e(_l('contract_signed_ip') . ": {$contract->acceptance_ip}"); ?>
                                                    </p>
                                                </div>
                                                <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                                                    <?php if ($contract->signed == 1 && staff_can('delete',  'contracts')) { ?>
                                                        <a href="<?php echo admin_url('contracts/clear_signature/' . $contract->id); ?>"
                                                            data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>"
                                                            class="_delete text-danger">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                </p>
                                                <div class="pull-right">
                                                    <img src="<?php echo site_url('download/preview_image?path=' . protected_file_url_by_path(get_upload_path_by_type('contract') . $contract->id . '/' . $contract->signature)); ?>"
                                                        class="img-responsive" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_notes">
                                    <?php echo form_open(admin_url('contracts/add_note/' . $contract->id), ['id' => 'sales-notes', 'class' => 'contract-notes-form mtop15']); ?>
                                    <?php echo render_textarea('description'); ?>
                                    <div class="text-right">
                                        <button type="submit"
                                            class="btn btn-primary mtop15 mbot15"><?php echo _l('contract_add_note'); ?></button>
                                    </div>
                                    <?php echo form_close(); ?>
                                    <hr />
                                    <div class="mtop20" id="sales_notes_area"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_comments">
                                    <div class="row contract-comments mtop15">
                                        <div class="col-md-12">
                                            <div id="contract-comments"></div>
                                            <div class="clearfix"></div>
                                            <textarea name="content" id="comment" rows="4"
                                                class="form-control mtop15 contract-comment"></textarea>
                                            <button type="button" class="btn btn-primary mtop10 pull-right"
                                                onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane<?php if ($this->input->get('tab') == 'attachments') {
                                                                        echo ' active';
                                                                    } ?>" id="attachments">
                                    <?php echo form_open(admin_url('contracts/add_contract_attachment/' . $contract->id), ['id' => 'contract-attachments-form', 'class' => 'dropzone mtop15']); ?>
                                    <?php echo form_close(); ?>
                                    <div class="tw-flex tw-justify-end tw-items-center tw-space-x-2 mtop15">
                                        <button class="gpicker" data-on-pick="contractGoogleDriveSave">
                                            <i class="fa-brands fa-google" aria-hidden="true"></i>
                                            <?php echo _l('choose_from_google_drive'); ?>
                                        </button>
                                        <div id="dropbox-chooser"></div>
                                    </div>
                                    <!-- <img src="https://drive.google.com/uc?id=14mZI6xBjf-KjZzVuQe8-rjtv_wXEbDTw" /> -->

                                    <div id="contract_attachments" class="mtop30">
                                        <?php
                                        $data = '<div class="row">';
                                        foreach ($contract->attachments as $attachment) {
                                            $href_url = site_url('download/file/contract/' . $attachment['attachment_key']);
                                            if (!empty($attachment['external'])) {
                                                $href_url = $attachment['external_link'];
                                            }
                                            $data .= '<div class="display-block contract-attachment-wrapper">';
                                            $data .= '<div class="col-md-10">';
                                            $data .= '<div class="pull-left"><i class="' . get_mime_class($attachment['filetype']) . '"></i></div>';
                                            $data .= '<a href="' . $href_url . '"' . (!empty($attachment['external']) ? ' target="_blank"' : '') . '>' . $attachment['file_name'] . '</a>';
                                            $data .= '<p class="text-muted">' . $attachment['filetype'] . '</p>';
                                            $data .= '</div>';
                                            $data .= '<div class="col-md-2 text-right">';
                                            if ($attachment['staffid'] == get_staff_user_id() || is_admin()) {
                                                $data .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,' . $attachment['id'] . '); return false;"><i class="fa fa fa-times"></i></a>';
                                            }
                                            $data .= '</div>';
                                            $data .= '<div class="clearfix"></div><hr/>';
                                            $data .= '</div>';
                                        }
                                        $data .= '</div>';
                                        echo $data;
                                        ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane<?php if ($this->input->get('tab') == 'renewals') {
                                                                        echo ' active';
                                                                    } ?>" id="renewals">
                                    <div class="mtop20">
                                        <?php if (staff_can('edit',  'contracts')) { ?>
                                            <div class="_buttons">
                                                <a href="#" class="btn btn-default" data-toggle="modal"
                                                    data-target="#renew_contract_modal">
                                                    <i class="fa fa-refresh"></i> <?php echo _l('contract_renew_heading'); ?>
                                                </a>
                                            </div>
                                            <hr />
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                        <?php
                                        if (count($contract_renewal_history) == 0) {
                                            echo '<p class="tw-m-0 tw-text-base tw-font-medium tw-text-neutral-500">' . _l('no_contract_renewals_found') . '</p>';
                                        }
                                        foreach ($contract_renewal_history as $renewal) { ?>
                                            <div class="display-block">
                                                <div class="media-body">
                                                    <div class="display-block">
                                                        <b>
                                                            <?php
                                                            echo e(_l('contract_renewed_by', $renewal['renewed_by']));
                                                            ?>
                                                        </b>
                                                        <?php if ($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()) { ?>
                                                            <a href="<?php echo admin_url('contracts/delete_renewal/' . $renewal['id'] . '/' . $renewal['contractid']); ?>"
                                                                class="pull-right _delete text-danger"><i
                                                                    class="fa fa-remove"></i></a>
                                                            <br />
                                                        <?php } ?>
                                                        <small
                                                            class="text-muted"><?php echo e(_dt($renewal['date_renewed'])); ?></small>
                                                        <hr class="hr-10" />
                                                        <span class="text-success bold" data-toggle="tooltip"
                                                            title="<?php echo e(_l('contract_renewal_old_start_date', _d($renewal['old_start_date']))); ?>">
                                                            <?php echo e(_l('contract_renewal_new_start_date', _d($renewal['new_start_date']))); ?>
                                                        </span>
                                                        <br />
                                                        <?php if (is_date($renewal['new_end_date'])) {
                                                            $tooltip = '';
                                                            if (is_date($renewal['old_end_date'])) {
                                                                $tooltip = e(_l('contract_renewal_old_end_date', _d($renewal['old_end_date'])));
                                                            } ?>
                                                            <span class="text-success bold" data-toggle="tooltip"
                                                                title="<?php echo e($tooltip); ?>">
                                                                <?php echo e(_l('contract_renewal_new_end_date', _d($renewal['new_end_date']))); ?>
                                                            </span>
                                                            <br />
                                                        <?php } ?>
                                                        <?php if ($renewal['new_value'] > 0) {
                                                            $contract_renewal_value_tooltip = '';
                                                            if ($renewal['old_value'] > 0) {
                                                                $contract_renewal_value_tooltip = ' data-toggle="tooltip" data-title="' . e(_l('contract_renewal_old_value', app_format_money($renewal['old_value'], $base_currency))) . '"';
                                                            } ?>
                                                            <span class="text-success bold"
                                                                <?php echo e($contract_renewal_value_tooltip); ?>>
                                                                <?php echo e(_l('contract_renewal_new_value', app_format_money($renewal['new_value'], $base_currency))); ?>
                                                            </span>
                                                            <br />
                                                        <?php  } ?>
                                                    </div>
                                                </div>
                                                <hr />
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane ptop10" id="tab_emails_tracking">
                                    <?php
                                    $this->load->view('admin/includes/emails_tracking', [
                                        'tracked_emails' => get_tracked_emails($contract->id, 'contract'),
                                    ]);
                                    ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_tasks">
                                    <div class="mtop20">
                                        <?php init_relation_tasks_table(['data-new-rel-id' => $contract->id, 'data-new-rel-type' => 'contract']); ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_templates">
                                    <div class="row contract-templates mtop15">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary"
                                                onclick="add_template('contracts', <?php echo $contract->id ?>);"><?php echo _l('add_template'); ?></button>
                                            <hr>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="contract-templates" class="contract-templates-wrapper"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<div id="modal-wrapper"></div>
<?php init_tail(); ?>
<?php if (isset($contract)) { ?>
    <!-- init table tasks -->
    <script>
        var contract_id = '<?php echo $contract->id; ?>';
    </script>
    <?php $this->load->view('admin/contracts/send_to_client'); ?>
    <?php $this->load->view('admin/contracts/renew_contract'); ?>
<?php } ?>
<?php $this->load->view('admin/contracts/contract_type'); ?>
<script>
    Dropzone.autoDiscover = false;
    $(function() {
        init_ajax_project_search_by_customer_id();
        if ($('#contract-attachments-form').length > 0) {
            new Dropzone("#contract-attachments-form", appCreateDropzoneOptions({
                success: function(file) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length ===
                        0) {
                        var location = window.location.href;
                        window.location.href = location.split('?')[0] + '?tab=attachments';
                    }
                }
            }));
        }

        if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
            document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
                success: function(files) {
                    $.post(admin_url + 'contracts/add_external_attachment', {
                        files: files,
                        contract_id: contract_id,
                        external: 'dropbox'
                    }).done(function() {
                        var location = window.location.href;
                        window.location.href = location.split('?')[0] + '?tab=attachments';
                    });
                },
                linkType: "preview",
                extensions: app.options.allowed_files.split(','),
            }));
        }

        appValidateForm($('#contract-form'), {
            client: 'required',
            datestart: 'required',
            subject: 'required'
        });

        appValidateForm($('#renew-contract-form'), {
            new_start_date: 'required'
        });

        //clientid chnage 
        $('#clientid').on('change', function() {
            var client_id = $(this).val();
            if (client_id) {
                $.get(admin_url + 'task_customize/get_customer_details/' + client_id, function(data) {
                    if (data) {
                        var data = JSON.parse(data);
                        if (!$.isEmptyObject(data)) {
                            $('.cutome_select').removeClass('hide');
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

        init_tinymce_inline_editor({
            saveUsing: save_contract_content,
            onSetup: function(editor) {
                editor.on('MouseDown ContextMenu', function() {
                    if (!is_mobile() && !$('.left-column').hasClass('hide')) {
                        contract_full_view();
                    }
                });
            }
        })
    });

    function save_contract_content(manual) {
        var editor = tinyMCE.activeEditor;
        var data = {};
        data.contract_id = contract_id;
        data.content = editor.getContent();
        $.post(admin_url + 'contracts/save_contract_data', data).done(function(response) {
            response = JSON.parse(response);
            if (typeof(manual) != 'undefined') {
                // Show some message to the user if saved via CTRL + S
                alert_float('success', response.message);
            }
            // Invokes to set dirty to false
            editor.save();
        }).fail(function(error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function delete_contract_attachment(wrapper, id) {
        if (confirm_delete()) {
            $.get(admin_url + 'contracts/delete_contract_attachment/' + id, function(response) {
                if (response.success == true) {
                    $(wrapper).parents('.contract-attachment-wrapper').remove();

                    var totalAttachmentsIndicator = $('.attachments-indicator');
                    var totalAttachments = totalAttachmentsIndicator.text().trim();
                    if (totalAttachments == 1) {
                        totalAttachmentsIndicator.remove();
                    } else {
                        totalAttachmentsIndicator.text(totalAttachments - 1);
                    }
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
        return false;
    }

    function insert_merge_field(field) {
        var key = $(field).text();
        tinymce.activeEditor.execCommand('mceInsertContent', false, key);
    }

    function contract_full_view() {
        $('.left-column').toggleClass('hide');
        $('.right-column').toggleClass('col-md-7');
        $('.right-column').toggleClass('col-md-12');
        $(window).trigger('resize');
    }

    function add_contract_comment() {
        var comment = $('#comment').val();
        if (comment == '') {
            return;
        }
        var data = {};
        data.content = comment;
        data.contract_id = contract_id;
        $('body').append('<div class="dt-loader"></div>');
        $.post(admin_url + 'contracts/add_comment', data).done(function(response) {
            response = JSON.parse(response);
            $('body').find('.dt-loader').remove();
            if (response.success == true) {
                $('#comment').val('');
                get_contract_comments();
            }
        });
    }

    function get_contract_comments() {
        if (typeof(contract_id) == 'undefined') {
            return;
        }
        requestGet('contracts/get_comments/' + contract_id).done(function(response) {
            $('#contract-comments').html(response);
            var totalComments = $('[data-commentid]').length;
            var commentsIndicator = $('.comments-indicator');
            if (totalComments == 0) {
                commentsIndicator.addClass('hide');
            } else {
                commentsIndicator.removeClass('hide');
                commentsIndicator.text(totalComments);
            }
        });
    }

    function remove_contract_comment(commentid) {
        if (confirm_delete()) {
            requestGetJSON('contracts/remove_comment/' + commentid).done(function(response) {
                if (response.success == true) {

                    var totalComments = $('[data-commentid]').length;

                    $('[data-commentid="' + commentid + '"]').remove();

                    var commentsIndicator = $('.comments-indicator');
                    if (totalComments - 1 == 0) {
                        commentsIndicator.addClass('hide');
                    } else {
                        commentsIndicator.removeClass('hide');
                        commentsIndicator.text(totalComments - 1);
                    }
                }
            });
        }
    }

    function edit_contract_comment(id) {
        var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
        if (content != '') {
            $.post(admin_url + 'contracts/edit_comment/' + id, {
                content: content
            }).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    alert_float('success', response.message);
                    $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
                }
            });
            toggle_contract_comment_edit(id);
        }
    }

    function toggle_contract_comment_edit(id) {
        $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
        $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
    }

    function contractGoogleDriveSave(pickData) {
        var data = {};
        data.contract_id = contract_id;
        data.external = 'gdrive';
        data.files = pickData;
        $.post(admin_url + 'contracts/add_external_attachment', data).done(function() {
            var location = window.location.href;
            window.location.href = location.split('?')[0] + '?tab=attachments';
        });
    }
</script>
</body>

</html>