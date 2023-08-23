function putTodo(todo) {
    fetch(window.location.href + 'api/todo/' + todo.id, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(todo)
    })
    .then(response => {
        if (response.ok) {
            return response.json(); // Parse the response JSON
        } else {
            throw new Error(`Failed to update todo: ${response.statusText}`);
        }
    })
    .then(data => {
        if (data.updatedTodo) {
            const updatedTodo = data.updatedTodo;
            const index = todos.findIndex(existingTodo => existingTodo.id === updatedTodo.id);
            if (index !== -1) {
                todos[index] = updatedTodo; // Update local todos array
                localStorage.setItem('todos', JSON.stringify(todos)); // Update localStorage
                console.log("Todo updated successfully");
            } else {
                console.error("Todo not found in local array");
            }
        } else {
            console.error("Failed to update todo:", data.message);
        }
    })
    .catch(error => console.error("Error while updating todo", error));
}

function postTodo(todo) {
    fetch(window.location.href + 'api/todo/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(todo)
    })
    .then(response => {
        if (response.ok) {
            todos.push(todo);
            localStorage.setItem('todos', JSON.stringify(todos));
            console.log("New Todo created with ID:", todo.id, "and this is the new Todo array of info", todo);
            console.log("Todo created successfully");
        } else {
            console.error("Failed to create todo");
        }
    })
    .catch(error => console.error("Error while creating todo", error));
}

function deleteTodo(todo) {
    fetch(window.location.href + 'api/todo/' + todo.id, {
        method: 'DELETE'
    })
    .then(response => {
        if (response.ok) {
            todos = todos.filter(existingTodo => existingTodo.id !== todo.id); // Update local todos array
            localStorage.setItem('todos', JSON.stringify(todos)); // Update localStorage
            console.log("Todo deleted successfully");
        } else {
            console.error("Failed to delete todo");
        }
    })
    .catch(error => console.error("Error while deleting todo", error));
}

// using the FETCH API to do a GET request
let todos = [];

function getTodos() {
    if (localStorage.getItem('todos')) {
        todos = JSON.parse(localStorage.getItem('todos')); // Load todos from localStorage
        drawTodos(todos);
    } else {
        fetch(window.location.href + 'api/todo')
        .then(response => response.json())
        .then(json => {
            todos = json; // Storing fetched todos globally
            localStorage.setItem('todos', JSON.stringify(todos)); // Update localStorage
            drawTodos(json);
        })
        .catch(error => showToastMessage('Failed to retrieve todos...'));
    }
}

getTodos();