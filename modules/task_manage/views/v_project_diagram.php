<div>


    <?php

    $status_style = array(
        1 => "background-color: #f5b678;",
        2 => "background-color: #e1f1a3;",
        3 => "background-color: #d6e7f5;",
        4 => "background-color: #d6f5ef;",
        5 => "background-color: #c9ffd0;",
    );

    if ( !empty( $task_group_data ) ) : ?>

        <?php foreach ( $task_group_data as $group_id => $group_data ) : ?>

            <div>
                <h4>
                    <strong> <?php echo $group_data["group_name"]?> </strong>

                    <a href="<?php echo admin_url('task_manage/manage/detail/'.$group_id)?>"> <?php echo _l('view')?> </a>
                </h4>
            </div>

            <table class="table">

                <thead>
                    <tr>

                        <th style="text-align: center"><?php echo _l('task_manage_completed')?></th>

                        <th style="text-align: center"><?php echo _l('task_manage_in_process')?></th>

                        <th style="text-align: center"><?php echo _l('task_manage_pending')?></th>

                    </tr>
                </thead>

                <tbody>

                    <tr>

                        <td width="33%">

                            <?php if ( !empty( $group_data["task_completed"] ) ) : ?>

                                <?php foreach ( $group_data["task_completed"] as $group_task) {
                                    $group_style_ =" font-weight:bold; ";
                                    if (isset($group_task->project_status_id) && !empty($status_style[$group_task->project_status_id]))
                                        $group_style_ .=$status_style[$group_task->project_status_id];

                                    echo project_diagram_task_status_text( $group_task , $group_style_);

                                } ?>

                            <?php endif; ?>

                        </td>

                        <td width="33%">

                            <?php if ( !empty( $group_data["task_in_process"] ) ) : ?>

                                <?php foreach ( $group_data["task_in_process"] as $group_task) {
                                    $group_style_ =" font-weight:bold; ";
                                    if (isset($group_task->project_status_id) && !empty($status_style[$group_task->project_status_id]))
                                        $group_style_ .= $status_style[$group_task->project_status_id];

                                    echo project_diagram_task_status_text( $group_task , $group_style_ );

                                } ?>

                            <?php endif; ?>

                        </td>

                        <td width="33%">

                            <?php if ( !empty( $group_data["task_pending"] ) ) : ?>

                                <?php foreach ( $group_data["task_pending"] as $group_task) {
                                    $group_style_ =" font-weight:bold; ";
                                    if (isset($group_task->project_status_id) && !empty($status_style[$group_task->project_status_id]))
                                        $group_style_ .= $status_style[$group_task->project_status_id];


                                    echo project_diagram_task_status_text( $group_task , $group_style_);

                                } ?>

                            <?php endif; ?>

                        </td>

                    </tr>

                </tbody>

            </table>

        <?php endforeach; ?>

    <?php endif; ?>

</div>


<style>

    .project_diagram_tasks
    {
        border: 1px solid #310808;
        padding: 5px;
        margin: 15px 15px 0px 15px;
        background-color: #fff;
        float: left;
        width: 194px;
        height: 50px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        border-radius: 5px;
    }

</style>
