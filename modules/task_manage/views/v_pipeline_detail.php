<?php defined('BASEPATH') or exit('No direct script access allowed');

if ( !empty( $items ) )
{


    foreach ( $items as $item )
    {

        if ( isset( $group_detail ) )
        {

            $item_id    = $item;

            $item_name  = "Step # $item";

            $project_tasks = task_manage_group_task_info( $group_id , $item_id );

        }
        else
        {

            $item_id    = $item->id;

            $item_name  = $item->group_name;

            $project_tasks = task_manage_task_info( $item_id );

        }


        $total_tasks    = count( $project_tasks );

        ?>


        <ul class="kan-ban-col" data-col-status-id="<?php echo $item_id; ?>" data-total-pages="<?php echo $total_tasks; ?>"
            data-total="<?php echo $total_tasks; ?>">
            <li class="kan-ban-col-wrapper">

                <div class="panel_s panel-default no-mbot">

                    <div class="panel-heading">
                        <?php echo $item_name;?> - <span class="tw-text-sm"> <?php echo $total_tasks . ' ' . _l('tasks') ?> </span>
                    </div>

                    <div class="kan-ban-content-wrapper">

                        <div class="kan-ban-content">

                            <ul class="sortable" data-status-id="<?php echo $item_id; ?>">

                                <?php
                                foreach ( $project_tasks as $project_task ) {
                                    $this->load->view('v_pipeline_card', ['project_task' => $project_task ] );
                                }
                                ?>

                                <li class="text-center not-sortable mtop30 kanban-empty<?php if ($total_tasks > 0) { echo ' hide'; } ?>">
                                    <h4>
                                        <i class="fa-solid fa-circle-notch" aria-hidden="true"></i><br /><br />
                                        <?php echo _l('no_tasks_found'); ?>
                                    </h4>
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </li>
        </ul>

    <?php }

}
else
    echo "Task not found";

?>
