<?php
declare(strict_types=1);

use GraphQL\Type\Schema;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

// the resolver function may return a string or a ReactPHP Promise
$productResolver = function ($review) use ($browser) {
    return $browser->get('http://product-service:3000/')->then(function($response) {
        $rawBody = (string)$response->getBody();
        return json_decode($rawBody);
    });
};

$reviewResolver = function ($product) use ($browser) {
    return $browser->get('http://review-service:3000/')->then(function($response) {
        $rawBody = (string)$response->getBody();
        return json_decode($rawBody);
    });
};

// return type definitions

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
            'resolve' => $productResolver
        ]
    ]
]);

// query defintion

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
            'resolve' => $productResolver
        ],
        'review' => [
            'type' => $reviewType,
            'args' => [
                'id' => Type::nonNull(Type::id()),
            ],
            'resolve' => $reviewResolver
        ],

    ],
]);

// schema packs query and types together

$schema = new Schema([
    'query' => $query,
    'types' => [
        $productType
    ]
]);
