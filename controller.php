<?php

if( isset($_POST['addNew']) )
    $todo->addTodo($_POST['task'], $_POST['priority']);

if( isset($_GET['action']) )
{
    $id = $_GET['id'];
    switch( $_GET['action'] )
    {
        case 'delete':
            $todo->deleteTodo( $id );
            break;
        case 'Return':
            $todo->returnTodo( $id );
            break;
        case 'Done':
            $todo->doneTodo( $id );
            break;
    }
}

if( isset($_POST['updateLast']) )
{
    $task = $_POST['task'];
    $id = $_POST['task_id'];
    $priority = $_POST['priority'];

    $todo->updateTodo($id, $task, $priority);
}