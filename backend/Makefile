setup:
	@make build
	@make up
	@make composer-update
	@make init
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up
composer-update:
	docker exec laravel_backend bash -c "composer update"
init:
	docker exec laravel_backend bash -c "php artisan migrate"
	docker exec laravel_backend bash -c "php artisan db:seed"