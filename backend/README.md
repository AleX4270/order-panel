# Order Panel — Backend

Laravel API for the Order Panel application. See the [root README](../README.md) for the full project overview and Docker setup.

Uses Laravel Sanctum for authentication and Laravel Reverb for WebSocket broadcasting. API routes are defined in [routes/api.php](routes/api.php).

## Development

In the Docker setup the backend runs automatically at `http://localhost:8000`. 

You can run artisan commands inside the container:

```bash
docker compose exec backend php artisan migrate
```

## Tests & static analysis

Static analysis runs with [PHPStan](https://phpstan.org):

```bash
vendor/bin/phpstan analyse
```
