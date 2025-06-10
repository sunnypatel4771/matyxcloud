<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();?>

<div id="wrapper" >

    <div class="content">


        <div class="row">

            <div class="col-md-12">

                <div class="tw-mb-2 sm:tw-mb-4">

                    <a href="#" onclick="task_manage_model( 0 )" class="btn btn-primary">

                        <i class="fa-regular fa-plus tw-mr-1"></i>

                        <?php echo _l('task_manage_group_new'); ?>

                    </a>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="panel_s">

                    <div class="panel-body">

                        <div class="table-responsive ">

                            <table class="table table_task_manage">
                                <thead>
                                    <th>#</th>
                                    <th><?php echo _l('task_manage_group_name')?></th>
                                    <th><?php echo _l('task_manage_date')?></th>
                                    <th><?php echo _l('task_manage_status')?></th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="modal fade" id="task_manage_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="myModalLabel">

                    <span class="edit-title"> <?php echo _l('task_manage_model_title')?> </span>

                </h4>

            </div>

            <?php echo form_open('task_manage/manage/group_save', [ 'id' => 'form_task_manage' ]); ?>

            <input type="hidden" name="group_id" id="group_id">

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-12">

                        <?php echo render_input('group_name', _l('task_manage_group_name') , '' , 'input' , [ 'required' => true ] ); ?>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>

            </div>

            <?php echo form_close(); ?>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script>


    (function($) {
        "use strict";

        $(function() {

            initDataTable('.table_task_manage', admin_url + 'task_manage/manage/lists', false, false );

        });


    })(jQuery);


    function task_manage_model( task_group_id = 0 )
    {

        $('#group_name').val('');

        $('#group_id').val(0);

        $.post(admin_url+"task_manage/manage/group_detail" , { group_id : task_group_id } ).done(function ( response ){

            response = JSON.parse( response );

            if( response.detail )
            {

                $('#group_name').val(response.detail.group_name);

                $('#group_id').val(response.detail.id);

            }

            $('#task_manage_modal').modal();

        })



    }

</script>


</body>

</html>
