<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_task_filter_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	* @param  integer (optional)
	* @return object
	* Get single task filter
	*/
	public function get($id = '')
	{
		$this->db->where('staff_id',get_staff_user_id());
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'si_task_filter')->row();
		}
		return $this->db->get(db_prefix() . 'si_task_filter')->result_array();
	}
	/**
	* get all filter templates of that staff
	*/
	function get_templates($staff_id)
	{
		if (is_numeric($staff_id)) {
			$this->db->where('staff_id', $staff_id);
			return $this->db->get(db_prefix() . 'si_task_filter')->result_array();
		}
		return array();
	}
	/**
	* Add new task filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function add($data)
	{
		if(isset($data['is_default']) && $data['is_default'] == 1)
			$this->remove_old_default_template($data['staff_id']);

		$this->db->insert(db_prefix() . 'si_task_filter', $data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			log_activity('New Custom Task Filter Added [Name:' . $data['filter_name'] . ']');
			return $insert_id;
		}
		return false;
	}
	/**
	* Update task filter
	* @param mixed $data All $_POST data
	* @return mixed
	*/
	public function update($data,$filter_id)
	{
		if(isset($data['is_default']) && $data['is_default'] == 1)
			$this->remove_old_default_template($data['staff_id']);

		$this->db->where('id',$filter_id);
		$update = $this->db->update(db_prefix() . 'si_task_filter', $data);
		if ($update) {
			log_activity('Custom Task Filter Updated [Name:' . $data['filter_name'] . ']');
			return true;
		}
		return false;
	}
	/**
	* Delete task filter
	* @param  mixed $id filter id
	* @return boolean
	*/
	public function delete($id,$staff_id)
	{
		$this->db->where('id', $id);
		$this->db->where('staff_id', $staff_id);
		$this->db->delete(db_prefix() . 'si_task_filter');
		if ($this->db->affected_rows() > 0) {
			log_activity('Custom Task Filter Deleted [ID:' . $id . ']');
			return true;
		}
		return false;
	}

	/**
	* get Default Template for staff
	* @param  mixed $staff_id staff id
	* @return int
	*/
	public function get_default_template($staff_id)
	{
		if($this->db->field_exists('is_default',db_prefix() . 'si_task_filter')){
			$this->db->where('staff_id', $staff_id);
			$this->db->where('is_default', 1);
			$result = $this->db->get(db_prefix() . 'si_task_filter')->row();
			if($result)
				return $result->id;
		}
		return "";
	}

	/**
	* remove previously set Default Template for staff
	* @param  mixed $staff_id staff id
	* @return boolean
	*/
	public function remove_old_default_template($staff_id)
	{
		$this->db->where('staff_id',$staff_id);
		$update = $this->db->update(db_prefix() . 'si_task_filter', ['is_default' => 0]);
		return $update;
	}
}
