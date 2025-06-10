
<div class="clearfix"></div>
<?php
$max_height = 0;
/*

1: Not Started
2: Awaiting Feedback
3: Waiting
4: In Progress
5: Complete

 */
$status_style = array(
        1 => "background-color: #f5b678;",
        2 => "background-color: #e1f1a3;",
        3 => "background-color: #d6e7f5;",
        4 => "background-color: #d6f5ef;",
        5 => "background-color: #c9ffd0;",
);
?>
<div>

    <?php if ( !empty( $task_group_data ) ) : ?>

        <div class="panel_s">

            <div class="panel-heading">
                <a href="#" onclick="project_task_diagram_toggle(); return false;" id="a_project_diagram_toggle" > <?php echo _l('task_manage_show')?> </a>
            </div>

            <div class="panel-body hide" id="div_project_diagram_toggle">

                <?php foreach ( $task_group_data as $group_id => $group_data ) : ?>

                    <div>
                        <h4>
                            <strong> <?php echo $group_data["group_name"]?> </strong>

                            <a href="<?php echo admin_url('task_manage/manage/detail/'.$group_id)?>"> <?php echo _l('view')?> </a>
                        </h4>
                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <?php if ( !empty( $group_data["task_data"] ) ) : ?>

                                <?php $current_group_order = $group_data['current_group']; ?>

                                <?php foreach ( $group_data["task_data"] as $grp_id => $group_task ) {

                                    echo "<div class='project_diagram_groups'>";

                                    echo "<div style='border-bottom:1px solid #0a0a0a '><h4 style='padding-left: 10px; font-weight: bold;'> "._l('task_manage_step')." # $grp_id </h4></div>";

                                    $max_height = max( $max_height , count( $group_task ) );

                                    foreach ( $group_task as $g_task )
                                    {
                                        $group_style_ = " font-weight:bold; ";

                                        if (isset($g_task->project_status_id) && !empty($status_style[$g_task->project_status_id]))
                                            $group_style_ .= $status_style[$g_task->project_status_id];

                                        echo project_diagram_task_status_text( $g_task , $group_style_);
                                    }

                                    echo "</div>";

                                } ?>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="hr-panel-separator"></div>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>

</div>


<style>

    .project_diagram_groups
    {
        border: 3px solid #919497;
        background-color: #f1f5f9;
        float: left;
        width: 230px;
        overflow-x: hidden;
        overflow-y: auto;
        margin: 10px;
        max-height: 400px;
        min-height: <?php echo $max_height * 60 + 100?>px;
        padding-bottom: 15px;
        border-radius: 15px;
    }

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

<script>

    function project_task_diagram_toggle()
    {

        if( $('#div_project_diagram_toggle').hasClass('hide') )
        {

            $('#div_project_diagram_toggle').removeClass('hide');

            $('#a_project_diagram_toggle').text("<?php echo _l('task_manage_hide') ?>");

        }
        else
        {

            $('#div_project_diagram_toggle').addClass('hide');

            $('#a_project_diagram_toggle').text("<?php echo _l('task_manage_show') ?>");

        }

    }

</script>
