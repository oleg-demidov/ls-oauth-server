## LiveStreet Oauth server

Приложения oauth server на базе фреймворка LiveStreet

### Установка
1. Скопировать все файлы в корень сайта. В каталог ``/framework/`` скачать содержимое фреймворка из [репо фреймворка](https://github.com/livestreet/livestreet-framework)
2. Выполнить в БД sql дамп из файла ``/application/install/dump.sql``
3. Переименовать конфиг ``/application/config/config.local.php.dist`` в ``/application/config/config.local.php`` и прописать в нем корректное подключение к БД и адрес сайта
4. Дать права на запись для каталогов: ``/uploads/``, ``/application/logs/``, ``/application/tmp/``
5. Запустить ``composer install``
6. Сгенерировать ключи `openssl genrsa -out keys/private.key 2048`, `openssl rsa -in keys/private.key -pubout -out keys/public.key`, `vendor/bin/generate-defuse-key > keys/encryption.key`

