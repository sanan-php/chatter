## Что это?

В этом репозитории представлен код веб-чата, реализованного на **PHP**, 
в качетсве БД используется **Redis**. В чате используются **веб-сокеты**.
По структуре кода может вам напомнить что-то между Yii2 и Symfony 3x.

***
**Список функций:**
* Регистрация
* Авторизация
* Просмотр, редактирование профиля
* Просмотр списка пользователей (случайные 50)
* Переписка с пользователем
* Точный поиск пользоватей (по имени/е-мейлу)
* Фавориты (Добавление/удаление пользователей в закладки для дальнейшей связи с ними)

***
**Как запустить?**

Добавляем хост
```bash
sanan@sanan:~/images/chatter$ sudo vim /etc/hosts

# ...

127.0.0.1       chatter.local

```

Поднимаем докер (перед этим вы должны убедиться, что у вас отключен nxinx на вашем пк, если он имеется, а так же php и redis нужно отключить):

```bash
sanan@sanan:~/images/chatter$ service [имя отключаемой службы] stop

sanan@sanan:~/images/chatter$ docker-compose up -d
```

Загружаем библиотеки:
```bash
sanan@sanan:~/images/chatter$ docker-compose exec php composer install

```
Укажите к папке ``vendor`` права вашего пользователя и www-data, как юзера и группу.
Пример:
```bash
sanan@sanan:~/images/chatter$ sudo chown sanan:www-data -R www/chatter/vendor/
```
Запускаем вебсокеты:
```bash
sanan@sanan:~/images/chatter$ docker-compose exec php php web/ws/index.php start -d
```

### Готово!

Откройте в браузере адрес:
 
 ``http://chatter.local``

***
По всем возникающим вопросам mail@sanan.tech