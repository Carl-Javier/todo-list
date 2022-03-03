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
        $date = time();
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
}

$todo = new Services();