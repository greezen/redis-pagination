[![Latest Stable Version](http://poser.pugx.org/greezen/redis-pagination/v)](https://packagist.org/packages/greezen/redis-pagination) 
[![Total Downloads](http://poser.pugx.org/greezen/redis-pagination/downloads)](https://packagist.org/packages/greezen/redis-pagination) 
[![Latest Unstable Version](http://poser.pugx.org/greezen/redis-pagination/v/unstable)](https://packagist.org/packages/greezen/redis-pagination) 
[![License](http://poser.pugx.org/greezen/redis-pagination/license)](https://packagist.org/packages/greezen/redis-pagination) 
[![PHP Version Require](http://poser.pugx.org/greezen/redis-pagination/require/php)](https://packagist.org/packages/greezen/redis-pagination)

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
