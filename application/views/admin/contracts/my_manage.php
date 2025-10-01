<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <?php if (staff_can('create',  'contracts')) { ?>
                        <a href="<?php echo admin_url('contracts/contract'); ?>"
                            class="btn btn-primary pull-left display-block tw-mb-2 sm:tw-mb-4">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_contract'); ?>
                        </a>
                    <?php } ?>
                    <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
                        <app-filters
                            id="<?php echo $table->id(); ?>"
                            view="<?php echo $table->viewName(); ?>"
                            :saved-filters="<?php echo $table->filtersJs(); ?>"
                            :available-rules="<?php echo $table->rulesJs(); ?>">
                        </app-filters>
                        <input type="hidden" name="type" value="main-contracts">
                    </div>
                    <div class="clearfix"></div>
                    <div id="contract_summary">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>

                            <span>
                                <?php echo _l('contract_summary_heading'); ?>
                            </span>
                        </h4>
                        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-gap-2">
                            <div
                                class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                <span class="tw-font-semibold sm:tw-w-auto tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php echo e($count_active); ?></span>
                                <span class="text-info"><?php echo _l('contract_summary_active'); ?></span>
                            </div>
                            <div
                                class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                <span class="tw-font-semibold sm:tw-w-auto tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php echo e($count_expired); ?></span>
                                <span class="text-danger"><?php echo _l('contract_summary_expired'); ?></span>
                            </div>
                            <div
                                class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                <span class="tw-font-semibold sm:tw-w-auto tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php echo count($expiring); ?>
                                </span>
                                <span class="text-warning"><?php echo _l('contract_summary_about_to_expire'); ?></span>
                            </div>
                            <div
                                class="md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center">
                                <span class="tw-font-semibold sm:tw-w-auto tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php echo e($count_recently_created); ?></span>
                                <span class="text-success"><?php echo _l('contract_summary_recently_added'); ?></span>
                            </div>
                            <div
                                class="tw-flex tw-items-center md:tw-border-r md:tw-border-solid tw-flex-1 md:tw-border-neutral-300 lg:tw-border-0">
                                <span class="tw-font-semibold sm:tw-w-auto tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                    <?php echo e($count_trash); ?></span>
                                <span class="text-muted"><?php echo _l('contract_summary_trash'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <?php echo form_hidden('custom_view'); ?>
                    <div class="panel-body">
                        <div class="row ">

                            <div class="col-md-6 border-right">
                                <h4 class="tw-font-semibold tw-mb-8"><?php echo _l('contract_summary_by_type'); ?></h4>
                                <div class="relative" style="max-height:400px">
                                    <canvas class="chart" height="400" id="contracts-by-type-chart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="tw-font-semibold tw-mb-8">
                                    <?php echo _l('contract_summary_by_type_value'); ?>
                                    (<span data-toggle="tooltip" data-title="<?php echo _l('base_currency_string'); ?>"
                                        class="text-has-action">
                                        <?php echo e($base_currency->name); ?></span>)
                                </h4>
                                <div class="relative" style="max-height:400px">
                                    <canvas class="chart" height="400" id="contracts-value-by-type-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="panel-table-full tw-mt-10">
                            <a href="#" data-toggle="modal" data-target="#contracts_bulk_actions"
                                class="bulk-actions-btn table-btn hide"
                                data-table=".table-contracts">
                                <?php echo _l('bulk_actions'); ?>
                            </a>
                            <?php $this->load->view('admin/contracts/table_html', ['type' => 'main-contracts']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bulk_actions" id="contracts_bulk_actions" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="checkbox checkbox-primary mass_delete_checkbox">
                    <input type="checkbox" name="mass_delete" id="mass_delete">
                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                </div>
                <div class="form-group mt-4">
                    <?php
                    $types = $this->db->select('id, name')->from('contracts_types')->get()->result_array();
                    echo render_select('contract_type', $types, ['id', 'name'], 'contract_type', '');
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo render_date_input('datestart', 'contract_start_date', _d(date('Y-m-d'))); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php echo render_date_input('dateend', 'contract_end_date', _d(date('Y-m-d'))); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo render_custom_fields('contracts', false); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <a href="#" class="btn btn-primary"
                    onclick="contracts_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php init_tail(); ?>
<script>
    $(function() {
        $(document).on('change', '#mass_select_all', function() {
            var checked = $(this).is(':checked');
            $('.table-contracts').find('tbody input.mass_select_all').prop('checked', checked);
        });

        $(document).on('change', '.table-contracts tbody input.mass_select_all', function() {
            if (!$(this).is(':checked')) {
                $('#mass_select_all').prop('checked', false);
            } else if ($('.table-contracts tbody input.mass_select_all:checked').length === $('.table-contracts tbody input.mass_select_all').length) {
                $('#mass_select_all').prop('checked', true);
            }
        });

        var LeadsServerParams = {
            type: "[name='type']",
        };
        initDataTable('.table-contracts', admin_url + 'contracts/table', [0], [0], LeadsServerParams,
            <?php echo hooks()->apply_filters('contracts_table_default_order', json_encode([6, 'asc'])); ?>);

        new Chart($('#contracts-by-type-chart'), {
            type: 'bar',
            data: <?php echo $chart_types; ?>,
            options: {
                legend: {
                    display: false,
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });
        new Chart($('#contracts-value-by-type-chart'), {
            type: 'line',
            data: <?php echo $chart_types_values; ?>,
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });
    });

    function contracts_bulk_action(el) {
        // Collect selected IDs
        var ids = [];
        $('.table-contracts tbody input.mass_select_all:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert_float('warning', 'No contracts selected.');
            return;
        }

        // Collect form data
        var mass_delete = $('#mass_delete').is(':checked') ? 1 : 0;
        var contract_type = $('#contract_type').val();
        var datestart = $('#datestart').val();
        var dateend = $('#dateend').val();

        // Collect custom fields (if any)
        var custom_fields = {};
        $('[name^="custom_fields"]').each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            custom_fields[name] = value;
        });

        // Confirm delete only if mass_delete is checked
        if (mass_delete && !confirm_delete()) {
            return;
        }

        // Send AJAX request
        $.post(admin_url + 'task_customize/contract_bulk_action', {
            ids: ids,
            mass_delete: mass_delete,
            contract_type: contract_type,
            datestart: datestart,
            dateend: dateend,
            custom_fields: custom_fields
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                $('.table-contracts').DataTable().ajax.reload(null, false);
                $('#contracts_bulk_actions').modal('hide');
            } else {
                alert_float('danger', response.message);
            }
        }).fail(function() {
            alert_float('danger', 'Something went wrong.');
        });
    }

    // Reset the bulk actions modal when opened
    $('#contracts_bulk_actions').on('show.bs.modal', function() {
        var modal = $(this);

        // Clear checkboxes
        modal.find('input[type="checkbox"]').prop('checked', false);

        // Reset select inputs
        modal.find('select').val('').trigger('change');

        // Reset date inputs to today's date
        var today = '<?php echo date('Y-m-d'); ?>';
        modal.find('input[name="datestart"]').val(today);
        modal.find('input[name="dateend"]').val(today);

        // Clear custom fields
        modal.find('[name^="custom_fields"]').each(function() {
            $(this).val('');
            if ($(this).hasClass('selectpicker')) {
                $(this).selectpicker('refresh');
            }
        });
    });
</script>
</body>

</html>