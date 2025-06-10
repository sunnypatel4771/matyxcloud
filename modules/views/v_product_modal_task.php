
<!-- Product task modal -->

    <?php if ( !empty( $perfex_items ) ) : ?>

        <div class="col-md-12">

            <div class="form-group">
                <select id="perfex_item" name="perfex_item" class="form-control selectpicker"
                        data-live-search="true"
                        data-none-selected-text="<?php echo _l('task_manage_group_name'); ?>" >
                    <option value=""></option>

                    <?php foreach ( $perfex_items as $perfex_item ) { ?>

                        <option <?php echo $item_id == $perfex_item->id ? 'selected' : '' ?> value="<?php echo $perfex_item->id; ?>"><?php echo $perfex_item->group_name; ?></option>

                    <?php } ?>

                </select>
            </div>

        </div>

    <?php endif; ?>



    <div class="col-md-12">


        <div class="checkbox checkbox-primary checkbox-inline task-add-edit-public tw-pt-2">

            <input type="checkbox" id="task_is_public" name="task_is_public" <?php echo $data->task_is_public == 1 ? 'checked' : '' ?> value="1" >

            <label for="task_is_public" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('task_public_help'); ?>"><?php echo _l('task_public'); ?></label>

        </div>


        <div class="checkbox checkbox-primary checkbox-inline task-add-edit-billable tw-pt-2">

            <input type="checkbox" id="task_is_billable" name="task_is_billable" <?php echo $data->task_is_billable == 1 ? 'checked' : '' ?> value="1"  >

            <label for="task_is_billable"><?php echo _l('task_billable'); ?></label>

        </div>


        <div class="task-visible-to-customer tw-pt-2 checkbox checkbox-inline checkbox-primary">

            <input type="checkbox" id="task_visible_to_client" name="task_visible_to_client" <?php echo $data->task_visible_to_client == 1 ? 'checked' : '' ?> value="1" >

            <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>

        </div>

    </div>

    <div class="col-md-12"> &nbsp; </div>



    <div class="col-md-12" >

        <?php echo render_input('name', 'task_add_edit_subject' , $data->name ); ?>

        <div class="form-group">

            <label for="milestone"><?php echo _l('task_milestone'); ?></label>

            <select name="milestone" id="milestone" class="selectpicker" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                <option value=""></option>

                <?php foreach ($milestones as $milestone) { ?>

                    <option <?php echo $milestone->id == $data->milestone ? 'selected' : '' ?> value="<?php echo $milestone->id; ?>"><?php echo $milestone->milestone_name; ?></option>

                <?php } ?>

            </select>

        </div>

    </div>



    <div class="col-md-6">

        <?php echo render_input('start_date', 'task_manage_start_date' , $data->start_date , 'number' ); ?>

    </div>

    <div class="col-md-6">

        <?php echo render_input('due_date', 'task_manage_due_date' , $data->due_date , 'number' ); ?>

    </div>

    <div class="col-md-6">

        <div class="form-group">

            <label for="priority" class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>

            <select name="priority" class="selectpicker" id="priority" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                <?php foreach (get_tasks_priorities() as $priority) { ?>

                    <option <?php echo $priority['id'] == $data->priority ? 'selected' : '' ?> value="<?php echo $priority['id']; ?>"><?php echo $priority['name']; ?></option>

                <?php } ?>

            </select>

        </div>

    </div>

    <div class="col-md-6">

        <div class="form-group">

            <label for="task_status" class="control-label"><?php echo _l('task_status'); ?></label>

            <select name="task_status" class="selectpicker" id="task_status" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                <?php foreach ($task_status as $status) { ?>

                    <option <?php echo $status['id'] == $data->task_status ? 'selected' : '' ?> value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>

                <?php } ?>

            </select>

        </div>

    </div>

    <div class="col-md-6">

        <div class="form-group select-placeholder>">

            <label for="assignees"><?php echo _l('task_single_assignees'); ?></label>

            <select name="assignees[]" id="assignees" class="selectpicker" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"

                    multiple data-live-search="true">

                <?php foreach ($members as $member) {

                    $selected = "";
                    if( !empty( $data->assignees ) )
                    {
                        $assignees_data = json_decode( $data->assignees , 1 );

                        if( in_array( $member["staffid"] , $assignees_data ) )
                            $selected = "selected";
                    }

                    ?>

                    <option <?php echo $selected?> value="<?php echo $member['staffid']; ?>">

                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>

    </div>


    <div class="col-md-6 mtop10">

        <div class="checkbox checkbox-primary">

            <input type="checkbox" <?php echo $data->assign_project_owner == 1 ? 'checked' : ''?> id="assign_project_owner" name="assign_project_owner" value="1" >

            <label for="assign_project_owner"><?php echo _l('task_manage_assign_project_owner') ?></label>

        </div>

    </div>


    <div class="clearfix"></div>


    <!--
    @Version 1.1.2
    -->

    <div class="col-md-6">

        <?php echo render_select( 'task_created_project_status' , $project_status , [ 'id' , [ 'name' ] ] , 'task_manage_created_project_status' , $data->task_created_project_status )?>

    </div>

    <div class="col-md-6">

        <?php echo render_select( 'task_completed_project_status' , $project_status , [ 'id' , [ 'name' ] ] , 'task_manage_completed_project_status' , $data->task_completed_project_status )?>

    </div>



    <div class="col-md-6">

        <div class="form-group select-placeholder>">

            <label for="followers"><?php echo _l('task_single_followers'); ?></label>

            <select name="followers[]" id="followers" class="selectpicker" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"

                    multiple data-live-search="true">

                <?php foreach ($members as $member) {
                    $selected = "";
                    if( !empty( $data->followers ) )
                    {
                        $followers_data = json_decode( $data->followers , 1 );

                        if( in_array( $member["staffid"] , $followers_data ) )
                            $selected = "selected";
                    }
                    ?>

                    <option <?php echo $selected?> value="<?php echo $member['staffid']; ?>">

                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>

    </div>

    <div class="col-md-6">

        <div class="form-group">

            <label for="repeat_every" class="control-label"><?php echo _l('task_repeat_every'); ?></label>

            <select name="repeat_every" id="repeat_every" class="selectpicker" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                <option value=""></option>

                <option <?php echo '1-week' == $data->repeat_every ? 'selected' : '' ?> value="1-week"><?php echo _l('week'); ?></option>

                <option <?php echo '2-week' == $data->repeat_every ? 'selected' : '' ?> value="2-week">2 <?php echo _l('weeks'); ?></option>

                <option <?php echo '1-month' == $data->repeat_every ? 'selected' : '' ?> value="1-month">1 <?php echo _l('month'); ?></option>

                <option <?php echo '2-month' == $data->repeat_every ? 'selected' : '' ?> value="2-month">2 <?php echo _l('months'); ?></option>

                <option <?php echo '3-month' == $data->repeat_every ? 'selected' : '' ?> value="3-month">3 <?php echo _l('months'); ?></option>

                <option <?php echo '6-month' == $data->repeat_every ? 'selected' : '' ?> value="6-month">6 <?php echo _l('months'); ?></option>

                <option <?php echo '1-year' == $data->repeat_every ? 'selected' : '' ?> value="1-year">1 <?php echo _l('year'); ?></option>

                <option <?php echo 'custom' == $data->repeat_every ? 'selected' : '' ?> value="custom"><?php echo _l('recurring_custom'); ?></option>

            </select>

        </div>

    </div>

    <div class="col-md-6 hide">

        <div class="form-group">

            <label for="related" class="control-label"><?php echo _l('task_related_to'); ?></label>

            <select name="related" id="related" class="selectpicker" data-width="100%"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">


                <option <?php echo 'project' == $data->related ? 'selected' : '' ?> value="project"><?php echo _l('project'); ?></option>

                <option <?php echo 'customer' == $data->related ? 'selected' : '' ?> value="customer"><?php echo _l('client'); ?></option>


            </select>

        </div>

    </div>


    <div class="clearfix"></div>
    <hr class="hr-panel-separator" />

    <div class="col-md-12">

        <div class="form-group checklist-templates-wrapper">

            <label for="checklist_items">
                <?php echo _l('insert_checklist_templates'); ?>
            </label>

            <a class="btn" style="float:right;" onclick="add_new_checklist()"> <i class="fa fa-plus"></i> <?php echo _l('task_manage_new_checklist')?></a>

            <select id="checklist_items" name="checklist_items[]"

                    class="selectpicker checklist-items-template-select" multiple="1"

                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>"

                    data-width="100%" data-live-search="true" data-actions-box="true">

                <option value="" class="hide"></option>

                <?php foreach ($checklistTemplates as $chkTemplate) {

                    $selected = "";
                    if( !empty( $data->checklist_items ) )
                    {
                        $checklist_items_data = json_decode( $data->checklist_items , 1 );

                        if( in_array( $chkTemplate["id"] , $checklist_items_data ) )
                            $selected = "selected";
                    }
                    ?>

                    <option <?php echo $selected?> value="<?php echo $chkTemplate['id']; ?>">

                        <?php echo $chkTemplate['description']; ?>

                    </option>

                <?php } ?>

            </select>

        </div>

        <div class="form-group">

            <div id="inputTagsWrapper">

                <label for="tags" class="control-label">
                    <i class="fa fa-tag" aria-hidden="true"></i>
                    <?php echo _l('tags'); ?>
                </label>

                <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo !empty( $data->tags ) ? $data->tags : ''?>" data-role="tagsinput">

            </div>

        </div>

    </div>

    <div class="col-md-12">
        <?php echo task_manage_task_render_custom_fields( 'tasks' , $data->task_id ); ?>
    </div>

    <div class="col-md-12">
        <?php echo render_textarea('description', '', $data->description , [ 'rows' => 6, 'placeholder' => _l('task_add_description'), 'data-task-ae-editor' => true ], [], 'no-mbot', 'tinymce-task');?>
    </div>
