<?php
require 'services.class.php';
if(isset($_POST["id"])) {
    $id = $_POST["id"];
    $todo->deleteTodo($id);
    echo json_encode(['err'=>false]);
}

?>