# Agwasuri

Water quality monitoring system for fishponds with IoT sensors and AI fish recommendations.

## What it does

-   Monitors water quality in real-time (temperature, pH, dissolved oxygen, salinity)
-   Shows live charts and data on a dashboard
-   Recommends best fish species based on water conditions using AI
-   Connects to ESP32/Arduino IoT sensors via REST API

## Requirements

-   PHP 8.2+
-   Composer
-   Node.js 18+
-   MySQL/PostgreSQL/SQLite
-   XAMPP (optional, includes PHP and MySQL)

**New to Laravel?** Watch this: https://youtu.be/2qgS_MCvDfk

## Installation

```bash
composer global require laravel/installer
```

Clone and setup:

```bash
git clone https://github.com/lokodata/agwasuri_prod.git
cd agwasuri_prod
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`:

```env
DB_CONNECTION=sqlite
```

Run migrations and install packages:

```bash
php artisan migrate
php artisan reverb:install
php artisan livewire-charts:install
```

Start the server:

```bash
composer run dev
```

Open http://127.0.0.1:8000 and register an account.

## API for IoT Devices

Send data to: `POST /store-water-quality-data`

Headers:

```
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json
```

Body:

```json
{
    "user_id": 1,
    "recorded_at": "2025-11-06 12:00:00",
    "temperature": 28.5,
    "ph_level": 7.2,
    "dissolved_oxygen": 6.8,
    "salinity": 15.3
}
```

## Troubleshooting

**Composer not found**: Install Composer and restart terminal

**npm not found**: Install Node.js and restart terminal

**Database error**: Check .env file, make sure MySQL is running in XAMPP

**Port 8000 in use**: Run `php artisan serve --port=8001`

**Real-time not working**: Run `php artisan reverb:start` in separate terminal

## Tech Stack

Laravel 11, Livewire 3, Tailwind CSS, Pest PHP, Laravel Reverb

## License

MIT License

Built for fishpond operators in Ternate, Cavite, Philippines as part of a thesis project.
