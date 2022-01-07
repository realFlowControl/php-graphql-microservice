<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Http\HttpServer;

use React\Http\Browser;

use GraphQL\GraphQL;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter;

require __DIR__ . '/vendor/autoload.php';

$browser = new Browser($loop);

require __DIR__ . '/schema.php';

// GraphQL-PHP needs to know which async implementation we are using
// in this case it's ReactPHP
$react = new ReactPromiseAdapter();

$server = new HttpServer(function (ServerRequestInterface $request) use ($schema, $react) {
    // GraphQL Input is "just" a JSON-String
    $input = json_decode((string)$request->getBody(), true);
    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    // just pass query and variables to the GraphQL lib
    $promise = GraphQL::promiseToExecute($react, $schema, $query, [], null, $variableValues);
    // promiseToExecute will return a ReactPHP Promise, so we can register our then callback
    return $promise->then(function(ExecutionResult $result) {
        $output = $result->toArray();
        return new Response(
            200,
            [ 
                'Content-Type' => 'application/json'
            ],
            json_encode($output, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );
    });
});
$socket = new \React\Socket\SocketServer('0.0.0.0:' . getenv('PORT'));
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
