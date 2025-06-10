<h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">

    <?php echo $current_tab_data['text']?>

</h4>

<?php echo form_open( 'admin/table_manage/setting/save_custom_table' ) ?>

<div class="panel_s">

    <div class="panel-body">


        <?php if( empty( $table_columns ) ) { ?>

            <div class="alert alert-warning">

                <?php echo _l( 'table_manage_custom_table_empty_message' , admin_url($current_tab_data['url'] ))?>

            </div>

        <?php } else { ?>

            <input type="hidden" name="table_hook" id="table_hook" value="<?php echo $current_tab_data['slug']?>" />

            <div>
                <h4><?php echo _l('table_manage_table_columns_order_text') ?></h4>
            </div>

            <div id="tm_custom_table_sortable">

                <?php foreach ( $table_columns as $table_column ) {

                    $field_text     = strip_tags( $table_column['table_field_text'] );
                    $field_index    =  $table_column['table_field_index'] ;

                    ?>

                    <div class="tm_custom_table_sortable_items">
                        <i class="fa fa-bars text-info"> </i>
                        <input type="hidden" name="field_index[]" value="<?php echo $field_index?>">
                        <?php echo $field_text?>
                    </div>


                <?php } ?>

            </div>


        <?php } ?>

    </div>

    <?php if( !empty( $table_columns ) ) { ?>

        <div class="panel-footer">

            <h4 class="text text-info"> <i class="fa fa-warning"></i><?php echo _l('table_manage_table_columns_order_text_warning')?></h4>

            <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> <?php echo _l('table_manage_custom_table_save_button')?> </button>

            <a class="btn btn-danger _delete" id="btn_table_manage_reset"> <i class="fa fa-sync"></i> <?php echo _l('table_manage_custom_table_reset_button')?> </a>

        </div>

    <?php } ?>

</div>

<?php echo form_close() ?>

