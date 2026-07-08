# 🐳 Docker Local Environment Setup Guide

## 📋 Contents
1. [Tech Stack](#-tech-stack)
2. [Build Process](#️-build-process)
3. [Everyday Usage](#-everyday-usage)
4. [Environment Variables](#-environment-variables)
5. [Useful Commands](#-useful-commands)
6. [Application Availability](#-application-availability)

---

## 🛠️ Tech Stack
The project's local environment configuration consists of these fundamental elements:

| Component | Technology | Version |
|-----------|-----------|---------|
| **Backend** | Laravel | 13 |
| **Frontend** | Angular | 21 |
| **Database** | PostgreSQL | 18 |

---

## 🏗️ Build Process

### First Time Setup

Build and run all services from scratch:
```bash
docker compose up --build -d
```

## 🚀 Everyday Usage

### Start Services

To run containers without rebuilding:
```bash
docker compose up -d
```

### Stop Services
```bash
docker compose down
```

### View Logs
```bash
# All services
docker compose logs -f

# Specific service (backend)
docker compose logs -f backend
```

---

## 🔐 Environment Variables

All environment variables (such as PostgreSQL credentials) are stored in a dedicated `.env` file located in the project root.

### Example Structure
```env
APP_NAMESPACE=app-local

POSTGRES_PASSWORD=example_password
POSTGRES_USER=example_user
POSTGRES_DB=example_db

FRONTEND_URL=app.local
BACKEND_URL=api.app.local
REVERB_URL=ws.app.local
```

> ⚠️ **Security Note:**  
> Environment variables are stored as GitHub Secrets and are being copied to the deployment server during the deployment process.

---

## 💡 Useful Commands

| Task | Command |
|------|---------|
| View running containers | `docker compose ps` |
| Restart a service | `docker compose restart <service>` |
| Rebuild after changes | `docker compose up -d --build` |
| Access container shell | `docker compose exec backend bash` |
| Remove all containers & volumes | `docker compose down -v` |

---

## 📚 Application Availability
✅ Particular services can be accessed here:
  - Frontend: `http://localhost:4200`
  - Backend: `http://localhost:8000`
  - Database: `localhost:5432`

---