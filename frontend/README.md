# TuTrámiteFácil — Prueba Técnica

API REST + SPA React para la gestión de solicitantes y solicitudes de ayudas sociales. El backend está en Laravel 11 con DDD + Clean Architecture, y el frontend en React 19 con Vite y Tailwind v4.

DDD en Laravel no es lo habitual — el ecosistema empuja hacia ActiveRecord y Eloquent en todas las capas. La decisión de separar Domain, Application e Infrastructure aquí tiene sentido porque el dominio de ayudas sociales tiene suficiente lógica propia (tipos de ayuda, estados, validaciones de negocio) como para que la separación no sea arquitectura por arquitectura.

---

## Credenciales de demo

> **Email:** `demo@tutramitefacil.com`
> **Contraseña:** `demo1234`

---

## Stack

**Backend:** Laravel 11, PHP 8.4, MySQL 8, Redis, Laravel Horizon, JWT (`php-open-source-saver/jwt-auth`), Swagger/OpenAPI (`l5-swagger`), Pest

**Frontend:** React 19, Vite 8, TypeScript, Tailwind CSS v4, Zustand, React Router, Axios

**Infraestructura:** Docker Compose (5 servicios: app, nginx, mysql, redis, horizon)

---

## Requisitos previos

- Docker y Docker Compose instalados
- Git
- Node.js 18+ (solo si quieres desarrollar el frontend localmente sin Docker)

---

## Levantar el proyecto

Para tenerlo funcionando desde cero, ejecuta estos pasos en orden:

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

El backend quedará disponible en `http://localhost:8010`.

---

## Frontend (desarrollo local)

```bash
cd frontend
npm install
npm run dev
```

El frontend quedará disponible en `http://localhost:5173`.

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

Los seeders crean **15 solicitantes** con nombres y comunidades autónomas reales en español, y entre **1–4 solicitudes** por solicitante con tipos de ayuda y estados variados.

---

## Tests

```bash
# Todos los tests de backend (Pest)
docker compose exec app ./vendor/bin/pest

# Un archivo concreto
docker compose exec app ./vendor/bin/pest tests/Feature/AuthTest.php
docker compose exec app ./vendor/bin/pest tests/Feature/SolicitanteTest.php
docker compose exec app ./vendor/bin/pest tests/Feature/SolicitudTest.php

# Tests del frontend (Vitest)
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

# Crear una solicitud (requiere token; encola ProcesarSolicitudJob en Horizon)
curl -s -X POST http://localhost:8010/api/solicitudes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{
    "solicitante_id": "<uuid-solicitante>",
    "tipo_ayuda": "Alquiler",
    "fecha_solicitud": "2026-06-15",
    "importe_estimado": 500.00
  }'

# Cambiar el estado de una solicitud (encola Job si el estado cambia)
curl -s -X PUT http://localhost:8010/api/solicitudes/<uuid> \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"estado": "Concedida"}'
```

---

## Arquitectura DDD

El backend está organizado por dominios, no por tipo de archivo. Cada dominio tiene su propia pila de tres capas:

```
app/
├── Auth/
│   └── Presentation/Controllers/
│       └── AuthController.php              # Login, registro, logout (JWT)
│
├── Solicitantes/
│   ├── Domain/
│   │   ├── Entities/
│   │   │   └── Solicitante.php             # PHP puro: sin Eloquent, sin Laravel
│   │   └── Repositories/
│   │       └── SolicitanteRepositoryInterface.php  # Define QUÉ, no el CÓMO
│   ├── Application/
│   │   └── Services/
│   │       └── SolicitanteService.php      # Orquesta los casos de uso
│   ├── Infrastructure/
│   │   └── Persistence/
│   │       ├── EloquentSolicitante.php     # Modelo Eloquent (solo existe aquí)
│   │       └── EloquentSolicitanteRepository.php  # Implementa la interfaz de Domain
│   └── Presentation/
│       ├── Controllers/SolicitanteController.php
│       ├── Requests/                       # Validación de entrada HTTP
│       └── Resources/SolicitanteResource.php  # Formato de salida JSON
│
├── Solicitudes/
│   ├── Domain/
│   │   ├── Entities/Solicitud.php
│   │   ├── Enums/
│   │   │   ├── Estado.php                  # Pendiente | EnRevision | Concedida | Denegada
│   │   │   └── TipoAyuda.php               # Alquiler | ChequeBebe | IMV | BonoCultural
│   │   └── Repositories/SolicitudRepositoryInterface.php
│   ├── Application/Services/SolicitudService.php
│   ├── Infrastructure/
│   │   ├── Jobs/
│   │   │   └── ProcesarSolicitudJob.php    # Se encola en Redis al crear/cambiar estado
│   │   └── Persistence/
│   │       ├── EloquentSolicitud.php
│   │       └── EloquentSolicitudRepository.php
│   └── Presentation/
│       ├── Controllers/SolicitudController.php
│       ├── Requests/
│       └── Resources/SolicitudResource.php
│
└── Providers/
    └── AppServiceProvider.php              # Vincula interfaces → implementaciones (IoC)
```

La regla de dependencia va en una sola dirección: **Presentation → Application → Domain**. Infrastructure implementa los contratos que define Domain, pero nunca al revés. El binding entre interfaz e implementación ocurre en `AppServiceProvider` — es el único sitio donde Laravel sabe que cuando alguien pide `SolicitudRepositoryInterface`, debe entregar `EloquentSolicitudRepository`.

---

## Decisiones técnicas

- **`php-open-source-saver/jwt-auth` en vez de Sanctum:** Sanctum está pensado para SPAs del mismo dominio usando cookies de sesión. Esta API puede ser consumida desde clientes externos, así que el enfoque stateless con JWT tiene más sentido. Los tokens viajan en el header `Authorization: Bearer` en cada petición, sin estado en el servidor.

- **Zustand en vez de Redux:** Redux añade demasiado boilerplate para lo que necesita este frontend. El store de autenticación cabe en 30 líneas y hace exactamente lo que se necesita: guardar el token en localStorage, exponerlo al interceptor de Axios y mantener `isAuthenticated` actualizado para que los componentes reaccionen al login/logout.

- **Los Jobs reciben datos primitivos, no la Entity:** Los Jobs se serializan en Redis para ser procesados de forma asíncrona por Horizon. Si se pasara la Entity de Domain directamente (con enums PHP, `DateTimeImmutable`, etc.), la serialización/deserialización podría fallar o producir un objeto en estado inconsistente al rehidratarse. Pasar strings simples es más predecible.

- **Los enums viven en Domain, no en Infrastructure:** `Estado` y `TipoAyuda` son conceptos de negocio, no detalles de implementación. Que la base de datos los almacene como `string` es una decisión de Infrastructure; que existan los valores `Concedida` o `Denegada` es una regla del dominio. El repositorio los convierte con `Estado::from()` al leer de BD — y si la BD tuviera un valor inválido, `from()` lanzaría un `ValueError` actuando como validación de integridad gratuita.

- **UUID como clave primaria en vez de bigint autoincremental:** Los UUIDs permiten generar IDs en cualquier capa sin round-trip a la base de datos. También evitan exponer información sobre el volumen de registros (un `id=42` revela cuántos solicitantes hay; un UUID no revela nada).

---

## Procesamiento asíncrono (Horizon)

Al crear una solicitud o cambiar su estado, se encola automáticamente un `ProcesarSolicitudJob` que Horizon procesa en segundo plano. En producción ese Job enviaría un email al solicitante y haría la verificación con los sistemas de la administración; en este proyecto logea la acción y simula el trabajo con un `sleep(1)` para que puedas ver el Job aparecer y completarse en el dashboard de Horizon (`http://localhost:8010/horizon`).

---

## Git Flow

El proyecto usa Git Flow con ramas `main`, `develop` y `feature/*`.

---

## Licencia

Proyecto de prueba técnica — uso privado.
