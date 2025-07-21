# 📦 Products Listing

## Deskripsi

Modul katalog produk dengan fitur filter dinamis, pencarian, kategori berjenjang, varian produk, dan ulasan pelanggan. Dibangun menggunakan **Laravel** untuk backend API dan **Vue 3** untuk frontend SPA.

---

## 🚀 Fitur Utama

* ✅ List produk dengan gambar utama
* ✅ Filter kategori dinamis (dropdown dari database)
* ✅ Pencarian produk dengan debounce
* ✅ Filter harga minimum & maksimum
* ✅ Sorting produk (A-Z, Z-A, Harga naik/turun)
* ✅ Detail produk dengan varian & ulasan
* ✅ Komponen modern dengan PrimeVue & TailwindCSS
* ✅ API dilindungi Sanctum

---

## ⚙️ Instalasi

### 1️⃣ Clone repository

```bash
git clone https://github.com/fazrisuhada/assesmen_pemad.git
cd your-repo-name
```

### 2️⃣ Instalasi backend Laravel

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### 3️⃣ Instalasi frontend Vue

```bash
cd frontend
npm install
npm run dev
```

---

## 🔗 Endpoint API

* `GET /api/products` — Get all products dengan filter & pagination
* `GET /api/categories` — Get all categories dengan parent info
* Semua endpoint dilindungi dengan middleware `auth:sanctum`


---

## ✨ Teknologi

* **Backend:** Laravel 10 + Sanctum
* **Frontend:** Vue 3 (Composition API), PrimeVue, TailwindCSS
* **Database:** MySQL/PostgreSQL

---

## 📄 Dokumentasi API dengan Swagger

Dokumentasi API tersedia di file:  
```plaintext
/public/docs/openapi.yml
