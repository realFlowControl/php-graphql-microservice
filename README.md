# GraphQL API on top of Microservices

This is just a port from Node.js/Express to PHP/ReactPHP for Chris Norings article on building a [Serverless GraphQL API on top of a Microservice architecture](https://dev.to/azure/learn-how-you-can-build-a-serverless-graphql-api-on-top-of-a-microservice-architecture-233g).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. 

### Prerequisites

You need to have `docker` and `docker-compose` up and running on your local machine.

### Installing

```bash
git clone https://github.com/flow-control/php-graphql-microservice.git
cd php-graphql-microservice
make
docker-compose up -d
curl -X POST \
       -H "Content-Type: application/json" \
       --data '{ "query": "{ product (id:1) { id name } }" }' \
       localhost:8000
```

## Build with

- [ReactPHP](https://reactphp.org/)
- [graphql-php](https://github.com/webonyx/graphql-php)
- [clue/buzz-react](https://github.com/clue/reactphp-buzz)

## License

MIT, see [LICENSE file](LICENSE).
