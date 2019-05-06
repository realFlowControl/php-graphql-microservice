# GraphQL API on top of Microservices

... written in PHP

## Quickstart example

```bash
$ make
$ docker-compose up -d
$ curl -X POST \
       -H "Content-Type: application/json" \
       --data '{ "query": "{ product (id:1) { id name } }" }' \
       localhost:8000
```

## License

MIT, see [LICENSE file](LICENSE).
