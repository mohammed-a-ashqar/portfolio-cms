# Portfolio CMS

A bilingual (English / Arabic, LTR / RTL) portfolio and freelance-services CMS built with **Laravel 12**. It runs a public portfolio site (projects, services with pricing packages, and a reels page that embeds Instagram, YouTube and TikTok) plus an admin panel where quote requests move through a strict workflow.

[![CI](https://github.com/mohammed-a-ashqar/portfolio-cms/actions/workflows/ci.yml/badge.svg)](https://github.com/mohammed-a-ashqar/portfolio-cms/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20)
![Tests](https://img.shields.io/badge/tests-62%20passing-2ea44f)

## Features

**Public site**
- Portfolio with category, technology, featured, search and sort filters
- Services with fixed, hourly and recurring pricing packages
- Reels gallery that imports a link from Instagram, YouTube or TikTok and fetches its metadata
- Contact and quote-request forms, rate limited at the router and throttled per email
- Full English / Arabic content with automatic RTL layout

**Admin panel**
- Projects (with image gallery), reels (drag-to-reorder), quote requests and contact messages
- Quote workflow: `new → in review → quoted → accepted / rejected → closed`; illegal jumps are rejected
- Admin and editor roles enforced by policies
- Email notifications to the owner on new quotes and messages

## Architecture

The code keeps HTTP, business rules and persistence apart, so each piece can be tested on its own.

```
Request ─► FormRequest ─► DTO ─► Action ─► Repository (interface) ─► Eloquent
             validation   typed   one use     swappable, cached
                          data    case
                                    │
                                    └─► Event ─► Listeners (mail, cache flush)
```

| Pattern | Where | Why |
|---|---|---|
| **Action classes** | `app/Actions/*` | One class per use case (`CreateProject`, `SubmitQuoteRequest`, `TransitionQuoteStatus`), so controllers stay thin. |
| **DTOs** | `app/DTOs/*` | Typed, readonly data from the request into the Action, with no loose arrays. |
| **Repository + interfaces** | `app/Repositories/{Contracts,Eloquent}` | Bound in `RepositoryServiceProvider`; Actions depend on the interface, not on Eloquent. |
| **Enum state machine** | `app/Enums/QuoteStatus.php` | Allowed transitions live on the enum, and `TransitionQuoteStatus` enforces them. |
| **Strategy** | `app/Support/Pricing/*` | `FixedPricing`, `HourlyPricing` and `RecurringPricing` behind one interface; money is a `Money` value object in minor units. |
| **Strategy + manager** | `app/Support/Reels/*` | One provider per platform. `ReelProviderManager` picks the one that supports a URL, with a manual fallback. |
| **Pipeline filters** | `app/Support/Filters/*` | Each project filter is a small class, chained with Laravel's `Pipeline`. |
| **Driver-agnostic cache** | `app/Services/PortfolioCache.php` | Uses cache tags when the driver supports them and falls back to a key index otherwise, so invalidation works on `database` and `file` stores too. |
| **Events & listeners** | `app/Events`, `app/Listeners` | Mail and cache invalidation stay out of the request path. |
| **Policies** | `app/Policies/*` | Admin and editor permissions for every resource. |
| **JSON translations** | `app/Support/Concerns/HasTranslations.php` | Translatable fields live in JSON columns, so adding a language in `config/portfolio.php` needs no migration. |

A short example is the quote workflow:

```php
// app/Enums/QuoteStatus.php
public function allowedTransitions(): array
{
    return match ($this) {
        self::New => [self::InReview, self::Rejected, self::Closed],
        self::InReview => [self::Quoted, self::Rejected, self::Closed],
        self::Quoted => [self::Accepted, self::Rejected, self::Closed],
        self::Accepted, self::Rejected => [self::Closed],
        self::Closed => [],
    };
}
```

## Tech stack

- **Backend:** PHP 8.2+, Laravel 12, MySQL / MariaDB
- **Frontend:** Blade, Tailwind CSS 3, Alpine.js, Vite
- **Quality:** PHPUnit (62 tests), Laravel Pint, strict types throughout, GitHub Actions CI on PHP 8.2 and 8.3

## Getting started

Requirements: PHP 8.2+, Composer, Node 20+, MySQL 8 or MariaDB.

```bash
git clone https://github.com/mohammed-a-ashqar/portfolio-cms.git
cd portfolio-cms

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
# set DB_DATABASE / DB_USERNAME / DB_PASSWORD in .env, then:
php artisan migrate --seed
php artisan storage:link

php artisan serve
```

- Site: <http://localhost:8000>
- Admin: <http://localhost:8000/admin> (`admin@example.com` / `password`, created by the seeder; change it)

### Configuration

| Variable | Purpose |
|---|---|
| `PORTFOLIO_ADMIN_PREFIX` | URL prefix of the admin panel (default `admin`) |
| `PORTFOLIO_QUOTES_EMAIL` | Receives new quote-request notifications |
| `PORTFOLIO_CONTACT_EMAIL` | Receives new contact-message notifications |
| `PORTFOLIO_CACHE_TTL` | Public-site cache lifetime in seconds (`0` while developing) |
| `PORTFOLIO_MAX_UPLOAD_KB` | Maximum image upload size |
| `INSTAGRAM_OEMBED_TOKEN` | Optional. Without it, Instagram reels still embed, using the caption and poster you enter |

## Tests

The tests run against MySQL, using the database named in `phpunit.xml` (`portfolio_cms_test`):

```bash
php artisan test        # 62 tests
vendor/bin/pint --test  # code style
```

They cover authentication, project management, the quote workflow, reel management, the public pages and forms, and unit tests for the quote state machine, the pricing strategies and the reel provider manager.

