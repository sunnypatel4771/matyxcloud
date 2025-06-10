<?php
defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
    'ticketstatusid',
    'name',
    'statuscolor',
    'statusorder',
    'is_active',
];

$sIndexColumn = 'ticketstatusid';
$sTable       = db_prefix() . 'tickets_status';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

$CI = &get_instance();
$CI->load->model('Ticket_status_model');

// Create map with status id as key and status info as value
// We use this map in status_can_change_to column
$statusMap = [];
foreach ($CI->Ticket_status_model->get('', false, false) as $aRow) {
    $statusMap[$aRow['ticketstatusid']] = ['name' => $aRow['name'], 'color' => $aRow['statuscolor']];
}

foreach ($rResult as &$aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {
        // For some of the fields we need to insert custom content
        if ($aColumns[$i] == 'statuscolor')
        {
            $_data = "<span style='color:{$aRow[$aColumns[$i]]}'>{$aRow[$aColumns[$i]]}</span>";
        }
        elseif ($aColumns[$i] == 'is_active')
        {
            $_data = $aRow[$aColumns[$i]] ? "Yes" : "No";
        }
        elseif ($aColumns[$i] == 'name')
        {
            $_data = $aRow[$aColumns[$i]];
            $_data .= '<div class="row-options">';
            $_data .= '<a class="cursor-pointer" onclick="edit_status(' . $aRow['ticketstatusid'] . ', `ticket`)">' . _l('edit') . '</a>';
            $_data .= ' | <a href="' . admin_url('advanced_status_manager/delete_ticket_status/' . $aRow['ticketstatusid']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            $_data .= '</div>';
        }
        else
        {
            $_data = $aRow[$aColumns[$i]];
        }
        $row[] = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
