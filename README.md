# instagram-like

Requires:
- PHP 8.1.0
- MySQL 5.7.36
- NPM 9.1.2
- PHP GD extension (https://www.php.net/manual/en/book.image.php)
- Composer

Install:
```bash
$ composer install
$ npm install
$ npm run build
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate -n
```
