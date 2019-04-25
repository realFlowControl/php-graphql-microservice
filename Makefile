install: api/vendor products/vendor reviews/vendor

%/vendor: %/composer.lock
	docker run --rm -i -u 1000:1000 --tty --volume $$PWD/$*:/app composer install

%/composer.lock: %/composer.json
	docker run --rm -i -u 1000:1000 --tty --volume $$PWD/$*:/app composer update
