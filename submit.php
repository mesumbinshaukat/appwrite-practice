<?php

require_once 'vendor/autoload.php';

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

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $isComplete = $_POST['isComplete'];

    if (!empty($title) || !empty($description) && !empty($isComplete)) {

        function seedDatabase($databases)
        {
            $databases->createDocument(
                $_ENV['database_id'],
                $_ENV['collection_id'],
                ID::unique(),

                [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'isComplete' => filter_var($_POST['isComplete'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]
            );
        }

        function runAllTasks($databases)
        {
            seedDatabase($databases);
        }

        runAllTasks($databases);

        $response['success'] = true;
    } else {

        $response['success'] = false;
        $response['error'] = "All fields are required";
    }
}



echo json_encode($response);
