<?php defined('BASEPATH') or exit('No direct script access allowed');

if ( !empty( $project_statuses ) )
{


    foreach ( $project_statuses as $status )
    {

        $item_id    = $status['id'];

        if ( $item_id == 4 ) // completed
            continue;

        $item_name  = $status['name'];

        $item_color = $status['color'];


        $projects = task_manage_project_info( $item_id );


        $total_tasks    = count( $projects );

        ?>


        <ul class="kan-ban-col" data-col-status-id="<?php echo $item_id; ?>" data-total-pages="<?php echo $total_tasks; ?>"
            data-total="<?php echo $total_tasks; ?>">
            <li class="kan-ban-col-wrapper">

                <div class="panel_s panel-default no-mbot">

                    <div class="panel-heading" style="background:<?php echo $item_color?>; border-color:<?php echo $item_color?>; color:#fff; ">
                        <?php echo $item_name;?> - <span class="tw-text-sm"> <?php echo $total_tasks . ' ' . _l('projects') ?> </span>
                    </div>

                    <div class="kan-ban-content-wrapper">

                        <div class="kan-ban-content">

                            <ul class="status sortable project-status"  data-status-id="<?php echo $item_id; ?>">

                                <?php
                                foreach ( $projects as $project )
                                {

                                    $this->load->view('v_kanban_card', ['project' => $project ] );

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

    <?php } ?>


<?php }
else
    echo "Project status not found";

?>
