# Build

build-backend:
	docker build -f ./backend/Dockerfile -t ghcr.io/alex4270/order-panel-backend:latest --platform linux/amd64 .

build-backend-dev:
	docker build -f ./backend/Dockerfile -t ghcr.io/alex4270/order-panel-backend:dev --platform linux/amd64 .

build-frontend:
	docker build --build-arg CONFIG="production" -f ./frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:latest --platform linux/amd64 .

build-frontend-dev:
	docker build --build-arg CONFIG="staging" -f ./frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:dev --platform linux/amd64 .

# Push

push-backend:
	docker push ghcr.io/alex4270/order-panel-backend:latest

push-backend-dev:
	docker push ghcr.io/alex4270/order-panel-backend:dev

push-frontend:
	docker push ghcr.io/alex4270/order-panel-frontend:latest

push-frontend-dev:
	docker push ghcr.io/alex4270/order-panel-frontend:dev
