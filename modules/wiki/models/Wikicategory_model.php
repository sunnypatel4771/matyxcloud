<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wikicategory_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        if (!empty($data)) {
            $this->db->insert(db_prefix() . 'wiki_category', $data);
            return $this->db->insert_id();
        }
        return false;
    }

    public function edit_category($id)
    {
        if (is_numeric($id) && $id != "") {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'wiki_category')->row();
        }
        return false;
    }

    public function update($id, $data)
    {
        if (is_numeric($id) && $id != "" && !empty($data)) {
            $this->db->where('id', $id)->update(db_prefix() . 'wiki_category', $data);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }

    public function delete_category($id)
    {
        if (is_numeric($id) && $id != "") {
            $this->db->where('id', $id)->delete(db_prefix() . 'wiki_category');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }
}
