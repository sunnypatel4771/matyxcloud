<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="panel_s panel-table-full">

    <div class="panel-body">

        <h4 class="tw-font-semibold tw-text-base tw-mb-4">

            <?php echo _l('project_files'); ?>

        </h4>

        <table class="table dt-table table-project-files" data-order-col="6" data-order-type="desc">

            <thead>

                <tr>

                    <th><?php echo _l('project_file_filename'); ?></th>

                    <th><?php echo _l('project_file__filetype'); ?></th>

                    <th><?php echo _l('project_discussion_last_activity'); ?></th>

                    <th><?php echo _l('project_discussion_total_comments'); ?></th>

                    <th><?php echo _l('project_file_visible_to_customer'); ?></th>

                    <th><?php echo _l('project_file_uploaded_by'); ?></th>

                    <th><?php echo _l('project_file_dateadded'); ?></th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($files as $file) {

                $path = get_upload_path_by_type('project') . $project->id . '/' . $file['file_name']; ?>

                <tr>


                    <td data-order="<?php echo $file['file_name']; ?>">

                        <?php if (is_image(PROJECT_ATTACHMENTS_FOLDER . $project->id . '/' . $file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))) {

                            echo '<div class="text-left">';

                                echo '<a href="'.project_file_url($file) .'" data-lightbox="task-attachment" >';

                                    echo '<img class="project-file-image img-table-loading" src="' . project_file_url($file, true) . '" width="100">';

                                echo '</a>';

                            echo '</div>';

                        }

                        echo $file['subject']; ?>

                    </td>

                    <td data-order="<?php echo $file['filetype']; ?>"><?php echo $file['filetype']; ?></td>

                    <td data-order="<?php echo $file['last_activity']; ?>">

                        <?php

                        if (!is_null($file['last_activity'])) { ?>

                            <span class="text-has-action" data-toggle="tooltip"

                                  data-title="<?php echo _dt($file['last_activity']); ?>">

                        <?php echo time_ago($file['last_activity']); ?>

                    </span>

                        <?php } else {

                            echo _l('project_discussion_no_activity');

                        } ?>

                    </td>

                    <?php $total_file_comments = total_rows(db_prefix() . 'projectdiscussioncomments', ['discussion_id' => $file['id'], 'discussion_type' => 'file']); ?>

                    <td data-order="<?php echo $total_file_comments; ?>">

                        <?php echo $total_file_comments; ?>

                    </td>

                    <td data-order="<?php echo $file['visible_to_customer']; ?>">

                        <?php

                        if ($file['visible_to_customer'] == 1)

                            echo "<label> "._l('lead_is_public_yes')." </label>";

                        else
                            echo "<label> "._l('lead_is_public_no')." </label>";?>

                    </td>

                    <td>

                        <?php if ($file['staffid'] != 0) {

                            $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']) . '">' . staff_profile_image($file['staffid'], [

                                    'staff-profile-image-small',

                                ]) . '</a>';

                            $_data .= ' <a href="' . admin_url('staff/member/' . $file['staffid']) . '">' . get_staff_full_name($file['staffid']) . '</a>';

                            echo $_data;

                        } else {

                            echo ' <img src="' . contact_profile_image_url($file['contact_id'], 'thumb') . '" class="client-profile-image-small mrigh5">

         <a href="' . admin_url('clients/client/' . get_user_id_by_contact_id($file['contact_id']) . '?contactid=' . $file['contact_id']) . '">' . get_contact_full_name($file['contact_id']) . '</a>';

                        } ?>

                    </td>

                    <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>


                </tr>

                <?php

            } ?>

            </tbody>

        </table>

    </div>

</div>

