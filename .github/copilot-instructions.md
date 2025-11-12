# AgwaSuri Copilot Instructions

## Project Overview

AgwaSuri is a water quality monitoring system for fishponds, combining IoT sensors with AI-powered fish species recommendations. Built for aquaculture operators in San Juan I, Ternate, Cavite, Philippines.

**Tech Stack**: Laravel 11 + Livewire 3 + Tailwind CSS + Pest PHP

### Thesis Context

**Full Title**: "AGWASURI: An IoT-Driven Platform for Monitoring Water Quality with Predictive Analytics Using Decision Tree Algorithm"

**In Simple Terms**: AgwaSuri uses solar-powered IoT sensors placed in fishponds to automatically measure water quality (dissolved oxygen, pH, salinity, temperature, ammonia). The system analyzes this data using a decision tree algorithm to recommend which fish species will grow best in those specific water conditions, helping fish farmers make better decisions.

**Problem Statement**: Fishpond operators in San Juan I, Ternate, Cavite face high fish mortality and reduced productivity due to poor water quality management and lack of real-time monitoring tools.

**Solution Approach**:

-   Solar-powered IoT system using ESP32 microcontrollers
-   Real-time monitoring of 4 critical parameters: pH, dissolved oxygen, temperature, salinity
-   Decision tree algorithm for predictive fish species selection
-   Web-based dashboard for data visualization and recommendations
-   Offline backup capability via microSD storage

**Academic Framework**:

-   Methodology: Prototyping model with iterative development
-   Evaluation: ISO/IEC 25010 standard (functionality, usability, reliability)
-   Testing: Alpha and beta testing in real-world fishpond environments

**Key Stakeholders**:

-   Primary: Fishpond operators (improved survival rates and productivity)
-   Secondary: Municipal Agriculture Office (efficient monitoring)
-   Tertiary: Aquaculture industry (sustainable practices and food security)

**Note**: For complete thesis documentation including research objectives, system testing details, and scope/limitations, see `docs/THESIS_CONTEXT.md`.

## UI/UX Design Philosophy

### Two-Mode Approach

The project supports two distinct design modes to balance professionalism with creative experimentation:

**1. Regular Mode** (`welcome.blade.php` - Production/Conservative)

-   Clean, professional design suitable for clients and stakeholders
-   Familiar UI patterns that "masa" (general audience) easily understands
-   Focus on clarity, readability, and trust-building
-   Conservative color usage and straightforward layouts
-   Ideal for: thesis presentations, client demos, official deployments

**2. Unconventional Mode** (`landing.blade.php` - Creative/Experimental)

-   Creative UI/UX improvements with modern design trends
-   Innovative interactions while maintaining usability
-   Experimental features like glassmorphism, gradients, micro-animations
-   Still accessible to general audience - creative but not messy
-   Ideal for: showcasing modern capabilities, A/B testing, portfolio pieces

**Safe Testing Workflow**:

```bash
# Main site (production-ready)
http://127.0.0.1:8000/          → welcome.blade.php (Regular Mode)

# Preview experimental designs
http://127.0.0.1:8000/preview   → landing.blade.php (Unconventional Mode)
```

**Design Guidelines**:

-   Both modes must maintain accessibility (WCAG 2.1 AA)
-   Both modes use the same custom color system (water, pond, aqua, ocean)
-   Unconventional mode can push boundaries but must stay professional
-   Test creative ideas in preview routes before making them live
-   Consider "masa" usability: avoid overly complex interactions

## Architecture Patterns

### Livewire Component Structure

All interactive features use **Livewire components** (not Vue/React). Components live in `app/Livewire/` with corresponding Blade views in `resources/views/livewire/`.

```php
// Livewire component pattern
class WaterQualityCard extends Component {
    #[On('echo:water-quality-data-created,WaterQualityCreated')]
    public function refreshData() { /* real-time updates */ }
}
```

**Key components**:

-   `WaterQualityDataTable` - paginated sensor data (10 rows dashboard, 50 rows historical)
-   `WaterQualityChart` - live charts via livewire-charts
-   `ClassifyFish` - AI fish recommendations from external FastAPI (`https://api.lokodata.site/predict`)
-   `WaterQualityCard` - real-time parameter cards with Laravel Reverb broadcasting

### Real-Time Broadcasting

Uses **Laravel Reverb** (not Pusher) for WebSocket events:

-   Event: `App\Events\WaterQualityCreated` broadcast on new sensor data
-   Components listen via `#[On('echo:water-quality-data-created,WaterQualityCreated')]`
-   Must run `php artisan reverb:start` in separate terminal for real-time updates

### IoT API Integration

Sensor data ingestion at `POST /store-water-quality-data`:

-   **Auth**: `Authorization: Bearer {api_key}` header (stored in `users.api_key`)
-   **Validation**: Requires `user_id`, `recorded_at`, `temperature`, `ph_level`, `dissolved_oxygen`, `salinity`
-   **Special logic**: Filters out temperature == 85.0000 (sensor calibration signal)
-   See `app/Http/Controllers/Api/WaterQualityDataController.php`

### Custom Tailwind Color System

Use **aquaculture-themed colors** (not default Tailwind):

-   `water-{50-950}` - primary blues (#0ea5e9 at 500)
-   `pond-{50-950}` - teals (#06b6d4 at 500)
-   `aqua-{50-950}` - green-blues (#14b8a6 at 500)
-   `ocean-{50-950}` - grays (#64748b at 500)

Defined in `tailwind.config.js`. See `docs/COLOR_SYSTEM.md` for usage guidelines.

### Dark Mode System

**Implementation**: Class-based dark mode with Livewire toggle component

-   **Toggle Component**: `DarkModeToggle` Livewire component in navigation
-   **Session Storage**: Dark mode preference stored in Laravel session
-   **Tailwind Config**: `darkMode: 'class'` strategy enabled
-   **Usage**: Add `dark:` prefix to Tailwind classes (e.g., `dark:bg-gray-900`)

**Adding dark mode styles**:

```blade
<!-- Background changes in dark mode -->
<div class="bg-white dark:bg-gray-900">

<!-- Text color changes -->
<p class="text-gray-900 dark:text-white">

<!-- Custom colors work too -->
<button class="bg-water-600 hover:bg-water-700 dark:bg-water-500 dark:hover:bg-water-600">
```

**Component**: `<livewire:dark-mode-toggle />` - Add to any navigation/header

-   `pond-{50-950}` - teals (#06b6d4 at 500)
-   `aqua-{50-950}` - green-blues (#14b8a6 at 500)
-   `ocean-{50-950}` - grays (#64748b at 500)

Defined in `tailwind.config.js`. See `docs/COLOR_SYSTEM.md` for usage guidelines.

## Development Workflow

### Start Development

```bash
composer run dev  # Runs PHP server + Vite HMR concurrently
```

This single command starts both:

-   `php artisan serve` (port 8000)
-   `npm run dev` (Vite with hot reload)

Configured via composer.json `scripts.dev` using `npx concurrently`.

### Database Setup

Default: **SQLite** (`DB_CONNECTION=sqlite` in `.env`)

```bash
php artisan migrate
php artisan db:seed --class=WaterQualityDataSeeder  # Sample data
```

Migration: `database/migrations/2024_10_30_080654_create_water_quality_data_table.php`

-   Uses `user_id` foreign key (not standard Laravel `id`)
-   All water params are floats: `temperature`, `ph_level`, `dissolved_oxygen`, `salinity`

### Testing

Uses **Pest PHP** (not PHPUnit):

```bash
php artisan test
```

Test files in `tests/Feature/` and `tests/Unit/` use Pest syntax.

## Code Conventions

### Routing Pattern

Routes use **view-based routing** (no controllers for simple pages):

```php
Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified']);
```

API routes return JSON and validate bearer tokens (`app/Http/Controllers/Api/`).

### Blade Component Usage

Livewire components embedded in Blade:

```blade
@livewire('water-quality-card', ['parameter' => $value])
```

Layouts extend `layouts/app.blade.php` which includes `@livewireStyles` and `@livewireChartsScripts`.

### Naming Conventions

-   Livewire classes: PascalCase (`WaterQualityCard`)
-   Blade views: kebab-case (`water-quality-card.blade.php`)
-   Routes: kebab-case (`optimal-fish`, `historical-data`)
-   Custom colors: lowercase (`water`, `pond`, `aqua`, `ocean`)

## External Dependencies

### AI Fish Classification

External FastAPI service at `https://api.lokodata.site/predict` (see `ClassifyFish` component):

-   Input: Average water parameters from database
-   Output: Top 3 fish species with confidence percentages + model metrics (accuracy, precision, recall, F1)

### Laravel Packages

-   `asantibanez/livewire-charts` - Chart components (must run `php artisan livewire-charts:install`)
-   `laravel/breeze` - Authentication scaffolding
-   `livewire/volt` - Single-file Livewire components (optional)

## Project Documentation

Key docs in `docs/`:

-   `COPILOT_WORKFLOW.md` - AI-assisted development practices
-   `COLOR_SYSTEM.md` - Complete Tailwind color palette reference
-   `UI_UX_IMPROVEMENTS.md` - Design decisions and conversion optimization
-   `LANDING_PAGE_BUILD_SUMMARY.md` - Landing page features

Reference these when working on UI/UX or understanding design decisions.

## Experimenting with UI/UX

### Creating Design Variants

When exploring unconventional UI ideas:

1. **Work on preview routes** - Never directly modify production pages
2. **Create variants** - Copy `landing.blade.php` to `landing-v2.blade.php` for new experiments
3. **Add preview routes**:
    ```php
    Route::view('/preview-v2', 'landing-v2')->name('preview-v2');
    ```
4. **Test with real users** - Show both modes to fishpond operators for feedback
5. **Keep it accessible** - Creative doesn't mean complex; "masa" should understand it

### When to Use Each Mode

**Use Regular Mode when:**

-   Presenting to thesis committee
-   Demonstrating to Municipal Agriculture Office
-   Deploying to actual fishpond operators (initial rollout)
-   Prioritizing stability and familiarity

**Use Unconventional Mode when:**

-   Showcasing innovation and modern tech capabilities
-   A/B testing engagement improvements
-   Portfolio/competition submissions
-   After user testing validates the creative approach

## Common Pitfalls

1. **Real-time not working**: Run `php artisan reverb:start` separately
2. **Colors not applying**: Use custom palette (`water-500` not `blue-500`)
3. **Livewire not updating**: Check `@livewireStyles` in layout and `#[On()]` listeners
4. **API auth failing**: Verify `Authorization: Bearer` header format and `api_key` in users table
5. **Charts missing**: Run `php artisan livewire-charts:install` after composer install

## Quick Reference

```bash
# Full setup from clone
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan reverb:install && php artisan livewire-charts:install
composer run dev  # Start everything

# Testing IoT API
curl -X POST http://127.0.0.1:8000/store-water-quality-data \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"user_id":1,"recorded_at":"2025-11-08 12:00:00","temperature":28.5,"ph_level":7.2,"dissolved_oxygen":6.8,"salinity":15.3}'
```
