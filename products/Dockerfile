FROM php:8.1-alpine
WORKDIR /app
ENV PORT=3000
COPY . .
EXPOSE $PORT
ENTRYPOINT ["php", "app.php"]
