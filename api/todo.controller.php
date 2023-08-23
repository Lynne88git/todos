<?php
require_once("todo.class.php");

class TodoController {
    private const PATH = __DIR__."/todo.json";
    private array $todos = [];

    public function __construct() {
        $content = file_get_contents(self::PATH);
        if ($content === false) {
            throw new Exception(self::PATH . " does not exist");
        }  
        $dataArray = json_decode($content);
        if (!json_last_error()) {
            foreach($dataArray as $data) {
                if (isset($data->id) && isset($data->title))
                $this->todos[] = new Todo($data->id, $data->title, $data->description, $data->done);
            }
        }
    }

    public function loadAll() : array {
        return $this->todos;
    }

    public function load(string $id) : Todo | bool {
        foreach($this->todos as $todo) {
            if ($todo->id == $id) {
                return $todo;
            }
        }
        return false;
    }

    public function create(Todo $todo) : bool {
        $this->todos[] = $todo;
        return $this->saveData();
    }

    public function update(string $id, Todo $todo) : bool {
        foreach ($this->todos as &$existingTodo) {
            if ($existingTodo->id == $id) {
                $existingTodo = $todo;
                if ($this->saveData()) {
                    http_response_code(200); // Updated successfully
    
                    // Send the updated todo as JSON response
                    echo json_encode(['updatedTodo' => $existingTodo]);
    
                    return true;
                }
            }
        }
        return false;
    }

    public function delete(string $id) : bool {
        $this->todos = array_values(array_filter($this->todos, function($todo) use ($id) {
            return $todo-> id !== $id;
        }));
        return $this->saveData();
    }

    private function saveData() : bool {
        return file_put_contents(self::PATH, json_encode($this->todos, JSON_PRETTY_PRINT));
    }
    
}