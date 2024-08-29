# Cashier API
This is the API for my cashier project before

## How to open the project

```bash
git clone https://github.com/NIxNref/ArkaCashier-API.git

cd ArkaCashier-API

composer install

cp .env.example .env

php artisan key:generate
```
### .env config
`
DB_DATABASE=YOUR_DATABASE_NAME
`
<br>
`
DB_USERNAME=DATABASE_USERNAME
`
<br>
`
DB_PASSWORD=DATABASE_PASSWORD ( leave empty if not using password )
`
<br>

```bash
php artisan migrate

php artisan serve
```
> you don't really need setup the .env, because you can use API that i have on my repo

<hr>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.
