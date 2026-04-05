# SPK Kelayakan Investasi — NPV Calculator
## Aplikasi Laravel — Sistem Pendukung Keputusan

---

## Struktur Arsitektur (Pemisahan Layer)

```
app/
├── Http/
│   └── Controllers/
│       └── NpvController.php       ← LAYER HTTP (terima request, validasi)
└── Services/
    └── NpvCalculatorService.php    ← LAYER LOGIKA (rumus matematis NPV)

resources/views/
├── layouts/
│   └── app.blade.php               ← LAYOUT MASTER (template utama)
└── npv/
    ├── index.blade.php             ← LAYER UI: Form Input
    └── result.blade.php            ← LAYER UI: Tampilan Hasil

routes/
└── web.php                         ← ROUTING
```

---

## Data Flow

```
[User] → Form Input
   ↓
[routes/web.php] → Route POST /npv/calculate
   ↓
[NpvController.php] → Validasi Input
   ↓
[NpvCalculatorService.php] → Hitung NPV (Rumus Matematis)
   ↓
[NpvController.php] → Kirim hasil ke View
   ↓
[npv/result.blade.php] → Tampilkan Laporan
```

---

## ⚙️ Instalation

### 1. Clone / copy project
```bash
cd /var/www   # atau folder pilihan Anda
# Copy semua file ke folder project laravel
```

### 2. Install Laravel (jika belum ada)
```bash
composer create-project laravel/laravel spk-npv
cd spk-npv
```

### 3. Copy file-file berikut ke project Laravel:
- `app/Services/NpvCalculatorService.php`
- `app/Http/Controllers/NpvController.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/npv/index.blade.php`
- `resources/views/npv/result.blade.php`
- `routes/web.php`

### 4. Jalankan server
```bash
php artisan serve
```

### 5. Buka browser
```
http://localhost:8000
```

---

## 🔢 Rumus NPV yang Diimplementasikan

```
NPV = -C₀ + Σ [ CFₜ / (1 + r)ᵗ ]

Dimana:
  C₀  = Investasi Awal (Modal Awal)
  CFₜ = Arus Kas pada tahun ke-t
  r   = Tingkat Diskonto (dalam desimal)
  t   = Periode tahun (1, 2, 3, ...)
```

**Keputusan:**
- NPV > 0 → ✅ Investasi LAYAK / DITERIMA
- NPV = 0 → ⚖️  Break Even (Impas)
- NPV < 0 → ❌ Investasi TIDAK LAYAK / DITOLAK

---

## 📁 Penjelasan Setiap File (untuk Presentasi)

| File | Layer | Fungsi |
|------|-------|--------|
| `NpvCalculatorService.php` | **Business Logic** | Berisi semua rumus matematis NPV, perhitungan PV per tahun, dan logika keputusan |
| `NpvController.php` | **HTTP/Controller** | Menerima HTTP request, memvalidasi input, memanggil Service, meneruskan hasil ke View |
| `layouts/app.blade.php` | **UI/Template** | Layout master: navbar, head, footer |
| `npv/index.blade.php` | **UI/Frontend** | Form input dinamis (nama proyek, modal, discount rate, arus kas) |
| `npv/result.blade.php` | **UI/Frontend** | Laporan hasil: tabel PV, nilai NPV, keputusan layak/tidak |
| `routes/web.php` | **Routing** | Mendefinisikan URL endpoint GET dan POST |

---

## 🎯 Fitur

- ✅ Form input dinamis (tambah/hapus tahun arus kas)
- ✅ Validasi input server-side (Laravel Validation)
- ✅ Perhitungan NPV dengan rincian PV per tahun
- ✅ Keputusan otomatis (Layak / Tidak Layak / Break Even)
- ✅ Tabel laporan lengkap
- ✅ UI dark-mode modern (tanpa dependensi CSS framework eksternal)
- ✅ Pemisahan layer: Service ↔ Controller ↔ View

---

*Dibangun dengan Laravel — SPK Metode Net Present Value*
