<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Lead_send_reminder extends Reminder_mail_template
{
    protected $reminder_id;
    protected $reminder_data;
    protected $user_email;
    public $slug = 'reminder-send-to-lead';
    public function __construct($reminder)
    {
        parent::__construct();
        $this->reminder_id     = $reminder['id'];
        $this->user_email = $reminder['email'];
        $this->reminder_data = $reminder;
        $this->set_reminder_merge_fields('reminder_merge_fields', $this->reminder_id, $this->reminder_data);
    }
    public function build()
    {
        $this->to($this->user_email)->set_rel_id($this->reminder_id);
    }
}
