<?php 
defined('BASEPATH') or exit('No direct script access allowed'); ?>


<div class="row">
<h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg tw-text-neutral-700 section-heading">
    <?= _l('Notes'); ?>
</h4>
    <div class="panel_s">
        <div class="panel-body">
        <a href="#" class="btn btn-primary mbot15" onclick="slideToggle('.usernote'); return false;">
            <i class="fa-regular fa-plus tw-mr-1"></i>
            <?= _l('new_note'); ?>
        </a>
        <div class="usernote hide">
            <?= form_open(admin_url('client_notes/client_notes_client/add_note/' . $client->userid . '/customer')); ?>
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
                       
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($client_notes as $note) { ?>
                <tr>
                    <td width="50%">
                        <div
                            data-note-description="<?= e($note['id']); ?>">
                            <?= process_text_content_for_display($note['note']); ?>
                        </div>
                        
                    </td>
                    <td>
                        <?php if ($note['msg_status'] == 'sent') { ?>
                            <?=  get_staff_full_name($note['staffid']); ?>    
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
                    
                </tr>
                <?php } ?>
            </tbody>
            </table>


        </div>
    </div>
</div>