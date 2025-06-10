<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();

$max_height = 0;

?>

<div id="wrapper">

    <div class="content">

        <h4><?php echo $item_info->group_name;?></h4>

        <div class="panel_s">

            <div class="panel-body">

                <div class="horizontal-scrollable-tabs panel-full-width-tabs">

                    <div class="horizontal-tabs">

                        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">

                            <li role="presentation" class="<?php echo $active_tab == 'task' ? 'active' : '' ?>">

                                <a href="#tab_tasks"  aria-controls="tab_payments" role="tab" data-toggle="tab">

                                    <i class="fa-regular fa-circle-check menu-icon"></i> <?php echo _l('task')?>

                                </a>

                            </li>

                            <li role="presentation" class="<?php echo $active_tab == 'milestone' ? 'active' : '' ?>">

                                <a href="#tab_milestones"  aria-controls="tab_payments" role="tab" data-toggle="tab">

                                    <i class="fa fa-rocket menu-icon"></i> <?php echo _l('project_milestones')?>

                                </a>

                            </li>


                        </ul>

                    </div>

                </div>


                <div class="tab-content tw-mt-5">


                    <div role="tabpanel" class="tab-pane <?php echo $active_tab == 'task' ? 'active' : '' ?>" id="tab_tasks">

                        <div class="row">

                            <div class="col-md-12">

                                <div>
                                    <a class="btn" onclick="task_manage_task_detail( 0 )"> <i class="fa fa-plus"></i> <?php echo _l('task_manage_add_new_task')?> </a>
                                </div>

                                <hr class="hr-panel-separator" />

                                <div class="table-responsive ">

                                    <table class="table table-product-tasks">
                                        <thead>
                                            <th>#</th>
                                            <th><?php echo _l('task_add_edit_subject') ?></th>
                                            <th><?php echo _l('task_milestone') ?></th>
                                            <th><?php echo _l('task_status') ?></th>
                                            <th><?php echo _l('task_add_edit_priority') ?></th>
                                            <th><?php echo _l('task_manage_order') ?></th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-12">

                                <?php if ( !empty( $item_groups ) ) : ?>

                                    <hr class="mbot20 mtop20 hr-panel-separator" />

                                    <p style="font-size: 18px" class="text-primary"><?php echo _l('task_manage_task_order_text')?></p>

                                    <div id="task_order_box">

                                        <div id="task-container" class="row task-container">

                                            <p style="font-size: 18px;width: 100%;"> * <?php echo _l('task_manage_task_group_info') ?></p>

                                            <div class="task-group task-group-class-main" id="group0">

                                                <?php if( !empty( $item_groups[0] ) ) {

                                                    $max_height = max( $max_height , count( $item_groups[0] ) );

                                                    ?>

                                                    <?php foreach ( $item_groups[0] as $task_id => $task_name ) {



                                                        ?>

                                                        <div class="task" task_id="<?php echo $task_id?>" draggable="true" ondragstart="dragStart(event)" ondragend="dragEnd(event)" id="task_<?php echo $task_id?>" title="<?php echo $task_name?>" > <?php echo "# $task_id | $task_name" ?> </div>

                                                    <?php } ?>

                                                <?php } ?>

                                            </div>


                                            <p style="width: 100%; font-size: 18px;"> * <?php echo _l('task_manage_task_group_others') ?></p>


                                            <?php for ( $_grp_id = 1 ; $_grp_id <= $max_group_value ; $_grp_id++  ) { ?>

                                                <div class="task-group task-group-class" id="group<?php echo $_grp_id?>">

                                                    <button class="delete-group-btn" onclick="deleteGroup( <?php echo $_grp_id?> )"><?php echo _l("delete")?></button>

                                                    <?php if( !empty( $item_groups[ $_grp_id ] ) ) {
                                                        $max_height = max( $max_height , count( $item_groups[ $_grp_id ] ) );
                                                        ?>

                                                        <?php foreach ( $item_groups[ $_grp_id ] as $task_id => $task_name ) { ?>

                                                            <div class="task" task_id="<?php echo $task_id?>" draggable="true" ondragstart="dragStart(event)" ondragend="dragEnd(event)" id="task_<?php echo $task_id?>" title="<?php echo $task_name?>" >
                                                                <?php echo "# $task_id | $task_name" ?>
                                                            </div>

                                                        <?php } ?>

                                                    <?php } ?>

                                                </div>

                                            <?php } ?>


                                        </div>

                                    </div>

                                    <div class="col-md-12 mbot20">

                                        <a class="btn btn-info" onclick="save_new_task_group()" > <?php echo _l('task_manage_save_new_task_group')?> </a>

                                        <a class="btn btn-primary" onclick="save_task_create_order()" > <?php echo _l('task_manage_save_changes')?> </a>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                    <div role="tabpanel" class="tab-pane <?php echo $active_tab == 'milestone' ? 'active' : '' ?>" id="tab_milestones">

                        <div class="row">

                            <div>
                                <a class="btn" onclick="task_manage_milestone_detail( 0 )"> <i class="fa fa-plus"></i> <?php echo _l('task_manage_add_new_milestone')?> </a>
                            </div>

                            <hr class="hr-panel-separator" />

                            <?php if( !empty( $milestones ) ) : ?>

                                <?php foreach ( $milestones as $milestone ) : ?>

                                    <div style="margin: 6px; border-radius: 20px; float: left; padding: 15px; color: white; background-color:<?php echo $milestone->milestone_color?>">

                                        <?php echo $milestone->milestone_name?>
                                        &nbsp;&nbsp;
                                        <a class="text-primary" onclick="task_manage_milestone_detail( <?php echo $milestone->id ?> )"> <i class="fa fa-edit"></i> </a>

                                        <a class="_delete text-danger" href="<?php echo admin_url('task_manage/manage/delete_milestone/'.$item_id.'/'.$milestone->id)?>"> <i class="fa fa-trash"></i> </a>

                                    </div>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Product milestone modal -->
<?php $this->load->view('v_product_modal_milestone') ?>

<!-- Item task modal -->
<div class="modal fade" id="product_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content" >

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="myModalLabel">
                    <span>Task Detail</span>
                </h4>

            </div>

            <?php echo form_open_multipart( admin_url('task_manage/manage/save_task') ); ?>

            <input type="hidden" name="id" id="task_id" value="" />
            <input type="hidden" name="group_id" value="<?php echo $item_id?>" />

            <div class="modal-body">

                <div class="row" id="task_modal_content">

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                <button type="submit" class="btn btn-primary"  ><?php echo _l('save'); ?></button>

            </div>

            <?php echo form_close(); ?>

        </div>

    </div>

</div>

<!-- Task new checklist templates -->
<div class="modal fade" id="task_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">

    <div class="modal-dialog modal-sm" role="document">

        <div class="modal-content" >

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="myModalLabel">
                    <span><?php echo _l('task_manage_new_checklist')?></span>
                </h4>

            </div>



            <div class="modal-body">


                <div class="row">

                    <div class="col-md-12">
                        <?php echo render_input( 'checklist_name' )?>
                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                <button type="button" class="btn btn-primary" onclick="save_checklist_template()" ><?php echo _l('save'); ?></button>

            </div>

        </div>

    </div>

</div>



<?php init_tail(); ?>

<style>


    #task_order_box .task-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }


    #task_order_box .task-group-class-main {
        border: 3px solid #919497;
        background-color: #f1f5f9;
        float: left;
        width: 230px;
        overflow-x: hidden;
        overflow-y: auto;
        margin: 10px;
        max-height: 400px;
        min-height: <?php echo $max_height * 60 + 130?>px;
        padding-bottom: 15px;
        border-radius: 15px;
        cursor: grab;
    }


    #task_order_box .task-group-class {
        border: 3px solid #919497;
        background-color: #f1f5f9;
        float: left;
        width: 230px;
        overflow-x: hidden;
        overflow-y: auto;
        margin: 10px;
        max-height: 400px;
        min-height: <?php echo $max_height * 60 + 130?>px;
        padding-bottom: 15px;
        border-radius: 15px;
        cursor: grab;
    }



    #task_order_box .task-group .delete-group-btn {

        width: -webkit-fill-available;
        top: 5px;
        right: 5px;
        background-color: #ff0000;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        display: none;
    }


    #task_order_box .task-group:hover .delete-group-btn {
        display: block;
    }

    #task_order_box .task {

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


        cursor: grab;
        float: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #task_order_box .task:hover {
        transform: scale(1.1);
    }


</style>

<script>

    let item_id = <?php echo $item_id?>;

    let max_group_id = <?php echo $max_group_value?>;

    var modal_tinymce = null;

    var lang_successfully = '<?php echo _l('task_manage_successful') ?>';



    function makeDraggable(element) {

        element.setAttribute("draggable", "true");

        element.addEventListener("dragstart", function (e) {
            e.dataTransfer.setData("text/plain", element.id);
        });

    }

    function makeDroppable(container) {

        container.addEventListener("dragover", function (e) {
            e.preventDefault();
        });

        container.addEventListener("drop", function (e) {
            e.preventDefault();
            var data = e.dataTransfer.getData("text/plain");
            var draggedElement = document.getElementById(data);
            container.appendChild(draggedElement);
        });

    }


    function deleteGroup(groupId) {

        var group = document.getElementById("group"+groupId);
        var tasks = group.querySelectorAll(".task");
        var destinationGroup = document.getElementById("group0"); // Replace with the desired destination group

        tasks.forEach(function (task) {
            destinationGroup.appendChild(task);
        });

        group.remove();

    }

    (function($) {
        "use strict";

        $(function() {

            initDataTable('.table-product-tasks', admin_url + 'task_manage/manage/list_tasks/<?php echo $item_id?>', false, false , [] , [ 5 , 'ASC' ] );

            $('.cpicker').click(function (){

                $('.cpicker').removeClass('cpicker-big').removeClass('cpicker-small');
                $('.cpicker').addClass('cpicker-small');
                $(this).addClass('cpicker-big').removeClass('cpicker-small');

                $('#milestone_color').val( $(this).attr('data-color') );

            })


            // Sortable
            //$('.task-box-container').sortable();

            document.querySelectorAll(".task").forEach(function (task) {
                makeDraggable(task);
            });

            document.querySelectorAll(".task-group").forEach(function (group) {
                makeDroppable(group);
            });



        });

    })(jQuery);


    function save_task_create_order()
    {

        var task_group_id = 0;
        var task_grp_data = [];
        var task_grp_string = "";

        $('.task-group-class').each(function () {


            if( $(this).find('.task').length > 0 )
            {

                task_group_id++;
                task_grp_data.push( task_group_id );

                $(this).find('.task').each(function(){

                    task_grp_string += "&task_group_"+task_group_id+"[]="+$(this).attr('task_id');

                })

            }


        });


        $.post( admin_url+"task_manage/manage/save_task_group_order?"+task_grp_string , { item_id : item_id , task_groups : task_grp_data } ).done(function (){

            alert_float( "success" , lang_successfully );

        })

    }

    function task_manage_task_detail( task_id = 0 , is_copy = 0 )
    {

        $.post( admin_url+"task_manage/manage/task_detail" , { item_id : item_id , task_id : task_id  , is_copy : is_copy } ).done(function (response){

            response = JSON.parse( response );

            $('#task_modal_content').html( response.task_content ).promise().done(function (){

                init_selectpicker();

                init_tags_inputs();

                $('#task_id').val(response.data.task_id);
                $('#description').val(response.data.description);

                tinyMCE.remove(".tinymce-task");
                init_editor('.tinymce-task', {height:200, auto_focus: true});

            });

            $('#product_task_modal').modal();


        })

    }

    function task_manage_milestone_detail( milestone_id = 0 )
    {

        $.post( admin_url+"task_manage/manage/milestone_detail" , { item_id : item_id , milestone_id : milestone_id } ).done(function (response){

            response = JSON.parse( response );

            if( response.data )
            {
                $('#milestone_id').val( milestone_id );

                $('#milestone_name').val( response.data.milestone_name );

                $('#milestone_order').val( response.data.milestone_order );

                $('#milestone_color').val( response.data.milestone_color );

                $('div[data-color="'+response.data.milestone_color+'"]').click();

                $('#product_milestone_modal').modal();

            }

        })

    }

    function add_new_checklist()
    {
        $('#checklist_name').val('');
        $('#checklist_name').focus();
        $('#task_checklist_modal').modal();
    }

    function save_checklist_template()
    {

        if( $.trim( $('#checklist_name').val() ) != ""  )
        {
            var post_data = {};

            post_data.checklist_name = $('#checklist_name').val();

            $.post( admin_url+"task_manage/manage/add_checklist" , post_data ).done(function ( response ){

                response = JSON.parse( response );

                if( response.success )
                {
                    $('#task_checklist_modal').modal('hide');

                    task_manage_task_detail( $('#task_id').val() );
                }

            });

        }
        else
            $('#checklist_name').focus();

    }

    function save_new_task_group()
    {

        max_group_id++;

        var newGroup = '<div id="group'+max_group_id+'" class="task-group task-group-class"><button onclick="deleteGroup( '+max_group_id+' )" class="delete-group-btn"><?php echo _l('delete')?></button></div>';


        $(".task-container").append(newGroup).promise().done(function (){

            document.querySelectorAll(".task-group").forEach(function (group) {
                makeDroppable(group);
            });


        })

    }


    let draggedBox = null;

    function dragStart(event) {
        draggedBox = event.target;
        event.target.style.opacity = '0.5';
    }

    function dragEnd(event) {
        if (draggedBox) {
            draggedBox.style.opacity = '1';
        }
    }

</script>


</body>

</html>
