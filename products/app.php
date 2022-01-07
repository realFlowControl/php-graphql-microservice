<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\HttpServer;

require __DIR__ . '/vendor/autoload.php';

$server = new HttpServer(function (ServerRequestInterface $request) {
    return new Response(
        200,
        [
            'Content-Type' => 'application/json'
        ],
        json_encode([
            'id' => 1,
            'name' => 'Avengers',
            'description' => 'a movie'
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
    );
});
$socket = new \React\Socket\SocketServer('0.0.0.0:' . getenv('PORT'));
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
