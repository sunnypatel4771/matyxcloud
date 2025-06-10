<?php $CI = &get_instance();
    $CI->load->model('staff_model');
    $staff = $CI->staff_model->get(); ?>
   

<div role="tabpanel" class="tab-pane" id="client_custome">
     <div class="row">
     <div class="col-md-12">

     <?php  echo render_select('cam_id', $staff, ['staffid', ['firstname', 'lastname']], 'cam_id', $client->cam_id ?? '', []); ?>
     </div>
     <div class="col-md-12">

     <?php  echo render_select('optimizer_id', $staff, ['staffid', ['firstname', 'lastname']], 'optimizer_id', $client->optimizer_id ?? '', []); ?>
     </div>
     <div class="col-md-12">

     <?php  echo render_select('organic_social_id', $staff, ['staffid', ['firstname', 'lastname']], 'organic_social_id',  $client->organic_social_id ?? '', []); ?>
     </div>
     <div class="col-md-12">

     <?php  echo render_select('seo_lead_id', $staff, ['staffid', ['firstname',  'lastname']], 'seo_lead_id', $client->seo_lead_id ?? '', []); ?>
     </div>
     <div class="col-md-12">

     <?php  echo render_select('sale_rep_id', $staff, ['staffid', ['firstname', 'lastname']], 'sale_rep_id', $client->sale_rep_id ?? '', []); ?>
     </div>
    </div>

    </div>