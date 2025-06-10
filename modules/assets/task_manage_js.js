
(function($) {
    "use strict";

    $(document).ready(function (){

        var currentURL = window.location.href;

        /**
         * Project Add/Edit page
         */
        if( currentURL.indexOf("projects/project") !== -1 )
        {

            if( $('#tab_project').length > 0 )
            {

                $.post(admin_url+"task_manage/manage/check_project_page" )
                    .done(function ( response ) {

                        response = JSON.parse(response);

                        if( response.content )
                        {

                            $('#tab_project').prepend( response.content ).promise().done(function (){
                                init_selectpicker();
                            });

                        }


                    }) ;

            }

        }

        /**
         * Project view page task tab
         */
        if( currentURL.indexOf("projects/view") !== -1 && currentURL.indexOf("?group=project_tasks") !== -1 )
        {

            if( $('.tasks-table').length > 0 )
            {

                if ( $('#task_manage_diagram_div').length == 0 )
                {

                    $('.tasks-table').parents('.panel_s').before("<div id='task_manage_diagram_div'></div>");

                }

                $.post(admin_url+"task_manage/manage/project_task_diagrams" , { project_id : project_id } ).done(function ( response ){

                    response = JSON.parse( response );

                    $('#task_manage_diagram_div').html(response.content);

                })

            }

        }

    });



})(jQuery);

