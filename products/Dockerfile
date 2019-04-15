FROM php:7.3-alpine
WORKDIR /app
ENV PORT=3000
COPY . .
EXPOSE $PORT
ENTRYPOINT ["php", "app.php"]
