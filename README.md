# Test Product

Задача:

Необходимо написать упрощённое REST API.

Каркас приложения, должен быть стандартный MVC, реализованный через Controller, Entity, Repository, Service.
API должно содержать несколько методов:
1) Сгенерировать стартовый набор данных, генерируется 20 сущностей "товар", у которых есть идентификатор, название и цена.
2) Создать заказ. Метод принимает набор идентификаторов существующих товаров. У заказа есть статус, который может быть в 2 состаяниях: новый, оплачено. При создании заказа, по умолчанию выставляется статус "новый". При успешном создании заказа, метод должен возвращать этот номер в ответе на запрос.
3) Оплатить заказ. Метод принимает на вход сумму и идентификатор заказа. Если сумма совпадает с суммой заказа и статус заказа "новый", то отправляем http запрос на сайт ya.ru, если статус запроса 200, то меняем статус заказа на "оплачено" (таким образом имитируем работу платёжки).

Таблицу пользователей делать не нужно, считаем что пользователь всегда авторизирован под id=1, login=admin.
Количество товаров в рассчёт не берём, считаем, что их у нас бесконечное количество.
Задачу нужно реализовать без фреймворков, никаких триггеров, процедур в mysql использовать нельзя, только обычные sql запросы и транзакции. ORM использовать можно.
Использовать сторонние отдельные библиотеки можно (например symfony router).
Решение необхоимо выложить на github или аналогичный сервис с системой контроля версий.
Проект должен быть оформлен так, как будто выкладываете его в продакшн (никакого закомментированного кода, переменные называем сразу как надо и т.п.).
Есть два уровня сложности.
1) Только бэкенд, никакого GUI.
2) Вы делаете Rest API на php и весь фронт делаете на reactJs или vue (с webpack!), для фуллстак разработчиков.

Можно сделать каркас на этих компонентах (для примера, можно и другие):
* symfony/http-kernel
* symfony/http-foundation
* symfony/routing
* symfony/dependency-injection
* doctrine/orm
* guzzlehttp/guzzle


## Installation
```bash
docker-compose up -d
docker exec -it test-product-fpm composer install

docker exec -it test-product-fpm \
    php vendor/doctrine/orm/bin/doctrine orm:schema-tool:update --force --dump-sql
```

#### Create .env
```bash
docker exec -it test-product-fpm \
    php -r "file_exists('.env') || copy('.env.example', '.env');"
    
docker exec -it test-product-fpm \
    php -r "file_exists('.env.testing') || copy('.env.example', '.env.testing');"
```

Please update `.env` and `.env.testing` files.

## Run tests
```bash
docker exec -it test-product-fpm vendor/bin/phpunit
```

## API

#### Create random products
`POST /api/v1/product/random`

Params:
* count | optional | int (min:1, max:100, default:20) - count random products

Example request:
```bash
curl -X POST \
  http://test-product.local/api/v1/product/random \
  -H 'Content-Type: application/json' \
  -d '{
	"count": 5
}'
```

Example response:
```json
{
    "object": "product_ids",
    "data": [
        1180,
        1181,
        1182,
        1183,
        1184
    ]
}
```

#### Create order
`POST /api/v1/order`

Params:
* product_ids | required | array (min_count:1, max_count:100) - list product ids

Example request:
```bash
curl -X POST \
  http://test-product.local/api/v1/order \
  -H 'Content-Type: application/json' \
  -d '{
	"product_ids": [1,2,3]
}
'
```

Example response:
```json
{
    "object": "order",
    "data": {
        "id": 168
    }
}
```

#### Pay order
`POST /api/v1/order/{order_id}/pay`

Params:
* order_id | required | int - order id
* amount | required | int - order amount in the smallest currency unit (e.g., 100 to charge 1 ruble)

Example request:
```bash
curl -X POST \
  http://test-product.local/api/v1/order/25/pay \
  -H 'Content-Type: application/json' \
  -d '{
	"amount": 100
}
'
```

Example response:
```json
{
    "object": "bool",
    "data": true
}
```

#### Errors

The application uses conventional HTTP response codes to indicate the success or failure of an API request. Errors include an error code that briefly explains the error reported.

Example error response:
```json
{
    "code": "resource_missing",
    "message": "Not found"
}
```