
(function($) {
    "use strict";


    $(document).ready(function (){

        user_mention_set_textarea_prop();

    })


    // model show action
    $("body").on('show.bs.modal', '.modal', function (event) {

        user_mention_set_textarea_prop();

        if ( user_mention_get_tab_is_note() == 'note' )
        {

            $('a[href="#lead_notes"]').tab('show');

        }

    });


})(jQuery);


function user_mention_set_textarea_prop()
{

    if( "undefined" != typeof user_mention_data && user_mention_data )
    {

        $('textarea').atwho({
            at: "@",
            data: user_mention_data,
            limit: 200
        });

    }

    user_mention_set_note_tab();

}

function user_mention_get_tab_is_note()
{

    var param_name = 'tab';

    let regex = new RegExp('[?&]' + param_name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec( window.location.href );

    if (!results) return null;
    if (!results[2]) return '';

    return decodeURIComponent(results[2].replace(/\+/g, ' '));

}

function user_mention_set_note_tab()
{
    if ( user_mention_get_tab_is_note() == 'note' )
    {
        $('a[href="#tab_notes"]').tab('show');

    }
}
