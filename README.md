# Instalation
Pertama clone
```sh
git clone https://github.com/agusbawa/testmydovi.git
```
Setelah Clone lakukan composer install untuk install semua devedency yang di perlukan
```sh
composer install
```
kemudian lakukan migration, untuk install semua database yang diperlukan. ingat sebelum melakukan migration lakukan pengecekan pada env untuk menyesuaikan dengan host database
```sh
php artisan migrate
```

kemudian lakukan seeding, ini untuk menamabahkan record default
```sh
php artisan db:seed
```
setelah berhasil semua sekarang tinggal di panggil melalui domain atau juga bisa menggunakan virtual server laravel
```sh
php artisan serve
```

# Author 
Dibuat Oleh Agus Bawa Nadi Putra
