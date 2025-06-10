<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="p_buttons">
    <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5">
        <app-filters id="<?= $contracts_table->id(); ?>"
            view="<?= $contracts_table->viewName(); ?>"
            :saved-filters="<?= $contracts_table->filtersJs(); ?>"
            :available-rules="<?= $contracts_table->rulesJs(); ?>">
        </app-filters>
    </div>
    
    <div class="tw-inline pull-left">
        <a href="<?= admin_url('contracts/contract?customer_id='.$project->clientid.'&project_id='.$project->id); ?>"
            class="btn btn-primary pull-left display-block mright5">
            <i class="fa-regular fa-plus tw-mr-1"></i>
            <?= _l('new_contract'); ?>
        </a>
    </div>
</div>
<div class="clearfix"></div>
<div class="panel_s panel-table-full tw-mt-4">
    <div class="panel-body">
        <div class="project_contracts">
            <?php
                $this->load->view('admin/contracts/table_html', [
                    'table_id' => 'project_contracts',
                ]);
?>
        </div>
    </div>
</div>