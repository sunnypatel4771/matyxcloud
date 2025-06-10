<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();
$report_heading = '';
?>
<link href="<?php echo module_dir_url('si_task_filters','assets/css/si_task_filters_style.css'); ?>" rel="stylesheet" />
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_open($this->uri->uri_string() . ($this->input->get('filter_id') ? '?filter_id='.$this->input->get('filter_id') : ''),"id=si_form_task_filter"); ?>
						<h4 class="pull-left"><?php echo _l('custom_reports')." - "._l('tasks_filter'); ?> <small class="text-success"><?php echo $saved_filter_name;?></small></h4>
						<div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?php echo _l('filter_templates'); ?>" data-original-title="" title="">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-list"></i>
							</button>
							<ul class="row dropdown-menu notifications width400">
							<?php
							if(!empty($filter_templates))
							{
								foreach($filter_templates as $row)
								{
									echo "<li class='" . ($this->input->get('filter_id') == $row['id'] ? 'active' : "")."'><a href='".admin_url('si_task_filters/tasks_report').($switch_kanban == 1 ? '/kanban' : '')."?filter_id=$row[id]'>".($row['is_default']? "<i class='fa fa-star'></i> ":"")."$row[filter_name]</a></li>";
								}
							}
							else
								echo '<li><a >'._l('no_filter_template').'</a></li>';
							?>
							</ul>
						</div>
						<button type="submit" data-toggle="tooltip" data-title="<?php echo _l('si_apply_filter'); ?>" class=" pull-right btn btn-info mleft4"><?php echo _l('filter'); ?></button>
						<a href="<?php echo admin_url('si_task_filters/tasks_report')?>" class="pull-right btn btn-info mleft4" data-toggle="tooltip" data-title="<?php echo _l('si_task_filter_new_info'); ?>"><?php echo _l('si_task_filter_new'); ?></a>
						<!--<a href="<?php echo admin_url('si_task_filters/tasks_report').($switch_kanban == 1 ? '' : '/kanban');?>" class="btn btn-default mleft10 pull-right hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{echo _l('leads_switch_to_kanban');}; ?>
                        </a>-->
						<button id="switch_kanban" value="<?php echo $switch_kanban;?>" class="btn btn-default mleft10 pull-right hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{echo _l('leads_switch_to_kanban');}; ?>
                        </button>
						<?php echo form_hidden('kanban',$switch_kanban); ?>
						<div class="clearfix"></div>
						<hr />
						<div class="row">
							<?php if(has_permission('tasks','','view')){ ?>
							<div class="col-md-2 border-right">
								<label for="member" class="control-label"><?php echo _l('staff_members'); ?></label>
								<?php echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
							</div>
							<?php } ?>
							<div class="col-md-2 text-center1 border-right">
								<label for="status" class="control-label"><?php echo _l('task_status'); ?></label>		
								<div class="form-group no-margin select-placeholder">
									<select name="status[]" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>" multiple>
										<option value="" <?php if(in_array('',$statuses)){echo 'selected'; } ?>><?php echo _l('task_list_all'); ?></option>
										<?php foreach($task_statuses as $status){ ?>
										<option value="<?php echo $status['id']; ?>" <?php if(in_array($status['id'],$statuses)){echo 'selected'; } ?>>
										<?php echo $status['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<!--start rel type-->
							<div class="col-md-2 border-right mbot15">
								<label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
								<select name="rel_type" class="selectpicker" id="si_tf_rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""></option>
									<option value="project" <?php if(isset($rel_type)){if($rel_type == 'project'){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
									<option value="invoice" <?php if(isset($rel_type)){if($rel_type == 'invoice'){echo 'selected';}} ?>><?php echo _l('invoice'); ?></option>
									<option value="customer" <?php if(isset($rel_type)){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('client'); ?></option>
									<option value="estimate" <?php if(isset($rel_type)){if($rel_type == 'estimate'){echo 'selected';}} ?>><?php echo _l('estimate'); ?></option>
									<option value="contract" <?php if(isset($rel_type)){if($rel_type == 'contract'){echo 'selected';}} ?>><?php echo _l('contract'); ?></option>
									<option value="ticket" <?php if(isset($rel_type)){if($rel_type == 'ticket'){echo 'selected';}} ?>><?php echo _l('ticket'); ?></option>
									<option value="expense" <?php if(isset($rel_type)){if($rel_type == 'expense'){echo 'selected';}} ?>><?php echo _l('expense'); ?></option>
									<option value="lead" <?php if(isset($rel_type)){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('lead'); ?></option>
									<option value="proposal" <?php if(isset($rel_type)){if($rel_type == 'proposal'){echo 'selected';}} ?>><?php echo _l('proposal'); ?></option>
								</select>
							</div>
							<!--end of list of rel type-->
							<!--start rel_id select from rel_type-->
							<div class="col-md-2 border-right form-group<?php if($rel_id == '' && $rel_type==''){echo ' hide';} ?>" id="si_tf_rel_id_wrapper">
								<label for="rel_id" class="control-label"><span class="si_tf_rel_id_label"></span></label>
								<div id="si_tf_rel_id_select">
									<select name="rel_id" id="si_tf_rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<?php if($rel_id != '' && $rel_type != ''){
									$rel_data = get_relation_data($rel_type,$rel_id);
									$rel_val = get_relation_values($rel_data,$rel_type);
									echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
									if($group_by=='')
									$report_heading.=" - ".$rel_val['name'];
									} ?>
									</select>
								</div>
							</div>
							<!--end rel_id select-->
							<!--start group_id select from rel_id if rel_type is customer-->
							<div class="col-md-2 border-right form-group<?php if($rel_type !== 'customer'){echo ' hide';} ?>" id="group_id_wrapper">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('customer_groups'); ?></span></label>
								<div id="group_id_select">
									<select name="group_id" id="group_id" class="selectpicker no-margin" data-width="100%" >
										<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
										<?php if(!empty($groups)){
											foreach($groups as $group)
											{
												echo '<option value="'.$group['id'].'" '.($group_id!='' && $group_id==$group['id']?'selected':'').'>'.$group['name'].'</option>';
												if($group_id==$group['id'])
													$report_heading.=" (Group:".$group['name'].")";
											}
											} 
										?>
									</select>
								</div>
							</div>
							<!--end group_id select-->
							<!--start includes client rel type-->
							<div class="col-md-2 border-right <?php if($rel_type !== 'customer'){echo ' hide';} ?>" id="include_rel_type_wrapper">
								<label for="include_rel_type" class="control-label">
									<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('si_task_filter_client_include_task_info');?>"></i>
									<?php echo _l('si_task_filter_client_include_task'); ?>
								</label>
								<select name="include_rel_type[]" class="selectpicker" id="si_tf_include_rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="multiple">
									<option value=""></option>
									<option value="customer" <?php if(isset($include_rel_type)){if(in_array('customer',$include_rel_type)){echo 'selected';}} ?> selected disabled><?php echo _l('client'); ?></option>
									<option value="project" <?php if(isset($include_rel_type)){if(in_array('project',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('project'); ?></option>
									<option value="invoice" <?php if(isset($include_rel_type)){if(in_array('invoice',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('invoice'); ?></option>
									<option value="estimate" <?php if(isset($include_rel_type)){if(in_array('estimate',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('estimate'); ?></option>
									<option value="contract" <?php if(isset($include_rel_type)){if(in_array('contract',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('contract'); ?></option>
									<option value="ticket" <?php if(isset($include_rel_type)){if(in_array('ticket',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('ticket'); ?></option>
									<option value="expense" <?php if(isset($include_rel_type)){if(in_array('expense',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('expense'); ?></option>
									<option value="proposal" <?php if(isset($include_rel_type)){if(in_array('proposal',$include_rel_type)){echo 'selected';}} ?>><?php echo _l('proposal'); ?></option>
								</select>
							</div>
							<!--end of includes client rel type-->
						</div>
						<div class="row">
							<!--start billable select -->
							<div class="col-md-2 border-right form-group">
								<label for="billable" class="control-label"><span class="control-label"><?php echo _l('task_billable'); ?></span></label>
								<select name="billable" id="billable" class="selectpicker no-margin" data-width="100%" >
									<option value=""><?php echo _l('task_list_all'); ?></option>
									<option value="1" <?php echo ($billable!='' && $billable=="1"?'selected':'')?>><?php echo _l('Yes'); ?></option>
									<option value="0" <?php echo ($billable!='' && $billable=="0"?'selected':'')?>><?php echo _l('No'); ?></option>
								</select>
							</div>
							<!--end billable select-->
							<!--start priority select -->
							<div class="col-md-2 border-right form-group">
								<?php echo render_select('priority',get_tasks_priorities(),array('id','name'),'priority',$priority,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')),array(),'no-margin'); ?>
							</div>
							<!--end priority select-->
							<!--start group_by select -->
							<div class="col-md-2 border-right form-group  <?php echo ($switch_kanban == 1 ? 'hide' : '')?>">
								<label for="group_id" class="control-label"><span class="control-label"><?php echo _l('group_by_task'); ?></span></label>
								<select name="group_by" id="group_by" class="selectpicker no-margin" data-width="100%">
									<option value="" selected><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="rel_name" <?php echo ($group_by!='' && $group_by=='rel_name'?'selected':'')?>><?php echo _l('task_related_to'); ?></option>
									<option value="rel_name_and_name" <?php echo ($group_by!='' && $group_by=='rel_name_and_name'?'selected':'')?>><?php echo _l('task_related_to_and_name'); ?></option>
									<option value="name_and_rel_name" <?php echo ($group_by!='' && $group_by=='name_and_rel_name'?'selected':'')?>><?php echo _l('task_name_and_related_to'); ?></option>
									<option value="task_name" <?php echo ($group_by!='' && $group_by=='task_name'?'selected':'')?>><?php echo _l('filter_task_name'); ?></option>
									<option value="status" <?php echo ($group_by!='' && $group_by=='status'?'selected':'')?>><?php echo _l('task_status'); ?></option>
								</select>
							</div>
							<!--end group_by select-->
							<!--start hide_export_columns select -->
							<div class="col-md-2 border-right form-group <?php echo ($switch_kanban == 1 ? 'hide' : '')?>">
								<label for="hide_columns" class="control-label">
								<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('hide_export_columns_info');?>"></i>
								<span class="control-label"><?php echo _l('hide_export_columns'); ?></span>
								</label>
								<select name="hide_columns[]" id="hide_columns" class="selectpicker no-margin" data-width="100%" multiple>
									<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
									<option value="id" <?php echo (in_array('id',$hide_columns)?'selected':'')?>><?php echo _l('the_number_sign'); ?></option>
									<option value="name" <?php echo (in_array('name',$hide_columns)?'selected':'')?>><?php echo _l('tasks_dt_name'); ?></option>
									<?php
									$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
										echo "<option value='$field[slug]' ".(in_array($field['slug'],$hide_columns)?'selected':'').">$field[name]</option>";
									?>
									<option value="status" <?php echo (in_array('status',$hide_columns)?'selected':'')?>><?php echo _l('task_status'); ?></option>
									<option value="priority" <?php echo (in_array('priority',$hide_columns)?'selected':'')?>><?php echo _l('priority'); ?></option>
									<option value="start_date" <?php echo (in_array('start_date',$hide_columns)?'selected':'')?>><?php echo _l('tasks_dt_datestart'); ?></option>
									<option value="due_date" <?php echo (in_array('due_date',$hide_columns)?'selected':'')?>><?php echo _l('task_duedate'); ?></option>
									<option value="due_days" <?php echo (in_array('due_days',$hide_columns)?'selected':'')?>><?php echo _l('si_task_filter_due_days'); ?></option>
									<option value="completed_date" <?php echo (in_array('completed_date',$hide_columns)?'selected':'')?>><?php echo _l('task_completed_date'); ?></option>
									<option value="billable" <?php echo (in_array('billable',$hide_columns)?'selected':'')?>><?php echo _l('task_billable'); ?></option>
									<option value="attachments" <?php echo (in_array('attachments',$hide_columns)?'selected':'')?>><?php echo _l('tasks_total_added_attachments'); ?></option>
									<option value="comments" <?php echo (in_array('comments',$hide_columns)?'selected':'')?>><?php echo _l('tasks_total_comments'); ?></option>
									<option value="checklist" <?php echo (in_array('checklist',$hide_columns)?'selected':'')?>><?php echo _l('task_checklist_items'); ?></option>
									<option value="logged_time" <?php echo (in_array('logged_time',$hide_columns)?'selected':'')?>><?php echo _l('staff_stats_total_logged_time'); ?></option>
									<option value="on_time" <?php echo (in_array('on_time',$hide_columns)?'selected':'')?>><?php echo _l('task_finished_on_time'); ?></option>
									<option value="assigned" <?php echo (in_array('assigned',$hide_columns)?'selected':'')?>><?php echo _l('task_assigned'); ?></option>
									<option value="tags" <?php echo (in_array('tags',$hide_columns)?'selected':'')?>><?php echo _l('tags'); ?></option>
								</select>
							</div>
							<!--end hide_export_columns select-->
							<div class="col-md-2 form-group border-right" id="report-time">
								<label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
								<select class="selectpicker" name="report_months" id="report_months" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
									<option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
									<option value="today"><?php echo _l('today'); ?></option>
									<option value="this_week"><?php echo _l('this_week'); ?></option>
									<option value="last_week"><?php echo _l('last_week'); ?></option>
									<option value="this_month"><?php echo _l('this_month'); ?></option>
									<option value="1"><?php echo _l('last_month'); ?></option>
									<option value="this_year"><?php echo _l('this_year'); ?></option>
									<option value="last_year"><?php echo _l('last_year'); ?></option>
									<option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
									<option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
									<option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
									<option value="custom"><?php echo _l('period_datepicker'); ?></option>
								</select>
								<?php
									if($report_months !== '')
									{
										$report_heading.=' for '._l('period_datepicker')." ";
										switch($report_months)
										{
											case 'today':$report_heading.=_d(date('d-m-Y'))." To "._d(date('d-m-Y'));break;
											case 'this_week':$report_heading.=_d(date('d-m-Y', strtotime('monday this week')))." To "._d(date('d-m-Y', strtotime('sunday this week')));break;
											case 'last_week':$report_heading.=_d(date('d-m-Y', strtotime('monday last week')))." To "._d(date('d-m-Y', strtotime('sunday last week')));break;
											case 'this_month':$report_heading.=_d(date('01-m-Y'))." To "._d(date('t-m-Y'));break;
											case '1'         :$report_heading.=_d(date('01-m-Y',strtotime('-1 month')))." To "._d(date('t-m-Y',strtotime('-1 month')));break;
											case 'this_year' :$report_heading.=_d(date('01-01-Y'))." To "._d(date('31-12-Y'));break;
											case 'last_year' :$report_heading.=_d(date('01-01-Y',strtotime('-1 year')))." To "._d(date('31-12-Y',strtotime('-1 year')));break;
											case '3'         :$report_heading.=_d(date('01-m-Y',strtotime('-2 month')))." To "._d(date('t-m-Y'));break;
											case '6'         :$report_heading.=_d(date('01-m-Y',strtotime('-5 month')))." To "._d(date('t-m-Y'));break;
											case '12'        :$report_heading.=_d(date('01-m-Y',strtotime('-11 month')))." To "._d(date('t-m-Y'));break;
											case 'custom'    :$report_heading.=$report_from." To ".$report_to;break;
											default          :$report_heading.='All Time';
										}
									}
								?>
							</div>
							<!--start filter_by select -->
							<div class="col-md-2 border-right form-group<?php if($date_by == ''){echo ' hide';} ?>" id="date_by_wrapper">
								<label for="date_by" class="control-label"><span class="control-label"><?php echo _l('task_filter_by_date'); ?></span></label>
								<select name="date_by" id="date_by" class="selectpicker no-margin" data-width="100%" >
									<option value="startdate"><?php echo _l('tasks_dt_datestart'); ?></option>
									<option value="datefinished" <?php echo ($date_by!='' && $date_by=='datefinished'?'selected':'')?>><?php echo _l('task_completed_date'); ?></option>
								</select>
							</div>
							<!--end filter_by select-->
							<div id="date-range" class="col-md-4 hide mbot15">
								<div class="row">
									<div class="col-md-6">
										<label for="report_from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_from" name="report_from" value="<?php echo $report_from;?>" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
									<div class="col-md-6 border-right">
										<label for="report_to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
										<div class="input-group date">
											<input type="text" class="form-control datepicker" id="report_to" name="report_to" autocomplete="off">
											<div class="input-group-addon">
												<i class="fa fa-calendar calendar-icon"></i>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--end date time div-->
							<!--start tags -->
							<div class="col-md-2 border-right mbot15">
								<?php 
								echo render_select('tags[]',get_tags(),array('id','name'),'tags',$tags,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>false),array(),'no-mbot','',false);?>
							</div>
							<!--end tags-->
							<!--start set as default-->
							<div class="col-md-2 border-right mtop10 mbot15">
								<div class="checkbox checkbox-success" data-toggle="tooltip" title="" data-original-title="<?php echo _l('si_task_filter_set_as_default_info'); ?>">
									<input type="checkbox" id="si_ts_is_default" name="is_default" value="1" title="<?php echo _l('si_task_filter_set_as_default_info'); ?>" <?php echo ($is_default == 1?'checked':'')?>>
									<label for=""><span><?php echo _l('si_task_filter_set_as_default'); ?></span></label>
								</div>
							</div>
							<!--end set as default-->
							<!--start save filter-->
							<div class="col-md-8">
								<div class="checklist relative">
									<div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="" data-original-title="<?php echo _l('save_filter_template'); ?>">
										<input type="checkbox" id="si_save_filter" name="save_filter" value="1" title="<?php echo _l('save_filter_template'); ?>" <?php echo ($this->input->get('filter_id')?'checked':'')?>>
										<label for=""><span class="hide"><?php echo _l('save_filter_template'); ?></span></label>
										<textarea id="si_filter_name" name="filter_name" rows="1" placeholder="<?php echo _l('filter_template_name'); ?>" <?php echo ($this->input->get('filter_id')?'':'disabled="disabled"')?> maxlength='100'><?php echo ($this->input->get('filter_id')?$saved_filter_name:'');?></textarea>
									</div>
								</div>
							</div>
							<!--end save filter-->
						</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<?php if($switch_kanban==1){?>
						<div class="panel_s">
							<div class="panel-body">
								<div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
									<div class="row">
										<div id="kanban-params">
										   <?php echo form_hidden('filter_id',$this->input->get('filter_id')); ?>
										</div>
										<div class="container-fluid">
										   <div id="kan-ban"></div>
										</div>
									</div>
								</div>
							</div>
						</div>		   <?php
					}else{	
					//start list view
				?>
				<div class="panel_s">
					<div class="panel-body">
					<?php
					foreach($overview as $month =>$data){ if(count($data) == 0){continue;} ?>
						<h4 class="bold text-success"><?php echo $month; ?>
						<?php if($this->input->get('project_id')){ echo ' - ' . get_project_name_by_id($this->input->get('project_id'));} ?>
						<?php if(is_numeric($staff_id) && has_permission('tasks','','view')) { echo ' ('.get_staff_full_name($staff_id).')';} ?>
						</h4>
						<table class="table tasks-overview dt-table scroll-responsive" data-order-type="desc" data-order-col="0">
							<caption class="si_caption"><?php echo $month.$report_heading;?></caption>
							<thead>
								<tr>
									<th class="<?php echo (in_array('id',$hide_columns)?'not-export':'')?>"><?php echo _l('the_number_sign'); ?></th>
								<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<th class="<?php echo (in_array('name',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_dt_name'); ?></th>
								<?php }?>
								<?php
									$custom_fields = get_custom_fields('tasks', ['show_on_table' => 1,]);
									foreach($custom_fields as $field)
									{
										echo '<th class="'.(in_array($field['slug'],$hide_columns)?'not-export':'').'">'.$field['name'].'</th>';	
									}
								?>
									<th class="<?php echo (in_array('status',$hide_columns)?'not-export':'')?>"><?php echo _l('task_status'); ?></th>
									<th class="<?php echo (in_array('priority',$hide_columns)?'not-export':'')?>"><?php echo _l('priority'); ?></th>
									<th class="<?php echo (in_array('start_date',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_dt_datestart'); ?></th>
									<th class="<?php echo (in_array('due_date',$hide_columns)?'not-export':'')?>"><?php echo _l('task_duedate'); ?></th>
									<th class="<?php echo (in_array('due_days',$hide_columns)?'not-export':'')?>"><?php echo _l('si_task_filter_due_days'); ?></th>
									<th class="<?php echo (in_array('completed_date',$hide_columns)?'not-export':'')?>"><?php echo _l('task_completed_date'); ?></th>
									<th class="<?php echo (in_array('billable',$hide_columns)?'not-export':'')?>"><?php echo _l('task_billable'); ?></th>
									<th class="<?php echo (in_array('attachments',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_total_added_attachments'); ?></th>
									<th class="<?php echo (in_array('comments',$hide_columns)?'not-export':'')?>"><?php echo _l('tasks_total_comments'); ?></th>
									<th class="<?php echo (in_array('checklist',$hide_columns)?'not-export':'')?>"><?php echo _l('task_checklist_items'); ?></th>
									<th class="<?php echo (in_array('logged_time',$hide_columns)?'not-export':'')?>"><?php echo _l('staff_stats_total_logged_time'); ?></th>
									<th class="<?php echo (in_array('on_time',$hide_columns)?'not-export':'')?>"><?php echo _l('task_finished_on_time'); ?></th>
									<th class="<?php echo (in_array('assigned',$hide_columns)?'not-export':'')?>"><?php echo _l('task_assigned'); ?></th>
									<th class="<?php echo (in_array('tags',$hide_columns)?'not-export':'')?>"><?php echo _l('tags'); ?></th>
								</tr>
							</thead>
						<tbody>
							<?php
								foreach($data as $task){ ?>
								<tr>
									<td data-order="<?php echo htmlentities($task['id']); ?>"><a href="<?php echo admin_url('tasks/view/'.$task['id']); ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['id']; ?></a>
									</td>
								<?php if (($group_by!=='rel_name_and_name' && $group_by!=='name_and_rel_name') || $month==''){?>
									<td data-order="<?php echo htmlentities($task['name']); ?>"><a href="<?php echo admin_url('tasks/view/'.$task['id']); ?>" onclick="init_task_modal(<?php echo $task['id']; ?>); return false;"><?php echo $task['name']; ?></a> 
									<?php if($task['is_recurring_from'] > 0){?><a class="text-muted" href="<?php echo admin_url('tasks/view/'.$task['is_recurring_from']); ?>" onclick="init_task_modal(<?php echo $task['is_recurring_from']; ?>); return false;" data-toggle="tooltip" data-title="<?php echo _l('si_task_filter_recurred_from')?>"><i class="fa fa-chain"></i></a><?php }?>
									<?php
										if (!empty($task['rel_id']) && $group_by!='rel_name')
											echo '<br />'. _l('task_related_to').': <a class="text-muted" href="' . task_rel_link($task['rel_id'],$task['rel_type']) . '">' . task_rel_name($task['rel_name'],$task['rel_id'],$task['rel_type']) . '</a>';
									?>
									</td>
								<?php }?>
								<?php
									foreach($custom_fields as $field)
									{
										$current_value = get_custom_field_value($task['id'], $field['id'], 'tasks', false);
										echo '<td>'.(($field['type']=='date_picker' || $field['type']=='date_picker_time') && $current_value!='' ? date('d-m-Y',strtotime($current_value)):$current_value).'</td>';
									}
								?>
									<td id="si-tbl-id-<?php echo $task['id']?>">
										<?php //echo format_task_status($task['status']); 
										$canChangeStatus = (has_permission('tasks', '', 'edit'));
										$status          = get_task_status_by_id($task['status']);
										$outputStatus    = '';
									
										$outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $task['status'] . '">';
									
										$outputStatus .= $status['name'];
									
										if ($canChangeStatus) {
											$outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
											$outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $task['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
											$outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
											$outputStatus .= '</a>';
									
											$outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $task['id'] . '">';
											foreach ($task_statuses as $taskChangeStatus) {
												if ($task['status'] != $taskChangeStatus['id'] && $taskChangeStatus['id']!=Tasks_model::STATUS_TESTING) {
													$outputStatus .= '<li>
													  <a href="#" onclick="si_tasks_status_update(' . $taskChangeStatus['id'] . ',' . $task['id'] . '); return false;">
														 ' . _l('task_mark_as', $taskChangeStatus['name']) . '
													  </a>
												   </li>';
												}
											}
											$outputStatus .= '</ul>';
											$outputStatus .= '</div>';
										}
									
										$outputStatus .= '</span>';
										echo $outputStatus; ?></td>
									<?php $task_priority = task_priority($task['priority'])?>
									<td data-order="<?php echo $task_priority; ?>"><span style="color:<?php echo  task_priority_color($task['priority'])?>;" class="inline-block"><?php echo $task_priority; ?></span>
									</td>	
									<td data-order="<?php echo $task['startdate']; ?>"><?php echo _d($task['startdate']); ?></td>
									<td data-order="<?php echo $task['duedate']; ?>"><?php echo _d($task['duedate']); ?></td>
									<?php $days=''; 	
										if(is_date($task['duedate']) && $task['status'] != Tasks_model::STATUS_COMPLETE){
											$now = time();
											$due_date = strtotime($task['duedate'].' 23:59:59');
											$datediff = $due_date - $now;
											$days = round($datediff / (86400));//60 * 60 * 24
										}
									?>
									<td class="<?php echo ($days < 0 ? 'text-danger' : '')?>" data-order="<?php echo $days; ?>"><?php echo $days; ?></td>
									<td data-order="<?php echo $task['datefinished']; ?>"><?php echo _d($task['datefinished']); ?></td>
									<td data-order="<?php echo $task['billable']; ?>"><?php echo ($task['billable']?'Yes':'No'); ?></td>
									<td data-order="<?php echo $task['total_files']; ?>">
										<span class="label label-default" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_added_attachments'); ?>">
											<a <?php if($task['total_files']>0) echo 'href="'.admin_url('tasks/download_files/'.$task['id']).'"';?> class="bold" disabled>
												<i class="fa fa-paperclip"></i>
												<?php
												if(!is_numeric($staff_id)) {
													echo $task['total_files'];
												}else{
													echo $task['total_files_staff'] . '/' . $task['total_files'];
												}
												?>
											</a>
										</span>
									</td>
									<td data-order="<?php echo $task['total_comments']; ?>">
										<span class="label label-default" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_comments'); ?>">
											<i class="fa fa-regular fa-comments"></i>
											<?php
											 if(!is_numeric($staff_id)) {
												echo $task['total_comments'];
											 } else {
												echo $task['total_comments_staff'] . '/' . $task['total_comments'];
											 }
											?>
										</span>
									</td>
									<td>
										<span class="label <?php if($task['total_checklist_items'] == '0'){ echo 'label-default'; } else if(($task['total_finished_checklist_items'] != $task['total_checklist_items'])){ echo 'label-danger';}
										else if($task['total_checklist_items'] == $task['total_finished_checklist_items']){echo 'label-success';} ?> pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('tasks_total_checklists_finished'); ?>">
											<i class="fa fa-th-list"></i>
											<?php echo $task['total_finished_checklist_items']; ?>/<?php echo $task['total_checklist_items']; ?>
										</span>
									</td>
									<td data-order="<?php echo $task['total_logged_time']; ?>">
										<span class="label label-default pull-left mright5" data-toggle="tooltip" data-title="<?php echo _l('staff_stats_total_logged_time'); ?>">
											<i class="fa fa-clock-o"></i> <?php echo seconds_to_time_format($task['total_logged_time']); ?>
										</span>
									</td>
									<?php
									$finished_on_time_class = '';
									$finishedOrder = 0;
									if(date('Y-m-d',strtotime($task['datefinished'] ?? '')) > $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])){
										$finished_on_time_class = 'text-danger';
										$finished_showcase = _l('task_not_finished_on_time_indicator');
									} else if(date('Y-m-d',strtotime($task['datefinished'] ?? '')) <= $task['duedate'] && $task['status'] == Tasks_model::STATUS_COMPLETE && is_date($task['duedate'])){
										$finishedOrder = 1;
										$finished_showcase = _l('task_finished_on_time_indicator');
									} else {
										$finished_on_time_class = '';
										$finished_showcase = '';
									}
									?>
									<td data-order="<?php echo $finishedOrder; ?>">
										<span class="<?php echo $finished_on_time_class; ?>">
										<?php echo $finished_showcase; ?>
										</span>
									</td>
									<td>
										<?php echo format_members_by_ids_and_names($task['assignees_ids'],$task['assignees'], false);?>
									</td>
									<td><?php echo  render_tags(prep_tags_input(get_tags_in($task['id'],'task'))); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<hr />
					<?php } ?>
					</div>
				</div>
				<?php } //end of list view?>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script src="<?php echo module_dir_url('si_task_filters','assets/js/si_task_filters_task_report.js'); ?>"></script>
<script>
(function($) {
"use strict";
<?php  if($report_months !== ''){ ?>
	$('#report_months').val("<?php echo $report_months;?>");
	$('#report_months').change();		
<?php }
	if($report_from !== ''){ 
?>
	$('#report_from').val("<?php echo $report_from;?>");
<?php
	}
	if($report_to !== ''){ 
?>
	$('#report_to').val("<?php echo $report_to;?>");
<?php
	}
?>
})(jQuery);	
//update task status
function si_tasks_status_update(status, task_id) 
{
	task_mark_as(status, task_id);
	setTimeout(function() {
		$.get(admin_url + 'si_task_filters/get_task_status/'+task_id, function(response) {
			response = JSON.parse(response);
			if (response.success == true && response.taskHtml !='undefined') {
				$('#si-tbl-id-'+task_id).html(response.taskHtml);
			}
		});
	}, 300);
}			  
</script>

