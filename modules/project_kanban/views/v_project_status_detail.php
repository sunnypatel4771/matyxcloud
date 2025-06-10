
<?php echo form_open( admin_url("project_kanban/project_kanban/status_save/$status_id") , ['id' => 'form-project-status'] ); ?>

    <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">

            <?php echo $title; ?>

        </h4>

    </div>

    <div class="modal-body">

        <?php echo render_input('status_name', 'project_kanban_status_name', $status->status_name , 'text', ['autofocus' => true]); ?>

        <?php echo render_input('status_color', 'project_kanban_status_color', $status->status_color, 'color', ['style' => 'width:5rem']); ?>

        <?php echo render_input('status_order', 'project_kanban_status_order', $status->status_order, 'number'); ?>

        <div class="checkbox checkbox-primary">

            <input type="checkbox" name="filter_default" id="filter_default" <?php echo $status->filter_default ? "checked" : "" ?> value="1">

            <label for="filter_default"><?php echo _l('project_kanban_default_filter'); ?></label>

        </div>


    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>



<?php echo form_close(); ?>

<script>


    $(function() {

        init_color_pickers();

        init_selectpicker();


        appValidateForm($('#form-project-status'), {

            status_name: 'required',

            status_color: 'required',

            status_order: 'required',

        })

    });



</script>
