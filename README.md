# Investment Feasibility Checker

## NPV Calculator (Decision Support System)

This project is a Laravel-based web application for evaluating investment feasibility using the Net Present Value (NPV) method.

Users can:

- Enter project investment data
- Calculate NPV with yearly present value breakdown
- Get an automatic feasibility decision
- Save and review calculation history

## Key Features

- Dynamic cash flow input (multiple years)
- Server-side validation using Laravel validation rules
- NPV calculation with detailed per-year present value
- Automatic decision output:
    - Worthy Investment / Accepted
    - Break Even Investment
    - Bad Investment / Declined
- History page with saved projects
- Layered architecture (Controller, Service, Repository)

## Tech Stack

- PHP 8+
- Laravel 12
- Blade templates
- MySQL or other Laravel-supported SQL database
- Vite (for frontend assets)

## Project Structure

```
app/
|-- Http/Controllers/
|   |-- NpvController.php
|-- Services/
|   |-- NpvCalculatorService.php
|-- Repositories/
|   |-- NpvRepository.php
|-- Models/
|   |-- NpvProject.php
|   |-- NpvCashflow.php

database/
|-- migrations/
|   |-- 2026_04_05_200812_create_npv_projects_table.php
|   |-- 2026_04_05_200847_create_npv_cashflows_table.php
|-- seeders/
|   |-- NpvProjectSeeder.php

resources/views/
|-- npv/
|   |-- index.blade.php
|   |-- result.blade.php
|   |-- history.blade.php
|-- layouts/
|   |-- app.blade.php

routes/
|-- web.php
```

## Request Flow

```
User Input Form
   |
   v
NpvController@calculate
   |
   v
NpvCalculatorService@calculate   (NPV computation)
   |
   v
NpvRepository@saveProject        (store in database)
   |
   v
Redirect to /npv/{id}
   |
   v
NpvRepository@findWithCashFlows
   |
   v
View: npv/result.blade.php
```

## NPV Formula

$$
NPV = -C_0 + \sum_{t=1}^{n} \frac{CF_t}{(1 + r)^t}
$$

Where:

- $C_0$: initial investment (initial capital)
- $CF_t$: cash flow at year $t$
- $r$: discount rate (decimal)
- $t$: time period (year 1, year 2, ...)

Decision rule:

- $NPV > 0$: project is financially feasible
- $NPV = 0$: break-even point
- $NPV < 0$: project is not financially feasible

## Application Routes

- `GET /` -> redirect to NPV form
- `GET /npv` -> input form
- `POST /npv/calculate` -> validate, calculate, save, and redirect
- `GET /npv/{id}` -> calculation detail/result
- `GET /npv/history` -> list of saved projects
- `DELETE /npv/{id}` -> remove a saved project

## Local Setup

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd SPK
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Update your database configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 4. Run migrations (and optional seeders)

```bash
php artisan migrate
php artisan db:seed --class=NpvProjectSeeder
```

### 5. Start the app

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Open:

```
http://127.0.0.1:8000
```

## Usage Guide

1. Open the NPV input page.
2. Enter the project name, initial investment, discount rate, and yearly cash flows.
3. Submit the form to calculate the NPV.
4. Review the result page and decision status.
5. Visit the history page to see all saved calculations.

## Troubleshooting

- If assets are not loaded, run `npm run dev` again.
- If database errors appear, verify `.env` DB settings and rerun migrations.
- If route/model changes are not reflected, run:

```bash
php artisan optimize:clear
```

## License

This project is open-source and intended for educational and practical decision-support use.
