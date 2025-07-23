<?php
defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'wiki_category.id as id',
    db_prefix() . 'wiki_category.name as name',
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'wiki_category';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];
    $row[] = $aRow['name'];
    $row[] = '<a class="btn btn-default btn-icon edit_category" data-id="' . $aRow['id'] . '"><i class="fa fa-edit"></i></a>
    <a class="btn btn-danger btn-icon delete_category _delete"  data-id="' . $aRow['id'] . '"><i class="fa fa-remove"></i></a>';

    $output['aaData'][] = $row;
}
