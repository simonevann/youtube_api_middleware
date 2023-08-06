<?php
//cors policy disable
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Credentials: true");
// get the id parameter and print a json from DataController
require_once('controllers/DataController.php');

//if there is no id, return a 404 error
if (!isset($id)) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
$dataController = new DataController();
$data = $dataController->getData($id);
//header('Content-Type: application/json');
//print a numbered list <of> of videos with the data->items array
if (isset($data['items'])) {
    echo '<ul>';
    foreach ($data['items'] as $item) {
        echo '<li><a href="https://www.youtube.com/watch?v=' . $item['snippet']['resourceId']['videoId'] . '">' . $item['snippet']['title'] . '</a></li>';
    }
    echo '</ul>';
} else {
    echo json_encode($data);
}
?>