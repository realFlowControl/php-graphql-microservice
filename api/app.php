<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

require __DIR__ . '/vendor/autoload.php';

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'echo' => [
            'type' => Type::string(),
            'args' => [
                'message' => Type::nonNull(Type::string()),
            ],
            'resolve' => function($root, $args) {
                return $root['prefix'] . $args['message'];
            }
        ]/*,
        'echo' => [
            'type' => Type::string(),
            'args' => [
                'message' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($root, $args) {
                return $root['prefix'] . $args['message'];
            }
        ],
        */
    ],
]);

$schema = new Schema([
    'query' => $queryType
]);

$loop = Factory::create();
$react = new ReactPromiseAdapter();

$server = new Server(function (ServerRequestInterface $request) use ($schema, $react) {
    $rawInput = (string)$request->getBody();
    $input = json_decode($rawInput, true);
    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    $rootValue = ['prefix' => 'You said: '];
    $promise = GraphQL::promiseToExecute($react, $schema, $query, $rootValue, null, $variableValues);
    return $promise->then(function(ExecutionResult $result) {
        $output = $result->toArray();
        return new Response(
            200,
            array(
                'Content-Type' => 'application/json'
            ),
            json_encode($output)
        );
    });
});
$socket = new \React\Socket\Server('0.0.0.0:3000', $loop);
$server->listen($socket);
echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
