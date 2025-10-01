<?php

defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [];

// Show checkbox column only if main-contracts
if (isset($type) && $type == 'main-contracts') {
  $table_data[] = '<div class="checkbox mass_select_all_wrap">
        <input type="checkbox" id="mass_select_all">
        <label for="mass_select_all"></label>
    </div>';
}

$table_data[] = _l('the_number_sign');
$table_data[] = _l('contract_list_subject');

$table_data[] = [
  'name'     => _l('contract_list_client'),
  'th_attrs' => ['class' => (isset($client) ? 'not_visible' : '')],
];

$table_data[] = _l('contract_types_list_name');
$table_data[] = _l('contract_value');
$table_data[] = _l('contract_list_start_date');
$table_data[] = _l('contract_list_end_date');

$table_data[] = (!isset($project) ? _l('project') : [
  'name'     => _l('project'),
  'th_attrs' => ['class' => 'not_visible'],
]);

$table_data[] = _l('signature');

// Add custom fields
$custom_fields = get_custom_fields('contracts', ['show_on_table' => 1]);
foreach ($custom_fields as $field) {
  $table_data[] = [
    'name'     => $field['name'],
    'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
  ];
}

$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

render_datatable(
  $table_data,
  (isset($class) ? $class : 'contracts'),
  ['number-index-2'],
  [
    'data-last-order-identifier' => 'contracts',
    'data-default-order'         => get_table_last_order('contracts'),
    'id' => $table_id ?? 'contracts',
  ]
);
