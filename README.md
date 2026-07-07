# Order Panel

Web-based application for order management in small businesses. Customers submit order requests through a public form; staff review them, convert them into orders and track their progress with real-time notifications.

## Features

- Public order request form — customers can submit requests without an account
- Order request review — accept requests and convert them into orders, or reject them
- Order management — full CRUD plus quick actions (e.g. mark as completed)
- Real-time notifications via WebSockets, with configurable notification channels and events
- User management with roles
- Company settings and a dashboard overview

## Tech Stack

**Frontend:** Angular 21, RxJS, NgXs, Tailwind CSS, Jest

**Backend:** Laravel 13 (PHP 8.5), Laravel Reverb, Laravel Sanctum, PEST, PHPStan

**Infrastructure:** Docker, Traefik, PostgreSQL, Apache HTTP Server

## Repository Structure

```
backend/    Laravel API application
frontend/   Angular SPA
docker/     Dockerfiles for backend, frontend, database and Traefik
deploy/     Server-side deploy scripts for staging and production
docs/       Additional documentation (local Docker environment guide)
Makefile    Manual image build & push targets
```

## Getting Started

The whole stack runs in Docker. Create a `.env` file in the project root (PostgreSQL credentials — see the [local environment guide](docs/docker/local-environment.md#-environment-variables)), then:

```bash
docker compose up --build -d
```

Services are then available at:

- Frontend: http://localhost:4200
- Backend API: http://localhost:8000
- PostgreSQL: `localhost:5432`

See the [Docker local environment guide](docs/docker/local-environment.md) for everyday commands, logs and troubleshooting. The `backend/` and `frontend/` READMEs cover running tests and other per-project tooling.

## Deployment

Docker images are built and pushed to GitHub Container Registry (`ghcr.io`) by GitHub Actions:

- Push to `staging` → [Build And Deploy To Dev](.github/workflows/deploy_dev.yml)
- Push to `main` → [Build And Deploy To Production](.github/workflows/deploy_prod.yml)

Both workflows can also be triggered manually via `workflow_dispatch`. The [Makefile](Makefile) provides equivalent local targets (`make deploy-dev`, `make deploy-prod`) for building and pushing images by hand. Server-side deploy scripts live in [deploy/](deploy/).

## Authors

- [@alex4270](https://github.com/AleX4270)

## License

See [LICENSE](LICENSE). All rights reserved.
