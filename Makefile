# Build

build-backend:
	docker build --target production -f ./docker/backend/Dockerfile -t ghcr.io/alex4270/order-panel-backend:latest --platform linux/amd64 .

build-backend-dev:
	docker build --target production -f ./docker/backend/Dockerfile -t ghcr.io/alex4270/order-panel-backend:dev --platform linux/amd64 .

build-frontend:
	docker build --build-arg CONFIG="production" -f ./docker/frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:latest --platform linux/amd64 .

build-frontend-dev:
	docker build --build-arg CONFIG="staging" -f ./docker/frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:dev --platform linux/amd64 .

# Push

push-backend:
	docker push ghcr.io/alex4270/order-panel-backend:latest

push-backend-dev:
	docker push ghcr.io/alex4270/order-panel-backend:dev

push-frontend:
	docker push ghcr.io/alex4270/order-panel-frontend:latest

push-frontend-dev:
	docker push ghcr.io/alex4270/order-panel-frontend:dev
