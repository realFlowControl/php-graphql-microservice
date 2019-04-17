<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server;

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Adapter\ReactPromiseAdapter;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

require __DIR__ . '/vendor/autoload.php';

$loop = Factory::create();
$browser = new Browser($loop);

$productType = new ObjectType([
    'name' => 'Product',
    'fields' => [
        'id' => [
            'type' => Type::id(),
        ],
        'name' => [
            'type' => Type::string(),
        ],
        'description' => [
            'type' => Type::string(),
        ]
    ]
]);

$reviewType = new ObjectType([
    'name' => 'Review',
    'fields' => [
        'id' => [
            'type' => Type::id(),
        ],
        'title' => [
            'type' => Type::string(),
        ],
        'grade' => [
            'type' => Type::int(),
        ],
        'comment' => [
            'type' => Type::string(),
        ],
        'product' => [
            'type' => $productType,
            'resolve' => function ($review) use ($browser) {
                return $browser->get('http://product-service:3000/')->then(function($response) {
                    $rawBody = (string)$response->getBody();
                    return json_decode($rawBody);
                });
            }
        ]
    ]
]);

$query = new ObjectType([
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
        ],
        'product' => [
            'type' => $productType,
            'args' => [
                'id' => Type::nonNull(Type::id()),
            ],
            'resolve' => function ($root, $args) use ($browser) {
                return $browser->get('http://product-service:3000/')->then(function($response) {
                    $rawBody = (string)$response->getBody();
                    return json_decode($rawBody);
                });
            }
        ],
        'review' => [
            'type' => $reviewType,
            'args' => [
                'id' => Type::nonNull(Type::id()),
            ],
            'resolve' => function ($root, $args) use ($browser) {
                return $browser->get('http://review-service:3000/')->then(function($response) {
                    $rawBody = (string)$response->getBody();
                    return json_decode($rawBody);
                });
            }
        ],

    ],
]);

$schema = new Schema([
    'query' => $query,
    'types' => [
        $productType
    ]
]);

$react = new ReactPromiseAdapter();

$server = new Server(function (ServerRequestInterface $request) use ($schema, $react) {
    $rawInput = (string)$request->getBody();
    $input = json_decode($rawInput, true);
    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    $rootValue = ['prefix' => 'You said: '];
    var_dump($query);
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
