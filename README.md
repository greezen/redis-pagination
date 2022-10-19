
## Install

```sh
composer require greezen/redis-pagination
```

## Examples

### Add

```php
$items = array(array('score' => 1, 'value' => 'test'));
$redisPagination = new greezen\RedisPagination($redis);
$redisPagination->setKey('list')->insert($items, 60);
```

### Get

```php
$redisPagination = new greezen\RedisPagination($redis);
$redisPagination->setKey('list')->paginate(1, 20);

$redisPagination->setKey('list')->offset(0)->limit(20)->order('desc')->get()

$redisPagination->setKey('list')->first();
$redisPagination->setKey('list')->last();
```

### Remove

```php
$redisPagination = new greezen\RedisPagination($redis);
$redisPagination->setKey('list')->delete();
```
