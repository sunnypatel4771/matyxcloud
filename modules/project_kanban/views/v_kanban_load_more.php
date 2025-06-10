<?php defined('BASEPATH') or exit('No direct script access allowed');


    $item_id    = $status_id;


    $projects = project_kanban_project_info( $item_id );

    $total_tasks = $this->input->get('total');
    $page = $this->input->get('page');

    $page++;

    if ( !empty( $projects ) )
    {

        foreach ( $projects as $project )
        {

            $this->load->view('v_kanban_card', ['project' => $project ] );

        }

        ?>

        <li class="text-center not-sortable kanban-load-more kanban-load-more-<?php echo $item_id; ?>"

            data-load-status="<?php echo $item_id; ?>">

            <a href="#" class="btn btn-default btn-block" data-page="<?php echo $page?>"

               onclick="project_kanban_load_more( <?php echo $item_id; ?> , <?php echo $page?> , <?php echo $total_tasks?> ); return false;" >

                <?php echo _l('load_more'); ?>

            </a>

        </li>

    <?php } ?>

