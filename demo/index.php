<?php
/** @noinspection ALL */
require_once __DIR__."/../vendor/autoload.php";

use Fruitware\WhmcsWrapper\Facade;

$apiUrl = 'https://example.com';
$apiIdentifier = '';
$apiSecret = '';

try {
    $client = Facade::run()->connect(
        $apiUrl,
        $apiIdentifier,
        $apiSecret
    );
    var_dump($client->call('GetClients'));
} catch (Exception $exception) {
    var_dump('Error: ', $exception->getMessage(), $exception->getTraceAsString());
}
