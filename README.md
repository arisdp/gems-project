# gems-project
=======

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

## GEMS Project – Technical Test

### 📌 Overview
GEMS Project adalah aplikasi web sederhana yang merepresentasikan proses **Engineering & Construction Project Control**, dengan fokus pada:

- Work Package (WP)
- BOQ (Bill of Quantity)
- Progress Measurement
- Metode **BOQ-based Progress (Cost Weighted)**

Aplikasi ini dibuat untuk memenuhi kebutuhan **GEMS Technical Test**, dengan fokus utama pada logic bisnis, validasi data, dan relasi antar entitas.

> Tidak menggunakan autentikasi/login sesuai requirement.

---

## 🧱 Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3
- **UI Template**: AdminLTE 3
- **Database**: MySQL
- **Architecture**: REST API

---

## 🧠 Business Logic

Perhitungan progress menggunakan metode **Cost Weighted Progress**:
Progress (%) = (Progress Cost BOQ / Total Cost Work Package) × 100


### Rules:
- Progress BOQ bersifat **incremental**
- Total progress BOQ **maksimal 100%**
- Progress Work Package dihitung otomatis dari total progress seluruh BOQ

---

## 🗂️ Data Structure

### Work Package
- code
- name
- description

### BOQ
- work_package_id
- item_name
- quantity
- unit_price
- total_cost

### Progress
- boq_id
- progress_percent
- progress_value
- input_date

---

## 🔄 Application Flow

1. User memilih **Work Package**
2. Dropdown **BOQ otomatis terfilter** berdasarkan WP
3. Detail BOQ ditampilkan otomatis
4. User menginput progress BOQ
5. Sistem melakukan:
   - Validasi progress
   - Perhitungan cost-weighted progress
   - Update progress kumulatif
6. Dashboard menampilkan summary progress

---

## 🎯 Features

- CRUD Work Package
- CRUD BOQ
- Input Progress BOQ
- Validasi Progress ≤ 100%
- Dropdown bertingkat (WP → BOQ)
- Progress Summary per Work Package
- Responsive UI (AdminLTE)

---

## 🧪 Validation

- Progress tidak boleh negatif
- Progress kumulatif maksimal 100%
- BOQ harus sesuai dengan Work Package
- Submit otomatis disable jika input tidak valid

---

## 🖥️ UX Consideration

- Dropdown bertingkat untuk mencegah kesalahan input
- Informasi BOQ muncul otomatis
- Perhitungan progress real-time
- Error message jelas dan informatif

---

## 🚀 Installation

```bash
git clone https://github.com/username/gems-project.git
cd gems-project

composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate
php artisan migrate --seed

php artisan serve

## 🚀 Akses
http://localhost:8000

## 📊 Notes for Reviewer

- Fokus utama aplikasi adalah **logic perhitungan progress**
- Struktur kode dibuat **modular dan scalable**
- UX disesuaikan dengan **workflow Project Control Engineer**

---

## 📝 Author

**Aries Dian**  
Laravel & Vue Developer  