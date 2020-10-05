<?php
require __DIR__ . '/config/paths.php';
require __DIR__ . '/vendor/autoload.php';

use App\DeliverGiggles;
use Cake\Database\Connection;
use Intervention\Image\Image;

$url = $_SERVER['REQUEST_URI'];
$httpHost = $_SERVER['HTTP_HOST'];
$serverName = $_SERVER['SERVER_NAME'];


if ($url == '/favicon.ico') {
    die();
}

$connection = new Connection([
    'driver' => Cake\Database\Driver\Sqlite::class,
    'database' => DATA . 'tickle.sqlite'
]);


$sql = file_get_contents(DATA . '/01_build_table.sql');
$statement = $connection->execute($sql);

$statement = $connection->execute('SELECT datetime() as datetime');
$rows = $statement->fetchAll('assoc');

$data = [
    'created' => gmdate("Y-m-d H:i:s"),
    'url' => $url,
    'httpHost' => $httpHost,
    'serverName' => $serverName,
    'requestHeaders' => getallheaders(),
];

$data = json_encode($data, JSON_PRETTY_PRINT);

$utcTime = gmdate("Y-m-d H:i:s");
$sql = "INSERT INTO tickles (created, modified, url) VALUES ('{$utcTime}', '{$utcTime}', '{$data}')";
$statement = $connection->execute($sql);

$giggles = new DeliverGiggles();
$response = $giggles->getGiggles();


if ($response instanceof Image) {
    /**
     * @var Image $response
     */
    $mimeType = $response->mime();
    header('Content-type: ', $mimeType);
    echo $response;
} elseif (is_array($response)) {
    header('Content-type: text/plain');
    http_response_code($response['code']);
    echo $response['message'] . ": " . $response['description'];
} else {
    echo $response;
}


