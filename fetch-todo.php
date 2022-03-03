<?php
require 'services.class.php';

$result = $todo->fetchAlltodo();

$return_arr = array();

while ($row = $result->fetchArray()) {
    $id = $row['id'];
    $title = $row['title'];
    $date = $row['date_created'];
    $done = $row['is_done'];
    $priority_val = $row['priority'];
    $priority = '';
    switch ($row['priority']) {
        case 0:
            $priority = 'alert-success';
            break;
        case 1:
            $priority = 'alert-warning';
            break;
        case 2:
            $priority = 'alert-danger';
            break;
        default:

    }
    $return_arr[] = array("id" => $id,
        "text" => $title,
        "date_created" => $date,
        "priority" => $priority,
        "priority_val" => $priority_val,
        "category" => $done);
}


echo json_encode($return_arr);
?>