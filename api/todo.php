<?php
try {
    require_once("todo.controller.php");
    
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = explode( '/', $uri);
    $requestType = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents('php://input');
    $pathCount = count($path);

    $controller = new TodoController();
    
    switch($requestType) {
        case 'GET':
            if ($path[$pathCount - 2] == 'todo' && isset($path[$pathCount - 1]) && strlen($path[$pathCount - 1])) {
                $id = $path[$pathCount - 1];
                $todo = $controller->load($id);
                if ($todo) {
                    http_response_code(200);
                    die(json_encode($todo));
                }
                http_response_code(404);
                die();
            } else {
                http_response_code(200);
                die(json_encode($controller->loadAll()));
            }
            break;
        case 'POST':
            $todoData = json_decode($body);
            $id = $path[$pathCount - 1];
            if($todoData && isset($todoData->title)&& isset($todoData->description)){
                $newTodo = new Todo('', $todoData->title, $todoData->description, false);
                if($controller->create($newTodo)) {
                    http_response_code(201); //Created successfully
                    die();
                }
            }
            http_response_code(400); //Bad Request
            die();
            break;
        case 'PUT':
            if($path[$pathCount - 2] == 'todo' && isset($path[$pathCount - 1]) && strlen($path[$pathCount -1])) {
               $id = $path[$pathCount - 1];
               $todoData = json_decode($body);
               if($todoData && isset($todoData->title)&& isset($todoData->description)){
                    $updatedTodo = new Todo($id, $todoData->title, $todoData->description, $todoData->done);
                    if($controller->update($id, $updatedTodo)) {
                        http_response_code(200); //Updated successfully
                        die();
                    }                
               }
            }
            http_response_code(400); //Bad Request
            die();
            break;
        case 'DELETE':
            if ($path[$pathCount - 2] == 'todo' && isset($path[$pathCount - 1]) && strlen($path[$pathCount - 1])) {
                $id = $path[$pathCount - 1];
                if($controller->delete($id)) {
                    http_response_code(200); //Deleted successfully
                    die();
                }
            }
            http_response_code(400); //Bad Request
            die();
            break;
        default:
            http_response_code(501);
            die();
            break;
    }
} catch(Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    die();
}
