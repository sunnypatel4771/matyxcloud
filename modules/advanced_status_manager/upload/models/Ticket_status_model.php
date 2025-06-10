<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_status_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get ticket statuses all or by id
     * @param mixed $id ticket status id
     * @return object|array Ticket status/es
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('ticketstatusid', $id);
        }

        $statuses = $this->db->get(db_prefix() . 'tickets_status')->result_array();

        if (!$statuses) {
            return false;
        }

        if ($id) {
            $statuses = $statuses[0];
        }

        return $statuses;
    }

    /**
     * Update ticket status
     * @param array $data ticket status $_POST data
     * @param mixed $statusId ticket status id
     * @return boolean
     */
    public function update($data, $statusId)
    {
        $this->db->where('ticketstatusid', $statusId);
        $this->db->update(db_prefix() . 'tickets_status', [
            'name' => $data['name'],
            'statusorder' => $data['statusorder'],
            'statuscolor' => $data['statuscolor'],
            'is_active' => isset($data['is_active']),
        ]);

        log_activity('Ticket Status Updated [ID: ' . $statusId . ', Name: ' . $data['name'] . ']');

        return true;
    }

    /**
     * Add new ticket status
     * @param array $data ticket status $_POST data
     * @return mixed
     */
    public function store($data)
    {
        $this->db->insert(db_prefix() . 'tickets_status', [
            'name' => $data['name'],
            'statusorder' => $data['order'],
            'statuscolor' => $data['color'],
            'is_active' => isset($data['is_active']),
        ]);
        $statusId = $this->db->insert_id();

        if (!$statusId) {
            return false;
        }

        log_activity('New Ticket status added [ID: ' . $statusId . ', Name: ' . $data['name'] . ']');

        return $statusId;
    }

    /**
     * Delete statuses and all connections
     * @param mixed $statusId ticket status id
     * @return boolean
     */
    public function delete($statusId)
    {
        $this->db->where('ticketstatusid', $statusId);
        $this->db->delete(db_prefix() . 'tickets_status');

        return true;
    }
}
