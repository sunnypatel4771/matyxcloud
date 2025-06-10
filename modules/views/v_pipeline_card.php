<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<li data-proposal-id="<?php echo $proposal['id']; ?>" class="not-sortable">

    <div class="panel-body">

        <div class="row">

            <div class="col-md-12">



                <h4 class="tw-font-semibold  tw-text-base pipeline-heading tw-mb-0.5">

                    <a style="cursor: pointer" onclick="init_task_modal(<?php echo $project_task->id?>); return false;"

                       data-toggle="tooltip" data-title="<?php echo $project_task->name; ?>"

                       class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900" >

                        <?php echo $project_task->name; ?>

                    </a>

                    <a style="cursor: pointer" onclick="init_task_modal(<?php echo $project_task->id?>); return false;" class="pull-right"
                       data-toggle="tooltip" data-title="Edit tasks" >
                        <small>
                            <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                        </small>
                    </a>


                </h4>


                <span style="font-size: 15px">

                    <?php

                    $client_url = admin_url('clients/client/' . $project_task->clientid );

                    $client_company = $project_task->company;

                    if ( empty( $client_company ) )
                    {
                        $contact_info = $this->db->select('id , firstname , lastname ')->where('userid', $project_task->clientid)->where('is_primary',1)->get(db_prefix() . 'contacts')->row();

                        if ( !empty( $contact_info ) )
                        {

                            $client_url .= "?group=contacts&contactid=$contact_info->id";

                            $client_company = $contact_info->firstname.' '.$contact_info->lastname;

                        }
                    }

                    echo '<a href="' . $client_url . '" data-toggle="tooltip" data-title="' . _l('client') . '">' . $client_company . '</a>';

                    ?>

                </span>

                <h4 style="margin: 5px 0px!important;" class="tw-font-semibold  tw-text-base pipeline-heading" >

                    <a href="<?php echo admin_url('projects/view/' . $project_task->project_id ) ?>"
                       data-toggle="tooltip" data-title="<?php echo $project_task->project_name; ?>"
                       class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900" >
                        <?php echo $project_task->project_name ?>
                    </a>

                </h4>


            </div>

            <div class="col-md-12">

                <div class="tw-flex">

                    <div class="tw-grow">

                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span class="tw-text-neutral-500">
                                    <b><?php echo _l('task_single_start_date'); ?>:</b>
                                </span>
                            <?php echo _d( $project_task->startdate ); ?>
                        </p>

                        <?php
                        $class_task_duedate = '';
                        if ( !empty( $project_task->duedate ) && $project_task->duedate < date( 'Y-m-d') )
                            $class_task_duedate = ' style="color:red!important" ';
                        ?>
                        <p <?php echo $class_task_duedate?> class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span <?php echo $class_task_duedate?> class="tw-text-neutral-500">
                                    <b><?php echo _l('task_single_due_date'); ?>:</b>
                                </span>
                            <?php echo _d( $project_task->duedate ); ?>
                        </p>


                        <select class="form-control" onchange="task_manage_task_mark_as( <?php echo $project_task->id?> )"
                                project_id="<?php echo $project_task->project_id?>" id="project_task_id_<?php echo $project_task->id?>">

                            <?php if ( !empty( $task_statuses ) ) {

                                foreach ($task_statuses as $taskChangeStatus)
                                {

                                    $selected = "";
                                    if ( $project_task->status == $taskChangeStatus['id'])
                                        $selected = "selected";

                                    echo "<option $selected value='".$taskChangeStatus['id']."'>".$taskChangeStatus['name']."</option>";

                                }

                            }?>

                        </select>



                    </div>

                </div>
                <?php $tags = get_tags_in( $project_task->id , 'task'); ?>
                <?php if (count($tags) > 0) { ?>
                    <div class="kanban-tags tw-text-sm tw-inline-flex">
                        <?php echo render_tags($tags); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</li>
