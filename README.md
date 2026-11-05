1. Создать файл `.env` в корне проекта и указать в нём ключ Checko API:

   ```ini
   CHECKO_API_KEY=ваш_ключ_Checko_API
   ```

2. Открыть терминал в корне проекта и запустить приложение:

   ```powershell
   php -S 127.0.0.1:8000 -t public public\index.php
   ```

   Файл `public\index.php` в конце команды нужен для маршрутизации запросов к Swagger UI и API.

2.1. Для запросов к Checko API в PHP должно быть включено расширение OpenSSL. Если оно не включено, укажите путь к каталогу, содержащему `php_openssl.dll`:

   ```powershell
   php -d "extension_dir=C:\путь\к\php\ext" -d "extension=php_openssl.dll" -S 127.0.0.1:8000 -t public public\index.php
   ```

   Замените `C:\путь\к\php\ext` на путь к каталогу `ext` вашей установки PHP. Для работы AppApi также требуется расширение PDO SQLite.

3. Пока сервер запущен, OpenAPI-документация доступна по адресу [http://127.0.0.1:8000/swagger](http://127.0.0.1:8000/swagger).
