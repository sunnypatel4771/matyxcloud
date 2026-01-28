<div class="clearfix"></div>
<div class="panel_s">
    <div class="panel-heading">
        <a href="#" onclick="project_task_resource_toggle(); return false;" id="a_project_resource_toggle">
            Show Resource Detail
        </a>
    </div>
    <div class="panel-body hide" id="div_project_resource_toggle">
        <div class="row">

            <!-- ===== CAM Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>CAM Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $onboarding_form_url = $projectResourceMap['onboarding_form_cam'] ?? '';
                        $ghl_dashboard_url_cam = $projectResourceMap['ghl_dashboard_cam'] ?? '';
                        $scope_doc_cam = $projectResourceMap['scope_doc_cam'] ?? '';
                        ?>
                        <div class="form-group">
                            <label for="onboarding_form_cam">Onboarding Form</label>
                            <div class="input-group">
                                <input type="text" name="onboarding_form_cam" id="onboarding_form_cam" value="<?php echo $onboarding_form_url; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ghl_dashboard_cam">GHL Dashboard</label>
                            <div class="input-group">
                                <input type="text" name="ghl_dashboard_cam" id="ghl_dashboard_cam" value="<?php echo $ghl_dashboard_url_cam; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="scope_doc_cam">Scope Doc</label>
                            <div class="input-group">
                                <input type="text" name="scope_doc_cam" id="scope_doc_cam" value="<?php echo $scope_doc_cam; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe custom_icon_size search-icon"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Web Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>Web Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $dev_site_url = $projectResourceMap['dev_site_url'] ?? '';
                        $page_copy_doc_web = $projectResourceMap['page_copy_doc_web'] ?? '';
                        $site_sheet_web = $projectResourceMap['site_sheet_web'] ?? '';
                        $dns_sheet_web = $projectResourceMap['dns_sheet_web'] ?? 'https://docs.google.com/spreadsheets/d/1IZio2ttg4l1uthtuzG99HQyzljfsRke1htj5INV1e4M/edit?usp=sharing';
                        $drive_folder_web = $projectResourceMap['drive_folder_web'] ?? '';
                        $current_website_web = $projectResourceMap['current_website_web'] ?? '';
                        ?>
                        <div class="form-group">
                            <label for="dev_site_url">Dev Site URL</label>
                            <div class="input-group">
                                <input type="text" name="dev_site_url" id="dev_site_url" value="<?php echo $dev_site_url; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="page_copy_doc_web">Page Copy Doc</label>
                            <div class="input-group">
                                <input type="text" name="page_copy_doc_web" id="page_copy_doc_web" value="<?php echo $page_copy_doc_web; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="site_sheet_web">Sitemap Sheet</label>
                            <div class="input-group">
                                <input type="text" name="site_sheet_web" id="site_sheet_web" value="<?php echo $site_sheet_web; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dns_sheet_web">DNS Sheet</label>
                            <div class="input-group">
                                <input type="text" name="dns_sheet_web" id="dns_sheet_web" value="<?php echo $dns_sheet_web; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="drive_folder_web">Drive Folder</label>
                            <div class="input-group">
                                <input type="text" name="drive_folder_web" id="drive_folder_web" value="<?php echo $drive_folder_web; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="current_website_web">Current Website</label>
                            <div class="input-group">
                                <input type="text" name="current_website_web" id="current_website_web" value="<?php echo $current_website_web; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== SEO Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>SEO Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $content_presentation = $projectResourceMap['content_presentation'] ?? '';
                        $audit_sheet = $projectResourceMap['audit_sheet'] ?? '';
                        ?>
                        <div class="form-group">
                            <label for="content_presentation">Content Presentation</label>
                            <div class="input-group">
                                <input type="text" name="content_presentation" id="content_presentation" value="<?php echo $content_presentation; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="audit_sheet">Audit Sheet</label>
                            <div class="input-group">
                                <input type="text" name="audit_sheet" id="audit_sheet" value="<?php echo $audit_sheet; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Content Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>Content Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $content_strategy_presentation = $projectResourceMap['content_strategy_presentation'] ?? '';
                        $master_content_calendar = $projectResourceMap['master_content_calendar'] ?? 'https://airtable.com/appwWcRy7scxY9WT0/shrekMsC1wd9WdN5w';
                        $content_creation_form = $projectResourceMap['content_creation_form'] ?? 'https://docs.google.com/forms/d/e/1FAIpQLSd8NDOPPogAFQsmp5LasA_9je_Rv1RI57JAWIWBB5g6ZcvB3A/viewform';
                        $content_pipeline_backlinks = $projectResourceMap['content_pipeline_backlinks'] ?? 'https://docs.google.com/spreadsheets/d/1PpTumUP54lUeRxHs4GwDcSqKlkESRFJFFOjcg8p1TXc/edit?usp=sharing';
                        $water_treatment_content_creation_form = $projectResourceMap['water_treatment_content_creation_form'] ?? 'https://forms.gle/pN9upmqNLyPagrHYAWater Treatment Content';
                        $pipeline_backlinks = $projectResourceMap['pipeline_backlinks'] ?? 'https://docs.google.com/spreadsheets/d/14sSKY2mSugRWf8g_WN-_BCzMs0yXb9oxBvgvh7wQvtI/edit?usp=sharing';
                        $organic_content_strategy = $projectResourceMap['organic_content_strategy'] ?? '';
                        ?>
                        <div class="form-group">
                            <label for="content_strategy_presentation">Content Strategy Presentation</label>
                            <div class="input-group">
                                <input type="text" name="content_strategy_presentation" id="content_strategy_presentation" value="<?php echo $content_strategy_presentation; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="master_content_calendar">Master Content Calendar</label>
                            <div class="input-group">
                                <input type="text" name="master_content_calendar" id="master_content_calendar" value="<?php echo $master_content_calendar; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="content_creation_form">Content Creation Form</label>
                            <div class="input-group">
                                <input type="text" name="content_creation_form" id="content_creation_form" value="<?php echo $content_creation_form; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="content_pipeline_backlinks">Content Pipeline Backlinks</label>
                            <div class="input-group">
                                <input type="text" name="content_pipeline_backlinks" id="content_pipeline_backlinks" value="<?php echo $content_pipeline_backlinks; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="water_treatment_content_creation_form">Water Treatment Content Creation Form</label>
                            <div class="input-group">
                                <input type="text" name="water_treatment_content_creation_form" id="water_treatment_content_creation_form" value="<?php echo $water_treatment_content_creation_form; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pipeline_backlinks">Pipeline Backlinks</label>
                            <div class="input-group">
                                <input type="text" name="pipeline_backlinks" id="pipeline_backlinks" value="<?php echo $pipeline_backlinks; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="organic_content_strategy">Organic Content Strategy</label>
                            <div class="input-group">
                                <input type="text" name="organic_content_strategy" id="organic_content_strategy" value="<?php echo $organic_content_strategy; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Paid Ads Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>Paid Ads Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php $strategy_doc = $projectResourceMap['strategy_doc_url_paid_ads'] ?? ''; ?>
                        <div class="form-group">
                            <label for="strategy_doc_url_paid_ads">Strategy Doc</label>
                            <div class="input-group">
                                <input type="text" name="strategy_doc_url_paid_ads" id="strategy_doc_url_paid_ads" value="<?php echo $strategy_doc; ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Automation Resources ===== -->
            <div class="col-md-4 mbot15">
                <div class="panel panel-default cutom_border_color">
                    <div class="panel-heading text-center cutom_color cutom_border_color">
                        <strong>Automation Resources</strong>
                    </div>
                    <div class="panel-body">
                        <?php
                        $page_copy_doc_url_automation = $projectResourceMap['page_copy_doc_url_automation'] ?? '';
                        $site_sheet_url_automation = $projectResourceMap['site_sheet_url_automation'] ?? '';
                        ?>
                        <div class="form-group">
                            <label for="page_copy_doc_url_automation">Page Copy Doc URL</label>
                            <div class="input-group">
                                <input type="text" name="page_copy_doc_url_automation" id="page_copy_doc_url_automation" value="<?php echo $page_copy_doc_url_automation; ?>"
                                    class="form-control" disabled>
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="site_sheet_url_automation">Sitemap Sheet URL</label>
                            <div class="input-group">
                                <input type="text" name="site_sheet_url_automation" id="site_sheet_url_automation" value="<?php echo $site_sheet_url_automation; ?>"
                                    class="form-control" disabled>
                                <span class="input-group-btn">
                                    <a href="javascript:void(0);" class="btn btn-default"> <i class="fa fa-globe search-icon custom_icon_size"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    .cutom_color {
        background-color: white !important;
        color: black !important;
    }

    .cutom_border_color {
        border-color: #c8c8c8 !important;
    }

    /* Minimal helper styles for Bootstrap 3 compatibility */
    #div_project_resource_toggle .row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
    }

    #div_project_resource_toggle .col-md-4 {
        display: flex;
    }

    #div_project_resource_toggle .panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    #div_project_resource_toggle .panel-body {
        flex-grow: 1;
        word-break: break-word;
        white-space: normal;
    }

    #div_project_resource_toggle a {
        word-wrap: break-word;
        overflow-wrap: break-word;
        display: inline-block;
        max-width: 100%;
        color: #007bff;
        text-decoration: underline;
    }
</style>

<script>
    function project_task_resource_toggle() {

        if ($('#div_project_resource_toggle').hasClass('hide')) {

            $('#div_project_resource_toggle').removeClass('hide');

            $('#a_project_resource_toggle').text("Hide Resource Detail");

        } else {

            $('#div_project_resource_toggle').addClass('hide');

            $('#a_project_resource_toggle').text("Show Resource Detail");

        }

    }

    $('.search-icon').on('click', function() {
        var inputVal = $(this).closest('.input-group').find('input').val().trim();

        if (inputVal) {
            if (!/^https?:\/\//i.test(inputVal)) {
                inputVal = 'https://' + inputVal;
            }

            window.open(inputVal, '_blank');
        } else {
            alert_float('danger', 'No Url Provided!');
        }
    });
</script>