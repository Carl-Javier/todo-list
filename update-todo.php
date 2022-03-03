<?php
require 'services.class.php';
if(isset($_POST["id"])) {
    $id = $_POST["id"];
    $done = $_POST["is_done"];
    $todo->changeTodoCategory($id, (int)$done);
    echo json_encode(['err'=>false]);
}

?>