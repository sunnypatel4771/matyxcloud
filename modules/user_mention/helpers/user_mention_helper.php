<?php


function user_mention_get_users( $description = '' )
{

    $users = get_instance()->db->select('staffid,firstname,lastname')->from(db_prefix().'staff')->where('active',1)->get()->result();

    $user_mention   = [];
    $user_ids       = [];

    if ( !empty( $users ) )
    {

        foreach ( $users as $user )
        {
            if ( !empty( $user->firstname ) || !empty( $user->lastname ) )
                $user_mention[$user->staffid] = "$user->firstname $user->lastname";
        }


        foreach ( $user_mention as $staff_id => $user)
        {

            //$pattern = '/@' . preg_quote($user, '/') . '\b/';

            $escaped_user = preg_quote($user, '/');

            $pattern = '/@' . $escaped_user . '\b/u';


            if ( preg_match( $pattern , $description ) )
            {

                $user_ids[ $staff_id ] = $staff_id;

            }

        }

    }


    return $user_ids;


}


function user_mention_relation_detail( $rel_type = '' , $rel_id = '' )
{

    $rel_data = get_relation_data($rel_type, $rel_id);

    $rel_val  = get_relation_values($rel_data, $rel_type);

    if ( !empty( $rel_val['link'] ) )
    {
        $rel_val['link'] = str_replace( admin_url() , '' , $rel_val['link'] );
    }
    else
    {
        $rel_val['link'] = '';
    }

    $rel_val['link'] .= user_mention_relation_detail_add($rel_type);

    if ( !isset( $rel_val['name'] ) )
        $rel_val['name'] = '';

    return $rel_val ;

}

function user_mention_relation_detail_add($rel_type){


     $rel_type_link = [
        'lead'              => "?tab=note",
        'contract'          => '?tab=note',
        'customer'          => '?group=notes',
        'proposal'          => '?tab=note',
        'ticket'            => '?tab=note',
        'invoice'           => '?tab=note',
        'estimate'          => '?tab=note',
    ];

     return !empty($rel_type_link[$rel_type]) ? $rel_type_link[$rel_type] : "";

}


/**
 * Note notification
 */
function user_mention_note_notification( $note_id = 0 )
{

    $note_detail = get_instance()->db->select('rel_id, rel_type, description')->from(db_prefix().'notes')->where('id',$note_id)->get()->row();

    if ( !empty( $note_detail ) )
    {

        $user_ids = user_mention_get_users( $note_detail->description );

        if ( !empty( $user_ids ) )
        {


            foreach ( $user_ids as $user_id )
            {

                $description = _l( 'user_mention_notification_text' , $note_detail->description );

                $related_data = user_mention_relation_detail( $note_detail->rel_type , $note_detail->rel_id );

                $note_detail->related_link = $related_data['link'];
                $note_detail->related_name = $related_data['name'];

                add_notification([

                    'description'     => $description,

                    'touserid'        => $user_id,

                    'fromcompany'     => 1,

                    'fromuserid'      => get_staff_user_id(),

                    'link'            => $note_detail->related_link ,

                    'additional_data' => serialize([

                        '<b>' . $note_detail->description . '</b>',

                    ]),

                ]);


                // Send mail
                $user_info = get_instance()->db->select('email, firstname, lastname, staffid')->from(db_prefix().'staff')->where('staffid',$user_id)->get()->row();

                if ( !empty( $user_info->email )  )
                    user_mention_send_mail( $user_info , $note_detail );

            }

        }


    }


}


function user_mention_send_mail( $user_info , $record_detail )
{


    $CI = &get_instance();

    $email  = $user_info->email;

    $record_detail->related_link = admin_url(  $record_detail->related_link );

    $sender_name        = get_staff_full_name( get_staff_user_id() );

    $subject            = _l('user_mention_mail_subject',$sender_name).' '._l($note_detail->rel_type).' : '.$record_detail->related_name;

    $view_data          = [
        'receiver_name'     => $user_info->firstname.' '.$user_info->lastname ,
        'sender_id'         => get_staff_user_id() ,
        'sender_name'       => $sender_name,
        'message'           => $record_detail->description ,
        'record_detail'     => $record_detail
    ];

    $view_name          = 'mail_content';
    $view_path          = module_views_path(USER_MENTION_MODULE_NAME,'my_mail_content');

    if ( file_exists( $view_path ) )
    {
        $view_name      = 'my_mail_content';
    }

    $message_content = $CI->load->view(USER_MENTION_MODULE_NAME."/".$view_name , $view_data ,true);

    $cnf = [

        'from_email' => get_option('smtp_email'),

        'from_name'  => get_option('companyname'),

        'email'      => $email,

    ];


    $CI->load->config('email');

    $CI->email->from($cnf['from_email'], $cnf['from_name']);

    $CI->email->to($cnf['email']);

    $CI->email->subject($subject);

    $CI->email->message($message_content);


    $systemBCC = get_option('bcc_emails');

    if ($systemBCC != '') {

        $CI->email->bcc($systemBCC);

    }

    $CI->email->send();

    $CI->email->send_queue();

}
