
<?php echo form_open( admin_url("project_kanban/project_kanban/status_delete")  ); ?>

    <input type="hidden" name="status_id" value="<?php echo $status_id?>">

    <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">

            <?php echo _l('project_kanban_delete_status'); ?>

        </h4>

    </div>

    <div class="modal-body">

        <h4><?php echo _l('project_kanban_delete_status_text',$status->status_name)?></h4>

        <hr />

        <h4> <?php echo _l('project_kanban_status_move_new')?> </h4>

        <div class="form-group">

            <select name="new_status_id" class="selectpicker" id="new_status_id" data-width="100%"

                    data-live-search="true"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                <option></option>

                <?php foreach ( $statuses as $statu ) {

                    if ( $status_id != $statu['id'] )
                        echo "<option value='".$statu['id']."'>".$statu['name']."</option>";


                } ?>


            </select>

        </div>

    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>



<?php echo form_close(); ?>

<script>

    $(function() {

        init_selectpicker();

    })

</script>
