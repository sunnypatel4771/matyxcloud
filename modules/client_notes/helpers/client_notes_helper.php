<?php
defined('BASEPATH') or exit('No direct script access allowed');

function get_client_notes($userid)
{
    $CI = &get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->order_by('date', 'desc');
    return $CI->db->get(db_prefix() . 'clientnotes')->result_array();
}
function customer_companyname($id)
{
    $CI = &get_instance();
    $CI->db->select('company');
    $CI->db->from(db_prefix() . 'clients');
    $CI->db->where('userid', $id);
    $row = $CI->db->get()->row();
    return $row->company; 

    
}