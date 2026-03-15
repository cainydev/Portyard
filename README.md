<img width="2498" height="798" alt="Portyard Banner" src="https://github.com/user-attachments/assets/2397ef90-c28c-49ad-949a-59504340ab49" />

## Portyard

A self-hostable container image registry with a management UI, built with Laravel, Livewire, and Flux UI.

Portyard wraps the [CNCF Distribution registry](https://distribution.github.io/distribution/) with JWT-based authentication, team management, activity logging, and a web dashboard for browsing images, tags, and layers. It works with Docker, Podman, and any OCI-compliant client.

### Cloud Service

The hosted instance is available at [portyard.de](https://portyard.de) and is free during the open beta.

```bash
# Tag, authenticate, and push
docker tag my-app:latest portyard.de/<namespace>/<repository>:<tag>
docker login portyard.de
docker push portyard.de/<namespace>/<repository>:<tag>
```

### Features

- **Spaces & Namespaces** -- Organize repositories under spaces with storage quotas and their own namespace (`domain/<namespace>/<repo>`)
- **Role-Based Access Control** -- Owner, Maintainer, Developer, and Viewer roles per space with invitation system
- **Public & Private Repositories** -- Toggle visibility per repository; public repos allow anonymous pulls
- **Multi-Architecture Support** -- Full manifest list / OCI index support for multi-platform images
- **Image Inspector** -- Browse tags, layers, architecture, OS, and config metadata through the web UI
- **Activity Log** -- Audit trail for auth events, space/member changes, pushes, pulls, and deletions
- **Webhooks** -- Per-repository webhook notifications on configurable trigger events
- **Two-Factor Authentication** -- TOTP-based 2FA via Laravel Fortify

### Self-Hosting

Portyard is a standard Laravel application. For general deployment guidance, see the official [Laravel deployment documentation](https://laravel.com/docs/12.x/deployment).

The key difference from a typical Laravel app is the **Docker registry sidecar** -- a `registry:2` container that Portyard authenticates and receives events from. The sections below explain how to set that up.

#### Prerequisites

- PHP 8.5+ with standard Laravel extensions
- MariaDB / MySQL
- A `registry:2` container (Docker Distribution)
- A reverse proxy that routes `/v2/` to the registry and everything else to Laravel (Traefik is used in the included compose files, but Nginx/Caddy work too)
- A domain with TLS (required by the Docker client for authentication)

#### 1. Generate JWT Key Pair

The registry and Laravel share an RSA key pair for token authentication. The registry gets the **public** key to verify tokens; Laravel gets the **private** key to sign them.

```bash
openssl genrsa -out certs/private.pem 4096
openssl rsa -in certs/private.pem -pubout -out certs/public.pem

# The registry needs the public key as a certificate:
openssl req -new -x509 -key certs/private.pem -out certs/auth.crt -days 3650 -subj "/CN=portyard"
```

#### 2. Configure Environment

Beyond the standard Laravel `.env` values, set the Dockhand variables:

```env
APP_DOMAIN=registry.example.com

# Dockhand (Container Registry)
DOCKHAND_BASE_URI=http://registry:5000/v2/       # Internal URL to the registry container
DOCKHAND_PRIVATE_KEY=/path/to/certs/private.pem   # RSA private key (signs JWT tokens)
DOCKHAND_PUBLIC_KEY=/path/to/certs/public.pem     # RSA public key
DOCKHAND_AUTHORITY_NAME=auth                      # JWT issuer claim
DOCKHAND_REGISTRY_NAME=registry                   # JWT audience / service name
DOCKHAND_NOTIFICATIONS_ROUTE=/dockhand/notify     # Webhook endpoint the registry POSTs to
DOCKHAND_NOTIFICATIONS_TOKEN=<random-secret>      # Shared secret for registry -> Laravel webhooks
```

#### 3. Configure the Registry Container

The registry must be configured to authenticate against Laravel and send push/pull notifications back. The critical environment variables on the `registry:2` container are:

```yaml
environment:
  REGISTRY_AUTH: token
  REGISTRY_AUTH_TOKEN_ISSUER: ${DOCKHAND_AUTHORITY_NAME}       # Must match Laravel
  REGISTRY_AUTH_TOKEN_SERVICE: ${DOCKHAND_REGISTRY_NAME}       # Must match Laravel
  REGISTRY_AUTH_TOKEN_REALM: https://${APP_DOMAIN}/auth/token   # Public URL to Laravel's token endpoint
  REGISTRY_AUTH_TOKEN_ROOTCERTBUNDLE: /etc/docker/registry/auth.pem  # Mount the public cert here

  REGISTRY_NOTIFICATIONS_ENDPOINTS: >-
    - name: portyard
      url: http://laravel${DOCKHAND_NOTIFICATIONS_ROUTE}
      headers:
        Authorization: ["Bearer ${DOCKHAND_NOTIFICATIONS_TOKEN}"]
      timeout: 500ms
      threshold: 5
      backoff: 1s

  REGISTRY_HTTP_SECRET: ${APP_KEY}
```

Mount the public certificate into the container at the path specified by `REGISTRY_AUTH_TOKEN_ROOTCERTBUNDLE`.

The notification URL should point to Laravel's **internal** hostname (e.g., `http://laravel:80/dockhand/notify`), not the public domain.

#### 4. Reverse Proxy Routing

Your reverse proxy must route requests so that:

- `https://your-domain.com/v2/*` goes to the **registry** container (port 5000)
- Everything else goes to **Laravel** (port 80)

Both must be served under the **same domain** with TLS. The included `docker-compose.yml` and Traefik configs demonstrate this with path-based routing and priority rules.

#### 5. Authentication Flow (How It Works)

Understanding the flow helps with debugging:

1. `docker login your-domain.com` sends credentials to `/auth/token` (Laravel)
2. Laravel validates the user and returns a signed JWT
3. `docker push` sends the JWT to `/v2/` (registry)
4. The registry verifies the JWT signature against the public certificate
5. After storing blobs, the registry POSTs a notification to Laravel via the webhook endpoint
6. Laravel records the manifest, tags, layers, and updates storage counters

If pushes silently fail, check that:
- The `REGISTRY_AUTH_TOKEN_ISSUER` and `REGISTRY_AUTH_TOKEN_SERVICE` match between registry and Laravel
- The public certificate mounted in the registry matches the private key Laravel signs with
- The notification URL is reachable from the registry container
- The `DOCKHAND_NOTIFICATIONS_TOKEN` matches on both sides

### Development

The project includes a full Docker Compose setup with Traefik, MariaDB, Valkey, and the registry:

```bash
cp .env.example .env
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate --seed
vendor/bin/sail npm run dev
```

### Tech Stack

- **Backend:** Laravel 12, PHP 8.5
- **Frontend:** Livewire 4, Flux UI Pro, Tailwind CSS 4
- **Registry Bridge:** [Laravel Dockhand](https://github.com/cainydev/laravel-dockhand)
- **Auth:** Laravel Fortify, Sanctum
- **Database:** MariaDB
- **Registry:** CNCF Distribution (registry:2)

### License

MIT -- see [LICENSE](LICENSE).

### Contact

Report issues via [GitHub Issues](https://github.com/cainydev/portyard/issues) or reach out on GitHub: [@cainydev](https://github.com/cainydev)
