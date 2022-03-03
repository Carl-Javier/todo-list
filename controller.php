<?php

if( isset($_POST['addNew']) )
    $todo->addTodo($_POST['task'], $_POST['priority']);

if( isset($_POST['updateLast']) )
{
    $task = $_POST['task'];
    $id = $_POST['task_id'];
    $priority = $_POST['priority'];

    $todo->updateTodo($id, $task, $priority);
}