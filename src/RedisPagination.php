<?php

namespace greezen;

/**
 * 
 * @property int $page
 * @property int $offset
 * @property int $limit
 * @property string $order
 * @property string $range
 * 
 * @method RedisPagination offset(int $offset = 0) set start number
 * @method RedisPagination limit(int $limit = 10) set return list number
 * @method RedisPagination page(int $page = 1) set return list page
 * @package greezen
 */

class RedisPagination
{
    /**
     * cache key
     * 
     * @var string
     */
    private $listKey = '';

    /**
     * attributes
     * 
     * @var (int|string)[]
     */
    private $attributes = array(
        'offset' => 0,
        'page' => 1,
        'limit' => 10,
        'order' => 'asc',
        'range' => 'zRange',
    );

    /**
     * redis object
     * 
     * @var Object
     */
    protected $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * set cache key
     * 
     * @param string $listKey 
     * @return $this 
     */
    public function setKey($listKey)
    {
        $this->listKey = $listKey;
        return $this;
    }

    /**
     * add data to redis zset
     * 
     * @param array $items list data eg:[['score' => 1, 'value' => 'test']]
     * @param int $expireTime expire time, default is 60s
     * @return $this 
     */
    public function insert($items, $expireTime = 60)
    {
        $listKey = $this->listKey;
        $this->redis->pipeline(function ($pipe) use($listKey, $items, $expireTime){
            foreach ($items as $item) {
                $pipe->zAdd($listKey, $item['score'], $item['value']);
            }

            $pipe->expire($listKey, $expireTime);
        });
        return $this;
    }

    /**
     * get list data
     * 
     * @param bool $withScore is return the score
     * @return array 
     */
    public function get($withScore = false)
    {
        $end = $this->getAttribute('offset') + $this->getAttribute('limit') - 1;
        $rangeFun = $this->getAttribute('range');
        return (array) $this->redis->{$rangeFun}(
            $this->listKey, 
            $this->getAttribute('offset'), 
            $end, 
            array('withscores' => $withScore)
        );
    }

    /**
     * sort regular
     * 
     * @param string $sort asc: the lowest score item first, desc: the highest score item first, default is asc
     * @return $this 
     */
    public function order($sort = 'asc')
    {
        $this->attributes['range'] = ($sort == 'desc' ? 'zRevRange' : 'zRange');

        return $this;
    }

    /**
     * get the first data of the list
     * 
     * @param bool $withScore 
     * @return array 
     */
    public function first($withScore = false)
    {
        $rangeFun = $this->getAttribute('range');
        return (array) $this->redis->{$rangeFun}($this->listKey, 0, 0, array('withscores' => $withScore));
    }

    /**
     * get the last data of the list
     * 
     * @param bool $withScore 
     * @return array 
     */
    public function last($withScore = false)
    {
        $rangeFun = $this->getAttribute('range');
        return (array) $this->redis->{$rangeFun}($this->listKey, -1, -1, array('withscores' => $withScore));
    }

    /**
     * number of list data
     * 
     * @return int 
     */
    public function count()
    {
        return (int) $this->redis->zCard($this->listKey);
    }

    /**
     * paging for data
     * 
     * @param int $page 
     * @param int $pageSize 
     * @return array 
     */
    public function paginate($page = 1, $pageSize = 10)
    {
        $page = max($page, 0);
        $start = ($page - 1) * $pageSize;
        $count = $this->count();

        return array(
            'page' => $page,
            'page_size' => $pageSize,
            'count' => $count,
            'list' => $this->offset($start)->limit($page)->get(),
        );
    }

    /**
     * deleting data from the cache
     * 
     * @return mixed 
     */
    public function delete()
    {
        return $this->redis->del($this->listKey);
    }

    /**
     * get the parameters in attribute
     * 
     * @param string $attribute 
     * @return int|string|null 
     */
    public function getAttribute($attribute)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : null;
    }

    /**
     * set the attribute parameter value
     * @param string $attribute 
     * @param mixed $val 
     * @return $this 
     */
    public function setAttribute($attribute, $val)
    {
        $this->attributes[$attribute] = $val;
        return $this;
    }

    public function __set($attribute, $val)
    {
        $this->setAttribute($attribute, $val);
    }

    public function __get($attribute)
    {
        $this->getAttribute($attribute);
    }

    public function __call($name, $arguments)
    {
        $this->attributes[$name] = array_shift($arguments);
        return $this;
    }
}
