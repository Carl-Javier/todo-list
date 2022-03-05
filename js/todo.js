const todo = document.getElementById('todoTask');
const inprogress = document.getElementById('inprogressTask');
const completed = document.getElementById('completedTask');


/**
 * @param event A jQuery event that occurs when an .droppable is being dragged
 */
function dragStartHandler(event) {
    var originalEvent = event.originalEvent;
    //We want to store the data-task-id of the object that is being dragged
    originalEvent.dataTransfer.setData("text", $(event.target).data("task-id"));
    originalEvent.dataTransfer.effectAllowed = "move";
    $("#dangerZone").show();
}

/**
 * @param event A jQuery event that occurs when a .droppable as been dropped
 */
function dropHandler(event) {
    event.preventDefault();
    event.stopPropagation();
    var originalEvent = event.originalEvent;
    var droppedItemId = originalEvent.dataTransfer.getData("text");
    var droppedItem = $("body").find(`[data-task-id='${droppedItemId}']`);
    var category = $(this).parent(".box").data("category");

    $.ajax({
        type: "POST",
        url: 'update-todo.php',
        dataType: "text",
        data: {id: droppedItemId, is_done: category},
        success: function (data) {
            console.log(data);
        },
        error: function (xhr, status, error) {
            console.log(error.message);
        }
    });

    droppedItem.data("category", category).appendTo($(this));
    //Hide the danger zone
    $("#dangerZone").hide();

    const countTodo = todo.childElementCount;
    document.getElementById("countTodo").innerHTML = `TO-DO (${countTodo})`;

    const countInprogress = inprogress.childElementCount;
    document.getElementById("countInprogress").innerHTML = `In progress (${countInprogress})`;

    const countCompleted = completed.childElementCount;
    document.getElementById("countCompleted").innerHTML = `Completed (${countCompleted})`;
}

/**
 *
 * @param taskId The id of the task we want to delete
 */
function deleteTask(taskId) {
    //Find the task with the given taskId
    var taskToDelete = $("body").find(`[data-task-id='${taskId}']`);
    $.ajax({
        type: "POST",
        url: 'remove-todo.php',
        dataType: "text",
        data: {id: taskId},
        success: function (data) {
            console.log(data);
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            console.log(error.message);
        }
    });

    //Remove it
    taskToDelete.remove();

    //Counter of task
    const countTodo = todo.childElementCount;
    document.getElementById("countTodo").innerHTML = `TO-DO (${countTodo})`;

    const countInprogress = inprogress.childElementCount;
    document.getElementById("countInprogress").innerHTML = `In progress (${countInprogress})`;

    const countCompleted = completed.childElementCount;
    document.getElementById("countCompleted").innerHTML = `Completed (${countCompleted})`;
}

/**
 *
 * @param taskElement Appending added task
 */
function createTask(taskElement) {
    //Creates a task div and appends
    var taskId = taskElement["id"];
    var taskText = taskElement["text"];
    var taskCategory = taskElement["category"];
    var taskPriority = taskElement["priority"];
    var taskPriorityVal = taskElement["priority_val"];
    var taskToAppend = $(`<div class='list-group-item droppable' draggable='true' data-task-id='${taskId}' data-category='${taskCategory}'>
                <div class="alert ${taskPriority} " role="alert">
                      <h4 class="alert-heading">${taskText}</h4>
                      <hr>
                      <a href="?id=${taskId}&action=edit&todo=${taskText}&priority=${taskPriorityVal}" class="btn btn-primary"><span class="fa fa-pencil"></span></a>
                        <a href="#" onclick="deleteTask(${taskId})" class="btn btn-danger"><span class="fa fa-trash"></span></a>
                    </div>
                </div>`);
    //Find the dropTarget to append the created task
    var dropTarget = $("body").find(`[data-category='${taskCategory}'] .dropTarget`);
    taskToAppend.appendTo(dropTarget);

    const countTodo = todo.childElementCount;
    document.getElementById("countTodo").innerHTML = `TO-DO (${countTodo})`;

    const countInprogress = inprogress.childElementCount;
    document.getElementById("countInprogress").innerHTML = `In progress (${countInprogress})`;

    const countCompleted = completed.childElementCount;
    document.getElementById("countCompleted").innerHTML = `Completed (${countCompleted})`;
}

/**
 * saving task
 */
function saveTasks() {
    //Collect all tasks
    var tasks = $(".droppable");
    //This array will store everything needed for a task in order to be saved
    var dataToStore = [];
    for (var i = 0, max = tasks.length; i < max; i++) {
        var currentTask = tasks[i];
        //For each task we need to store
        //its text, and its category
        //It will be reassigned a unique id after it is loaded back from localStorage
        var taskData = {
            text: $(currentTask).text(),
            category: $(currentTask).data("category")
        };
        dataToStore[i] = taskData;
    }
    localStorage.setItem("taskList", JSON.stringify(dataToStore));
    alert("Tasks have been saved");
}

/**
 * Loaded that ask to view
 */
function loadTasks() {
    //Fetch todo data
    $.ajax({
        type: "GET",
        url: 'fetch-todo.php',
        dataType: "json",
        success: function (data) {
            var count = 0;
            for (var i = 0, max = data.length; i < max; i++) {
                data[i].id = data[i]['id'];
                createTask(data[i]);
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

$(document).ready(function () {
    loadTasks();
    //When a new task/item is creatted it is assigned a unique data attribute which is the task index
    var taskIndex = $(".list-group-item").length;
    $("#saveTasksBtn").on("click", saveTasks);
    $("#deleteAllTasksBtn").on("click", function () {
        var answer = confirm("Are you sure you want to delete all tasks?");
        if (answer) {
            $(".droppable").remove();
            taskIndex = 0;
            alert("Tasks removed");
        }
    });

    $("body").on("dragstart", ".droppable", dragStartHandler);
    $("body").on("dblclick", ".droppable", function () {
        //Ask the user for a new task description
        var newTaskDescription = prompt("Enter a new description for this task");
        if (newTaskDescription) {
            //Update the task description
            $(this).text(newTaskDescription);
        }
    });
    $(".dropTarget").on("dragenter", function (event) {
        event.preventDefault();
        event.stopPropagation();
    }).on("dragover", false)
        .on("drop", dropHandler);
    $("#dangerZone").on("dragenter", function (event) {
        event.preventDefault();
        event.stopPropagation();
    });
});