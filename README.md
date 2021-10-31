# Модуль получения расписания рейсов между станциями - api-rasp

- Подключение

Задаем минимальную конфигурацию в файле, например **rasp.php**
```php
<?php

use kibrycker\api\rasp\Module;

return [
    'bootstrap' => ['rasp'],
    /** Aliases можно не задавать если модуль установлен через composer */
    'aliases' => [
        '@kibrycker/api/rasp' => '@app/modules/api-rasp/src'
    ],
    'modules' => [
        'rasp' => [
            'class' => Module::class,
            'apiYandexKey' => 'Ключ доступа к API полученный в Кабинете Разработчика',
            'duration' => 'Необходимое количество секунд для хранения кэша'
        ]
    ]
];
```

и подключаем в общей конфигурации, например:
```php
return ArrayHelper::merge(
    [... общая конфигурауия ...],
    include __DIR__ . '/путь_до_файла_конфига_модуля/apikb.php'
)
```

Документация по API Яндекс.Расписаний находится по https://yandex.ru/dev/rasp/doc/concepts/about.html

### Примеры запросов к методам
#### Тестирование модуля
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "test"
}
```

#### Получение списка рейсов между станциями
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "search",
    "params": {
        "format": "json",
        "from": "c146",
        "to": "c213",
        "lang": "ru_RU",
        "page": 1,
        "date": "2021-10-29",
        "transport_types": "plane"
    }
}
```

#### Получение списка рейсов между станциями
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "schedule",
        "params": {
        "station": "s9600213",
        "transport_types": "suburban",
        "direction": "на Москву"
    }
}
```

#### Получение список станций следования нитки
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "thread",
    "params": {
        "format": "json",
        "uid": "6005x7505_0_9600213_g21_4",
        "lang": "ru_RU",
        "show_systems": "all"
    }
}
```

#### Получение список станций следования нитки
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "nearest-stations",
    "params": {
        "format": "json",
        "lat": "50.440046",
        "lng": "40.4882367",
        "distance": "50",
        "lang": "ru_RU"
    }
}
```

#### Получение информации о ближайшем к указанной точке городе
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "nearest-settlement",
    "params": {
        "format": "json",
        "lat": "50.440046",
        "lng": "40.4882367",
        "distance": "50",
        "lang": "ru_RU"
    }
}
```

#### Получение информации о перевозчике по указанному коду перевозчика
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "carrier",
    "params": {
        "lang": "ru_RU",
        "code": "TK",
        "system": "iata"
    }
}
```

#### Получение полного списка станций
```json
POST Урл адрес вашего сайта/rasp/default
Content-Type: application/json

{
    "jsonrpc": "2.0",
    "id": 1,
    "method": "stations-list",
    "params": {
        "format": "json",
        "lang": "ru_RU"
    }
}
```
