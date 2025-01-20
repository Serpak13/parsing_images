composer install
cp .env.example .env
# пишем настройки в .env файле
php artisan key:generate
php artisan migrate

Запускаем сервер
php artisan serve

Сжатые изображения сохраняются в виде архива 
