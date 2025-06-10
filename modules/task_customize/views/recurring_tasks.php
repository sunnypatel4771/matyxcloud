<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" id="vueApp">
      <div class="row _buttons tw-mb-2">
            <div class="col-md-8">


            </div>
            <div class="col-md-4">
                <?php $this->load->view('admin/tasks/filters', ['filters_wrapper_id' => 'vueApp', 'detached' => true]); ?>
            </div>
        </div>
        <div class="row">


       <div class="_hidden_inputs _filters _tasks_filters">
        <input type="hidden" name="recurring" value="1">
       </div>


            <div class="col-md-12">

                <div class="panel_s">
                    <div class="panel-body">

                  
                        <div class="panel-table-full">
                            <?php $this->load->view('admin/tasks/_table', ['bulk_actions' => true]); ?>
                        </div>
                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>