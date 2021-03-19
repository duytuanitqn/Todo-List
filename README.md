### Required:
```
- PHP >= 7.1
```
### Install
```
1. clone source code my computer
2. go to source code folder
3. cp .env.example .env
4. rename your DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD
5. rename 'development' => [
                'adapter' => 'mysql',
                'host' => your host mysql,
                'name' => your database name,
                'user' => your user login database,
                'pass' => your password login database,
                'port' => your port mysql default is 3306,
                'charset' => 'utf8',
            ], in ```phinx.php``` file.
6. docker-compose up -d
7. exec app container and run command:
- composer install
- /vendor/bin/phinx migrate -e development
8. Open browser and enter your host
```
### Unitest
```
./vendor/bin/phpunit
```