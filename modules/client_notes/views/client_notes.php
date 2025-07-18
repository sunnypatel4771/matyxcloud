<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (isset($client)) { 
   $user_notes = get_client_notes($client->userid);
    
    ?>

<h4 class="customer-profile-group-heading">
    <?= _l('client_notes_tab'); ?>

</h4>
<div class="row">
    <div class="col-md-12">
        <a href="#" class="btn btn-primary mbot15" onclick="slideToggle('.usernote'); return false;">
            <i class="fa-regular fa-plus tw-mr-1"></i>
            <?= _l('new_note'); ?>
        </a>
        <div class="usernote hide">
            <?= form_open(admin_url('client_notes/add_note/' . $client->userid . '/customer')); ?>
            <?= render_textarea('description', 'note_description', '', ['rows' => 5],[],'','tinymce'); ?>
            <button class="btn btn-primary pull-right mbot15">
                <?= _l('submit'); ?>
            </button>
            <?= form_close(); ?>
        </div>
        <table class="table dt-table" data-order-col="2" data-order-type="desc">
            <thead>
                <tr>
                    <th width="50%">
                        <?= _l('clients_notes_table_description_heading'); ?>
                    </th>
                    <th>
                        <?= _l('clients_notes_table_addedfrom_heading'); ?>
                    </th>
                    <th>
                        <?= _l('clients_notes_table_dateadded_heading'); ?>
                    </th>
                    <th>
                        <?= _l('options'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_notes as $note) { ?>
                <tr>
                    <td width="50%">
                        <div
                            data-note-description="<?= e($note['id']); ?>">
                            <?= process_text_content_for_display($note['note']); ?>
                        </div>
                        <div data-note-edit-textarea="<?= e($note['id']); ?>"
                            class="hide">
                            
                            <textarea name="note" class="tinymce"
                                rows="4"><?= clear_textarea_breaks($note['note']); ?></textarea>
                            <div class="text-right mtop15">
                                <button type="button" class="btn btn-default"
                                    onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                                    <?= _l('cancel'); ?>
                                </button>
                                <button type="button" class="btn btn-primary"
                                    onclick="edit_client_note(<?= e($note['id']); ?>);">
                                    <?= _l('update_note'); ?>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($note['msg_status'] == 'sent') { ?>
                            <?= '<a href="' . admin_url('profile/' . $note['staffid']) . '">' . get_staff_full_name($note['staffid']) . '</a>' ?>    
                            <?php }else{ ?>
                                <?= customer_companyname($note['userid']);  ?>    
                           <?php  } ?>
                        
                    </td>
                    <td
                        data-order="<?= e($note['date']); ?>">
                        <?php if (! empty($note['date_contacted'])) { ?>
                        <span data-toggle="tooltip"
                            data-title="<?= e(_dt($note['date_contacted'])); ?>">
                            <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
                        </span>
                        <?php } ?>
                        <?= e(_dt($note['date'])); ?>
                    </td>
                    <td>
                    <?php if ($note['msg_status'] == 'sent') { ?>
                        <div class="tw-flex tw-items-center tw-space-x-2">
                            <?php if ($note['staffid'] == get_staff_user_id() || is_admin()) { ?>
                            <a href="#"
                                onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;"
                                class="text-muted">
                                <i class="fa-regular fa-pen-to-square fa-lg"></i>
                            </a>
                            <a href="<?= admin_url('client_notes/delete_note/' . $note['id']); ?>"
                                class="text-muted _delete">
                                <i class="fa-regular fa-trash-can fa-lg"></i>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>