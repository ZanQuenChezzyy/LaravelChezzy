<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://i.imgur.com/Ue6oJFg.png" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## (ID) Langkah Instalasi

### Harap Diperhatikan
Sebelum melakukan instalasi di wajibkan menggunakan PHP versi 8.3 keatas, jika PHP anda belum sesuai silahkan unduh melalui link berikut:  
```bash
https://windows.php.net/download/
```
Jika sudah silahkan modifikasi file `php.ini` kemudian cari `;extension=zip` lalu hilang kan tanda `;`, sehingga menjadi `extension=zip`.  

### Clone Repositori dari GitHub
Jalankan perintah berikut untuk meng-clone repositori starter kit dari GitHub ke lokal Anda.
```bash
git clone https://github.com/ZanQuenChezzyy/LaravelChezzy.git
```
Perintah ini akan mengunduh seluruh kode dari repositori ke folder dengan nama `LaravelChezzy`.

### Masuk ke Direktori Proyek
Setelah proses clone selesai, masuk ke dalam `direktori/folder` dengan perintah:

```bash
cd LaravelChezzy
```
Anda sekarang berada di dalam folder proyek tersebut.  

Masuk ke `VS Code` menggunakan perintah:
```bash
code .
```
Sekarang anda siap untuk melakukan konfigurasi lebih lanjut.

### Instalasi Package/Dependensi
Laravel menggunakan Composer dan Node untuk mengelola dependensi. Jalankan perintah berikut di terminal Anda untuk menginstal semua dependensi dan mengonfigurasi aplikasi Laravel secara otomatis:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
npm run build
```
`composer install` : Akan mengunduh dan menginstal semua dependensi yang tercantum di dalam file `composer.json`.  
`npm install` : Akan mengunduh dan menginstal semua dependensi yang tercantum di dalam file `package.json`.  
`cp .env.example .env` : Akan  menyalin file `.env.example` menjadi `.env` untuk konfigurasi aplikasi.  
`php artisan key:generate` : Akan menghasilkan key unik untuk enkripsi data Laravel dan otomatis menambahkannya ke file `.env`.  
`php artisan storage:link` : Akan membuat tautan antara direktori publik `public/storage` dengan penyimpanan file aplikasi `storage/app/public`.  
`npm run build` : Akan mengkompilasi aset secara lokal atau di environment yang terkontrol sebelum mengunggahnya ke server

### Konfigurasi `.env`
Buka file `.env` dan sesuaikan pengaturan koneksi basis data dan lainnya sesuai dengan konfigurasi basis data yang Anda gunakan sebagai contoh:

Konfigurasi Basis data:
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-chezzy
DB_USERNAME=root
DB_PASSWORD=
```
Pada bagian `DB_DATABASE` sesuaikan dengan `nama database` yang ingin anda buat.  

### Konfigurasi Model dan Data Migrasi
Sebelum melakukan `Migrasi Data` konfigurasi data Model dan Migrations menggunakan perintah berikut:
```bash
php model.php
``` 
Perintah ini akan melakukan pembuatan `Model` serta `Data Migrasi` secara otomatis sesuai data yang anda masukkan.  

### Migrasi Database dan Seed Data
Setelah mengonfigurasi `.env`, `Model`, `Data Migrasi` serta `Resource`, Pada `Terminal` Baru jalankan perintah berikut untuk melakukan `migrasi Data` ke database lalu memasukkan data `User`, `Role`, dan `Permission` agar aplikasi dapat langsung digunakan:
```bash
php artisan migrate
php artisan db:seed --class=UserRolePermissionSeeder
php resource.php
composer run dev
```
`php artisan migrate` perintah untuk membuat `tabel` secara otomatis melalui `migrations`.  
`php artisan db:seed --class=UserRolePermissionSeeder` perintah untuk membuat data `User`, `Role`, serta `Permission` agar aplikasi bisa diakses.  
`php artisan resource` perintah untuk membuat Resource sesuai `nama model` yang udah dibuat tadi.  
`php artisan serve` perintah untuk menjalankan server lokal atau aplikasi laravel.

### Akses Filament Admin Panel
Anda dapat mengakses panel admin Filament melalui URL berikut:
```bash
http://127.0.0.1:8000
```
Gunakan kredensial berikut untuk mengakses Admin Panel:

Masuk sebagai admin:  
email : `admin@starter.com`  
password : `12345678`

Masuk sebagai user:  
email : `user@starter.com`  
password : `12345678`

## Fungsi Tambahan

### Membuat Model
```bash
php model.php
```
Perintah ini akan melakukan pembuatan `Model` serta `Data Migrasi`.  

### Membuat Relasi Eloquent
```bash
php relation.php
```
Perintah ini akan melakukan pembuatan `Eloquent Relationship` dari Model `Sumber` dan `Tujuan` yang anda pilih.  

### Membuat Resource
```bash
php resource.php
```
perintah ini akan melakukan pembuatan Resource sesuai `nama model` yang udah ada.  

### Ekspor File
```bash
php exporter.php
```
Perintah ini akan melakukan pembuatan fungsi ekspor data ke format Excel `.xlsx` atau `.csv` di Resource Anda.  



## Lisensi

Laravel framework dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).
