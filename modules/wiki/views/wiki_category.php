<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <button class=" mright5 btn btn-primary pull-left display-block" data-toggle="modal" id="add_category">
                    <?php echo _l('add_new_category'); ?>
                </button>

                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
                <div class="clearfix"></div>
                <?php
                $table = [
                    _l('id'),
                    _l('name'),
                    _l('option'),
                ];
                render_datatable($table, 'category'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="category_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <?php echo form_open(admin_url('wiki/category/category_save'), array('id' => 'category_form')); ?>
        <div class="modal-content width-100">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span id="category_title" class="add_category_title"><?php echo _l('add_category'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <?php echo render_input('name', _l('name')); ?>

                <input type="hidden" name="category_hid" id="category_hid">
            </div>
            <div class="modal-footer">
                <button type="" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button id="category_btn" type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    $(function() {
        appValidateForm($('#category_form'), {
            name: 'required',
        });

        initDataTable('.table-category', admin_url + 'wiki/category/get_category', [2], [2]);
    })

    $(document).ready(function() {
        $('#add_category').on('click', function() {
            $('#category_modal').modal('show');
            $('#category_hid').val('');
            $('#category_form')[0].reset();
            $('#category_title').text('<?php echo _l('add_category'); ?>');
            $('#category_btn').text('<?php echo _l('submit'); ?>');
        });

        $('#category_form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        alert_float('success', res.message);
                        $('#category_modal').modal('hide');
                        form[0].reset();
                        if ($.fn.DataTable.isDataTable('.table-category')) {
                            $('.table-category').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        alert_float('danger', res.message || 'Something went wrong.');
                    }
                },
            });
        });

        $(document).on('click', '.edit_category', function() {
            var id = $(this).data('id');

            $.ajax({
                url: admin_url + 'wiki/category/edit_category/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        console.log(response.data.id);
                        $('#category_hid').val(response.data.id);
                        $('#name').val(response.data.name);
                        $('#category_modal').modal('show');
                        $('#category_title').text('<?php echo _l('edit_category'); ?>');
                        $('#category_btn').text('<?php echo _l('update'); ?>');
                    }
                }
            })
        });

        $(document).on('click', '.delete_category', function() {
            var id = $(this).data('id');

            $.ajax({
                url: admin_url + 'wiki/category/delete_category/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        if ($.fn.DataTable.isDataTable('.table-category')) {
                            $('.table-category').DataTable().ajax.reload(null, false);
                        } else {
                            alert_float('danger', response.message || 'Something went wrong.');
                        }
                    }
                }
            })
        })
    })
</script>