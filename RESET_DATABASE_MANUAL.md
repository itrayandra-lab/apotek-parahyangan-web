# Reset Database ke Kondisi Awal

**Tujuan:** Mengembalikan database ke kondisi sebelum import SQL (kondisi Laravel fresh)

---

## 🔄 Cara Reset Database

### **Opsi 1: Command Prompt (CMD) - Recommended**

1. **Buka Command Prompt sebagai Administrator**
2. **Navigate ke folder project:**
   ```cmd
   cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"
   ```

3. **Reset database dengan Laravel:**
   ```cmd
   php artisan db:wipe --force
   php artisan migrate:fresh --force
   php artisan db:seed --force
   ```

4. **Clear cache:**
   ```cmd
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

### **Opsi 2: MySQL Command Line**

1. **Buka Command Prompt**
2. **Drop dan recreate database:**
   ```cmd
   mysql -u root -h 127.0.0.1 -e "DROP DATABASE IF EXISTS apotek_parahyangan_db;"
   mysql -u root -h 127.0.0.1 -e "CREATE DATABASE apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. **Run Laravel migrations:**
   ```cmd
   cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"
   php artisan migrate:fresh --seed --force
   ```

---

### **Opsi 3: phpMyAdmin (GUI)**

1. **Buka phpMyAdmin:** `http://localhost/phpmyadmin`
2. **Login dengan username `root`**
3. **Pilih database `apotek_parahyangan_db`**
4. **Drop database:**
   - Klik database `apotek_parahyangan_db`
   - Klik tab "Operations"
   - Scroll ke bawah, klik "Drop the database"
   - Confirm dengan mengetik nama database

5. **Buat database baru:**
   - Klik "New" di sidebar
   - Database name: `apotek_parahyangan_db`
   - Collation: `utf8mb4_unicode_ci`
   - Klik "Create"

6. **Run Laravel migrations:**
   ```cmd
   cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"
   php artisan migrate:fresh --seed --force
   ```

---

### **Opsi 4: MySQL Workbench**

1. **Buka MySQL Workbench**
2. **Connect ke localhost**
3. **Execute SQL:**
   ```sql
   DROP DATABASE IF EXISTS apotek_parahyangan_db;
   CREATE DATABASE apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Run Laravel migrations:**
   ```cmd
   cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"
   php artisan migrate:fresh --seed --force
   ```

---

## 🔍 Verifikasi Reset

Setelah reset, verifikasi dengan command berikut:

### **Cek Tabel Laravel Default:**
```cmd
php artisan tinker --execute="
echo 'Tables in database:' . PHP_EOL;
foreach(DB::select('SHOW TABLES') as \$table) {
    \$tableName = array_values((array)\$table)[0];
    \$count = DB::table(\$tableName)->count();
    echo '- ' . \$tableName . ': ' . \$count . ' records' . PHP_EOL;
}
"
```

### **Tabel yang Seharusnya Ada (Laravel Fresh):**

**Core Laravel Tables:**
- `users` - Default user table
- `password_reset_tokens` - Password reset
- `sessions` - User sessions
- `cache` - Cache storage
- `jobs` - Queue jobs
- `failed_jobs` - Failed jobs

**Migration Tables:**
- `migrations` - Migration history

**Seeder Data (jika ada):**
- Data sample dari DatabaseSeeder
- Admin user default
- Sample categories/products

### **Cek Data Sample:**
```cmd
php artisan tinker --execute="
echo 'User count: ' . App\\Models\\User::count() . PHP_EOL;
echo 'Product count: ' . App\\Models\\Product::count() . PHP_EOL;
echo 'Category count: ' . App\\Models\\Category::count() . PHP_EOL;
"
```

---

## 📋 Script Batch untuk Windows

Buat file `reset_db.bat` dengan isi:

```batch
@echo off
echo === RESET DATABASE TO LARAVEL FRESH ===
echo.

cd "C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web"

echo Dropping all tables...
php artisan db:wipe --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to wipe database
    pause
    exit /b 1
)

echo Running fresh migrations...
php artisan migrate:fresh --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to run migrations
    pause
    exit /b 1
)

echo Running seeders...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to run seeders
    pause
    exit /b 1
)

echo Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo.
echo === RESET COMPLETED ===
echo.

echo Showing current tables:
php artisan tinker --execute="
foreach(DB::select('SHOW TABLES') as \$table) {
    \$tableName = array_values((array)\$table)[0];
    \$count = DB::table(\$tableName)->count();
    echo \$tableName . ': ' . \$count . ' records' . PHP_EOL;
}
"

echo.
echo SUCCESS: Database reset to Laravel fresh state!
pause
```

---

## 🎯 Quick Reset Commands

**One-liner untuk reset cepat:**
```cmd
php artisan db:wipe --force && php artisan migrate:fresh --seed --force && php artisan config:clear
```

**Dengan konfirmasi:**
```cmd
echo "This will reset database to fresh Laravel state. Continue? (Y/N)"
set /p confirm=
if /i "%confirm%"=="Y" (
    php artisan db:wipe --force
    php artisan migrate:fresh --seed --force
    php artisan config:clear
    echo Database reset completed!
) else (
    echo Reset cancelled.
)
```

---

## ⚠️ Peringatan

**PERHATIAN:** Reset database akan:
- ❌ Menghapus SEMUA data yang ada
- ❌ Menghapus semua tabel
- ❌ Menghapus data import SQL sebelumnya
- ✅ Kembali ke kondisi Laravel fresh
- ✅ Hanya ada tabel dan data default Laravel

**Backup dulu jika perlu:**
```cmd
mysqldump -u root -h 127.0.0.1 apotek_parahyangan_db > backup_before_reset.sql
```

---

## 🚀 Setelah Reset

1. **Test aplikasi:**
   ```cmd
   php artisan serve
   ```

2. **Buka browser:** `http://localhost:8000`

3. **Cek login admin** (jika ada seeder):
   - Email: admin@example.com
   - Password: password

4. **Cek database kosong/fresh**

---

**Status:** ✅ Siap untuk reset  
**Rekomendasi:** Gunakan **Opsi 1 (CMD)** untuk reset otomatis