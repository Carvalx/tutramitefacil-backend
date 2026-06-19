# TuTrámiteFácil — Backend

API REST para la gestión de solicitudes de ayudas gubernamentales, construida con Laravel 11 (PHP 8.4) y arquitectura limpia por dominio (DDD).

## Requisitos

- Docker y Docker Compose
- (Opcional, sin Docker) PHP 8.4 + Composer + MySQL 8

## Puesta en marcha

```bash
# 1. Copiar variables de entorno
cp .env.example .env

# 2. Levantar los contenedores
docker compose up -d

# 3. Ejecutar migraciones
docker compose exec app php artisan migrate
```

La API queda disponible en `http://localhost:8010/api/`.

## Comandos habituales

| Tarea | Comando |
|---|---|
| Levantar servicios | `docker compose up -d` |
| Migrar | `docker compose exec app php artisan migrate` |
| Resetear BD | `docker compose exec app php artisan migrate:fresh` |
| Ejecutar tests | `docker compose exec app php artisan test` |
| Un solo test | `docker compose exec app php artisan test --filter=NombreTest` |
| REPL | `docker compose exec app php artisan tinker` |
| Formatear código | `docker compose exec app ./vendor/bin/pint` |
| Verificar formato | `docker compose exec app ./vendor/bin/pint --test` |

## Estructura del proyecto

La aplicación está organizada por dominios bajo `app/`. Cada dominio sigue una arquitectura limpia de 3 capas:

```
app/{Dominio}/
├── Domain/
│   ├── Entities/       # Clases PHP puras — sin Eloquent ni Laravel
│   ├── Enums/          # Enums de PHP 8.1 (objetos de valor)
│   └── Repositories/   # Solo interfaces — definen QUÉ, no CÓMO
├── Application/
│   └── Services/       # Orquestación de casos de uso
└── Presentation/
    ├── Controllers/    # Finos: validar → llamar servicio → devolver recurso
    ├── Requests/       # Validación con Form Requests de Laravel
    └── Resources/      # Formato de respuesta (JsonResource)
```

Dominios actuales: `Solicitantes` y `Solicitudes`.

La infraestructura (modelos Eloquent y repositorios concretos) vive en `app/{Dominio}/Infrastructure/Persistence/`.

## Infraestructura Docker

| Servicio | Descripción | Puerto externo |
|---|---|---|
| `app` | PHP-FPM 8.4 | — |
| `nginx` | Servidor web | 8010 |
| `mysql` | MySQL 8 | 3307 |
| `redis` | Caché y colas | 6379 |

## Convenciones clave

- **UUIDs como PK** en todos los modelos de dominio.
- **Enums PHP** (`TipoAyuda`, `Estado`) para valores de dominio.
- **Sin Eloquent en el dominio**: las entidades son clases PHP puras e inmutables.
- **Rutas**: `routes/api.php` con `Route::apiResource()`. Actualmente públicas; se añadirá middleware JWT (`auth:api`) en rutas de escritura al implementar autenticación.

## Tests

Los tests requieren el contenedor de MySQL activo. SQLite está desactivado en `phpunit.xml` por diseño.

```bash
docker compose exec app php artisan test
```

## Licencia

MIT
