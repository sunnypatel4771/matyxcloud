<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<li data-project-id="<?php echo $project->project_id; ?>" >

    <div class="panel-body">

        <div class="row">

            <div class="col-md-12">



                <h4 class="tw-font-semibold  tw-text-base pipeline-heading tw-mb-0.5">

                    <a style="cursor: pointer" target="_blank" href="#"

                       onclick="init_project_preview(<?php echo $project->project_id ?>); return false;"

                       data-toggle="tooltip" data-title="<?php echo $project->project_name; ?>"

                       class="tw-text-neutral-700 hover:tw-text-neutral-900 active:tw-text-neutral-900" >

                        <?php echo $project->project_name; ?>

                    </a>

                    <a style="cursor: pointer; float: right" target="_blank" href="#"

                       onclick="init_project_preview(<?php echo $project->project_id ?>); return false;"

                       data-toggle="tooltip" data-title="<?php echo _l('view')?>" >
                        <small>
                            <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i>
                        </small>
                    </a>

                </h4>


                <span style="font-size: 15px">

                    <?php

                    $client_url = admin_url('clients/client/' . $project->clientid );

                    $client_company = $project->company;

                    if ( empty( $client_company ) )
                    {
                        $contact_info = $this->db->select('id , firstname , lastname ')->where('userid', $project->clientid)->where('is_primary',1)->get(db_prefix() . 'contacts')->row();

                        if ( !empty( $contact_info ) )
                        {

                            $client_url .= "?group=contacts&contactid=$contact_info->id";

                            $client_company = $contact_info->firstname.' '.$contact_info->lastname;

                        }
                    }

                    echo '<a href="' . $client_url . '" data-toggle="tooltip" data-title="' . _l('client') . '">' . $client_company . '</a>';

                    ?>

                </span>


            </div>

            <div class="col-md-12">

                <div class="tw-flex">

                    <div class="tw-grow">

                        <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span class="tw-text-neutral-500">
                                    <b><?php echo _l('project_start_date'); ?>:</b>
                                </span>
                            <?php echo _d( $project->start_date ); ?>
                        </p>

                        <?php

                        $class_project_deadline = '';

                        if ( !empty( $project->deadline ) && $project->deadline < date( 'Y-m-d') )
                            $class_project_deadline = ' style="color:red!important" ';

                        ?>
                        <p <?php echo $class_project_deadline?> class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span <?php echo $class_project_deadline?> class="tw-text-neutral-500">
                                    <b><?php echo _l('project_deadline'); ?>:</b>
                                </span>
                            <?php echo _d( $project->deadline ); ?>
                        </p>


                        <?php if ( !empty( $project->es_status_change_date ) ) { ?>
                            <p class="tw-mb-0 tw-text-sm tw-text-neutral-700">
                                <span class="tw-text-neutral-500">
                                    <b><?php echo _l('project_kanban_status_change_date'); ?>:</b>
                                </span>
                                <?php echo _dt( $project->es_status_change_date ); ?>
                            </p>

                        <?php } ?>


                    </div>

                </div>

            </div>




            <div class="col-md-12 tw-text-neutral-600 mtop10 tw-text-sm">

                <?php

                $project_members = $this->db->select('staffid, firstname, lastname')
                                            ->from(db_prefix().'project_members m')
                                            ->join(db_prefix().'staff s','s.staffid = m.staff_id')
                                            ->where('m.project_id',$project->project_id)
                                            ->get()
                                            ->result();

                $member_ids = '';
                $member_text = '';

                foreach ( $project_members as $project_member )
                {

                    if( $member_ids != '' )
                        $member_ids .= ',';

                    $member_ids .= $project_member->staffid;


                    if( $member_text != '' )
                        $member_text .= ',';

                    $member_text .= $project_member->firstname.' '.$project_member->lastname;

                }

                echo format_members_by_ids_and_names( $member_ids , $member_text , 'sm');

                ?>

            </div>

            <div class="clearfix"></div>

            <div class="col-md-12">

                <?php $tags = get_tags_in( $project->project_id , 'project'); ?>
                <?php if (count($tags) > 0) { ?>
                    <div class="kanban-tags tw-text-sm tw-inline-flex">
                        <?php echo render_tags($tags); ?>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>

</li>
