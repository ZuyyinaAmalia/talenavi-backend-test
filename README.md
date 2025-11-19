# Technical Test Backend Developer Intern - Talenavi

Repository ini adalah hasil pengerjaan **Technical Test** untuk posisi Backend Developer Intern.

---

## Data Kandidat

- **Nama Kandidat:** Zuyyina Amalia
- **Tech Stack:**  
  - Laravel 12.38.1
  - SQLite (Database)  
  - Maatwebsite/Laravel-Excel  
  - Postman  

---

## Dokumentasi Teknis & Cara Instalasi

Berikut adalah panduan untuk menjalankan project ini di local computer (menggunakan SQLite).

---

### 1. Clone & Install

```bash
git clone https://github.com/ZuyyinaAmalia/talenavi-backend-test.git
cd talenavi-backend-test
composer install 
```

### 2. Setup Environment (SQLite)

Salin file ```.env:```
```bash 
cp .env.example .env
```
Edit file ```.env``` dan atur menjadi:
```bash
DB_CONNECTION=sqlite
# Hapus baris DB_HOST, DB_PORT, DB_DATABASE, dll
```

### 3. Buat Database & Migrate
Buat file database kosong:

Windows : ```New-Item -ItemType File database/database.sqlite```

MacOs: ```touch database/database.sqlite```

Generate key & migrate database:
```bash
php artisan key:generate
php artisan migrate
```
 
### 4. Jalankan Server
```php artisan serve```

Akses API:
http://localhost:8000

### 5. API Documentation
File **TalenaviBackendTest.postman_collection.json** tersedia di dalam repository untuk di-import ke Postman.

Contoh Endpoint Export dengan Filter:
```
GET /api/todo-lists/export?status=pending,open&priority=high&start_date=2025-11-01&end_date=2025-11-30
```

