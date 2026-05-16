# Bootstrap — running the Laravel scaffolding

Run these from the repo root, once, when you're ready to take
this from "design doc" to "running app." Order matters: each step
assumes the previous one.

## Prerequisites

```bash
php --version    # 8.3+
composer --version  # 2.x
node --version   # 22+
pnpm --version   # 9+ (or npm 10+; this guide uses pnpm)
```

## 1. Laravel skeleton

```bash
# Empty-dir trick: laravel/laravel refuses to create-project in a
# non-empty dir, but we have CI scaffolding + docs already. Move
# those aside, install, then move them back.
mkdir .bootstrap-keep
mv .github docs CHANGELOG.md CODE_OF_CONDUCT.md CONTRIBUTING.md \
   INITIALIZE.md LICENSE README.md SECURITY.md .editorconfig \
   .bootstrap-keep/

composer create-project laravel/laravel . "^13"

# Merge back. composer ships its own .gitignore + README; we
# overwrite with ours because Simtabi conventions take priority.
cp -r .bootstrap-keep/. .
rm -rf .bootstrap-keep
```

## 2. Auth: Laravel Passport

```bash
composer require laravel/passport
php artisan install:api --passport
php artisan migrate
php artisan passport:keys
```

Add a client for the React frontend:

```bash
php artisan passport:client --personal
```

## 3. Frontend: Inertia + React + Tailwind

```bash
composer require inertiajs/inertia-laravel
php artisan inertia:middleware

pnpm add react react-dom @inertiajs/react @vitejs/plugin-react \
        tailwindcss@latest @tailwindcss/forms autoprefixer postcss
pnpm add -D typescript @types/react @types/react-dom \
            @typescript-eslint/eslint-plugin eslint prettier
```

Configure Vite for React (`vite.config.ts`):

```ts
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.tsx', 'resources/css/app.css'],
      refresh: true,
    }),
    react(),
  ],
})
```

## 4. Multi-tenancy

Pick ONE — both work; document the choice in `docs/architecture.md`:

- **Single-DB, tenant_id column**:
  `composer require stancl/tenancy` (rejected here — overkill for
  the audit-log + registry surface).
- **Hand-rolled tenant scope** (preferred):
  Each Eloquent model has `tenant_id`. Global scopes filter by
  `auth()->user()->tenant_id`. ~50 lines of Tenant + TenantScope
  classes. Skip the package; the surface is small enough.

Migrate:

```bash
php artisan make:model Tenant -m
php artisan make:model Registry -m
php artisan make:model AuditLogEntry -m
php artisan migrate
```

## 5. OpenAPI spec

Authoritative API spec lives at `docs/api/v1.yaml`. Generate
client SDKs from it via `npx openapi-typescript docs/api/v1.yaml
-o resources/js/api/types.ts`. Controllers conform to the spec;
write integration tests against it via
[`openapi-validator`](https://www.npmjs.com/package/express-openapi-validator)-equivalent
for Laravel: [`hkulekci/php-openapi-validator`](https://github.com/hkulekci/php-openapi-validator).

```bash
composer require --dev hkulekci/openapi-validator
```

## 6. Testing

```bash
# Pest is the Laravel 13 default. Confirm it's wired:
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
./vendor/bin/pest --init

# Quality gates that the CI already expects:
composer require --dev laravel/pint phpstan/phpstan \
                       larastan/larastan rector/rector
```

## 7. First commit

```bash
git add -A
git commit -m "feat: Laravel 13 + Inertia + React + Passport bootstrap"
git push origin main
```

CI fires on push. The matrix in `.github/workflows/ci.yml` is
already configured for PHP 8.3 + 8.4 and Node 22.

## 8. Decision log

After bootstrap, fill in `docs/decisions.md` with ADRs for:

- Multi-tenancy approach (stancl vs hand-rolled).
- OAuth providers (GitHub + GitLab confirmed; Microsoft Entra
  pending real customer).
- Deployment target (Forge vs Vapor vs self-host Docker).
- Background worker driver (Redis vs database queue).
- Where the registry signed-URL secret lives.

## Done? Next steps

1. Update `README.md` "Status" table: bootstrap → done.
2. Land an `ADR-0001` recording the multi-tenancy + auth choices.
3. Open the first PR against `simtabi/get-installer-admin` with
   the bootstrap commit + this update.
