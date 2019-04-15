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
        array(
            'Content-Type' => 'application/json'
        ),
        json_encode([
            'id' => 2,
            'title' => 'Oh snap what an ending',
            'grade' => 5,
            'comment' => 'I need therapy after this...',
            'product' => 1
        ])
    );
});
$socket = new \React\Socket\Server('0.0.0.0:3000', $loop);
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
