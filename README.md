## Tutorial How To Clone
- Masuk ke folder yang anda ingin tempatkan

- Masukkan Command Berikut 
```shell
git clone https://github.com/LibraTechDev/API-LMS-SEDERHANA.git
```
- Ketikkan di Terminal
```shell
cp .env.example .env
```
- Ketikkan di Terminal 
```shell
php artisan key:generate
```
- Ketikkan 
```shell
composer update
```
- Ketikkan 
```shell
php artisan migrate --seed
```
- Lalu jalankan server
```shell
php artisan ser
```

## Kredensial Yang Tersedia Sebagai Seeder
```shell
{
"username" : "Minprim",
"password" : "123456789"
"role" : "admin"
}
```

## CATATAN
- Beberapa route api memiliki middleware cek admin
- Diasumsikan bahwa user bisa menjalankan semua route kecuali route untuk manage user dan route untuk restore, force-delete
- Untuk mengakses route diperlukan auth token yang diperoleh pada saat meregister, dan login. Buat satu akun user untuk bisa memeriksa interaksi yang berbeda antara user dan admin
