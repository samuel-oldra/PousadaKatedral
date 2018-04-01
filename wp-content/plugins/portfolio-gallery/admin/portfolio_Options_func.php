<?php
function showStyles($op_type = "0")
{
    global $wpdb;
    $query = "SELECT *  from " . $wpdb->prefix . "huge_itportfolio_params ";
    $rows = $wpdb->get_results($query);
    $param_values = array();
    foreach ($rows as $row) {
        $key = $row->name;
        $value = $row->value;
        $param_values[$key] = $value;
    }
    html_showStyles($param_values, $op_type);
}
?> 