<?php

require_once 'vendor/autoload.php';

use Appwrite\Client;
use Appwrite\Services\Databases;
use Appwrite\ID;

$client = new Client();

$client
    ->setEndpoint('https://cloud.appwrite.io/v1')
    ->setProject('66a377aa0003a2b11af1')
    ->setKey('af87242bd7c3118255a3b95773bb35f0416249b33460df26c5cecbbad253773db461587826d13884a37393ed595cc9a7ded71fc924a87e1e8db8b5928c7f01499af3870ac2b4ea16896cb7510da8e99cab99bb92299d92c3e8256a03f926796070d617d92e5ef073d0b32a156d62d9f28bbcd1e3b2e6b0c848c1027c0989ebf7');

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
                '66a378bed0f700ee4ba9',
                '66a378c056a0a397b910',
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