<?php
include('services.class.php');
include('controller.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>PHP Todo-list TEST</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="col-md-12 rounded bg-white">
    <div class="container">
        <div class="row">
            <div class="jumbotron intro">
                <form action="" class="input-group-append" method="post" autocomplete="off">
                    <div class="form-group row">
                        <label for="exampleInputEmail1">Task Title</label>

                        <div class="col-sm-12">
                            <input required type="text"
                                   value="<?= isset($_GET['action']) && $_GET['action'] == 'edit' ? $_GET['todo'] : ''; ?>"
                                   name="task" class="form-control" name id="task" placeholder="write a task ...">
                        </div>
                    </div>
                    <fieldset class="form-group">
                        <div class="row">
                            <legend class="col-form-label col-sm-2 pt-0">Priority</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input <?= isset($_GET['action']) && $_GET['action'] == 'edit' && $_GET['priority'] == '2' ? 'checked' : ''; ?>
                                            type="radio" class="form-check-input" name="priority" value="2" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">High</label>
                                </div>
                                <div class="form-check">
                                    <input <?= isset($_GET['action']) && $_GET['action'] == 'edit' && $_GET['priority'] == '1' ? 'checked' : ''; ?>
                                            type="radio" class="form-check-input" name="priority" value="1" id="exampleCheck2">
                                    <label class="form-check-label" for="exampleCheck2">Medium</label>
                                </div>
                                <div class="form-check disabled">
                                    <input <?= isset($_GET['action']) && $_GET['action'] == 'edit' && $_GET['priority'] == '0' ? 'checked' : ''; ?>
                                            type="radio" name="priority" class="form-check-input" value="0" id="exampleCheck3">
                                    <label class="form-check-label" for="exampleCheck3">Low</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="form-group row">
                        <div class="col-sm-10">
                            <input name="<?= isset($_GET['action']) && $_GET['action'] == 'edit' ? 'updateLast' : 'addNew'; ?>"
                                   type="submit"
                                   value="<?php echo isset($_GET['action']) && $_GET['action'] == 'edit' ? 'Edit' : 'Add'; ?>"
                                   class="btn btn-primary"/>
                            <input type="hidden" type="submit" onclick="this.reset()"
                                   value="<?= isset($_GET['action']) && $_GET['action'] == 'edit' ? $_GET['id'] : ''; ?>"
                                   name="task_id"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php $todo->showTotal(); ?>

        <br/>

        <h3 class="mt-4">Todos: </h3>
        <?php $todo->showTodo(); ?>

        <br />
        <h3>Completed: </h3>
        <?php $todo->showTodo(1); ?>
<!--        <div class="row">-->
<!--            <div class="col-md-4 box" data-category="0">-->
<!--                <h2 class="text-info text-center" id="countTodo">TO-DO</h2>-->
<!--                <div class="dropTarget list-group" id="todoTask">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-4 box" data-category="1">-->
<!--                <h1 class="text-info text-center" id="countInprogress">In progress</h1>-->
<!--                <div class="dropTarget list-group" id="inprogressTask">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-4 box" data-category="2">-->
<!--                <h1 class="text-info text-center" id="countCompleted">Completed<span-->
<!--                            class="createTask glyphicon"></span></h1>-->
<!--                <div class="dropTarget list-group" id="completedTask">-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>
<script src="js/todo.js"></script>
</body>

</html>