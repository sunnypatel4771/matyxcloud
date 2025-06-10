
(function($) {
    "use strict";

    $(document).ready(function (){

        if ( $('a[href="'+admin_url+'projects/gantt"]').length == 1 )
        {

            $('a[href="'+admin_url+'projects/gantt"]').after( '<a href="'+admin_url+'project_kanban" data-toggle="tooltip" data-title="'+lang_project_kanban+'" class="btn btn-default btn-with-tooltip"><i class="fa fa-grip-vertical menu-icon" aria-hidden="true"></i></a>' );

        }

    });

})(jQuery);
