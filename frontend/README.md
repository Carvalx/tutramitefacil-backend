# TuTramiteFacil — Prueba Técnica

API REST + SPA React para gestión de solicitantes y solicitudes de ayudas sociales, desarrollada con **Laravel 11 (DDD + Clean Architecture)** y **React + Vite + TypeScript + Tailwind v4**.

---

## Credenciales de demo

Email: demo@tutramitefacil.com
Contraseña: demo1234

---

## Stack

**Backend:** Laravel 11, PHP 8.4, MySQL 8, Redis, Laravel Horizon, JWT (`php-open-source-saver/jwt-auth`), Swagger/OpenAPI (`l5-swagger`), Pest

**Frontend:** React 19, Vite 8, TypeScript, Tailwind CSS v4, Zustand, React Router, Axios

**Infraestructura:** Docker Compose (5 servicios: app, nginx, mysql, redis, horizon)

---

## Requisitos previos

- Docker y Docker Compose instalados
- Git
- Node.js 18+ (solo para desarrollo local del frontend)

---

## Levantar el proyecto

```bash
# 1. Clona el repositorio
git clone https://github.com/Carvalx/tutramitefacil-backend.git
cd tutramitefacil-backend

# 2. Copia el archivo de entorno
cp .env.example .env

# 3. Levanta todos los contenedores (construye las imágenes la primera vez)
docker compose up -d --build

# 4. Instala dependencias PHP (si no se instalaron en el build)
docker compose exec app composer install

# 5. Genera la clave de aplicación
docker compose exec app php artisan key:generate

# 6. Genera el secret JWT
docker compose exec app php artisan jwt:secret

# 7. Ejecuta las migraciones y seeders
docker compose exec app php artisan migrate --seed

# 8. Genera la documentación Swagger
docker compose exec app php artisan l5-swagger:generate
```

El backend estará disponible en `http://localhost:8010`

---

## Frontend (desarrollo local)

```bash o cmd
cd frontend
npm install
npm run dev
```

El frontend estará disponible en `http://localhost:5173`

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
# Ejecutar migraciones pendientes
docker compose exec app php artisan migrate

# Ejecutar migraciones + seeders (borra datos existentes)
docker compose exec app php artisan migrate:fresh --seed

# Solo seeders (sin borrar tablas)
docker compose exec app php artisan db:seed
```

Los seeders crean: **15 solicitantes** con nombres y comunidades autónomas reales en español, y entre **1-4 solicitudes** por solicitante con tipos de ayuda y estados variados.

---

## Tests

```bash
# Correr todos los tests de backend (Pest)
docker compose exec app ./vendor/bin/pest

# Correr un archivo de tests específico
docker compose exec app ./vendor/bin/pest tests/Feature/AuthTest.php
docker compose exec app ./vendor/bin/pest tests/Feature/SolicitanteTest.php
docker compose exec app ./vendor/bin/pest tests/Feature/SolicitudTest.php

# Correr tests del frontend (Vitest)
cd frontend
npm run test
```

---

## Ejemplos de llamadas a la API

### Registro e inicio de sesión

```bash
# Registrar un usuario
curl -s -X POST http://localhost:8010/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Carlos","email":"carlos@test.com","password":"password123"}'

# Iniciar sesión (devuelve access_token)
curl -s -X POST http://localhost:8010/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"carlos@test.com","password":"password123"}'
```

### Solicitantes (GET público, POST/PUT/DELETE requieren JWT)

```bash
# Listar todos los solicitantes (público)
curl -s http://localhost:8010/api/solicitantes

# Crear un solicitante (requiere token)
curl -s -X POST http://localhost:8010/api/solicitantes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{
    "nombre": "Carlos",
    "apellidos": "Macero",
    "email": "carlos@example.com",
    "telefono": "600000000",
    "comunidad_autonoma": "Comunidad Valenciana",
    "fecha_registro": "2026-06-15"
  }'

# Ver detalle de un solicitante (público)
curl -s http://localhost:8010/api/solicitantes/<uuid>

# Actualizar un solicitante (requiere token)
curl -s -X PUT http://localhost:8010/api/solicitantes/<uuid> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"comunidad_autonoma": "Madrid"}'

# Eliminar un solicitante (requiere token)
curl -s -X DELETE http://localhost:8010/api/solicitantes/<uuid> \
  -H "Authorization: Bearer <token>"
```

### Solicitudes (GET público, POST/PUT/DELETE requieren JWT)

```bash
# Listar todas las solicitudes (público)
curl -s http://localhost:8010/api/solicitudes

# Crear una solicitud (requiere token, encola ProcesarSolicitudJob en Horizon)
curl -s -X POST http://localhost:8010/api/solicitudes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{
    "solicitante_id": "<uuid-solicitante>",
    "tipo_ayuda": "Alquiler",
    "fecha_solicitud": "2026-06-15",
    "importe_estimado": 500.00
  }'

# Cambiar estado de una solicitud (encola Job si el estado cambia)
curl -s -X PUT http://localhost:8010/api/solicitudes/<uuid> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"estado": "Concedida"}'
```

---

## Arquitectura DDD
---

## Procesamiento asíncrono (Horizon)

Al crear una solicitud o cambiar su estado, se encola automáticamente un `ProcesarSolicitudJob` procesado por el worker de Horizon. El dashboard de Horizon está disponible en `http://localhost:8010/horizon`.

---

## Git Flow

El proyecto usa Git Flow con ramas `main`, `develop`, y `feature/*`.

---

## Licencia

Proyecto de prueba técnica — uso privado.
