build-backend:
	docker build -f ./backend/Dockerfile -t ghcr.io/alex4270/order-panel-backend:latest --platform linux/amd64 .

build-frontend-production:
	docker build --build-arg CONFIG="production" -f ./frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:production --platform linux/amd64 .

build-frontend-staging:
	docker build --build-arg CONFIG="staging" -f ./frontend/Dockerfile -t ghcr.io/alex4270/order-panel-frontend:staging --platform linux/amd64 .

push-backend:
	docker push ghcr.io/alex4270/order-panel-backend:latest

push-frontend-staging:
	docker push ghcr.io/alex4270/order-panel-frontend:staging

push-frontend-production:
	docker push ghcr.io/alex4270/order-panel-frontend:production
