# TuTramiteFacil — Prueba Técnica

API REST + SPA React para gestión de solicitantes y solicitudes de ayudas sociales, desarrollada con **Laravel 11 (DDD + Clean Architecture)** y **React + Vite + TypeScript + Tailwind v4**.

---

## Credenciales de demo

> **Email:** demo@tutramitefacil.com  
> **Contraseña:** demo1234

---

## Stack

**Backend:** Laravel 11, PHP 8.4, MySQL 8, Redis, Laravel Horizon, JWT (`php-open-source-saver/jwt-auth`), Swagger/OpenAPI (`l5-swagger`), Pest

**Frontend:** React 19, Vite 8, TypeScript, Tailwind CSS v4, Zustand, React Router, Axios

**Infraestructura:** Docker Compose (5 servicios: app, nginx, mysql, redis, horizon)

---

## Requisitos previos

- Docker y Docker Compose
- Node.js 18+
- Git

---

## Levantar el proyecto

```bash
git clone https://github.com/Carvalx/tutramitefacil-backend.git
cd tutramitefacil-backend
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan l5-swagger:generate
```

## Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## URLs disponibles

| Servicio | URL |
|---|---|
| API REST | http://localhost:8010/api |
| Swagger UI | http://localhost:8010/api/documentation |
| Horizon Dashboard | http://localhost:8010/horizon |
| Frontend React | http://localhost:5173 |

---

## Migraciones y seeders

```bash
# Migraciones pendientes
docker compose exec app php artisan migrate

# Reset completo + seeders
docker compose exec app php artisan migrate:fresh --seed

# Solo seeders
docker compose exec app php artisan db:seed
```

Los seeders crean 15 solicitantes con datos reales en español y entre 1-4 solicitudes por solicitante, además del usuario de demo.

---

## Tests

```bash
# Backend (Pest)
docker compose exec app ./vendor/bin/pest

# Frontend (Vitest)
cd frontend && npm run test
```

---

## Ejemplos de llamadas a la API

```bash
# Login
curl -s -X POST http://localhost:8010/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@tutramitefacil.com","password":"demo1234"}'

# Listar solicitantes (público)
curl -s http://localhost:8010/api/solicitantes

# Crear solicitante (requiere token)
curl -s -X POST http://localhost:8010/api/solicitantes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"nombre":"Carlos","apellidos":"Macero","email":"carlos@example.com","telefono":"600000000","comunidad_autonoma":"Comunidad Valenciana","fecha_registro":"2026-06-15"}'

# Crear solicitud (encola ProcesarSolicitudJob en Horizon)
curl -s -X POST http://localhost:8010/api/solicitudes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"solicitante_id":"<uuid>","tipo_ayuda":"Alquiler","fecha_solicitud":"2026-06-15","importe_estimado":500}'
```

---

## Arquitectura DDD

---

## Decisiones técnicas

- **`php-open-source-saver/jwt-auth` en vez de Sanctum** — Sanctum está orientado a SPA con cookies de sesión. JWT es stateless y más apropiado para una API pura consumida por cualquier cliente.
- **Zustand en vez de Redux Toolkit** — Redux añade demasiado boilerplate para el alcance de esta prueba. Zustand cubre el mismo caso de uso con mucho menos código.
- **Jobs con datos primitivos, no Entities** — los Jobs se serializan en Redis. Las Entities de dominio (con enums PHP, etc.) pueden no deserializar limpiamente, así que se pasan solo strings y numbers.
- **Enums en Domain, no en Infrastructure** — `TipoAyuda` y `Estado` son conceptos de negocio, no de persistencia. Eloquent los castea, pero su definición pertenece al dominio.

---

## Procesamiento asíncrono (Horizon)

Al crear una solicitud o cambiar su estado, se encola automáticamente un `ProcesarSolicitudJob` procesado por el worker de Horizon. Dashboard: `http://localhost:8010/horizon`.

---

## CI/CD

GitHub Actions ejecuta automáticamente los tests de Pest y Vitest en cada push a `develop` o `main`.

---

## Git Flow

Ramas: `main`, `develop`, `feature/*`.
