<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__));
$dotenv->load();

use Appwrite\Client;
use Appwrite\Services\Databases;
use Appwrite\ID;

$client = new Client();

$client
    ->setEndpoint('https://cloud.appwrite.io/v1')
    ->setProject($_ENV["project_id"])
    ->setKey($_ENV["api_key"]);

$databases = new Databases($client);



function prepareDatabase($databases)
{
    $todoDatabase = $databases->create(
        databaseId: ID::unique(),
        name: 'TodosDB'
    );

    $todoCollection = $databases->createCollection(
        databaseId: $todoDatabase['$id'],
        collectionId: ID::unique(),
        name: 'Todos'
    );

    $databases->createStringAttribute(
        databaseId: $todoDatabase['$id'],
        collectionId: $todoCollection['$id'],
        key: 'title',
        size: 255,
        required: true
    );

    $databases->createStringAttribute(
        databaseId: $todoDatabase['$id'],
        collectionId: $todoCollection['$id'],
        key: 'description',
        size: 255,
        required: false,
    );

    $databases->createBooleanAttribute(
        databaseId: $todoDatabase['$id'],
        collectionId: $todoCollection['$id'],
        key: 'isComplete',
        required: true
    );

    return [$todoDatabase, $todoCollection];
}

function seedDatabase($databases, $todoDatabase, $todoCollection)
{
    $testTodo1 = [
        'title' => 'Workout',
        'description' => 'At least 2KGs',
        'isComplete' => true
    ];

    $testTodo2 = [
        'title' => 'Walk',
        'isComplete' => true
    ];

    $testTodo3 = [
        'title' => 'Run',
        'description' => 'Run in park',
        'isComplete' => false
    ];

    $databases->createDocument(
        $todoDatabase['$id'],
        $todoCollection['$id'],
        ID::unique(),
        $testTodo1
    );

    $databases->createDocument(
        $todoDatabase['$id'],
        $todoCollection['$id'],
        ID::unique(),
        $testTodo2
    );

    $databases->createDocument(
        $todoDatabase['$id'],
        $todoCollection['$id'],
        ID::unique(),
        $testTodo3
    );
}

function getTodos($databases/*, $todoDatabase, $todoCollection*/)
{
    $todos = $databases->listDocuments(
        $_ENV["database_id"],
        $_ENV["collection_id"]
    );
    echo "<table id='todos' style='border: 1px solid black; text-align: center'>";
    echo "<tr><th>Title</th><th>Description</th><th>Is Todo Complete</th></tr>";
    foreach ($todos['documents'] as $todo) {
        echo "<tr><td>" . $todo['title'] . "</td><td>" . $todo['description'] . "</td><td>" . ($todo['isComplete'] ? 'Yes' : 'No') . "</td></tr>";
    }
    echo "</table>";
}

function runAllTasks($databases)
{
    // [$todoDatabase, $todoCollection] = prepareDatabase($databases);
    // seedDatabase($databases, $todoDatabase, $todoCollection);
    getTodos($databases);
}

runAllTasks($databases);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <br><br>
    <h2>Form</h2>
    <hr>
    <br><br>
    <form id="form">
        <input type="text" name="title">
        <input type="text" name="description">
        <select name="isComplete">
            <option value="true">Done</option>
            <option value="false">Not Done</option>
        </select>
        <input type="button" value="Submit" id="submit">
    </form>


    <script>
        $(document).ready(function() {
            $("#submit").on("click", function() {
                var form = $("#form");
                $.ajax({
                    type: "POST",
                    url: "submit.php",
                    data: form.serialize(),
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            console.log("Result: ", result);
                            if (result.success) {
                                alert("Success");
                                // Optionally, refresh the list of todos
                                fetchTodos();
                            } else {
                                alert("Error: " + result.message);
                            }
                        } catch (e) {
                            alert("Error: " + e.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("AJAX Error: " + status + " - " + error);
                    }
                });
            });

            function fetchTodos() {
                $.ajax({
                    type: "GET",
                    url: "fetch_todos.php", // Create a separate PHP file to fetch todos
                    success: function(response) {
                        $("#todos").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert("AJAX Error: " + status + " - " + error);
                    }
                });
            }
        });
    </script>


</body>

</html>