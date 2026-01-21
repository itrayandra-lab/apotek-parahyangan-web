# Import Database dengan Git Bash

**File SQL:** `temp/apotek_parahyangan_db.sql`  
**Database:** `apotek_parahyangan_db`  
**Tool:** Git Bash

---

## 🚀 Langkah-Langkah Import

### **1. Buka Git Bash**

- Klik kanan di folder project `apotek-parahyangan-web`
- Pilih **"Git Bash Here"**
- Atau buka Git Bash dan navigate ke folder project:

```bash
cd /c/Users/IT\ LUNARAY/Herd/apotek-parahyangan-web
```

### **2. Verifikasi File SQL**

```bash
# Cek apakah file SQL ada
ls -la temp/apotek_parahyangan_db.sql

# Cek ukuran file
du -h temp/apotek_parahyangan_db.sql

# Lihat beberapa baris pertama
head -10 temp/apotek_parahyangan_db.sql
```

### **3. Test Koneksi MySQL**

```bash
# Test koneksi ke MySQL
mysql -u root -h 127.0.0.1 -e "SELECT 'MySQL connection successful' as status;"
```

**Jika diminta password:** Tekan Enter (karena default root tanpa password)

### **4. Buat Database**

```bash
# Buat database jika belum ada
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Verifikasi database dibuat
mysql -u root -h 127.0.0.1 -e "SHOW DATABASES;" | grep apotek
```

### **5. Import SQL File**

```bash
# Import file SQL ke database
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql
```

**Output yang diharapkan:** Tidak ada error message (silent success)

### **6. Verifikasi Import**

```bash
# Cek tabel yang berhasil diimport
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SHOW TABLES;"

# Cek jumlah tabel
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'apotek_parahyangan_db';"

# Cek beberapa data sample
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SELECT COUNT(*) as total_users FROM users;"
mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SELECT COUNT(*) as total_products FROM products;"
```

---

## 📋 Script Lengkap (Copy-Paste)

Jika ingin menjalankan semua sekaligus, copy-paste script ini ke Git Bash:

```bash
#!/bin/bash

echo "=== IMPORT DATABASE APOTEK PARAHYANGAN ==="
echo "Starting import process..."
echo

# Navigate to project directory
cd /c/Users/IT\ LUNARAY/Herd/apotek-parahyangan-web

# Check if SQL file exists
if [ ! -f "temp/apotek_parahyangan_db.sql" ]; then
    echo "❌ ERROR: SQL file not found!"
    echo "Expected: temp/apotek_parahyangan_db.sql"
    exit 1
fi

echo "✅ SQL file found"
echo "File size: $(du -h temp/apotek_parahyangan_db.sql | cut -f1)"
echo

# Test MySQL connection
echo "Testing MySQL connection..."
if mysql -u root -h 127.0.0.1 -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ MySQL connection successful"
else
    echo "❌ ERROR: Cannot connect to MySQL"
    echo "Make sure MySQL is running and accessible"
    exit 1
fi
echo

# Create database
echo "Creating database..."
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS apotek_parahyangan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "✅ Database created/verified"
echo

# Import SQL file
echo "Importing SQL file..."
echo "This may take a few minutes..."
if mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql; then
    echo "✅ SQL import successful"
else
    echo "❌ ERROR: SQL import failed"
    exit 1
fi
echo

# Verify import
echo "Verifying import..."
table_count=$(mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'apotek_parahyangan_db';" -s -N)
echo "Total tables imported: $table_count"

if [ "$table_count" -gt 0 ]; then
    echo "✅ Import verification successful"
    echo
    echo "=== SAMPLE DATA COUNTS ==="
    mysql -u root -h 127.0.0.1 apotek_parahyangan_db -e "
    SELECT 'users' as table_name, COUNT(*) as record_count FROM users
    UNION ALL
    SELECT 'products', COUNT(*) FROM products
    UNION ALL
    SELECT 'medicines', COUNT(*) FROM medicines
    UNION ALL
    SELECT 'orders', COUNT(*) FROM orders
    UNION ALL
    SELECT 'articles', COUNT(*) FROM articles;
    "
else
    echo "❌ ERROR: No tables found after import"
    exit 1
fi

echo
echo "🎉 DATABASE IMPORT COMPLETED SUCCESSFULLY!"
echo
echo "Next steps:"
echo "1. Update .env file if needed"
echo "2. Run: php artisan config:clear"
echo "3. Run: php artisan serve"
```

---

## 🔧 Troubleshooting

### **Error: "mysql: command not found"**

**Penyebab:** MySQL tidak ada di PATH Git Bash

**Solusi 1 - Tambah MySQL ke PATH:**
```bash
# Tambahkan MySQL ke PATH (sementara)
export PATH="/c/Program Files/MySQL/MySQL Server 8.0/bin:$PATH"

# Atau jika menggunakan XAMPP
export PATH="/c/xampp/mysql/bin:$PATH"

# Atau jika menggunakan Laragon
export PATH="/c/laragon/bin/mysql/mysql-8.0.30-winx64/bin:$PATH"

# Test lagi
mysql --version
```

**Solusi 2 - Gunakan path lengkap:**
```bash
# XAMPP
/c/xampp/mysql/bin/mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql

# Laragon
/c/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql

# MySQL Installer
"/c/Program Files/MySQL/MySQL Server 8.0/bin/mysql" -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql
```

### **Error: "Access denied for user 'root'"**

**Solusi:**
```bash
# Jika ada password
mysql -u root -p -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql

# Atau coba tanpa host
mysql -u root apotek_parahyangan_db < temp/apotek_parahyangan_db.sql
```

### **Error: "Unknown database"**

**Solusi:**
```bash
# Buat database dulu
mysql -u root -h 127.0.0.1 -e "CREATE DATABASE apotek_parahyangan_db;"

# Lalu import
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql
```

### **Error: File path dengan spasi**

**Solusi:**
```bash
# Gunakan quotes atau escape
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < "temp/apotek_parahyangan_db.sql"

# Atau escape spasi
mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek\ parahyangan\ db.sql
```

---

## 📍 Lokasi MySQL di Berbagai Environment

### **XAMPP:**
```bash
/c/xampp/mysql/bin/mysql
```

### **Laragon:**
```bash
/c/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql
```

### **MySQL Installer:**
```bash
"/c/Program Files/MySQL/MySQL Server 8.0/bin/mysql"
```

### **Herd (Laravel Herd):**
```bash
# Herd biasanya menggunakan MySQL yang sudah ada di system
mysql
```

---

## ✅ Verifikasi Setelah Import

Setelah import berhasil, jalankan command ini untuk memastikan:

```bash
# Cek koneksi Laravel ke database
php artisan tinker --execute="echo 'DB Connection: ' . (DB::connection()->getPdo() ? 'OK' : 'FAILED') . PHP_EOL;"

# Cek jumlah users
php artisan tinker --execute="echo 'Total Users: ' . App\\Models\\User::count() . PHP_EOL;"

# Clear cache Laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Test aplikasi
php artisan serve
```

---

## 🎯 Quick Commands

**Cek MySQL berjalan:**
```bash
netstat -an | grep 3306
```

**Restart MySQL (jika perlu):**
```bash
# XAMPP
/c/xampp/mysql_restart.bat

# Windows Service
net stop mysql80 && net start mysql80
```

**Backup database (setelah import):**
```bash
mysqldump -u root -h 127.0.0.1 apotek_parahyangan_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

**Status:** ✅ Siap digunakan  
**Rekomendasi:** Gunakan script lengkap untuk import otomatis