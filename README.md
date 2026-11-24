# SymHome

<!-- Badges: add CI / license badges once a pipeline and a LICENSE file exist. -->

SymHome is a furniture e-commerce web application built with Symfony 7.4. Customers browse and search furniture, manage a shopping cart, go through a simulated card payment and follow their orders; an admin area manages furniture, categories, orders and users.

## Main features

**Customer side**

- Home page and product catalogue (`/produits`) with keyword search, category filter and pagination (6 items per page).
- Product detail page.
- Shopping cart stored in the session: add, increase / decrease quantity, remove, empty.
- Checkout with a **simulated** card payment (the card number must contain exactly 13 digits), then order confirmation.
- Order history and order detail (`/mes-commandes`).
- Registration, login / logout (Symfony Security form login).
- Account area (`/mon-compte`): edit profile, change password, delete account.

**Admin side** (`/admin/...`)

- Dashboard with counts of customers, furniture and orders.
- Furniture CRUD (name, description, price, stock, image, category).
- Category CRUD.
- Order list, detail and status update (`en_attente`, `confirmee`, `annulee`) with CSRF protection.
- User list, detail and deletion.

## Screenshots / demo

Screenshots are stored in [./screenshots/client/](./screenshots/client/).

| Home | Cart | Payment |
| --- | --- | --- |
| ![Home](./screenshots/client/accueil.png) | ![Cart](./screenshots/client/panier.png) | ![Payment](./screenshots/client/valider%20et%20confirmer.png) |

| Login | My orders | Admin dashboard |
| --- | --- | --- |
| ![Login](./screenshots/client/login.png) | ![My orders](./screenshots/client/mes%20commandes.png) | ![Admin dashboard](./screenshots/client/dashboard%20admin.png) |

## Prerequisites

- **PHP 8.2+** with the `ctype` and `iconv` extensions (plus the PDO driver of your database)
- **Composer 2**
- A database supported by Doctrine: the provided [compose.yaml](./compose.yaml) starts **PostgreSQL 16**; MySQL / MariaDB / SQLite also work through `DATABASE_URL`
- *(optional)* [Symfony CLI](https://symfony.com/download) for the local web server
- *(optional)* Docker + Docker Compose for the database

## Installation

```bash
git clone https://github.com/nour-agrebi-web/SymHome.git
cd SymHome
composer install
```

`composer install` also runs `cache:clear`, `assets:install` and `importmap:install` (Symfony Flex auto-scripts).

## Configuration / environment variables

Symfony reads `.env`, then `.env.local` (not committed, listed in `.gitignore`). Put your local values in `.env.local`.

| Variable | Value in `.env` | Description |
| --- | --- | --- |
| `APP_ENV` | `dev` | Environment (`dev`, `test`, `prod`) |
| `APP_SECRET` | *(empty; set in `.env.dev`)* | Symfony secret |
| `DATABASE_URL` | **commented out** | **Required.** Doctrine connection URL |
| `MESSENGER_TRANSPORT_DSN` | `doctrine://default?auto_setup=0` | Messenger transport |
| `MAILER_DSN` | `null://null` | Mailer (disabled) |
| `DEFAULT_URI` | `http://localhost` | Base URI for URLs generated outside HTTP requests |

Docker Compose variables for the database container: `POSTGRES_VERSION` (default `16`), `POSTGRES_DB` (`app`), `POSTGRES_USER` (`app`), `POSTGRES_PASSWORD` (`!ChangeMe!`).

Example `.env.local` matching the default Docker database:

```dotenv
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

Or for a local MySQL (adapt user, password, version and database name):

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:3306/symhome?serverVersion=8.0.32&charset=utf8mb4"
```

## Running the project

1. Start the database (Docker option):

   ```bash
   docker compose up -d database
   ```

2. Create the database and the schema. No migration files are committed (see notes), so generate one first:

   ```bash
   php bin/console doctrine:database:create --if-not-exists
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

3. Start the web server:

   ```bash
   symfony server:start
   # or, without the Symfony CLI:
   php -S 127.0.0.1:8000 -t public
   ```

4. Open <http://127.0.0.1:8000>.

## Usage

### Main routes

| Route | Description |
| --- | --- |
| `/` | Home page |
| `/produits` | Catalogue (`search`, `categorie`, `page` query parameters) |
| `/produit/{id}` | Product detail |
| `/panier` | Cart (`/panier/ajouter/{id}`, `/panier/plus/{id}`, `/panier/moins/{id}`, `/panier/supprimer/{id}`, `/panier/vider`) |
| `/commande/paiement` → `/commande/checkout` → `/commande/confirmation/{id}` | Payment, order creation, confirmation |
| `/mes-commandes`, `/mes-commandes/{id}` | Order history and detail |
| `/register`, `/login`, `/logout` | Account creation and authentication |
| `/mon-compte`, `/mon-compte/modifier`, `/mon-compte/mot-de-passe` | Account area (requires `ROLE_USER`) |
| `/admin/dashboard` | Admin dashboard |
| `/admin/meuble`, `/admin/categorie` | Furniture and category CRUD |
| `/admin/commandes`, `/admin/users` | Orders and users management |

Furniture images are served from `public/uploads/meubles/`.

The full route list can be printed with:

```bash
php bin/console debug:router
```

## Tests

PHPUnit 11 is installed and configured ([phpunit.dist.xml](./phpunit.dist.xml)):

```bash
php bin/phpunit
```

> To be completed: the `tests/` folder only contains `bootstrap.php`; no test cases exist yet. The `test` environment needs its own `DATABASE_URL` (in `.env.test.local`).

## Project structure

```text
SymHome/
├── assets/                 # AssetMapper entry point, Stimulus controllers, CSS
├── bin/console             # Symfony console
├── compose.yaml            # PostgreSQL service (+ compose.override.yaml)
├── config/                 # bundles, packages (security, doctrine, ...), routes
├── migrations/             # Doctrine migrations (empty)
├── public/
│   ├── index.php
│   └── uploads/meubles/    # furniture images
├── screenshots/client/     # application screenshots
├── src/
│   ├── Controller/         # Home, Produit, Panier, Commande, Profile, Registration, Security,
│   │                       # Meuble, Categorie, AdminDashboard, AdminCommande, AdminUser
│   ├── DataFixtures/       # AppFixtures (empty)
│   ├── Entity/             # User, Meuble, Categorie, Commande, LigneCommande
│   ├── Form/               # MeubleType, CategorieType
│   ├── Repository/
│   └── Security/           # AuthAuthenticator (form login)
├── templates/              # Twig templates (front office + admin_base.html.twig)
├── tests/
└── translations/
```

## Technologies

- PHP 8.2+, Symfony 7.4 (FrameworkBundle, Security, Form, Validator, Twig, Mailer, Messenger, Serializer, Translation)
- Doctrine ORM 3, Doctrine Migrations
- Symfony AssetMapper, Stimulus, UX Turbo
- PostgreSQL 16 (Docker Compose) or any Doctrine-supported database
- PHPUnit 11, Symfony Maker Bundle, Doctrine Fixtures Bundle, Web Profiler

## Deployment

> To be completed: no production deployment configuration is present. Standard Symfony steps apply (`APP_ENV=prod`, `composer install --no-dev --optimize-autoloader`, `php bin/console asset-map:compile`, run migrations), but the target environment must be documented.

## Contributing

> To be completed: no contribution guidelines exist. Suggested minimum: create a feature branch, run `php bin/phpunit`, open a pull request.

## License

> To be completed: `composer.json` declares `"license": "proprietary"`, and no `LICENSE` file is present. Confirm the intended license.

## Authors / contacts

- Repository owner: [@nour-agrebi-web](https://github.com/nour-agrebi-web)

> To be completed: full names and contact details of the authors.

## Useful resources

- [Symfony documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Symfony AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)
- [Symfony UX Turbo](https://symfony.com/bundles/ux-turbo/current/index.html)

## Notes to complete

- **Security**: the `/admin/...` routes are not protected — `access_control` only covers `^/mon-compte`, no controller checks `ROLE_ADMIN`, and no code assigns an admin role. Any visitor can reach the admin area.
- `DATABASE_URL` is commented out in `.env`; it must be defined in `.env.local`.
- No Doctrine migration is committed and `AppFixtures` is empty: there is no sample data or admin account.
- The payment step is a simulation (13-digit card number check); no payment provider is integrated.
- `.env.dev` contains a committed `APP_SECRET`; rotate it for any shared environment.
- No tests, deployment documentation, contribution guide or `LICENSE` file.
