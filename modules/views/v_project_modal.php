
<div class="modal-header">

    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

    <h4 class="modal-title" id="myModalLabel">

        <?php echo $title; ?>

        <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex">

            <div class="tw-flex -tw-space-x-1">

                <?php foreach ($members as $member) { ?>

                    <span class="tw-group tw-relative"

                          data-title="<?php echo get_staff_full_name($member['staff_id']) . (has_permission('projects', '', 'create') || $member['staff_id'] == get_staff_user_id() ? ' - ' . _l('total_logged_hours_by_staff') . ': ' . seconds_to_time_format($member['total_logged_time']) : ''); ?>"

                          data-toggle="tooltip">

                            <?php echo staff_profile_image($member['staff_id'], ['tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white', '']); ?>

                        </span>

                <?php } ?>

            </div>

        </div>

        <?php

        echo '<span class="tw-ml-1 project_status tw-inline-block label project-status-' . $project->status . '" style="color:' . $project_status['color'] . ';border:1px solid ' . adjust_hex_brightness($project_status['color'], 0.4) . ';background: ' . adjust_hex_brightness($project_status['color'], 0.04) . ';">' . $project_status['name'] . '</span>';

        ?>

        <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex mleft25">
            <a target="_blank" href="<?php echo admin_url('projects/view/'.$project->id)?>"> <?php echo _l('task_manage_show_project')?></a>
        </div>

    </h4>

</div>

<div class="modal-body">

    <div class="row">

        <div class="col-md-12">

            <?php $this->load->view('v_project_overview'); ?>

        </div>

    </div>

</div>


<?php if (isset($project_overview_chart)) { ?>

    <script>

        var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;

    </script>

<?php } ?>


<script>


    $(function() {

        var project_progress_color = '<?php echo hooks()->apply_filters('admin_project_progress_color', '#84c529'); ?>';

        var circle = $('.project-progress').circleProgress({

            fill: {

                gradient: [project_progress_color, project_progress_color]

            }

        }).on('circle-animation-progress', function(event, progress, stepValue) {

            $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');

        });


        if (

            $("#timesheetsChart").length > 0 &&

            typeof project_overview_chart != "undefined"

        ) {

            var chartOptions = {

                type: "bar",

                data: {},

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    tooltips: {

                        enabled: true,

                        mode: "single",

                        callbacks: {

                            label: function (tooltipItems, data) {

                                return decimalToHM(tooltipItems.yLabel);

                            },

                        },

                    },

                    scales: {

                        yAxes: [

                            {

                                ticks: {

                                    beginAtZero: true,

                                    min: 0,

                                    userCallback: function (label, index, labels) {

                                        return decimalToHM(label);

                                    },

                                },

                            },

                        ],

                    },

                },

            };

            chartOptions.data = project_overview_chart.data;

            var ctx = document.getElementById("timesheetsChart");

            timesheetsChart = new Chart(ctx, chartOptions);

        }


        <?php if ( has_permission('projects', '', 'edit') ) { ?>

            init_selectpicker();

            if ( $('#project_overview_form').length > 0 )
            {

                appValidateForm($('#project_overview_form'), {
                    status: 'required'
                }, project_overview_form_function );

            }

        <?php } ?>

    });



    function project_overview_form_function(form)
    {


        $("body").append('<div class="dt-loader"></div>');

        $.post( form.action , $(form).serialize() ).done(function(response) {

            response = JSON.parse(response);

            if (response.success == true)
            {

                init_project_preview( <?php echo $project->id ?> );

                alert_float('success', response.message);

            }
            else
                alert_float('danger', response.message);

            $("body").find(".dt-loader").remove();

        });


        $("body").find(".dt-loader").remove();

        return false;

    }



</script>
