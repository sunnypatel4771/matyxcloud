<?php

hooks()->add_action('note_created', function ( $note_id = 0 ){

    user_mention_note_notification( $note_id );

});


hooks()->add_action('note_updated', function ( $note_id = 0 ){

    user_mention_note_notification( $note_id );

});


hooks()->add_action('after_invoice_preview_template_rendered', function (){

    echo "<script> user_mention_set_textarea_prop(); </script>";

});

hooks()->add_action('after_proposal_view_as_client_link', function (){

    echo "<script> user_mention_set_textarea_prop(); </script>";

});

hooks()->add_action('after_estimate_view_as_client_link', function (){

    echo "<script> user_mention_set_textarea_prop(); </script>";

});
