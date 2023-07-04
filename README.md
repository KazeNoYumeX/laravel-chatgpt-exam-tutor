# Laravel ChatGPT Exam Tutor
This is a lightweight project that inputs questions and answers through the API and answers them through the OpenAI API


## System Requirements

The following are required to function properly.

* PHP Version：PHP 8.1.x
* Laravel Version：10.14.x

## Installation

* copy .env.example .env
* composer install
* php artisan key:generate
* chmod -R 777 storage bootstrap/cache

## Usage

### Development

```bash
    php artisan serve
    npm run dev
```

#### If not install redis, please change .env

```
    CACHE_DRIVER="file"
    QUEUE_CONNECTION="sync"
    SESSION_DRIVER="file"
```

### Production

```bash
    composer install --prefer-dist --no-dev -o
```

### Clear and cache

```optimize``` will clear and cache config, route, file

```bash
    php artisan optimize
```

### Directory Permissions

```bash
    chmod -R 777 storage bootstrap/cache
```

### Storage Link

```bash
    php artisan storage:link
```

### Migrate with seeder

```bash
    php artisan migrate:fresh --seed
```

### Scribe

#### Scribe and Ide Helper Generator

```bash
    composer ide-helper-gen
```

#### Only Scribe Generator

```bash
    php artisan scribe:generate
```

#### how to open api document

1. run development ```php artisan serve```
2. use browser open ```http://127.0.0.1:8000/docs```
