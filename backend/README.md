# Order Panel — Backend

Laravel API for the Order Panel application. See the [root README](../README.md) for the full project overview and Docker setup.

Uses Laravel Sanctum for authentication and Laravel Reverb for WebSocket broadcasting. API routes are defined in [routes/api.php](routes/api.php).

## Development

In the Docker setup the backend runs automatically at `http://localhost:8000`. 

You can run artisan commands inside the container:

```bash
docker compose exec backend php artisan migrate
```

## Tests

The backend code base has approximately ~90% test coverage. These tests are a **mandatory job** included in the production and staging deployment workflows.

In order to execute all tests locally, run:

```bash
php artisan test
```

To manually run tests and analyze the current code coverage run:

```bash
php artisan test --coverage
```
> **Important**
> In order to analyze the coverage you need to have code coverage driver installed locally (eg. pcov, xdebug). This project includes the pcov driver in the docker local environment.

## Static analysis

Static analysis runs with [PHPStan](https://phpstan.org):

```bash
vendor/bin/phpstan analyse
```
