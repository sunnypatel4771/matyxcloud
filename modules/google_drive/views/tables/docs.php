<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'title',
    'description',
    'date',
    db_prefix() . 'google_drives.staffid as doc_staffid'
];

$sIndexColumn       = 'id';
$sTable             = db_prefix() . 'google_drives';
$where              = [
    'AND ' . db_prefix() . 'google_drives.type = \'doc\''
];

$join = ['LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'google_drives.staffid'];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', db_prefix() . 'google_drives.driveid', db_prefix() . 'staff.profile_image', db_prefix() . 'staff.firstname', db_prefix() . 'staff.lastname']);

$output             = $result['output'];
$rResult            = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $aColumnsI = $aColumns[$i];
        if (strpos($aColumnsI, 'as') !== false && !isset($aRow[$aColumnsI])) {
            $aColumnsI = strafter($aColumnsI, 'as ');
        }
        $_data = $aRow[$aColumnsI];
        if ($aColumnsI == 'doc_staffid') {
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['doc_staffid']) . '">' . staff_profile_image($aRow['doc_staffid'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/member/' . $aRow['doc_staffid']) . '">' . e($aRow['firstname'] . ' ' . $aRow['lastname']) . '</a>';
        } elseif ($aColumnsI == 'date') {
            $_data = e(_d($_data));
        }

        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';    
    if (staff_can('edit', 'google_drive')) {
        $options .= '<a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" data-toggle="modal" data-target="#google_drive_doc_modal" data-id="' . $aRow['id'] . '">
                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                    </a>';
    }
    if (staff_can('view', 'google_drive')) {
        $options .= '<a href="' . admin_url('google_drive/view/' . $aRow['id']) . '" class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                        <i class="fa-regular fa-eye fa-lg"></i>
                    </a>';
    }
    if (staff_can('delete', 'google_drive')) {
        $options .= '<a href="' . admin_url('google_drive/delete/' . $aRow['id']) . '" class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                        <i class="fa-regular fa-trash-can fa-lg"></i>
                    </a>';
    }
    $options .= '</div>';

    $row[] = $options;
    
    $output['aaData'][] = $row;
}