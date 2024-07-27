<?php
require_once realpath(__DIR__ . '/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/..'));
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
