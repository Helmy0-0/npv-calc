# Investment Feasibility Checker — NPV Calculator
## Decision Support System

---

## Architectural Structure

```
app/
├── Http/Controllers/
│   └── NpvController.php          ← HTTP LAYER  : request, val, etc.
├── Services/
│   └── NpvCalculatorService.php   ← LOGIC LAYER : NPV Math Formula
├── Repositories/
│   └── NpvRepository.php          ← DATA LAYER  : database
└── Models/
    ├── NpvProject.php             
    └── NpvCashFlow.php            
database/
├── migrations/
│   ├── ..._create_npv_projects_table.php
│   └── ..._create_npv_cash_flows_table.php
└── seeders/
    └── NpvProjectSeeder.php      

resources/views/npv/
├── index.blade.php               
├── result.blade.php               
└── history.blade.php              

routes/
└── web.php                   
```

---

## Data Flow

```
User Form Input
   ↓
NpvController → validate()
   ↓
NpvCalculatorService → calculate()   [COUNT NPV]
   ↓
NpvRepository → saveProject()        [SAVE TO DB]
   ↓
redirect → GET /npv/{id}
   ↓
NpvRepository → findWithCashFlows()  [LOAD FROM DB]
   ↓
npv/result.blade.php
```

---

## ⚙️ Instalation

### 1. Clone / copy project
```bash
cd /var/www 
```

### 2. Install Laravel (if it doesn't exist yet)
```bash
composer create-project laravel/laravel npvcalc
cd npvcalc
```

### 3. Run Server
```bash
php artisan serve
```

### 5. Buka browser
```
http://localhost:8000
```

---

## NPV Formula

```
NPV = -C₀ + Σ [ CFₜ / (1 + r)ᵗ ]

Dimana:
  C₀  = Initial Investment (Initial Capital)
  CFₜ = Cash Flow in t year
  r   = Discount Rate (in decimal)
  t   = Year period (1, 2, 3, ...)
```

**Decision:**
- NPV > 0 → Worthy investment / Accepted
- NPV = 0 → Break Even 
- NPV < 0 → Bad investment / Declined

---

## Feature

- Dynamic input form (add/remove cash flow years)
- Server-side input validation (Laravel Validation)
- NPV calculation with annual PV breakdown
- Automated decision (Feasible/Not Feasible/Break Even)
- Comprehensive report tables
- Modern dark-mode UI (no external CSS framework dependencies)
- Layer separation: Service ↔ Controller ↔ View

---

*Build with Laravel — Net Present Value Method*
