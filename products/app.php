<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

require __DIR__ . '/vendor/autoload.php';

$loop = Factory::create();
$server = new Server(function (ServerRequestInterface $request) {
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
$socket = new \React\Socket\Server('0.0.0.0:3000', $loop);
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
