<?php

class Services
{
    /**
     * @var SQLite3 $db
     */
    private $db;

    /**
     * @var string $root
     */
    public $root;

    /**
     * Class constructor
     * Create SQL lite table
     */
    function __construct()
    {
        $this->db = new SQLite3('./db/sqlite/todo.db');
        $this->root = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $this->migrateDB();
    }

    /**
     * DB Migration / Creating table
     */
    public function migrateDB()
    {
        $query = "CREATE TABLE todo(id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT UNIQUE , date_created TEXT NOT NULL, is_done INT(11) NOT NULL, priority INT(11) NOT NULL)";
        $this->db->exec($query);
    }

    /**
     * Create To do or task
     *
     * @param string $task
     * @param int $priority
     */
    public function addTodo($task, $priority = 0)
    {
        $date = date("Y-m-d H:i:s");
        $query = "INSERT INTO todo (title, date_created, is_done, priority) VALUES ('$task', '$date', 0, $priority)";

        $this->executeService($query);
    }

    /**
     * Fetch all task
     *
     * @return SQLite3Result
     */
    public function fetchAlltodo()
    {
        $query = "SELECT * FROM todo ORDER BY priority DESC";

        return $this->db->query($query);
    }

    /**
     * Remove task
     *
     * @param int $id
     */
    public function deleteTodo($id)
    {
        $query = "DELETE FROM todo WHERE todo.id='$id'";
        $this->executeService($query);
    }

    /**
     * Update task category
     *
     * @param int $id
     * @param int $category
     */
    public function changeTodoCategory($id, $category = 0)
    {
        $now = date("Y-m-d H:i:s");

        $data = ['is_done' => $category, 'date_created' => $now];
        $where = ['id' => $id];

        $this->updateSqlQuery($data, $where, $table = 'todo');
    }

    /**
     * Update Task details
     *
     * @param int $id
     * @param string $task
     */
    public function updateTodo($id, $task, $priority)
    {

        $data = ['title' => $task, 'priority' => $priority];
        $where = ['id' => $id];

        $this->updateSqlQuery($data, $where, $table = 'todo');
    }

    /**
     * Updating SQL queries
     *
     * @param string $data
     * @param string $where
     * @param string $table
     */
    public function updateSqlQuery($data, $where, $table = 'todo')
    {
        $cols = [];
        foreach ($data as $key => $val) {
            $cols[] = "$key = '$val'";
        }

        $wheres = [];
        foreach ($where as $key => $val) {
            $wheres[] = "$key = '$val'";
        }

        $query = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . implode(', ', $wheres);

        $this->executeService($query);
    }

    /**
     * Execute queries
     *
     * @param string $query
     */
    private function executeService($query)
    {
        $this->db->exec($query);
        $this->redirect();
    }

    /**
     * Redirect to home.
     *
     * @param string $url optional
     */
    public function redirect()
    {
        header('Location: http://localhost:8888/todoListTest/');
    }

    public function showTotal()
    {
        $total = $this->db->query('SELECT COUNT(*) as total FROM todo');
        $totalCompleted = $this->db->query('SELECT COUNT(*) as total FROM todo WHERE is_done = 1');

        echo '<div class="col-sm-12">';
        echo '<div class="col-sm-6">';
        echo '<ul class="list-group">';
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"> TOTAL TASK';
        echo '<span class="badge badge-primary badge-pill">'. $total->fetchArray()[0] .'</span>';
        echo '</li>';
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">TASK COMPLETED';
        echo '<span class="badge badge-primary badge-pill">'. $totalCompleted->fetchArray()[0] .'</span>';
        echo '</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Show todo
     *
     * @param string $done
     */
    public function showTodo($done=0)
    {
        $columns = array('title','priority');
        $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
        $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

        $todos = $this->selectTodo($done, $column, $sort_order);

        $up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order);
        $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';

        echo '<table class="table table-striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">#</th>';
        echo '<th scope="col"><a href="?column=title&order='. $asc_or_desc . '">Task <i class="fa fa-sort '. ($column == 'title' ? '-' . $up_or_down : '') .'"></i></a></th>';
        echo '<th scope="col"><a href="?column=priority&order='. $asc_or_desc . '">Priority <i class="fa fa-sort '. ($column == 'priority' ? '-' . $up_or_down : '') .'"></i></a></th>';
        echo '<th scope="col">Date</th>';
        echo '<th scope="col">Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        $num = 1;
        while( $row = $todos->fetchArray() ):
            $priority = '';
            switch ($row['priority']) {
                case 0:
                    $priority = '<span class="badge badge-pill" style="background-color: #28a745">Low</span>';
                    break;
                case 1:
                    $priority = '<span class="badge badge-pill bg-warning" style="background-color: #ffc107">Medium</span>';
                    break;
                case 2:
                    $priority = '<span class="badge badge-pill bg-danger" style="background-color: #dc3545">High</span>';
                    break;
                default:

            }

            echo '<tr>';
            echo '<th scope="row">'.$num.'</th>';
            echo '<td>'.$row["title"].'</td>';
            echo '<td>'.$priority.'</td>';
            echo '<td>'.$row["date_created"].'</td>';
            echo '<td>';
            $name = ($done==0) ? 'Done': 'Return';
            echo '<a href="?id='.$row["id"].'&action='.$name.'"><span class="fa '. ($name == 'Done'? 'fa-check-circle' : 'fa-undo').'"> '.$name.'</span></a>';
            echo ' &nbsp;<a href="?id='.$row["id"].'&action=edit&todo='.$row["title"].'&priority='.$row['priority'].'" class="text-success"><span class="fa fa-pencil"></span></a>';
            echo ' <a class="text-danger mx-2 d-inline-block" href="?id='.$row["id"].'&action=delete"><span class="fa fa-trash"></span></a>';
            echo '</td>';
            echo '</tr>';
            $num++;
        endwhile;
        echo '</tbody>';
        echo '</table>';
    }
    /**
     * Select todo
     *
     * @param string $done
     */
    private function selectTodo($done=0, $column = 'title', $sortOrder = 'ASC')
    {
        $query = "SELECT * FROM todo WHERE is_done= $done ORDER BY $column $sortOrder";

        $run_select = $this->runQueryreturn($query);

        if(!$run_select) {
            echo '<h1>Please follow intro.php file instructions ...</h1>';
            exit;
        }

        return $run_select;
    }

    /**
     * Run sql query and return result
     *
     * @param string $query
     */
    private function runQueryreturn($query)
    {
        return $this->db->query($query);
    }

    /**
     * Return todo to undone
     *
     * @param int $id
     */
    public function returnTodo($id)
    {
        $now = date("Y-m-d H:i:s");;

        $data = [ 'is_done' => 0, 'date_created' => $now ];
        $where = [ 'id' => $id ];

        $this->updateDoneQuery($data, $where, $table='todo');
    }

    /**
     * Sql query and run for todo update
     *
     * @param array $data
     * @param array $where
     * @param string $table
     */
    public function updateDoneQuery($data, $where, $table='todo')
    {
        $cols = [];
        foreach ($data as $key => $val) {
            $cols[] = "$key = '$val'";
        }

        $wheres = [];
        foreach ($where as $key => $val) {
            $wheres[] = "$key = '$val'";
        }

        $query = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . implode(', ', $wheres);

        $this->executeService($query);
    }

    /**
     * Done todo
     *
     * @param int $id
     */
    public function doneTodo($id)
    {
        $now = date("Y-m-d H:i:s");;

        $data = [ 'is_done' => 1, 'date_created' => $now ];
        $where = [ 'id' => $id ];

        $this->updateDoneQuery($data, $where, $table='todo');
    }
}

$todo = new Services();