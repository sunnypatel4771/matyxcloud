<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Client_notes_client extends ClientsController
{

    public function __construct()
    {
        parent::__construct();
        
    }
    public function index()
    {
        
        $data['title'] = _l('client_notes');
        $this->db->where('userid', get_contact_user_id());
        $this->db->order_by('date', 'desc');
        $data['client_notes'] = $this->db->get(db_prefix() . 'clientnotes')->result_array();
        
        $this->data($data);
        $this->view('client/client_notes');
        $this->layout();
    }
    function add_note($userid,$type)
    {
        
        $this->db->insert(db_prefix().'clientnotes', [
            'userid' => $userid,
            'type' => $type,
            'note' => $this->input->post('description',false),
            'date' => date('Y-m-d H:i:s'),
            'msg_status' => 'received',
        ]);
        redirect(site_url('client_notes/client_notes_client'));
    }
}