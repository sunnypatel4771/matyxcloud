
<!-- Product milestone modal -->
<div class="modal fade" id="product_milestone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="myModalLabel">
                    <span><?php echo _l('task_manage_milestone_detail')?></span>
                </h4>

            </div>

            <?php echo form_open_multipart( admin_url('task_manage/manage/save_milestone') ); ?>

                <input type="hidden" name="id" id="milestone_id" value="0" />
                <input type="hidden" name="milestone_color" id="milestone_color" value="" />
                <input type="hidden" name="group_id" value="<?php echo $item_id?>" />

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12" >

                            <?php echo render_input( 'milestone_name' , 'task_manage_milestone_name' ) ?>

                            <?php echo render_input( 'milestone_order' , 'task_manage_milestone_order' ) ?>

                            <?php
                            foreach ( get_system_favourite_colors() as $system_color )
                            {

                                echo "<div class='kanban-cpicker cpicker cpicker-small' data-color='" . $system_color . "' style='background:" . $system_color . ';border:1px solid ' . $system_color . "'></div>";

                            }
                            ?>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                    <button type="submit" class="btn btn-primary"  ><?php echo _l('submit'); ?></button>

                </div>

            <?php echo form_close(); ?>

        </div>

    </div>

</div>
