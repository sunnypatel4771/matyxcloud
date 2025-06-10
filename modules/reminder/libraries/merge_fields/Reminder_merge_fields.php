<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reminder_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Name',
                'key'       => '{name}',
                'available' => ['client', 'leads'],
                'templates' => ['reminder-send-to-lead']
            ],
            [
                'name'      => 'Reminder Link',
                'key'       => '{reminder_link}',
                'available' => ['client', 'leads'],
                'templates' => ['reminder-send-to-lead']
            ],
            [
                'name'      => 'Relation Type',
                'key'       => '{rel_type}',
                'available' => ['client', 'leads'],
                'templates' => ['reminder-send-to-lead']
            ],
            [
                'name'      => 'Message',
                'key'       => '{item_description}',
                'available' => ['client', 'leads'],
                'templates' => ['reminder-send-to-lead']
            ],
        ];
    }
    public function format($reminder_id, $data)
    {
        $fields = [];
        $fields['{item_description}']          = $data['description'];
        if(isset($data['firstname']) && isset($data['lastname'])){
            $fields['{name}']                      = $data['firstname'] . ' ' . $data['lastname'];
        }if(isset($data['name'])){
            $fields['{name}']                      = $data['name'];
        }
        $fields['{reminder_link}']             = admin_url('reminder#' . $reminder_id);
        $fields['{rel_type}']                  = $data['rel_type'];
        return hooks()->apply_filters('reminder_merge_fields', $fields, [
            'data'      => $data,
        ]);
    }
    public function name()
    {
        if (is_null($this->for)) {
            $this->for = strtolower(strbefore(get_class($this), '_merge_fields'));
        }
        return $this->for;
    }
}
