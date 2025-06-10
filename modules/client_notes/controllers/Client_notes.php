<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Client_notes extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		
	}
    function index()
    {
        $this->load->model('Client_notes_model');
        $data['title'] = _l('client_notes');
        $data['client_notes'] = $this->Client_notes_model->get_client_notes();
        $this->load->view('client_notes', $data);
    }
    function add_note($userid,$type)
    {
        
        $this->db->insert(db_prefix().'clientnotes', [
            'userid' => $userid,
            'type' => $type,
            'note' => $this->input->post('description',false),
            'date' => date('Y-m-d H:i:s'),
            'staffid' => get_staff_user_id(),
            'msg_status' => 'sent',
        ]);
        redirect(admin_url('clients/client/'.$userid.'?group=client_notes'));
    }
    function edit_note($id){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'clientnotes', [
            'note' => $this->input->post('description',false),
        ]);
        echo json_encode(['success'=>true,'message'=>_l('client_note_updated')]);
    }
    function delete_note($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'clientnotes');

        if (!$this->input->is_ajax_request()) {
            
            set_alert('success', _l('deleted', _l('note')));
            redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);
        } else {
            echo json_encode(['success'=>true,'message'=>_l('client_note_deleted')]);
        }
        
    }
}